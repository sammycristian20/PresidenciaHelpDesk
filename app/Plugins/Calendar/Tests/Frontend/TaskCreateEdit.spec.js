import { mount} from '@vue/test-utils';

import TaskCreate from '../../views/js/components/TaskCreateEdit.vue';

import moxios from 'moxios';

import Vue from 'vue';

let wrapper;

describe('TaskCreate', () => {
    beforeEach(()=>{

        moxios.install();

        wrapper = mount(TaskCreate,{

            stubs:['text-field','alert','dynamic-select','loader','date-time-field'],

            propsData: {
                ticketId:'1'
            },

            mocks:{ lang: (string) => string },

        })

    });

    afterEach(() => {
        moxios.uninstall();
    })

    it('tests for props', () => {
        expect(wrapper.props().ticketId).toBe('1')
    });

    it('TextField should exists when page created', () => {
        expect(wrapper.find('text-field-stub').exists()).toBe(true)
    });

    it('dynamic-select should exists when page created', () => {
        expect(wrapper.find('dynamic-select-stub').exists()).toBe(true)
    });

    it('date-time-field should exists when page created', () => {
        expect(wrapper.find('date-time-field-stub').exists()).toBe(true)
    });

    it('populates the default selected ticket',done => {

        moxios.stubRequest('api/dependency/tickets?ids[0]=1', {
            success: 'true',
            data: {data: {ticket : [{id: 1, ticket_number: 'AAAA-0000-0000'}]}}
        });

        wrapper.vm.getSelectedTicket(1);

        Vue.nextTick(()=> {
            expect(moxios.requests.mostRecent().config.url).toBe('api/dependency/tickets?ids[0]=1');
            done();
        })
    });

    it('should fetch execute getTaskDetails when mode is edit', done => {
        moxios.stubRequest('/tasks/api/get-task-by-id/null', {
            success: 'true',
            data: {data: {}}
        });

        wrapper.vm.getTaskDetails(1);

        Vue.nextTick(()=> {
            expect(moxios.requests.mostRecent().config.url).toBe('/tasks/api/get-task-by-id/null');
            done();
        })

    });

    it('shows isThisEditForm Method return true when the form is for editing', () => {
        delete window.location
        window.location = new URL('https://www.example.com/something/3/edit');
        expect(wrapper.vm.isThisEditForm()).toBe(true);
    });

    it('shows isThisEditForm Method return false when the form is not for editing', () => {
        delete window.location
        window.location = new URL('https://www.example.com/something/3');
        expect(wrapper.vm.isThisEditForm()).toBe(false);
    });

})
