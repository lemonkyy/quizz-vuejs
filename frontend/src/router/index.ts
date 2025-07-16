import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import UIPreview from '../pages/UIPreview.vue';
import Register from '@/pages/Register.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: {
      title: 'Quizz App - Home',
      description: 'Welcome to the Quizz App! Challenge your friends and test your knowledge with fun quizzes.',
    },
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: {
      title: 'Quizz App - Login',
      description: 'Login to your Quizz App account to access your quizzes and challenge your friends.',
    },
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: {
      title: 'Quizz App - Register',
      description: 'Create a new account on the Quizz App to start creating and playing quizzes.',
    },
  },
  {
    path: '/ui',
    name: 'UIPreview',
    component: UIPreview,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.afterEach((to) => {
  const defaultTitle = 'Quizup';
  const defaultDescription = 'Enjoy quizzes with friends!';

  document.title = (to.meta.title as string) || defaultTitle;

  let metaDescription = document.querySelector('meta[name="description"]');

  if (!metaDescription) {
    metaDescription = document.createElement('meta');
    metaDescription.setAttribute('name', 'description');
    document.head.appendChild(metaDescription);
  }

  metaDescription.setAttribute('content', (to.meta.description as string) || defaultDescription);
});

export default router;
