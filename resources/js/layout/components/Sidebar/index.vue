<template>
	<div id="sidebar-wrapper">
		<MenuSidebar class="sidebar-menu" style="height: 100%; font-weight: bold;" :routes="routes" />
	</div>
</template>

<script>
import MenuSidebar from './Menu.vue';

export default {
    name: 'Sidebar',
    components: {
        MenuSidebar,
    },
    computed: {
        routes() {
            return this.$store.getters.permissionRoutes.filter(item => item.hidden !== true);
        },
    },
};
</script>

<style lang="scss" scoped>
    @import '@/scss/variables.scss';
    @import '@/scss/modules/layout.scss';
</style>
