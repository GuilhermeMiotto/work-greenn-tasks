import { createRouter, createWebHistory } from 'vue-router';
import LoginScreen from '@/views/LoginScreen.vue';
import SignUpScreen from '@/views/SingUpScreen.vue';
import MainPage from '@/components/MainPage.vue';

const routes = [
  {
    path: '/',
    name: 'login',
    component: LoginScreen,
  },
  {
    path: '/cadastro',
    name: 'cadastro',
    component: SignUpScreen,
  },
  {
    path: '/homepage',
    name: 'homepage',
    component: MainPage,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

