import {
  generateRegistrationOptions,
  verifyRegistrationResponse,
  generateAuthenticationOptions,
  verifyAuthenticationResponse,
} from '@simplewebauthn/server';
import type {
  PublicKeyCredentialCreationOptionsJSON,
  PublicKeyCredentialRequestOptionsJSON,
  RegistrationResponseJSON,
  AuthenticationResponseJSON,
  VerifiedRegistrationResponse,
  VerifiedAuthenticationResponse,
  AuthenticatorTransport, // Explicitly import AuthenticatorTransport
} from '@simplewebauthn/types';
import { randomUUID } from 'crypto';

// Configuration for WebAuthn Relying Party (RP)
// These should ideally be loaded from environment variables for production.
// Ensure RP_ID is your domain (e.g., 'your-app.com') and ORIGIN includes the full URL and port.
const RP_ID = process.env.WEBAUTHN_RP_ID || 'localhost';
const RP_NAME = process.env.WEBAUTHN_RP_NAME || 'My Secure App';
const ORIGIN = process.env.WEBAUTHN_ORIGIN || `http://localhost:3000`; // The URL where your client app is hosted

// --- Challenge Store (Replaced in-memory map with a more robust, albeit still mock, class) ---
// In a production environment, this should be a persistent, shared store like Redis
// or a database with TTL capabilities, to support horizontal scaling and server restarts.

interface StoredChallenge {
  challenge: string;
  userId?: string; // Optional: userId if known at the start of authentication
  expiresAt: number; // Timestamp for challenge expiration
}

const CHALLENGE_EXPIRATION_MS = 5 * 60 * 1000; // Challenges expire in 5 minutes

/**
 * A mock challenge store that simulates persistence and handles challenge expiration.
 * In a real application, this would be an adapter for Redis, a secure session store,
 * or a database with TTL.
 */
class ChallengeStore {
  private challenges: Map<string, StoredChallenge> = new Map();
  private cleanupInterval: NodeJS.Timeout | null = null;

  constructor() {
    // Periodically clean up expired challenges from the map
    this.cleanupInterval = setInterval(() => this.cleanup(), 60 * 1000); // Every minute
  }

  /**
   * Stores a new challenge with an expiration timestamp.
   * @param challengeId Unique ID for the challenge.
   * @param challengeData The challenge string and optional userId.
   */
  set(challengeId: string, challengeData: Omit<StoredChallenge, 'expiresAt'>): void {
    const expiresAt = Date.now() + CHALLENGE_EXPIRATION_MS;
    this.challenges.set(challengeId, { ...challengeData, expiresAt });
  }

  /**
   * Retrieves a challenge if it exists and has not expired.
   * Expired challenges are automatically removed.
   * @param challengeId Unique ID of the challenge to retrieve.
   * @returns The stored challenge data or undefined if not found or expired.
   */
  get(challengeId: string): StoredChallenge | undefined {
    const stored = this.challenges.get(challengeId);
    if (stored && stored.expiresAt > Date.now()) {
      return stored;
    }
    // If expired or not found, remove it from the store
    if (stored) {
      this.challenges.delete(challengeId);
    }
    return undefined;
  }

  /**
   * Deletes a challenge from the store.
   * @param challengeId Unique ID of the challenge to delete.
   */
  delete(challengeId: string): void {
    this.challenges.delete(challengeId);
  }

  /**
   * Cleans up all expired challenges.
   */
  private cleanup(): void {
    const now = Date.now();
    for (const [key, value] of this.challenges.entries()) {
      if (value.expiresAt <= now) {
        this.challenges.delete(key);
      }
    }
  }

  /**
   * Clears the cleanup interval when the store is no longer needed (e.g., app shutdown).
   */
  destroy(): void {
    if (this.cleanupInterval) {
      clearInterval(this.cleanupInterval);
      this.cleanupInterval = null;
    }
  }
}

const challengeStore = new ChallengeStore();

// --- Mock Database Structures (Replace with your actual ORM/DB logic) ---

/**
 * Represents an authenticator (passkey) associated with a user.
 */
