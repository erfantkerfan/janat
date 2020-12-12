const Auth = {
    install(Vue) {

        // inject some component options
        Vue.mixin({
            computed: {
                authenticatedUser: {
                    get () {
                        return this.$store.getters['users/user']
                    },
                    set (user) {
                        this.$store.dispatch('users/setUser', user)
                    }
                }
            }
        })

        // add an instance method
        Vue.prototype.$auth = function () {
            let $store = this.$store
            return {
                refreshAuthenticatedUserData () {
                    $store.getters['users/user'].loading = true
                    $store.getters['users/user'].show()
                        .then((response) => {
                            $store.getters['users/user'].loading = false
                            $store.dispatch('users/setUser', response.data)
                        })
                        .catch((error) => {
                            this.$store.dispatch('alerts/fire', {
                                icon: 'error',
                                title: 'توجه',
                                message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                            });
                            console.log('error: ', error)

                            $store.getters['users/user'].loading = false;
                        })
                },
                setUser (user) {
                    $store.dispatch('users/setUser', user)
                },
                user () {
                    return $store.getters['users/user']
                },
                isSuperAdmin () {
                    return $store.getters['users/user'].hasSuperAdminRole()
                }
            }
        }
    }
}

export default Auth
