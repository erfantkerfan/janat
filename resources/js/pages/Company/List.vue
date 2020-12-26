<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>assignment</md-icon>
                    </div>
                    <h4 class="title">لیست شرکت ها</h4>
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
                                        <md-input v-model="filterData.name" />
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
                    <md-table
                        :value="companies.list"
                        :md-sort.sync="filterData.sortation.field"
                        :md-sort-order.sync="filterData.sortation.order"
                        :md-sort-fn="customSort"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-toolbar>
                            <md-field>
                                <md-button class="md-dense md-raised md-info" @click="getList">جستجو</md-button>
                                <md-button class="md-dense md-raised md-primary" to="/company/create">افزودن</md-button>
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
                            <md-table-cell md-label="نام شرکت" md-sort-by="name">
                                {{item.name}}
                            </md-table-cell>
                            <md-table-cell md-label="نام صندوق" md-sort-by="fund.name">
                                {{item.fund.name}}
                            </md-table-cell>
                            <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                                {{item.shamsiDate('created_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <router-link :to="'/company/'+item.id">
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
                    <loading :active.sync="companies.loading" :is-full-page="false"></loading>
                </md-card-content>
                <md-card-actions v-if="companies.paginate" md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            نمایش
                            {{ companies.paginate.from }}
                            تا
                            {{ companies.paginate.to }}
                            از
                            {{ companies.paginate.total }}
                            مورد
                        </p>
                    </div>
                    <paginate
                        v-model="companies.paginate.current_page"
                        :page-count="companies.paginate.last_page"
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

    import {Fund, FundList} from '@/models/Fund';
    import {CompanyList} from '@/models/Company';
    import Pagination from "@/components/Pagination";

    export default {
        name: "CompanyList",
        watch: {
            'filterData.perPage' : function () {
                this.getList()
            }
        },
        components: {
            "pagination": Pagination
        },
        data: () => ({
            companies: new CompanyList(),
            funds: new FundList(),
            filterData: {
                sortation: {
                    field: "created_at",
                    order: "asc"
                },
                perPage: 10,
                perPageOptions: [5, 10, 25, 50, 100, 200, 300, 500],
                name: null,
                fund_id: null,
            }
        }),
        mounted() {
            this.getList()
            this.getfunds()
        },
        methods: {
            clickCallback (data) {
                this.getList(data)
            },
            getList (page) {
                if (!page) {
                    page = 1
                }
                this.companies.loading = true;
                this.companies.fetch({
                    page,
                    sortation_field: this.filterData.sortation.field,
                    sortation_order: this.filterData.sortation.order,
                    length: this.filterData.perPage,
                    fund_id: (this.filterData.fund_id === null || this.filterData.fund_id === 0) ? null: this.filterData.fund_id,
                    name: this.filterData.name
                })
                    .then((response) => {
                        this.companies.loading = false
                        this.companies = new CompanyList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        this.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        this.companies.loading = false
                        this.companies = new CompanyList()
                    })
            },
            getfunds () {
                let that = this
                this.funds.loading = true;
                this.funds.fetch()
                    .then((response) => {
                        that.funds.loading = false;
                        that.funds = new FundList(response.data.data, response.data)
                        this.funds.addItem(new Fund({id: 0, name: ''}))
                    })
                    .catch((error) => {
                        this.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        that.funds.loading = false;
                        that.funds = new FundList()
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
