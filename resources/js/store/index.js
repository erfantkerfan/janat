import Vue from 'vue';
import Vuex from 'vuex';

import auth from "./modules/auth";
import alerts from "./modules/alerts-module";
import users from "./modules/users-module";
import settings from "./modules/settings";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        alerts,
        settings,
        users
    }
});
