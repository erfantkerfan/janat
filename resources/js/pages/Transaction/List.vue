<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>assignment</md-icon>
                    </div>
                    <h4 class="title">لیست تراکنش ها</h4>
                </md-card-header>
                <md-card-content>

                    <div class="md-layout">
                        <div class="md-layout-item">
                            <md-radio v-model="filterData.transaction_type" value="payer" name="transaction_type">پرداخت کننده</md-radio>
                            <md-radio v-model="filterData.transaction_type" value="recipient" name="transaction_type">دریافت کننده</md-radio>
                        </div>
                    </div>

                    <div class="md-layout">
                        <div class="md-layout-item">
                            <md-field>
                                <label>وام:</label>
                                <md-select v-model="filterData.loan_id" name="pages">
                                    <md-option
                                        v-for="item in loans.list"
                                        :key="item.id"
                                        :label="item.name"
                                        :value="item.id"
                                    >
                                        <div v-if="item.id !== 0">
                                            صندوق:
                                            {{ item.fund.name }}
                                        </div>
                                        <div v-if="item.id !== 0">
                                            وام:
                                            {{ item.name }}
                                        </div>
                                    </md-option>
                                </md-select>
                            </md-field>
                        </div>
                        <div class="md-layout-item">
                            <md-field>
                                <label>صندوق:</label>
                                <md-select v-model="filterData.fund_id" name="pages">
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
                            <md-field>
                                <label>شرکت:</label>
                                <md-select v-model="filterData.company_id" name="pages">
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
                        </div>
                        <div v-if="false" class="md-layout-item">
                            <md-field>
                                <label>شرکت:</label>
                                <md-select v-model="filterData.company_id" name="pages">
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
                    </div>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <md-field>
                                <label>وضعیت تراکنش:</label>
                                <md-select v-model="filterData.transaction_status_id" name="pages">
                                    <md-option
                                        v-for="item in transactionStatuses.list"
                                        :key="item.id"
                                        :label="item.display_name"
                                        :value="item.id"
                                    >
                                        {{ item.display_name }}
                                    </md-option>
                                </md-select>
                            </md-field>
                        </div>
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    کد عضویت:
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.user_id" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    شماره حساب:
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.account_id" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    تاریخ ایجاد از:
                                </label>
                                <div class="md-layout-item">
                                    <date-picker
                                        v-model="filterData.createdSinceDate"
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
                                    تاریخ ایجاد تا:
                                </label>
                                <div class="md-layout-item">
                                    <date-picker
                                        v-model="filterData.createdTillDate"
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
                                <label class="md-layout-item md-size-15 md-form-label">
                                    تاریخ پرداخت از:
                                </label>
                                <div class="md-layout-item">
                                    <date-picker
                                        v-model="filterData.paidSinceDate"
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
                                    تاریخ پرداخت تا:
                                </label>
                                <div class="md-layout-item">
                                    <date-picker
                                        v-model="filterData.paidTillDate"
                                        type="datetime"
                                        :editable="true"
                                        format="YYYY-MM-DD HH:mm:ss"
                                        display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <md-empty-state
                        v-if="!transctions.loading && transctions.list.length === 0"
                        class="md-warning"
                        md-icon="info"
                        md-label="وامی یافت نشد"
                    >
                    </md-empty-state>
                    <md-table
                        :value="transctions.list"
                        :md-sort.sync="filterData.sortation.field"
                        :md-sort-order.sync="filterData.sortation.order"
                        :md-sort-fn="customSort"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-toolbar>
                            <md-field>
                                <md-button class="md-dense md-raised md-info" @click="getList">جستجو</md-button>
                                <md-button v-if="false" class="md-dense md-raised md-primary" to="/transactions/create">افزودن</md-button>
                            </md-field>
                            <md-field>
                                <label>تعداد در هر صفحه:</label>
                                <md-select v-model="filterData.perPage" name="pages">
                                    <md-option
                                        v-for="item in filterData.perPageOptions"
                                        :key="item"
                                        :label="item"
                                        :value="item"
                                    >
                                        {{ item }}
                                    </md-option>
                                </md-select>
                            </md-field>
                        </md-table-toolbar>
                        <md-table-row v-if="!transctions.loading && transctions.list.length > 0" slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="پرداخت کنندگان">
                                <div v-for="related_payer in item.related_payers"
                                    :key="'related_payer-'+related_payer.id"
                                     v-html="
                                    item.getRelatedModelType(related_payer.transaction_payers_type) +
                                    ': ' +
                                    item.getRelatedModelLabel(related_payer.transaction_payers_type, related_payer.transaction_payers)"
                                />
                            </md-table-cell>
                            <md-table-cell md-label="دریافت کنندگان">
                                <div v-for="related_recipient in item.related_recipients"
                                     :key="'related_recipient-'+related_recipient.id"
                                     v-html="
                                    item.getRelatedModelType(related_recipient.transaction_recipients_type) +
                                    ': ' +
                                    item.getRelatedModelLabel(related_recipient.transaction_recipients_type, related_recipient.transaction_recipients)"
                                />
                            </md-table-cell>
                            <md-table-cell :md-label="'مبلغ'+'('+currencyUnit+')'" md-sort-by="cost">
                                {{item.cost | currencyFormat}}
                            </md-table-cell>
                            <md-table-cell md-label="وضعیت" md-sort-by="parent_transaction_id">
                                {{item.transaction_status.display_name}}
                                <span v-if="item.paid_as_payroll_deduction">
                                    (کسر از حقوق)
                                </span>
                            </md-table-cell>
                            <md-table-cell md-label="مهلت پرداخت" md-sort-by="deadline_at">
                                <span v-if="item.deadline_at">
                                    {{item.shamsiDate('deadline_at').dateTime}}
                                </span>
                                <span v-else>-</span>
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
                                           @click="confirmRemove(item)"
                                           style="margin: .2rem;">
                                    <md-icon>delete</md-icon>
                                </md-button>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
                    <vue-confirm-dialog></vue-confirm-dialog>
                    <loading :active.sync="transctions.loading" :is-full-page="false"></loading>
                </md-card-content>
                <list-pagination
                    :paginate="transctions.paginate"
                    @changePage="clickCallback"
                />
            </md-card>
        </div>
    </div>
