import Layout from '@/layout';

const dev = {
    path: '/dev',
    name: 'Dev',
    hidden: true,
    meta: {
        title: 'ROUTER.DEV',
        icon: 'fab fa-dev',
    },
    component: Layout,
    redirect: { name: 'DevIndex' },
    children: [
        {
            path: 'index',
            name: 'DevIndex',
            meta: {
                title: 'ROUTER.DEV',
            },
            component: () => import(/* webpackChunkName: "DevIndex" */ '@/pages/Dev/index.vue'),
        },
    ],
};

export default dev;
