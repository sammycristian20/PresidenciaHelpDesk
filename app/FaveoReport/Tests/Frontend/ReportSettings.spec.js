import {mount } from '@vue/test-utils'
import ReportSettings from './../../views/js/components/ReportSettings.vue';
import moxios from 'moxios';

let wrapper;

jest.mock('helpers/responseHandler', () => ({
	errorHandler: ()=> jest.fn(),
	successHandler: ()=> jest.fn()
}));

const JSDOMEnvironment = require('jest-environment-jsdom');

describe('ReportSettings',() => {

	
	const updateWrapper = () =>{
		wrapper = mount(ReportSettings,{
			stubs: ['alert','loader','static-select'],
			methods:{
				trans: (string) => string,
				isValid: () => true
			}
		})   
	}

	beforeEach(() => {
		updateWrapper();

		moxios.install();

		moxios.stubRequest('/api/report-settings',{
			status: 200,
			response: fakeResponse
		})
	})

	afterEach(() => {
		moxios.uninstall()
	})

	it('makes an API call on initial load to get data', (done) => {
		updateWrapper();
		wrapper.vm.getInitialValues();
		wrapper.vm.$nextTick(()=>{
			expect(moxios.requests.mostRecent().config.method).toBe('get')
			expect(moxios.requests.mostRecent().url).toBe('/api/report-settings')
			done();
		});
	});

	it('makes an API call for submitting the data when submit is clicked', (done) => {
		updateWrapper();
		wrapper.vm.$nextTick(()=>{
			wrapper.setData({isDisabled: false});
			wrapper.find("#submit_btn").trigger("click");
			wrapper.vm.$nextTick(()=>{
				expect(moxios.requests.mostRecent().config.method).toBe('post')
				expect(moxios.requests.mostRecent().url).toBe('/api/report-settings')
				done();
			});
		});
	});

	it('shows loader if loading is true', () => {
		wrapper.vm.$data.loading = true;
		expect(wrapper.find('loader-stub').exists()).toBe(true)
	});

	it('does not show loader if loading is false', () => {
		wrapper.vm.$data.loading = false;
		expect(wrapper.find('loader-stub').exists()).toBe(false)
	});


	let fakeResponse = {
			success:true,
			data:{
				data:{
					records_per_export: 100,
				}	
			}
	}
})