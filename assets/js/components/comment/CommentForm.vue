<template>
  <form @submit.prevent="addComment">
      <textarea v-model="body" class="form-control" rows="5" required>
      </textarea>
      <template v-if="errors.length > 0" v-for="(error, key) in errors" :key="key">
        <div class="invalid-feedback d-block">{{ error }}</div>
      </template>
      <button v-if="appUser" class="btn btn-dark my-3" type="submit">
        {{ submitButtonText }}
      </button>
      <a v-else class="btn btn-dark mt-3" :href="'/login'" type="button" style="color: #fff;">
        {{ submitButtonText }}
      </a>
  </form>
</template>

<script>
import { defineComponent, inject, ref } from "vue";
import { http } from "../../http";

export default defineComponent({
  name: "comment-form",
  emits: ["add-comment"],
  props: {
    action: { type: String },
    submitButtonText: { type: String, default: 'Add comment' },
  },
  setup(props, { emit }) {
    let body = ref('');
    let errors = ref([]);
    const appUser = inject('appUser');
    const addComment = async () => {
      const { error, data } = await http.post(props.action, { body: body.value });
      if (data.errors) {
        errors.value = data.errors;
      }

      if (data.comment) {
        emit('add-comment', {
          'comment': data.comment,
          'totalCount': data.totalCount,
        });
        body.value = '';
        errors.value = [];
      }

      if (error) {
        console.log(error);
      }
    }

    return {
      addComment,
      appUser,
      body,
      errors,
    }
  }
});
</script>
