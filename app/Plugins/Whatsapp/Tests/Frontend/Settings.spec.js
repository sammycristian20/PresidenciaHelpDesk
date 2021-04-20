import WhatsappSettings from './../../views/js/components/WhatsappSettings';
import { shallowMount } from '@vue/test-utils';
import moxios from 'moxios'
import Vue from 'vue';


window.eventHub = new Vue()

const fakeRequestData = {
    'success':true,
    'data':{}
}

describe('WhatsappSettings', () => {

    let wrapper;
  
    beforeEach(() => {
      moxios.install()

     afterEach(() => {
       moxios.uninstall()
     })
  
      wrapper = shallowMount(WhatsappSettings,{
        mocks:{
            stubs:['text-field','dynamic-select'],
            lang:(string)=>string
        },
        methods : {
          basePath : jest.fn()
        },
      })
  
    })

    it('is a vue instance', () => {
	  
        expect(wrapper.isVueInstance()).toBeTruthy()

    });
  
    it('DynamicSelect should exists when page created', () => {
    
        expect(wrapper.find('dynamic-select-stub').exists()).toBe(true)

    });

    it('checks for whatsapp app details',done => {
      moxios.stubRequest('whatsapp/api/accounts',{
        status:200,
        response:fakeRequestData
     })
     
     wrapper.vm.getValuesForEdit();

      Vue.nextTick(()=> {
        expect(moxios.requests.mostRecent().config.url).toBe('whatsapp/api/accounts');
        done();
      }) 


    })


    it('onClose closes the modal',() => {
      wrapper.vm.showModal = true;
      wrapper.vm.onClose();
      Vue.nextTick(() => {
        expect(wrapper.vm.showModal).not.toBeTruthy();
      })
    })

  })