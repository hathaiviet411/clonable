import Vue from 'vue';
import { ToastPlugin } from 'bootstrap-vue';

Vue.use(ToastPlugin);

import i18n from '@/lang';

class MakeToast {
    constructor() {
        this.toast = new Vue();
    }

    show({ variant = null, title, content, toaster = 'b-toaster-top-center', autoHideDelay = 1500 }) {
        this.toast.$bvToast.toast(content, {
            static: true,
            title: title,
            variant: variant,
            toaster: toaster,
            solid: true,
            autoHideDelay: autoHideDelay,
            appendToast: true,
        });
    }

    success({ title = i18n.t('TOAST.TITLE.SUCCESS'), content = '', toaster = 'b-toaster-top-center', autoHideDelay = 1500 }) {
        this.toast.$bvToast.toast(content, {
            static: true,
            title: title,
            variant: 'success',
            toaster: toaster,
            solid: true,
            autoHideDelay: autoHideDelay,
            appendToast: true,
        });
    }

    warning({ title = i18n.t('TOAST.TITLE.WARNING'), content = '', toaster = 'b-toaster-top-center', autoHideDelay = 1500 }) {
        this.toast.$bvToast.toast(content, {
            static: true,
            title: title,
            variant: 'warning',
            toaster: toaster,
            solid: true,
            autoHideDelay: autoHideDelay,
            appendToast: true,
        });
    }

    error({ title = i18n.t('TOAST.TITLE.ERROR'), content = '', toaster = 'b-toaster-top-center', autoHideDelay = 1500 }) {
        this.toast.$bvToast.toast(content, {
            static: true,
            title: title,
            variant: 'danger',
            toaster: toaster,
            solid: true,
            autoHideDelay: autoHideDelay,
            appendToast: true,
        });
    }

    hide(id) {
        this.toast.$bvToast.hide(id);
    }
}

export default {
    install(Vue) {
        const toast = new MakeToast();

        Vue.prototype.$toast = toast;
    },
};

