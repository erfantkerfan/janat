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
                                {{ allocatedLoan.loan_amount }}
                                <br>
                                مبلغ هر قسط وام:
                                {{ allocatedLoan.installment_rate }}
                                <br>
                                تعداد اقساط:
                                {{ allocatedLoan.number_of_installments }}
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
                                        @click="createAllocatedLoanInstallment"
                                    >
                                        ثبت قسط و تراکنش پرداخت
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
    import {User} from "@/models/User";
    import {FundList} from "@/models/Fund";
    import {LoanList} from "@/models/Loan";
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
            transaction: new Transaction(),

            selectedFund: null,
            selectedAccount: null,
            funds: new FundList(),
            loans: new LoanList(),
            selectedLoan: null,

            sortation: {
                field: 'created_at',
                order: 'asc'
            },
            installment: new AllocatedLoanInstallment(),
            installmentTransactionsShowDialog: false
        }),
        created () {
        },
        mounted() {
            this.getAllocatedLoan()
            this.getTransactionStatus(false)
        },
        methods: {
            getAllocatedLoan () {
                this.allocatedLoan.loading = true;
                this.allocatedLoan.show(this.$route.params.allocated_loan_id)
                    .then((response) => {
                        this.allocatedLoan.loading = false;
                        this.allocatedLoan = new AllocatedLoan(response.data)
                        this.loadTransactionFromAllocatedLoanData()
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.allocatedLoan.loading = false;
                        this.allocatedLoan = new AllocatedLoan()
                    })
            },
            loadTransactionFromAllocatedLoanData () {
                this.transaction.cost = this.allocatedLoan.installment_rate
                this.transaction.paid_at = new Date()
            },
            createAllocatedLoanInstallment () {
                this.allocatedLoanInstallment.loading = true;
                let that = this
                this.allocatedLoanInstallment.create({
                    allocated_loan_id: this.allocatedLoan.id
                })
                    .then((response) => {
                        that.allocatedLoanInstallment.loading = false;
                        that.allocatedLoanInstallment = new AllocatedLoanInstallment(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'قسط با موفقیت ثبت شد'
                        });
                        that.createTransaction()
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.allocatedLoanInstallment.loading = false;
                        that.allocatedLoanInstallment = new AllocatedLoan()
                    })

            },
            createTransaction () {
                this.transaction.loading = true;
                this.transaction.transaction_type = 'user_pay_installment'
                this.transaction.allocated_loan_installment_id = this.allocatedLoanInstallment.id
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
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.transaction.loading = false;
                        that.transaction = new Transaction()
                    })
            },

            showUserAccounts () {
                let that = this
                this.selectedUser.loading = true;
                this.selectedUser.show(this.selectedUser.id)
                    .then((response) => {
                        that.selectedUser.loading = false
                        that.selectedUser = new User(response.data)
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.selectedUser.loading = false;
                        that.selectedUser = new User()
                    })
            },
            onSelectAccount (item) {
                this.selectedAccount = item
            },
            onSelectFund (item) {
                this.selectedFund = item
                this.getLoans()
            },
            onSelectLoan (item) {
                this.selectedLoan = item
            },
            getLoans (page) {
                if (!page) {
                    page = 1
                }
                let that = this
                this.loans.loading = true
                this.loans.fetch({page, fund_id: this.selectedFund.id})
                    .then((response) => {
                        that.loans.loading = false
                        that.loans = new LoanList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.loans.loading = false
                        that.loans = new LoanList()
                    })
            },
            getFunds (page) {
                if (!page) {
                    page = 1
                }
                let that = this
                this.funds.loading = true;
                this.funds.fetch({page})
                    .then((response) => {
                        that.funds.loading = false;
                        that.funds = new FundList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.funds.loading = false;
                        that.funds = new FundList()
                    })
            },
            createAllocatedLoan () {
                let that = this
                this.allocatedLoan.loading = true
                this.allocatedLoan.create({
                    account_id: this.selectedAccount.id,
                    loan_id: this.selectedLoan.id,
                    payroll_deduction: this.allocatedLoan.payroll_deduction
                })
                    .then((response) => {
                        that.allocatedLoan.loading = false;
                        let newAllocatedLoan = new AllocatedLoan(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات وام تخصیص داده شده با موفقیت ثبت شد'
                        });
                        that.$router.push({ path: '/allocated_loan/'+newAllocatedLoan.id })
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.allocatedLoan.loading = false;
                        that.allocatedLoan = new AllocatedLoan()
                    })
            },


            updateAllocatedLoan () {
                if (this.isCreateForm()) {
                    this.createAllocatedLoan()
                    return
                }
                let that = this
                this.allocatedLoan.loading = true;
                this.allocatedLoan.update()
                    .then((response) => {
                        that.allocatedLoan.loading = false;
                        that.allocatedLoan = new AllocatedLoan(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات با موفقیت ویرایش شد'
                        });
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.allocatedLoan.loading = false;
                        that.allocatedLoan = new AllocatedLoan()
                    })
            },
            getInstallmentRowClass (item) {
                if (!item.is_settled && parseInt(item.remaining_payable_amount) === parseInt(item.rate)) {
                    return 'table-danger'
                } else if (!item.is_settled) {
                    return 'table-warning'
                }
                return '';
                // {
                //     "table-success": id === 1,
                //     "table-info": id === 3,
                //     "table-danger": id === 5,
                //     "table-warning": id === 7
                // }
            },
            customSort (value) {
                return value.sort((a, b) => {
                    const sortBy = this.sortation.field

                    if (this.sortation.order === 'desc') {
                        return a[sortBy].toString().localeCompare(b[sortBy])
                    }

                    return b[sortBy].toString().localeCompare(a[sortBy])
                })
            },
            showInstallmentTransactions (installment) {
                this.installment = installment
                this.installmentTransactionsShowDialog = true
            },
            closeInstallmentTransactions () {
                this.installment = new AllocatedLoanInstallment()
                this.installmentTransactionsShowDialog = false
            },

        }
    }
</script>

<style scoped>

</style>
