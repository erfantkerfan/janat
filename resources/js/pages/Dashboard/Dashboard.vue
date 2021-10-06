<template>
    <div v-if="!dashboardLoading" class="md-layout">
        <div v-if="LoggedInUser.hasSuperAdminRole()" class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
            <stats-card header-color="blue">
                <template slot="header">
                    <div class="card-icon">
                        <md-icon>group</md-icon>
                    </div>
                    <p class="category">تعداد کاربران</p>
                    <h3 class="title">
                        {{ counts.users }}
                    </h3>
                </template>
                <template v-if="false" slot="footer">
                    <div class="stats">
                        <md-icon>update</md-icon>
                        Just Updated
                    </div>
                </template>
            </stats-card>
        </div>
        <div v-if="LoggedInUser.hasSuperAdminRole()" class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
            <stats-card header-color="rose">
                <template slot="header">
                    <div class="card-icon">
                        <md-icon>account_balance</md-icon>
                    </div>
                    <p class="category">
                        صندوق ها
                    </p>
                    <h3 class="title">
                        {{ counts.funds }}
                    </h3>
                </template>

                <template v-if="false" slot="footer">
                    <div class="stats">
                        <md-icon>local_offer</md-icon>
                        Tracked from Google Analytics
                    </div>
                </template>
            </stats-card>
        </div>
        <div v-if="LoggedInUser.hasSuperAdminRole()" class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
            <stats-card header-color="green">
                <template slot="header">
                    <div class="card-icon">
                        <md-icon>monetization_on</md-icon>
                    </div>
                    <p class="category">
                        وام ها
                    </p>
                    <h3 class="title">
                        {{ counts.loans }}
                    </h3>
                </template>

                <template v-if="false" slot="footer">
                    <div class="stats">
                        <md-icon>date_range</md-icon>
                        Last 24 Hours
                    </div>
                </template>
            </stats-card>
        </div>
        <div v-if="LoggedInUser.hasSuperAdminRole()" class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25">
            <stats-card header-color="warning">
                <template slot="header">
                    <div class="card-icon">
                        <md-icon>store_mall_directory</md-icon>
                    </div>
                    <p class="category">
                        شرکت ها
                    </p>
                    <h3 class="title">
                        {{ counts.companies }}
                    </h3>
                </template>

                <template v-if="false" slot="footer">
                    <div class="stats">
                        <md-icon class="text-danger">warning</md-icon>
                        <a href="#pablo">Get More Space...</a>
                    </div>
                </template>
            </stats-card>
        </div>

        <div v-if="LoggedInUser.hasSuperAdminRole()" class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-rose">
                    <div class="card-icon">
                        <md-icon>account_balance</md-icon>
                    </div>
                    <h4 class="title">
                        گزارش کلی صندوق ها
                    </h4>
                </md-card-header>
                <md-card-content>
                    <md-table v-model="funds.list" table-header-color="green">
                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="#">{{ item.id }}</md-table-cell>
                            <md-table-cell md-label="نام">{{ item.name }}</md-table-cell>
                            <md-table-cell :md-label="'موجودی'+'('+currencyUnit+')'">{{ item.balance | currencyFormat }}</md-table-cell>
                            <md-table-cell :md-label="'درآمد کل'+'('+currencyUnit+')'">{{ item.incomes.sum_of_all | currencyFormat }}</md-table-cell>
                            <md-table-cell :md-label="'هزینه ها'+'('+currencyUnit+')'">{{ item.expenses | currencyFormat }}</md-table-cell>
                            <md-table-cell :md-label="'طلب ها'+'('+currencyUnit+')'">{{ item.demands | currencyFormat }}</md-table-cell>
                            <md-table-cell :md-label="'سرمایه کل'+'('+currencyUnit+')'">{{ item.capital | currencyFormat }}</md-table-cell>
                        </md-table-row>
                    </md-table>
                </md-card-content>
            </md-card>
        </div>

        <div v-if="false"
            class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100"
        >
            <chart-card
                :chart-data="fundsChart.data"
                :chart-options="fundsChart.options"
                :chart-responsive-options="fundsChart.responsiveOptions"
                chart-type="Bar"
                chart-inside-header
                background-color="rose"
            >
                <md-icon slot="fixed-button">build</md-icon>
                <md-button class="md-simple md-info md-just-icon" slot="first-button">
                    <md-icon>refresh</md-icon>
                    <md-tooltip md-direction="bottom">Refresh</md-tooltip>
                </md-button>
                <md-button class="md-simple md-just-icon" slot="second-button">
                    <md-icon>edit</md-icon>
                    <md-tooltip md-direction="bottom">Change Date</md-tooltip>
                </md-button>

                <template slot="content">
                    <h4 class="title">موجودی صندوق ها</h4>
                </template>
            </chart-card>
        </div>
        <div v-if="false"
            class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-33"
        >
            <chart-card
                :chart-data="dailySalesChart.data"
                :chart-options="dailySalesChart.options"
                chart-type="Line"
                chart-inside-header
                background-color="green"
            >
                <md-button class="md-simple md-info md-just-icon" slot="first-button">
                    <md-icon>refresh</md-icon>
                    <md-tooltip md-direction="bottom">Refresh</md-tooltip>
                </md-button>
                <md-button class="md-simple md-just-icon" slot="second-button">
                    <md-icon>edit</md-icon>
                    <md-tooltip md-direction="bottom">Change Date</md-tooltip>
                </md-button>

                <template slot="content">
                    <h4 class="title">Daily Sales</h4>
                    <p class="category">
            <span class="text-success"
            ><i class="fas fa-long-arrow-alt-up"></i>
              55%
            </span>
                        increase in today sales.
                    </p>
                </template>

                <template slot="footer">
                    <div class="stats">
                        <md-icon>access_time</md-icon>
                        updated 4 minutes ago
                    </div>
                </template>
            </chart-card>
        </div>
        <div v-if="false"
            class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-33"
        >
            <chart-card
                :chart-data="dataCompletedTasksChart.data"
                :chart-options="dataCompletedTasksChart.options"
                chart-type="Line"
                chart-inside-header
                background-color="blue"
            >
                <md-button class="md-simple md-info md-just-icon" slot="first-button">
                    <md-icon>refresh</md-icon>
                    <md-tooltip md-direction="bottom">Refresh</md-tooltip>
                </md-button>
                <md-button class="md-simple md-just-icon" slot="second-button">
                    <md-icon>edit</md-icon>
                    <md-tooltip md-direction="bottom">Change Date</md-tooltip>
                </md-button>

                <template slot="content">
                    <h4 class="title">Completed Tasks</h4>
                    <p class="category">
                        Last Campaign Performance
                    </p>
                </template>

                <template slot="footer">
                    <div class="stats">
                        <md-icon>access_time</md-icon>
                        campaign sent 26 minutes ago
                    </div>
                </template>
            </chart-card>
        </div>

        <div v-if="false" class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-50">
            <md-card>
                <md-card-header class="md-card-header-text md-card-header-warning">
                    <div class="card-text">
                        <h4 class="title">Employees Stats</h4>
                        <p class="category">
                            New employees on 15th September, 2016
                        </p>
                    </div>
                </md-card-header>
                <md-card-content>
                    <md-table v-model="users" table-header-color="orange">
                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="Id">{{ item.id }}</md-table-cell>
                            <md-table-cell md-label="Name">{{ item.name }}</md-table-cell>
                            <md-table-cell md-label="Salary">{{ item.salary }}</md-table-cell>
                            <md-table-cell md-label="Country">{{ item.country }}</md-table-cell>
                        </md-table-row>
                    </md-table>
                </md-card-content>
            </md-card>
        </div>

        <div v-if="false" class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-50">
            <nav-tabs-card>
                <template slot="content">
                    <span class="md-nav-tabs-title">Tasks</span>
                    <md-tabs md-sync-route class="md-rose" md-alignment="left">
                        <md-tab id="tab-home" md-label="Bugs" md-icon="bug_report">
                            <md-table v-model="firstTabs" @md-selected="onSelect">
                                <md-table-row
                                    slot="md-table-row"
                                    slot-scope="{ item }"
                                    md-selectable="multiple"
                                    md-auto-select
                                >
                                    <md-table-cell>{{ item.tab }}</md-table-cell>
                                    <md-table-cell>
                                        <md-button class="md-just-icon md-simple md-primary">
                                            <md-icon>edit</md-icon>
                                            <md-tooltip md-direction="top">Edit</md-tooltip>
                                        </md-button>
                                        <md-button class="md-just-icon md-simple md-danger">
                                            <md-icon>close</md-icon>
                                            <md-tooltip md-direction="top">Close</md-tooltip>
                                        </md-button>
                                    </md-table-cell>
                                </md-table-row>
                            </md-table>
                        </md-tab>

                        <md-tab id="tab-pages" md-label="Website" md-icon="code">
                            <md-table v-model="secondTabs" @md-selected="onSelect">
                                <md-table-row
                                    slot="md-table-row"
                                    slot-scope="{ item }"
                                    md-selectable="multiple"
                                    md-auto-select
                                >
                                    <md-table-cell>{{ item.tab }}</md-table-cell>
                                    <md-table-cell>
                                        <md-button class="md-just-icon md-simple md-primary">
                                            <md-icon>edit</md-icon>
                                            <md-tooltip md-direction="top">Edit</md-tooltip>
                                        </md-button>
                                        <md-button class="md-just-icon md-simple md-danger">
                                            <md-icon>close</md-icon>
                                            <md-tooltip md-direction="top">Close</md-tooltip>
                                        </md-button>
                                    </md-table-cell>
                                </md-table-row>
                            </md-table>
                        </md-tab>

                        <md-tab id="tab-posts" md-label="Server" md-icon="cloud">
                            <md-table v-model="thirdTabs" @md-selected="onSelect">
                                <md-table-row
                                    slot="md-table-row"
                                    slot-scope="{ item }"
                                    md-selectable="multiple"
                                    md-auto-select
                                >
                                    <md-table-cell>{{ item.tab }}</md-table-cell>
                                    <md-table-cell>
                                        <md-button class="md-just-icon md-simple md-primary">
                                            <md-icon>edit</md-icon>
                                            <md-tooltip md-direction="top">Edit</md-tooltip>
                                        </md-button>
                                        <md-button class="md-just-icon md-simple md-danger">
                                            <md-icon>close</md-icon>
                                            <md-tooltip md-direction="top">Close</md-tooltip>
                                        </md-button>
                                    </md-table-cell>
                                </md-table-row>
                            </md-table>
                        </md-tab>

                    </md-tabs>
                </template>
            </nav-tabs-card>
        </div>

    </div>
