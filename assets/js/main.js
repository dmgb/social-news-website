import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '../css/main.css';
import './functions';
import { createApp } from 'vue/dist/vue.esm-bundler';
import { createRouter, createWebHistory } from 'vue-router';
import Components from './register';

const app = createApp({});
const router = createRouter({
    history: createWebHistory(),
    routes: [{ path: '/:pathMatch(.*)', component: { template: '' } }],
})

Components.register(app);

app.use(router)
app.mount('#app');
