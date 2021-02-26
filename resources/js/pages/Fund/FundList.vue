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
                        v-if="!funds.loading && funds.list.length === 0"
                        class="md-warning"
                        md-icon="cancel_presentation"
                        md-label="صندوقی یافت نشد"
                    >
                    </md-empty-state>

                    <md-table
                        :value="funds.list"
                        :md-sort.sync="filterData.sortation.field"
                        :md-sort-order.sync="filterData.sortation.order"
                        :md-sort-fn="customSort"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-toolbar>
                            <md-field>
                                <md-button class="md-dense md-raised md-info" @click="getList">جستجو</md-button>
                                <md-button class="md-dense md-raised md-primary" to="/fund/create">افزودن</md-button>
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
                        <md-table-row v-if="!funds.loading && funds.list.length > 0" slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="نام صندوق" md-sort-by="name">
                                {{item.name}}
                            </md-table-cell>
                            <md-table-cell :md-label="'موجودی '+'(' + currencyUnit + ')'" md-sort-by="balance">
                                {{ item.balance | currencyFormat }}
                            </md-table-cell>
                            <md-table-cell md-label="تاریخ ایجاد" md-sort-by="created_at">
                                {{item.shamsiDate('created_at').dateTime}}
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <md-button
                                    :to="'/fund/'+item.id"
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
                    <loading :active.sync="funds.loading" :is-full-page="false"></loading>
                </md-card-content>
                <list-pagination
                    :paginate="funds.paginate"
                    @changePage="clickCallback"
                />
            </md-card>
        </div>
    </div>
</template>

<script>
    import {FundList} from '@/models/Fund'
    import ListPagination from '@/components/ListPagination'
    import { priceFilterMixin, axiosMixin } from '@/mixins/Mixins'

    export default {
        watch: {
            'filterData.perPage' : function () {
                this.getList()
            }
        },
        mixins: [priceFilterMixin, axiosMixin],
        components: {
            ListPagination
        },
        data: () => ({
            funds: new FundList(),
            filterData: {
                sortation: {
                    field: 'created_at',
                    order: 'asc'
                },
                perPage: 10,
                perPageOptions: [5, 10, 25, 50, 100, 200, 300, 500],
                name: null,
                createdSinceDate: null,
                createdTillDate: null
            }
        }),
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
                    sortation_field: this.filterData.sortation.field,
                    sortation_order: this.filterData.sortation.order,
                    length: this.filterData.perPage,
                    name: this.filterData.name,
                    createdSinceDate: this.filterData.createdSinceDate,
                    createdTillDate: this.filterData.createdTillDate
                })
                    .then((response) => {
                        this.funds.loading = false
                        this.funds = new FundList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
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
