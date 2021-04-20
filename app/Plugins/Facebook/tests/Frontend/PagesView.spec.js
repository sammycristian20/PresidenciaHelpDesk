import PagesView from './../../views/js/components/PagesView';
import { shallowMount } from '@vue/test-utils';
import moxios from 'moxios'
import Vue from 'vue';

window.eventHub = new Vue()

import VTooltip from 'v-tooltip'

Vue.use(VTooltip);

const fakeRequestData = {
    'success':true,
    'data':{}
}

describe('PagesView', () => {

    let wrapper;
  
    beforeEach(() => {
      moxios.install()

      wrapper = shallowMount(PagesView,{
        mocks:{
            stubs:['data-table'],
            lang:(string)=>string
            },
      })
  
    })

    it('is a vue instance', () => {
	  
        expect(wrapper.isVueInstance()).toBeTruthy()

    });
  
    it('DataTable should exists when page created', () => {
    
        expect(wrapper.find('data-table-stub').exists()).toBe(true)

    });

    it('checks for facebook pages count before mount',done => {
      moxios.stubRequest('facebook/api/credentials',{
        status:200,
        response:fakeRequestData
     })
     
     wrapper.vm.fetchPageCount();

      Vue.nextTick(()=> {
        expect(moxios.requests.mostRecent().config.url).toBe('facebook/api/pages/view');
        done();
      }) 


    })

    it('click sync button performs api call to get synchronize FB Pages',done => {
        moxios.stubRequest('facebook/api/credentials',{
            status:200,
            response:fakeRequestData
        })

        wrapper.find('.refresh').trigger('click');

        Vue.nextTick(() => {
            expect(moxios.requests.mostRecent().config.url).toBe('facebook/api/pages/refresh');
            done();
        })
    })

  
  })