
import { mount } from '@vue/test-utils';

import TaskCreateWrapper from '../../views/js/components/TaskCreateWrapper.vue';

import moxios from 'moxios';

import Vue from 'vue';

let wrapper;

describe('TaskCreateWrapper', () => {
    beforeEach(()=>{

      moxios.install();

      wrapper = mount(TaskCreateWrapper,{

          stubs:['TaskCreate','Alert'],

          mocks:{ lang: (string) => string },

      })

    });

    afterEach(() => {
      moxios.uninstall();
    })
  

    it('is a vue instance', () => {
      expect(wrapper.isVueInstance()).toBeTruthy();
    });

    it('TaskCreate should exists when page created', () => {
      expect(wrapper.find('TaskCreate-stub').exists()).toBe(true)
    });

  }) 
