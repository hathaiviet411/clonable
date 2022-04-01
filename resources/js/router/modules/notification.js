import Layout from '@/layout/index.vue';

const Notification = {
    path: '/notification',
    name: 'notification',
    component: Layout,
    redirect: '/notification/index',
    meta: {
        title: 'ROUTER.NOTIFICATION',
        icon: 'notification',
        // roles: []
    },
};

export default Notification;
