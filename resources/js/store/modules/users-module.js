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

const actions = {
    setUser (context, user) {
        let authenticatedUser = new User(user);
        authenticatedUser.getUserPic()
            .then((response) => {
                user['user_pic'] = response.data
                context.commit("SET_USER", user);
            })
            .catch((error) => {
                context.commit("SET_USER", null);
            })
    }
};

const getters = {
    user: state => new User(state.user)
};

const users = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};

export default users;
