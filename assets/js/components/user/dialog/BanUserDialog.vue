<template>
  <div class="modal fade pe-1" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">
            <span v-if="action === 'ban'">
              <i class="bi bi-person-x-fill"></i>
              Ban user
            </span>
            <span v-else>
              <i class="bi bi-person-check-fill"></i>
              Unban user
            </span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post" @submit.prevent="banUnbanUser">
            <template v-if="action === 'ban'">
              <div class="row mb-2">
                <div class="col">
                  <label for="bannedReason">Reason</label>
                </div>
                <div class="col text-end">
                  <select @change="changePreset($event)">
                    <option value="">--Choose a preset--</option>
                    <option value="Spammer">Spammer</option>
                  </select>
                </div>
              </div>
              <textarea v-show="isVisible" v-model="bannedReason" id="bannedReason" class="form-control" rows="3" required></textarea>
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
  name: "ban-user-dialog",
  props: {
    url: { type: String },
    action: { type: String },
  },
  setup(props) {
    let bannedReason = ref('');
    let isVisible = ref(true);
    let errors = ref([]);
    const changePreset = (event) => {
      bannedReason.value = event.target.value;
      isVisible.value = bannedReason.value === '';
      errors.value = [];
    }

    const banUnbanUser = async () => {
      errors.value = [];
      const { error, data } = await http.post(props.url, { bannedReason: bannedReason.value });
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
      bannedReason,
      banUnbanUser,
      changePreset,
      errors,
      isVisible,
    }
  }
});
</script>
