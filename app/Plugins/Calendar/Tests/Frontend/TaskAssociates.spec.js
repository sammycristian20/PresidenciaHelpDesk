import {  mount } from '@vue/test-utils'

import Vue from 'vue'

import TaskAssociates from '../../views/js/components/TaskAssociates.vue';

import moxios from 'moxios';

window.eventHub = new Vue();

describe('TaskAssociates',() => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = mount(TaskAssociates,{

            stubs: ['alert','loader','task-activity'],

            mocks:{ trans:(string)=>string },

            propsData : { taskId : 1 }
        })
    }

    beforeEach(() => {

        updateWrapper();

        moxios.install();
    })

    afterEach(() => {

        moxios.uninstall()
    })

    it("updates `category & loading` value when `associates` method called",(done)=>{

        wrapper.vm.associates('activity');

        expect(wrapper.vm.loading).toEqual(true);

        setTimeout(()=>{
            expect(wrapper.vm.loading).toEqual(false);
            done();
        },2);

        expect(wrapper.vm.category).toEqual('activity');
    })

})