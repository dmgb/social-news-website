import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '../css/main.css';
import './functions';
import { createApp } from 'vue/dist/vue.esm-bundler';
import Components from './register';

const app = createApp({});

Components.register(app);
app.mount('#app');
