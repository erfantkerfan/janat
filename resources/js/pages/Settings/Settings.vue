<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-100">
            <div class="md-layout-item md-size-100">
                <md-card>
                    <md-card-header class="md-card-header-icon md-card-header-green">
                        <div class="card-icon">
                            <md-icon>settings</md-icon>
                        </div>
                        <h4 class="title">
                            تنظیمات
                        </h4>
                    </md-card-header>
                    <md-card-content>
                        <div v-for="item in settings.list"
                             :key="item.id"
                             class="md-layout"
                        >
                            <label class="md-layout-item md-size-35 md-form-label">
                                {{ item.display_name }}
                                <br>
                                <md-button v-if="!item.editMode"
                                           @click="setSettingEditMode(item, true)"
                                           class="md-icon-button md-raised md-round md-info"
                                >
                                    <md-icon>edit</md-icon>
                                </md-button>
                                <md-button v-if="item.editMode"
                                           @click="setSettingEditMode(item, false)"
                                           class="md-icon-button md-raised md-round md-info"
                                >
                                    <md-icon>cancel</md-icon>
                                </md-button>
                                <md-button v-if="item.editMode"
                                           @click="updateSetting(item)"
                                           class="md-icon-button md-raised md-round md-info"
                                >
                                    <md-icon>save</md-icon>
                                </md-button>
                            </label>
                            <div class="md-layout-item">
                                <md-field v-if="item.name === 'type_of_loan_interest_payment'">
                                    <label>صندوق:</label>
                                    <md-select v-model="item.value" :disabled="!item.editMode">
                                        <md-option
                                            :value="'paid_at_first'"
                                        >
                                            پرداخت کارمزد وام به صورت یکجا در ابتدای دریافت وام
                                        </md-option>
                                        <md-option
                                            :value="'monthly_payment'"
                                        >
                                            پرداخت کارمزد به صورت ماهانه
                                        </md-option>
                                    </md-select>
                                </md-field>
                                <price-input
                                    v-else-if="item.name === 'loan_interest_per_month'"
                                    v-model="item.value"
                                    :disabled="!item.editMode"
                                    :label="'کارمزد وام برای هر ماه قسط'"
                                />
                                <md-field v-else class="md-invalid">
                                    <md-input v-model="item.value" :disabled="!item.editMode"/>
                                </md-field>
                            </div>
                        </div>
                        <loading :active.sync="settings.loading" :is-full-page="false"></loading>
                    </md-card-content>
                </md-card>
            </div>
        </div>
        <div class="md-layout-item md-size-100">
            <fixed-plugin
                :color.sync="sidebarBackground"
                :colorBg.sync="sidebarBackgroundColor"
                :sidebarMini.sync="sidebarMini"
                :sidebarImg.sync="sidebarImg"
                :image.sync="sidebarBackgroundImage"
            >
            </fixed-plugin>
        </div>
    </div>
</template>

<script>
import FixedPlugin from '@/pages/FixedPlugin';
import PriceInput from '@/components/PriceInput'
import {priceFilterMixin, getFilterDropdownMixin, axiosMixin} from '@/mixins/Mixins'
import {Setting, SettingList} from "@/models/Setting";

export default {
    name: 'Settings',
    watch: {
        sidebarBackground() {
            window.localStorage.setItem('sidebarBackground', this.sidebarBackground)
            this.$store.commit('settings/update_SidebarBackground', this.sidebarBackground)
        },
        sidebarBackgroundColor() {
            window.localStorage.setItem('sidebarBackgroundColor', this.sidebarBackgroundColor)
            this.$store.commit('settings/update_SidebarBackgroundColor', this.sidebarBackgroundColor)
        },
        sidebarMini() {
            window.localStorage.setItem('sidebarMini', this.sidebarMini)
            this.$store.commit('settings/update_SidebarMini', this.sidebarMini)
        },
        sidebarImg() {
            window.localStorage.setItem('sidebarImg', this.sidebarImg)
            this.$store.commit('settings/update_SidebarImg', this.sidebarImg)
        },
        sidebarBackgroundImage() {
            window.localStorage.setItem('sidebarBackgroundImage', this.sidebarBackgroundImage)
            this.$store.commit('settings/update_SidebarBackgroundImage', this.sidebarBackgroundImage)
        },
        'loan.fund.id': function () {
            this.loan.fund_id = this.loan.fund.id
        }
    },
    components: {
        FixedPlugin,
        PriceInput
    },
    mixins: [priceFilterMixin, getFilterDropdownMixin, axiosMixin],
    computed: {
        currentSidebarMini() {
            const localStorage = window.localStorage.getItem('sidebarMini')
            if (!localStorage) {
                return this.$store.getters['settings/sidebarMini']
            }

            return (localStorage === 'true')
        },
        currentSidebarImg() {
            const localStorage = window.localStorage.getItem('sidebarImg')
            if (!localStorage) {
                return this.$store.getters['settings/sidebarImg']
            }

            return (localStorage === 'true')
        }
    },
    data: () => ({
        sidebarBackground: "green",
        sidebarBackgroundColor: "black",
        sidebarMini: true,
        sidebarImg: true,
        sidebarBackgroundImage: "/img/sidebar-2.jpg",

        setting: new Setting(),
        settings: new SettingList(),
    }),
    mounted() {
        this.getData()
        this.sidebarMini = this.currentSidebarMini
        this.sidebarImg = this.currentSidebarImg
    },
    methods: {
        getData() {
            this.settings.loading = true;
            this.settings.fetch()
                .then((response) => {
                    this.settings.loading = false
                    this.settings = new SettingList(response.data.data, response.data)
                    this.updateSettingsInStore()
                })
                .catch((error) => {
                    this.axios_handleError(error)
                    this.settings.loading = false
                    this.settings = new SettingList()
                })
        },
        setSettingEditMode(setting, editMode) {
            this.settings.setEditMode(false)
            setting.editMode = editMode
        },
        updateSetting(setting) {
            let that = this
            this.setting = new Setting(setting)
            this.setting.loading = true;
            this.setting.update()
                .then(() => {
                    that.setting.loading = false
                    that.getData()
                    that.$store.dispatch('alerts/fire', {
                        icon: 'success',
                        title: 'توجه',
                        message: 'اطلاعات با موفقیت ویرایش شد'
                    });
                })
                .catch((error) => {
                    that.getData()
                    that.axios_handleError(error)
                    that.setting.loading = false;
                })
        },
        updateSettingsInStore() {
            this.$store.commit('settings/SET_SETTINGS', this.settings)
        },
        createLoan() {
            let that = this
            this.loan.loading = true
            delete this.loan.created_at
            delete this.loan.updated_at
            this.loan.create()
                .then((response) => {
                    that.loan.loading = false;
                    that.loan = new Loan(response.data)
                    that.$store.dispatch('alerts/fire', {
                        icon: 'success',
                        title: 'توجه',
                        message: 'اطلاعات صندوق با موفقیت ثبت شد'
                    });
                    that.$router.push({path: '/loan/' + that.loan.id})
                })
                .catch((error) => {
                    that.axios_handleError(error)
                    that.loan.loading = false;
                })
        }
    }
}
</script>
