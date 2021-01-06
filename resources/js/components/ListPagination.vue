<template>
    <md-card-actions
        v-if="paginate &&
                    paginate.from !== null &&
                    paginate.to !== null &&
                    parseInt(paginate.total) !== 0"
        md-alignment="space-between">
        <p class="card-category">
            نمایش
            {{ paginate.from }}
            تا
            {{ paginate.to }}
            از
            {{ paginate.total }}
            مورد
        </p>
        <paginate
            v-model="paginate.current_page"
            :page-count="paginate.last_page"
            :page-range="3"
            :margin-pages="2"
            :click-handler="clickCallback"
            :prev-text="'<'"
            :next-text="'>'"
            :container-class="'pagination pagination-no-border pagination-success pagination-primary'"
            :page-class="'page-item'"
            :page-link-class="'page-link'">
        </paginate>
    </md-card-actions>
</template>
<script>
    export default {
        name: 'ListPagination',
        props: ['paginate'],
        methods: {
            clickCallback(data) {
                this.$emit('changePage', data)
            }
        }
    };
</script>
