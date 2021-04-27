<template>
    <div
        class="wrapper"
        :class="[
          { 'nav-open': $sidebar.showSidebar },
          { rtl: $route.meta.rtlActive },
        ]"
    >
        <notifications></notifications>
        <side-bar
            :active-color="sidebarBackground"
            :background-image="sidebarBackgroundImage"
            :data-background-color="sidebarBackgroundColor"
        >
            <user-menu></user-menu>
            <mobile-menu v-if="false"></mobile-menu>

            <template slot="links">
                <sidebar-item
                    :link="{ name: 'پیشخوان', icon: 'dashboard', path: '/' }"
                />
                <sidebar-item v-if="LoggedInUser.hasSuperAdminRole()" :opened="false" :link="{ name: 'تعاریف اولیه', icon: 'layers' }">
                    <sidebar-item
                        :link="{
                        name: 'صندوق ها',
                        icon: 'account_balance',
                        path: '/fund/list',
                      }"
                    />
                    <sidebar-item
                        :link="{
                        name: 'وام ها',
                        icon: 'monetization_on',
                        path: '/loan/list',
                      }"
                    />
                    <sidebar-item
                        :link="{
                        name: 'شرکت ها',
                        icon: 'store_mall_directory',
                        path: '/company/list',
                      }"
                    />
                </sidebar-item>
                <sidebar-item v-if="LoggedInUser.hasSuperAdminRole()" :opened="false" :link="{ name: 'فرایند های دوره ای', icon: 'autorenew' }">
                    <sidebar-item
                        :link="{
                        name: 'پرداخت اقساط کسر از حقوق',
                        icon: 'account_balance',
                        path: '/periodic_processes/payment_of_payroll_deductions',
                      }"
                    />
                    <sidebar-item
                        :link="{
                        name: 'پرداخت ماهانه کسر از حقوق',
                        icon: 'class',
                        path: '/periodic_processes/pay_fund_monthly_payment_by_payroll_deduction',
                      }"
                    />
                </sidebar-item>
                <sidebar-item
                    v-if="LoggedInUser.hasSuperAdminRole()"
                    :link="{
                        name: 'مدیریت اعضا',
                        icon: 'people',
                        path: '/user/list',
                      }"
                />
                <sidebar-item
                    :link="{
                        name: 'وام های تخصیص داده شده',
                        icon: 'attach_money',
                        path: '/allocated_loan/list',
                      }"
                />
                <sidebar-item
                    :link="{
                        name: 'تراکنش ها',
                        icon: 'credit_card',
                        path: '/transactions/list',
                      }"
                />
                <sidebar-item
                    v-if="LoggedInUser.hasSuperAdminRole()"
                    :link="{
                        name: 'تنظیمات',
                        icon: 'settings',
                        path: '/setting',
                      }"
                />


                <sidebar-item
                    v-if="false"
                    :link="{
                        name: 'Table Lists',
                        icon: 'content_paste',
                        path: '/components/table',
                      }"
                />
                <sidebar-item
                    v-if="false"
                    :link="{
                        name: 'Typography',
                        icon: 'library_books',
                        path: '/components/typography',
                      }"
                />
                <sidebar-item
                    v-if="false"
                    :link="{
                        name: 'Icons',
                        icon: 'bubble_chart',
                        path: '/components/icons',
                      }"
                />
                <sidebar-item
                    v-if="false"
                    :link="{ name: 'Maps', icon: 'place', path: '/components/maps' }"
                />
                <sidebar-item
                    v-if="false"
                    :link="{
                        name: 'Notifications',
                        icon: 'notifications',
                        path: '/components/notifications',
                      }"
                />
                <sidebar-item
                    v-if="false"
                    :link="{
            name: 'RTL Support',
            icon: 'language',
            path: '/components/rtl',
          }"
                />

            </template>
        </side-bar>
        <div class="main-panel">
            <top-navbar></top-navbar>
            <div :class="{ content: !$route.meta.hideContent }">
                <zoom-center-transition :duration="200" mode="out-in">
                    <!-- your content here -->
                    <router-view/>
                </zoom-center-transition>
            </div>
            <content-footer v-if="!$route.meta.hideFooter"></content-footer>
        </div>
    </div>
