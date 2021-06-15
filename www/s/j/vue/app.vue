<!--suppress HtmlUnknownTag, JSUnresolvedVariable, JSUnusedLocalSymbols -->
<div id="appTemplate">
<div>
  <h3 class="title is-5">Vue application</h3>

  <button class="button is-primary" @click="logTest">Test log</button>

  <log-panel :title="'Log panel component'"
             @row:delete="onRowDelete"
  ></log-panel>
</div>
</div>

<!--suppress JSUnusedGlobalSymbols -->
<script>

document.addEventListener("DOMContentLoaded", function () {

  const app = Vue.createApp({

    data: function () {
      return {
        logTestCounter: 0,
      }
    },

    mounted: function () {
      console.log('App mounted');
    },

    methods: {

      onRowDelete: function (e) {
        appState.log.rm(e.id);
      },

      logTest: function () {
        this.logTestCounter++;
        appState.log.wr('Test ' + this.logTestCounter);
      },
    },


  });

  app.component('log-row', LogRowComponent);
  app.component('log-panel', LogPanelComponent);
  app.mount('#appTemplate');
});

</script>
<style>


</style>
