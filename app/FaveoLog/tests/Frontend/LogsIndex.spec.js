import { mount, shallow } from '@vue/test-utils';

import Vue from 'vue';

import LogsIndex from 'components/LogsIndex';

let wrapper;

describe('LogsIndex',()=>{

	const updateWrapper = ()=>{

		wrapper = mount(LogsIndex,{

			mocks : { lang : (string)=> string },

			stubs : ['alert', 'exception-logs', 'cron-logs', 'mail-logs', 'logs-modal']
		})
	};

	beforeEach(()=>{

		updateWrapper();
	});

	it('is a vue instance',()=>{

		expect(wrapper.isVueInstance()).toBeTruthy();
	});

	it('updates `category_ids` of the logs when onChange method is called with suitable parameters for category',()=>{

    	wrapper.vm.onChange({ "id":1,"name":"category"}, 'category_ids');

    	expect(wrapper.vm.category_ids).toEqual({"id": 1, "name": "category"});
  	});

  	it('all 3 datatables should be visible when mounted',()=>{

  		expect(wrapper.find('exception-logs-stub').exists()).toBe(true);

  		expect(wrapper.find('cron-logs-stub').exists()).toBe(true);

  		expect(wrapper.find('mail-logs-stub').exists()).toBe(true)
  	})
})
