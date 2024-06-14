<template>
  <table class="table">
    <thead>
      <slot name="columns">
        <th v-for="(column, index) in columns" :key="index">{{ column }}</th>
      </slot>
    </thead>
    <tbody>
      <tr v-for="(item, index) in data" :key="index">
        <slot :row="item">
          <td v-for="(column, index) in columns" v-if="hasValue(item, column)" :key="index">{{ itemValue(item, column) }}</td>
        </slot>
      </tr>
    </tbody>
  </table>
</template>
<script>
export default {
  name: 'LTable',
  props: {
    columns: Array,
    data: Array,
  },
  methods: {
    hasValue(item, column) {
      return item[column.toLowerCase()] !== 'undefined';
    },
    itemValue(item, column) {
      return item[column.toLowerCase()];
    },
  },
};
</script>
<style>
</style>
