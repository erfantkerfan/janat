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
                            پرداخت دوره ای اقساط
                        </h4>
                    </md-card-header>

                    <md-card-content>
                        <div class="md-layout">
                            <div class="md-layout-item">
                                <md-field>
                                    <label>شرکت:</label>
                                    <md-select v-model="companyId" name="pages">
                                        <md-option
                                            v-for="item in companies.list"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.id"
                                        >
                                            {{ item.name }}
                                        </md-option>
                                    </md-select>
                                </md-field>
                            </div>
                            <div class="md-layout-item">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    زمان شروع بازه
                                </label>
                                <date-picker
                                    v-model="from"
                                    type="datetime"
                                    :editable="true"
                                    format="YYYY-MM-DD HH:mm:ss"
                                    display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                            </div>
                            <div class="md-layout-item">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    زمان پایان بازه
                                </label>
                                <date-picker
                                    v-model="to"
                                    type="datetime"
                                    :editable="true"
                                    format="YYYY-MM-DD HH:mm:ss"
                                    display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                            </div>
                            <div class="md-layout-item">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    تاریخ پرداخت
                                </label>
                                <date-picker
                                    v-model="paidAt"
                                    type="datetime"
                                    :editable="true"
                                    format="YYYY-MM-DD HH:mm:ss"
                                    display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                            </div>
                        </div>

                        <loading :active.sync="loading" :is-full-page="false"></loading>

                    </md-card-content>

                    <md-card-actions>
                        <md-button type="submit" @click="create" :disable="loading" :loading="loading">
                            تایید
                        </md-button>
                    </md-card-actions>

                </md-card>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import moment from 'moment-jalaali'
import {getFilterDropdownMixin, axiosMixin} from '@/mixins/Mixins'

export default {
    name: 'Create',
    mixins: [getFilterDropdownMixin, axiosMixin],
    data: ()=>{
        return {
            loading: false,
            companyId: null,
            to: moment().endOf('jMonth').format('YYYY-MM-DD HH:mm:ss'),
            from: moment().startOf('jMonth').format('YYYY-MM-DD HH:mm:ss'),
            paidAt: moment().endOf('jMonth').format('YYYY-MM-DD HH:mm:ss')
        }
    },
    mounted () {
        this.getCompanies(false)
    },
    methods: {
        create () {
            if (this.$route.name === 'PayrollDeduction.Create.Loan') {
                this.createLoan()
            }
            if (this.$route.name === 'PayrollDeduction.Create.MonthlyPayment') {
                this.createMonthlyPayment()
            }
        },
        createLoan () {
            this.loading = true
            axios.post('/api/payroll_deduction/pay/loan', {
                company_id: this.companyId,
                from: this.from,
                to: this.to,
                paid_at: this.paidAt,
            })
                .then((response) => {
                    this.loading = false
                    this.$store.dispatch('alerts/fire', {
                        icon: 'success',
                        title: 'توجه',
                        message: 'اطلاعات صندوق با موفقیت ثبت شد'
                    });
                    this.$router.push({ name: 'PayrollDeduction.Show', params: {id: response.data.id} })
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    this.loading = false;
                })
        },
        createMonthlyPayment () {
            this.loading = true
            axios.post('/api/payroll_deduction/pay/monthly_payment', {
                company_id: this.companyId,
                from: this.from,
                to: this.to,
                paid_at: this.paidAt,
            })
                .then((response) => {
                    this.loading = false
                    this.$store.dispatch('alerts/fire', {
                        icon: 'success',
                        title: 'توجه',
                        message: 'اطلاعات صندوق با موفقیت ثبت شد'
                    });
                    this.$router.push({ name: 'PayrollDeduction.Show', params: {id: response.data.id} })
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    this.loading = false;
                })
        },
    }
}
</script>
