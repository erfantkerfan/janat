<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>assignment</md-icon>
                    </div>
                    <h4 class="title">لیست وام ها</h4>
                </md-card-header>
                <md-card-content>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    نام وام
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.name" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <md-field>
                                <label>نوع وام:</label>
                                <md-select v-model="filterData.loan_type_id" name="pages">
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
                    </div>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-25 md-form-label">
                                    مبلغ وام
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.loan_amount" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    مبلغ هر قسط
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.installment_rate" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    تعداد اقساط
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.number_of_installments" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                    </div>

                    <md-empty-state
                        v-if="!loans.loading && loans.list.length === 0"
                        class="md-warning"
                        md-icon="info"
                        md-label="وامی یافت نشد"
                    >
                    </md-empty-state>

                    <md-table
                        :value="loans.list"
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
                        <md-table-row v-if="!loans.loading && loans.list.length > 0"
                                      slot="md-table-row"
                                      slot-scope="{ item }"
                        >
                            <md-table-cell md-label="نام وام" md-sort-by="name">
                                {{ item.name }}
                            </md-table-cell>
                            <md-table-cell md-label="نوع وام" md-sort-by="loanType.display_name">
                                {{ item.loan_type.display_name }}
                            </md-table-cell>
                            <md-table-cell :md-label="'مبلغ وام '+'(' + currencyUnit + ')'" md-sort-by="loan_amount">
                                {{ item.loan_amount | currencyFormat }}
                            </md-table-cell>
                            <md-table-cell :md-label="'مبلغ هر قسط '+'(' + currencyUnit + ')'" md-sort-by="installment_rate">
                                {{ item.installment_rate | currencyFormat }}
                            </md-table-cell>
                            <md-table-cell md-label="تعداد اقساط" md-sort-by="number_of_installments">
                                {{ item.number_of_installments }}
                            </md-table-cell>
                            <md-table-cell md-label="نرخ کارمزد" md-sort-by="interest_rate">
                                {{ item.interest_rate }}%
                            </md-table-cell>
                            <md-table-cell :md-label="'مقدار کارمزد '+'(' + currencyUnit + ')'" md-sort-by="interest_amount">
                                {{ item.interest_amount | currencyFormat }}
                            </md-table-cell>
                            <md-table-cell md-label="نام صندوق" md-sort-by="fund.name">
                                {{ item.fund.name }}
                            </md-table-cell>
                            <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                                {{ item.shamsiDate('created_at').dateTime }}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <md-button
                                    :to="'/loan/'+item.id"
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
                    <loading :active.sync="loans.loading" :is-full-page="false"></loading>
                </md-card-content>
                <list-pagination
                    :paginate="loans.paginate"
                    @changePage="clickCallback"
                />
            </md-card>
        </div>
    </div>
</template>

<script>

    import {LoanList} from '@/models/Loan'
    import ListPagination from '@/components/ListPagination'
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
            loans: new LoanList(),
            filterData: {
                sortation: {
                    field: "created_at",
                    order: "asc"
                },
                perPage: 10,
                perPageOptions: [5, 10, 25, 50, 100, 200, 300, 500],
                name: null,
                loan_amount: null,
                installment_rate: null,
                number_of_installments: null,
                fund_id: null,
                loan_type_id: null
            }
        }),
        mounted() {
            this.getList()
            this.getFunds()
            this.getLoanTypes()
        },
        methods: {
            clickCallback (data) {
                this.getList(data)
            },
            getList (page) {
                if (!page) {
                    page = 1
                }
                this.loans.loading = true;
                this.loans.fetch({
                    page,
                    sortation_field: this.filterData.sortation.field,
                    sortation_order: this.filterData.sortation.order,
                    length: this.filterData.perPage,
                    fund_id: (this.filterData.fund_id === null || this.filterData.fund_id === 0) ? null: this.filterData.fund_id,
                    loan_type_id: (this.filterData.loan_type_id === null || this.filterData.loan_type_id === 0) ? null: this.filterData.loan_type_id,
                    name: this.filterData.name,
                    loan_amount: this.filterData.loan_amount,
                    installment_rate: this.filterData.installment_rate,
                    number_of_installments: this.filterData.number_of_installments
                })
                    .then((response) => {
                        this.loans.loading = false
                        this.loans = new LoanList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.loans.loading = false
                        this.loans = new LoanList()
                    })
            },
            confirmRemove(item) {
                this.$confirm(
                    {
                        message: `از حذف صندوق اطمینان دارید؟`,
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
            }
        }

    }

</script>
