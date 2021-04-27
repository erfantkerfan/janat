<template>
    <md-card class="fixed-plugin">
        <md-card-header class="md-card-header-icon md-card-header-green">
            <div class="card-icon">
                <md-icon>settings</md-icon>
            </div>
            <h4 class="title">
                تنظیمات
            </h4>
        </md-card-header>
        <md-card-content>
            <ul>
                <li class="header-title">رنگ مورد انتخاب شده منو</li>
                <li class="adjustments-line text-center">
                    <span
                        v-for="item in sidebarColors"
                        :key="item.color"
                        class="badge filter"
                        :class="[`badge-${item.color}`, { active: item.active }]"
                        :data-color="item.color"
                        @click="changeSidebarBackground(item)"
                    >
                      </span>
                </li>
                <li class="header-title">رنگ پس زمینه منو</li>
                <li class="adjustments-line text-center">
                      <span
                          v-for="item in sidebarBg"
                          :key="item.colorBg"
                          class="badge filter"
                          :class="[`badge-${item.colorBg}`, { active: item.active }]"
                          :data-color="item.colorBg"
                          @click="changeSidebarBg(item)"
                      >
                      </span>
                </li>
            </ul>
            <md-switch
                :value="!sidebarMini"
                @change="val => updateValue('sidebarMini', val)"
            >
                حالت منوی کوچک
            </md-switch>
            <md-switch
                :value="!sidebarImg"
                @change="val => updateValueImg('sidebarImg', val)"
            >
                پس زمینه منو
            </md-switch>
            <div v-if="sidebarImg" class="sidebarImagesWrapper">
                تصاویر
                <div
                    v-for="item in sidebarImages"
                    :key="item.image"
                    :class="{ active: item.active }"
                    @click="changeSidebarImage(item)"
                >
                    <div class="img-holder">
                        <img :src="item.image" alt=""/>
                    </div>
                </div>
            </div>
        </md-card-content>
    </md-card>
</template>

<script>
import Vue from "vue";
import SocialSharing from "vue-social-sharing";
import VueGitHubButtons from "vue-github-buttons";
import "vue-github-buttons/dist/vue-github-buttons.css";

Vue.use(SocialSharing);
Vue.use(VueGitHubButtons, {useCache: true});
export default {
    props: {
        sidebarMini: Boolean,
        sidebarImg: Boolean
    },
    data() {
        return {
            documentationLink: "https://vue-material-dashboard-laravel.creative-tim.com/documentation/",
            shareUrl: "https://www.creative-tim.com/product/vue-material-dashboard-laravel",
            buyUrl: "",
            downloadUrl: "https://www.creative-tim.com/product/vue-material-dashboard-laravel",
            upgradeUrl: "https://www.creative-tim.com/product/vue-material-dashboard-laravel-pro",
            isOpen: false,
            backgroundImage: '/img/sidebar-2.jpg',
            selectedSidebarColor: 'green',
            sidebarColors: [
                {color: "purple", active: false},
                {color: "azure", active: false},
                {color: "green", active: true},
                {color: "orange", active: false},
                {color: "rose", active: false},
                {color: "danger", active: false}
            ],
            sidebarBg: [
                {colorBg: "black", active: true},
                {colorBg: "white", active: false},
                {colorBg: "red", active: false}
            ],
            sidebarImages: [
                {image: '/img/sidebar-1.jpg', active: false},
                {image: '/img/sidebar-2.jpg', active: false},
                {image: '/img/sidebar-3.jpg', active: false},
                {image: '/img/sidebar-4.jpg', active: false}
            ]
        };
    },
    created() {
        this.loadCorrentData()
    },
    computed: {
        currentSidebarBackgroundColor () {
            console.log('setting-sidebarBackgroundColor', this.$store.getters['settings/sidebarBackgroundColor'])
            const localStorage = window.localStorage.getItem('sidebarBackgroundColor')
            if (!localStorage) {
                return this.$store.getters['settings/sidebarBackgroundColor']
            }

            return localStorage
        },
        currentSidebarBackground () {
            console.log('setting-sidebarBackground', this.$store.getters['settings/sidebarBackground'])
            const localStorage = window.localStorage.getItem('sidebarBackground')
            if (!localStorage) {
                return this.$store.getters['settings/sidebarBackground']
            }

            return localStorage
        },
        currentSidebarBackgroundImage () {
            console.log('setting-sidebarBackgroundImage', this.$store.getters['settings/sidebarBackgroundImage'])
            if (!this.sidebarImg) {
                return ''
            }
            const localStorage = window.localStorage.getItem('sidebarBackgroundImage')
            if (!localStorage) {
                return this.$store.getters['settings/sidebarBackgroundImage']
            }

            return localStorage
        }
    },
    methods: {
        loadCorrentData () {
            // sidebarBackgroundImage
            let sidebarBackgroundImage = this.sidebarImages.find(item => item.image === this.currentSidebarBackgroundImage)
            if (sidebarBackgroundImage) {
                this.toggleList(this.sidebarImages, sidebarBackgroundImage)
            }
        },
        toggleDropDown() {
            this.isOpen = !this.isOpen;
        },
        closeDropDown() {
            this.isOpen = false;
        },
        toggleList(list, itemToActivate) {
            list.forEach(listItem => {
                listItem.active = false;
            });
            itemToActivate.active = true;
        },
        updateValue(name, val) {
            this.$emit(`update:${name}`, val);
        },
        updateValueImg(name, val) {
            this.$emit(`update:${name}`, val);

            if (this.sidebarImg) {
                document.body.classList.toggle("sidebar-image");
                this.$emit("update:image", "");
            } else {
                document.body.classList.toggle("sidebar-image");
                this.$emit("update:image", this.backgroundImage);
            }
        },
        changeSidebarBackground(item) {
            this.$emit("update:color", item.color);
            this.toggleList(this.sidebarColors, item);
        },
        changeSidebarBg(item) {
            this.$emit("update:colorBg", item.colorBg);
            this.toggleList(this.sidebarBg, item);
        },
        changeSidebarImage(item) {
            if (this.sidebarImg) {
                this.$emit("update:image", item.image);
            }
            this.backgroundImage = item.image;
            this.toggleList(this.sidebarImages, item);
        }
    }
};
</script>

<style lang="scss">
.sidebarImagesWrapper {
    display: flex;

    .active {
        background-color: #00bcd4;
        border: solid 5px #00bcd4;
        border-radius: 10px;
    }

    .img-holder {
        text-align: center;
        border-radius: 10px;
        background-color: #FFF;
        border: 3px solid #FFF;
        opacity: 1;
        cursor: pointer;
        display: block;
        max-width: 100px;
        overflow: hidden;
        padding: 0;

        img {
            margin-top: auto;
        }
    }
}
</style>

<style>
.centered-row {
    display: flex;
    height: 100%;
    align-items: center;
}

.button-container .btn {
    margin-right: 10px;
}

.centered-buttons {
    display: flex;
    justify-content: center;
}
</style>
