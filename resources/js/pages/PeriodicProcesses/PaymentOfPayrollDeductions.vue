<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>how_to_vote</md-icon>
                    </div>
                    <h4 class="title">پرداخت کسر از حقوق ها</h4>
                </md-card-header>
                <md-card-content>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                پرداخت اقساط کسر از حقوق تسویه نشده ای که در بازه زمانی انتخاب شده زیر پرداختی نداشته اند
                            </div>
                        </div>
                    </div>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    از
                                </label>
                                <div class="md-layout-item">
                                    <date-picker
                                        v-model="paySinceDate"
                                        type="datetime"
                                        :editable="true"
                                        format="YYYY-MM-DD HH:mm:ss"
                                        display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    تا
                                </label>
                                <div class="md-layout-item">
                                    <date-picker
                                        v-model="payTillDate"
                                        type="datetime"
                                        :editable="true"
                                        format="YYYY-MM-DD HH:mm:ss"
                                        display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <md-button class="md-dense md-raised md-info" @click="pay">پرداخت</md-button>
                            </div>
                        </div>
                    </div>
                    <vue-confirm-dialog></vue-confirm-dialog>
                    <loading :active.sync="allocatedLoans.loading" :is-full-page="false"></loading>

                    <md-empty-state
                        v-if="payRequestIsSent && !allocatedLoans.loading && allocatedLoans.list.length === 0"
                        class="md-warning"
                        md-icon="cancel_presentation"
                        md-label="در بازه انتخاب شده هیچ وام کسر از حقوقی وجود ندارد که پرداختی قسط نداشته باشد."
                    >
                    </md-empty-state>
                    <md-field>
                        <Json-excel
                            v-if="!allocatedLoans.loading && allocatedLoans.list.length > 0"
                            :data="allocatedLoans.list"
                            :fields="json_fields"
                        >
                            <md-button class="md-dense md-icon-button md-raised md-info">
                                <md-icon>calendar_view_month</md-icon>
                            </md-button>
                        </Json-excel>
                    </md-field>
                    <md-table
                        :value="allocatedLoans.list"
                        class="table-hover"
                    >
                        <md-table-row slot="md-table-row"
                                      slot-scope="{ item }"
                                      :class="getInstallmentRowClass(item)"
                        >
                            <md-table-cell md-label="نام" md-sort-by="account.user.f_name">
                                {{item.account.user.f_name}}
                            </md-table-cell>
                            <md-table-cell md-label="نام خانوادگی" md-sort-by="account.user.l_name">
                                {{item.account.user.l_name}}
                            </md-table-cell>
                            <md-table-cell md-label="شماره عضویت" md-sort-by="account.user.l_name">
                                {{item.account.user.id}}
                            </md-table-cell>
                            <md-table-cell md-label="مبلغ وام" md-sort-by="loan_amount">
                                {{item.loan_amount | currencyFormat}}
                            </md-table-cell>
                            <md-table-cell md-label="مبلغ هر قسط" md-sort-by="installment_rate">
                                {{item.installment_rate | currencyFormat}}
                            </md-table-cell>
                            <md-table-cell md-label="تعداد اقساط" md-sort-by="number_of_installments">
                                {{item.number_of_installments}}
                            </md-table-cell>
                            <md-table-cell md-label="نام صندوق" md-sort-by="loan.fund.name">
                                {{item.loan.fund.name}}
                            </md-table-cell>
                            <md-table-cell md-label="وضعیت">
                                <span v-if="item.is_settled">
                                    تسویه شده
                                </span>
                                <span v-else>
                                    تسویه نشده
                                </span>
                            </md-table-cell>
                            <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                                {{item.shamsiDate('created_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <md-button
                                    @click="openLinkInNewTab(item.id)"
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
    </div>
</template>

<script>
    import JsonExcel from 'vue-json-excel'
    import {AllocatedLoanList} from '@/models/AllocatedLoan'
    import moment from 'moment-jalaali'
    import { priceFilterMixin, getFilterDropdownMixin, axiosMixin, dateMixin } from '@/mixins/Mixins'

    export default {
        name: 'PaymentOfPayrollDeductions',
        mixins: [getFilterDropdownMixin, priceFilterMixin, axiosMixin, dateMixin],
        computed: {
            json_fields () {
                return {
                    'نام': 'account.user.f_name',
                    'نام خانوادگی': 'account.user.l_name',
                    'شماره عضویت': 'account.user.id',
                    'مبلغ وام': 'loan_amount',
                    'مبلغ هر قسط': 'installment_rate',
                    'تعداد اقساط': 'number_of_installments',
                    'نام صندوق': 'loan.fund.name',
                    'وضعیت': {
                        field: 'is_settled',
                        callback: (value) => {
                            if (value) {
                                return 'تسویه شده'
                            } else {
                                return 'تسویه نشده'
                            }
                        },
                    },
                    'تاریخ ایجاد': {
                        field: 'created_at',
                        callback: (value) => {
                            return this.shamsiDate(value).dateTime
                        }
                    }
                }
            }
        },
        components: {
            JsonExcel
        },
        data: () => ({
            payRequestIsSent: false,
            allocatedLoans: new AllocatedLoanList(),
            paySinceDate: null,
            payTillDate: null
        }),
        mounted() {
            this.loadDatePicker()
            // this.getList()
            // this.getFunds()
            // this.getLoans()
            this.allocatedLoans.loading = false
        },
        methods: {
            loadDatePicker () {
                this.paySinceDate = moment().startOf('jMonth').format('YYYY-MM-DD HH:mm:ss')
                this.payTillDate = moment().endOf('jMonth').format('YYYY-MM-DD HH:mm:ss')
            },
            pay () {
                this.allocatedLoans.loading = true
                axios.get('/api/allocated_loans/pay_periodic_payroll_deduction', {
                    params: {
                        pay_since_date: this.paySinceDate,
                        pay_till_date: this.payTillDate
                    }
                })
                .then( (response) => {
                    this.allocatedLoans.loading = false
                    this.payRequestIsSent = true
                    this.allocatedLoans = new AllocatedLoanList(response.data)
                    if(this.allocatedLoans.list.length > 0) {
                        this.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'برای اقساط کسر از حقوق تسویه نشده ای که در بازه زمانی انتخاب شده پرداختی نداشته اند به صورت کسر از حقوق برای آنها قسط و تراکنش ثبت شد و لیست وام آنها برای شما نمایش داده می شود.'
                        });
                    }
                })
                .catch( (error) => {
                    this.axios_handleError(error)
                    this.allocatedLoans.loading = false
                })
            },
            openLinkInNewTab (allocatedLoanId) {
                window.open('/dashboard#/allocated_loan/' + allocatedLoanId, '_blank');
            },
            loadDatePickers () {
                this.paySinceDate = moment().format('YYYY-MM-DD HH:mm:ss')
                this.payTillDate = moment().format('YYYY-MM-DD HH:mm:ss')
            },
            clickCallback (data) {
                this.getList(data)
            },
            getList (page) {
                if (!page) {
                    page = 1
                }
                this.allocatedLoans.loading = true;
                this.allocatedLoans.fetch({
                    page,
                    sortation_field: this.filterData.sortation.field,
                    sortation_order: this.filterData.sortation.order,
                    length: this.filterData.perPage,
                    fund_id: (this.filterData.fund_id === null || this.filterData.fund_id === 0) ? null : this.filterData.fund_id,
                    loan_id: (this.filterData.loan_id === null || this.filterData.loan_id === 0) ? null : this.filterData.loan_id,
                    settled: (this.filterData.settled === false) ? null : this.filterData.settled,
                    notSettled: (this.filterData.notSettled === false) ? null : this.filterData.notSettled,
                    payroll_deduction: (this.filterData.payroll_deduction === false) ? null : 1,
                    loan_amount: this.filterData.loan_amount,
                    f_name: this.filterData.f_name,
                    l_name: this.filterData.l_name,
                    SSN: this.filterData.SSN,
                    installment_rate: this.filterData.installment_rate,
                    number_of_installments: this.filterData.number_of_installments,
                    last_paid_at_before: this.filterData.lastPaidAtBefore,
                    last_paid_at_after: this.filterData.lastPaidAtAfter,
                    createdSinceDate: this.filterData.createdSinceDate,
                    createdTillDate: this.filterData.createdTillDate
                })
                    .then((response) => {
                        this.allocatedLoans.loading = false
                        this.allocatedLoans = new AllocatedLoanList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.allocatedLoans.loading = false
                        this.allocatedLoans = new AllocatedLoanList()
                    })
            },
            confirmRemove(item) {
                this.$confirm(
                    {
                        message: `از حذف وام تخصیص داده شده اطمینان دارید؟`,
                        button: {
                            no: 'خیر',
                            yes: 'بله'
                        },
                        callback: confirm => {
                            if (confirm) {
                                this.remove(item)
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
                            message: 'صندوق با موفقیت حذف شد'
                        });
                        that.getList()
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        item.editMode = false
                        item.loading = false
                    });
            },
            customSort() {
                this.getList()
                return false;
            },
            getInstallmentRowClass (item) {
                if (item.is_settled) {
                    return 'table-success'
                } else {
                    return 'table-danger'
                }
                // {
                //     "table-success": id === 1,
                //     "table-info": id === 3,
                //     "table-danger": id === 5,
                //     "table-warning": id === 7
                // }
            },
        }

    }

</script>