interface Authenticator {
  credentialID: Buffer; // Unique ID for the passkey credential
  credentialPublicKey: Buffer; // The public key for verifying signatures
  counter: number; // Used to prevent replay attacks
  transports?: AuthenticatorTransport[]; // Communication methods (e.g., 'usb', 'nfc', 'internal')
}

/**
 * Represents a user in the system.
 */
interface User {
  id: string;
  username: string;
  authenticators: Authenticator[]; // List of passkeys registered by this user
  // Other user properties like email, password hash, etc.
}

// Simple in-memory mock database for demonstration purposes.
// In a real application, this would be replaced by database queries.
const usersDB: User[] = [];

const findUserById = (userId: string): User | undefined => {
  return usersDB.find(u => u.id === userId);
};

const findUserByCredentialID = (credentialID: Buffer): User | undefined => {
  return usersDB.find(u =>
    u.authenticators.some(a => a.credentialID.equals(credentialID))
  );
};

const saveUser = (user: User): void => {
  const index = usersDB.findIndex(u => u.id === user.id);
  if (index > -1) {
    usersDB[index] = user;
  } else {
    usersDB.push(user);
  }
};

// --- Passkey Service Functions ---

/**
 * Generates options for the client to initiate a passkey registration.
 *
 * @param userId - The ID of the user attempting to register.
 * @param username - The username associated with the user.
 * @returns An object containing the options to send to the client and a unique challenge ID.
 */
export async function generatePasskeyRegistrationOptions(
  userId: string,
  username: string,
): Promise<{ options: PublicKeyCredentialCreationOptionsJSON; challengeId: string }> {
  const user = findUserById(userId);

  const options = await generateRegistrationOptions({
    rpName: RP_NAME,
    rpID: RP_ID,
    userID: userId,
    userName: username,
    attestationType: 'none', // 'none' is generally preferred for passkeys for privacy
    excludeCredentials: user ? user.authenticators.map(authenticator => ({
      id: authenticator.credentialID,
      type: 'public-key',
      transports: authenticator.transports,
    })) : [],
    authenticatorSelection: {
      residentKey: 'required', // Passkeys are "discoverable" credentials
      userVerification: 'preferred', // User verification (e.g., PIN, biometric) is handled by the passkey provider
    },
    timeout: 60000, // 60 seconds
  });

  const challengeId = randomUUID();
  challengeStore.set(challengeId, { challenge: options.challenge, userId });

  return { options, challengeId };
}

/**
 * Verifies the client's response after a passkey registration attempt.
 *
 * @param challengeId - The unique ID for the stored challenge.
 * @param attResp - The registration response from the client.
 * @returns The newly registered Authenticator object if successful, null otherwise.
 */
export async function verifyPasskeyRegistration(
  challengeId: string,
  attResp: RegistrationResponseJSON,
): Promise<Authenticator | null> {
  const storedChallenge = challengeStore.get(challengeId);
  if (!storedChallenge || !storedChallenge.userId) {
    // This error indicates either an expired/invalid challenge or an internal flow issue.
    throw new Error('Registration challenge not found, expired, or missing user context.');
  }

  const { challenge: expectedChallenge, userId } = storedChallenge;
  const user = findUserById(userId);
  if (!user) {
    // This scenario should ideally not happen if userId was properly stored with the challenge.
    // Indicates potential data inconsistency or flow error.
    throw new Error('User not found during registration verification.');
  }

  let verification: VerifiedRegistrationResponse;
  try {
    verification = await verifyRegistrationResponse({
      response: attResp,
      expectedChallenge,
      expectedOrigin: ORIGIN,
      expectedRPID: RP_ID,
      requireUserVerification: false, // User verification is handled by the passkey provider
    });
  } catch (error) {
    // Implement robust error logging here (e.g., to Sentry, ELK stack)
    // Avoids console.log as per instructions; returning null indicates failure to the caller.
    return null;
  }

  const { verified, registrationInfo } = verification;

  if (verified && registrationInfo) {
    const { credentialID, credentialPublicKey, counter, transports } = registrationInfo;

    const newAuthenticator: Authenticator = {
      credentialID: Buffer.from(credentialID),
      credentialPublicKey: Buffer.from(credentialPublicKey),
      counter,
      transports,
    };

    user.authenticators.push(newAuthenticator);
    saveUser(user); // Persist updated user in your database
    challengeStore.delete(challengeId); // Clean up the challenge
    return newAuthenticator;
  }

  return null;
}

