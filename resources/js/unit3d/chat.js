import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const forceTLS = (import.meta.env.VITE_PUSHER_SCHEME ?? window.location.protocol.replace(':', '')) === 'https';
const defaultPort = window.location.port || (forceTLS ? 443 : 80);

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: '',
    wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
    wsPort: import.meta.env.VITE_PUSHER_PORT || defaultPort,
    wssPort: import.meta.env.VITE_PUSHER_PORT || defaultPort,
    forceTLS,
    enabledTransports: ['ws', 'wss'],
});
