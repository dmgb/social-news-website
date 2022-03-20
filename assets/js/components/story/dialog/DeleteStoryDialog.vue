<template>
  <div class="modal fade pe-1" :id="'modal-' + storyId" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">
            <span v-if="action === 'delete'">
              <i class="bi bi-trash"></i>
              Delete story
            </span>
            <span v-else>
              <i class="bi bi-arrow-counterclockwise"></i>
              Undelete story
            </span>
          </h5>
          <button type="button" class="btn-close" :data-bs-dismiss="'modal-' + action" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post" @submit.prevent="deleteUndeleteStory">
            <button class="btn btn-dark me-2 my-3" type="submit">Confirm</button>
            <button class="btn btn-secondary my-3" type="button" data-bs-dismiss="modal">Cancel</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref } from "vue";
import { http } from "../../../http";

export default defineComponent({
  name: "delete-story-dialog",
  props: {
    url: { type: String },
    action: { type: String },
    storyId: { type: Number },
  },
  setup(props) {
    let errors = ref([]);
    const deleteUndeleteStory = async () => {
      console.log(props.url);
      errors.value = [];
      const {error, data} = await http.post(props.url);
      if (data.errors) {
        errors.value = data.errors;
      }

      if (error) {
        console.log(error);
      }

      if (data.success) {
        window.location.reload();
      }
    }

    return {
      deleteUndeleteStory,
      errors,
    }
  }
});
</script>
