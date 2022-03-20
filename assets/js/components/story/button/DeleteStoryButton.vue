<template>
  <a href="#" class="me-1 text-decoration-none" role="button" data-bs-toggle="modal" :data-bs-target="'#modal-' + storyId">
    <span v-if="action === 'delete'">
      <i class="bi bi-trash"></i>
    </span>
    <span v-else>
      <i class="bi bi-arrow-counterclockwise"></i>
    </span>
  </a>
  <delete-story-dialog :url="url" :action="action" :story-id="storyId"></delete-story-dialog>
</template>

<script>
import DeleteStoryDialog from "../dialog/DeleteStoryDialog";
import { defineComponent } from "vue";

export default defineComponent({
  name: "delete-story-button",
  components: { DeleteStoryDialog },
  props: {
    url: { type: String },
    action: { type: String },
    storyId: { type: Number },
  },
});
</script>
