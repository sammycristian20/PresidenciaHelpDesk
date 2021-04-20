import { mount, shallow } from '@vue/test-utils';

import Vue from 'vue';

import moment from 'moment';

Vue.use(moment)

import ExceptionLogs from 'components/LogsTables/ExceptionLogs.vue';

let wrapper;

describe('ExceptionLogs',()=>{

	const updateWrapper = ()=>{

		wrapper = mount(ExceptionLogs,{

			mocks : { lang : (string)=> string },

			stubs : ['logs-table', 'dynamic-select', 'date-time-field', ]
		})
	};

	beforeEach(()=>{

		updateWrapper();
	});

	it('is a vue instance',()=>{

		expect(wrapper.isVueInstance()).toBeTruthy();
	});

  	it('shows exception datatable when page mounted',()=>{

  		expect(wrapper.find('logs-table-stub').exists()).toBe(true);
  	})
})
