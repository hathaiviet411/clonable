const login = {
    path: '/login',
    name: 'Login',
    hidden: true,
    component: () => import(/* webpackChunkName: "Login" */ '@/pages/Login/index.vue'),
};

export default login;
