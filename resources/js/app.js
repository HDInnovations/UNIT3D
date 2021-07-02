/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Vue from 'vue';
import version from './components/Version';
import chatbox from './components/chat/Chatbox';

if (document.getElementById('vue')) {
    new Vue({
        el: '#vue',
        components: { version: version, chatbox: chatbox }
    });
}
