<template>
    <md-card class="md-card-profile">
        <div class="md-card-avatar">
            <img @click="$refs.userProfilePic.click()" class="img" :src="cardUserImage"/>
        </div>
        <md-card-content>
            <div v-if="cardUserNewImage !== null">
                <md-button @click="clearUserPicBuffer" class="md-icon-button md-warning">
                    <md-icon>clear</md-icon>
                </md-button>
                <md-button @click="updateUserPic" class="md-icon-button md-success">
                    <md-icon>check</md-icon>
                </md-button>
            </div>
            <input v-if="!isCreateForm() && cardUserNewImage === null" v-show="false" type="file" ref="userProfilePic"
                   @change="bufferUserPic($event)"/>
            <h6 class="category text-gray">{{ value.f_name }} {{ value.l_name }}</h6>
<!--            <h4 class="card-title">{{ value.company.name }}</h4>-->
            <p class="card-description">
                {{ value.description }}
            </p>
            <md-empty-state
                v-if="!isCreateForm() && value.accounts.list.length === 0"
                class="md-warning"
                md-icon="info"
                md-label="حسابی یافت نشد"
            >
            </md-empty-state>
            <md-card v-if="!isCreateForm()">
                <md-card-header class="md-card-header-text md-card-header-blue" style="text-align: right">
                    <div class="card-icon" style="padding: 0">
                        <md-button
                            class="md-dense md-raised md-primary"
                            style="margin: 0"
                            @click="showAddAccountDialog">
                            ایجاد حساب جدید
                        </md-button>
                    </div>
                    <div v-if="value.accounts.list.length > 0" class="card-text">
                        <h4 class="title">حساب های کاربر</h4>
                        <p class="category">
                            تعداد حساب های کاربر:
                            {{ value.accounts.list.length }}
                        </p>
                    </div>
                </md-card-header>
                <md-card-content v-if="value.accounts.list.length > 0">
                    <md-table
                        :value="value.accounts.list"
                        :md-sort.sync="sortation.field"
                        :md-sort-order.sync="sortation.order"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="نام صندوق" md-sort-by="name">{{ item.fund.name }}</md-table-cell>
                            <md-table-cell md-label="شماره حساب" md-sort-by="email">{{ item.id }}</md-table-cell>
                            <md-table-cell md-label="تاریخ عضویت" md-sort-by="created_at">
                                {{ item.shamsiDate('joined_at').date }}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <md-button
                                    class="md-icon-button md-raised md-round md-info"
                                    style="margin: .2rem;"
                                    @click="showEditAccountDialog(item)"
                                >
                                    <md-icon>edit</md-icon>
                                </md-button>
                                <md-button
                                    class="md-icon-button md-raised md-round md-danger"
                                    style="margin: .2rem;"
                                    @click="confirmRemoveAccount(item)"
                                >
                                    <md-icon>delete</md-icon>
                                </md-button>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
                    <md-button v-if="!calcedUserTotalBalance"
                               class="md-dense md-raised md-primary"
                               style="margin: 0"
                               @click="getUserTotalBalance">
                        مشاهده کل موجودی کاربر در تمام حساب ها
                    </md-button>
                    <price-input v-if="calcedUserTotalBalance"
                                 v-model="userTotalBalance"
                                 :label="'مجموع کل موجودی کاربر در تمام حساب ها'"
                                 :label-size="40"
                                 disabled
                    />
                </md-card-content>
            </md-card>
            <md-field>
                <label>وضعیت کاربر:</label>
                <md-select v-model="value.status.id" name="pages">
                    <md-option
                        v-for="item in userStatuses.list"
                        :key="item.id"
                        :label="item.display_name"
                        :value="item.id"
                    >
                        {{ item.display_name }}
                    </md-option>
                </md-select>
            </md-field>
            <md-field>
                <label>نوع کاربر:</label>
                <md-select v-model="value.user_type.id" name="pages">
                    <md-option
                        v-for="item in userTypes.list"
                        :key="item.id"
                        :label="item.display_name"
                        :value="item.id"
                    >
                        {{ item.display_name }}
                    </md-option>
                </md-select>
            </md-field>
