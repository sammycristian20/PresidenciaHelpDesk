import { mount, shallow,createLocalVue } from '@vue/test-utils';
import LdapTable from './../../views/js/components/LdapTable'
import moxios from 'moxios'
import sinon from 'sinon'
import Vue from 'vue'
import Vuex from 'vuex'
window.axios = require('axios');
window.$ = window.jQuery = require('jquery');
window.axios.defaults.baseURL = document.head.querySelector('meta[name="api-base-url"]');
window.performance.navigation = new Vue();
let wrapper;

const fakeRequestData = {
		'success':true,
		'data':{}
}

const localVue = createLocalVue()
localVue.use(Vuex);

describe('LdapTable', () => {

	let store
	let actions

	beforeEach(() => {
		actions = {
			unsetValidationError: () => {return true},
		}

		store = new Vuex.Store({
			actions,
		})

		moxios.install()

		moxios.stubRequest('/api/dependency/ldap-directory-attributes/',{
			status:200,
			response:fakeRequestData
		})
	})

	const initializeComponent = () => {
		wrapper = mount(LdapTable,{
		stubs:['data-table','data-table-actions','data-table-modal','alert','tool-tip'],
			mocks:{
				lang: (string) => string,
			},store
		})
	}

	it('is a vue instance', () => {

		initializeComponent();
			expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('does not show  modal popup if `showModal` is false',()=>{
		initializeComponent()
		expect(wrapper.find('data-table-modal-stub').exists()).toBe(false);
	});

	it('show  modal popup if `showModal` is true',()=>{
		initializeComponent()
		wrapper.vm.showModal = true
		expect(wrapper.find('data-table-modal-stub').exists()).toBe(true);
	});

	it('set `showModal` value is false when `onClose` method called',()=>{
		initializeComponent()
		wrapper.vm.onClose()
		expect(wrapper.vm.$data.showModal).toBe(false);
	});
})
