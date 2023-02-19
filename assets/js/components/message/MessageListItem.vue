<script setup>
import {http} from "../../http.js"
import {ref} from "vue"

const props = defineProps({
  message: {type: Object},
  appUser: {type: Object},
})
const state = ref(props.message.state)
const changeState = async () => {
  const {data} = await http.post(props.message.urls.changeState, {shortId: props.message.shortId})
  state.value = data.state;
};
</script>

<template>
  <div class="d-flex px-2 pt-2">
    <div class="flex-column px-1">
      <div>
        <a :href="message.sender.url" class="me-1">
          <img class="avatar-sm" :src="message.sender.avatarPath" alt=""/>
        </a>
        <small>
          <a :href="message.sender.url" class="text-decoration-none hoverable">
            {{ message.sender.username }}
          </a>
          {{ message.createdAt }}
        </small>
      </div>
    </div>
    <div class="ps-3">
      <a :href="message.urls.show" class="me-1 text-decoration-none">
        {{ message.subject }}
      </a>
    </div>
    <div class="ps-3">
      <a :href="message.urls.toggleState" @click.prevent="changeState()" class="text-decoration-none" style="cursor: pointer;">
        <i v-if="state === 'UNREAD'" class="bi bi-envelope"></i>
        <i v-else class="bi bi-envelope-open"></i>
      </a>
    </div>
  </div>
</template>