<template>
	<b-button
		:class="className"
		:disabled="disabled"
	>
		<slot>{{ textButton }}</slot>
	</b-button>
</template>

<script>
export default {
    name: 'VButton',
    props: {
        textButton: {
            type: String,
            require: true,
            default: 'Button',
        },
        className: {
            type: String,
            require: false,
            default: 'v-button-default',
        },
        disabled: {
            type: Boolean,
            require: false,
            default: false,
        },
    },
};
</script>

<style lang="scss" scoped>
    @import '@/scss/variables.scss';

    .btn {
        min-width: 150px;
        border: none;
    }

    .login-btn {
        min-width: 250px;
        border-radius: 15px;
    }

    .maintenance-pdf-btn {
        min-width: 200px;
        float: right;
        margin-bottom: 10px;
        margin-top: 30px;
        text-align: center;
        align-items: center;
        background-color: $west-side;
    }

    .maintenance-btn {
        width: 100%;
        height: 48px;
        font-weight: bold;
    }

    .maintenance-btn-default {
        &:hover {
            opacity: .5;
            background-color: $west-side !important;
        }

        &:active {
            background-color: $west-side !important;
        }

        &:focus {
            background-color: $west-side !important;
        }
    }

    .vehicle-info-btn {
        width: 100%;
        height: 48px;
        font-weight: bold;
    }

    .cost-info-btn {
        width: 100%;
        height: 48px;
        font-weight: bold;
    }

    .updated-history-btn {
        width: 100%;
        height: 48px;
        font-weight: bold;
    }

    .vehicle-info-btn-default {
        &:hover {
            opacity: .5;
            background-color: $west-side !important;
        }

        &:active {
            background-color: $west-side !important;
        }

        &:focus {
            background-color: $west-side !important;
        }
    }

    .v-button-default {
        background-color: $west-side !important;

        &:hover {
            opacity: .5;
            background-color: $west-side !important;
        }

        &:active {
            background-color: $west-side !important;
        }

        &:focus {
            background-color: $west-side !important;
        }
    }

    .v-button-primary {
        background-color: $picton-blue !important;

        &:hover {
            opacity: .5;
            background-color: $picton-blue !important;
        }

        &:active {
            background-color: $picton-blue !important;
        }

        &:focus {
            background-color: $picton-blue !important;
        }
    }

    .v-izumi-btn {
        background-color: $smalt !important;
        color: $golden-tainoi !important;
        font-weight: bold !important;

        &:hover {
            opacity: .5;
            background-color: $smalt !important;
        }

        &:active {
            background-color: $smalt !important;
        }

        &:focus {
            background-color: $smalt !important;
        }
    }
</style>
