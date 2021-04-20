import { mount, shallowMount } from "@vue/test-utils";

import moxios from 'moxios';

import TaskTemplateCreateEdit from "../../views/js/components/TaskTemplateCreateEdit";
import TaskSettings from "../../views/js/components/TaskSettings";

const templateDetails = {
    "id": 40,
    "name": "For Phaniraj",
    "description": "Sample",
    "category_id": 1,
    "template_tasks": [
        {
            "id": 81,
            "name": "Contact by call",
            "template_id": 40,
            "end": "1",
            "end_unit": "day",
            "assignees": [
                "1"
            ],
            "order": "1",
            "assign_task_to_ticket_agent": 0
        }
    ],
    "category": {
        "id": 1,
        "name": "Phaniraj"
    }
}

const fakeResponse = {success:true,data: {}};

jest.mock('helpers/responseHandler', () => ({
    errorHandler: jest.fn()
}))

describe('TaskTemplateCreateEdit', () => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = shallowMount(TaskTemplateCreateEdit,{

            stubs: ['text-field','dynamic-select','alert','v-select','loader','form-field','tool-tip','faveo-box'],

            mocks:{ lang:(string)=>string },

        })
    }

    beforeEach(() => {
        updateWrapper();
        moxios.install();
    })

    afterEach(() => {
        moxios.uninstall()
    })

    it('should call proper api when `getSelectOptions` method called', async () => {

        moxios.stubRequest('api/dependency/agents?meta=true', {status:200, response:fakeResponse})
        await wrapper.vm.getSelectOptions();
        expect(moxios.requests.mostRecent().config.url).toBe('api/dependency/agents?meta=true');

    });

    it('should add a task row when `addTaskRow` method is called', () => {
        wrapper.vm.addTaskRow();
        expect(wrapper.vm.templateTasks.length).toBe(2);
    });

    it('should only remove task row only when length of `templateTasks` is > 1 when `removeTaskRow` is called', () => {
        window.alert = jest.fn();
        wrapper.vm.removeTaskRow(0);
        expect(wrapper.vm.templateTasks.length).toBe(1);
    });

    it('should remove a task row when `removeTaskRow` method is called', () => {
        wrapper.vm.addTaskRow();
        wrapper.vm.removeTaskRow(1);
        expect(wrapper.vm.templateTasks.length).toBe(1);
    });

    it('should fill the template values when `fillFieldsIfEdit` is called',  () => {
        wrapper.vm.addFilledTaskRows = jest.fn();

        wrapper.setProps({
            templateDetails : JSON.stringify(templateDetails)
        })

        wrapper.vm.fillFieldsIfEdit();

        expect(wrapper.vm.name).toBe(templateDetails.name)
        expect(wrapper.vm.description).toBe(templateDetails.description)
        expect(wrapper.vm.addFilledTaskRows).toHaveBeenCalled()
    });

    it('should update `template tasks` when `addFilledTaskRows` is called', () => {
        wrapper.setProps({
            templateDetails : JSON.stringify(templateDetails)
        })

        expect(wrapper.vm.templateTasks[0].taskName).not.toBe(templateDetails.template_tasks[0].name)

        wrapper.vm.addFilledTaskRows(templateDetails.template_tasks);

        expect(wrapper.vm.templateTasks[0].taskName).toBe(templateDetails.template_tasks[0].name)
        expect(wrapper.vm.templateTasks[0].taskEndUnit).toBe(templateDetails.template_tasks[0].end_unit)

    });

    it('should submit to `PUT` api end point when the form is update form', async () => {
        wrapper.setProps({
            templateDetails : JSON.stringify(templateDetails)
        })

        moxios.stubRequest('/tasks/api/template/update/' + templateDetails.id, {status:200, response:fakeResponse})

        wrapper.vm.fillFieldsIfEdit();

        wrapper.vm.submit();

        await wrapper.vm.$nextTick();

        expect(moxios.requests.mostRecent().config.url).toBe('/tasks/api/template/update/' + templateDetails.id);
    });

    it('should submit to `POST` api when the form is create form', async () => {
        moxios.stubRequest('/tasks/api/template/store', {status:200, response:fakeResponse});

        wrapper.vm.submit();

        await wrapper.vm.$nextTick();

        expect(moxios.requests.mostRecent().config.url).toBe('/tasks/api/template/store');
    });

    it('should update the order of task rows when `updateOrder` is called', () => {
        wrapper.setData({
            templateTasks: [
                {
                    taskName: '',
                    assignees: [],
                    taskEnd: '',
                    order: '',
                    taskEndUnit: 'day',
                    assignTaskToTicketAssignee: false,
                }
            ]
        });

        wrapper.vm.updateOrder()

        expect(wrapper.vm.templateTasks[0].order).toBe(1)
    });

});
