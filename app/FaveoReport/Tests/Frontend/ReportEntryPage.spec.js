import { mount } from '@vue/test-utils';
import ReportEntryPage from './../../views/js/components/ReportEntryPage.vue';
import moxios from 'moxios';
import Vue from 'vue';
import responseHandler from 'helpers/responseHandler';
import  utils from '../../../FaveoReport/views/js/helpers/utils';

let wrapper;
window.eventHub = new Vue();

jest.mock('helpers/responseHandler', () => ({
  errorHandler: ()=> jest.fn(),
  successHandler: ()=> jest.fn()
}));

jest.mock('../../../FaveoReport/views/js/helpers/utils', () => ({
  getValidFilterObject: ({})=> jest.fn( () => { return {}}),
}));

describe('ReportEntryPage', () => {

  let mountComponent = () => {
    wrapper = mount(ReportEntryPage, {
      stubs: ['ticket-filter', 'tabular-report-layout', 'time-series-chart', 'category-based-report', 'save-report-modal', 'alert'],
      mocks: {
        lang: string => string
      }
    });
  }

  beforeEach(() => {
    moxios.install();
    mountComponent();
  });

  afterEach(() => {
    moxios.uninstall();
  });

  it('calls export api as soon as `#export-button` is clicked', (done) => {
    mockExportApi();
    mountComponent();
    wrapper.vm.dataCount = 1;
    wrapper.vm.reportConfigObj = { sub_reports: [{ data_type: 'datatable' }], export_url: 'api/agent/export-chart/top-customer-analysis' };
    wrapper.find('#export-report').trigger('click');

    setTimeout(() => {
      expect(moxios.requests.mostRecent().config.url).toBe('api/agent/export-chart/top-customer-analysis');
      done();
    });
  })

  it('close modal when closeSaveReportModal called', () => {
    wrapper.vm.closeSaveReportModal();
    expect(wrapper.vm.openSaveReportModal).toBe(false);
  })

  it('Update local copy of filter object with the filter-object recieved form api response', () => {
    wrapper.vm.reportConfigObj = { sub_reports: [], filters: [ {key: 'created-at', value: 'last::1~day'}] };
    wrapper.vm.updateFilterObj();

    expect(wrapper.vm.filterParams).toStrictEqual({'created-at': 'last::1~day'})
  })

  it('showExportButton should return true if report.data_type is datatable else it will return false', () => {
    wrapper.vm.reportConfigObj = { sub_reports: [{ data_type: 'datatable' }], export_url: 'api/agent/export-chart/top-customer-analysis' };
    expect(wrapper.vm.showExportButton).toBe(true);

    wrapper.vm.reportConfigObj = { sub_reports: [{ data_type: 'not_datatable' }], export_url: 'api/agent/export-chart/top-customer-analysis' };
    expect(wrapper.vm.showExportButton).toBe(false);
  })

  it('onColumnUpdate will update local copy of the column list', () => {
    wrapper.vm.clonedReportConfigOnj = { sub_reports: [{}, { columns: []}]};
    wrapper.vm.onColumnUpdate([{ id: 0, name: 'new_column'}], 1);
    expect(wrapper.vm.clonedReportConfigOnj.sub_reports[1].columns).toStrictEqual([{ id: 0, name: 'new_column'}]);
  })

  it('Update local copy of `key` property with the updated one', () => {
    wrapper.vm.clonedReportConfigOnj = { sub_reports: [{}, { columns: []}]};
    wrapper.vm.updateChangedValue([{ id: 0, name: 'new_column'}], 1, 'columns');
    expect(wrapper.vm.clonedReportConfigOnj.sub_reports[1].columns).toStrictEqual([{ id: 0, name: 'new_column'}]);
  })

  it('getReportConfiguration will get report config object -- SUCCESS', function (done) {

    wrapper.vm.updateFilterObj = jest.fn();

    utils.getValidFilterObject = jest.fn(({}) => { return {}});

    wrapper.vm.getReportConfiguration(3);

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      request.respondWith({
        status: 200,
        response: { data: [{ sub_reports: [{ data_type: 'datatable' }], export_url: '' }] }
      }).then(function () {
        expect(request.config.url).toBe('api/agent/report-config/3')
        expect(wrapper.vm.reportConfigObj).toStrictEqual([{ sub_reports: [{ data_type: 'datatable' }], export_url: '' }]);
        expect(wrapper.vm.clonedReportConfigOnj).toStrictEqual([{ sub_reports: [{ data_type: 'datatable' }], export_url: '' }]);
        expect(wrapper.vm.isLoading).toBe(false);
        expect(wrapper.vm.updateFilterObj).toHaveBeenCalled();
        done();
      })
    })
  })

  it('getReportConfiguration will get report config object -- ERROR', function (done) {

    responseHandler.errorHandler = jest.fn();

    utils.getValidFilterObject = jest.fn(({}) => { return {}});

    wrapper.vm.getReportConfiguration(3);

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      request.respondWith({
        status: 400,
        response: { data: [{ sub_reports: [{ data_type: 'datatable' }], export_url: '' }] }
      }).then(function () {
        expect(request.config.url).toBe('api/agent/report-config/3');
        expect(wrapper.vm.isLoading).toBe(false);
        expect(responseHandler.errorHandler).toHaveBeenCalled();
        done();
      })
    })
  })

})

function mockExportApi() {
  moxios.stubRequest('api/agent/export-chart/top-customer-analysis', {
    status: 200,
    response: { success: true, message: 'success' },
  });
}