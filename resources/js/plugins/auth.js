const Auth = {
    install(Vue) {
        // add an instance method
        Vue.prototype.$auth = function () {
            return {
                setUser (user) {
                    this.$store.commit('users/SET_USER', user)
                },
                user () {
                    return this.$store.getters['users/user']
                },
                isSuperAdmin () {
                    return this.$store.getters['users/user'].hasSuperAdminRole()
                }
            }
        }
    }
}

export default Auth
