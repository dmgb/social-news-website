<template>
  <a href="#" data-bs-toggle="modal" data-bs-target="#modal">
    <img v-if="action === 'ban'" src="/build/images/ban.png" alt="" style="height: 2rem; width: auto;" />
    <img v-else src="/build/images/checked.png" alt="" style="height: 2rem; width: auto;" />
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
