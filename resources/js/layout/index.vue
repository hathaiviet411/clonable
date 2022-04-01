<template>
	<div id="wrapper" :class="toggle">
		<Sidebar />
		<div id="page-content-wrapper">
			<Navbar @toggle="toggleMenu()" />
			<AppMain class="app-main" />
		</div>
	</div>
</template>

<script>
import Navbar from './components/Navbar/index';
import Sidebar from './components/Sidebar/index';
import AppMain from './components/AppMain';

export default {
    name: 'Layout',
    components: {
        Navbar,
        Sidebar,
        AppMain,
    },
    data() {
        return {
            toggle: '',
            toggleBool: false,
        };
    },
    methods: {
        toggleMenu(){
            this.toggleBool = !this.toggleBool;
            if (this.toggleBool === true){
                this.toggle = 'toggled';
            } else {
                this.toggle = '';
            }
        },
    },
};
</script>

<style lang="scss">
    @import '@/scss/variables.scss';
    @import '@/scss/modules/layout.scss';

    .app-main {
        margin-top: 100px !important;
    }
</style>
