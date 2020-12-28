<template>
    <div>
        <div class="md-layout md-gutter">
            <div class="md-layout-item md-size-60 md-small-size-100">
                <div class="md-layout-item md-size-100">
                    <user-edit-card v-model="user" @update="updateUserProfile"/>
                </div>
            </div>
            <div class="md-layout-item md-size-40 md-small-size-100">
                <user-profile-card v-model="user"
                                   ref="userProfileCard"
                                   @update="getProfile"
                                   @updateUserPic="updateUserPic"
                />
            </div>
        </div>
        <div v-if="false" class="md-layout md-gutter">
            <div
                class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-33"
            >
                <md-card
                    :data-count="0"
                    class="md-card-chart"
                >
                    <md-card-header
                        :class="[
                            { 'md-card-header-blue': true },
                            { 'md-card-header-text': false },
                            { 'md-card-header-icon': false }
                          ]"
                    >
                        <chart :chart-data="datacollection"/>
                    </md-card-header>

                    <md-card-content>
                        md-card-content
                        <slot name="content"></slot>
                    </md-card-content>

                    <md-card-actions md-alignment="left">
                        footer
                    </md-card-actions>
                </md-card>
            </div>
        </div>
        <div class="md-layout md-gutter">
            <div class="md-layout-item md-size-100">
                <loans :user="user"/>
            </div>
            <div class="md-layout-item md-size-100">
                <user-password-card v-model="user" @update="getProfile"/>
            </div>
        </div>
    </div>
</template>

<script>
    import Chart from '@/components/Chart'
    import Loans from '@/pages/User/UserProfile/Loans.vue';
    import UserEditCard from '@/pages/User/UserProfile/EditProfileCard.vue';
    import UserProfileCard from '@/pages/User/UserProfile/UserProfileCard.vue';
    import UserPasswordCard from '@/pages/User/UserProfile/EditPasswordCard.vue';
    import {User} from '@/models/User';

    export default {
        name: 'user-profile-example',
        components: {
            Loans,
            Chart,
            'user-profile-card': UserProfileCard,
            'user-edit-card': UserEditCard,
            'user-password-card': UserPasswordCard
        },
        data: () => ({
            datacollection: null,
            user: new User(),
            emailsSubscriptionChart: {
                data: {
                    labels: {
                        labels: ['Bananas', 'Apples', 'Grapes'],
                        series: [20, 15, 40]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Chart.js Doughnut Chart'
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    },
                    responsiveOptions: [
                    ]
                }
            }
        }),
        created() {
            this.getProfile()
            this.fillData()
        },
        methods: {
            isCreateForm () {
                return (this.$route.name === 'User.Create')
            },
            fillData () {
                this.datacollection = {
                    datasets: [{
                        data: [10, 20, 30],
                        backgroundColor: ['#ff742d', '#22ff25', '#fff'],
                        label: 'Dataset 1'
                    }],
                    labels: ['ff742d', '22ff25', 'fff']
                }
            },
            updateUserPic(data) {
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
            getProfile() {
                if (this.isCreateForm()) {
                    this.user.id = 0
                    this.user.status.id = 0
                    this.user.company.id = 0
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
            refreshAuthenticatedUserDataIfNeed() {
                if (parseInt(this.authenticatedUser.id) === parseInt(this.$route.params.id)) {
                    this.$auth().refreshAuthenticatedUserData()
                }
            },
            updateUserProfile() {
                if (this.isCreateForm()) {
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
                        that.getProfile()
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
            createUserProfile() {
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
                        that.$router.push({path: '/user/' + that.user.id})
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
