<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-small-size-100">
            <div class="md-layout-item md-size-100">
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
                                مبلغ
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="transaction.cost"/>
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                توضیحات مدیر
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-textarea v-model="transaction.manager_comment" />
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                توضیحات کاربر
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-textarea v-model="transaction.user_comment" />
                                </md-field>
                            </div>
                        </div>
                        <md-field>
                            <label>وضعیت تراکنش</label>
                            <md-select v-model="transaction.transaction_status.id" name="pages">
                                <md-option
                                    v-for="item in transactionStatuses.list"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id"
                                >
                                    {{ item.display_name }}
                                </md-option>
                            </md-select>
                        </md-field>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                مهلت پرداخت
                            </label>
                            <div class="md-layout-item">
                                <date-picker
                                    v-model="transaction.deadline_at"
                                    type="datetime"
                                    :editable="true"
                                    format="YYYY-MM-DD HH:mm:ss"
                                    display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تاریخ پرداخت
                            </label>
                            <div class="md-layout-item">
                                <date-picker
                                    v-model="transaction.paid_at"
                                    type="datetime"
                                    :editable="true"
                                    format="YYYY-MM-DD HH:mm:ss"
                                    display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                            </div>
                        </div>
                        <div v-if="!isCreateForm()" class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تاریخ ایجاد
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="transaction.shamsiDate('created_at').date" :disabled="true"/>
                                </md-field>
                            </div>
                        </div>

                        <loading :active.sync="transaction.loading" :is-full-page="false"></loading>

                    </md-card-content>

                </md-card>

                <md-card v-if="transaction.related_payers">
                    <md-card-header class="md-card-header-text md-card-header-warning">
                        <div class="card-text">
                            <h4 class="title">پرداخت کنندگان</h4>
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <md-table :value="transaction.related_payers"
                                  table-header-color="green"
                                  class="table-hover">
                            <md-table-row slot="md-table-row" slot-scope="{ item }">
                                <md-table-cell md-label="نوع پرداخت کننده">{{ transaction.getRelatedModelType(item.transaction_payers_type) }}</md-table-cell>
                                <md-table-cell md-label="اطلاعات پرداخت کننده">{{ transaction.getRelatedModelLabel(item.transaction_payers_type, item.transaction_payers) }}</md-table-cell>
                                <md-table-cell md-label="مشاهده">
                                    <md-button
                                        :to="transaction.getRelatedModelRoute(item.transaction_payers_type, item.transaction_payers)"
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

                <md-card v-if="transaction.related_recipients">
                    <md-card-header class="md-card-header-text md-card-header-blue">
                        <div class="card-text">
                            <h4 class="title">دریافت کنندگان</h4>
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <md-table :value="transaction.related_recipients"
                                  table-header-color="green"
                                  class="table-hover">
                            <md-table-row slot="md-table-row" slot-scope="{ item }">
                                <md-table-cell md-label="نوع دریافت کننده">{{ transaction.getRelatedModelType(item.transaction_recipients_type) }}</md-table-cell>
                                <md-table-cell md-label="اطلاعات دریافت کننده">{{ transaction.getRelatedModelLabel(item.transaction_recipients_type, item.transaction_recipients) }}</md-table-cell>
                                <md-table-cell md-label="مشاهده">
                                    <md-button
                                        :to="transaction.getRelatedModelRoute(item.transaction_recipients_type, item.transaction_recipients)"
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
    </div>
</template>

<script>
    import { Transaction } from '@/models/Transaction'
    import { priceFilterMixin, getFilterDropdownMixin, axiosMixin } from '@/mixins/Mixins'

    export default {
        watch: {
            'transaction.transaction_status.id': function () {
                this.transaction.transaction_statusـid = this.transaction.transaction_status.id
            }
        },
        mixins: [getFilterDropdownMixin, priceFilterMixin, axiosMixin],
        data: () => ({
            transaction: new Transaction(),
            sortation: {
                field: "created_at",
                order: "asc"
            },
        }),
        mounted() {
            this.getData()
            this.getTransactionStatus()
        },
        methods: {
            isCreateForm () {
                return (this.$route.name === 'Transaction.Create')
            },
            getData () {
                if (this.isCreateForm()) {
                    return false
                }
                this.transaction.loading = true;
                this.transaction.show(this.$route.params.id)
                    .then((response) => {
                        this.transaction.loading = false;
                        this.transaction = new Transaction(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.transaction.loading = false;
                        this.transaction = new Transaction()
                    })
            },
            updateTransaction () {
                if (this.isCreateForm()) {
                    this.createTransaction()
                    return
                }
                let that = this
                this.transaction.loading = true;
                this.transaction.update()
                    .then((response) => {
                        that.transaction.loading = false;
                        that.transaction = new Transaction(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات با موفقیت ویرایش شد'
                        });
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.transaction.loading = false;
                        that.transaction = new Transaction()
                    })
            },
            createTransaction () {
                let that = this
                this.transaction.loading = true
                delete this.transaction.created_at
                delete this.transaction.updated_at
                this.transaction.create()
                    .then((response) => {
                        that.transaction.loading = false;
                        that.transaction = new Transaction(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات صندوق با موفقیت ثبت شد'
                        });
                        that.$router.push({ path: '/allocated_loan/'+that.transaction.id })
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.transaction.loading = false;
                        that.transaction = new Transaction()
                    })
            }
        }
    }
</script>