</template>
<script>
    /* eslint-disable no-new */
    import PerfectScrollbar from "perfect-scrollbar";
    import "perfect-scrollbar/css/perfect-scrollbar.css";
    import {userMixin} from '@/mixins/Mixins'

    function hasElement(className) {
        return document.getElementsByClassName(className).length > 0;
    }

    function initScrollbar(className) {
        if (hasElement(className)) {
            new PerfectScrollbar(`.${className}`);
            document.getElementsByClassName(className)[0].scrollTop = 0;
        } else {
            // try to init it later in case this component is loaded async
            setTimeout(() => {
                initScrollbar(className);
            }, 100);
        }
    }

    function reinitScrollbar() {
        let docClasses = document.body.classList;
        let isWindows = navigator.platform.startsWith("Win");
        if (isWindows) {
            // if we are on windows OS we activate the perfectScrollbar function
            initScrollbar("sidebar");
            initScrollbar("sidebar-wrapper");
            initScrollbar("main-panel");

            docClasses.add("perfect-scrollbar-on");
        } else {
            docClasses.add("perfect-scrollbar-off");
        }
    }

    import TopNavbar from "./TopNavbar.vue";
    import ContentFooter from "./ContentFooter.vue";
    import MobileMenu from "./Extra/MobileMenu.vue";
    import FixedPlugin from "../../FixedPlugin.vue";
    import UserMenu from "./Extra/UserMenu.vue";

    export default {
        components: {
            TopNavbar,
            ContentFooter,
            FixedPlugin,
            MobileMenu,
            UserMenu,
        },
        mixins: [userMixin],
        computed: {
            sidebarBackgroundColor () {
                const localStorage = window.localStorage.getItem('sidebarBackgroundColor')
                if (!localStorage) {
                    return 'black'
                }

                return localStorage
            },
            sidebarBackground () {
                const localStorage = window.localStorage.getItem('sidebarBackground')
                if (!localStorage) {
                    return 'green'
                }

                return localStorage
            },
            sidebarBackgroundImage () {
                if (!this.sidebarImg) {
                    return ''
                }
                const localStorage = window.localStorage.getItem('sidebarBackgroundImage')
                if (!localStorage) {
                    return '/img/sidebar-2.jpg'
                }

                return localStorage
            },
            sidebarMini () {
                const localStorage = window.localStorage.getItem('sidebarMini')
                if (!localStorage) {
                    return true
                }

                return (localStorage === 'true')
            },
            sidebarImg () {
                const localStorage = window.localStorage.getItem('sidebarImg')
                if (!localStorage) {
                    return true
                }

                return (localStorage === 'true')
            }
        },
        methods: {
            toggleSidebar() {
                if (this.$sidebar.showSidebar) {
                    this.$sidebar.displaySidebar(false);
                }
            },
            minimizeSidebar() {
                if (this.$sidebar) {
                    this.$sidebar.toggleMinimize();
                }
            },
        },
        updated() {
            reinitScrollbar();
        },
        mounted() {
            reinitScrollbar();
        },
        watch: {
            sidebarMini() {
                this.minimizeSidebar();
            },
        },
    };
</script>
<style lang="scss">
    $scaleSize: 0.95;
    @keyframes zoomIn95 {
        from {
            opacity: 0;
            transform: scale3d($scaleSize, $scaleSize, $scaleSize);
        }
        to {
            opacity: 1;
        }
    }

    .main-panel .zoomIn {
        animation-name: zoomIn95;
    }

    @keyframes zoomOut95 {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
            transform: scale3d($scaleSize, $scaleSize, $scaleSize);
        }
    }

    .main-panel .zoomOut {
        animation-name: zoomOut95;
    }
</style>
