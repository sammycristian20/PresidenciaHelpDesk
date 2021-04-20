

import { mount } from '@vue/test-utils';

import TaskActions from '../../views/js/components/TaskActions.vue';

import moxios from 'moxios';

import Vue from 'vue';

let wrapper;

describe('TaskActions', () => {
    beforeEach(()=>{

      moxios.install();

      wrapper = mount(TaskActions,{

          stubs:['delete-modal'],

          mocks:{ lang: (string) => string },

          methods : {

            basePath : jest.fn()
          },

          propsData : {

            task : { id : 1}
          },
        
      })

    });

    afterEach(() => {
      moxios.uninstall();
    })
  
    

    it('is a vue instance', () => {
      expect(wrapper.isVueInstance()).toBeTruthy();
    });

    it('returns only 15 characters if value length is more than 15 and returns exact value if option length not more than 15',() => {		
		
        expect(wrapper.vm.subString('name name name name name ')).toEqual('name name name ...');
        expect(wrapper.vm.subString('name')).toEqual('name');

    });



  }) 
