import { mount, shallow } from '@vue/test-utils';

import Vue from 'vue';

import CronLogs from 'components/LogsTables/CronLogs.vue';

let wrapper;

describe('CronLogs',()=>{

	const updateWrapper = ()=>{

		wrapper = mount(CronLogs,{

			methods : { trans : (string)=> string },

			stubs : ['date-time-field', 'logs-table'],

			mocks: {
				lang: (string) => string,
			}
		})
	};

	beforeEach(()=>{

		updateWrapper();
	});

	it('is a vue instance',()=>{

		expect(wrapper.isVueInstance()).toBeTruthy();
	});

	it('updates `cron_created_date` value of the cron logs when onChange method is called with suitable parameters for cron_created_date',()=>{

    	wrapper.vm.onChange([{0:'10-10-2018'},{1:'15-10-2019'}], 'cron_created_date');

    	expect(wrapper.vm.cron_created_date).toEqual([{"0": "10-10-2018"}, {"1": "15-10-2019"}]);
  	});

  	it('shows cron datatable when page mounted',()=>{

  		expect(wrapper.find('logs-table-stub').exists()).toBe(true);
  	})
})
