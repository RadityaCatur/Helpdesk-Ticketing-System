<template>
  <div class="card">
    <div v-if="$slots.image" class="card-image">
      <slot name="image" />
    </div>
    <div v-if="$slots.header" class="card-header">
      <slot name="header" />
    </div>
    <div class="card-body">
      <slot />
    </div>
    <div v-if="$slots.footer" class="card-footer">
      <hr>
      <slot name="footer" />
    </div>
  </div>
</template>
<script>
export default {
  name: 'Card',
};
</script>
<style>
</style>
