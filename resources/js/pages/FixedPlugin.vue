<template>
  <div v-if="false" class="fixed-plugin">
      <md-menu md-size="medium" :md-offset-x="127" :md-offset-y="-36">
          <md-button md-menu-trigger>
              <md-icon>settings</md-icon>
          </md-button>

          <md-menu-content>
              <ul>
                  <li class="header-title">Sidebar Filters</li>
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
                  <li class="header-title">Sidebar Background</li>
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
                  <li class="adjustments-line sidebar-mini">
                      Sidebar Mini
                      <md-switch
                          :value="!sidebarMini"
                          @change="val => updateValue('sidebarMini', val)"
                      ></md-switch>
                  </li>
                  <li class="adjustments-line sidebar-img">
                      Sidebar Image
                      <md-switch
                          :value="!sidebarImg"
                          @change="val => updateValueImg('sidebarImg', val)"
                      ></md-switch>
                  </li>

                  <li class="header-title">Images</li>
                  <li
                      v-for="item in sidebarImages"
                      :key="item.image"
                      :class="{ active: item.active }"
                      @click="changeSidebarImage(item)"
                  >
                      <a class="img-holder switch-trigger">
                          <img :src="item.image" alt=""/>
                      </a>
                  </li>
              </ul>
          </md-menu-content>
      </md-menu>
  </div>
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
          {image: '/img/sidebar-2.jpg', active: true},
          {image: '/img/sidebar-3.jpg', active: false},
          {image: '/img/sidebar-4.jpg', active: false}
        ]
      };
    },
    methods: {
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
