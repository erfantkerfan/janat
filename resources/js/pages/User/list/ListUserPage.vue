<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>assignment</md-icon>
                    </div>
                    <h4 class="title">لیست کاربران</h4>
                </md-card-header>
                <md-card-content>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-25 md-form-label">
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
                                <label class="md-layout-item md-size-25 md-form-label">
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
                                    کد عضویت
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.id" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-25 md-form-label">
                                    کد پرسنلی
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.staff_code" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-25 md-form-label">
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
                            <div class="md-layout">
                                <label class="md-layout-item md-size-25 md-form-label">
                                    تلفن ثابت
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.phone" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-25 md-form-label">
                                    تلفن همراه
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.mobile" />
                                    </md-field>
                                </div>
                            </div>
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
                            <md-checkbox v-model="filterData.hasLoanPayrollDeduction">دارای وام کسر از حقوق</md-checkbox>
                            <md-checkbox v-model="filterData.hasAccountPayrollDeduction">دارای ماهانه کسر از حقوق</md-checkbox>
                        </div>
                    </div>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <md-field>
                                <label>وضعیت:</label>
                                <md-select v-model="filterData.status_id" name="pages">
                                    <md-option
                                        v-for="item in userStatuses.list"
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
                            <label class="md-layout-item md-size-15 md-form-label">
                                از تاریخ
                            </label>
                            <date-picker
                                v-model="filterData.createdSinceDate"
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
                                v-model="filterData.createdTillDate"
                                type="datetime"
                                :editable="true"
                                format="YYYY-MM-DD HH:mm:ss"
                                display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                        </div>
                    </div>
                    <div class="text-right">
                    </div>
                    <md-empty-state
                        v-if="!users.loading && users.list.length === 0"
                        class="md-warning"
                        md-icon="info"
                        md-label="کاربری یافت نشد"
                    >
                    </md-empty-state>

                    <div>
                        <button type="button" class="md-button md-dense md-raised md-info md-theme-default" style="width: 145px;">
                            <div class="md-ripple">
                                <div class="md-button-content" style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;">
                                    <label for="importExcelInputFile" style="position: absolute;top: 0;left: 0;width: 100%;height: 100%; display: flex;justify-content: center;align-items: center;">
                                        <span v-if="!importFromExcelLoading">
                                            ورود اطلاعات از اکسل
                                        </span>
                                        <span v-else>
                                            کمی صبر کنید...
                                        </span>
                                        <input type="file" @change="importFromExcel($event)" id="importExcelInputFile" style="display: none">
                                    </label>
                                </div>
                            </div>
                        </button>
                        <a href="/assets/sample-import/import.xlsx" target="_blank">
                            <button type="button" class="md-button md-dense md-raised md-info md-theme-default" style="width: 145px;">
                                دانلود نمونه فایل اکسل
                            </button>
                        </a>
                    </div>
                    <md-table
                        :value="users.list"
                        :md-sort.sync="filterData.sortation.field"
                        :md-sort-order.sync="filterData.sortation.order"
                        :md-sort-fn="customSort"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-toolbar>
                            <md-field>
                                <md-button class="md-dense md-raised md-info" @click="getList">جستجو</md-button>
                                <md-button class="md-dense md-raised md-primary" to="/user/create">افزودن</md-button>
                                <Json-excel
                                    v-if="!users.loading && users.list.length > 0"
                                    :data="users.list"
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
                        <md-table-row v-if="!users.loading && users.list.length > 0" slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="کد عضویت" md-sort-by="id">
                                {{item.id}}
                            </md-table-cell>
                            <md-table-cell md-label="کد پرسنلی" md-sort-by="staff_code">
                                {{item.staff_code}}
                            </md-table-cell>
                            <md-table-cell md-label="نام" md-sort-by="f_name">
                                {{item.f_name}}
                            </md-table-cell>
                            <md-table-cell md-label="نام خانوادگی" md-sort-by="l_name">
                                {{item.l_name}}
                            </md-table-cell>
                            <md-table-cell md-label="نام شرکت" md-sort-by="company.name" v-html="item.accounts.getCompaniesNameWithAccountNumber().join('<br>')" />
                            <md-table-cell md-label="کد ملی" md-sort-by="SSN">{{item.SSN}}</md-table-cell>
                            <md-table-cell md-label="شماره همراه" md-sort-by="mobile">{{item.mobile}}</md-table-cell>
                            <md-table-cell md-label="تلفن ثابت" md-sort-by="phone">{{item.phone}}</md-table-cell>
                            <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                                {{item.shamsiDate('created_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <md-button
                                    :to="'/user/'+item.id"
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
                    <loading :active.sync="users.loading" :is-full-page="false"></loading>
                </md-card-content>
                <list-pagination
                    :paginate="users.paginate"
                    @changePage="clickCallback"
                />
            </md-card>
        </div>
    </div>
</template>

<script>

    import JsonExcel from 'vue-json-excel'
    import { UserList } from '@/models/User'
    import ListPagination from '@/components/ListPagination'
    import { getFilterDropdownMixin, axiosMixin, dateMixin } from '@/mixins/Mixins'

    export default {
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
            users: new UserList(),
            filterData: {
                sortation: {
                    field: "created_at",
                    order: "asc"
                },
                perPage: 10,
                perPageOptions: [5, 10, 25, 50, 100, 200, 300, 500],
                hasLoanPayrollDeduction: false,
                hasAccountPayrollDeduction: false,
                f_name: null,
                l_name: null,
                phone: null,
                id: null,
                staff_code: null,
                SSN: null,
                status_id: null,
                company_id: null,
                fund_id: null,
                createdSinceDate: null,
                createdTillDate: null
            }
        }),
        mounted() {
            this.getList()
            this.getCompanies()
            this.getFunds()
            this.getUserStatus()
        },
        methods: {
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
            getList (page) {
                if (!page) {
                    page = 1
                }
                this.users.loading = true;
                this.users.fetch({
                    page,
                    sortation_field: this.filterData.sortation.field,
                    sortation_order: this.filterData.sortation.order,
                    hasLoanPayrollDeduction: (this.filterData.hasLoanPayrollDeduction === false) ? null : this.filterData.hasLoanPayrollDeduction,
                    hasAccountPayrollDeduction: (this.filterData.hasAccountPayrollDeduction === false) ? null : this.filterData.hasAccountPayrollDeduction,
                    length: this.filterData.perPage,
                    f_name: this.filterData.f_name,
                    l_name: this.filterData.l_name,
                    phone: this.filterData.phone,
                    id: this.filterData.id,
                    staff_code: this.filterData.staff_code,
                    SSN: this.filterData.SSN,
                    fund_id: (this.filterData.fund_id === 0) ? null: this.filterData.fund_id,
                    status_id: (this.filterData.status_id === 0) ? null: this.filterData.status_id,
                    company_id: (this.filterData.company_id === 0) ? null : this.filterData.company_id,
                    createdSinceDate: this.filterData.createdSinceDate,
                    createdTillDate: this.filterData.createdTillDate
                })
                    .then((response) => {
                        this.users.loading = false
                        this.users = new UserList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.users.loading = false
                        this.users = new UserList()
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
                let that = this;
                item.delete()
                    .then(function(response) {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'کاربر با موفقیت حذف شد'
                        });
                        that.getList()
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
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
