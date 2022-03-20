<template>
  <a href="#" class="btn btn-sm btn-dark" role="button" data-bs-toggle="modal" data-bs-target="#modal">
    <span v-if="action === 'ban'">
      Ban user
    </span>
    <span v-else>
      Unban user
    </span>
  </a>
  <ban-user-dialog :url="url" :action="action"></ban-user-dialog>
</template>

<script>
import BanUserDialog from "../dialog/BanUserDialog";
import { defineComponent } from "vue";

export default defineComponent({
  name: "ban-user-button",
  components: { BanUserDialog },
  props: {
    url: { type: String },
    action: { type: String },
  },
});
</script>
