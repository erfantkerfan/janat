<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-small-size-100">
            <div class="md-layout-item md-size-100">
                <md-card>
                    <md-card-header class="md-card-header-icon md-card-header-green">
                        <div class="card-icon">
                            <md-icon>store_mall_directory</md-icon>
                        </div>
                        <h4 class="title">
                            ویرایش اطلاعات
                        </h4>
                    </md-card-header>

                    <md-card-content>

                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                نام شرکت
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="company.name"/>
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                نام مسئول
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="company.undertaker"/>
                                </md-field>
                            </div>
                        </div>
                        <div v-if="!isCreateForm()" class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تاریخ تعریف شرکت
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="company.shamsiDate('created_at').date" :disabled="true"/>
                                </md-field>
                            </div>
                        </div>

                        <loading :active.sync="company.loading || funds.loading" :is-full-page="false"></loading>

                    </md-card-content>

                    <md-card-actions>
                        <md-button type="submit"
                                   class="md-info"
                                   @click="updateCompany"
                        >
                            ذخیره اطلاعات
                        </md-button>
                        <md-button v-if="!isCreateForm()"
                                   type="submit"
                                   class="md-success"
                                   :to="{
                                        name: 'Company.AddPayment',
                                        params: {
                                            company_id: company.id,
                                            fund_id: company.fund.id
                                            }
                                        }"
                        >
                            ثبت واریز وجه به صندوق
                        </md-button>
                    </md-card-actions>

                </md-card>
            </div>
        </div>
    </div>
</template>

<script>
    import {FundList} from '@/models/Fund'
    import {Company} from '@/models/Company'
    import { axiosMixin } from '@/mixins/Mixins'

    export default {
        name: "CompanyForm",
        watch: {
            'company.fund.id': function () {
                this.company.fund_id = this.company.fund.id
            }
        },
        mixins: [axiosMixin],
        data: () => ({
            company: new Company(),
            funds: new FundList()
        }),
        mounted() {
            this.getData();
            this.getfunds();
        },
        methods: {
            isCreateForm () {
                return (this.$route.name === 'Company.Create')
            },
            getData () {
                if (this.isCreateForm()) {
                    return false
                }
                this.company.loading = true;
                this.company.show(this.$route.params.id)
                    .then((response) => {
                        this.company.loading = false;
                        this.company = new Company(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.company.loading = false;
                        this.company = new Company()
                    })
            },
            getfunds () {
                let that = this
                this.funds.loading = true;
                this.funds.fetch()
                    .then((response) => {
                        that.funds.loading = false;
                        that.funds = new FundList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.funds.loading = false;
                        that.funds = new FundList()
                    })
            },
            updateCompany () {
                if (this.isCreateForm()) {
                    this.createCompany()
                    return
                }
                let that = this
                this.company.loading = true;
                this.company.update()
                    .then((response) => {
                        that.company.loading = false;
                        that.company = new Company(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات با موفقیت ویرایش شد'
                        });
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.company.loading = false;
                        that.company = new Company()
                    })
            },
            createCompany () {
                let that = this
                this.company.loading = true
                delete this.company.created_at
                delete this.company.updated_at
                this.company.create()
                    .then((response) => {
                        that.company.loading = false;
                        that.company = new Company(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات صندوق با موفقیت ثبت شد'
                        });
                        that.$router.push({ path: '/company/'+that.company.id })
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.company.loading = false;
                        that.company = new Company()
                    })
            }
        }
    }
</script>
