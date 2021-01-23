<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-small-size-100">
            <div class="md-layout-item md-size-100">

                <div class="md-layout">
                    <div  v-if="allocatedLoan.account" class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
                        <stats-card header-color="blue">
                            <template slot="header">
                                <div class="card-icon">
                                    <md-icon>perm_identity</md-icon>
                                </div>
                                <p class="category">نام و نام خانوادگی وام گیرنده</p>
                                <h3 class="title">
                                    {{ allocatedLoan.account.user.f_name+' '+allocatedLoan.account.user.l_name }}
                                </h3>
                            </template>
                            <template slot="footer">
                                <div class="stats">
                                    <md-button
                                        v-if="allocatedLoan.account"
                                        class="md-dense md-raised md-success"
                                        :to="'/user/'+allocatedLoan.account.user.id">
                                        مشاهده اطلاعات کاربر
                                    </md-button>
                                </div>
                            </template>
                        </stats-card>
                    </div>
                    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
                        <stats-card header-color="warning">
                            <template slot="header">
                                <div class="card-icon">
                                    <md-icon>monetization_on</md-icon>
                                </div>
                                <p class="category">اطلاعات وام</p>
                                <h3 class="title">
                                    {{ allocatedLoan.loan.name }}
                                </h3>
                            </template>

                            <template slot="footer">
                                <div class="stats">
                                    <md-button
                                        class="md-dense md-raised md-success"
                                        :to="'/loan/'+allocatedLoan.loan.id">
                                        مشاهده اطلاعات وام
                                    </md-button>
                                </div>
                            </template>
                        </stats-card>
                    </div>
                    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
                        <stats-card header-color="green">
                            <template slot="header">
                                <div class="card-icon">
                                    <md-icon>account_balance</md-icon>
                                </div>
                                <p class="category">اطلاعات صندوق</p>
                                <h3 class="title">
                                    {{ allocatedLoan.loan.fund.name }}
                                </h3>
                            </template>
                            <template slot="footer">
                                <div class="stats">
                                    <md-button
                                        class="md-dense md-raised md-success"
                                        :to="'/fund/'+allocatedLoan.loan.fund.id">
                                        مشاهده اطلاعات صندوق
                                    </md-button>
                                </div>
                            </template>
                        </stats-card>
                    </div>
                    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
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
                        </stats-card>
                    </div>
                </div>

                <md-card>
                    <md-card-header class="md-card-header-text" :class="{'md-card-header-warning': !allocatedLoan.is_settled, 'md-card-header-blue': allocatedLoan.is_settled}">
                        <div class="card-text">
                            <h4 class="title">اقساط</h4>
                            <p class="category">
                                <span v-if="allocatedLoan.is_settled">
                                    تسویه شده
                                </span>
                                <span v-else>
                                    تسویه نشده
                                </span>
                                <br>
                                کل مبلغ پرداختی:
                                {{ allocatedLoan.total_payments | currencyFormat }}
                                -
                                مبلغ قابل پرداخت باقیمانده:
                                {{ allocatedLoan.remaining_payable_amount | currencyFormat}}
                                <br>
                                تعداد
                                {{ allocatedLoan.count_of_paid_installments }}
                                قسط از
                                {{ allocatedLoan.number_of_installments }}
                                قسط پرداخت شده
                                و تعداد
                                {{ allocatedLoan.count_of_remaining_installments }}
                                قسط باقیمانده
                            </p>
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <md-table :value="allocatedLoan.installments.list"
                                  table-header-color="green"
                                  :md-sort.sync="sortation.field"
                                  :md-sort-order.sync="sortation.order"
                                  :md-sort-fn="customSort"
                                  class="table-hover">
                            <md-table-row slot="md-table-row"
                                          slot-scope="{ item }"
                                          :class="getInstallmentRowClass(item)">
                                <md-table-cell md-label="مبلغ قسط" md-sort-by="rate">{{ item.rate | currencyFormat }}</md-table-cell>
                                <md-table-cell md-label="کل پرداختی" md-sort-by="total_payments">{{ item.total_payments | currencyFormat }}</md-table-cell>
                                <md-table-cell md-label="مبلغ قابل پرداخت باقیمانده" md-sort-by="remaining_payable_amount">{{ item.remaining_payable_amount | currencyFormat }}</md-table-cell>
                                <md-table-cell md-label="وضعیت" md-sort-by="is_settled">
                                    <span v-if="item.is_settled">
                                        تسویه شده
                                    </span>
                                    <span v-else>
                                        تسویه نشده
                                    </span>
                                </md-table-cell>
                                <md-table-cell md-label="تاریخ" md-sort-by="created_at">{{ item.shamsiDate('created_at').date }}</md-table-cell>
                                <md-table-cell md-label="مشاهده">
                                    <md-button
                                        v-if="item.received_transactions.list.length > 0"
                                        @click="showInstallmentTransactions(item)"
                                        class="md-icon-button md-raised md-round md-info"
                                        style="margin: .2rem;"
                                    >
                                        <md-icon>edit</md-icon>
                                    </md-button>
                                    <md-button
                                        :to="{ name: 'AllocatedLoanInstallment.AddPayment', params: {'allocated_loan_id': allocatedLoan.id, 'allocated_loan_installment_id': item.id} }"
                                        class="md-icon-button md-raised md-round md-info"
                                        style="margin: .2rem;"
                                    >
                                        <md-icon>payment</md-icon>
                                    </md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                        <md-button
                            class="md-dense md-raised md-success"
                            :to="{ name: 'AllocatedLoanInstallment.Create', params: {'allocated_loan_id': allocatedLoan.id} }">
                            تعریف قسط جدید
                        </md-button>
                    </md-card-content>
                </md-card>

                <md-dialog :md-active.sync="installmentTransactionsShowDialog">
                    <md-dialog-title>پرداختی های قسط</md-dialog-title>

                    <md-dialog-content>
                        <div style="height: 300px;display: flex;flex-flow: column;justify-content: center;">
                            <md-card v-if="installment.id">
                                <md-card-header class="md-card-header-text" :class="{'md-card-header-warning': !installment.is_settled, 'md-card-header-blue': installment.is_settled}">
                                    <div class="card-text">
                                        <h4 class="title">تاریخ قسط: {{installment.shamsiDate('created_at').date}}</h4>
                                        <p class="category">
                                    <span v-if="installment.is_settled">
                                        تسویه شده
                                    </span>
                                            <span v-else>
                                        تسویه نشده
                                    </span>
                                            <br>
                                            کل پرداخت:
                                            {{installment.total_payments | currencyFormat}}
                                        </p>
                                    </div>
                                </md-card-header>
                                <md-card-content>
                                    <md-table v-model="installment.received_transactions.list" table-header-color="orange">
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
                                                    <md-icon>edit</md-icon>
                                                </md-button>
                                            </md-table-cell>
                                        </md-table-row>
                                    </md-table>
                                </md-card-content>
                            </md-card>
                        </div>
                    </md-dialog-content>

                    <md-dialog-actions>
                        <md-button class="md-default" @click="closeInstallmentTransactions">بستن</md-button>
                    </md-dialog-actions>
                </md-dialog>

            </div>
        </div>
    </div>
