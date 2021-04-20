import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import TaskListModal from '../../../views/js/components/MiniComponents/TaskListModal.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('TaskListModal',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	  wrapper = mount(TaskListModal,{
	   	
	   	mocks : { lang:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn() },
		
			stubs:['modal','loader','text-field','dynamic-select'],
		
		});
	}
	
	it('Is a vue instance',()=>{
		
		populateWrapper()

		expect(wrapper.isVueInstance()).toBe(true)	
	});

	it("updates `name, task_id and project` values when `updateSubject` method called",()=>{

		wrapper.setProps({ title : 'tasklist_edit', data : { id : 1, name : 'task', project : { id :1, name :'project'}}})
		
		wrapper.vm.updateSubject();

		expect(wrapper.vm.name).toEqual('task');

		expect(wrapper.vm.project).toEqual({id:1,name:'project'});

		expect(wrapper.vm.task_id).toEqual(1);
	})

	it('updates `name` of the project when onChange method is called with suitable parameters',()=>{
	
		wrapper.vm.onChange('task', 'name');
	
		expect(wrapper.vm.name).toEqual('task');
	})

	it('makes an edit api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData({ name : 'task', task_id : 1});

		wrapper.setProps({ title : 'tasklist_edit', data : { id : 1, name : 'name',project : {id:1,name:'name'}}, alertComponentName : 'dataTableModal'});

		wrapper.vm.afterRespond = jest.fn();

		editRequest()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/tasks/api/category/edit/1')

	  	expect(wrapper.vm.afterRespond).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('calls `afterRespond` method if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setData({ name : 'task', task_id : 1 });

		wrapper.setProps({ title : 'tasklist_edit', data : { id : 1, name : 'name',project : {id:1,name:'name'}}, alertComponentName : 'dataTableModal'});

		wrapper.vm.afterRespond = jest.fn();

		editRequest(400)
		
		wrapper.vm.onSubmit()
	   
    setTimeout(()=>{

	  	expect(wrapper.vm.afterRespond).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('makes an create api call when `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData({ name : 'task' });

		wrapper.setProps({ title : 'add_tasklist', alertComponentName : 'dataTableModal'});

		wrapper.vm.afterRespond = jest.fn();

		createRequest()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/tasks/api/category/create')

	  	expect(wrapper.vm.afterRespond).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('calls `afterRespond` method if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setData({ name : 'task', task_id : 1 });

		wrapper.setProps({ title : 'add_tasklist', alertComponentName : 'dataTableModal'});

		wrapper.vm.afterRespond = jest.fn();

		createRequest(400)
		
		wrapper.vm.onSubmit()
	   
    setTimeout(()=>{

	  	expect(wrapper.vm.afterRespond).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it("updates `loading` value and calls `onClose` method when `afterRespond` method called",()=>{

		populateWrapper()

		wrapper.vm.afterRespond();

		expect(wrapper.vm.onClose).toHaveBeenCalled();

		expect(wrapper.vm.loading).toEqual(false);
	});

	function createRequest(status = 200,url = '/tasks/api/category/create'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}

	function editRequest(status = 200,url = '/tasks/api/category/edit/1'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});