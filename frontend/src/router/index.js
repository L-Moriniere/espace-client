import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ResetPasswordView from "@/views/ResetPasswordView.vue";
import LoginView from "@/views/LoginView.vue";
import RegisterView from "@/views/RegisterView.vue";
import RequestResetPasswordView from "@/views/RequestResetPasswordView.vue";


//Différentes routes
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/login',
    },
    {
      path: '/home',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/request-reset-password',
      name: 'reset-password',
      component: RequestResetPasswordView,
    },
    {
      path: '/reset-password',
      name: 'reset-password',
      component: ResetPasswordView,
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
  ],
})

//vérification token
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');

  // Pages accessibles sans token
  const publicPages = ['login', 'reset-password', 'register'];

  if (!token && !publicPages.includes(to.name)) {
    next('/login');
  } else if (token && to.name === 'login') {
    next('/home');
  } else {
    next();
  }
});

export default router
