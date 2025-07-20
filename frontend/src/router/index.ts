
import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import Register from '@/pages/Register.vue';
import Question from '../pages/Question.vue'
import Create from '../pages/Create.vue'
import Room from '../pages/Room.vue'
import Results from '../pages/Results.vue'
import PublicRooms from '../pages/PublicRooms.vue'


const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: {
      title: 'QuizUp - Home',
      description: 'Welcome to QuizUp! Challenge your friends and test your knowledge with fun quizzes.',
    },
  },
  {
    path: '/public-rooms',
    name: 'PublicRooms',
    component: PublicRooms,
    meta: {
      title: 'QuizUp - Public Rooms',
      description: 'Discover and join public quiz rooms.',
    },
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: {
      title: 'QuizUp - Login',
      description: 'Login to your QuizUp account to access your quizzes and challenge your friends.',
    },
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: {
      title: 'QuizUp - Register',
      description: 'Create a new account on QuizUp to start creating and playing quizzes.',
    },
  },
  {
    path: '/question/:id?',
    name: 'Question',
    component: Question,
    meta: {
      title: 'QuizUp - Take a Quiz',
      description: 'Answer the questions and see how you score in this quiz!',
    },
  },
  {
    path: '/create',
    name: 'Create',
    component: Create,
    meta: {
      title: 'QuizUp - Create a Quiz',
      description: 'Create your own custom quiz to challenge your friends.',
    },
  },
  {
    path: '/room',
    name: 'Room',
    component: Room,
    meta: {
      title: 'QuizUp - Room',
      description: 'Join your friends in this quiz room and start playing!',
    },
  },
  {
    path: '/results',
    name: 'Results',
    component: Results,
    meta: {
      title: 'QuizUp - Results',
      description: 'See the quiz results and how you performed!',
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
