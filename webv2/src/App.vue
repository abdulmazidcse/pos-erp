<template> 
  <MasterTemplate :key="reRenderRoute" />
</template>

<script>
import MasterTemplate from './components/MasterTemplate.vue'
import {ref, onMounted, getCurrentInstance } from "vue";

export default {
  name: 'App',
  components: {
    MasterTemplate
  }
}
</script>


 
