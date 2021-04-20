import {boolean, lang, MULTIPLE_PROPERTY_HELPER} from 'helpers/extraLogics'
import {getFormFieldValue, getValueToSubmit} from 'helpers/formUtils'

const RELATION_LIST = [
  { id: 'equal', name: lang('equal_to') },
  { id: 'not_equal', name: lang('not_equal_to') },
  { id: 'contains', name: lang('contains') },
  { id: 'dn_contains', name: lang('does_not_contain') },
  { id: 'starts', name: lang('starts_with') },
  { id: 'ends', name: lang('ends_with') },
];

export class FaveoAutomator {
    constructor(
        id = 'automator__' + Math.random().toString(),
        rules = [],
        actions = [],
        events = [],
        matcher = 'any',
        actionsPerformer = 'agent'
    ) {
        this.id = id;
        this.rules = rules;
        this.actions = actions;
        this.events = events;
        this.matcher = matcher;
        this.actionsPerformer = actionsPerformer;
    }
}

export class Rule {
    constructor(
        _id = 'rule__' + Math.random().toString(),
        category = 'ticket',
        field = null,
        value = null,
        relation = RELATION_LIST[0],
        rules = [],
    ) {
        this._id = _id;
        this.category = category;
        this.field = field;
        this.value = value;
        this.relation = relation;
        this.rules = rules;
    }
}

export class Action {
    constructor(
        _id = 'action__' + Math.random().toString(),
        field = null,
        value = null,
        actions = [],
        action_email = { id: null, subject: '', body: '', users: [] }
    ) {
        this._id = _id;
        this.field = field;
        this.value = value;
        this.actions = actions;
        this.action_email = action_email
    }
}


export class Event {
    constructor(
        _id = 'event__' + Date.now(),
        field = null,
        to = null,
        from = null,
        type = null,
        api_info = null
    ) {
        this._id = _id;
        this.field = field;
        this.to = to;
        this.from = from;
        this.type = type;
        this.api_info = api_info;
    }
}

/**
 * @param {Array} array List of rules/actions/...
 * @param {String} type reule or actions or ...
 *
 * This will return list of Enforcers in the format we want to render/mount our enforcer list components
 */
export const getEnforcerInstanceList = (array = [], type) => {
    let list = [];
    array.forEach((item) => list.push(bindNestedObjects(type, item)));
    return list;
}

// Bind nested object if any to the enforcer list
const bindNestedObjects = (type, nestedObj) => {

    let child = [];

    const selectedOption = getFormFieldValue(nestedObj.value, nestedObj.options, nestedObj.default);

    if (Array.isArray(selectedOption)) {
        selectedOption.forEach(el => {
            el.nodes && el.nodes.forEach((node) => {
                child.push(bindNestedObjects(type, node))
            })
        })
    } else {
      // if selectedOption is not falsy and having nodes array then bind object as child
        selectedOption && selectedOption.nodes && selectedOption.nodes.forEach((node) => {
            child.push(bindNestedObjects(type, node))
        })
    }

    return getNewEnforcerInstance(type, nestedObj, child);

}

export const getNewEnforcerInstance = (type, item, child = []) => {
    if (type === 'rule') {
        const relationObj = RELATION_LIST.find((relation) => relation.id === item.relation);
        return new Rule(item.rule_id, item.category, item, item.value, relationObj, child)
    } else if (type === 'action') {
        return new Action(item.action_id, item, item.value, child, item.action_email)
    }
}

export const getEvenInstancetList = (events = []) => {
    let eventList = [];
    events.forEach((item) => eventList.push(new Event(item.event_id, { id: item.id, name: item.name }, item.to, item.from, item.type, item.api_info)));
    return eventList;
}

export const getEventDataForSubmit = (events = []) => {
    let clonedData = JSON.parse(JSON.stringify(events));
    events.forEach((item, index) => {
        ['from', 'to', 'field'].forEach((prop) => {
            clonedData[index][prop] = item[prop] ? item[prop].id : null;
        })
    })

  return clonedData;
}

export const getEnforcerDataByPropertyForSubmit = (data = [], property) => {
    let clonedData = JSON.parse(JSON.stringify(data));

    clonedData = assignNewValue(clonedData, property);

    function assignNewValue(array, property)
    {
        array = array.filter(element => (element.field));

        array.forEach(element => {
            const optionLabel = MULTIPLE_PROPERTY_HELPER.convertStringOfPropertiesToObject(element.field.api_info).key || 'label';
            element.value = getValueToSubmit(element.value, element.field.default, optionLabel);
            element.field = element.field.unique;
            if (property === 'rules') {
                element.relation = element.relation.id;
            }
            if (property === 'actions') {
                element.action_email = getActionEmailObjectForSubmit(element.action_email);
            }
            boolean(element[property]) && (element[property] = assignNewValue(element[property], property));
        })
        return array;
    }

    return clonedData
}

const getActionEmailObjectForSubmit = (actionEmail) => {
    if (!boolean(actionEmail.body)) {
        return };

    return {
        id: actionEmail.id,
        body: actionEmail.body,
        subject: actionEmail.subject,
        user_ids: actionEmail.users.map(user => user.id)
    };
}

export const isLocalElement = (id) => {
    return typeof id === 'string' && id.includes('__')
}

export const getRelationList = (type = '') => {
    const simpleFields = ['text', 'number', 'date', 'email', 'file', 'textarea', 'htmltextarea']

    return simpleFields.indexOf(type) > -1 ? RELATION_LIST : [RELATION_LIST[0], RELATION_LIST[1]]
}
