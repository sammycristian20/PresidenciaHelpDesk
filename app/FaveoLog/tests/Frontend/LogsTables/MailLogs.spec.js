import { mount, shallow } from '@vue/test-utils';

import Vue from 'vue';

import moment from 'moment';

Vue.use(moment);

import MailLogs from 'components/LogsTables/MailLogs.vue';

let wrapper;

describe('MailLogs',()=>{

	const updateWrapper = ()=>{

		wrapper = mount(MailLogs,{

			mocks : { lang : (string)=> string },

			stubs : ['dynamic-select', 'logs-table', 'date-time-field']
		})
	};

	beforeEach(()=>{

		updateWrapper();
	});

	it('is a vue instance',()=>{

		expect(wrapper.isVueInstance()).toBeTruthy();
	});

	it('updates `sender_user_ids` value of the mail logs when onChange method is called with suitable parameters for sender_user_ids',()=>{

    	wrapper.vm.onChange([1,2,3], 'sender_user_ids');

    	expect(wrapper.vm.sender_user_ids).toEqual([1,2,3]);
  	});

  	it('updates `receiver_user_ids` value of the mail logs when onChange method is called with suitable parameters for receiver_user_ids',()=>{

    	wrapper.vm.onChange([4,5,6], 'receiver_user_ids');

    	expect(wrapper.vm.receiver_user_ids).toEqual([4,5,6]);
  	});

  	it('shows mail datatable when page mounted',()=>{

  		expect(wrapper.find('logs-table-stub').exists()).toBe(true);
  	})

		it('passes correct API endpoint to dynamic select',()=>{
			const senderBoxAttribites = wrapper.find('#sender-select-box').attributes();
  		expect(senderBoxAttribites.apiendpoint).toBe('/api/dependency/users?meta=true&supplements=true')

			const recieverBoxAttribites = wrapper.find('#reciever-select-box').attributes();
  		expect(senderBoxAttribites.apiendpoint).toBe('/api/dependency/users?meta=true&supplements=true')
  	})
});
