require('./bootstrap');

import Vue from 'vue'

import App from './components/Chatbot.vue'

const app = new Vue({
    el: '#app',
    components: { App }
});
