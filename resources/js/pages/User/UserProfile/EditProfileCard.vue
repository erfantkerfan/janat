<template>
    <md-card>

        <md-card-header class="md-card-header-icon md-card-header-green">
            <div class="card-icon">
                <md-icon>perm_identity</md-icon>
            </div>
            <h4 class="title">
                ویرایش اطلاعات
            </h4>
        </md-card-header>

        <md-card-content>

            <div v-if="!isCreateForm()" class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    کد عضویت:
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-input v-model="value.id" @input="updateUserModel" />
                    </md-field>
                </div>
            </div>
            <div class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    نام
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-input v-model="value.f_name" @input="updateUserModel" />
                    </md-field>
                </div>
            </div>
            <div class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    نام خانوادگی
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-input v-model="value.l_name" @input="updateUserModel"/>
                    </md-field>
                </div>
            </div>
            <div class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    نام پدر
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-input v-model="value.father_name" @input="updateUserModel"/>
                    </md-field>
                </div>
            </div>
            <div class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    کد ملی
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-input v-model="value.SSN" @input="updateUserModel"/>
                    </md-field>
                </div>
            </div>
            <div class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    کد پرسنلی
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-input v-model="value.staff_code" @input="updateUserModel"/>
                    </md-field>
                </div>
            </div>
            <price-input
                v-model="value.salary"
                :label="'حقوق ماهیانه'"
                @input="updateUserModel"
            />
            <div class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    تلفن ثابت
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-input v-model="value.phone" @input="updateUserModel"/>
                    </md-field>
                </div>
            </div>
            <div class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    تلفن همراه
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-input v-model="value.mobile" @input="updateUserModel"/>
                    </md-field>
                </div>
            </div>
            <div class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    ایمیل
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-input v-model="value.email" @input="updateUserModel"/>
                    </md-field>
                </div>
            </div>
            <div class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    آدرس
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-textarea v-model="value.address" @input="updateUserModel"/>
                    </md-field>
                </div>
            </div>
            <div class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    توضیحات
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-textarea v-model="value.description" @input="updateUserModel"/>
                    </md-field>
                </div>
            </div>
            <div v-if="!isCreateForm()" class="md-layout">
                <label class="md-layout-item md-size-15 md-form-label">
                    تاریخ ایجاد کاربر
                </label>
                <div class="md-layout-item">
                    <md-field class="md-invalid">
                        <md-input v-model="value.shamsiDate('created_at').date" :disabled="true"/>
                    </md-field>
                </div>
            </div>

            <loading :active.sync="value.loading" :is-full-page="false"></loading>

        </md-card-content>

    </md-card>
</template>
<script>
    import {ValidationError} from "@/components";
    import formMixin from "@/mixins/form-mixin";
    import {User} from '@/models/User';
    import PriceInput from "@/components/PriceInput";

    export default {
        name: "edit-profile-card",

        props: {
            value: {
                type: User,
                default () {
                    return new User()
                }
            }
        },

        components: {PriceInput, ValidationError},

        mixins: [formMixin],

        data() {
            return {
                default_img: "/img/placeholder.jpg",
            }
        },

        methods: {
            isCreateForm () {
                return (this.$route.name === 'User.Create')
            },
            fireUpdateEvent () {
                this.$emit('update', this.value)
            },
            updateUserModel() {
                this.$emit('input', this.value)
            }
            // async updateProfile() {
            //     if (["1", "2", "3"].includes(this.user.id)) {
            //         await this.$store.dispatch("alerts/error", "You are not allowed not change data of default users.")
            //         return
            //     }
            //
            //     try {
            //         await this.$store.dispatch("profile/update", this.user)
            //         await this.$store.dispatch("alerts/success", "Profile updated successfully.")
            //         await this.$store.getters["profile/me"]
            //     } catch (e) {
            //         await this.$store.dispatch("alerts/error", "Oops, something went wrong!")
            //         this.setApiValidation(e.response.data.errors)
            //     }
            // }

        }
    };
</script>

<style></style>
