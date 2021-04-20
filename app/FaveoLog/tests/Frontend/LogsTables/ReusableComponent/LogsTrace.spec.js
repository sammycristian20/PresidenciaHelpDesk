import { mount } from '@vue/test-utils'

import LogsTrace from 'components/LogsTables/ReusableComponent/LogsTrace.vue'

import moxios from 'moxios'

let wrapper;

describe('LogsTrace',()=>{

	beforeEach(()=>{

		wrapper = mount(LogsTrace,{
			stubs:['logs-modal'],
			propsData : {
				data : {trace:'text'}
			},
			mocks:{
            	lang: (string) => string,
        	}
		});
	});


	it('makes read more button enabled when page created',()=>{

		expect(wrapper.find('span').exists()).toBe(true);
	});

	it('does not show logs modal popup if showModal is false',() => {
		wrapper.vm.$data.showModal = false;
		expect(wrapper.find('logs-modal-stub').exists()).toBe(false)
	});

	it('show logs modal popup if showModal is true',() => {
		wrapper.vm.$data.showModal = true;
		expect(wrapper.find('logs-modal-stub').exists()).toBe(true)
	});

});
