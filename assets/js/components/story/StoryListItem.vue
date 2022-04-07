<template>
  <div class="d-flex px-2 pt-2 list-item">
    <vote-buttons v-if="!story.pending || !story.deleted" :data="data" @update-score="updateScore">
    </vote-buttons>
    <div class="flex-column px-1">
      <div>
        <a :href="story.url" class="me-1 text-decoration-none" style="font-weight: bolder;">
          {{ story.title }}
        </a>
        <a v-for="tag in story.tags" class="me-1 text-decoration-none" :href="tag.url">
          <span class="badge rounded-pill bg-dark">{{ tag.name }}</span>
        </a>
        <a :href="story.domain.url" class="text-decoration-none hoverable">
          <small>
            <i>{{ story.domain.name }}</i>
          </small>
        </a>
      </div>
      <div>
        <a :href="story.user.url">
          <img class="avatar-sm" :src="story.user.avatarPath"  alt=""/>
        </a>
        <small>
          submitted by
          <a :href="story.user.url" class="text-decoration-none hoverable">
            {{ story.user.username }}
          </a>
          {{ story.createdAt }} |
          <a v-if=!story.adminView :href="story.urls.show" class="text-decoration-none hoverable">
            <span v-if="story.commentsCount === 0">
              discuss
            </span>
            <span v-else>
              {{ story.commentsCount }} comments
            </span>
          </a>
          <span v-if="story.urls.edit"> |
              <a :href="story.urls.edit" class="me-1 text-decoration-none">
                <i class="bi bi-pencil-square"></i>
              </a>
              <a v-if="story.urls.delete" class="me-1 text-decoration-none">
                <delete-story-button :url="story.urls.delete.url" action="delete" :story-id="story.id"></delete-story-button>
              </a>
          </span>
        </small>
      </div>
    </div>
  </div>
</template>

<script>
import VoteButtons from "../VoteButtons";
import { defineComponent, provide, ref } from "vue";

export default defineComponent({
  name: 'story-list-item',
  components: {VoteButtons},
  props: {
    story: {type: Object},
    appUser: {type: Object},
  },
  setup(props) {
    provide('appUser', props.appUser);
    const score = ref(props.story.score);
    const hasVoteOfCurrentUser = ref(props.story.hasVoteOfCurrentUser);
    const updateScore = (value) => {
      score.value = value;
      hasVoteOfCurrentUser.value = true;
    }
    const data = {
      'score': score,
      'hasVoteOfCurrentUser': hasVoteOfCurrentUser,
      'parentId': props.story.id,
      'url': props.story.urls.vote,
    }

    return {
      data,
      updateScore,
    }
  },
});
</script>

<style scoped>
  .list-item:last-of-type {
    padding-bottom: .5rem;
  }
</style>
