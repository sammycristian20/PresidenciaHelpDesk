import {mount,createLocalVue} from "@vue/test-utils";
import MailSubject from "components/LogsTables/ReusableComponent/MailSubject";
import Vuex from "vuex";
import sinon from "sinon";
import moxios from "moxios";

let wrapper;

const localVue = createLocalVue();

localVue.use(Vuex);

describe("MailSubject", () => {

  let actions;

  let store;

  beforeEach(() => {
    actions = {
      unsetValidationError: jest.fn()
    }
    store = new Vuex.Store({
      actions
    })

    moxios.install();
  });

  afterEach(() => {
    moxios.uninstall();
  })

  const populateWrapperWithoutStub = () => {

    moxios.stubRequest("/api/get-log-mail-body/1", {
      status: 200,
      response: {
        "success": true,
        "data": {
          "mail_body": "test_body"
        }
      }
    });

    wrapper = mount(MailSubject, {
      propsData: {
        data: {}
      },
      stubs: ["loader"],
      mocks: {
        lang: (string) => string
      },
      store,
      localVue
    });
  }

  const populateWrapperWithStub = () => {
    wrapper = mount(MailSubject, {
      propsData: {
        data: {}
      },
      stubs: ["logs-modal"],
      mocks: {
        lang: (string) => string
      },
      store,
      localVue
    });
  }

  it("sends an API request when log-mail-subject link is clicked", (done) => {
    populateWrapperWithoutStub();
    wrapper.setProps({
      data: {
        id: 1,
        subject: "test_subject"
      }
    });
    wrapper.find("#log-mail-subject").trigger("click");
    setTimeout(() => {
      expect(moxios.requests.mostRecent().url).toBe("/api/get-log-mail-body/1");
      done();
    }, 0)
  });

  it("mounts modal pop-up with content from API call", (done) => {
    populateWrapperWithoutStub();
    wrapper.setProps({
      data: {
        id: 1,
        subject: "test_subject"
      }
    });
    wrapper.find("#log-mail-subject").trigger("click");
    setTimeout(() => {
      expect(moxios.requests.mostRecent().url).toBe("/api/get-log-mail-body/1");
      expect(wrapper.text()).toContain("test_subject");
      expect(wrapper.text()).toContain("test_body");
      done();
    }, 0)
  });

  it("Does not show `logs modal popup` if `showModal` is false", () => {
    populateWrapperWithStub();
    wrapper.vm.$data.showModal = false;
    expect(wrapper.find("logs-modal-stub").exists()).toBe(false);
  });

  it("Show `logs modal popup` if `showModal` is true", () => {
    populateWrapperWithStub();
    wrapper.vm.$data.showModal = true;
    expect(wrapper.find("logs-modal-stub").exists()).toBe(true);
  });

  it("Initial value of `showModal` should be false", () => {
    populateWrapperWithStub();
    expect(wrapper.vm.$data.showModal).toBe(false);
  });

  it("`ShowModal` should be false when `onClose` method is called", () => {
    populateWrapperWithStub();
    wrapper.setData({
      showModal: true
    });
    expect(wrapper.vm.$data.showModal).toBe(true);
    wrapper.vm.onClose();
    expect(wrapper.vm.$data.showModal).toBe(false);
  });

});
