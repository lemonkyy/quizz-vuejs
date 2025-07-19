import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";
import './style.css';
import App from './App.vue';
import router from './router';
import axios from './api/axios';
import VueMatomo from 'vue-matomo/src/index.js'


import * as Sentry from "@sentry/vue";

const app = createApp(App);

const pinia = createPinia();

const  toastOptions = {
  position: POSITION.BOTTOM_CENTER
}

app.use(router);
app.use(pinia);
app.use(Toast, toastOptions);
app.use(VueMatomo, {
  host: import.meta.env.VITE_MATOMO_HOST || import.meta.env.VITE_MATOMO_HOST_DEV,
  siteId: import.meta.env.VITE_MATOMO_SITE_ID || import.meta.env.VITE_MATOMO_SITE_ID_DEV,
  router: router,
  enableLinkTracking: true,
  trackInitialView: true,
  trackHeartbeat: true,
  debug: false,
});

declare global {
  interface Window {
    _paq: any;
  }
}

Sentry.init({
  app,
  dsn: import.meta.env.VITE_SENTRY_DSN,
  sendDefaultPii: true,
  environment: "development"
});

window._paq.push(['trackPageView']);

app.config.globalProperties.$axios = axios;

app.mount('#app');
