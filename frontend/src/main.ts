import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'
import { OhVueIcon, addIcons } from "oh-vue-icons";
import * as Octicons from "oh-vue-icons/icons/oi";

const Oct = Object.values({ ...Octicons });
addIcons(...Oct);


const app = createApp(App)
app.component("v-icon", OhVueIcon);
app.use(router)
app.mount('#app')
