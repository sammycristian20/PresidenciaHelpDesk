import { mount ,createLocalVue, shallowMount} from '@vue/test-utils'

import TaskModal from '../../../views/js/components/MiniComponents/TaskModal.vue';

import Vue from 'vue'

import moxios from 'moxios'

jest.mock('helpers/responseHandler')

window.eventHub = new Vue();

let wrapper;

describe('TaskModal',()=>{

	beforeEach(()=>{

		moxios.install()
	});

	afterEach(()=>{
		
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	
	  wrapper = mount(TaskModal,{
	   	
	   	mocks : { lang:(string)=> string },
	   	
	   	propsData : { onClose : jest.fn() },
		
			stubs:['modal','loader','text-field'],
		
		});
	}
	
	it('Is a vue instance',()=>{
		
		populateWrapper()

		expect(wrapper.isVueInstance()).toBe(true)	
	});

	it("updates `name and project_id` values when `updateSubject` method called",()=>{

		wrapper.setProps({ title : 'edit_project', data : { id : 1, name : 'name'}})
		
		wrapper.vm.updateSubject();

		expect(wrapper.vm.name).toEqual('name');

		expect(wrapper.vm.project_id).toEqual(1);
	})

	it('updates `name` of the project when onChange method is called with suitable parameters',()=>{
	
		wrapper.vm.onChange('project', 'name');
	
		expect(wrapper.vm.name).toEqual('project');
	})

	it('makes an edit api call as soon as `onSubmit` is called',(done)=>{
		
		populateWrapper()

		wrapper.setData({ name : 'project', project_id : 1});

		wrapper.setProps({ title : 'edit_project', data : { id : 1, name : 'name'}, alertComponentName : 'dataTableModal'});

		wrapper.vm.afterRespond = jest.fn();

		editRequest()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/tasks/api/project/edit/1')

	  	expect(wrapper.vm.afterRespond).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('calls `afterRespond` method if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setData({ name : 'project', project_id : 1});

		wrapper.setProps({ title : 'edit_project', data : { id : 1, name : 'name'}, alertComponentName : 'dataTableModal'});

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

		wrapper.setData({ name : 'project' });

		wrapper.setProps({ title : 'add_project', alertComponentName : 'dataTableModal'});

		wrapper.vm.afterRespond = jest.fn();

		createRequest()
		
		wrapper.vm.onSubmit()

		expect(wrapper.vm.loading).toBe(true)
	   
    setTimeout(()=>{
    	
    	expect(moxios.requests.mostRecent().url).toBe('/tasks/api/project/create')

	  	expect(wrapper.vm.afterRespond).toHaveBeenCalled();
	  
      done();
    },1)
	})

	it('calls `afterRespond` method if api returns error response',(done)=>{
		
		populateWrapper()

		wrapper.setData({ name : 'project', project_id : 1});

		wrapper.setProps({ title : 'add_project', alertComponentName : 'dataTableModal'});

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

	function createRequest(status = 200,url = '/tasks/api/project/create'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}

	function editRequest(status = 200,url = '/tasks/api/project/edit/1'){
	   
	  moxios.uninstall();
	   
	  moxios.install();
	   
	  moxios.stubRequest(url,{
	   
	    status: status,
	   
	    response : {}
	  })
	}
});