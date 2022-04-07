<template>
  <story-list-item :story="storyRef" :appUser="appUser">
  </story-list-item>
  <div class="mt-3 mb-n1 px-2">
    <comment-form :action="story.urls.comment" @add-comment="addComment"></comment-form>
  </div>
  <div class="pb-3">
    <template v-for="comment in commentsRef" :key="comment.id">
      <comment-list-item :comment="comment" :appUser="appUser" @update-comments-count="updateCommentsCount"></comment-list-item>
    </template>
  </div>
</template>

<script>
import CommentForm from "../comment/CommentForm";
import CommentListItem from "../comment/CommentListItem";
import StoryListItem from "./StoryListItem";
import { defineComponent, provide, ref } from "vue";

export default defineComponent({
  name: "story-view",
  components: {
    CommentForm,
    CommentListItem,
    StoryListItem,
  },
  props: {
    story: { type: Object },
    comments: { type: Object },
    appUser: { type: Object },
  },
  setup: function (props) {
    provide('appUser', props.appUser);
    const storyRef = ref(props.story);
    const commentsRef = ref(props.comments);
    const addComment = (value) => {
      commentsRef.value.unshift(value.comment);
      updateCommentsCount(value.totalCount);
    }
    const updateCommentsCount = (value) => {
      storyRef.value.commentsCount = value;
    }

    return {
      addComment,
      commentsRef,
      storyRef,
      updateCommentsCount,
    }
  }
});
</script>

<style scoped>
  @media (min-width: 992px) {
    .px-2 {
      max-width: 100%;
    }
  }

  @media (min-width: 576px) {
    .px-2 {
      max-width: 75%;
    }
  }
</style>
