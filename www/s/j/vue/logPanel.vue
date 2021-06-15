<!--suppress HtmlUnknownTag, JSUnresolvedVariable, JSUnusedLocalSymbols -->
<template id="LogPanelTemplate">
  <div class="log-panel">

    <div class="-title">
      <h6 class="title is-6">{{title}}</h6>
    </div>

    <div class="-log" v-if="this.hasRows">
      <log-row v-for="r in rows"
               :key="r.id"
               :row="r"
               @row:delete="onRowDelete"
      ></log-row>
    </div>
    <div class="-log-empty" v-else>
      No log rows yet.
    </div>
  </div>

</template>

<script defer>

const LogPanelTemplate = '#LogPanelTemplate';

const LogPanelComponent = {
  template: LogPanelTemplate,

  emits: ['row:delete'],

  props: {
    title: {
      type: String,
      default: '',
      required: false,
    },
  },

  setup(props) {
    return {
      rows: appState.log.rows,
    }
  },

  computed: {
    hasRows: function () {
      return this.rows.length > 0;
    }
  },

  methods: {
    onRowDelete: function (e) {
      this.$emit('row:delete', e);
    }
  }
}

</script>
<!--suppress CssUnusedSymbol -->
<style>
.log-panel {
  margin-top: 1rem;
  border: solid 1px #f0f0f0;
  padding: .5rem;
}


.log-panel .-title {
  margin: -.5rem -.5rem .5rem -.5rem;
  padding: .5rem .5rem;
  background: #f0f0f0;
}

.log-panel .-log {
  font-size: small;
  display: flex;
  flex-direction: column;
  margin: -.5rem -.5rem;
}

.log-panel .-log .-row {
  border-bottom: solid 1px #f0f0f0;
  padding: 1px 0;
  display: flex;
  align-items: center;
}

.log-panel .-log .-row:last-child {
  border-bottom: none;
}

.log-panel .-log .-row .-id {
  flex-basis: 2rem;
  text-align: right;
  margin-right: .5rem
}

.log-panel .-log .-row .-text {
  flex-grow: 2;
}

.log-panel .-log .-row .-act {
  display: flex;
  margin-left: .5rem
}
</style>
