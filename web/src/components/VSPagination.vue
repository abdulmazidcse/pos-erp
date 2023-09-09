<template>
  <div>
    <div class="pagination-row">
      <button class="btn btn-light btn-sm pagination-button">&lt;&lt;</button>
      <button class="btn btn-light btn-sm pagination-button">&lt;</button>
      <span v-for="(item, index) in new Array(10)" :key="index">
        <button class="btn btn-light btn-sm pagination-button">{{ index + 1 }}</button>
      </span>
      <button class="btn btn-light btn-sm pagination-button">&gt;</button>
      <button class="btn btn-light btn-sm pagination-button">&gt;&gt;</button>
    </div>
    <slot name="data" :pageNumber="pageNumber" />
  </div>
</template>

<script>

export default {
  data() {
    return {
      pageNumber: 1,
    }
  }
};
</script>

<style scoped>

.pagination-row {
  padding: 5px;
  text-align: center;
}
.pagination-button {
  margin: 2px;
  padding: 6px 10px;
}

</style>