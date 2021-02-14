<template>
    <div>
        <div v-for="account in user.accounts.list">
            <md-card v-if="account.allocated_loans.list.length > 0">
                <md-card-header class="md-card-header-text md-card-header-warning">
                    <div class="card-text">
                        <h4 class="title">نام صندوق: {{account.fund.name}}</h4>
                        <p class="category">
                            شماره حساب:
                            {{account.id}}
                            -
                            تاریخ عضویت:
                            {{account.shamsiDate('joined_at').date}}
                        </p>
                    </div>
                </md-card-header>
                <md-card-content>
                    <md-table v-model="account.allocated_loans.list" table-header-color="orange">
                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="نام وام">{{ item.loan.name }}</md-table-cell>
                            <md-table-cell md-label="مبلغ وام">{{ item.loan_amount | currencyFormat }}</md-table-cell>
                            <md-table-cell md-label="کل پرداختی">{{ item.total_payments | currencyFormat }}</md-table-cell>
                            <md-table-cell md-label="پرداختی باقیمانده">{{ item.remaining_payable_amount | currencyFormat }}</md-table-cell>
                            <md-table-cell md-label="تعداد کل اقساط">{{ item.number_of_installments }}</md-table-cell>
                            <md-table-cell md-label="تعداد اقساط پرداخت شده">{{ item.count_of_paid_installments }}</md-table-cell>
                            <md-table-cell md-label="تعداد اقساط باقیمانده">{{ item.count_of_remaining_installments }}</md-table-cell>
                            <md-table-cell md-label="وضعیت">
                                <span v-if="item.is_settled">تسویه شده</span>
                                <span v-else>تسویه نشده</span>
                            </md-table-cell>
                            <md-table-cell md-label="عملیات">
                                <router-link v-if="item.loan.id !== null" :to="'/allocated_loan/'+item.id">
                                    <md-button
                                        class="md-icon-button md-raised md-round md-info"
                                        style="margin: .2rem;"
                                    >
                                        <md-icon>edit</md-icon>
                                    </md-button>
                                </router-link>
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
                </md-card-content>
            </md-card>
        </div>
    </div>
</template>

<script>
    import {User} from '@/models/User';
    import { priceFilterMixin } from '@/mixins/Mixins'

    export default {
        name: 'Loans',
        mixins: [priceFilterMixin],
        props: {
            user: {
                type: User,
                default () {
                    return new User()
                }
            }
        }
    }
</script>

<style scoped>

</style>
