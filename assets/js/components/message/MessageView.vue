<script setup>
import {http} from "../../http";
import {ref, watch} from "vue";
import {useRoute} from 'vue-router';

const props = defineProps({
  data: {type: Object},
})
const messages = ref(props.data)
const route = useRoute()
const url = new URL(window.location)
const activeLink = ref(url.pathname.split('/').at(-1))
watch(
    () => route.path,
    async path => {
      messages.value = await getData(path)
      activeLink.value = path.split('/').at(-1)
    }
)

const getData = async (path) => {
  const {data} = await http.post(path)

  return data.data
}
</script>

<template>
  <div class="row p-3">
    <div class="col-2">
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
          <router-link to="/messages/inbox"
                       class="nav-link"
                       :class="activeLink === 'inbox' ? ' active' : ''"
          >
            <img src="/build/images/email.png" alt="" class="me-1" style="height: 1.5rem; width: auto;"/>
            inbox
          </router-link>
        </li>
        <li>
          <router-link to="/messages/sent" class="nav-link" :class="activeLink === 'sent' ? ' active' : ''">
            <img src="/build/images/send.png" alt="" class="me-1" style="height: 1.5rem; width: auto;"/>
            sent
          </router-link>
        </li>
        <li>
          <router-link to="/messages/trash" class="nav-link" :class="activeLink === 'trash' ? ' active' : ''">
            <img src="/build/images/delete.png" alt="" class="me-1" style="height: 1.5rem; width: auto;"/>
            trash
          </router-link>
        </li>
      </ul>
    </div>
    <div class="col-8">
      <message-list-item v-for="message in messages" :message="message">
      </message-list-item>
    </div>
  </div>
</template>

<style scoped>

</style>