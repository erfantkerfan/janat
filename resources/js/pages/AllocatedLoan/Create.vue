<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-small-size-100">
            <div class="md-layout-item md-size-100">
                <div class="md-layout">
                    <div class="md-layout-item md-size-100">
                        <stats-card header-color="blue">
                            <template slot="header">
                                <div class="card-icon">
                                    <md-icon>perm_identity</md-icon>
                                </div>
                                <p class="category">انتخاب فرد وام گیرنده</p>
                                <h3 v-if="selectedUser.f_name !== null || selectedUser.l_name !== null" class="title">
                                    {{ selectedUser.f_name+' '+selectedUser.l_name }}
                                </h3>
                                <div class="md-layout">
                                    <div class="md-layout-item">
                                        <label class="md-layout-item md-size-15 md-form-label">
                                            کد کاربر:
                                        </label>
                                        <div class="md-layout-item">
                                            <md-field class="md-invalid">
                                                <md-input v-model="selectedUser.id" />
                                            </md-field>
                                        </div>
                                    </div>
                                </div>
                                <div class="md-layout">
                                    <div class="md-layout-item">
                                        <md-table v-if="!selectedUser.loading && selectedUser.id !== null"
                                                  v-model="selectedUser.accounts.list"
                                                  @md-selected="onSelectAccount">
                                            <md-table-toolbar>
                                                <h1 class="md-title">حساب های کاربر</h1>
                                            </md-table-toolbar>
                                            <md-table-row
                                                class="md-primary"
                                                slot="md-table-row"
                                                slot-scope="{ item }"
                                                md-selectable="single"
                                                md-auto-select
                                            >
                                                <md-table-cell  md-label="صندوق">
                                                    {{ item.fund.name }}
                                                </md-table-cell>
                                                <md-table-cell  md-label="شماره حساب">
                                                    {{ item.acc_number }}
                                                </md-table-cell>
                                                <md-table-cell  md-label="تعداد وام های دریافت شده">
                                                    {{ item.allocated_loans.list.length }}
                                                </md-table-cell>
                                            </md-table-row>
                                        </md-table>
                                        <loading :active.sync="selectedUser.loading" :is-full-page="false"></loading>
                                    </div>
                                </div>
                            </template>
                            <template slot="footer">
                                <div class="stats">
                                    <md-button class="md-dense md-raised md-success" @click="showUserAccounts">
                                        مشاهده حساب های کاربر
                                    </md-button>
                                </div>
                            </template>
                        </stats-card>
                    </div>
                    <div class="md-layout-item md-size-100">
                        <stats-card header-color="green">
                            <template slot="header">
                                <div class="card-icon">
                                    <md-icon>account_balance</md-icon>
                                </div>
                                <p class="category">انتخاب صندوق</p>
                                <h3 v-if="selectedFund !== null" class="title">
                                    {{ selectedFund.name }}
                                </h3>
                                <br>
                                <div class="md-layout">
                                    <div class="md-layout-item">
                                        <md-table v-if="!funds.loading"
                                                  v-model="funds.list"
                                                  @md-selected="onSelectFund">
                                            <md-table-row
                                                class="md-primary"
                                                slot="md-table-row"
                                                slot-scope="{ item }"
                                                md-selectable="single"
                                                md-auto-select
                                            >
                                                <md-table-cell  md-label="کد">
                                                    {{ item.id }}
                                                </md-table-cell>
                                                <md-table-cell  md-label="نام">
                                                    {{ item.name }}
                                                </md-table-cell>
                                                <md-table-cell  md-label="موجودی">
                                                    {{ item.balance | currencyFormat }}
                                                </md-table-cell>
                                            </md-table-row>
                                        </md-table>
                                        <list-pagination
                                            :paginate="funds.paginate"
                                            @changePage="getFunds"
                                        />
                                        <loading :active.sync="funds.loading || loans.loading" :is-full-page="false"></loading>
                                    </div>
                                </div>
                            </template>
                        </stats-card>
                    </div>
                    <div v-if="selectedFund !== null" class="md-layout-item md-size-100">
                        <stats-card header-color="warning">
                            <template slot="header">
                                <div class="card-icon">
                                    <md-icon>monetization_on</md-icon>
                                </div>
                                <p class="category">انتخاب وام</p>
                                <h3 v-if="selectedLoan !== null" class="title">
                                    {{ selectedLoan.name }}
                                </h3>
                                <br>
                                <div class="md-layout">
                                    <div class="md-layout-item">
                                        <md-table v-if="!loans.loading"
                                                  v-model="loans.list"
                                                  @md-selected="onSelectLoan">
                                            <md-table-row
                                                class="md-primary"
                                                slot="md-table-row"
                                                slot-scope="{ item }"
                                                md-selectable="single"
                                                md-auto-select
                                            >
                                                <md-table-cell md-label="نام وام">
                                                    {{ item.name }}
                                                </md-table-cell>
                                                <md-table-cell md-label="نوع وام">
                                                    {{ item.loan_type.display_name }}
                                                </md-table-cell>
                                                <md-table-cell md-label="مبلغ وام">
                                                    {{ item.loan_amount | currencyFormat }}
                                                </md-table-cell>
                                                <md-table-cell md-label="مبلغ هر قسط">
                                                    {{ item.installment_rate | currencyFormat }}
                                                </md-table-cell>
                                                <md-table-cell md-label="تعداد اقساط">
                                                    {{ item.number_of_installments }}
                                                </md-table-cell>
                                                <md-table-cell md-label="نرخ کارمزد">
                                                    {{ item.interest_rate }}%
                                                </md-table-cell>
                                                <md-table-cell md-label="مقدار کارمزد">
                                                    {{ item.interest_amount | currencyFormat }}
                                                </md-table-cell>
                                            </md-table-row>
                                        </md-table>
                                        <list-pagination
                                            :paginate="loans.paginate"
                                            @changePage="getLoans"
                                        />
                                        <loading :active.sync="loans.loading" :is-full-page="false"></loading>
                                    </div>
                                </div>
                            </template>
                        </stats-card>
                    </div>
                    <div v-if="selectedUser !== null && selectedAccount !== null && selectedLoan !== null" class="md-layout-item md-size-100">
                        <stats-card header-color="rose">
                            <template slot="header">
                                <div class="card-icon">
                                    <md-icon>monetization_on</md-icon>
                                </div>
                                <p class="category">اطلاعات نهایی وام جهت تخصیص</p>
                                کاربر:
                                {{ selectedUser.f_name }}
                                {{ selectedUser.l_name }}
                                <br>
                                حساب:
                                {{ selectedAccount.acc_number }}
                                <md-chip v-if="selectedAccount.fund.id !== selectedFund.id" class="md-primary">
                                    حساب کاربر متعلق به صندوق انتخاب شده نیست
                                </md-chip>
                                <br>
                                صندوق:
                                {{ selectedFund.name }}
                                <br>
                                وام:
                                {{ selectedLoan.name }}
                                <br>
                                <md-checkbox v-model="allocatedLoan.payroll_deduction">پرداخت اقساط به صورت کسر از حقوق</md-checkbox>
                                <br>
                                <md-button class="md-dense md-raised md-success" @click="createAllocatedLoan">
                                    ثبت
                                </md-button>
                            </template>
                        </stats-card>
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
    import { priceFilterMixin, axiosMixin } from '@/mixins/Mixins'
    import {User} from "@/models/User";
    import {FundList} from "@/models/Fund";
    import {LoanList} from "@/models/Loan";

    export default {
        name: 'Create',
        watch: {
            'allocatedLoan.fund.id': function () {
                this.allocatedLoan.fund_id = this.allocatedLoan.fund.id
            }
        },
        components: { StatsCard, ListPagination },
        mixins: [priceFilterMixin, axiosMixin],
        data: () => ({
            selectedUser: new User(),
            selectedFund: null,
            selectedAccount: null,
            funds: new FundList(),
            loans: new LoanList(),
            selectedLoan: null,
            allocatedLoan: new AllocatedLoan(),

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
            this.getFunds()
        },
        methods: {
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

            getData () {
                if (this.isCreateForm()) {
                    return false
                }
                this.allocatedLoan.loading = true;
                this.allocatedLoan.show(this.$route.params.id)
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
