import { mount } from '@vue/test-utils';
import LdapListPage from './../../views/js/components/LdapListPage.vue';
import moxios from 'moxios';
import responseHandler from 'helpers/responseHandler';

jest.mock('helpers/responseHandler', () => ({
  errorHandler: () => jest.fn(),
  successHandler: () => jest.fn()
}));


const farziData = { "success": true, "data": { "ldap_list": [{ "id": 12, "schema": "active_directory", "username": "msdewj", "encryption": null, "domain": "swqdewfr@dkw.com", "port": null }, { "id": 11, "schema": "active_directory", "username": "dqewre", "encryption": "ssl", "domain": "sdqe@sw.com", "port": null }], "hide_default_login": false } };


let wrapper;

const mountComponent = () => {
  wrapper = mount(LdapListPage, {
    mocks: {
      trans: (string) => string,
    },
    methods: {
      basePath: () => 'http://localhost'
    },
    stubs: ['alert', 'loader']
  });
}

describe('LdapListPage', () => {
  beforeEach(() => {
    moxios.install()
  })

  afterEach(function () {
    moxios.uninstall()
  })

  it('getLdapList will fetch ldap elements --Success', function (done) {
    mountComponent();
    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      expect(request.url).toBe('api/ldap/settings');
      request.respondWith({
        status: 200,
        response: farziData
      }).then(function () {
        expect(wrapper.vm.adList.length).toBe(2);
        expect(wrapper.vm.hideDefaultLogin).toBe(false);
        done();
      })
    })
  })

  it('getLdapList will fetch ldap elements --Error', function (done) {
    mountComponent();
    responseHandler.errorHandler = jest.fn();
    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      request.respondWith({
        status: 400,
        response: { success: false, message: 'Err:msg' }
      }).then(function () {
        expect(wrapper.vm.adList.length).toBe(0);
        expect(responseHandler.errorHandler).toHaveBeenCalled();
        done();
      })
    })
  })

  it('deleteItem will delete specified ldap_id', function (done) {
    mountComponent();

    global.confirm = jest.fn(() => {
      return true;
    })

    wrapper.vm.ldapId = 3;
    responseHandler.successHandler = jest.fn();
    wrapper.vm.getLdapList = jest.fn();

    wrapper.vm.deleteItem(3);

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      expect(request.url).toBe('api/ldap/settings/3');
      request.respondWith({
        status: 200,
        response: { success: true, message: 'Deleted' }
      }).then(function () {
        expect(responseHandler.successHandler).toHaveBeenCalled();
        expect(wrapper.vm.getLdapList).toHaveBeenCalled();
        done();
      })
    })
  })

  it('updateHideDefaultLogin will make an api call and call successHandler if succeeded -- SUCCESS', (done) => {
    mountComponent();

    responseHandler.successHandler = jest.fn();
    wrapper.vm.updateHideDefaultLogin();

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      expect(request.url).toBe('api/ldap/hide-default-login');
      request.respondWith({
        status: 200,
        response: { success: true, message: 'Updated' }
      }).then(function () {
        expect(responseHandler.successHandler).toHaveBeenCalled();
        done();
      })
    })
  })

  it('updateHideDefaultLogin will make an api call and call errorHandler if fails -- ERROR', (done) => {
    mountComponent();

    responseHandler.errorHandler = jest.fn();
    wrapper.vm.updateHideDefaultLogin();

    moxios.wait(function () {
      let request = moxios.requests.mostRecent();
      expect(request.url).toBe('api/ldap/hide-default-login');
      request.respondWith({
        status: 400,
        response: { success: false, message: 'Not Updated' }
      }).then(function () {
        expect(responseHandler.errorHandler).toHaveBeenCalled();
        done();
      })
    })
  })

})
