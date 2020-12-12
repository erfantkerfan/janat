<template>
  <router-view :key="$route.name + ($route.params.id || '')"></router-view>
</template>

<script>
import Vue from 'vue';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
Vue.component('Loading', Loading);

import Paginate from 'vuejs-paginate'
Vue.component('paginate', Paginate)

export default {
    name: 'App',
    props: ['user'],
    created() {
        this.$store.commit('users/SET_USER', this.user)
    }
};
</script>

<style>
    .vpd-input-group input {
        width: 100%;
    }
</style>
