import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import login from './modules/login';
import dev from './modules/dev';
import ErrorPage from './modules/errorPage';

export const constantRoutes = [
    {
        path: '/',
        redirect: { name: 'MaintenanceScheduleResults' },
        hidden: true,
    },
    login,
    dev,
];

export const asyncRoutes = [
    ErrorPage,
    {
        path: '*',
        redirect: { name: 'ErrorPage' },
        hidden: true,
    },
];

const createRouter = () => new VueRouter({
    mode: 'history',
    scrollBehavior: () => ({ y: 0 }),
    routes: constantRoutes,
});

const router = createRouter();

export function resetRouter() {
    const newRouter = createRouter();
    router.matcher = newRouter.matcher;
}

export default router;

