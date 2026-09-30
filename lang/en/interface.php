<?php

return [
    // partials/achievement-modal.blade.php
    'well-done' => 'Well done!',
    'all-achievements' => 'All achievements',

    // partials/captcha.blade.php (honeypot label, hidden from real users)
    'honeypot-name' => 'Name',

    // partials/comparison.blade.php
    'show' => 'Show',

    // partials/footer.blade.php
    'view-all' => '[View all]',
    'api-documentation' => 'API documentation',
    'built-using' => 'Built using:',
    'wikis' => 'Wikis',

    // partials/quick-search-dropdown.blade.php
    'quick-search-placeholder' => 'Search',
    'quick-search-prompt' => 'Search movies, tv series, or people',
    'quick-search-no-results' => 'No results found',

    // partials/statsgroupmenu.blade.php
    'group-requirements' => 'Group requirements',

    // partials/statsusermenu.blade.php
    'top-upload-snatches' => 'Most upload snatches',
    'top-messages' => 'Most messages',

    // partials/top-nav.blade.php
    'documentation' => 'Information and documentation',
    'about-site' => 'About :site',
    'wiki' => 'Wiki',
    'donate' => 'Donate',
    'donate-filled' => ':percent% filled',
    'support-site' => 'Support :site (:percent%)',
    'support-brand' => 'Support :brand',

    // layout/default.blade.php
    'confirm-action-title' => 'Are you sure?',

    // blocks/latest-posts.blade.php, latest-topics.blade.php
    'no-posts' => 'No posts.',
    'no-topics' => 'No topics.',

    // blocks/chat.blade.php
    'chatbox-loading' => 'Chatbox loading',
    'chatbox' => 'Chatbox',
    'users-count' => 'Users:',
    'toggle-typing-notifications' => 'Toggle typing notifications',
    'room' => 'Room',
    'status' => 'Status',
    'toggle-fullscreen' => 'Toggle fullscreen',
    'custom-user-icon' => 'Custom user icon',
    'lifetime-donor' => 'Lifetime donor',
    'donor' => 'Donor',
    'system-notification' => 'System notification',
    'users' => 'Users',
    'gift-user-bon' => 'Gift user bon',
    'send-chat-pm' => 'Send chat PM',
    'write-your-message' => 'Write your message...',
    'unknown-user' => 'Unknown',
    'no-chat-history' => 'There is no chat history here. Send a message!',
    'delete-message' => 'Delete message',
    'typing-several' => 'Several people are typing…',
    'typing-one' => ':name is typing…',
    'typing-multiple' => ':names are typing…',

    // resources/js chatbox.js
    'chat-connection-lost' => 'Connection lost. Trying to reconnect…',
    'chat-loading-error' => 'Error loading chat. Please try again.',

    // resources/js like/dislike/bookmark buttons
    'error-title' => 'Error',
    'liked' => 'Liked',
    'like-this-post' => 'Like this post',
    'like-applied' => 'Your like was successfully applied!',
    'disliked' => 'Disliked',
    'dislike-this-post' => 'Dislike this post',
    'dislike-applied' => 'Your dislike was successfully applied!',
    'unbookmark' => 'Unbookmark',
    'bookmark' => 'Bookmark',
    'bookmark-applied' => 'Torrent has been bookmarked successfully!',
    'unbookmark-applied' => 'Torrent has been unbookmarked successfully!',

    // resources/js/unit3d/helper.js
    'found-match' => 'Found Match: :title (:year)',

    // errors/layout.blade.php and error pages
    'error-go-home' => 'Go home',
    'error-400-title' => 'Error 400: Bad request!',
    'error-400-description' => 'The request could not be understood by the server due to malformed syntax. The client SHOULD NOT repeat the request without modifications.',
    'error-401-title' => 'Error 401: Unauthorized!',
    'error-401-description' => 'Error code response for missing or invalid authentication token.',
    'error-403-title' => 'Error 403: Forbidden!',
    'error-403-description' => 'You do not have permission to perform this action!',
    'error-404-title' => 'Error 404: Page not found',
    'error-404-description' => 'The requested page cannot be found! Not sure what you\'re looking for but check the address and try again!',
    'error-405-title' => 'Error 405: Method not allowed!',
    'error-405-description' => 'The method you are trying is use, is not allowed by this server.',
    'error-444-title' => 'Error 444',
    'error-444-description' => 'CONNECTION CLOSED WITHOUT RESPONSE.',
    'error-500-title' => 'Error 500: Internal server error',
    'error-500-description' => 'Our server encountered an internal error. Sorry for the inconvenience',
    'error-502-title' => 'Error 502: Bad gateway!',
    'error-502-description' => 'The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request.',
    'error-503-title' => 'Error 503: Service unavailable!',
    'error-503-description' => 'Sorry, we are doing some maintenance. Please check back soon.',
    'error-307-title' => 'Error 307: Temporary redirect!',
    'error-307-description-1' => 'Please forgive the inconvenience.',
    'error-307-description-2' => 'We are currently building or revamping this feature.',
    'error-307-description-3' => "It's okay, we're excited too!",

    // auth pages — session flash prefixes
    'flash-warning' => 'Warning: :message',
    'flash-info' => 'Info: :message',
    'flash-success' => 'Success: :message',

    // auth/two-factor-challenge.blade.php
    'two-factor-title' => 'Two factor authentication',
    'use-recovery-code' => 'Use a recovery code',

    // auth/verify-email.blade.php
    'verify-email-almost-done' => 'Almost done…',
    'verify-email-instructions' => 'Click the verification link sent to your email to activate your account.',
    'verify-email-having-issues' => 'Having issues?',
    'verify-email-resend' => 'Resend verification email',

    // auth/application/create.blade.php
    'application-title' => 'Application',
    'application-description' => 'Application',
    'proofs' => 'Proofs',
    'proof-number' => 'Proof :number',
    'response-time' => 'Response time:',
    'memory-used' => 'Memory used:',
    'system-load' => 'System load:',
    'site-design-copyright' => 'Site and design © :years :site',
    'versus' => 'vs',
    'pagination-navigation' => 'Pagination navigation',
    'achievement-unlocked' => 'Achievement unlocked',
];
