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
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.csp.esm.js';

// Custom AlpineJS Components
import './components/alpine/chatbox';
import checkboxGrid from './components/alpine/checkboxGrid';
import clipboardButton from './components/alpine/clipboardButton';
import comparison from './components/alpine/comparison';
import dislikeButton from './components/alpine/dislikeButton';
import formSubmit from './components/alpine/formSubmit';
import likeButton from './components/alpine/likeButton';
import posterRow from './components/alpine/posterRow';
import smallBookmarkButton from './components/alpine/smallBookmarkButton';
import tabs from './components/alpine/tabs';
import ternaryCheckMark from './components/alpine/ternaryCheckMark';
import toggle from './components/alpine/toggle';
import torrentGrouping from './components/alpine/torrentGrouping';

Alpine.data('checkboxGrid', checkboxGrid);
Alpine.data('clipboardButton', clipboardButton);
Alpine.data('comparison', comparison);
Alpine.data('dislikeButton', dislikeButton);
Alpine.data('formSubmit', formSubmit);
Alpine.data('likeButton', likeButton);
Alpine.data('posterRow', posterRow);
Alpine.data('bookmark', smallBookmarkButton);
Alpine.data('tabs', tabs);
Alpine.data('ternaryCheckMark', ternaryCheckMark);
Alpine.data('toggle', toggle);
Alpine.data('torrentGroup', torrentGrouping);

Livewire.start();
