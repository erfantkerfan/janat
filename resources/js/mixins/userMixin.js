export default {
    computed: {
        LoggedInUser () {
            return this.$store.getters['users/user']
        }
    },
};
