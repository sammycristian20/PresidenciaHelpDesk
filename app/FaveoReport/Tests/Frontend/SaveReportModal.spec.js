import { mount } from '@vue/test-utils';
import SaveReportModal from './../../views/js/components/SaveReportModal.vue';
import moxios from 'moxios';
import Vue from 'vue';
import responseHandler from 'helpers/responseHandler';
import * as extraLogics from "helpers/extraLogics";
import Vuex from 'vuex'

let wrapper;
window.eventHub = new Vue();

Vue.use(Vuex);

global.confirm = jest.fn(() => {
  return true;
})

let store;

let getters = {
  formattedTime: () => () => {return ''},
}

store = new Vuex.Store({
  getters
})

jest.mock('helpers/responseHandler', () => ({
  errorHandler: ()=> jest.fn(),
  successHandler: ()=> jest.fn()
}));


describe('SaveReportModal', () => {

  let mountComponent = (modalMode = 'fork') => {

    extraLogics.getIdFromUrl = () => { return 3 };

    wrapper = mount(SaveReportModal, {
      stubs: ['modal', 'alert', 'text-field', 'checkbox', 'custom-loader'],
      propsData: {
        modalMode: modalMode,
        reportDataObj: {
          id: 3,
          name: 'report_name',
          description: 'report_description',
          is_public: true
        },
        onClose: () => jest.fn()
      },
      methods:{
        trans: (string) => string
      },
      mocks: {
        lang: string => string
      },
      store
    });
  }

  beforeEach(() => {
    moxios.install();
    mountComponent();
  });

  afterEach(() => {
    moxios.uninstall();
  });

  it('setUpComponentPropertiesBasisOfMode will setup component properties basis of mode', () => {
    expect(wrapper.vm.title).toBe('fork_this_report');

    mountComponent('update');
    expect(wrapper.vm.title).toBe('update_this_report');
    expect(wrapper.vm.name).toBe('report_name');
    expect(wrapper.vm.description).toBe('report_description');
    expect(wrapper.vm.isPublic).toBe(true);
  })

  it('getSaveReportParams will return parameters', () => {
    expect(wrapper.vm.getSaveReportParams().id).toBe(null);

    mountComponent('update');
    expect(wrapper.vm.getSaveReportParams().name).toBe('report_name');
    expect(wrapper.vm.getSaveReportParams().description).toBe('report_description');
    expect(wrapper.vm.getSaveReportParams().is_public).toBe(true);
  })

  it('onSubmit save/update the report -- SUCCESS', function (done) {

    wrapper.vm.getSaveReportParams = jest.fn(() => { return {} });
    responseHandler.successHandler = jest.fn();

    wrapper.vm.onSubmit();

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      request.respondWith({
        status: 200,
        response: { message: '' }
      }).then(function () {
        expect(request.config.url).toBe('api/agent/report-config/3')
        expect(wrapper.vm.isLoading).toBe(false);
        expect(responseHandler.successHandler).toHaveBeenCalled();
        done();
      })
    })
  })

  it('onSubmit save/update the report -- ERROR', function (done) {

    wrapper.vm.getSaveReportParams = jest.fn(() => { return {} });
    responseHandler.errorHandler = jest.fn();
    wrapper.vm.onSubmit();

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      request.respondWith({
        status: 400,
        response: { message: '' }
      }).then(function () {
        expect(request.config.url).toBe('api/agent/report-config/3');
        expect(responseHandler.errorHandler).toHaveBeenCalled();
        expect(wrapper.vm.isLoading).toBe(false);
        done();
      })
    })
  })

})