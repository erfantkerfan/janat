import {User} from '@/models/User';

const state = {
    user: null,
};

const mutations = {
    SET_LIST: (state, list) => {
        state.list = list;
    },
    SET_RESOURCE: (state, user) => {
        state.user = user;
    },
    SET_USER: (state, user) => {
        state.user = user;
    },
    SET_META: (state, meta) => {
        state.meta = meta;
    }
};

const getters = {
    user: state => new User(state.user)
};

const users = {
    namespaced: true,
    state,
    getters,
    mutations
};

export default users;
