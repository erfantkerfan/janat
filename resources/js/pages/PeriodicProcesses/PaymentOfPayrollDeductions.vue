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
                    <vue-confirm-dialog></vue-confirm-dialog>
                    <loading :active.sync="allocatedLoans.loading" :is-full-page="false"></loading>
                </md-card-content>
            </md-card>
        </div>
    </div>
</template>

<script>
    import {AllocatedLoanList} from '@/models/AllocatedLoan'
    import { priceFilterMixin, getFilterDropdownMixin, axiosMixin } from '@/mixins/Mixins'

    export default {
        name: 'PaymentOfPayrollDeductions',
        mixins: [getFilterDropdownMixin, priceFilterMixin, axiosMixin],
        data: () => ({
            allocatedLoans: new AllocatedLoanList(),
            paySinceDate: null,
            payTillDate: null
        }),
        mounted() {
            this.getList()
            this.getFunds()
            this.getLoans()
            this.allocatedLoans.loading = false
        },
        methods: {
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
                }
                return ''
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