<!--            <md-field>-->
<!--                <label>شرکت کاربر:</label>-->
<!--                <md-select v-model="value.company.id" name="pages">-->
<!--                    <md-option-->
<!--                        v-for="item in companies.list"-->
<!--                        :key="item.id"-->
<!--                        :label="item.name"-->
<!--                        :value="item.id"-->
<!--                    >-->
<!--                        {{ item.name }}-->
<!--                    </md-option>-->
<!--                </md-select>-->
<!--            </md-field>-->

            <md-dialog :md-active.sync="createAccountShowDialog">
                <md-dialog-title v-if="editAccountState">ویرایش حساب</md-dialog-title>
                <md-dialog-title v-else>ایجاد حساب جدید</md-dialog-title>

                <md-dialog-content>
                    <div>
                        <div v-if="funds.list.length === 0">
                            <md-empty-state
                                class="md-warning"
                                md-icon="info"
                                md-label="صندوقی یافت نشد"
                                md-description="برای ایجاد حساب ابتدا یک صندوق برای سیستم تعریف کنید"
                            >
                            </md-empty-state>
                            <md-button
                                class="md-dense md-raised md-primary"
                                @click="createAccountShowDialog = true">
                                برای ایجاد صندوق کلیک کنید
                            </md-button>
                        </div>
                        <div v-if="funds.list.length > 0">
                            <div v-if="editAccountState" class="md-layout">
                                شماره حساب:
                                {{ newAccount.id }}
                                <br>
                            </div>
                            <md-field>
                                <label>انتخاب صندوق:</label>
                                <md-select v-model="newAccount.fund.id">
                                    <md-option
                                        v-for="item in funds.list"
                                        :key="item.id"
                                        :label="item.name"
                                        :value="item.id"
                                    >
                                        {{ item.name }}
                                    </md-option>
                                </md-select>
                            </md-field>
                            <md-field>
                                <label>انتخاب شرکت:</label>
                                <md-select v-model="newAccount.company.id">
                                    <md-option
                                        v-for="item in companies.list"
                                        :key="item.id"
                                        :label="item.name"
                                        :value="item.id"
                                    >
                                        {{ item.name }}
                                    </md-option>
                                </md-select>
                            </md-field>
                            <div class="md-layout">
                                <md-checkbox v-model="newAccount.payroll_deduction">کسر از حقوق</md-checkbox>
                            </div>
                            <price-input
                                v-model="newAccount.monthly_payment"
                                :label="'ماهانه'"
                            />
                            <hr>
                            <div class="md-layout">
                                <md-button
                                    v-if="editAccountState && showAccountBalanceBtn"
                                    @click="showAccountBalance"
                                    :disabled="newAccount.loading"
                                >
                                    مشاهده موجودی
                                    <md-progress-spinner v-if="newAccount.loading" :md-diameter="30" :md-stroke="3"
                                                         md-mode="indeterminate"></md-progress-spinner>
                                </md-button>


                                <price-input
                                    v-if="!showAccountBalanceBtn"
                                    v-model="newAccount.balance"
                                    :label="'جمع پرداختی های ماهانه و کمک مالی به صندوق'"
                                    :label-size="40"
                                    :disabled="true"
                                />

                            </div>
                            <hr>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-40 md-form-label">
                                    تاریخ عضویت
                                </label>
                                <div class="md-layout-item md-size-100">
                                    <date-picker
                                        v-model="newAccount.joined_at"
                                        type="datetime"
                                        :editable="true"
                                        format="YYYY-MM-DD HH:mm:ss"
                                        display-format="dddd jDD jMMMM jYYYY ساعت HH:mm"/>
                                </div>
                            </div>

                            <div class="md-layout">
                                <md-button v-if="funds.list.length > 0 && editAccountState"
                                           class="md-layout-item md-size-100 md-success"
                                           :to="{
                                                name: 'User.AddPayment',
                                                params: {
                                                    user_id: value.id,
                                                    account_id: newAccount.id,
                                                    fund_id: newAccount.fund.id,
                                                    payment_type: 'account_charge_fund'
                                                }
                                            }"
                                >
                                    واریز وجه از طرف کاربر به صندوق
                                </md-button>
                            </div>
                            <div class="md-layout">
                                <md-button v-if="funds.list.length > 0 && editAccountState"
                                           class="md-layout-item md-size-100 md-success"
                                           :to="{
                                                name: 'User.AddPayment',
                                                params: {
                                                    user_id: value.id,
                                                    account_id: newAccount.id,
                                                    fund_id: newAccount.fund.id,
                                                    payment_type: 'account_pay_the_fund_tuition'
                                                }
                                            }"
                                >
                                    پرداخت ماهانه کاربر به صندوق
                                </md-button>
                            </div>
                            <div class="md-layout">
                                <md-button v-if="funds.list.length > 0 && editAccountState"
                                           class="md-layout-item md-size-100 md-success"
                                           :to="{
                                                name: 'User.AddPayment',
                                                params: {
                                                    user_id: value.id,
                                                    account_id: newAccount.id,
                                                    fund_id: newAccount.fund.id,
                                                    payment_type: 'user_withdraw_from_account'
                                                }
                                            }"
                                >
                                    برداشت وجه کاربر از صندوق
                                </md-button>
                            </div>
                            <div class="md-layout">
                                <md-button v-if="funds.list.length > 0 && editAccountState"
                                           class="md-success md-layout-item md-size-100"
                                           :to="{
                                                    name: 'AllocatedLoan.Create',
                                                    params: {
                                                        user_id: value.id,
                                                        account_id: newAccount.id,
                                                    }
                                                }"
                                >
                                    تخصیص وام
                                </md-button>
                            </div>
                        </div>
                    </div>
                </md-dialog-content>

                <md-dialog-actions>
                    <md-button class="md-default" @click="createAccountShowDialog = false">انصراف</md-button>
                    <md-button v-if="funds.list.length > 0 && !editAccountState"
                               class="md-success"
                               @click="createNewAccount"
                    >
                        ذخیره
                    </md-button>
                    <md-button v-if="funds.list.length > 0 && editAccountState"
                               class="md-success"
                               @click="editAccount"
                    >
                        ذخیره
                    </md-button>
                </md-dialog-actions>
            </md-dialog>

            <loading :active.sync="companies.loading || userStatuses.loading || value.loading"
                     :is-full-page="false"></loading>

            <vue-confirm-dialog></vue-confirm-dialog>

        </md-card-content>
    </md-card>
