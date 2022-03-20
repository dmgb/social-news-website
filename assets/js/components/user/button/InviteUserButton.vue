<template>
  <a href="#" class="btn btn-sm btn-dark" role="button" data-bs-toggle="modal" data-bs-target="#modal">
      Invite user
  </a>
  <invite-user-dialog :url="url"></invite-user-dialog>
</template>

<script>
import InviteUserDialog from "../dialog/InviteUserDialog";
import { defineComponent } from "vue";

export default defineComponent({
  name: "invite-user-button",
  components: { InviteUserDialog },
  props: {
    url: { type: String },
  },
});
</script>
