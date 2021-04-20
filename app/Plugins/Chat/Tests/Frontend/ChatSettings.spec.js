import ChatSettings from './../../views/js/components/ChatSettings';
import { shallowMount } from '@vue/test-utils';
import moxios from 'moxios'
import Vue from 'vue';


window.eventHub = new Vue()

const fakeRequestData = {
    'success':true,
    'data':{}
}

describe('ChatSettings', () => {

    let wrapper;
  
    beforeEach(() => {
      moxios.install()

     afterEach(() => {
       moxios.uninstall()
     })
  
      wrapper = shallowMount(ChatSettings,{
        mocks:{
            stubs:['data-table'],
            lang:(string)=>string
        },
        methods : {
          basePath : jest.fn()
        },
      })
  
    })

    it('Datatable should exists when page created', () => {
    
        expect(wrapper.find('data-table-stub').exists()).toBe(true)

    });

  })