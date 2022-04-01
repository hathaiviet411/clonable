<template>
	<div>
		<ul class="display-menu">
			<router-link
				v-for="(itemRouter, indexRouter) in routes"
				:key="indexRouter"
				:to="itemRouter.path"
			>
				<li :class="'item ' + 'router-item-' + indexRouter">
					<i :class="itemRouter.meta.icon" />
					<span>{{ $t(itemRouter.meta.title) }}</span>
				</li>
			</router-link>
		</ul>
	</div>
</template>

<script>
export default {
    name: 'MenuSidebar',
    props: {
        routes: {
            type: Array,
            require: true,
            default: function() {
                return [];
            },
        },
    },
    methods: {},
};
</script>

<style lang="scss" scoped>
    @import '@/scss/variables.scss';

    .display-menu {
        overflow: auto;
        position: relative;
        height: 100%;
        display: block;
        list-style-type: disc;
        margin-block-start: 0;
        margin-block-end: 0;
        margin-inline-start: 0;
        margin-inline-end: 0;
        padding-inline-start: 0;
        text-align: center;
        padding: 5px;

        a {
            li {
                i {
                    display: block;
                    margin-bottom: 5px;
                    font-size: 35px;
                    padding-top: 10px;
                }

                text-decoration: none;
                list-style-type: none;
                margin-bottom: 20px;
                font-size: 14px;

                color: $white;
            }

            &:hover {
                text-decoration: none;
            }
        }

        a.router-link-active {
            li {
                color: #EE4540;
            }
        }
    }
</style>