</template>

<script>

    import ListPagination from '@/components/ListPagination'
    import { TransactionList } from '@/models/Transaction'
    import { priceFilterMixin, getFilterDropdownMixin, axiosMixin } from '@/mixins/Mixins'

    export default {
        watch: {
            'filterData.perPage' : function () {
                this.getList()
            }
        },
        mixins: [getFilterDropdownMixin, priceFilterMixin, axiosMixin],
        components: {
            ListPagination
        },
        data: () => ({
            transctions: new TransactionList(),
            filterData: {
                sortation: {
                    field: "created_at",
                    order: "asc"
                },
                perPage: 10,
                perPageOptions: [5, 10, 25, 50, 100, 200, 300, 500],
                transaction_type: 'payer',
                fund_id: null,
                user_id: null,
                account_id: null,
                loan_id: null,
                company_id: null,
                transaction_status_id: null,
                paidSinceDate: null,
                paidTillDate: null,
                createdSinceDate: null,
                createdTillDate: null
            }
        }),
        mounted() {
            this.getList()
            this.getLoans()
            this.getFunds()
            this.getCompanies()
            this.getTransactionStatus()
        },
        methods: {
            clickCallback (data) {
                this.getList(data)
            },
            getList (page) {
                if (!page) {
                    page = 1
                }
                this.transctions.loading = true;
                this.transctions.fetch({
                    page,
                    sortation_field: this.filterData.sortation.field,
                    sortation_order: this.filterData.sortation.order,
                    length: this.filterData.perPage,
                    user_id: (this.filterData.user_id === null || this.filterData.user_id.trim().length === 0) ? null: this.filterData.user_id,
                    account_id: (this.filterData.account_id === null || this.filterData.account_id.trim().length === 0) ? null: this.filterData.account_id,
                    fund_id: (this.filterData.fund_id === null || this.filterData.fund_id === 0) ? null: this.filterData.fund_id,
                    loan_id: (this.filterData.loan_id === null || this.filterData.loan_id === 0) ? null: this.filterData.loan_id,
                    company_id: (this.filterData.company_id === null || this.filterData.company_id === 0) ? null: this.filterData.company_id,
                    transaction_status_id: (this.filterData.transaction_status_id === null || this.filterData.transaction_status_id === 0) ? null: this.filterData.transaction_status_id,
                    transaction_type: this.filterData.transaction_type,
                    paid_at_since_date: this.filterData.paidSinceDate,
                    paid_at_till_date: this.filterData.paidTillDate,
                    createdSinceDate: this.filterData.createdSinceDate,
                    createdTillDate: this.filterData.createdTillDate
                })
                    .then((response) => {
                        this.transctions.loading = false
                        this.transctions = new TransactionList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.transctions.loading = false
                        this.transctions = new TransactionList()
                    })
            },
            confirmRemove(item) {
                this.$confirm(
                    {
                        message: `از حذف تراکنش اطمینان دارید؟`,
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
                    .then(function() {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'تراکنش با موفقیت حذف شد'
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
            }
        }

    }

</script>
