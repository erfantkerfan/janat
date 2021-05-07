<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-small-size-100">
            <div class="md-layout-item md-size-100">
                <md-card>
                    <md-card-header class="md-card-header-icon md-card-header-green">
                        <div class="card-icon">
                            <md-icon>credit_card</md-icon>
                        </div>
                        <h4 class="title">
                            اطلاعات تراکنش
                        </h4>
                    </md-card-header>
                    <md-card-content>
                        <price-input
                            v-model="transaction.cost"
                            :label="'مبلغ'"
                        />
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                توضیحات مدیر
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-textarea v-model="transaction.manager_comment" />
                                </md-field>
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                توضیحات کاربر
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-textarea v-model="transaction.user_comment" />
                                </md-field>
                            </div>
                        </div>
                        <md-field>
                            <label>وضعیت تراکنش</label>
                            <md-select v-model="transaction.transaction_status.id" name="pages">
                                <md-option
                                    v-for="item in transactionStatuses.list"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id"
                                >
                                    {{ item.display_name }}
                                </md-option>
                            </md-select>
                        </md-field>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                مهلت پرداخت
                            </label>
                            <div class="md-layout-item">
                                <date-picker
                                    v-model="transaction.deadline_at"
                                    type="datetime"
                                    :editable="true"
                                    format="YYYY-MM-DD HH:mm:ss"
                                    display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                            </div>
                        </div>
                        <div class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تاریخ پرداخت
                            </label>
                            <div class="md-layout-item">
                                <date-picker
                                    v-model="transaction.paid_at"
                                    type="datetime"
                                    :editable="true"
                                    format="YYYY-MM-DD HH:mm:ss"
                                    display-format="dddd jDD jMMMM jYYYY ساعت HH:mm" />
                            </div>
                        </div>
                        <div v-if="!isCreateForm()" class="md-layout">
                            <label class="md-layout-item md-size-15 md-form-label">
                                تاریخ ایجاد
                            </label>
                            <div class="md-layout-item">
                                <md-field class="md-invalid">
                                    <md-input v-model="transaction.shamsiDate('created_at').date" :disabled="true"/>
                                </md-field>
                            </div>
                        </div>
                        <loading :active.sync="transaction.loading" :is-full-page="false"></loading>
                    </md-card-content>
                    <md-card-actions>
                        <md-button class="md-info" @click="updateTransaction">
                            ذخیره اطلاعات
                        </md-button>
                        <md-button class="md-danger" @click="confirmRemove">
                            حذف
                        </md-button>
                    </md-card-actions>
                </md-card>

                <md-card v-if="transaction.related_payers">
                    <md-card-header class="md-card-header-text md-card-header-warning">
                        <div class="card-text">
                            <h4 class="title">پرداخت کنندگان</h4>
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <md-table :value="transaction.related_payers"
                                  table-header-color="green"
                                  class="table-hover">
                            <md-table-row slot="md-table-row" slot-scope="{ item }">
                                <md-table-cell md-label="نوع پرداخت کننده">{{ transaction.getRelatedModelType(item.transaction_payers_type) }}</md-table-cell>
                                <md-table-cell md-label="اطلاعات پرداخت کننده">{{ transaction.getRelatedModelLabel(item.transaction_payers_type, item.transaction_payers) }}</md-table-cell>
                                <md-table-cell md-label="مشاهده">
                                    <md-button
                                        :to="transaction.getRelatedModelRoute(item.transaction_payers_type, item.transaction_payers)"
                                        class="md-icon-button md-raised md-round md-info"
                                        style="margin: .2rem;"
                                    >
                                        <md-icon>pageview</md-icon>
                                    </md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                    </md-card-content>
                </md-card>

                <md-card v-if="transaction.related_recipients">
                    <md-card-header class="md-card-header-text md-card-header-blue">
                        <div class="card-text">
                            <h4 class="title">دریافت کنندگان</h4>
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <md-table :value="transaction.related_recipients"
                                  table-header-color="green"
                                  class="table-hover">
                            <md-table-row slot="md-table-row" slot-scope="{ item }">
                                <md-table-cell md-label="نوع دریافت کننده">{{ transaction.getRelatedModelType(item.transaction_recipients_type) }}</md-table-cell>
                                <md-table-cell md-label="اطلاعات دریافت کننده">{{ transaction.getRelatedModelLabel(item.transaction_recipients_type, item.transaction_recipients) }}</md-table-cell>
                                <md-table-cell md-label="مشاهده">
                                    <md-button
                                        :to="transaction.getRelatedModelRoute(item.transaction_recipients_type, item.transaction_recipients)"
                                        class="md-icon-button md-raised md-round md-info"
                                        style="margin: .2rem;"
                                    >
                                        <md-icon>pageview</md-icon>
                                    </md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                    </md-card-content>
                </md-card>

                <md-card>
                    <md-card-header class="md-card-header-text md-card-header-blue">
                        <div class="card-text">
                            <h4 class="title">ضمیمه ها</h4>
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <md-button class="md-info" @click="$refs.userProfilePic.click()">
                            افزودن تصویر
                        </md-button>
                        <img class="img" :src="cardUserImage"/>
                        <div v-if="cardUserNewImage !== null">
                            <md-button @click="clearUserPicBuffer" class="md-icon-button md-warning">
                                <md-icon>clear</md-icon>
                            </md-button>
                            <md-button @click="updateUserPic" class="md-icon-button md-success">
                                <md-icon>check</md-icon>
                            </md-button>
                        </div>
                        <input v-show="false"
                               type="file"
                               ref="userProfilePic"
                               @change="bufferUserPic($event)"/>

                        <md-button class="md-info" @click="getPics">
                            گرفتن تصاویر
                        </md-button>
                        <div v-for="pic in transactionPictures">
                            <img class="attached_picture" :src="pic">
                            <hr>
                            <hr>
                            <hr>
                        </div>
                    </md-card-content>
                </md-card>

                <vue-confirm-dialog></vue-confirm-dialog>
            </div>
        </div>
    </div>
