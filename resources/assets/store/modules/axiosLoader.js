/**
 * Use single instance of loader for multiple axios calls on a page 
 * 
 * Uses:
 * 
 * TO START 
 * this.$store.dispatch('startLoader', this.$options.name);
 * 
 * TO STOP
 * this.$store.dispatch('stopLoader', this.$options.name);
 * 
 * TO FORCE STOP
 * this.$store.dispatch('forceStopLoader');
 * 
 */

const state = {
	axiosCallsStack: []
};


const getters = {

	/**
	 * Display loader if axiosCallsStack is not empty
	 */
	showLoader: state => state.axiosCallsStack.length > 0

};


const mutations = {

	/**
	 * Start loader with a unique instance
	 */
	startLoader(state, loaderInstance) {
		state.axiosCallsStack.push(loaderInstance);
  },
	
	/**
	 * Stop loader for a particular instance
	*/
  stopLoader(state, loaderInstance) {
		state.axiosCallsStack.splice(state.axiosCallsStack.indexOf(loaderInstance), 1);
	},

	/**
	 * Force stop the loader by clearing the axiosCallsStack
	 */
	forceStopLoader(state) {
		state.axiosCallsStack = [];
	}

};


const actions = {

	startLoader({ commit, state }, loaderInstance) {
		if(!loaderInstance) {
			throw new Error('Loader instance is required!');
		}
		else if(state.axiosCallsStack.includes(loaderInstance)) {
			throw new Error('Can\'t start loader with the same instance!');
		} else {
			commit('startLoader', loaderInstance);
		}
  },
  
  stopLoader({ commit, state }, loaderInstance) {
		if(!loaderInstance) {
			throw new Error('Loader instance is required!');
		}
		else if(!state.axiosCallsStack.includes(loaderInstance)) {
			throw new Error(`There is no loader started with this ${loaderInstance} instance!`);
		} else {
			commit('stopLoader', loaderInstance);
		}
	},

	forceStopLoader({ commit }) {
		commit('forceStopLoader')
	}

}

export default { state, getters, mutations, actions }