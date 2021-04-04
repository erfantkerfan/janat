<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>autorenew</md-icon>
                    </div>
                    <h4 class="title">پرداخت ماهانه صندوق به صورت کسر از حقوق</h4>
                </md-card-header>
                <md-card-content>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                پرداخت ماهانه صندوق به صورت کسر از حقوق برای حساب هایی که در بازه زمانی انتخاب شده زیر پرداختی ای به صورت کسر از حقوق به صندوق نداشته اند
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
                                <md-button class="md-dense md-raised md-danger" @click="rollback">برگشت</md-button>
                            </div>
                        </div>
                    </div>
                    <vue-confirm-dialog></vue-confirm-dialog>
                    <loading :active.sync="accounts.loading" :is-full-page="false"></loading>

                    <md-empty-state
                        v-if="payRequestIsSent && !accounts.loading && accounts.list.length === 0"
                        class="md-warning"
                        md-icon="cancel_presentation"
                        md-label="در بازه انتخاب شده هیچ حسابی وجود ندارد که پرداختی ماهانه کسر از حقوق نداشته باشد."
                    >
                    </md-empty-state>

<!--                    :md-sort.sync="sortation.field"-->
<!--                    :md-sort-order.sync="sortation.order"-->
<!--                    :md-sort-fn="customSort"-->

                    <md-field>
                        <Json-excel
                            v-if="!accounts.loading && accounts.list.length > 0"
                            :data="accounts.list"
                            :fields="json_fields"
                        >
                            <md-button class="md-dense md-icon-button md-raised md-info">
                                <md-icon>file_download</md-icon>
                            </md-button>
                        </Json-excel>
                    </md-field>
                    <md-table
                        :value="accounts.list"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="نام" md-sort-by="user.f_name">{{item.user.f_name}}</md-table-cell>
                            <md-table-cell md-label="نام خانوادگی" md-sort-by="user.l_name">{{item.user.l_name}}</md-table-cell>
                            <md-table-cell md-label="شماره عضویت" md-sort-by="user.id">{{item.user.id}}</md-table-cell>
                            <md-table-cell md-label="شماره حساب" md-sort-by="id">{{item.id}}</md-table-cell>
                            <md-table-cell md-label="نام صندوق" md-sort-by="fund.name">{{item.fund.name}}</md-table-cell>
                            <md-table-cell md-label="تاریخ عضویت" md-sort-by="created_at">
                                {{item.shamsiDate('joined_at').date}}
                            </md-table-cell>
                            <md-table-cell md-label="مشاهده تراکنش">
                                <md-button
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
    import {AccountList} from '@/models/Account'
    import moment from 'moment-jalaali'
    import { priceFilterMixin, axiosMixin, dateMixin } from '@/mixins/Mixins'

    export default {
        name: 'PayFundMonthlyPaymentByPayrollDeduction',
        mixins: [priceFilterMixin, axiosMixin, dateMixin],
        computed: {
            json_fields () {
                return {
                    'نام': 'user.f_name',
                    'نام خانوادگی': 'user.l_name',
                    'شماره عضویت': 'user.id',
                    'شماره حساب': 'id',
                    'نام صندوق': 'fund.name',
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
            accounts: new AccountList(),
            paySinceDate: null,
            payTillDate: null,
            sortation: {
                field: 'created_at',
                order: 'asc'
            },
        }),
        mounted() {
            this.loadDatePicker()
        },
        methods: {
            loadDatePicker () {
                this.paySinceDate = moment().startOf('jMonth').format('YYYY-MM-DD HH:mm:ss')
                this.payTillDate = moment().endOf('jMonth').format('YYYY-MM-DD HH:mm:ss')
            },
            pay () {
                this.accounts.loading = true
                axios.get('/api/accounts/pay_periodic_payroll_deduction', {
                    params: {
                        pay_since_date: this.paySinceDate,
                        pay_till_date: this.payTillDate
                    }
                })
                    .then( (response) => {
                        this.accounts.loading = false
                        this.payRequestIsSent = true
                        this.accounts = new AccountList(response.data)
                        if(this.accounts.list.length > 0) {
                            this.$store.dispatch('alerts/fire', {
                                icon: 'success',
                                title: 'توجه',
                                message: 'برای حساب هایی که پرداخت کسر از حقوق ثبت شده و در بازه زمانی انتخاب شده پرداخت کسر از حقوق نداشته اند تراکنش ثبت شد و لیست حساب ها برای شما نمایش داده می شود.'
                            });
                        }
                    })
                    .catch( (error) => {
                        this.axios_handleError(error)
                        this.accounts.loading = false
                    })
            },
            rollback () {
                this.accounts.loading = true
                axios.get('/api/accounts/rollback_pay_periodic_payroll_deduction', {
                    params: {
                        pay_since_date: this.paySinceDate,
                        pay_till_date: this.payTillDate
                    }
                })
                    .then( () => {
                        this.accounts.loading = false
                        this.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'تمام تراکنش های کسر از حقوق مربوط به شهریه که در بازه زمانی انتخاب شده پرداخت شده بودند حذف شدند.'
                        })
                    })
                    .catch( (error) => {
                        this.axios_handleError(error)
                        this.accounts.loading = false
                    })
            },
            openLinkInNewTab (transactionId) {
                window.open('/dashboard#/allocated_loan/' + transactionId, '_blank');
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
        }

    }

</script>

