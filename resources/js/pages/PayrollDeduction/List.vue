<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>assignment</md-icon>
                    </div>
                    <h4 class="title">لیست پرداخت های دوره ای</h4>
                </md-card-header>
                <md-card-content>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <md-radio v-model="filterData.payroll_type" value="loan" name="payroll_type">شامل پرداخت اقساط</md-radio>
                            <md-radio v-model="filterData.payroll_type" value="monthly_payment" name="payroll_type">شامل پرداخت ماهانه</md-radio>
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
                        <div class="md-layout-item">
                            <label class="md-layout-item md-size-15 md-form-label">
                                از تاریخ
                            </label>
                            <date-picker
                                v-model="filterData.from"
                                type="datetime"
                                :editable="true"
                                format="YYYY-MM-DD HH:mm:ss"
                                display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                        </div>
                        <div class="md-layout-item">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تا تاریخ
                            </label>
                            <date-picker
                                v-model="filterData.to"
                                type="datetime"
                                :editable="true"
                                format="YYYY-MM-DD HH:mm:ss"
                                display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                        </div>
                    </div>
                    <div class="text-right">
                    </div>
                    <md-empty-state
                        v-if="!payrollDeductions.loading && payrollDeductions.list.length === 0"
                        class="md-warning"
                        md-icon="info"
                        md-label="موردی یافت نشد"
                    >
                    </md-empty-state>

                    <md-table
                        :value="payrollDeductions.list"
                        :md-sort.sync="filterData.sortation.field"
                        :md-sort-order.sync="filterData.sortation.order"
                        :md-sort-fn="customSort"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-toolbar>
                            <md-field>
                                <md-button class="md-dense md-raised md-info" @click="getList()">جستجو</md-button>
<!--                                <md-button class="md-dense md-raised md-primary" to="/user/create">افزودن</md-button>-->
                                <Json-excel
                                    v-if="!payrollDeductions.loading && payrollDeductions.list.length > 0"
                                    :data="payrollDeductions.list"
                                    :fields="json_fields"
                                >
                                    <md-button class="md-dense md-raised md-primary" @click="getList">دانلود اکسل این صفحه</md-button>
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
                        <md-table-row v-if="!payrollDeductions.loading && payrollDeductions.list.length > 0" slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="شناسه" md-sort-by="id">
                                {{ item.id }}
                            </md-table-cell>
                            <md-table-cell md-label="نوع پرداخت" md-sort-by="paid_for_loan">
                                {{ getTypeOfPayrollDeduction(item) }}
                            </md-table-cell>
                            <md-table-cell md-label="زمان شروع بازه" md-sort-by="from">
                                {{item.shamsiDate('from').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="زمان پایان بازه" md-sort-by="to">
                                {{item.shamsiDate('to').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="تاریخ پرداخت" md-sort-by="paid_at">
                                {{item.shamsiDate('paid_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                                {{item.shamsiDate('created_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <md-button
                                    :to="{ name: 'PayrollDeduction.Show', params: {id: item.id}}"
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
                    <loading :active.sync="payrollDeductions.loading" :is-full-page="false"></loading>
                </md-card-content>
                <list-pagination
                    :paginate="payrollDeductions.paginate"
                    @changePage="clickCallback"
                />
            </md-card>
        </div>
    </div>
</template>

<script>
import JsonExcel from 'vue-json-excel'
import ListPagination from '@/components/ListPagination'
import { PayrollDeductionList } from '@/models/PayrollDeduction'
import { getFilterDropdownMixin, axiosMixin, dateMixin } from '@/mixins/Mixins'

export default {
    name: 'PayrollDeductionList',
    watch: {
        'filterData.perPage' : function () {
            this.getList()
        }
    },
    mixins: [getFilterDropdownMixin, axiosMixin, dateMixin],
    computed: {
        json_fields () {
            return {
                'کد عضویت': 'id',
                'کد پرسنلی': 'staff_code',
                'نام': 'f_name',
                'نام خانوادگی': 'l_name',
                'کد ملی': 'SSN',
                'شماره همراه': 'mobile',
                'تلفن ثابت': 'phone',
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
        importFromExcelLoading: false,
        payrollDeductions: new PayrollDeductionList(),
        filterData: {
            sortation: {
                field: "id",
                order: "asc"
            },
            perPage: 10,
            perPageOptions: [5, 10, 25, 50, 100, 200, 300, 500],
            payroll_type: 'loan',
            from: null,
            to: null
        }
    }),
    mounted () {
        this.getList()

        this.getCompanies()
        // this.getFunds()
        // this.getUserStatus()
    },
    methods: {
        getTypeOfPayrollDeduction (payrollDeduction) {
            if (payrollDeduction.paid_for_loan) {
                return 'پرداخت قسط'
            }
            if (payrollDeduction.paid_for_monthly_payment) {
                return 'پرداخت ماهانه'
            }

            return '-'
        },
        clickCallback (data) {
            this.getList(data)
        },
        importFromExcel (event) {
            let formData = new FormData()
            formData.append('users', event.target.files[0])
            this.importFromExcelLoading = true
            axios.post('api/users/import', formData,{
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then( () => {
                    this.importFromExcelLoading = false
                    this.getList()
                })
                .catch( (error) => {
                    this.$store.dispatch('alerts/error', {
                        icon: 'error',
                        title: 'توجه',
                        message: 'مشکلی در وارد کردن اطلاعات رخ داده است.',
                        timeout: 5000
                    });
                    let errorMessage = ''
                    for (let property in error.response.data.errors) {
                        errorMessage += error.response.data.errors[property].row + '<br>\r\n'
                        for (let key in error.response.data.errors[property].values) {
                            errorMessage += key + ': ' + error.response.data.errors[property].values[key] + '-'
                        }
                        this.$store.dispatch('alerts/error', {
                            icon: 'error',
                            title: 'توجه',
                            message: errorMessage,
                            timeout: 10000
                        });
                    }


                    this.importFromExcelLoading = false
                    this.getList()
                })
        },
        getList (page = 1) {
            this.payrollDeductions.loading = true;
            this.payrollDeductions.fetch({
                page,
                length: this.filterData.perPage,
                payroll_type: this.filterData.payroll_type,

                sortation_field: this.filterData.sortation.field,
                sortation_order: this.filterData.sortation.order,

                company_id: (this.filterData.company_id === 0) ? null : this.filterData.company_id,
                from: this.filterData.from,
                to: this.filterData.to
            })
                .then((response) => {
                    this.payrollDeductions.loading = false
                    this.payrollDeductions = new PayrollDeductionList(response.data.data, response.data)
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    this.payrollDeductions.loading = false
                    this.payrollDeductions = new PayrollDeductionList()
                })
        },
        confirmRemove(item) {
            this.$confirm(
                {
                    message: `از حذف کاربر اطمینان دارید؟`,
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
            item.delete()
                .then(() => {
                    this.$store.dispatch('alerts/fire', {
                        icon: 'success',
                        title: 'توجه',
                        message: 'پرداخت دوره ای با موفقیت حذف شد'
                    });
                    this.getList()
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    item.editMode = false;
                    item.loading = false;
                });
        },
        customSort() {
            this.getList()
            return false;
        }
    }
}
</script>
