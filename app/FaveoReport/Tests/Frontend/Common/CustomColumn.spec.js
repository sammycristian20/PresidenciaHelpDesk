import { mount } from '@vue/test-utils';
import CustomReportColumn from './../../../views/js/components/Common/CustomReportColumn';
import moxios from 'moxios';
import {shortCodesResponse} from './../FakeData/managementReportResponse'
import Vue from 'vue';

let wrapper;
window.eventHub  = new Vue();

describe('CustomReportColumn', ()=> {
  let mountComponent = (isEditing = false, column = {}) => {
    wrapper = mount(CustomReportColumn, {
      stubs: ['relative-loader', 'custom-loader', 'checkbox','text-field', 'alert', 'dynamic-select'],
      propsData: {
        closeView : jest.fn(),
        isEditing: isEditing,
        column: column,
        addCustomColumnUrl: 'api/add-custom-column',
        shortCodeUrl: 'api/report-shortcodes'
      },
      mocks:{
        lang: string => string
      }
    });
  }

  beforeEach(() => {
    moxios.install();

    moxios.stubRequest('api/report-shortcodes', {
      status: 200,
      response: shortCodesResponse,
    });

  });

  afterEach(()=>{
    moxios.uninstall();
  });

  it('makes an api for shortcodes as soon as component is mounted', (done)=>{
    mountComponent();
    setTimeout(()=>{
      expect(moxios.requests.mostRecent().config.url).toBe('api/report-shortcodes');
      expect(wrapper.vm.shortCodes).toEqual(shortCodesResponse.data);
      done();
    }, 0);
  });

  it('shows `relative-loader` until shortCodes as populated', (done)=>{
    mountComponent();
    expect(wrapper.find('relative-loader-stub').exists()).toBe(true);
    expect(wrapper.find('#report-shortcode-list').exists()).toBe(false);

    setTimeout(()=>{
      expect(wrapper.find('relative-loader-stub').exists()).toBe(false);
      expect(wrapper.find('#report-shortcode-list').exists()).toBe(true);
      done();
    }, 0);
  });

  it('populates column data if `isEditing` is true at the time of mount', ()=>{
    mountComponent();
    expect(wrapper.vm.id).toEqual(0);
    expect(wrapper.vm.name).toEqual("");
    expect(wrapper.vm.equation).toEqual("");
    expect(wrapper.vm.isTimestamp).toEqual(false);

    mountComponent(true, {id: 1, label:'label', equation:'equation', is_timestamp: true, timestamp_format: "F j, Y"});
    expect(wrapper.vm.id).toEqual(1);
    expect(wrapper.vm.name).toEqual("label");
    expect(wrapper.vm.equation).toEqual("equation");
    expect(wrapper.vm.isTimestamp).toEqual(true);
  })

  it('makes an api call to `api/add-custom-column` when submit button is clicked', (done)=>{
    mountComponent(true, {id: 1, label:'label', equation:'equation', is_timestamp: true, timestamp_format: "F j, Y"});
    mockAddCustomColumn();
    wrapper.find("#custom-column-submit").trigger('click');
    setTimeout(()=>{
      expect(moxios.requests.mostRecent().config.url).toBe('api/add-custom-column');
      done();
    }, 0);
  })

  it('emits `refresh-report` event on success of `api/add-custom-column` call', (done)=>{
    mountComponent(true, {id: 1, label:'label', equation:'equation', is_timestamp: true, timestamp_format: "F j, Y"});
    mockAddCustomColumn();
    wrapper.find("#custom-column-submit").trigger('click');
    let eventEmitted = false;
    window.eventHub.$on('refresh-report', ()=> eventEmitted = true);
    expect(eventEmitted).toBe(false);
    setTimeout(()=>{
      expect(eventEmitted).toBe(true);
      done();
    }, 0);
  })
})

function mockAddCustomColumn(status = 200){
  moxios.stubRequest('api/add-custom-column', {
    status: status,
    response: {success: true, message: "updated successfully"},
  });
}
