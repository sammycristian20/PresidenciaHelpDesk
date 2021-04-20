import FacebookSettings from './../../views/js/components/FacebookSettings';
import { shallowMount } from '@vue/test-utils';
import moxios from 'moxios'
import Vue from 'vue';

window.eventHub = new Vue()

describe('FacebookSettings', () => {

    let wrapper;
  
    beforeEach(() => {
        moxios.install()

        wrapper = shallowMount(FacebookSettings,{
            mocks:{
                stubs:['data-table'],
                lang:(string)=>string,
                methods: {
                    redirect : jest.fn()
                }
            },
        })
  
    })

    it('is a vue instance', () => {
      
        expect(wrapper.isVueInstance()).toBeTruthy()

    });
  
    it('DataTable should exists when page created', () => {
    
        expect(wrapper.find('data-table-stub').exists()).toBe(true)

    });

})