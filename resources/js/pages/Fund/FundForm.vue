<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-small-size-100">
            <div class="md-layout-item md-size-100">
                <md-card>
                    <md-card-header class="md-card-header-icon">
                        <div class="card-icon">
                            <md-icon>perm_identity</md-icon>
                        </div>
                        <h4 class="title">
                            ویرایش اطلاعات
                        </h4>
                    </md-card-header>

                    <md-card-content>

                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                نام صندوق
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="fund.name"/>
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                میزان شهریه
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="fund.monthly_payment"/>
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تاریخ تعریف صندوق
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="fund.shamsiDate('created_at').date" :disabled="true"/>
                                </md-field>
                            </div>
                        </div>

                        <loading :active.sync="fund.loading" :is-full-page="false"></loading>

                    </md-card-content>

                    <md-card-actions>
                        <md-button type="submit" @click="updateFund">
                            ذخیره اطلاعات
                        </md-button>
                    </md-card-actions>

                </md-card>
            </div>
        </div>
    </div>
</template>

<script>
    import {Fund} from '@/models/Fund';

    export default {
        name: "fund-form",
        data: () => ({
            fund: new Fund()
        }),
        mounted() {
            this.getData();
        },
        methods: {
            getData () {
                if (this.$route.name === 'Create') {
                    return false
                }
                this.fund.loading = true;
                this.fund.show(this.$route.params.id)
                    .then((response) => {
                        this.fund.loading = false;
                        this.fund = new Fund(response.data)
                    })
                    .catch((error) => {
                        this.fund.loading = false;
                        this.fund = new Fund()
                    })
            },
            updateFund () {
                if (this.$route.name === 'Create') {
                    this.createFund()
                    return
                }
                let that = this
                this.fund.loading = true;
                this.fund.update()
                    .then((response) => {
                        that.fund.loading = false;
                        that.fund = new Fund(response.data)
                    })
                    .catch((error) => {
                        that.fund.loading = false;
                        that.fund = new Fund()
                    })
            },
            createFund () {
                let that = this
                this.fund.loading = true
                delete this.fund.created_at
                delete this.fund.updated_at
                this.fund.create()
                    .then((response) => {
                        that.fund.loading = false;
                        that.fund = new Fund(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات صندوق با موفقیت ثبت شد'
                        });
                        that.$router.push({ path: '/fund/'+that.fund.id })
                    })
                    .catch((error) => {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        that.fund.loading = false;
                        that.fund = new Fund()
                    })
            }
        }
    }
</script>
