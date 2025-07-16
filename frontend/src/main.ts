import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";
import './style.css';
import App from './App.vue';
import router from './router';
import axios from './plugins/axios';

const app = createApp(App);
const pinia = createPinia();
const  toastOptions = {
  position: POSITION.BOTTOM_CENTER
}

app.use(router);
app.use(pinia);
app.use(Toast, toastOptions);

app.config.globalProperties.$axios = axios;

app.mount('#app');
