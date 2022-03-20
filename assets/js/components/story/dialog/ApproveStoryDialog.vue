<template>
  <div class="modal fade" :id="'modal-' + action + storyId" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">
            <span v-if="action === 'approve'">
              <i class="bi bi-check-circle-fill"></i>
              Approve story
            </span>
            <span v-else>
              <i class="bi bi-x-circle-fill"></i>
              Disapprove story
            </span>
          </h5>
          <button type="button" class="btn-close" :data-bs-dismiss="'modal-' + action" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post" @submit.prevent="approveDisapproveStory">
            <template v-if="action === 'disapprove'">
              <div class="row mb-2">
                <div class="col">
                  <label for="disapprovedReason">Reason</label>
                </div>
                <div class="col text-end">
                  <select @change="changePreset($event)">
                    <option value="">--Choose a preset--</option>
                    <option value="Spam">Spam</option>
                  </select>
                </div>
              </div>
              <textarea v-show="isVisible" v-model="disapprovedReason" class="form-control" id="disapprovedReason" rows="3" required>
              </textarea>
              <template v-if="errors.length > 0" v-for="(error, key) in errors" :key="key">
                <div class="invalid-feedback d-block">{{ error }}</div>
              </template>
            </template>
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
  name: "approve-story-dialog",
  props: {
    url: { type: String },
    action: { type: String },
    storyId: { type: Number },
  },
  setup(props) {
    let disapprovedReason = ref('');
    let isVisible = ref(true);
    let errors = ref([]);
    const changePreset = (event) => {
      disapprovedReason.value = event.target.value;
      isVisible.value = disapprovedReason.value === '';
      errors.value = [];
    }

    const approveDisapproveStory = async () => {
      errors.value = [];
      const { error, data } = await http.post(props.url, { disapprovedReason: disapprovedReason.value });
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
      approveDisapproveStory,
      changePreset,
      disapprovedReason,
      errors,
      isVisible,
    }
  }
});
</script>

<style scoped>
  .col {
    font-size: 0.95rem;
  }
</style>
