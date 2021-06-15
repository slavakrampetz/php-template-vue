<!--suppress JSUnresolvedFunction -->
<template id="LogRowTemplate">
  <div class="-row">
    <div class="-id">
      {{row.id}}
    </div>
    <div class="-text">
      {{row.text}}
    </div>
    <div class="-act">
      <button class="button is-small is-white" @click="onClick(row.id)">
        <span class="icon">
          <i class="la la-trash"></i>
        </span>
      </button>
    </div>
  </div>
</template>
<script>
const LogRowTemplate = '#LogRowTemplate';

const LogRowComponent = {

  template: LogRowTemplate,

  emits: ['row:delete'],

  props: {
    row: {
      type: Object,
      required: true,
      default: {
        id: 0,
        text: '',
      }
    }
  },

  methods: {
    onClick: function (id) {
      this.$emit('row:delete', { id });
    }
  }
}
</script>
