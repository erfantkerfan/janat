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
                                    <br>
                                    (کد ملی: {{ selectedUser.SSN }})
                                </h3>
                                <div class="md-layout">
                                    <div class="md-layout-item">
                                        <label class="md-layout-item md-size-15 md-form-label">
                                            کد عضویت:
                                        </label>
                                        <div class="md-layout-item">
                                            <md-field class="md-invalid">
                                                <md-input v-model="targetUserId" />
                                            </md-field>
                                        </div>
                                    </div>
                                </div>
                                <div class="md-layout">
                                    <div class="md-layout-item">
                                        <md-empty-state v-if="!selectedUser.loading && selectedUser.id !== null && selectedUser.accounts.list.length === 0"
                                            class="md-warning"
                                            md-icon="info"
                                            md-label="حسابی برای کاربر انتخاب شده یافت نشد"
                                        >
                                        </md-empty-state>
                                        <md-table v-if="!selectedUser.loading && selectedUser.id !== null && !hasDefaultAccount"
                                                  v-model="selectedUser.accounts.list"
                                                  @md-selected="onSelectAccount"
                                        >
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
                                                    {{ item.id }}
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
                                {{ selectedAccount.id }}
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
                                                توضیحات مدیر
                                            </label>
                                            <div class="md-layout-item">
                                                <md-field class="md-invalid">
                                                    <md-textarea v-model="managerComment" />
                                                </md-field>
                                            </div>
                                        </div>
                                        <div class="md-layout">
                                            <label class="md-layout-item md-size-15 md-form-label">
                                                تاریخ پرداخت
                                            </label>
                                            <div class="md-layout-item">
                                                <date-picker
                                                    v-model="paidAt"
                                                    type="datetime"
                                                    :editable="true"
                                                    format="YYYY-MM-DD HH:mm:ss"
                                                    display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                                            </div>
                                        </div>
                                    </md-card-content>
                                </md-card>
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
            paidAt: null,
            managerComment: null,
            hasDefaultAccount: false,
            targetUserId: null,
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
        mounted() {
            this.getData()
            console.log('gg', this.$route.params.account_id)
        },
        methods: {
            showUserAccounts () {
                let that = this
                this.selectedUser.loading = true
                this.selectedUser.show(this.targetUserId)
                    .then((response) => {
                        that.selectedUser.loading = false
                        that.selectedUser = new User(response.data)
                        that.selectDefaultAccount()
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
                    paid_at: this.paidAt,
                    manager_comment: this.managerComment,
                    payroll_deduction: this.allocatedLoan.payroll_deduction,
                    payroll_deduction: this.allocatedLoan.payroll_deduction,
                    payroll_deduction: this.allocatedLoan.payroll_deduction,
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
                        that.axios_handleError(error)
                        that.allocatedLoan.loading = false;
                    })
            },

            getData () {
                this.getFunds()
                this.targetUserId = this.$route.params.user_id
                this.showUserAccounts()
            },
            selectDefaultAccount () {
                const defaultAccount = this.selectedUser.accounts.list.find( item => parseInt(item.id) === parseInt(this.$route.params.account_id))
                if (defaultAccount) {
                    this.onSelectAccount(defaultAccount)
                    this.hasDefaultAccount = true
                } else {
                    this.hasDefaultAccount = false
                }
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
                        that.axios_handleError(error)
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
