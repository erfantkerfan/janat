/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from "vue";

require('./bootstrap');

window.Vue = require('vue');

import axios from "axios";

import 'material-design-icons/iconfont/material-icons.css' // Ensure you are using css-loader

// Plugins
// import App from "./App.vue";
import VueAxios from "vue-axios";
import DashboardPlugin from "./material-dashboard";
import Auth from "./plugins/auth.js";
// plugin setup
Vue.use(DashboardPlugin);

Vue.use(VueAxios, axios);
Vue.use(Auth)
// router & store setup

import router from "./router";
import store from "./store";




import Chartist from "chartist";
// global library setup
Vue.prototype.$Chartist = Chartist;



/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('App', require('./App.vue').default);

import VuePersianDatetimePicker from 'vue-persian-datetime-picker'
Vue.component('date-picker', VuePersianDatetimePicker)

import VueConfirmDialog from 'vue-confirm-dialog'
Vue.use(VueConfirmDialog)

const app = new Vue({
    router: router,
    store: store,
    el: "#dashboard_app"
    // render: h => h(App)
});

store.$app = app;
