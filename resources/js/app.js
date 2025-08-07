import lodash from 'lodash';
window._ = lodash;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
};

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Sweet Alert
import Swal from 'sweetalert2';
window.Swal = Swal;

// Vite Import
import.meta.glob(['/public/img/pipes/**', '/resources/sass/vendor/webfonts/font-awesome/**']);

// Livewire + AlpineJS
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm.js';

// Custom AlpineJS Components
import './components/alpine/chatbox';
import './components/alpine/checkboxGrid';
import './components/alpine/clipboardButton';
import './components/alpine/dialog';
import './components/alpine/dislikeButton';
import './components/alpine/likeButton';
import './components/alpine/livewireDialog';
import './components/alpine/posterRow';
import './components/alpine/smallBookmarkButton';
import './components/alpine/tabs';
import './components/alpine/toggle';
import './components/alpine/torrentGrouping';

Livewire.start();
