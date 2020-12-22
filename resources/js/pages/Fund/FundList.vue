<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>assignment</md-icon>
                    </div>
                    <h4 class="title">لیست صندوق ها</h4>
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
                            <div class="md-layout">
                                <label class="md-layout-item md-size-15 md-form-label">
                                    پرداخت ماهیانه
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="filterData.monthly_payment" />
                                    </md-field>
                                </div>
                            </div>
                        </div>
                    </div>
                    <md-table
                        :value="funds.list"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-toolbar>
                            <md-field>
                                <md-button class="md-dense md-raised md-info" @click="getList">جستجو</md-button>
                                <md-button class="md-dense md-raised md-primary" @click="onProFeature">افزودن صندوق</md-button>
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
                            <md-table-cell md-label="نام" md-sort-by="name">{{item.name}}
                                {{item.name}}
                            </md-table-cell>
                            <md-table-cell md-label="پرداخت ماهیانه" md-sort-by="email">
                                {{item.monthly_payment}}
                            </md-table-cell>
                            <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                                {{item.shamsiDate('created_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <router-link :to="'/fund/'+item.id">
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
                    <loading :active.sync="funds.loading" :is-full-page="false"></loading>
                </md-card-content>
                <md-card-actions v-if="funds.paginate" md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            نمایش
                            {{ funds.paginate.from }}
                            تا
                            {{ funds.paginate.to }}
                            از
                            {{ funds.paginate.total }}
                            مورد
                        </p>
                    </div>
                    <paginate
                        v-model="funds.paginate.current_page"
                        :page-count="funds.paginate.last_page"
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
    import {FundList} from '@/models/Fund';

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
            funds: new FundList(),
            filterData: {
                perPage: 10,
                perPageOptions: [5, 10, 25, 50, 100, 200, 300, 500],
                name: null,
                monthly_payment: null
            }
        }),

        computed: {
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
        },
        methods: {
            clickCallback (data) {
                this.getList(data)
            },
            getList (page) {
                if (!page) {
                    page = 1
                }
                this.funds.loading = true;
                this.funds.fetch({
                    page,
                    length: this.filterData.perPage,
                    name: this.filterData.name,
                    monthly_payment: this.filterData.monthly_payment
                })
                    .then((response) => {
                        this.funds.loading = false
                        this.funds = new FundList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        this.funds.loading = false
                        this.funds = new FundList()
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
                        // toastr.success('تیکت با موفقیت حذف شد.');
                        // that.removeTicketFromList(item);
                        that.getList()
                    })
                    .catch(function(error) {
                        // toastr.error('مشکلی رخ داده است.');
                        // Assist.handleErrorMessage(error);
                        item.editMode = false;
                        item.loading = false;
                    });
            },
            onProFeature() {
                this.$store.dispatch("alerts/error", "This is a PRO feature.")
            },

            // customSort() {
            //     return false
            // }

        }

    }

</script>
