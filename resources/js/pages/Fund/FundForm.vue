<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-60 md-small-size-100">
            <div class="md-layout-item md-size-100">
                <md-card>
                    <md-card-header class="md-card-header-icon md-card-header-green">
                        <div class="card-icon">
                            <md-icon>account_balance</md-icon>
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
                        <div v-if="!isCreateForm()" class="md-layout">
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
        <div class="md-layout-item md-size-40 md-small-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-blue">
                    <div class="card-icon">
                        <md-icon>monetization_on</md-icon>
                    </div>
                    <h4 class="title">
                        وام های تعریف شده صندوق
                    </h4>
                </md-card-header>

                <md-card-content>
                    <md-empty-state
                        v-if="!loan.loading && loans.list.length === 0"
                        class="md-warning"
                        md-icon="cancel_presentation"
                        md-label="وامی یافت نشد"
                    >
                    </md-empty-state>
                    <md-table
                        v-if="loans.list.length > 0"
                        :value="loans.list"
                        class="paginated-table table-striped table-hover"
                    >
                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="نام" md-sort-by="name">{{item.name}}</md-table-cell>
                            <md-table-cell md-label="مبلغ وام" md-sort-by="email">{{item.loan_amount}}</md-table-cell>
                            <md-table-cell md-label="مبلغ هر قسط" md-sort-by="email">{{item.installment_rate}}</md-table-cell>
                            <md-table-cell md-label="تعداد اقساط" md-sort-by="email">{{item.number_of_installments}}</md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <md-button
                                    class="md-icon-button md-raised md-round md-info"
                                    style="margin: .2rem;"
                                    :to="'/loan/'+item.id"
                                >
                                    <md-icon>edit</md-icon>
                                </md-button>
                                <md-button
                                    class="md-icon-button md-raised md-round md-danger"
                                    style="margin: .2rem;"
                                    @click="confirmRemove(item)"
                                >
                                    <md-icon>delete</md-icon>
                                </md-button>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
                    <md-dialog :md-active.sync="createLoanShowDialog">
                        <md-dialog-title>ایجاد وام جدید</md-dialog-title>

                        <md-dialog-content>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    نام وام
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="loan.name"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <md-field>
                                    <label>نوع وام</label>
                                    <md-select v-model="loan.loan_type.id" name="pages">
                                        <md-option
                                            v-for="item in loanTypes.list"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.id"
                                        >
                                            {{ item.display_name }}
                                        </md-option>
                                    </md-select>
                                </md-field>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    مقدار وام
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="loan.loan_amount"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    مبلغ هر قسط
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="loan.installment_rate"/>
                                    </md-field>
                                </div>
                            </div>
                            <div class="md-layout">
                                <label class="md-layout-item md-size-35 md-form-label">
                                    تعداد اقساط
                                </label>
                                <div class="md-layout-item">
                                    <md-field class="md-invalid">
                                        <md-input v-model="loan.number_of_installments"/>
                                    </md-field>
                                </div>
                            </div>
                        </md-dialog-content>

                        <md-dialog-actions>
                            <md-button class="md-default" @click="closeLoanDialog">انتصراف</md-button>
                            <md-button v-if="!editLoanState" class="md-success" @click="createNewLoan">ذخیره</md-button>
                            <md-button v-else class="md-success" @click="editLoan">ذخیره</md-button>
                        </md-dialog-actions>
                    </md-dialog>
                    <loading :active.sync="loans.loading" :is-full-page="false"></loading>
                    <vue-confirm-dialog></vue-confirm-dialog>
                </md-card-content>

                <md-card-actions>
                    <md-button
                        class="md-dense md-raised md-primary"
                        @click="showDialog">
                        تعریف وام جدید
                    </md-button>
                </md-card-actions>

            </md-card>
        </div>
    </div>
</template>

<script>
    import {Fund} from '@/models/Fund';
    import {Loan, LoanList} from '@/models/Loan';
    import getFilterDropdownMixin from "@/mixins/getFilterDropdownMixin";

    export default {
        name: "fund-form",
        mixins: [getFilterDropdownMixin],
        data: () => ({
            fund: new Fund(),
            loan: new Loan(),
            loans: new LoanList(),
            editLoanState: false,
            createLoanShowDialog: false,
        }),
        mounted() {
            this.getData()
            this.getLoans()
            this.getLoanTypes()
        },
        methods: {
            isCreateForm () {
                return (this.$route.name === 'Fund.Create')
            },
            showDialog (item) {
                this.loan = new Loan(item)
                this.editLoanState = false
                this.createLoanShowDialog = true
            },
            closeLoanDialog () {
                this.createLoanShowDialog = false
            },
            createNewLoan () {
                let that = this
                this.loan.loading = true
                this.loan.fund.id = this.$route.params.id
                this.loan.create()
                    .then((response) => {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'وام جدید با موفقیت تعریف شد'
                        });
                        that.closeLoanDialog()
                        that.getLoans()
                    })
                    .catch((error) => {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        that.closeLoanDialog()
                    })
            },
            editLoan () {
                let that = this
                this.value.loading = true
                this.updateUserModel()
                this.newAccount.user_id = this.$route.params.id
                this.newAccount.update()
                    .then((response) => {
                        that.$emit('update', this.value)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'حساب با موفقیت ویرایش شد'
                        });
                        that.closeAccountDialog()
                    })
                    .catch((error) => {
                        that.$emit('update', this.value)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        that.closeAccountDialog()
                    })
            },
            confirmRemove(item) {
                let that = this
                this.$confirm(
                    {
                        message: `از حذف وام اطمینان دارید؟`,
                        button: {
                            no: 'خیر',
                            yes: 'بله'
                        },
                        callback: confirm => {
                            if (confirm) {
                                that.remove(item)
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
                            message: 'وام با موفقیت حذف شد'
                        })
                        that.getLoans()
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
            getData () {
                if (this.isCreateForm()) {
                    return false
                }
                this.fund.loading = true;
                this.fund.show(this.$route.params.id)
                    .then((response) => {
                        this.fund.loading = false;
                        this.fund = new Fund(response.data)
                    })
                    .catch((error) => {
                        this.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        this.fund.loading = false;
                        this.fund = new Fund()
                    })
            },
            getLoans () {
                let that = this
                this.loans.loading = true;
                this.loans.fetch({
                    fund_id: this.$route.params.id
                })
                    .then((response) => {
                        that.loans.loading = false;
                        that.loans = new LoanList(response.data.data, response.data)
                    })
                    .catch((error) => {
                        this.$store.dispatch('alerts/fire', {
                            icon: 'error',
                            title: 'توجه',
                            message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                        });
                        console.log('error: ', error)
                        that.loans.loading = false;
                        that.loans = new LoanList()
                    })
            },
            updateFund () {
                if (this.isCreateForm()) {
                    this.createFund()
                    return
                }
                let that = this
                this.fund.loading = true;
                this.fund.update()
                    .then((response) => {
                        that.fund.loading = false;
                        that.fund = new Fund(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات با موفقیت ویرایش شد'
                        });
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
