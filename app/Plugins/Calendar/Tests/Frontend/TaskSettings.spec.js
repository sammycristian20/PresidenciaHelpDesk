import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import Vue from 'vue'

import TaskSettings from '../../views/js/components/TaskSettings.vue';

describe('TaskSettings',() => {

	let wrapper;

	const updateWrapper = () =>{
		
		wrapper = mount(TaskSettings,{

			stubs: ['task-project','task-list','alert'],
			
			mocks:{ lang:(string)=>string },
		})  
	}
	
	beforeEach(() => {
		
		updateWrapper();
	})

	it('is vue instance',() => {		
		
		expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it("updtaes `category` when `associates` method called",()=>{

		updateWrapper();

		wrapper.vm.associates('assets');
		
		expect(wrapper.vm.category).toEqual('assets')
	})
})