import { mount } from '@vue/test-utils';
import ColumnList from './../../../views/js/components/Common/ColumnList';
import moxios from 'moxios';
import {reportColumnResponse} from './../FakeData/managementReportResponse'
import Vue from 'vue';

let wrapper;
window.eventHub  = new Vue();

describe('ColumnList', ()=> {

  let mountComponent = (isEditing = false, column = {}) => {
    wrapper = mount(ColumnList, {
      stubs: [ 'custom-loader', 'custom-column'],
      propsData: {
        onColumnUpdate : jest.fn(),
        columnUrl: 'api/agent/management-report-columns',
        saveColumnUrl: 'api/agent/report-columns',
        addCustomColumnUrl: 'api/add-custom-column',
        shortCodeUrl: 'api/report-shortcodes',
        deleteCustomColumnUrl: 'api/delete-custom-column',
        tableColumns: [],
        shortCodeUrl: '',
        subReportId: 3,
        reportIndex: 1
      },
      mocks:{
        lang: string => string
      }
    });
  }

  beforeEach(() => {
    moxios.install();

    moxios.stubRequest('api/agent/management-report-columns', {
      status: 200,
      response: reportColumnResponse,
    });

  });

  afterEach(()=>{
    moxios.uninstall();
  });

    it('makes `isEditingCustomColumn` as true and assign passed column when `onEdit` is called', ()=>{
      mountComponent();
      wrapper.vm.onEdit('test_column');
      expect(wrapper.vm.showAddCustomColumn).toBe(true);
      expect(wrapper.vm.isEditingCustomColumn).toBe(true);
      expect(wrapper.vm.column).toBe('test_column');
    })

    it('makes `showCustomColumn` and `isEditingCustomColumn` as false and `column` to be empty when `hideCustomColumn` is called', ()=>{
      mountComponent();
      wrapper.vm.showAddCustomColumn = false;
      wrapper.vm.hideCustomColumn();
      expect(wrapper.vm.showAddCustomColumn).toBe(false);
      expect(wrapper.vm.isEditingCustomColumn).toBe(false);
      expect(wrapper.vm.column).toEqual({});
    })

    it('makes delete api call as soon as `onDelete` method is called with particular id', (done)=>{
      mockDeleteApi();
      mountComponent();
      wrapper.vm.onDelete('testId');

      setTimeout(()=>{
        // as soon as api comes as success, it calls columnlist api again to update
        expect(moxios.requests.__items[1].config.url).toBe('api/agent/report-columns/3');
        done();
      },0);
    })

    it('calls column-list api as soon as delete api is success', (done)=>{
      mockDeleteApi();
      mountComponent();
      wrapper.vm.onDelete('testId');

      setTimeout(()=>{
        expect(moxios.requests.mostRecent().config.url).toBe('api/agent/report-columns/3');
        done();
      },0);
    })

    it('calls save column api as soon as `#save-columns` button is clicked', (done)=>{
      mountComponent();

      wrapper.vm.saveColumns();

      setTimeout(()=>{
        // as soon as api comes as success, it calls columnlist api again to update
        expect(moxios.requests.mostRecent().config.url).toBe('api/agent/report-columns/3');
        done();
      },0);
    })

    function mockDeleteApi(){
      moxios.stubRequest('api/delete-custom-column/testId', {
        status: 200,
        response: {success: false, data: 'deleted successfully'}
      });
    }
  });
