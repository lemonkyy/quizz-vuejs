import { createApp } from 'vue';
import { createPinia } from 'pinia';
import './style.css';
import App from './App.vue';
import router from './router';
import axios from './plugins/axios';

const app = createApp(App);
const pinia = createPinia();

app.use(router);
app.use(pinia);

app.config.globalProperties.$axios = axios;

app.mount('#app');
