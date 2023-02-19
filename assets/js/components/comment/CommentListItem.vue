<template>
  <div class="d-flex px-2 pt-2">
    <vote-buttons :data="data" @update-score="updateScore">
    </vote-buttons>
    <div class="flex-column ps-1 w-100">
      <div :id="'c_' + comment.shortId">
        <a class="me-1" :href="comment.user.url">
          <img class="avatar-sm" :src="comment.user.avatarPath"  alt=""/>
        </a>
        <small>
          <a :href="comment.user.url" class="text-decoration-none hoverable">
            {{ comment.user.username }}
          </a>
          {{ comment.createdAt }} |
          <span v-if="!nestedView">
          <a class="text-decoration-none hoverable" :href="comment.story.url + '#c_' + comment.shortId">
            link
          </a> | on:
          <a class="text-decoration-none hoverable" :href="comment.story.url">
            {{ comment.story.name }}
          </a>
        </span>
          <span v-else>
          <a v-if="childrenCount > 0"
             class="text-decoration-none"
             :href="'#children-list-' + comment.id"
             data-bs-toggle="collapse"
             aria-expanded="false"
             @click="expandCollapse">
              {{ childrenCount }} replies
            <i class="bi" :class="collapsible"></i>
          </a>
          <span v-else>{{ childrenCount }} replies</span>
          <a v-if="appUser"
             class="ms-2 text-decoration-none"
             :href="'#reply-form-' + comment.id"
             data-bs-toggle="collapse"
             aria-expanded="false">
            <i class="bi bi-reply-fill"></i> reply
          </a>
          <a v-else class="ms-2 text-decoration-none" :href="'/login'">
            <i class="bi bi-reply-fill"></i> reply
          </a>
        </span>
        </small>
        <div class="body">
          {{ comment.body }}
        </div>
      </div>
      <div class="collapse mt-3" :id="'reply-form-' + comment.id">
        <comment-form :action="comment.urls.reply" :submit-button-text="'add reply'" @add-comment="addComment">
        </comment-form>
      </div>
      <div class="collapse show" :id="'children-list-' + comment.id">
        <template v-for="child in children" :key="child.id">
          <children-list-item :comment="child" :app-user="appUser">
          </children-list-item>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import ChildrenListItem from "./ChildrenListItem";
import CommentForm from "./CommentForm";
import VoteButtons from '../VoteButtons';
import { defineComponent, provide, ref } from "vue";

export default defineComponent({
  name: 'comment-list-item',
  components: {ChildrenListItem, CommentForm, VoteButtons},
  props: {
    comment: { type: Object },
    appUser: { type: Object },
    nestedView: { type: Boolean, default: true },
  },
  setup(props, {emit}) {
    provide('appUser', props.appUser);
    const collapsible = ref('bi-arrows-collapse');
    const score = ref(props.comment.score);
    const hasVoteOfCurrentUser = ref(props.comment.hasVoteOfCurrentUser);
    const id = ref(props.comment.id);
    const children = ref(props.comment.children);
    const childrenCount = ref(props.comment.childrenCount);
    let body = ref('');
    let errors = ref([]);
    const addComment = (value) => {
      children.value.unshift(value.comment);
      childrenCount.value = value.comment.childrenCount;
      emit('update-comments-count', value.totalCount);
    }
    const updateScore = (value) => {
      score.value = value;
      hasVoteOfCurrentUser.value = true;
      emit('update-score', value);
    }
    const expandCollapse = () => {
      collapsible.value = collapsible.value === 'bi-arrows-collapse' ? 'bi-arrows-expand' : 'bi-arrows-collapse';
    }
    const data = {
      'score': score,
      'hasVoteOfCurrentUser': hasVoteOfCurrentUser,
      'parentId': id,
      'url': props.comment.urls.vote,
    }

    return {
      addComment,
      body,
      children,
      childrenCount,
      collapsible,
      data,
      errors,
      expandCollapse,
      id,
      updateScore,
    }
  },
});
</script>
