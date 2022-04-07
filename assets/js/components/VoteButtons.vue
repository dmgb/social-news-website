<template>
  <div class="text-center">
    <div v-if="!appUser">
      <a :href="'/login'">
        <i class="bi bi-caret-up-fill"></i>
      </a>
    </div>
    <div v-else-if="data.hasVoteOfCurrentUser.value">
      <i class="bi bi-caret-up-fill"></i>
    </div>
    <div v-else>
      <a :href="data.url" @click.prevent="addVote(true)">
        <i class="bi bi-caret-up-fill"></i>
      </a>
    </div>
    <div class="score">
      {{ data.score.value }}
    </div>
    <div v-if="!appUser">
      <a :href="'/login'">
        <i class="bi bi-caret-down-fill"></i>
      </a>
    </div>
    <div v-else-if="data.hasVoteOfCurrentUser.value">
      <i class="bi bi-caret-down-fill"></i>
    </div>
    <div v-else>
      <a :href="data.url" @click.prevent="addVote(false)">
        <i class="bi bi-caret-down-fill"></i>
      </a>
    </div>
  </div>
</template>

<script>
import { defineComponent, inject, isRef } from "vue";
import { http } from "../http.js";

export default defineComponent({
  name: 'vote-buttons',
  emits: ['update-score'],
  props: {
    data: { type: Object },
  },
  setup(props, { emit }) {
    const appUser = inject('appUser');
    const parentId = isRef(props.data.parentId) ? props.data.parentId.value : props.data.parentId;
    const addVote = async (upvote) => {
        const { error, data } = await http.post(props.data.url, { id: parentId, upvote: upvote }, {
        });

        if (data) {
          emit('update-score', data.score);
        }

        if (error) {
          console.log(error);
        }
    };

    return {
      appUser,
      addVote,
    }
  }
});
</script>

<style scoped>
  a {
    color: inherit;
  }

  .score {
    font-size: .75rem;
  }

  .text-center {
    padding-top: 0.2rem;
    line-height: 0.75rem;
  }
</style>