</template>

<script>
import {User} from '@/models/User'
import {Account} from '@/models/Account'
import PriceInput from '@/components/PriceInput'
import {priceFilterMixin, getFilterDropdownMixin, axiosMixin} from '@/mixins/Mixins'

export default {
    name: 'user-profile-card',
    components: {PriceInput},
    watch: {
        'value.status.id': function () {
            this.value.status_id = this.value.status.id
        },
        'value.company.id': function () {
            this.value.company_id = this.value.company.id
        },
        'newAccount.fund.id': function () {
            this.newAccount.fund_id = this.newAccount.fund.id
        }
    },
    mixins: [priceFilterMixin, getFilterDropdownMixin, axiosMixin],
    props: {
        value: {
            type: User,
            default() {
                return new User()
            }
        }
    },
    data() {
        return {
            userTotalBalance: 0,
            calcedUserTotalBalance: false,
            newAccount: new Account(),
            createAccountShowDialog: false,
            showAccountBalanceBtn: true,
            editAccountState: false,
            cardUserImage: '',
            cardUserNewImage: null,
            sortation: {
                field: "created_at",
                order: "asc"
            },
        };
    },
    mounted() {
        this.getFunds(false)
        this.getUserPic()
        this.getCompanies(false)
        this.getUserTypes()
        this.getUserStatus()
    },
    methods: {
        isCreateForm() {
            return (this.$route.name === 'User.Create')
        },
        showAccountBalance() {
            this.newAccount.loading = true
            this.newAccount.getBalance()
                .then((response) => {
                    this.showAccountBalanceBtn = false
                    this.newAccount.loading = false
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    this.newAccount.loading = false
                    this.showAccountBalanceBtn = true
                })
        },
        bufferUserPic($event) {
            const toBase64 = file => new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });

            let that = this
            toBase64($event.target.files[0])
                .then((result) => {
                    that.cardUserNewImage = $event.target.files[0]
                    that.cardUserImage = result
                })
                .catch((error) => {
                    that.axios_handleError(error)
                    that.clearUserPicBuffer()
                })
        },
        getUserTotalBalance() {
            this.value.loading = true
            this.value.getTotalBalance()
                .then(response => {
                    this.calcedUserTotalBalance = true
                    this.userTotalBalance = response.data
                    this.value.loading = false
                })
                .catch(error => {
                    this.axios_handleError(error)
                    this.value.loading = false
                    this.newAccount.loading = false
                    this.showAccountBalanceBtn = true
                })
        },
        clearUserPicBuffer(newUserPic) {
            this.cardUserNewImage = null
            if (!newUserPic) {
                newUserPic = this.authenticatedUser.user_pic
            }
            this.cardUserImage = newUserPic
        },
        updateUserPic() {
            this.$emit('updateUserPic', this.cardUserNewImage)
        },
        updateUserModel() {
            this.value.status_id = this.value.status.id
            // this.value.company_id = this.value.company.id
            this.$emit('input', this.value)
        },
        showAddAccountDialog() {
            this.newAccount = new Account()
            this.editAccountState = false
            this.createAccountShowDialog = true
            this.showAccountBalanceBtn = true
        },
        showEditAccountDialog(item) {
            this.newAccount = item
            this.editAccountState = true
            this.createAccountShowDialog = true
            this.showAccountBalanceBtn = true
        },
        closeAccountDialog() {
            this.createAccountShowDialog = false
        },
        createNewAccount() {
            let that = this
            this.value.loading = true
            this.updateUserModel()
            this.newAccount.user_id = this.$route.params.id
            if (!this.newAccount.payroll_deduction) {
                this.newAccount.payroll_deduction = false
            }
            this.newAccount.create()
                .then((response) => {
                    this.value.loading = false
                    that.$emit('update', this.value)
                    that.$store.dispatch('alerts/fire', {
                        icon: 'success',
                        title: 'توجه',
                        message: 'حساب جدید با موفقیت ثبت شد'
                    });
                    that.closeAccountDialog()
                })
                .catch((error) => {
                    this.value.loading = false
                    this.axios_handleError(error)
                })
        },
        editAccount() {
            let that = this
            this.value.loading = true
            this.updateUserModel()
            this.newAccount.user_id = this.$route.params.id
            this.newAccount.update()
                .then((response) => {
                    this.value.loading = false
                    that.$emit('update', this.value)
                    that.$store.dispatch('alerts/fire', {
                        icon: 'success',
                        title: 'توجه',
                        message: 'حساب با موفقیت ویرایش شد'
                    });
                    that.closeAccountDialog()
                })
                .catch((error) => {
                    this.value.loading = false
                    this.axios_handleError(error)
                })
        },
        confirmRemoveAccount(item) {
            let that = this
            this.$confirm(
                {
                    message: `از حذف حساب اطمینان دارید؟`,
                    button: {
                        no: 'خیر',
                        yes: 'بله'
                    },
                    callback: confirm => {
                        if (confirm) {
                            that.removeAccount(item)
                        }
                    }
                }
            )
        },
        removeAccount(item) {
            item.loading = true;
            let that = this;
            item.delete()
                .then(function (response) {
                    that.$emit('update')
                    that.$store.dispatch('alerts/fire', {
                        icon: 'success',
                        title: 'توجه',
                        message: 'حساب با موفقیت حذف شد'
                    });
                })
                .catch((error) => {
                    that.axios_handleError(error)
                    that.$emit('update')
                    item.editMode = false;
                    item.loading = false;
                });
        },
        getUserPic() {
            this.value.getUserPic(this.$route.params.id)
                .then((response) => {
                    this.cardUserImage = response.data
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    // this.user.loading = false;
                    // this.user = new User()
                })
        }
    }
};
</script>

<style>
.md-dialog-container {
    max-height: fit-content;
}
</style>
