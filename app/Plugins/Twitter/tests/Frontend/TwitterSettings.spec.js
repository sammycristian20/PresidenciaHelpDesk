import TwitterSettings from './../../views/js/components/TwitterSettingsPage';
import { shallowMount } from '@vue/test-utils';
import moxios from 'moxios'
import Vue from 'vue';


window.eventHub = new Vue()

const fakeRequestData = {
    'success':true,
    'data':{}
}

describe('TwitterSettings', () => {

    let wrapper;
  
    beforeEach(() => {
      moxios.install()

      moxios.stubRequest('facebook/credentials',{
        status:200,
        response:fakeRequestData
     })

     afterEach(() => {
       moxios.uninstall()
     })
  
      wrapper = shallowMount(TwitterSettings,{
        mocks:{
            stubs:['text-field','dynamic-select'],
            lang:(string)=>string
            },
      })
  
    })

    it('is a vue instance', () => {
	  
        expect(wrapper.isVueInstance()).toBeTruthy()

    });
  
    it('DynamicSelect should exists when page created', () => {
    
        expect(wrapper.find('dynamic-select-stub').exists()).toBe(true)

    });

    it('pressing button will show modal',() => {

        expect(wrapper.findAll('button').at(1).trigger('click'));
        setTimeout(()=>{
        
          expect(wrapper.vm.showModal).toBeTruthy()

        },1)

    })

    it('checks for twitter app details',done => {
      moxios.stubRequest('twitter/api/app',{
        status:200,
        response:fakeRequestData
     })
     
     wrapper.vm.getAppDetails();

      Vue.nextTick(()=> {
        expect(moxios.requests.mostRecent().config.url).toBe('twitter/api/app');
        done();
      }) 


    })

  })