</template>

<script>
    import { Transaction } from '@/models/Transaction'
    import PriceInput from '@/components/PriceInput'
    import { priceFilterMixin, getFilterDropdownMixin, axiosMixin } from '@/mixins/Mixins'

    export default {
        watch: {
            'transaction.transaction_status.id': function () {
                this.transaction.transaction_statusـid = this.transaction.transaction_status.id
            }
        },
        components: {PriceInput},
        mixins: [getFilterDropdownMixin, priceFilterMixin, axiosMixin],
        data: () => ({
            cardUserImage: '',
            cardUserNewImage: null,
            transactionPictures: [],

            transaction: new Transaction(),
            sortation: {
                field: "created_at",
                order: "asc"
            },
        }),
        mounted() {
            this.getData()
            this.getTransactionStatus()
        },
        methods: {
            isCreateForm () {
                return (this.$route.name === 'Transaction.Create')
            },
            getData () {
                if (this.isCreateForm()) {
                    return false
                }
                this.transaction.loading = true;
                this.transaction.show(this.$route.params.id)
                    .then((response) => {
                        this.transaction.loading = false;
                        this.transaction = new Transaction(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.transaction.loading = false;
                        this.transaction = new Transaction()
                    })
            },
            updateTransaction () {
                if (this.isCreateForm()) {
                    this.createTransaction()
                    return
                }
                let that = this
                this.transaction.loading = true;
                this.transaction.update()
                    .then(() => {
                        that.transaction.loading = false;
                        // that.transaction = new Transaction(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات با موفقیت ویرایش شد'
                        });
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.transaction.loading = false;
                        that.transaction = new Transaction()
                    })
            },
            createTransaction () {
                let that = this
                this.transaction.loading = true
                delete this.transaction.created_at
                delete this.transaction.updated_at
                this.transaction.create()
                    .then((response) => {
                        that.transaction.loading = false;
                        that.transaction = new Transaction(response.data)
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'اطلاعات صندوق با موفقیت ثبت شد'
                        });
                        that.$router.push({ path: '/allocated_loan/'+that.transaction.id })
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        that.transaction.loading = false;
                        that.transaction = new Transaction()
                    })
            },
            confirmRemove() {
                this.$confirm(
                    {
                        message: `از حذف تراکنش اطمینان دارید؟`,
                        button: {
                            no: 'خیر',
                            yes: 'بله'
                        },
                        callback: confirm => {
                            if (confirm) {
                                this.remove()
                            }
                        }
                    }
                )
            },
            remove() {
                let that = this;
                that.transaction.loading = true;
                that.transaction.delete()
                    .then(function() {
                        that.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'تراکنش با موفقیت حذف شد'
                        })
                        that.$router.go(-1)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        that.transaction.editMode = false
                        that.transaction.loading = false
                    });
            },


            getPics () {
                this.transaction.loading = true;
                this.transaction.getPictures()
                    .then((response) => {
                        console.log('response', response)

                        this.transactionPictures = response.data

                        this.transaction.loading = false;
                        this.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'تصاویر تراکنش با موفقیت دریافت شد'
                        });
                        // this.refreshAuthenticatedUserDataIfNeed()
                        // this.$refs.userProfileCard.clearUserPicBuffer(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.transaction.loading = false
                    })
            },
            updateUserPic() {
                this.transaction.loading = true;
                this.transaction.addPicture(this.cardUserNewImage)
                    .then((response) => {
                        console.log('response', response)
                        this.transaction.loading = false;
                        this.$store.dispatch('alerts/fire', {
                            icon: 'success',
                            title: 'توجه',
                            message: 'تصویر با موفقیت به تراکنش ضمیمه شد.'
                        });
                        // this.refreshAuthenticatedUserDataIfNeed()
                        // this.$refs.userProfileCard.clearUserPicBuffer(response.data)
                    })
                    .catch((error) => {
                        this.axios_handleError(error)
                        this.transaction.loading = false
                    })
            },
            clearUserPicBuffer(newUserPic) {
                this.cardUserNewImage = null
                if (!newUserPic) {
                    newUserPic = this.authenticatedUser.user_pic
                }
                this.cardUserImage = newUserPic
            },
            bufferUserPic($event) {
                const toBase64 = file => new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = error => reject(error);
                });

                let that = this
                toBase64($event.target.files[0])
                    .then((result) => {
                        that.cardUserNewImage = $event.target.files[0]
                        that.cardUserImage = result
                    })
                    .catch((error) => {
                        that.axios_handleError(error)
                        // that.clearUserPicBuffer()
                    })
            },
        }
    }
</script>
