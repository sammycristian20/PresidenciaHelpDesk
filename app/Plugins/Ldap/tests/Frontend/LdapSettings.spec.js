import { mount } from '@vue/test-utils'
import LdapSettings from './../../views/js/components/LdapSettings'
import jsdom from 'jsdom';
import sinon from 'sinon'
import moxios from 'moxios';
import { wrap } from 'module';

let wrapper;

const fakeRequestData = {
  'domain': '192.113.1.1',
  'username': 'navin',
  'password': 'Ladybird',
  'is_valid': 1,
  'search_bases': [{
    'id': 23,
    'ldap_id': 1,
    'organizations': [],
    'departments': ["1", "2"],
    'user_type': 'admin',
    'search_base': 'something'
  }]
}

const fakesuccessQuery = {
  message: "0 new users imported from the active directory",
  success: true
}



const JSDOMEnvironment = require('jest-environment-jsdom');


const populateWrapper = () => {
  wrapper = mount(LdapSettings, {
    mocks: {
      lang: (string) => string
    },
    stubs : ['static-select','text-field','alert','custom-loader','search-basis','user-import-mapper','checkbox','ldap-table']
  });
}

describe('LdapSettings', () => {
  beforeEach(() => {
    populateWrapper();
    moxios.install()
  })

  //faking the success response
  moxios.stubRequest('/api/ldap/settings', {
    status: 200,
    response: fakeRequestData
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it('is a vue instance', () => {
    expect(wrapper.isVueInstance()).toBeTruthy()
  })

  it('show loader if data is not populated and loading is true', () => {
    wrapper.vm.$data.hasDataPopulate = false;
    wrapper.vm.$data.loading = true;

    expect(wrapper.find('custom-loader-stub').exists()).toBe(true)
  })

  it('shows loader if data is populated but loading is true', () => {
    wrapper.vm.$data.hasDataPopulated = true;
    wrapper.vm.$data.loading = true;

    expect(wrapper.find('custom-loader-stub').exists()).toBe(true)
  })
  it('does not show loader if data is populated and loading is false', () => {
    wrapper.vm.$data.hasDataPopulated = true;
    wrapper.vm.$data.loading = false;

    expect(wrapper.find('custom-loader-stub').exists()).toBe(false)
  })

  it('shows `ldap-configure-warning` when message is non-empty', () => {
    wrapper.setData({message: 'test_message'});
    wrapper.vm.$data.hasDataPopulated = true;
    wrapper.vm.$data.loading = false;
    expect(wrapper.find('#ldap-configure-warning').exists()).toBe(true);
  })

  it('hides `ldap-configure-warning` when message is empty', () => {
    wrapper.setData({message: ''});
    wrapper.vm.$data.hasDataPopulated = true;
    wrapper.vm.$data.loading = false;
    expect(wrapper.find('#ldap-configure-warning').exists()).toBe(false);
  })

  it('updates state data correctly(according to the key) when `updateStatesWithData` is called', () => {
    wrapper.vm.updateStatesWithData({ domain: 'test domain', username: 'test username', password: 'test password' });
    expect(wrapper.vm.$data.domain).toBe('test domain');
    expect(wrapper.vm.$data.username).toBe('test username');
    expect(wrapper.vm.$data.password).toBe('test password');
  })

  it('makes `user-import-mapper` component be visible if `is_valid` is 1', () => {
    wrapper.setData({ is_valid: 1, hasDataPopulated: true });
    expect(wrapper.find('user-import-mapper-stub').exists()).toBe(true);
  })

  it('makes `user-import-mapper` component invisible if `is_valid` is 0', () => {
    wrapper.setData({ is_valid: 0, hasDataPopulated: true });
    expect(wrapper.find('user-import-mapper-stub').exists()).toBe(false);
  })

  it('makes `search-basis` component be visible if `is_valid` is 1', () => {
    wrapper.setData({ is_valid: 1, hasDataPopulated: true });
    expect(wrapper.find('search-basis-stub').exists()).toBe(true);
  })

  it('makes `search-basis` component invisible if `is_valid` is 0', () => {
    wrapper.setData({ is_valid: 0, hasDataPopulated: true });
    expect(wrapper.find('search-basis-stub').exists()).toBe(false);
  })

  it('makes API call with correct parameters when submit button is clicked', (done) => {
    wrapper.vm.isValid = () => true;
    wrapper.vm.loading = false
    wrapper.setData({ is_valid: 0, hasDataPopulated: true });
    wrapper.find('#ldap-settings-submit').trigger('click');

    setTimeout(()=>{
      let data = JSON.parse(moxios.requests.mostRecent().config.data)
      expect(data).toHaveProperty('domain')
      expect(data).toHaveProperty('encryption')
      expect(data).toHaveProperty('forgot_password_link')
      expect(data).toHaveProperty('ldap_label')
      expect(data).toHaveProperty('password')
      expect(data).toHaveProperty('port')
      expect(data).toHaveProperty('username')
      expect(data).toHaveProperty('prefix')
      expect(data).toHaveProperty('suffix')
      expect(data).toHaveProperty('schema')
      done();
    }, 1)
  })

})
