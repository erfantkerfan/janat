<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-small-size-100">
            <div class="md-layout-item md-size-100">
                <md-card>
                    <md-card-header class="md-card-header-icon md-card-header-green">
                        <div class="card-icon">
                            <md-icon>monetization_on</md-icon>
                        </div>
                        <h4 class="title">
                            ویرایش اطلاعات وام
                        </h4>
                    </md-card-header>

                    <md-card-content>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                نام وام
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="loan.name"/>
                                </md-field>
                            </div>
                        </div>
                        <price-input v-if="!isCreateForm()" v-model="loan.installment_rate" :label="'مبلغ هر قسط'" :disabled="true" />
                        <hr v-if="!isCreateForm()">
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تعداد اقساط
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="loan.number_of_installments"/>
                                </md-field>
                            </div>
                        </div>
                        <div v-if="!isCreateForm()" class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                نرخ کارمزد
                            </label>
                            <div class="md-layout-item">
                                {{ loan.interest_rate }}%
                            </div>
                        </div>
                        <hr v-if="!isCreateForm()">
                        <price-input v-if="!isCreateForm()" v-model="loan.interest_amount" :label="'مقدار کارمزد'" :disabled="true" />
                        <hr v-if="!isCreateForm()">
                        <md-field>
                            <label>نوع وام:</label>
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
                        <md-field>
                            <label>صندوق:</label>
                            <md-select v-model="loan.fund.id" name="pages">
                                <md-option
                                    v-for="item in funds.list"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </md-option>
                            </md-select>
                        </md-field>
                        <div v-if="!isCreateForm()" class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تاریخ تعریف وام
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="loan.shamsiDate('created_at').date" :disabled="true"/>
                                </md-field>
                            </div>
                        </div>

                        <loading :active.sync="loan.loading || funds.loading" :is-full-page="false"></loading>

                    </md-card-content>

                    <md-card-actions>
                        <md-button type="submit" @click="updateLoan">
                            ذخیره اطلاعات
                        </md-button>
                    </md-card-actions>

                </md-card>
            </div>
        </div>
    </div>
</template>

<script>
    import {Loan} from '@/models/Loan'
    import PriceInput from '@/components/PriceInput'
    import { priceFilterMixin, getFilterDropdownMixin, axiosMixin } from '@/mixins/Mixins'

    export default {
        components: {PriceInput},
        watch: {
            'loan.fund.id': function () {
                this.loan.fund_id = this.loan.fund.id
            }
        },
        mixins: [priceFilterMixin, getFilterDropdownMixin, axiosMixin],
        data: () => ({
            loan: new Loan(),
        }),
        mounted() {
            this.getData()
            this.getFunds(false)
            this.getLoanTypes(false)
        },
        methods: {
            isCreateForm () {
                return (this.$route.name === 'Loan.Create')
            },
            getData () {
                if (this.isCreateForm()) {
                    return false
                }
                this.loan.loading = true;
                this.loan.show(this.$route.params.id)
                    .then((response) => {
                        this.loan.loading = false;
                        this.loan = new Loan(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.loan.loading = false;
                        this.loan = new Loan()
                    })
            },
            updateLoan () {
                if (this.isCreateForm()) {
                    this.createLoan()
                    return
                }
                let that = this
                this.loan.loading = true;
                this.loan.update()
                    .then((response) => {
                        that.loan.loading = false;
                        that.loan = new Loan(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات با موفقیت ویرایش شد'
                        });
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.loan.loading = false;
                    })
            },
            createLoan () {
                let that = this
                this.loan.loading = true
                delete this.loan.created_at
                delete this.loan.updated_at
                this.loan.create()
                    .then((response) => {
                        that.loan.loading = false;
                        that.loan = new Loan(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات صندوق با موفقیت ثبت شد'
                        });
                        that.$router.push({ path: '/loan/'+that.loan.id })
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.loan.loading = false;
                    })
            }
        }
    }
</script>
