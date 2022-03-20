<template>
  <a href="#" class="me-1 text-decoration-none" role="button" data-bs-toggle="modal" :data-bs-target="'#modal-' + action + storyId">
    <span v-if="action === 'approve'">
      <i class="bi bi-check-circle-fill"></i>
    </span>
    <span v-else>
      <i class="bi bi-x-circle-fill"></i>
    </span>
  </a>
  <approve-story-dialog :url="url" :action="action" :story-id="storyId"></approve-story-dialog>
</template>

<script>
import ApproveStoryDialog from "../dialog/ApproveStoryDialog";
import { defineComponent } from "vue";

export default defineComponent({
  name: "approve-story-button",
  components: { ApproveStoryDialog },
  props: {
    url: { type: String },
    action: { type: String },
    storyId: { type: Number },
  },
});
</script>
