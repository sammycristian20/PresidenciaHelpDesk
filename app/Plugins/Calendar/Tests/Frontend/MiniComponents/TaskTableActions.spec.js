import { mount ,createLocalVue} from '@vue/test-utils'

import TaskTableActions from '../../../views/js/components/MiniComponents/TaskTableActions'

import Vuex from 'vuex'

let localVue = createLocalVue()

localVue.use(Vuex)

let wrapper;

let store;

let actions;

actions = { unsetValidationError: jest.fn() }

store = new Vuex.Store({ actions })

describe('TaskTableActions',()=>{

	beforeEach(()=>{

		wrapper = mount(TaskTableActions,{
				
			propsData : {
					
				data : {componentName:'TaskList', is_default : false}
			},
				
			mocks : { trans:(string)=> string },
				
			stubs:['delete-modal','task-modal','task-list-modal'],

			localVue, store
		});
	});


	it('`ShowModal` should be false when `onClose` method is called',()=>{
		
		wrapper.setData({ showModal : true })

		wrapper.vm.onClose()
		
		expect(wrapper.vm.$data.showModal).toBe(false)
	});
});