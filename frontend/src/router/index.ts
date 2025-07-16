
import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import UIPreview from '../pages/UIPreview.vue';
import Register from '@/pages/Register.vue';
import Question from '../pages/Question.vue'
import Create from '../pages/Create.vue'


const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/login', name: 'Login', component: Login },
  { path: '/question/:id?', name: 'Question', component: Question },
  { path : '/create', name: 'Create', component: Create },
  { path: '/register', name: 'Register', component: Register },
  { path: '/ui', name: 'UIPreview', component: UIPreview },
];


const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