/**
 * Generates options for the client to initiate a passkey authentication.
 * This function can handle both user-specific (username-first) and discoverable (passwordless) authentication.
 *
 * @param userId - Optional: The ID of the user if their identity is already known (e.g., they typed a username).
 * @returns An object containing the options to send to the client and a unique challenge ID.
 */
export async function generatePasskeyAuthenticationOptions(
  userId?: string,
): Promise<{ options: PublicKeyCredentialRequestOptionsJSON; challengeId: string }> {
  let userAuthenticators: Authenticator[] = [];
  if (userId) {
    const user = findUserById(userId);
    if (user) {
      userAuthenticators = user.authenticators;
    }
  }

  const options = await generateAuthenticationOptions({
    rpID: RP_ID,
    // If userId is provided, restrict to that user's authenticators.
    // Otherwise, allow discovery of any registered passkey.
    allowCredentials: userAuthenticators.map(authenticator => ({
      id: authenticator.credentialID,
      type: 'public-key',
      transports: authenticator.transports,
    })),
    userVerification: 'preferred', // Preferred for passkeys
    timeout: 60000, // 60 seconds
  });

  const challengeId = randomUUID();
  challengeStore.set(challengeId, { challenge: options.challenge, userId });

  return { options, challengeId };
}

/**
 * Verifies the client's response after a passkey authentication attempt.
 *
 * @param challengeId - The unique ID for the stored challenge.
 * @param authResp - The authentication response from the client.
 * @returns The ID of the authenticated user if successful, null otherwise.
 */
export async function verifyPasskeyAuthentication(
  challengeId: string,
  authResp: AuthenticationResponseJSON,
): Promise<string | null> {
  const storedChallenge = challengeStore.get(challengeId);
  if (!storedChallenge) {
    // This error indicates an expired or invalid challenge.
    throw new Error('Authentication challenge not found or expired.');
  }

  const { challenge: expectedChallenge, userId: knownUserId } = storedChallenge;

  const credentialID = Buffer.from(authResp.rawId, 'base64url');
  let user: User | undefined;
  let authenticator: Authenticator | undefined;

  // Prioritize finding the user and authenticator based on `knownUserId` if available.
  // Otherwise, search across all users for the credentialID (for discoverable credentials).
  if (knownUserId) {
    user = findUserById(knownUserId);
    authenticator = user?.authenticators.find(a => a.credentialID.equals(credentialID));
  } else {
    user = findUserByCredentialID(credentialID);
    authenticator = user?.authenticators.find(a => a.credentialID.equals(credentialID));
  }

  if (!user || !authenticator) {
    // This scenario indicates either an unknown credential, a credential not belonging
    // to the expected user, or a tampered credentialID.
    // In a production app, this might lead to logging or more specific error handling.
    return null;
  }

  let verification: VerifiedAuthenticationResponse;
  try {
    verification = await verifyAuthenticationResponse({
      response: authResp,
      expectedChallenge,
      expectedOrigin: ORIGIN,
      expectedRPID: RP_ID,
      authenticator, // Pass the found authenticator for counter verification
      requireUserVerification: false, // User verification handled by passkey provider
    });
  } catch (error) {
    // Implement robust error logging here
    // Avoids console.log as per instructions; returning null indicates failure to the caller.
    return null;
  }

  const { verified, authenticationInfo } = verification;

  if (verified && authenticationInfo) {
    // Update authenticator counter to prevent replay attacks
    authenticator.counter = authenticationInfo.newCounter;
    saveUser(user); // Persist updated user/authenticator in your database
    challengeStore.delete(challengeId); // Clean up the challenge
    return user.id; // Return the authenticated user's ID
  }

  return null;
}

// Optional: Export challengeStore for testing or explicit cleanup in app shutdown hooks
// For demonstration, it's globally instantiated.
// export { challengeStore };