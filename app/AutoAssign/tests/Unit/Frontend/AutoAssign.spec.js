import { mount, shallow } from '@vue/test-utils';

import Vue from 'vue';

import AutoAssign from '../../../views/js/components/AutoAssign';

import moxios from 'moxios';

import * as validation from "../../../views/js/helpers/validator/autoAssignRules.js";

jest.mock('helpers/responseHandler')

let wrapper;

describe('AutoAssign',()=>{

	const updateWrapper = ()=>{

		wrapper = mount(AutoAssign,{

			mocks : { trans : (string)=> string },

			stubs : ['alert', 'loader', 'custom-loader', 'radio-button', 'number-field']
		})
	};

	beforeEach(()=>{

		updateWrapper();
		
		moxios.install();
	});

	afterEach(() => {
	
		moxios.uninstall()
	});

	it('makes an API call', (done) => {
			
		updateWrapper();
			
		wrapper.vm.getValues();
			
		expect(wrapper.vm.loading).toBe(true)

		stubRequest();
			
		setTimeout(()=>{
			
			expect(moxios.requests.mostRecent().url).toBe('/api/get-auto-assign')
				
			expect(wrapper.vm.loading).toBe(false)

			expect(wrapper.vm.hasDataPopulated).toBe(true)

			done();
		},50)
	});

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.getValues();

		stubRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.loading).toBe(false)

			done();
		},50)
	});

	it('updates state data correctly(according to the key) when `updateStatesWithData` is called',() => {
		
		var data = { threshold :'10' }
		
		wrapper.vm.updateStatesWithData(data);
		
		expect(wrapper.vm.threshold).toBe(10);
	});

	it('updates `username` value when onChange method is called',()=>{
	
		wrapper.vm.onChange(10, 'threshold');
		
		expect(wrapper.vm.threshold).toBe(10);
	});

	it('isValid - should return false ', done => {
       	
      validation.validateAutoAssignSettings = () =>{return {errors : [], isValid : false}}
      
      expect(wrapper.vm.isValid()).toBe(false)
      
      done()
   });

    it('isValid - should return true ', done => {
       
      validation.validateAutoAssignSettings = () =>{return {errors : [], isValid : true}}
      
      expect(wrapper.vm.isValid()).toBe(true)
      
      done()
   });

   it('makes an AJAX call when onSubmit method is called',(done)=>{

		updateWrapper()

		wrapper.vm.getValues = jest.fn();

		wrapper.setData({ loading : false, hasDataPopulated : true})

		wrapper.vm.isValid = () =>{return true}

		wrapper.vm.onSubmit()

		expect(wrapper.vm.pageLoad).toBe(true)

		mockSubmitRequest();

		setTimeout(()=>{
				
			expect(wrapper.vm.pageLoad).toBe(false);

			expect(wrapper.vm.getValues).toHaveBeenCalled();
				
			done();
		},1);
	});

	it("makes `loading` as false if api returns error response",(done)=>{

		updateWrapper();

		wrapper.vm.onSubmit();

		mockSubmitRequest(400);

		setTimeout(()=>{

			expect(wrapper.vm.pageLoad).toBe(false)

			done();
		},50)
	});

	function mockSubmitRequest(status = 200,url = '/api/auto-assign'){
	   
	   moxios.uninstall();
	   
	   moxios.install();
	   
	   moxios.stubRequest(url,{
	      
	      status: status,
	      
	      response : {}
	   })
	 }

	function stubRequest(status = 200,url = '/api/get-auto-assign'){
	   
	   moxios.uninstall();
	   
	   moxios.install();
	   
	   moxios.stubRequest(url,{
	     
	     status: status,
	     
	     response : {}
	   })
	 }
})
