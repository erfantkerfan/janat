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
                            ویرایش اطلاعات
                        </h4>
                    </md-card-header>

                    <md-card-content>

                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                نام صندوق
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="fund.name"/>
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                نام مسئول
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="fund.undertaker"/>
                                </md-field>
                            </div>
                        </div>
                        <price-input v-if="!isCreateForm()" v-model="fund.balance" :label="'موجودی صندوق'" :disabled="true" />
                        <div v-if="!isCreateForm()" class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تاریخ تعریف صندوق
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="fund.shamsiDate('created_at').date" :disabled="true"/>
                                </md-field>
                            </div>
                        </div>


                        <md-button v-if="!showFundIncomes && !fund.loading" type="submit" @click="getIncomesAndExpenses">
                            نمایش هزینه ها و دریافتی های صندوق
                        </md-button>

                        <price-input v-if="showFundIncomes && !fund.loading" v-model="fund.incomes.sum_of_users_pay_the_fund_tuition" :label="'مجموع ماهانه ها'" :disabled="true" />
                        <price-input v-if="showFundIncomes && !fund.loading" v-model="fund.incomes.sum_of_charge_fund" :label="'مجموع واریزی های خیرین'" :disabled="true" />
                        <price-input v-if="showFundIncomes && !fund.loading" v-model="fund.incomes.sum_of_installments_interest" :label="'مجموع کارمزد اقساط'" :disabled="true" />
                        <price-input v-if="showFundIncomes && !fund.loading" v-model="fund.incomes.sum_of_all" :label="'مجموع تمام دریافتی های صندوق'" :disabled="true" />
                        <price-input v-if="showFundIncomes && !fund.loading" v-model="fund.expenses" :label="'مجموع هزینه های صندوق'" :disabled="true" />

                        <price-input v-if="showFundIncomes && !fund.loading" v-model="sumOfIncomesAndExpenses" :label="'مجموع هزینه ها و درآمد های صندوق'" :disabled="true" />

                        <div class="md-layout">
                            <md-button v-if="!isCreateForm() && !fund.loading"
                                       class="md-layout-item md-size-100 md-success"
                                       :to="{
                                            name: 'Fund.AddPayment',
                                            params: {
                                                fund_id: fund.id
                                            }
                                        }"
                            >
                                پرداخت هزینه برای صندوق
                            </md-button>
                        </div>

                        <loading :active.sync="fund.loading" :is-full-page="false"></loading>

                    </md-card-content>

                    <md-card-actions>
                        <md-button type="submit" @click="updateFund">
                            ذخیره اطلاعات
                        </md-button>
                    </md-card-actions>

                </md-card>
            </div>
        </div>
        <div v-if="!isCreateForm()" class="md-layout-item md-size-100 md-small-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-blue">
                    <div class="card-icon">
                        <md-icon>monetization_on</md-icon>
                    </div>
                    <h4 class="title">
                        وام های تعریف شده صندوق
                    </h4>
                </md-card-header>

                <md-card-content>
                    <md-empty-state
                        v-if="!loan.loading && loans.list.length === 0"
                        class="md-warning"
                        md-icon="info"
                        md-label="وامی یافت نشد"
                    >
                    </md-empty-state>
                    <md-table
                        v-if="loans.list.length > 0"
                        :value="loans.list"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="نام" md-sort-by="name">{{item.name}}</md-table-cell>
                            <md-table-cell :md-label="'مبلغ وام '+'(' + currencyUnit + ')'" md-sort-by="email">{{item.loan_amount | currencyFormat}}</md-table-cell>
                            <md-table-cell :md-label="'مبلغ هر قسط '+'(' + currencyUnit + ')'" md-sort-by="email">{{item.installment_rate | currencyFormat}}</md-table-cell>
                            <md-table-cell md-label="تعداد اقساط" md-sort-by="email">{{item.number_of_installments}}</md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <md-button
                                    class="md-icon-button md-raised md-round md-info"
                                    style="margin: .2rem;"
                                    :to="'/loan/'+item.id"
                                >
                                    <md-icon>edit</md-icon>
                                </md-button>
                                <md-button
                                    class="md-icon-button md-raised md-round md-danger"
                                    style="margin: .2rem;"
                                    @click="confirmRemove(item)"
                                >
                                    <md-icon>delete</md-icon>
                                </md-button>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
                    <md-dialog :md-active.sync="createLoanShowDialog">
                        <md-dialog-title>ایجاد وام جدید</md-dialog-title>

                        <md-dialog-content>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    نام وام
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="loan.name"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <md-field>
                                    <label>نوع وام</label>
                                    <md-select v-model="loan.loan_type.id" name="pages">
                                        <md-option
                                            v-for="item in loanTypes.list"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.id"
                                        >
                                            {{ item.display_name }}
                                        </md-option>
                                    </md-select>
                                </md-field>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    مقدار وام
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="loan.loan_amount"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    مبلغ هر قسط
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="loan.installment_rate"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    تعداد اقساط
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="loan.number_of_installments"/>
                                    </md-field>
                                </div>
                            </div>
                        </md-dialog-content>

                        <md-dialog-actions>
                            <md-button class="md-default" @click="closeLoanDialog">انتصراف</md-button>
                            <md-button v-if="!editLoanState" class="md-success" @click="createNewLoan">ذخیره</md-button>
                            <md-button v-else class="md-success" @click="editLoan">ذخیره</md-button>
                        </md-dialog-actions>
                    </md-dialog>
                    <loading :active.sync="loans.loading" :is-full-page="false"></loading>
                    <vue-confirm-dialog></vue-confirm-dialog>
                </md-card-content>

                <md-card-actions>
                    <md-button
                        v-if="false"
                        class="md-dense md-raised md-primary"
                        @click="showDialog">
                        تعریف وام جدید
                    </md-button>
                </md-card-actions>

            </md-card>
        </div>
        <div v-if="!isCreateForm()" class="md-layout-item md-size-100 md-small-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-blue">
                    <div class="card-icon">
                        <md-icon>monetization_on</md-icon>
                    </div>
                    <h4 class="title">
                        هزینه های صندوق
                    </h4>
                </md-card-header>
                <md-card-content>
                    <md-empty-state
                        v-if="expenseTransactionsReceived && !expenseTransactions.loading && expenseTransactions.list.length === 0"
                        class="md-warning"
                        md-icon="info"
                        md-label="هزینه ای یافت نشد"
                    >
                    </md-empty-state>
                    <md-table
                        v-if="expenseTransactions.list.length > 0"
                        :value="expenseTransactions.list"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell :md-label="'مبلغ'+'('+currencyUnit+')'" md-sort-by="cost">
                                {{item.cost | currencyFormat}}
                            </md-table-cell>
                            <md-table-cell md-label="وضعیت" md-sort-by="parent_transaction_id">
                                {{item.transaction_status.display_name}}
                            </md-table-cell>
                            <md-table-cell md-label="توضیحات مدیر" md-sort-by="manager_comment">
                                {{item.manager_comment}}
                            </md-table-cell>
                            <md-table-cell md-label="تاریخ پرداخت" md-sort-by="created_at">
                                {{item.shamsiDate('paid_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="مشاهده">
                                <md-button
                                    class="md-icon-button md-raised md-round md-info"
                                    style="margin: .2rem;"
                                    :to="'/transactions/'+item.id"
                                >
                                    <md-icon>pageview</md-icon>
                                </md-button>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
                    <list-pagination
                        :paginate="expenseTransactions.paginate"
                        @changePage="getExpenseTransactions"
                    />
                    <loading :active.sync="expenseTransactions.loading" :is-full-page="false"></loading>
                </md-card-content>
                <md-card-actions>
                    <md-button
                        class="md-dense md-raised md-primary"
                        @click="getExpenseTransactions">
                        مشاهده هزینه های صندوق
                    </md-button>
                </md-card-actions>
            </md-card>
        </div>
    </div>
</template>

<script>
    import {Fund} from '@/models/Fund'
    import {Loan, LoanList} from '@/models/Loan'
    import PriceInput from '@/components/PriceInput'
    import ListPagination from '@/components/ListPagination'
    import { priceFilterMixin, getFilterDropdownMixin, axiosMixin } from '@/mixins/Mixins'
    import {TransactionList} from "@/models/Transaction";

    export default {
        name: "fund-form",
        components: {PriceInput, ListPagination},
        mixins: [priceFilterMixin, getFilterDropdownMixin, axiosMixin],
        data: () => ({
            fund: new Fund(),
            loan: new Loan(),
            loans: new LoanList(),
            expenseTransactions: new TransactionList(),
            showFundIncomes: false,
            expenseTransactionsReceived: false,
            sumOfIncomesAndExpenses: 0,
            editLoanState: false,
            createLoanShowDialog: false,
        }),
        mounted() {
            this.getData()
            this.getLoans()
            this.getLoanTypes()
        },
        methods: {
            isCreateForm () {
                return (this.$route.name === 'Fund.Create')
            },
            showDialog (item) {
                this.loan = new Loan(item)
                this.editLoanState = false
                this.createLoanShowDialog = true
            },
            closeLoanDialog () {
                this.createLoanShowDialog = false
            },
            createNewLoan () {
                let that = this
                this.loan.loading = true
                this.loan.fund.id = this.$route.params.id
                this.loan.create()
                    .then((response) => {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'وام جدید با موفقیت تعریف شد'
                        });
                        that.closeLoanDialog()
                        that.getLoans()
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.closeLoanDialog()
                    })
            },
            editLoan () {
                let that = this
                this.value.loading = true
                this.updateUserModel()
                this.newAccount.user_id = this.$route.params.id
                this.newAccount.update()
                    .then((response) => {
                        that.$emit('update', this.value)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'حساب با موفقیت ویرایش شد'
                        });
                        that.closeAccountDialog()
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.closeAccountDialog()
                    })
            },
            confirmRemove(item) {
                let that = this
                this.$confirm(
                    {
                        message: `از حذف وام اطمینان دارید؟`,
                        button: {
                            no: 'خیر',
                            yes: 'بله'
                        },
                        callback: confirm => {
                            if (confirm) {
                                that.remove(item)
                            }
                        }
                    }
                )
            },
            remove(item) {
                item.loading = true;
                let that = this;
                item.delete()
                    .then(function(response) {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'وام با موفقیت حذف شد'
                        })
                        that.getLoans()
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        item.editMode = false;
                        item.loading = false;
                    });
            },
            getData () {
                if (this.isCreateForm()) {
                    return false
                }
                this.fund.loading = true;
                this.fund.show(this.$route.params.id)
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
            getLoans () {
                let that = this
                this.loans.loading = true;
                this.loans.fetch({
                    fund_id: this.$route.params.id
                })
                    .then((response) => {
                        that.loans.loading = false;
                        that.loans = new LoanList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.loans.loading = false;
                        that.loans = new LoanList()
                    })
            },
            updateFund () {
                if (this.isCreateForm()) {
                    this.createFund()
                    return
                }
                let that = this
                this.fund.loading = true;
                this.fund.update()
                    .then((response) => {
                        that.fund.loading = false;
                        that.fund = new Fund(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات با موفقیت ویرایش شد'
                        });
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.fund.loading = false;
                        that.fund = new Fund()
                    })
            },
            getExpenseTransactions(page) {
                if (!page) {
                    page = 1
                }
                this.expenseTransactions.loading = true
                this.fund.getExpenseTransactions({page})
                    .then(response => {
                        this.expenseTransactions = new TransactionList(response.data.data, response.data)
                        this.expenseTransactions.loading = false
                        this.expenseTransactionsReceived = true
                    })
                    .catch(error => {
                        this.axios_handleError(error)
                        this.expenseTransactions.loading = false
                        this.expenseTransactionsReceived = false
                    })
            },
            getIncomesAndExpenses () {
                let that = this
                this.fund.loading = true;
                this.fund.getIncomesAndExpenses()
                    .then((response) => {

                        that.fund.loading = false;
                        that.showFundIncomes = true;
                        that.fund.incomes = response.data.incomes
                        that.fund.expenses = response.data.expenses
                        that.sumOfIncomesAndExpenses = (that.fund.incomes.sum_of_all-that.fund.expenses)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات دریافتی صندوق محاسبه شد'
                        });
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.fund.loading = false;
                        that.showFundIncomes = false;
                    })
            },
            createFund () {
                let that = this
                this.fund.loading = true
                delete this.fund.created_at
                delete this.fund.updated_at
                this.fund.create()
                    .then((response) => {
                        that.fund.loading = false;
                        that.fund = new Fund(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات صندوق با موفقیت ثبت شد'
                        });
                        that.$router.push({ path: '/fund/'+that.fund.id })
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.fund.loading = false;
                        that.fund = new Fund()
                    })
            }
        }
    }
</script>
