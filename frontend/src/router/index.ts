
import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import UIPreview from '../pages/UIPreview.vue';
import Register from '@/pages/Register.vue';
import Question from '../pages/Question.vue'
import Create from '../pages/Create.vue'
import Room from '../pages/Room.vue'


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
    path: '/question/:id?',
    name: 'Question',
    component: Question,
    meta: {
      title: 'Quizz App - Take a Quiz',
      description: 'Answer the questions and see how you score in this quiz!',
    },
  },
  {
    path: '/create',
    name: 'Create',
    component: Create,
    meta: {
      title: 'Quizz App - Create a Quiz',
      description: 'Create your own custom quiz to challenge your friends.',
    },
  },
  {
    path: '/room',
    name: 'Room',
    component: Room,
    meta: {
      title: 'Quizz App - Room',
      description: 'Join your friends in this quiz room and start playing!',
    },
  },
  {
    path: '/ui',
    name: 'UIPreview',
    component: UIPreview,
    meta: {
      title: 'Quizz App - UI Preview',
      description: 'A preview of the UI components for the Quizz App.',
    },
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
