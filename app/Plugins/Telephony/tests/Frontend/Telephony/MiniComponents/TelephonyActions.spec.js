import { mount, createLocalVue } from '@vue/test-utils';

import TelephonyActions from '../../../../views/js/components/MiniComponents/TelephonyActions';

import Vue from 'vue';

import Vuex from 'vuex';

const localVue = createLocalVue();

localVue.use(Vuex);

describe('TelephonyActions',()=>{

	let wrapper;

	let actions = {
	  
	  unsetValidationError: jest.fn()
	}

	let store = new Vuex.Store({
	  
	  actions
	})

	const updateWrapper = () =>{

		wrapper = mount(TelephonyActions,{
			
			stubs: ['telephony-settings-modal', 'telephony-edit-modal'],
		
			mocks:{ lang:(string)=>string },
			
			propsData : {

				data : { id : 1, name : 'test', short : 'test', base_url : 'url'}
			},

			store
		})  
	}

	beforeEach(()=>{

		updateWrapper();
	})

	it("is vue instance",()=>{

		expect(wrapper.isVueInstance()).toBeTruthy();
	});

	it("updates `showEditModal, showSettingsModal` value when `onClose` method called",()=>{

		wrapper.vm.onClose();

		expect(wrapper.vm.showEditModal).toBe(false);

		expect(wrapper.vm.showSettingsModal).toBe(false);

		expect(actions.unsetValidationError).toHaveBeenCalled()
	})
})