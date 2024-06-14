<template>
  <div class="form-group" :class="{'input-group': hasIcon}">
    <slot name="label">
      <label v-if="label" class="control-label">
        {{ label }}
      </label>
    </slot>
    <slot name="addonLeft">
      <span v-if="addonLeftIcon" class="input-group-addon">
        <i :class="addonLeftIcon" />
      </span>
    </slot>
    <input
      :value="value"
      v-bind="$attrs"
      class="form-control"
      aria-describedby="addon-right addon-left"
      @input="$emit('input',$event.target.value)"
    >
    <slot />
    <slot name="addonRight">
      <span v-if="addonRightIcon" class="input-group-addon">
        <i :class="addonRightIcon" />
      </span>
    </slot>
  </div>
</template>
<script>
export default {
  name: 'FgInput',
  inheritAttrs: false,
  props: {
    label: String,
    value: [String, Number],
    addonRightIcon: String,
    addonLeftIcon: String,
  },
  computed: {
    hasIcon() {
      const { addonRight, addonLeft } = this.$slots;
      return addonRight !== undefined || addonLeft !== undefined || this.addonRightIcon !== undefined || this.addonLeftIcon !== undefined;
    },
  },
};
</script>
<style>

</style>
