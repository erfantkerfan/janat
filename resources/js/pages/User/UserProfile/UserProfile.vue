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
            <user-profile-card v-model="user" ref="userProfileCard" @update="getProfile" @updateUserPic="updateUserPic"/>
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
        data: () => ({
            user: new User()
        }),
        mounted() {
            this.getProfile();
        },
        methods: {
            updateUserPic (data) {
                this.user.loading = true;
                this.user.setUserPic(data)
                    .then((response) => {
                        this.user.loading = false;
                        this.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات کاربر با موفقیت ویرایش شد'
                        });
                        this.refreshAuthenticatedUserDataIfNeed()
                        this.$refs.userProfileCard.clearUserPicBuffer(response.data)
                    })
                    .catch((error) => {
                        this.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        this.user.loading = false;
                    })
            },
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
            refreshAuthenticatedUserDataIfNeed () {
                if (parseInt(this.authenticatedUser.id) === parseInt(this.$route.params.id)) {
                    this.$auth().refreshAuthenticatedUserData()
                }
            },
            updateUserProfile () {
                if (this.$route.name === 'Create') {
                    this.createUserProfile()
                    return
                }
                let that = this
                this.user.loading = true;
                delete this.user.password
                delete this.user.user_pic
                this.user.update()
                    .then((response) => {
                        that.user.loading = false;
                        that.user = new User(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات کاربر با موفقیت ویرایش شد'
                        });
                        that.refreshAuthenticatedUserDataIfNeed()
                    })
                    .catch((error) => {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        that.user.loading = false;
                        that.user = new User()
                    })
            },
            createUserProfile () {
                let that = this
                this.user.loading = true;
                delete this.user.user_pic
                delete this.user.created_at
                delete this.user.updated_at
                this.user.create()
                    .then((response) => {
                        that.user.loading = false;
                        that.user = new User(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات کاربر با موفقیت ثبت شد'
                        });
                        that.$router.push({ path: '/user/'+that.user.id })
                    })
                    .catch((error) => {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        that.user.loading = false;
                        that.user = new User()
                    })
            }
        }
    }
</script>
