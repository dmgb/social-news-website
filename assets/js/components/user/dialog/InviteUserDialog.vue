<template>
  <div class="modal fade pe-1" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">
            <i class="bi bi-person-plus-fill"></i>
            Invite user
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post" @submit.prevent="inviteUser">
            <div class="row mb-2">
              <div class="col">
                <label for="email">Email address</label>
              </div>
            </div>
            <input v-model="email" class="form-control" id="email" type="email" required/>
            <template v-if="errors.length > 0" v-for="(error, key) in errors" :key="key">
              <div class="invalid-feedback d-block">{{ error }}</div>
            </template>
            <button class="btn btn-dark me-2 my-3" type="submit">Invite user</button>
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
  name: "invite-user-dialog",
  props: {
    url: {type: String},
  },
  setup(props) {
    let email = ref('');
    let errors = ref([]);
    const inviteUser = async () => {
      console.log(email.value);
      errors.value = [];
      const {error, data} = await http.post(props.url, {email: email.value});
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
      email,
      errors,
      inviteUser,
    }
  }
});
</script>
