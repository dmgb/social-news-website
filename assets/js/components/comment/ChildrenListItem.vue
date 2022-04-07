<template>
  <div class="d-flex">
    <vote-buttons :data="data" @update-score="updateScore">
    </vote-buttons>
    <div class="flex-column px-1" :id="'c_' + comment.shortId">
      <div>
        <a class="me-1" :href="comment.user.url">
          <img class="avatar-sm" :src="comment.user.avatarPath"  alt=""/>
        </a>
        <small>
          <a :href="comment.user.url" class="text-decoration-none hoverable">
            {{ comment.user.username }}
          </a>
          {{ comment.createdAt }}
        </small>
      </div>
      <div class="body mb-2">
        {{ comment.body }}
      </div>
    </div>
  </div>
</template>

<script>
import VoteButtons from '../VoteButtons';
import { defineComponent, inject, provide, ref } from "vue";

export default defineComponent({
  name: 'child-comment-list-item',
  components: { VoteButtons },
  props: {
    comment: { type: Object },
  },
  setup(props, { emit }) {
    const appUser = inject('appUser');
    provide('appUser', appUser);
    const score = ref(props.comment.score);
    const hasVoteOfCurrentUser = ref(props.comment.hasVoteOfCurrentUser);
    const id = ref(props.comment.id);
    const updateScore = (value) => {
      score.value = value;
      hasVoteOfCurrentUser.value = true;
      emit('update-score', value);
    }
    const data = {
      'score': score,
      'hasVoteOfCurrentUser': hasVoteOfCurrentUser,
      'parentId': id,
      'url': props.comment.urls.vote,
    }

    return {
      appUser,
      data,
      updateScore,
    }
  },
});
</script>