</template>

<script>

    import Vue from 'vue'
    import {
        StatsCard,
        ChartCard,
        NavTabsCard
    } from "@/components";
    import {userMixin, priceFilterMixin} from "@/mixins/Mixins";
    import {FundList} from "@/models/Fund";

    export default {
        components: {
            StatsCard,
            ChartCard,
            NavTabsCard
        },
        mixins: [userMixin, priceFilterMixin],
        data() {
            return {

                funds: new FundList(),
                tableData: [
                    {
                        id: 1,
                        name: "Dakota Rice",
                        salary: "$36.738",
                        country: "Niger",
                        city: "Oud-Turnhout"
                    },
                    {
                        id: 2,
                        name: "Minerva Hooper",
                        salary: "$23,789",
                        country: "Curaçao",
                        city: "Sinaai-Waas"
                    },
                    {
                        id: 3,
                        name: "Sage Rodriguez",
                        salary: "$56,142",
                        country: "Netherlands",
                        city: "Baileux"
                    },
                    {
                        id: 4,
                        name: "Philip Chaney",
                        salary: "$38,735",
                        country: "Korea, South",
                        city: "Overland Park"
                    },
                    {
                        id: 5,
                        name: "Doris Greene",
                        salary: "$63,542",
                        country: "Malawi",
                        city: "Feldkirchen in Kärnten"
                    }
                ],

                product1: "/img/card-2.jpg",
                product2: "/img/card-3.jpg",
                product3: "/img/card-1.jpg",
                seq2: 0,

                selected: [],
                firstTabs: [
                    {tab: "Sign contract for \"What are conference organizers afraid of?\""},
                    {tab: "Lines From Great Russian Literature? Or E-mails From My Boss?"},
                    {tab: "Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit"},
                    {tab: "Create 4 Invisible User Experiences you Never Knew About"}
                ],
                secondTabs: [
                    {
                        tab:
                            "Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit"
                    },
                    {
                        tab:
                            "Sign contract for \"What are conference organizers afraid of?\""
                    }
                ],
                thirdTabs: [
                    {
                        tab:
                            "Lines From Great Russian Literature? Or E-mails From My Boss?"
                    },
                    {
                        tab:
                            "Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit"
                    },
                    {
                        tab:
                            "Sign contract for \"What are conference organizers afraid of?\""
                    }
                ],

                users: [
                    {
                        id: 1,
                        name: "Noelia O'Kon",
                        salary: "13098.00",
                        country: "Niger"
                    },
                    {
                        id: 2,
                        name: "Mr. Enid Von PhD",
                        salary: "35978.00",
                        country: "Curaçao"
                    },
                    {
                        id: 3,
                        name: "Colton Koch",
                        salary: "26278.00",
                        country: "Netherlands"
                    },
                    {
                        id: 4,
                        name: "Gregory Vandervort",
                        salary: "25537.00",
                        country: "South Korea"
                    },
                ],
                dailySalesChart: {
                    data: {
                        labels: ["M", "T", "W", "T", "F", "S", "S"],
                        series: [[12, 17, 7, 17, 23, 18, 38]]
                    },
                    options: {
                        lineSmooth: this.$Chartist.Interpolation.cardinal({
                            tension: 0
                        }),
                        low: 0,
                        high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                        chartPadding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 0
                        }
                    }
                },
                dataCompletedTasksChart: {
                    data: {
                        labels: ["12am", "3pm", "6pm", "9pm", "12pm", "3am", "6am", "9am"],
                        series: [[230, 750, 450, 300, 280, 240, 200, 190]]
                    },

                    options: {
                        lineSmooth: this.$Chartist.Interpolation.cardinal({
                            tension: 0
                        }),
                        low: 0,
                        high: 1000, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                        chartPadding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 0
                        }
                    }
                },
                dashboardLoading: false,
                counts: {
                    users: 0,
                    funds: 0,
                    companies: 0,
                    loans: 0
                },
                fundsChart: {
                    data: {
                        labels: [],
                        series: [[]]
                    },
                    options: {
                        axisX: {
                            showGrid: false
                        },
                        low: 0,
                        // high: 1000,
                        chartPadding: {
                            top: 15,
                            right: 5,
                            bottom: 5,
                            left: 5
                        }
                    },
                    responsiveOptions: [
                        [
                            "screen and (max-width: 640px)",
                            {
                                seriesBarDistance: 5,
                                axisX: {
                                    labelInterpolationFnc: function (value) {
                                        return value[0];
                                    }
                                }
                            }
                        ]
                    ]
                }
            };
        },
        mounted() {
            this.getAllData()
        },
        methods: {
            getAllData() {
                this.dashboardLoading = true
                axios.get('/api/dashboard')
                    .then((response) => {
                        this.loadnFundsChart(response.data.funds)
                        this.loadFundsData(response.data.funds)
                        this.loadCounts(response.data.counts)
                        this.dashboardLoading = false
                    })
                    .catch(() => {

                    })
            },
            loadCounts (counts) {
                Vue.set(this.counts, 'users', counts.users)
                Vue.set(this.counts, 'funds', counts.funds)
                Vue.set(this.counts, 'companies', counts.companies)
                Vue.set(this.counts, 'loans', counts.loans)
            },
            loadFundsData (fundsData) {
                this.funds = new FundList(fundsData)
                this.funds.calcCapitals()
                const sumOfAll = this.funds.sumOfAll()
                this.funds.add({
                    id: '',
                    name: 'مجموع',
                    incomes: {
                        sum_of_all: sumOfAll.incomes
                    },
                    capital: sumOfAll.capital,
                    expenses: sumOfAll.expenses,
                    balance: sumOfAll.balance,
                    demands: sumOfAll.demands
                })
            },
            loadnFundsChart(funds) {
                let labels = []
                let series = [[]]
                funds.forEach((item) => {
                    labels.push(item.name)
                    series[0].push(item.balance)
                })
                Vue.set(this.fundsChart.data, 'labels', labels)
                Vue.set(this.fundsChart.data, 'series', series)
            },
            onSelect: function (items) {
                this.selected = items;
            }
        }
    };
</script>
