<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-small-size-100">
            <loading :active.sync="user.loading || fund.loading" :is-full-page="false"></loading>
            <div class="md-layout">
                <div class="md-layout-item md-size-50 md-small-size-100">
                    <stats-card header-color="rose">
                        <template slot="header">
                            <div class="card-icon">
                                <md-icon>account_balance</md-icon>
                            </div>
                        </template>
                        <template slot="content">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    نام صندوق
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="fund.name" disabled/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    ماهانه تعریف شده برای حساب کاربر
                                </label>
                                <div class="md-layout-item">
                                    {{ account.monthly_payment | currencyFormat}}
                                    <br>
                                    {{ digitsToWords(account.monthly_payment) }} {{ currencyUnit }}
                                </div>
                            </div>
                            <hr>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    موجودی صندوق
                                </label>
                                <div class="md-layout-item">
                                    {{ fund.balance | currencyFormat}}
                                    <br>
                                    {{ digitsToWords(fund.balance) }} {{ currencyUnit }}
                                </div>
                            </div>
                            <hr>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    تاریخ تعریف صندوق
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="fund.shamsiDate('created_at').date" disabled/>
                                    </md-field>
                                </div>
                            </div>
                        </template>
                        <template slot="footer">
                            <md-button
                                class="md-dense md-raised md-success"
                                :to="{ name: 'Fund.Show', params: {id: fund.id} }">
                                مشاهده اطلاعات صندوق
                            </md-button>
                        </template>
                    </stats-card>
                </div>
                <div class="md-layout-item md-size-50 md-small-size-100">
                    <stats-card header-color="rose">
                        <template slot="header">
                            <div class="card-icon">
                                <md-icon>store_mall_directory</md-icon>
                            </div>
                            <p class="category">اطلاعات کاربر</p>
                        </template>
                        <template slot="content">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    کد عضویت:
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="user.id" />
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    نام
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="user.f_name"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    نام خانوادگی
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="user.l_name"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    کد ملی
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="user.SSN"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    کد پرسنلی
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="user.staff_code"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    تلفن ثابت
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="user.phone"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    تلفن همراه
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="user.mobile"/>
                                    </md-field>
                                </div>
                            </div>
                        </template>
                        <template slot="footer">
                            <md-button
                                class="md-dense md-raised md-success"
                                :to="{ name: 'User.Show', params: {id: user.id} }">
                                مشاهده اطلاعات شرکت
                            </md-button>
                        </template>
                    </stats-card>
                </div>
            </div>
            <div class="md-layout-item md-size-100">
                <div class="md-layout">
                    <div class="md-layout-item md-size-100">
                        <md-card>
                            <md-card-header class="md-card-header-icon md-card-header-green">
                                <div class="card-icon">
                                    <md-icon>credit_card</md-icon>
                                </div>
                                <h4 class="title">
                                    اطلاعات تراکنش
                                </h4>
                            </md-card-header>
                            <md-card-content>
                                <price-input
                                    v-model="transaction.cost"
                                    :label="'مبلغ'"
                                />
                                <div class="md-layout">
                                    <label class="md-layout-item md-size-15 md-form-label">
                                        توضیحات مدیر
                                    </label>
                                    <div class="md-layout-item">
                                        <md-field class="md-invalid">
                                            <md-textarea v-model="transaction.manager_comment" />
                                        </md-field>
                                    </div>
                                </div>
                                <div class="md-layout">
                                    <label class="md-layout-item md-size-15 md-form-label">
                                        توضیحات کاربر
                                    </label>
                                    <div class="md-layout-item">
                                        <md-field class="md-invalid">
                                            <md-textarea v-model="transaction.user_comment" />
                                        </md-field>
                                    </div>
                                </div>
                                <md-field>
                                    <label>وضعیت تراکنش</label>
                                    <md-select v-model="transaction.transaction_status.id" name="pages">
                                        <md-option
                                            v-for="item in transactionStatuses.list"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.id"
                                        >
                                            {{ item.display_name }}
                                        </md-option>
                                    </md-select>
                                </md-field>
                                <div class="md-layout">
                                    <label class="md-layout-item md-size-15 md-form-label">
                                        مهلت پرداخت
                                    </label>
                                    <div class="md-layout-item">
                                        <date-picker
                                            v-model="transaction.deadline_at"
                                            type="datetime"
                                            :editable="true"
                                            format="YYYY-MM-DD HH:mm:ss"
                                            display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                                    </div>
                                </div>
                                <div class="md-layout">
                                    <label class="md-layout-item md-size-15 md-form-label">
                                        تاریخ پرداخت
                                    </label>
                                    <div class="md-layout-item">
                                        <date-picker
                                            v-model="transaction.paid_at"
                                            type="datetime"
                                            :editable="true"
                                            format="YYYY-MM-DD HH:mm:ss"
                                            display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                                    </div>
                                </div>
                                <loading :active.sync="transaction.loading || transactionStatuses.loading" :is-full-page="false"></loading>
                            </md-card-content>
                            <md-card-actions>
                                <md-button
                                    class="md-dense md-raised md-success"
                                    @click="createTransaction"
                                >
                                    ثبت تراکنش پرداخت
                                </md-button>
                            </md-card-actions>
                        </md-card>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ListPagination from '@/components/ListPagination'
    import { StatsCard } from "@/components"
    import { priceFilterMixin, axiosMixin, getFilterDropdownMixin } from '@/mixins/Mixins'
    import moment from 'moment'
    import {Transaction} from "@/models/Transaction";
    import {Fund} from "@/models/Fund";
    import PriceInput from '@/components/PriceInput'
    import {User} from "@/models/User";
    import {Account} from "@/models/Account";

    export default {
        name: 'Create',
        components: { StatsCard, ListPagination, PriceInput },
        mixins: [priceFilterMixin, axiosMixin, getFilterDropdownMixin],
        data: () => ({
            fund: new Fund(),
            account: new Account(),
            user: new User(),
            transaction: new Transaction()
        }),
        mounted() {
            this.getData()
            this.getTransactionStatus(false)
        },
        methods: {
            getData () {
                this.getFund()
                this.getAccount()
                this.getUser()
                this.loadTransactionDefaultData()
            },
            getAccount () {
                this.account.loading = true;
                this.account.show(this.$route.params.account_id)
                    .then((response) => {
                        this.account.loading = false;
                        this.account = new Account(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.account.loading = false;
                        this.account = new Account()
                    })
            },
            getFund () {
                this.fund.loading = true;
                this.fund.show(this.$route.params.fund_id)
                    .then((response) => {
                        this.fund.loading = false;
                        this.fund = new Fund(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.fund.loading = false;
                        this.fund = new Fund()
                    })
            },
            getUser () {
                this.user.loading = true;
                this.user.show(this.$route.params.user_id)
                    .then((response) => {
                        this.user.loading = false;
                        this.user = new User(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.user.loading = false;
                        this.user = new User()
                    })
            },
            loadTransactionDefaultData () {
                this.transaction.transaction_status.id = 1
                this.transaction.paid_at = moment().format('YYYY-MM-DD HH:mm:ss')
            },
            createTransaction () {
                this.transaction.loading = true;
                this.transaction.transaction_type = this.$route.params.payment_type
                this.transaction.account_id = this.$route.params.account_id
                let that = this
                this.transaction.create()
                    .then((response) => {
                        that.transaction.loading = false;
                        that.transaction = new Transaction(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات تراکنش با موفقیت ثبت شد'
                        });
                        that.getData()
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.transaction.loading = false
                        that.getData()
                    })
            }
        }
    }
</script>
