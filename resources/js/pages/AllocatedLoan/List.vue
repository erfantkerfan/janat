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
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    نام
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.f_name" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    نام خانوادگی
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.l_name" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    کد ملی
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.SSN" />
                                    </md-field>
                                </div>
                            </div>
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
                                            صندوق:
                                            {{ item.fund.name }}
                                        <br>
                                            وام:
                                            {{ item.name }}
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
                                        :label="item.id"
                                        :value="item.id"
                                    >
                                        {{ item.name }}
                                    </md-option>
                                </md-select>
                            </md-field>
                        </div>
                        <div class="md-layout-item">
                            <md-checkbox v-model="filterData.settled">تسویه شده</md-checkbox>
                            <md-checkbox v-model="filterData.notSettled">تسویه نشده</md-checkbox>
                            <md-checkbox v-model="filterData.payroll_deduction">کسر از حقوق</md-checkbox>
                        </div>
                    </div>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
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
                                <label class="md-layout-item md-size-20 md-form-label">
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
                                <label class="md-layout-item md-size-15 md-form-label">
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
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    تاریخ پرداخت بعد از
                                </label>
                                <div class="md-layout-item">
                                    <date-picker
                                        v-model="filterData.lastPaidAtAfter"
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
                                    تاریخ پرداخت قبل از
                                </label>
                                <div class="md-layout-item">
                                    <date-picker
                                        v-model="filterData.lastPaidAtBefore"
                                        type="datetime"
                                        :editable="true"
                                        format="YYYY-MM-DD HH:mm:ss"
                                        display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <md-empty-state
                        v-if="!allocatedLoans.loading && allocatedLoans.list.length === 0"
                        class="md-warning"
                        md-icon="cancel_presentation"
                        md-label="وامی یافت نشد"
                    >
                    </md-empty-state>
                    <md-table
                        :value="allocatedLoans.list"
                        :md-sort.sync="filterData.sortation.field"
                        :md-sort-order.sync="filterData.sortation.order"
                        :md-sort-fn="customSort"
                        class="paginated-table table-hover"
                    >
                        <md-table-toolbar>
                            <md-field>
                                <md-button class="md-dense md-icon-button md-raised md-primary" @click="getList">
                                    <md-icon>search</md-icon>
                                </md-button>
                                <Json-excel
                                    :data="allocatedLoans.list"
                                    :fields="json_fields"
                                >
                                    <md-button class="md-dense md-icon-button md-raised md-info">
                                        <md-icon>file_download</md-icon>
                                    </md-button>
                                </Json-excel>
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
                        <md-table-row v-if="!allocatedLoans.loading && allocatedLoans.list.length > 0"
                                      slot="md-table-row"
                                      slot-scope="{ item }"
                                      :class="{ 'table-success': item.is_settled }"
                        >
                            <md-table-cell md-label="نام" md-sort-by="account.user.f_name">
                                {{item.account.user.f_name}}
                            </md-table-cell>
                            <md-table-cell md-label="نام خانوادگی" md-sort-by="account.user.l_name">
                                {{item.account.user.l_name}}
                            </md-table-cell>
                            <md-table-cell :md-label="'مبلغ وام'+'('+currencyUnit+')'" md-sort-by="loan_amount">
                                {{item.loan_amount | currencyFormat}}
                            </md-table-cell>
                            <md-table-cell :md-label="'مبلغ هر قسط'+'('+currencyUnit+')'" md-sort-by="installment_rate">
                                {{item.installment_rate | currencyFormat}}
                            </md-table-cell>
                            <md-table-cell md-label="نام وام" md-sort-by="loan.name">
                                {{item.loan.name}}
                            </md-table-cell>
                            <md-table-cell md-label="نام صندوق" md-sort-by="loan.fund.name">
                                {{item.loan.fund.name}}
                            </md-table-cell>
                            <md-table-cell md-label="وضعیت">
                                <span v-if="item.is_settled">
                                    تسویه شده
                                </span>
                                <span v-else>
                                    تسویه نشده
                                </span>
                            </md-table-cell>
                            <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                                {{item.shamsiDate('created_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <md-button
                                    :to="'/allocated_loan/'+item.id"
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
                    <loading :active.sync="allocatedLoans.loading" :is-full-page="false"></loading>
                </md-card-content>
                <list-pagination
                    :paginate="allocatedLoans.paginate"
                    @changePage="clickCallback"
                />
            </md-card>
        </div>
    </div>
</template>

<script>

    import {AllocatedLoanList} from '@/models/AllocatedLoan'
    import ListPagination from '@/components/ListPagination'
    import JsonExcel from 'vue-json-excel'
    import { priceFilterMixin, getFilterDropdownMixin, axiosMixin, dateMixin } from '@/mixins/Mixins'

    export default {
        watch: {
            'filterData.perPage' : function () {
                this.getList()
            }
        },
        mixins: [getFilterDropdownMixin, priceFilterMixin, axiosMixin, dateMixin],
        computed: {
            json_fields () {
                return {
                    'نام': 'account.user.f_name',
                    'نام خانوادگی': 'account.user.l_name',
                    'مبلغ وام': 'loan_amount',
                    'مبلغ هر قسط': 'installment_rate',
                    'نام وام': 'loan.name',
                    'نام صندوق': 'loan.fund.name',
                    'وضعیت': {
                        field: 'is_settled',
                        callback: (value) => {
                            if (value) {
                                return 'تسویه شده'
                            } else {
                                return 'تسویه نشده'
                            }
                        },
                    },
                    'تاریخ ایجاد': {
                        field: 'created_at',
                        callback: (value) => {
                            return this.shamsiDate(value).dateTime
                        }
                    },
                }
            }
        },
        components: {
            ListPagination,
            JsonExcel
        },
        data: () => ({
            allocatedLoans: new AllocatedLoanList(),
            filterData: {
                sortation: {
                    field: "created_at",
                    order: "asc"
                },
                perPage: 10,
                perPageOptions: [5, 10, 25, 50, 100, 200, 300, 500],
                loan_amount: null,
                installment_rate: null,
                number_of_installments: null,
                fund_id: null,
                loan_id: null,
                settled: false,
                payroll_deduction: false,
                notSettled: false,
                f_name: null,
                l_name: null,
                SSN: null,
                lastPaidAtBefore: null,
                lastPaidAtAfter: null,
                createdSinceDate: null,
                createdTillDate: null
            }
        }),
        mounted() {
            this.getList()
            this.getFunds()
            this.getLoans()
        },
        methods: {
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
            }
        }

    }

</script>
