import { mount } from '@vue/test-utils';
import TabularReportLayout from './../../../views/js/components/Common/TabularReportLayout.vue';
import moxios from 'moxios';
import Vue from 'vue';

let wrapper;
window.eventHub  = new Vue();

describe('TabularReportLayout', ()=> {

  let mountComponent = () => {
    wrapper = mount(TabularReportLayout, {
      stubs: ['ticket-filter', 'dynamic-datatable', 'faveo-box', 'alert','custom-loader','column-list'],
      mocks:{
        lang: string => string
      },
      propsData:{
        dataUrl: 'api/agent/management-report',
        columnUrl: 'api/agent/report-columns/management_report',
        exportUrl: 'api/agent/management-report-export',
        panelTitle: 'management-performance',
        saveColumnUrl: 'api/agent/report-columns',
        addCustomColumnUrl: 'api/add-custom-column',
        shortCodeUrl: 'api/report-shortcodes',
        deleteCustomColumnUrl: 'api/report-shortcodes',
        subReportId: 1,
        addCustomColumnUrl: 'add/custom/column/url',
        tableColumns: [{ key: 'key11', label: 'label11', is_visible: true }, {key: 'key22', label: 'label22', is_visible: false }],
        reportIndex: 3
      }
    });
  }

  beforeEach(() => {
    moxios.install();
  });

  afterEach(()=>{
    moxios.uninstall();
  });

  // it('assigns `filterData` to `filterParams` when `setFilter` is called with `filterData`', ()=>{
  //   mountComponent();
  //   let testFilter = {testKey: "testValue"};
  //   wrapper.vm.setFilter(testFilter);
  //   expect(wrapper.vm.filterParams).toEqual(testFilter);
  // })

  it('mounts `dynamic-datatable` only if loading columns length is non zero', ()=>{
    mountComponent();
    expect(wrapper.find('dynamic-datatable-stub').exists()).toBe(true);
    wrapper.vm.columns = [];
    expect(wrapper.find('dynamic-datatable-stub').exists()).toBe(false);
  })

  it('assign `columns` vlaue to columns and call updateVisibleColumns fn ', () => {
    mountComponent();
    wrapper.vm.updateVisibleColumns = jest.fn();
    wrapper.vm.onColumnUpdate([1,2,3]);
    expect(wrapper.vm.columns).toEqual([1,2,3]);
    expect(wrapper.vm.updateVisibleColumns).toHaveBeenCalled();
  })

  it('update vissible columns when updateVisibleColumns called', () => {
    mountComponent();
    wrapper.vm.columns = [{ key: 'key11', label: 'label11', is_visible: true }, {key: 'key22', label: 'label22', is_visible: false } ];
    wrapper.vm.updateVisibleColumns();
    expect(wrapper.vm.visibleColumns).toEqual({ key11: 'label11' });

    wrapper.vm.columns = [];
    wrapper.vm.updateVisibleColumns();
    expect(wrapper.vm.visibleColumns).toEqual({});
  })

  // it('calls export api as soon as `#export-button` is clicked', (done)=>{
  //   mockExportApi();
  //   mountComponent();
  //   wrapper.find('#export-report').trigger('click');

  //   setTimeout(()=>{
  //     expect(moxios.requests.mostRecent().config.url).toBe('api/agent/management-report-export');
  //     done();
  //   });
  // })
})

function mockExportApi(){
    moxios.stubRequest('api/agent/management-report-export', {
      status: 200,
      response: {success: true, message: 'success'},
    });
}
