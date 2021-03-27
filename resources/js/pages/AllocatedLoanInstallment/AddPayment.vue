<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-small-size-100">
            <div class="md-layout-item md-size-100">
                <div class="md-layout">
                    <div class="md-layout-item md-size-100">
                        <stats-card header-color="rose">
                            <template slot="header">
                                <div class="card-icon">
                                    <md-icon>monetization_on</md-icon>
                                </div>
                                <p class="category">اطلاعات وام تخصیص داده شده</p>
                                تاریخ ایجاد:
                                {{ allocatedLoan.shamsiDate('created_at').date }}
                                <br>
                                مبلغ وام:
                                {{ allocatedLoan.loan_amount | currencyFormat }}
                                <br>
                                مبلغ هر قسط وام:
                                {{ allocatedLoan.installment_rate | currencyFormat }}
                                <br>
                                تعداد اقساط:
                                {{ allocatedLoan.number_of_installments }}
                                <hr>
                                وضعیت قسط:
                                {{ (allocatedLoanInstallment.is_settled) ? 'تسویه شده' : 'تسویه نشده' }}
                                <br>
                                مبلغ قسط:
                                {{ allocatedLoanInstallment.rate | currencyFormat }}
                                <br>
                                تعداد پرداختی ها:
                                {{ allocatedLoanInstallment.received_transactions.list.length }}
                                <br>
                                جمع کل پرداختی این قسط:
                                {{ allocatedLoanInstallment.total_payments | currencyFormat }}
                                <br>
                                مبلغ باقیمانده جهت پرداخت این قسط:
                                {{ allocatedLoanInstallment.remaining_payable_amount | currencyFormat }}
                            </template>
                            <template slot="content">
                                <md-table v-model="allocatedLoanInstallment.received_transactions.list" table-header-color="orange">
                                    <md-table-row slot="md-table-row" slot-scope="{ item }">
                                        <md-table-cell md-label="مبلغ تراکنش">{{ item.cost | currencyFormat }}</md-table-cell>
                                        <md-table-cell md-label="تاریخ پرداخت">{{ item.shamsiDate('paid_at').date }}</md-table-cell>
                                        <md-table-cell md-label="وضعیت">{{ item.transaction_status.display_name }}</md-table-cell>
                                        <md-table-cell md-label="توضیحات کاربر">{{ item.user_comment }}</md-table-cell>
                                        <md-table-cell md-label="توضیحات مدیر">{{ item.manager_comment }}</md-table-cell>
                                        <md-table-cell md-label="مشاهده">
                                            <md-button
                                                :to="'/transactions/'+item.id"
                                                class="md-icon-button md-raised md-round md-info"
                                                style="margin: .2rem;"
                                            >
                                                <md-icon>pageview</md-icon>
                                                <md-tooltip md-direction="top">مشاهده</md-tooltip>
                                            </md-button>
                                        </md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </template>
                            <template slot="footer">
                                <div class="stats">
                                    <md-button
                                        class="md-dense md-raised md-success"
                                        :to="{ name: 'AllocatedLoan.Show', params: {id: allocatedLoan.id} }">
                                        مشاهده اطلاعات وام تخصیص داده شده
                                    </md-button>
                                </div>
                            </template>
                        </stats-card>
                        <loading :active.sync="allocatedLoan.loading" :is-full-page="false"></loading>
                    </div>
                    <div v-if="!allocatedLoanInstallment.is_settled" class="md-layout-item md-size-100">
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
                                <div class="md-layout">
                                    <label class="md-layout-item md-size-15 md-form-label">
                                        مبلغ
                                    </label>
                                    <div class="md-layout-item">
                                        <md-field class="md-invalid">
<!--                                            <md-input :value="transaction.cost | currencyFormat" @input="currencyFormatInput"/>-->
                                            <md-input v-model="transaction.cost"/>
                                        </md-field>
                                    </div>
                                </div>
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
    import ListPagination from '@/components/ListPagination'
    import { StatsCard } from "@/components"
    import { AllocatedLoan } from '@/models/AllocatedLoan'
    import { AllocatedLoanInstallment } from "@/models/AllocatedLoanInstallment"
    import { priceFilterMixin, axiosMixin, getFilterDropdownMixin } from '@/mixins/Mixins'
    import moment from 'moment'
    import {Transaction} from "@/models/Transaction";

    export default {
        name: 'Create',
        watch: {
            'allocatedLoan.fund.id': function () {
                this.allocatedLoan.fund_id = this.allocatedLoan.fund.id
            }
        },
        components: { StatsCard, ListPagination },
        mixins: [priceFilterMixin, axiosMixin, getFilterDropdownMixin],
        data: () => ({
            allocatedLoan: new AllocatedLoan(),
            allocatedLoanInstallment: new AllocatedLoanInstallment(),
            transaction: new Transaction()
        }),
        mounted() {
            this.getData()
            this.getTransactionStatus(false)
        },
        methods: {
            getData () {
                this.getAllocatedLoan()
                this.getAllocatedLoanInstallment()
            },
            getAllocatedLoan () {
                this.allocatedLoan.loading = true;
                this.allocatedLoan.show(this.$route.params.allocated_loan_id)
                    .then((response) => {
                        this.allocatedLoan.loading = false;
                        this.allocatedLoan = new AllocatedLoan(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.allocatedLoan.loading = false;
                        this.allocatedLoan = new AllocatedLoan()
                    })
            },
            getAllocatedLoanInstallment () {
                this.allocatedLoanInstallment.loading = true;
                this.allocatedLoanInstallment.show(this.$route.params.allocated_loan_installment_id)
                    .then((response) => {
                        this.allocatedLoanInstallment.loading = false;
                        this.allocatedLoanInstallment = new AllocatedLoanInstallment(response.data)
                        this.loadTransactionFromAllocatedLoanData()
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.allocatedLoanInstallment.loading = false;
                        this.allocatedLoanInstallment = new AllocatedLoanInstallment()
                    })
            },
            loadTransactionFromAllocatedLoanData () {
                this.transaction.cost = this.allocatedLoanInstallment.remaining_payable_amount
                this.transaction.transaction_status.id = 1
                this.transaction.paid_at = moment().format('YYYY-MM-DD HH:mm:ss')
            },
            createTransaction () {
                this.transaction.loading = true;
                this.transaction.transaction_type = 'user_pay_installment'
                this.transaction.allocated_loan_installment_id = this.$route.params.allocated_loan_installment_id
                let that = this
                this.transaction.create()
                    .then((response) => {
                        that.transaction.loading = false;
                        that.transaction = new Transaction(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات تراکنش قسط با موفقیت ثبت شد'
                        });
                        that.getData()
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.transaction.loading = false
                        that.getData()
                    })
            }
        }
    }
</script>

<style scoped>

</style>
