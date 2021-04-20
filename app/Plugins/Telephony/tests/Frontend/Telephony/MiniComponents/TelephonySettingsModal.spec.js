import { mount } from '@vue/test-utils';

import TelephonySettings from '../../../../views/js/components/MiniComponents/TelephonySettingsModal';

import moxios from 'moxios';

describe('TelephonySettings',()=>{

	let wrapper;

	const updateWrapper = () =>{

		wrapper = mount(TelephonySettings,{
			
			stubs: ['modal', 'loader', 'text-field', 'dynamic-select'],
		
			mocks:{ lang:(string)=>string },
			
			propsData : {

				showModal : true,

				onClose : jest.fn(),

				data : { id : 1, name : 'test', short : 'test', base_url : 'url'}
			}
		})  
	}

	beforeEach(()=>{

		updateWrapper();

		moxios.install();
	});

	afterEach(()=>{

		moxios.uninstall();
	})

	it("is vue instance",()=>{

		expect(wrapper.isVueInstance()).toBeTruthy();
	});

	it("updates `dept_url` value when `onChange` method called with `department`",()=>{

		wrapper.vm.onChange({ id : 1, name : 'dept' }, 'department');
	
		expect(wrapper.vm.department).toEqual({ id : 1, name : 'dept' });

		expect(wrapper.vm.dept_url).toEqual('url/1/department')

		wrapper.vm.onChange('', 'department');
	
		expect(wrapper.vm.department).toEqual('');

		expect(wrapper.vm.dept_url).toEqual('url')
	})

	it("updates `topic_url` value when `onChange` method called with `helptopic`",()=>{

		wrapper.vm.onChange({ id : 1, name : 'topic' }, 'helptopic');
	
		expect(wrapper.vm.helptopic).toEqual({ id : 1, name : 'topic' });

		expect(wrapper.vm.topic_url).toEqual('url/1/helptopic')

		wrapper.vm.onChange('', 'helptopic');
	
		expect(wrapper.vm.helptopic).toEqual('');

		expect(wrapper.vm.topic_url).toEqual('url')
	})
})