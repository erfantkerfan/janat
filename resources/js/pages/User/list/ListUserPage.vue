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
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
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
                                <label class="md-layout-item md-size-15 md-form-label">
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
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    شرکت
                                </label>
                                <div class="md-layout-item">
                                    <md-field>
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
                        </div>
                    </div>
                    <div class="md-layout">
                        <div class="md-layout-item">
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    وضعیت
                                </label>
                                <div class="md-layout-item">
                                    <md-field>
                                        <md-select v-model="filterData.status_id" name="pages">
                                            <md-option
                                                v-for="item in userStatuses.list"
                                                :key="item.id"
                                                :label="item.displayName"
                                                :value="item.id"
                                            >
                                                {{ item.displayName }}
                                            </md-option>
                                        </md-select>
                                    </md-field>
                                </div>
                            </div>
                        </div>
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
                    <div class="text-right">
                    </div>
                    <md-table
                        :value="users.list"
                        :md-sort.sync="sortation.field"
                        :md-sort-order.sync="sortation.order"
                        :md-sort-fn="customSort"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-toolbar>
                            <md-field>
                                <md-button class="md-dense md-raised md-info" @click="getList">جستجو</md-button>
                                <md-button class="md-dense md-raised md-primary" to="/user/create">افزودن کاربر</md-button>
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
                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="نام و نام خانوادگی" md-sort-by="name">{{item.f_name}}
                                {{item.l_name}}
                            </md-table-cell>
                            <md-table-cell md-label="کد ملی" md-sort-by="email">{{item.SSN}}</md-table-cell>
                            <md-table-cell md-label="شماره همراه" md-sort-by="email">{{item.mobile}}</md-table-cell>
                            <md-table-cell md-label="تلفن ثابت" md-sort-by="email">{{item.phone}}</md-table-cell>
                            <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                                {{item.shamsiDate('created_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <router-link :to="'/user/'+item.id">
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
                    <loading :active.sync="users.loading" :is-full-page="false"></loading>
                </md-card-content>
                <md-card-actions v-if="users.paginate" md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            نمایش
                            {{ users.paginate.from }}
                            تا
                            {{ users.paginate.to }}
                            از
                            {{ users.paginate.total }}
                            مورد
                        </p>
                    </div>
                    <paginate
                        v-model="users.paginate.current_page"
                        :page-count="users.paginate.last_page"
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

    import {UserList} from '@/models/User';
    import { Company, CompanyList} from '@/models/Company';
    import {UserStatusList} from '@/models/UserStatus';
    import Pagination from '@/components/Pagination';
    import {UserStatus} from "../../../models/UserStatus";

    export default {
        watch: {
            'filterData.perPage' : function () {
                this.getList()
            }
        },
        components: {
            "pagination": Pagination
        },
        data: () => ({
            users: new UserList(),
            companies: new CompanyList(),
            userStatuses: new UserStatusList(),
            filterData: {
                perPage: 10,
                perPageOptions: [5, 10, 25, 50, 100, 200, 300, 500],
                f_name: null,
                l_name: null,
                phone: null,
                SSN: null,
                status_id: null,
                company_id: null,
                createdSinceDate: null,
                createdTillDate: null
            },
            table: [],
            footerTable: ["Name", "Email", "Created At", "Actions"],

            query: null,

            sortation: {
                field: "created_at",
                order: "asc"
            },

            total: 1

        }),
        computed: {

            sort() {
                if (this.sortation.order === "desc") {
                    return `-${this.sortation.field}`
                }

                return this.sortation.field;
            },

            from() {
                return this.pagination.perPage * (this.pagination.currentPage - 1);
            },

            to() {
                let highBound = this.from + this.pagination.perPage;
                if (this.total < highBound) {
                    highBound = this.total;
                }
                return highBound;
            },

        },
        mounted() {
            this.getList()
            this.getCompanies()
            this.getUserStatus()
        },
        methods: {
            clickCallback (data) {
                this.getList(data)
            },
            getList (page) {
                if (!page) {
                    page = 1
                }
                this.users.loading = true;
                this.users.fetch({
                    page,
                    length: this.filterData.perPage,
                    f_name: this.filterData.f_name,
                    l_name: this.filterData.l_name,
                    phone: this.filterData.phone,
                    SSN: this.filterData.SSN,
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
                        this.users.loading = false
                        this.users = new UserList()
                    })
            },
            getUserStatus () {
                this.userStatuses.loading = true;
                this.userStatuses.fetch()
                    .then((response) => {
                        this.userStatuses.loading = false;
                        this.userStatuses = new UserStatusList(response.data)
                        this.userStatuses.addItem(new UserStatus({id: 0, displayName: ''}))
                    })
                    .catch((error) => {
                        this.userStatuses.loading = false;
                        this.userStatuses = new UserStatusList()
                    })
            },
            getCompanies () {
                this.companies.loading = true;
                this.companies.fetch()
                    .then((response) => {
                        this.companies.loading = false;
                        this.companies = new CompanyList(response.data)
                        this.companies.addItem(new Company({id: 0, name: ''}))
                    })
                    .catch((error) => {
                        this.companies.loading = false;
                        this.companies = new CompanyList()
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
                        this.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'کاربر با موفقیت حذف شد'
                        });
                        that.getList()
                    })
                    .catch(function(error) {
                        this.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        item.editMode = false;
                        item.loading = false;
                    });
            },
            onProFeature() {
                this.$store.dispatch("alerts/error", "This is a PRO feature.")
            },

            customSort() {
                return false
            }

        }
    }

</script>
