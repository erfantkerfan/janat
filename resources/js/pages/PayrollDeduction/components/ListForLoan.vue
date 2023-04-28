<template>
    <div>
        <md-table v-if="!transactions.loading && transactions.list.length > 0"
                  :value="transactions.list"
                  class="paginated-table table-hover"
        >
            <md-table-row v-if="!transactions.loading && transactions.list.length > 0"
                          slot="md-table-row"
                          :class="getInstallmentRowClass(item)"
                          slot-scope="{ item, index }">


                <md-table-cell md-label="ردیف">
                    {{ (index+1) }}
                </md-table-cell>
                <md-table-cell md-label="شماره عضویت" md-sort-by="account.user.id">
                    {{ item.related_payers[0].transaction_payers?.user?.id }}
                </md-table-cell>
                <md-table-cell md-label="کد پرسنلی" md-sort-by="account.user.staff_code">
                    {{ item.related_payers[0].transaction_payers?.user?.staff_code }}
                </md-table-cell>
                <md-table-cell md-label="نام" md-sort-by="account.user.f_name">
                    {{ item.related_payers[0].transaction_payers?.user?.f_name }}
                </md-table-cell>
                <md-table-cell md-label="نام خانوادگی" md-sort-by="account.user.l_name">
                    {{ item.related_payers[0].transaction_payers?.user?.l_name }}
                </md-table-cell>
                <md-table-cell :md-label="'مبلغ وام '+'('+currencyUnit+')'" md-sort-by="loan_amount">
                    {{ item.related_recipients[0].transaction_recipients.allocated_loan.loan_amount | currencyFormat }}
                </md-table-cell>
                <md-table-cell :md-label="'مبلغ کسر از حقوق '+'('+currencyUnit+')'" md-sort-by="payroll_deduction_amount">
                    {{ item.related_recipients[0].transaction_recipients.allocated_loan.payroll_deduction_amount | currencyFormat }}
                </md-table-cell>
                <md-table-cell :md-label="'مبلغ پرداخت شده'+'('+currencyUnit+')'" md-sort-by="cost">
                    {{item.cost | currencyFormat}}
                </md-table-cell>
                <md-table-cell :md-label="'مبلغ هر قسط '+'('+currencyUnit+')'" md-sort-by="installment_rate">
                    {{ item.related_recipients[0].transaction_recipients.allocated_loan.installment_rate | currencyFormat }}
                </md-table-cell>

                <md-table-cell md-label="تعداد اقساط" md-sort-by="number_of_installments">
                    {{ item.related_recipients[0].transaction_recipients.allocated_loan.number_of_installments }}
                </md-table-cell>
                <md-table-cell md-label="تعداد اقساط پرداخت نشده" md-sort-by="count_of_remaining_installments">
                    {{ item.related_recipients[0].transaction_recipients.allocated_loan.count_of_remaining_installments }}
                </md-table-cell>
                <md-table-cell :md-label="'مبلغ قابل پرداخت باقیمانده '+'('+currencyUnit+')'" md-sort-by="remaining_payable_amount">
                    {{ item.related_recipients[0].transaction_recipients.allocated_loan.remaining_payable_amount | currencyFormat }} {{ currencyUnit }}
                </md-table-cell>
                <md-table-cell md-label="نام صندوق" md-sort-by="loan.fund.name">
                    {{ item.related_payers[0].transaction_payers.fund.name }}
                </md-table-cell>
                <md-table-cell md-label="نام شرکت" md-sort-by="loan.fund.name">
                    {{ item.related_payers[0].transaction_payers.company.name }}
                </md-table-cell>
                <md-table-cell md-label="وضعیت تراکنش" md-sort-by="parent_transaction_id">
                    {{item.transaction_status.display_name}}
                </md-table-cell>
                <md-table-cell md-label="وضعیت وام">
                                <span v-if="item.related_recipients[0].transaction_recipients.allocated_loan.is_settled">
                                    تسویه شده
                                </span>
                    <span v-else>
                                    تسویه نشده
                                </span>
                </md-table-cell>
                <md-table-cell md-label="تاریخ پرداخت" md-sort-by="created_at">
                    {{item.shamsiDate('paid_at').dateTime}}
                </md-table-cell>
                <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                    {{item.shamsiDate('created_at').dateTime}}
                </md-table-cell>
                <md-table-cell md-label="عملیات">
                    <md-button
                        :to="'/transactions/'+item.id"
                        class="md-icon-button md-raised md-round md-info"
                        style="margin: .2rem;"
                    >
                        <md-icon>edit</md-icon>
                    </md-button>
                    <md-button class="md-icon-button md-raised md-round md-danger"
                               @click="confirmRemoveTransaction(item)"
                               style="margin: .2rem;">
                        <md-icon>delete</md-icon>
                    </md-button>
                </md-table-cell>
            </md-table-row>
        </md-table>
        <div v-else>
            کمی صبر کنید...
        </div>
    </div>
</template>

<script>
import {TransactionList} from "@/models/Transaction";
import {axiosMixin, priceFilterMixin} from "@/mixins/Mixins";

export default {
    name: "ListForLoan",
    mixins: [priceFilterMixin, axiosMixin],
    props: {
        transactions: {
            type: TransactionList,
            default: new TransactionList()
        }
    },
    methods: {
        getInstallmentRowClass(item) {
            if (item.related_recipients[0].transaction_recipients.allocated_loan.is_settled) {
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
        confirmRemoveTransaction(item) {
            this.$confirm(
                {
                    message: `از حذف تراکنش اطمینان دارید؟`,
                    button: {
                        no: 'خیر',
                        yes: 'بله'
                    },
                    callback: confirm => {
                        if (confirm) {
                            this.removeTransaction(item)
                        }
                    }
                }
            )
        },
        removeTransaction(item) {
            item.loading = true
            item.delete()
                .then(() => {
                    this.$store.dispatch('alerts/fire', {
                        icon: 'success',
                        title: 'توجه',
                        message: 'تراکنش با موفقیت حذف شد'
                    });
                    this.$emit('on-deleted')
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    item.editMode = false
                    item.loading = false
                });
        },
    }
}
</script>
