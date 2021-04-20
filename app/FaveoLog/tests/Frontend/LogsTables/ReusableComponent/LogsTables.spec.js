import { mount, shallow } from '@vue/test-utils';

import LogsTable from 'components/LogsTables/ReusableComponent/LogsTable.vue'

import sinon from 'sinon'

import Vue from 'vue'

let wrapper;


const initializeComponent = () => {

	wrapper = mount(LogsTable,{

		stubs:['data-table'],
        mocks:{
            lang: (string) => string,
        }
	})
}




describe('LogsTable', () => {


	it('is a vue instance', () => {

		initializeComponent();
	    expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('data-table should exists when page created', () => {

        initializeComponent()

        expect(wrapper.find('data-table-stub').exists()).toBe(true)

    });
})
