<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-small-size-100">
            <loading :active.sync="fund.loading" :is-full-page="false"></loading>
            <div class="md-layout">
                <div class="md-layout-item md-size-100">
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
                            <div class="stats">
                                <md-button
                                    class="md-dense md-raised md-success"
                                    :to="{ name: 'Fund.Show', params: {id: fund.id} }">
                                    مشاهده اطلاعات صندوق
                                </md-button>
                            </div>
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
                                    <md-icon>payments</md-icon>
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
                                <div class="stats">
                                    <md-button
                                        class="md-dense md-raised md-success"
                                        @click="createTransaction"
                                    >
                                        ثبت تراکنش پرداخت
                                    </md-button>
                                </div>
                            </md-card-actions>
                        </md-card>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment'
    import {Fund} from "@/models/Fund"
    import {Transaction} from "@/models/Transaction"
    import { StatsCard } from "@/components"
    import PriceInput from '@/components/PriceInput'
    import ListPagination from '@/components/ListPagination'
    import { priceFilterMixin, axiosMixin, getFilterDropdownMixin } from '@/mixins/Mixins'

    export default {
        name: 'FundAddPayment',
        components: { StatsCard, ListPagination, PriceInput },
        mixins: [priceFilterMixin, axiosMixin, getFilterDropdownMixin],
        data: () => ({
            fund: new Fund(),
            transaction: new Transaction()
        }),
        mounted() {
            this.getData()
            this.getTransactionStatus(false)
        },
        methods: {
            getData () {
                this.getFund()
                this.loadTransactionDefaultData()
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
            loadTransactionDefaultData () {
                this.transaction.transaction_status.id = 1
                this.transaction.paid_at = moment().format('YYYY-MM-DD HH:mm:ss')
            },
            createTransaction () {
                this.transaction.loading = true;
                this.transaction.transaction_type = 'pay_fund_expenses'
                this.transaction.fund_id = this.$route.params.fund_id
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
