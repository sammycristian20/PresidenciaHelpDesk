import { FaveoAutomator } from 'helpers/AutomatorUtils'
import { boolean } from 'helpers/extraLogics'

const state = {
    automator: null,
}

const getters = {

    getAutomatorData: (state) => state.automator,

    getAutomatorDataByKey: (state) => {
        return function (key) {
            return state.automator[key]
        }
    },

    getAutomatorRules: (state) => {
        return state.automator.rules
    },

    getAutomatorActions: (state) => {
        return state.automator.actions
    },

    getSelectedActionKeys : (state) => {
        return state.automator.actions.map((action)=>{
            return action.field ? action.field.unique : '';
        })
    }
}

const mutations = {

    createNewAutomatorInstance: (state, data) => state.automator = data ? data : new FaveoAutomator(),

    destroyAutomatorInstance: (state) => state.automator = null,

    setAutomatorDataByKey: (state, { key, value }) => state.automator[key] = value,

    addNewElementToAutomatorProperty: (state, { key, value }) => state.automator[key].push(value),

    deleteElementFromAutomator: (state, { key, index }) => {
        if (typeof index === 'undefined') {
            console.error(`Can't delete ${key} having index undefined`)
            return;
        }

        state.automator[key + 's'].splice(index, 1);
    },

    onCategoryChange: (state, { key, index, value }) => state.automator[key][index] = value,

    onRelationChange: (state, { key, index, relation }) => state.automator[key][index].relation = relation,

    onFieldChange: (state, { key, index, field }) => {

        if (!boolean(field)) {
            state.automator[key][index].field = null;
        } else {
            let clonedField = JSON.parse(JSON.stringify(field));
            clonedField._id = state.automator[key][index]._id;
            state.automator[key][index].field = clonedField;
        }

        state.automator[key][index][key] = [];
        state.automator[key][index].value = null;
    },

    updateEventData: (state, { index, value }) => state.automator.events[index] = value,

    updateEnforcerList: (state, { key, affectedUnique, formFieldUnique, enforcerList, value, isMultiple, itemToRemove }) => {
        const enforcerType = key + 's';

        findAndReplaceNewElement(state.automator[enforcerType])

        function findAndReplaceNewElement(array) {

            for (let i = 0; i < array.length; i++) {
                // _id gets assigned the id of the rule as soon as data arrives
                // so, when _id meets the affected rule's id and unique matches (we are matching unique to tackle the nesting)
                if (array[i]._id === affectedUnique && array[i].field.unique === formFieldUnique) {
                    if (boolean(itemToRemove)) {
                        removeChildFields(itemToRemove, array[i][enforcerType]);
                    } else {
                        if (isMultiple) {
                              array[i][enforcerType].push(...enforcerList);
                        } else {
                              array[i][enforcerType] = enforcerList;
                        }
                    }
                    array[i].value = value;
                    break;
                }
                if (boolean(array[i][enforcerType])) {
                    findAndReplaceNewElement(array[i][enforcerType])
                }
            }
        }

        /**
         * Removes all the nodes present inside automatorChild
         * @param removedOption
         * @param automatorChild
         */
        function removeChildFields(removedOption, automatorChild) {
            if (removedOption.nodes) {
                // all the nodes inside itemToRemoved must be removed
                removedOption.nodes.forEach(childNode => {
                    const indexToRemove = automatorChild.findIndex(el => el.field.unique === childNode.unique);
                    if (indexToRemove > -1) {
                        automatorChild.splice(indexToRemove, 1);
                    }
                });
            }
        }
    },

    updateActionEmail: (state, { index, key, value }) => state.automator.actions[index].action_email[key] = value,

}

const actions = {

    createNewAutomatorInstance: ({ commit }, data) => commit('createNewAutomatorInstance', data),

    destroyAutomatorInstance: ({ commit }) => commit('destroyAutomatorInstance'),

    setAutomatorDataByKey: ({ commit }, data) => commit('setAutomatorDataByKey', data),

    addNewElementToAutomatorProperty: ({ commit }, data) => commit('addNewElementToAutomatorProperty', data),

    deleteElementFromAutomator: ({ commit }, data) => commit('deleteElementFromAutomator', data),

    onCategoryChange: ({ commit }, data) => commit('onCategoryChange', data),

    onRelationChange: ({ commit }, data) => commit('onRelationChange', data),

    onFieldChange: ({ commit }, data) => commit('onFieldChange', data),

    updateEventData: ({ commit }, data) => commit('updateEventData', data),

    updateEnforcerList: ({ commit }, data) => commit('updateEnforcerList', data),

    removeElementFromEnforcerList: ({ commit }, data) => commit('removeElementFromEnforcerList', data),

    updateActionEmail: ({ commit }, data) => commit('updateActionEmail', data),

}

export default { state, getters, mutations, actions }

