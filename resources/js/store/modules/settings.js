import {SettingList} from "@/models/Setting";

const state = {
    settings: null,
};

const mutations = {
    SET_SETTINGS: (state, payload) => {
        state.settings = payload;
    }
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
};

const settings = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};

export default settings;
