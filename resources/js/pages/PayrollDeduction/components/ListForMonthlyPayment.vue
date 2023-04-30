<template>
    <div>
        <md-table v-if="!transactions.loading && transactions.list.length > 0"
            :value="transactions.list"
            class="paginated-table table-striped table-hover"
        >
            <md-table-row v-if="!transactions.loading && transactions.list.length > 0"
                          slot="md-table-row"
                          slot-scope="{ item, index }">
                <md-table-cell md-label="ردیف">
                    {{ (index+1) }}
                </md-table-cell>
                <md-table-cell md-label="کد عضویت" md-sort-by="account.user.id">
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

                <md-table-cell md-label="شماره حساب" md-sort-by="id">
                    {{item.related_payers[0].transaction_payers.id}}
                </md-table-cell>
                <md-table-cell :md-label="'موجودی '+'('+currencyUnit+')'" md-sort-by="balance">
                    {{ item.related_payers[0].transaction_payers.balance | currencyFormat }}
                </md-table-cell>
                <md-table-cell :md-label="'ماهانه '+'('+currencyUnit+')'" md-sort-by="monthly_payment">
                    {{ item.related_payers[0].transaction_payers.monthly_payment | currencyFormat }}
                </md-table-cell>
                <md-table-cell md-label="نام صندوق" md-sort-by="loan.fund.name">
                    {{ item.related_payers[0].transaction_payers.fund.name }}
                </md-table-cell>
                <md-table-cell md-label="نام شرکت" md-sort-by="loan.fund.name">
                    {{ item.related_payers[0].transaction_payers.company.name }}
                </md-table-cell>
                <md-table-cell md-label="تاریخ عضویت" dir="ltr">
                    {{getPersianDateTime(item.related_payers[0].transaction_payers.joined_at)}}
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
import moment from 'moment-jalaali'
import {TransactionList} from "@/models/Transaction";
import {axiosMixin, priceFilterMixin} from "@/mixins/Mixins";

export default {
    name: "ListForMonthlyPayment",
    mixins: [priceFilterMixin, axiosMixin],
    props: {
        transactions: {
            type: TransactionList,
            default: new TransactionList()
        }
    },
    methods: {
        getPersianDateTime (date) {
            return moment(Date(date)).format('jYYYY/jMM/jDD HH:mm:ss')
        },
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
            item.loading = true;
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
