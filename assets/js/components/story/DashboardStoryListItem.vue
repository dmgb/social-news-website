<template>
  <div class="d-flex px-2 pb-2 list-item">
    <div class="flex-column">
      <div>
        <a :href="story.url" class="me-1 text-decoration-none" style="font-weight: bolder;">
          {{ story.title }}
        </a>
        <a v-for="tag in story.tags" class="me-1 text-decoration-none" :href="tag.route">
          <span class="badge rounded-pill bg-dark">{{ tag.name }}</span>
        </a>
        <a :href="story.domain.url" class="text-decoration-none hoverable">
          <small>
            <i>{{ story.domain.name }}</i>
          </small>
        </a>
      </div>
      <div>
        <small>
          submitted by
          <a :href="story.user.url" class="text-decoration-none hoverable">
            {{ story.user.username }}
          </a>
          {{ story.createdAt }} |
          <span>
            <approve-story-button v-if=story.routes.approve :url="story.routes.approve" :action="'approve'" :story-id="story.id">
            </approve-story-button>
            <approve-story-button v-if=story.routes.disapprove :url="story.routes.disapprove" :action="'disapprove'" :story-id="story.id">
            </approve-story-button>
            <a :href="story.routes.edit" class="me-1 text-decoration-none">
              <i class="bi bi-pencil-square"></i>
            </a>
            <delete-story-button :url="story.routes.delete.url" :action="story.routes.delete.action" :story-id="story.id">
            </delete-story-button>
          </span>
        </small>
      </div>
    </div>
  </div>
</template>

<script>
import ApproveStoryButton from "./button/ApproveStoryButton";
import DeleteStoryButton from "./button/DeleteStoryButton";
import { defineComponent } from "vue";

export default defineComponent({
  name: 'dashboard-story-list-item',
  components: { DeleteStoryButton, ApproveStoryButton },
  props: {
    story: { type: Object },
  },
});
</script>

<style scoped>
  .list-item:last-of-type {
    padding-bottom: .5rem;
  }
</style>
