<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-100 md-small-size-100">
            <div class="md-layout-item md-size-100">
                <md-card>
                    <md-card-header class="md-card-header-icon md-card-header-green">
                        <div class="card-icon">
                            <md-icon>account_balance</md-icon>
                        </div>
                        <h4 class="title">
                            مشاهده اطلاعات
                        </h4>
                    </md-card-header>

                    <md-card-content>

                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                شناسه
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="payrollDeduction.id" disabled=""/>
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                نوع پرداخت
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    {{ getTypeOfPayrollDeduction(payrollDeduction) }}
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                زمان شروع بازه
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="payrollDeduction.shamsiDate('from').dateTime" disabled=""/>
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                زمان پایان بازه
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="payrollDeduction.shamsiDate('to').dateTime" disabled=""/>
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تاریخ پرداخت
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="payrollDeduction.shamsiDate('paid_at').dateTime" disabled=""/>
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تاریخ ایجاد
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="payrollDeduction.shamsiDate('created_at').dateTime" disabled=""/>
                                </md-field>
                            </div>
                        </div>

                        <loading :active.sync="payrollDeduction.loading" :is-full-page="false"></loading>

                    </md-card-content>

                    <md-card-actions>
                        <md-button type="submit" @click="confirmRemove">
                            حذف
                        </md-button>
                    </md-card-actions>

                </md-card>
            </div>
        </div>
        <div class="md-layout-item md-size-100 md-small-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-blue">
                    <div class="card-icon">
                        <md-icon>monetization_on</md-icon>
                    </div>
                    <h4 class="title">
                        تراکنش های فرایند دوره ای
                    </h4>
                </md-card-header>

                <md-card-content>
                    <md-empty-state
                        v-if="!transactions.loading && transactions.list.length === 0"
                        class="md-warning"
                        md-icon="info"
                        md-label="موردی یافت نشد"
                    >
                    </md-empty-state>

                    <list-for-loan v-if="payrollDeduction.paid_for_loan" :transactions="transactions" @on-deleted="getTransactions" />
                    <list-for-monthly-payment v-if="payrollDeduction.paid_for_monthly_payment" :transactions="transactions" @onDeleted="getTransactions" />

                    <loading :active.sync="transactions.loading" :is-full-page="false"></loading>
                    <vue-confirm-dialog></vue-confirm-dialog>
                </md-card-content>

                <md-card-actions>
                    <list-pagination
                        :paginate="transactions.paginate"
                        @changePage="getTransactions"
                    />
                </md-card-actions>
                <md-card-actions>
                    <div style="width: 100%">
                        <div v-if="sumLoading">
                            کمی صبر کنید...
                        </div>
                        <div v-else>
                            جمع کل تراکنش ها:
                            {{ sumOfTransactions | currencyFormat }}
                            {{ currencyUnit }}
                        </div>
                    </div>
                </md-card-actions>

            </md-card>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import {TransactionList} from '@/models/Transaction'
import ListPagination from '@/components/ListPagination'
import { PayrollDeduction } from '@/models/PayrollDeduction'
import { priceFilterMixin, axiosMixin } from '@/mixins/Mixins'
import ListForLoan from '@/pages/PayrollDeduction/components/ListForLoan'
import ListForMonthlyPayment from '@/pages/PayrollDeduction/components/ListForMonthlyPayment'

export default {
    name: "PayrollDeductionForm",
    components: {ListForMonthlyPayment, ListForLoan, ListPagination},
    mixins: [priceFilterMixin, axiosMixin],
    data: () => ({
        sumLoading: false,
        sumOfTransactions: 0,
        transactions: new TransactionList(),
        payrollDeduction: new PayrollDeduction()
    }),
    mounted() {
        this.getData()
        this.getTransactions()
        this.getSumOfTransactions()
    },
    methods: {
        getTypeOfPayrollDeduction (payrollDeduction) {
            if (payrollDeduction.paid_for_loan) {
                return 'پرداخت قسط'
            }
            if (payrollDeduction.paid_for_monthly_payment) {
                return 'پرداخت ماهانه'
            }

            return '-'
        },
        getData () {
            this.payrollDeduction.loading = true;
            this.payrollDeduction.show(this.$route.params.id)
                .then((response) => {
                    this.payrollDeduction.loading = false;
                    this.payrollDeduction = new PayrollDeduction(response.data)
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    this.payrollDeduction.loading = false;
                    this.payrollDeduction = new PayrollDeduction()
                })
        },
        getTransactions (page = 1) {
            this.transactions.loading = true;
            axios.get('/api/payroll_deduction/' + this.$route.params.id + '/transactions',
                {params: {page}}
            )
                .then((response) => {
                    this.transactions.loading = false;
                    this.transactions = new TransactionList(response.data.data, response.data)
                })
                .catch((error) => {
                    that.axios_handleError(error)
                    this.transactions.loading = false;
                    this.transactions = new TransactionList()
                })
        },
        getSumOfTransactions () {
            this.sumLoading = true
            axios.get('/api/payroll_deduction/' + this.$route.params.id + '/report/sum_of_transactions')
                .then((response) => {
                    this.sumOfTransactions = response.data
                    this.sumLoading = false
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    this.sumLoading = false
                })
        },


        confirmRemove() {
            this.$confirm(
                {
                    message: `از حذف این فرایند دوره ای اطمینان دارید؟`,
                    button: {
                        no: 'خیر',
                        yes: 'بله'
                    },
                    callback: confirm => {
                        if (confirm) {
                            this.removePayrollDeduction()
                        }
                    }
                }
            )
        },
        removePayrollDeduction () {
            this.payrollDeduction.loading = true;
            this.payrollDeduction.delete()
                .then(() => {
                    this.$store.dispatch('alerts/fire', {
                        icon: 'success',
                        title: 'توجه',
                        message: 'پرداخت دوره ای با موفقیت حذف شد'
                    });
                    this.$router.push({ name: 'PayrollDeduction.List'})
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    this.payrollDeduction.loading = false;
                    this.payrollDeduction = new PayrollDeduction()
                })
        },

    }
}
</script>
