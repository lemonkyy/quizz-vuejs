import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import UIPreview from '../pages/UIPreview.vue';

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/login', name: 'Login', component: Login },
  { path: '/ui-preview', name: 'UIPreview', component: UIPreview },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
