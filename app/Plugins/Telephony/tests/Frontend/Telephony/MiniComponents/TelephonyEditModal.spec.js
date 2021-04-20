import { mount } from '@vue/test-utils';

import TelephonyEdit from '../../../../views/js/components/MiniComponents/TelephonyEditModal';

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

jest.mock('helpers/responseHandler')

describe('TelephonyEdit',()=>{

	let wrapper;

	const updateWrapper = () =>{

		extraLogics.findObjectByKey = () =>{return {}}

		wrapper = mount(TelephonyEdit,{
			
			stubs: ['modal', 'loader', 'alert', 'text-field', 'radio-button','dynamic-select'],
		
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

	it("makes an API call when `getValues` method called",(done)=>{

		wrapper.vm.getValues();

		expect(wrapper.vm.loading).toBe(true);

		expect(wrapper.vm.isDisabled).toBe(true);

		mockGetRequest(200);

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe('/telephony/api/get-provider-details/test')

			expect(wrapper.vm.loading).toBe(false);
			
			expect(wrapper.vm.isDisabled).toBe(false);

			done();
		},2000)
	})

	it("makes an `loading , isDisabled` false when `getValues` method returns error",(done)=>{

		wrapper.vm.getValues();

		expect(wrapper.vm.loading).toBe(true);

		expect(wrapper.vm.isDisabled).toBe(true);

		mockGetRequest(400);

		setTimeout(()=>{
			
			expect(wrapper.vm.loading).toBe(false);
			
			expect(wrapper.vm.isDisabled).toBe(false);

			done();
		},1)
	})

	it("makes an POST API call when `onSubmit` method called",(done)=>{

		wrapper.setData({ iso : true});

		wrapper.vm.onSubmit();

		wrapper.setProps({ data : { short : 'test'}});

		mockSubmitRequest(200);

		setTimeout(()=>{

			expect(moxios.requests.mostRecent().url).toBe('/telephony/api/update-provider-details/test')

			expect(moxios.requests.mostRecent().config.method).toEqual('post')

			expect(wrapper.vm.loading).toBe(false);
			
			expect(wrapper.vm.isDisabled).toBe(false);

			done();
		},1)
	});

	function mockGetRequest(status = 200, url = '/telephony/api/get-provider-details/test'){

		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response: {

				data : [
					{ key : 'token', value : 'value'},
					{ key : 'log_miss_call', value : '0'},
					{ key : 'iso', value : { iso : 'in', name : 'name'}}
				]
			}
		})
	}

	function mockSubmitRequest(status = 200,url = '/telephony/api/update-provider-details/test'){

		moxios.uninstall();
		
		moxios.install();
		
		moxios.stubRequest(url,{
			
			status: status,
			
			response: {'success':true,'message':'updated'}
		})
	}
})