</template>

<script>
    import { StatsCard } from "@/components"
    import { AllocatedLoan } from '@/models/AllocatedLoan'
    import { AllocatedLoanInstallment } from "@/models/AllocatedLoanInstallment"
    import { priceFilterMixin, axiosMixin } from '@/mixins/Mixins'
    import {Account} from "@/models/Account";
    import {User} from "@/models/User";

    export default {
        watch: {
            'allocatedLoan.fund.id': function () {
                this.allocatedLoan.fund_id = this.allocatedLoan.fund.id
            }
        },
        components: { StatsCard },
        mixins: [priceFilterMixin, axiosMixin],
        data: () => ({
            allocatedLoan: new AllocatedLoan(),
            newAllocatedLoan: new AllocatedLoan(),
            sortation: {
                field: 'created_at',
                order: 'asc'
            },
            installment: new AllocatedLoanInstallment(),
            installmentTransactionsShowDialog: false
        }),
        created () {
            this.newAllocatedLoan.account = new Account()
            this.newAllocatedLoan.account_id = null
            this.newAllocatedLoan.account.user = new User()
            this.newAllocatedLoan.user_id = null
        },
        mounted() {
            this.getData()
        },
        methods: {
            isCreateForm () {
                return (this.$route.name === 'AllocatedLoan.Create')
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
            createAllocatedLoan () {
                let that = this
                this.allocatedLoan.loading = true
                delete this.allocatedLoan.created_at
                delete this.allocatedLoan.updated_at
                this.allocatedLoan.create()
                    .then((response) => {
                        that.allocatedLoan.loading = false;
                        that.allocatedLoan = new AllocatedLoan(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات صندوق با موفقیت ثبت شد'
                        });
                        that.$router.push({ path: '/allocated_loan/'+that.allocatedLoan.id })
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
            showUserAccounts () {
                let that = this
                this.newAllocatedLoan.account.user.loading = true;
                this.newAllocatedLoan.account.user.show(this.newAllocatedLoan.user_id)
                    .then((response) => {
                        that.newAllocatedLoan.account.user.loading = false
                        that.newAllocatedLoan.account.user = new User(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.newAllocatedLoan.account.user.loading = false;
                        that.newAllocatedLoan.account.user = new User()
                    })
            }
        }
    }
</script>
