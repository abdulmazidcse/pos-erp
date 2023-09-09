<template>
    <div class="card">
      <div class="card-content">
    </div>    
    </div>
</template>
<script>
import {mapActions} from 'vuex'
export default {
  name: "ProductListItem",
  props: ["productItem"],
  methods: {
    ...mapActions(["addCartItem"]),
  },
};
</script>