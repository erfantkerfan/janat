<template>
    <div v-if="user" class="md-layout md-gutter">
        <div class="md-layout-item md-size-60 md-small-size-100">
            <div class="md-layout-item md-size-100">
                <user-edit-card v-model="user" @update="updateUserProfile"/>
            </div>
            <div class="md-layout-item md-size-100">
                <user-password-card :user="user" @update="getProfile"/>
            </div>
        </div>
        <div class="md-layout-item md-size-40 md-small-size-100">
            <user-profile-card v-model="user" @update="getProfile"/>
        </div>
    </div>
</template>

<script>
    import UserEditCard from "@/pages/User/UserProfile/EditProfileCard.vue";
    import UserPasswordCard from "@/pages/User/UserProfile/EditPasswordCard.vue";
    import UserProfileCard from "@/pages/User/UserProfile/UserProfileCard.vue";
    import {User} from '@/models/User';

    export default {
        name: "user-profile-example",
        components: {
            "user-profile-card": UserProfileCard,
            "user-edit-card": UserEditCard,
            "user-password-card": UserPasswordCard
        },
        computed: {
            logedInUser () {
                return this.$store.getters['users/user']
            }
        },
        data: () => ({
            user: new User()
        }),
        mounted() {
            this.getProfile();
        },
        methods: {
            getProfile () {
                if (this.$route.name === 'Create') {
                    return false
                }
                this.user.loading = true;
                this.user.show(this.$route.params.id)
                    .then((response) => {
                        this.user.loading = false;
                        this.user = new User(response.data)
                    })
                    .catch((error) => {

                        this.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)

                        this.user.loading = false;
                        this.user = new User()
                    })
            },
            updateUserProfile () {
                this.user.loading = true;
                this.user.update()
                    .then((response) => {
                        this.user.loading = false;
                        this.user = new User(response.data)
                        this.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات کاربر با موفقیت ویرایش شد'
                        });
                    })
                    .catch((error) => {
                        this.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        this.user.loading = false;
                        this.user = new User()
                    })
            },
            createUserProfile () {
                this.user.loading = true;
                this.user.create()
                    .then((response) => {
                        this.user.loading = false;
                        this.user = new User(response.data)
                        this.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات کاربر با موفقیت ثبت شد'
                        });
                        this.router.push({ name: 'user/Show', params: { id: '123' } })
                    })
                    .catch((error) => {
                        this.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        this.user.loading = false;
                        this.user = new User()
                    })
            }
        }
    }
</script>
