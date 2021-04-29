import {SettingList} from "@/models/Setting";

const state = {
    settings: null,
    sidebarBackground: 'green',
    sidebarBackgroundColor: 'black',
    sidebarBackgroundImage: '/img/sidebar-2.jpg',
    sidebarMini: true,
    sidebarImg: true
};

const mutations = {
    SET_SETTINGS: (state, payload) => {
        state.settings = payload;
    },
    update_SidebarBackground: (state, payload) => {
        state.sidebarBackground = payload;
    },
    update_SidebarBackgroundColor: (state, payload) => {
        state.sidebarBackgroundColor = payload;
    },
    update_SidebarBackgroundImage: (state, payload) => {
        state.sidebarBackgroundImage = payload;
    },
    update_SidebarMini: (state, payload) => {
        state.sidebarMini = payload;
    },
    update_SidebarImg: (state, payload) => {
        state.sidebarImg = payload;
    },
};

const actions = {};

const getters = {
    settings: state => new SettingList(state.settings),
    currencyUnit: state => {
        let settings = new SettingList(state.settings)
        let currencyUnit = settings.list.find(item => item.name === 'currency_unit')
        if(currencyUnit) {
            return currencyUnit.value
        } else {
            return 'ریال'
        }
    },
    sidebarBackground: state => state.sidebarBackground,
    sidebarBackgroundColor: state => state.sidebarBackgroundColor,
    sidebarBackgroundImage: state => state.sidebarBackgroundImage,
    sidebarMini: state => state.sidebarMini,
    sidebarImg: state => state.sidebarImg,
};

const settings = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};

export default settings;
