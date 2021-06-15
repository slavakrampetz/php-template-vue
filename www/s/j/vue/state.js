
//
// Vue 3

// noinspection JSUnresolvedFunction
const rowsRef = Vue.ref([]);

// Global application state
let appState = {


	log: {
		rows: rowsRef,
		maxId: 0,

		/** Write text to log */
		wr: function (/*string*/ msg) {
			const next = appState.log.maxId + 1;
			appState.log.maxId = next;
			appState.log.rows.value.push({ id: next, text: msg});
		},

		/** Delete value by Id from array */
		rm: function (/*int*/ id) {
			const old = appState.log.rows.value;
			appState.log.rows.value = old.filter(
				value => value.id !== id
			)
		},
	}
};
