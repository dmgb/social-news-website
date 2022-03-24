import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '../css/main.css';
import { createApp } from 'vue/dist/vue.esm-bundler';
import CommentListItem from './components/comment/CommentListItem';
import StoryListItem from './components/story/StoryListItem';
import StoryView from './components/story/StoryView';
import BanUserButton from './components/user/button/BanUserButton';
import ApproveStoryButton from "./components/story/button/ApproveStoryButton";
import InviteUserButton from "./components/user/button/InviteUserButton";
import DeleteStoryButton from "./components/story/button/DeleteStoryButton";
import DashboardStoryListItem from "./components/story/DashboardStoryListItem";

const bootstrap = require('bootstrap/dist/js/bootstrap.bundle.min.js');
const app = createApp({});
app.component('comment-list-item', CommentListItem);
app.component('story-list-item', StoryListItem);
app.component('story-view', StoryView);
app.component('ban-user-button', BanUserButton);
app.component('approve-story-button', ApproveStoryButton);
app.component('invite-user-button', InviteUserButton);
app.component('delete-story-button', DeleteStoryButton);
app.component('dashboard-story-list-item', DashboardStoryListItem);

app.mount('#app');

const selectElement = (element) => document.querySelector(element);
selectElement('.hamburger').addEventListener('click', () => {
    selectElement('.hamburger').classList.toggle('active');
});

window.addEventListener('load', () => {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    if (toastElList) {
        const toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl, { delay: 5000 });
        });
        toastList.forEach(toast => toast.show());
    }
});
