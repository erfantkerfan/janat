import Swal from "sweetalert2";

const state = {};

const mutations = {};

const actions = {
    fire({commit, dispatch}, data) {
        Swal.fire({
            title: data.title,
            text: data.message,
            icon: data.icon,
            // showCancelButton: true,
            confirmButtonText: 'متوجه شدم',
            // cancelButtonText: 'No, keep it'
        })
    },

    success({commit, dispatch}, data) {
        this.$app.$notify({
            timeout: 2500,
            message: message,
            horizontalAlign: "right",
            verticalAlign: "top",
            icon: "add_alert",
            type: "success"
        });
    },

    error({commit, dispatch}, message) {
        this.$app.$notify({
            timeout: 2500,
            message: message,
            horizontalAlign: "right",
            verticalAlign: "top",
            icon: "add_alert",
            type: "warning"
        });
    }
};

const getters = {};

const alerts = {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};

export default alerts;
