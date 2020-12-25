<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>assignment</md-icon>
                    </div>
                    <h4 class="title">لیست وام های تخصیص داده شده</h4>
                </md-card-header>
                <md-card-content>
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
                        <div class="md-layout-item">
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
                                <label class="md-layout-item md-size-15 md-form-label">
                                    کد کاربر:
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.user_id" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    از تاریخ
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
                                    تا تاریخ
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
                    <md-empty-state
                        v-if="!transctions.loading && transctions.list.length === 0"
                        class="md-warning"
                        md-icon="cancel_presentation"
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
                                <md-button class="md-dense md-raised md-primary" to="/loan/create">افزودن</md-button>
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
                                    :key="'related_payer-'+related_payer.id">
                                    {{item.getRelatedModelType(related_payer.transaction_payers_type)}}:
                                    {{item.getRelatedModelLabel(related_payer.transaction_payers_type, related_payer.transaction_payers)}}
                                </div>
                            </md-table-cell>
                            <md-table-cell md-label="دریافت کنندگان">
                                <div v-for="related_recipient in item.related_recipients"
                                     :key="'related_recipient-'+related_recipient.id">
                                    {{item.getRelatedModelType(related_recipient.transaction_recipients_type)}}:
                                    {{item.getRelatedModelLabel(related_recipient.transaction_recipients_type, related_recipient.transaction_recipients)}}
                                </div>
                            </md-table-cell>
                            <md-table-cell md-label="مبلغ" md-sort-by="cost">
                                {{item.cost}}
                            </md-table-cell>
                            <md-table-cell md-label="وضعیت" md-sort-by="parent_transaction_id">
                                {{item.transaction_status.display_name}}
                            </md-table-cell>
                            <md-table-cell md-label="مهلت پرداخت" md-sort-by="deadline_at">
                                <span v-if="item.deadline_at">
                                    {{item.shamsiDate('deadline_at').dateTime}}
                                </span>
                                <span v-else>-</span>
                            </md-table-cell>
                            <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                                {{item.shamsiDate('created_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <router-link :to="'/transactions/'+item.id">
                                    <md-button
                                        class="md-icon-button md-raised md-round md-info"
                                        style="margin: .2rem;"
                                    >
                                        <md-icon>edit</md-icon>
                                    </md-button>
                                </router-link>
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
                <md-card-actions v-if="transctions.paginate" md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            نمایش
                            {{ transctions.paginate.from }}
                            تا
                            {{ transctions.paginate.to }}
                            از
                            {{ transctions.paginate.total }}
                            مورد
                        </p>
                    </div>
                    <paginate
                        v-model="transctions.paginate.current_page"
                        :page-count="transctions.paginate.last_page"
                        :page-range="3"
                        :margin-pages="2"
                        :click-handler="clickCallback"
                        :prev-text="'<'"
                        :next-text="'>'"
                        :container-class="'pagination pagination-no-border pagination-success pagination-primary'"
                        :page-class="'page-item'"
                        :page-link-class="'page-link'">
                    </paginate>
                </md-card-actions>
            </md-card>
        </div>
    </div>
</template>

<script>

    import Pagination from "@/components/Pagination";
    import getFilterDropdownMixin from '@/mixins/getFilterDropdownMixin';
    import {TransactionList} from "@/models/Transaction";

    export default {
        watch: {
            'filterData.perPage' : function () {
                this.getList()
            }
        },
        mixins: [getFilterDropdownMixin],
        components: {
            "pagination": Pagination
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
                fund_id: null,
                user_id: null,
                loan_id: null,
                company_id: null,
                transaction_status_id: null,
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
                    fund_id: (this.filterData.fund_id === null || this.filterData.fund_id === 0) ? null: this.filterData.fund_id,
                    loan_id: (this.filterData.loan_id === null || this.filterData.loan_id === 0) ? null: this.filterData.loan_id,
                    company_id: (this.filterData.company_id === null || this.filterData.company_id === 0) ? null: this.filterData.company_id,
                    transaction_status_id: (this.filterData.transaction_status_id === null || this.filterData.transaction_status_id === 0) ? null: this.filterData.transaction_status_id,
                    createdSinceDate: this.filterData.createdSinceDate,
                    createdTillDate: this.filterData.createdTillDate
                })
                    .then((response) => {
                        this.transctions.loading = false
                        this.transctions = new TransactionList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        this.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        this.transctions.loading = false
                        this.transctions = new TransactionList()
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
                    .catch(function(error) {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
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
