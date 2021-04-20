import { mount, createLocalVue, shallowMount } from '@vue/test-utils';

import TaskProject from '../../../views/js/components/MiniComponents/TaskProject.vue';

import Vue from 'vue'

import Vuex from 'vuex'

let localVue = createLocalVue()

localVue.use(Vuex)

describe('TaskProject', () => {

  let wrapper;

  let store;

  let actions;

  let getters;

  getters = {

    formattedTime: () => () => {return ''}
  }

  actions = { unsetValidationError: jest.fn() }

  store = new Vuex.Store({ getters, actions })

	beforeEach(()=>{

		wrapper = mount(TaskProject,{
			
			stubs:['data-table','task-modal','task-table-actions'],
		   
		  mocks:{ lang: (string) => string },

		  methods : {

		  	basePath : jest.fn()
		  },
      localVue, store
		})
	})

	it('is a vue instance', () => {
	  
	  expect(wrapper.isVueInstance()).toBeTruthy()
	});

	it('data-table should exists when page created', () => {
    
    expect(wrapper.find('data-table-stub').exists()).toBe(true)
  });

  it("return row->created_at for `created_at` column in template option of datatable", () => {
    
    expect(wrapper.vm.options.templates.created_at('test', {'created_at': '2012-10-12'})).toEqual("")
  })

  it("requestAdapter method should return `sort-field`, `sort-order`, `search-query`, `page` & `limit`", () => {
    let reqAdptData = {
      "orderBy": "id",
      "ascending": true,
      "query": "something",
      "page": 10,
      "limit": 10
    }
    let reqAdptDataReturn = {
      "sortField": "id",
      "sortOrder": "desc",
      "searchTerm": "something",
      "page": 10,
      "limit": 10
    }
    expect(wrapper.vm.options.requestAdapter(reqAdptData)).toEqual(reqAdptDataReturn)
  });

  it("`responseAdapter` set edit_url, delete_url and view_url to the data property", () => {

    let responseAdpData = {
      "data": {
        "data": {
          "projects": [
            {id: 1,subject:'name'},
          ],
          "total": 1
        }
      }
    }
    let responseAdpDataReturn = {"count": 1, "data": [{"componentTitle": "TaskProjects", "delete_url": "undefined/tasks/api/project/delete/1", "edit_modal": true, "id": 1, "subject": "name"}]}
    
    expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
  });

  it("makes `showModal` false when `onClose` method called",()=>{

    wrapper.vm.onClose();

    expect(wrapper.vm.showModal).toEqual(false);

    expect(actions.unsetValidationError).toHaveBeenCalled();
  })
})