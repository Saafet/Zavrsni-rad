import { createRouter, createWebHistory } from 'vue-router'
import PRIJAVI from '../components/PRIJAVI.vue'
import LoginForm from '../components/LoginForm.vue';
import MainPage from '../components/MainPage.vue';

const routes = [
  {
    path: '/prijavi',
    name: 'prijavi',
    component: PRIJAVI
  },
  {
        path: '/',
        name: 'LoginForm',
        component: LoginForm
    },
    {
        path: '/main',
        name: 'MainPage',
        component: MainPage,
        meta: {
            requiresAuth: true
        }
    },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        const user = localStorage.getItem('user');
        if (!user) {
            next('/');
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router
