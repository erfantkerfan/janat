<template>
    <md-card>
        <md-card-header class="md-card-header-icon">
            <div class="card-icon">
                <md-icon>vpn_key</md-icon>
            </div>
            <h4 class="title">
                ویرایش کلمه عبور
            </h4>
        </md-card-header>
        <md-card-content>
            <div class="md-layout">
                <div class="md-layout-item md-size-100">
                    <md-field v-if="isCreateForm" class="md-invalid">
                        <label>کلمه عبور</label>
                        <md-input v-model="value.password" type="password" @input="updateUserModel"/>
                    </md-field>
                    <md-field v-if="isCreateForm" class="md-invalid">
                        <label>تکرار عبور</label>
                        <md-input v-model="value.password_confirmation" type="password" @input="updateUserModel"/>
                    </md-field>
                    <md-field v-if="!$auth.isSuperAdmin && !isCreateForm" class="md-invalid">
                        <label>کلمه عبور فعلی</label>
                        <md-input v-model="password" type="password"/>
                    </md-field>
                    <md-field v-if="!isCreateForm" class="md-invalid">
                        <label>کلمه عبور جدید</label>
                        <md-input v-model="new_password" type="password"/>
                    </md-field>
                    <md-field v-if="!isCreateForm" class="md-invalid">
                        <label>تکرار کلمه عبور جدید</label>
                        <md-input v-model="confirm_password" type="password"/>
                    </md-field>
                </div>
            </div>
            <loading :active.sync="value.loading" :is-full-page="false"></loading>
        </md-card-content>
        <md-card-actions v-if="!isCreateForm">
            <md-button @click="changePassword">
                تغییر کلمه عبور
            </md-button>
        </md-card-actions>
    </md-card>
</template>

<script>
    import {ValidationError} from "@/components"
    import {User} from '@/models/User'
    import { axiosMixin } from '@/mixins/Mixins'

    export default {
        name: "edit-password-card",
        props: {
            user: Object,
            value: {
                type: User,
                default () {
                    return new User()
                }
            }
        },
        components: {ValidationError},
        mixins: [axiosMixin],

        data: () => ({
            password: null,
            new_password: null,
            confirm_password: null
        }),

        methods: {
            isCreateForm () {
                return (this.$route.name === 'User.Create')
            },
            updateUserModel() {
                this.$emit('input', this.value)
            },
            isValidPassword (password) {
                if (!password) {
                    this.$store.dispatch('alerts/fire', {
                        icon: 'error',
                        title: 'توجه',
                        message: 'کلمه عبور را وارد کنید'
                    });
                    return false
                }

                if (password.length < 4) {
                    this.$store.dispatch('alerts/fire', {
                        icon: 'error',
                        title: 'توجه',
                        message: 'کلمه عبور باید بیش از سه کاراکتر باشد'
                    });
                    return false
                }

                return true
            },
            isValid () {
                if (this.new_password !== this.confirm_password) {
                    this.$store.dispatch('alerts/fire', {
                        icon: 'error',
                        title: 'توجه',
                        message: 'کلمه عبور جدید و تکرار کلمه عبول جدید متفاوت هستند'
                    });
                    return false
                }

                if (
                    (!this.logedInUser.hasSuperAdminRole() && (!this.isValidPassword(this.password) || !this.isValidPassword(this.new_password)))
                    ||
                    (this.logedInUser.hasSuperAdminRole() && !this.isValidPassword(this.new_password))
                ) {
                    return false
                }

                return true
            },
            changePassword() {

                if (!this.isValid()) {
                    return false
                }

                let that = this
                this.value.updatePassword(this.password, this.new_password)
                    .then((response) => {
                        that.$emit('update')
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'کلمه عبور با موفقیت ویرایش شد'
                        });
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.$emit('update')
                    })

            }
        }
    };
</script>
