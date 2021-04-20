import ChatEdit from './../../views/js/components/ChatEdit';
import { shallowMount } from '@vue/test-utils';
import moxios from 'moxios'
import Vue from 'vue';


window.eventHub = new Vue()

const fakeRequestData = {
    'success':true,
    'data':{}
}

describe('ChatEdit', () => {

    let wrapper;
  
    beforeEach(() => {
      moxios.install()

     afterEach(() => {
       moxios.uninstall()
     })
  
      wrapper = shallowMount(ChatEdit,{
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
  
    it('DynamicSelect and textfield should exists when page created', () => {
    
        expect(wrapper.find('dynamic-select-stub').exists()).toBe(true)
        expect(wrapper.find('text-field-stub').exists()).toBe(true)

    });

    it('obtains the chat service details',done => {
      moxios.stubRequest('/chat/api/chats?ids[]=undefined',{
        status:200,
        response:fakeRequestData
     })
     
     wrapper.vm.getChatDetails();

      Vue.nextTick(()=> {
        expect(moxios.requests.mostRecent().config.url).toBe('/chat/api/chats?ids[]=undefined');
        done();
      }) 
      
    })

    it('submit button submits the data',done => {
        moxios.stubRequest('chat/api/update/undefined',{
            status:200,
            response:fakeRequestData
        })

        wrapper.find('button').trigger('click');

        Vue.nextTick(()=> {
            expect(moxios.requests.mostRecent().config.url).toBe('chat/api/update/undefined');
            done();
        })    

    }) 


  })