'use strict';
/**
 * This store is used to control(for now) telephonic popup behaviour
 * May be used as a STACK for handling data
 */

const state = {
	list: []
};

const getters = {
	getItems: (state) => state.list
};

const mutations = {

	addUpdateElement(state, data) {
		let isNewItem = true;

		for (let i = 0; i < state.list.length; i++) {
			if (state.list[i].id === data.id) {
				clearTimeout(state.list[i].timer.timerId);
				isNewItem = false;
				state.list[i].data = data.data;
				state.list[i].status = data.status;
				state.list[i].timer = data.timer;
				break;
			}
		}

		if (isNewItem) {
			state.list.push(data);
		}

		if (state.list.length > 3) {
			state.list.shift()
		}
	},

	removeElement(state, item) {
		clearTimeout(item.timer);
		state.list = state.list.filter(v => v.id !== item.id)
	},

	clearAll(state) {
		state.list = [];
	}

};

const actions = {

	addUpdateElement({ commit }, data) {
		commit('addUpdateElement', data);
	},

	removeElement({ commit }, item) {
		commit('removeElement', item);
	},

	clearAll({ commit }) {
		commit('clearAll')
	}

}

export default { state, getters, mutations, actions };