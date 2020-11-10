<template>
  <div class="user">
    <div class="photo">
      <img :src="avatar" alt="avatar" />
    </div>
    <div class="user-info">
      <a
        data-toggle="collapse"
        :aria-expanded="!isClosed"
        @click.stop="toggleMenu"
        @click.capture="clicked"
      >
        <span>
          {{ title }}
          <b class="caret"></b>
        </span>
      </a>

      <collapse-transition>
        <div v-show="!isClosed">
          <ul class="nav">
            <slot>
              <li>
                <a @click="goToProfile">
                  <span class="sidebar-mini">MP</span>
                  <span class="sidebar-normal">حساب من</span>
                </a>
              </li>
              <li>
                  <form id="logout-form" action="/logout" method="POST" style="display: none;">
                      <input type="hidden" id="logout-form-csrf_token" name="_token" value="viVMg6WzIMXAPgk9QFvOzpVZl5jahXKOhCSTziVW">
                  </form>
                <a @click="logout">
                  <span class="sidebar-mini">L</span>
                  <span class="sidebar-normal">خروج</span>
                </a>
              </li>
            </slot>
          </ul>
        </div>
      </collapse-transition>
    </div>
  </div>
</template>
<script>
export default {

  data() {
    return {
      isClosed: true,
      title: 'Profile',
      avatar: "/img/faces/marc.jpg"
    };
  },

  async created() {
    this.$store.watch(() => this.$store.getters["profile/me"], (me) => {
      this.title = me.name
    })
    await this.$store.dispatch("profile/me")
  },
    mounted() {
        // document.getElementById('logout-form').action = ''
        document.getElementById('logout-form-csrf_token').value = document.getElementsByName('csrf-token')[0].content
    },

  methods: {
    clicked: function(e) {
      e.preventDefault();

    },
    toggleMenu: function() {
      this.isClosed = !this.isClosed;
    },
    goToProfile() {
      this.$router.push({name: "User Profile"})
    },
    logout() {
      // this.$store.dispatch("logout");
        document.getElementById('logout-form').submit();
    }
  }
}
</script>
<style>
.collapsed {
  transition: opacity 1s;
}
</style>
