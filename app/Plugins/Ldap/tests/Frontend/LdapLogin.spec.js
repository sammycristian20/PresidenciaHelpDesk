import { mount, createLocalVue } from '@vue/test-utils'
import LdapLogin from './../../views/js/components/LdapLogin'
import Vue from 'vue';
import Vuex from "vuex";


window.eventHub  = new Vue();

let localVue = createLocalVue()
localVue.use(Vuex)

let wrapper;


describe('LdapLogin',()=>{

  function mountComponent(label = "", forgotPasswordLink = "") {

    let getters = {getButtonStyle: jest.fn(), getLinkStyle: jest.fn()}

    let store = new Vuex.Store({getters})

    wrapper = mount(LdapLogin, {
      propsData: {
        data: JSON.stringify({ ldap_meta_settings: {
            hide_default_login: false,
            directory_settings: [{id: 1, ldap_label: label, forgot_password_link: forgotPasswordLink}]
          }})
      },
      mocks:{
        lang:(string)=>string
      },
      store, localVue
    })
  }

  it('displays login_via_ldap label is empty',()=>{
    mountComponent();
    expect(wrapper.find('#ldap-login-button-0').text()).toContain('login_via_ldap')
  })

  it('displays ldap-forgot-password-0 if password is non-empty',()=>{
      mountComponent('','test');
      expect(wrapper.find('#ldap-forgot-password-0').exists()).toBe(true)
  })

  it('hides ldap-forgot-password-0 if password is empty',()=>{
    mountComponent('','');
    expect(wrapper.find('#ldap-forgot-password-0').exists()).toBe(false)
  })

  it('disables login button when `login-success` event is fired',(done)=>{
    mountComponent('','');
    setTimeout(() => {
      expect(wrapper.find('#ldap-login-button-0').attributes().disabled).toBe(undefined)
      window.eventHub.$emit('login-success');
      expect(wrapper.find('#ldap-login-button-0').attributes().disabled).toBe("disabled")
      done();
    }, 1)
  })

  it('enabled login button when `login-failure` event is fired',(done)=>{
    mountComponent();
    wrapper.setData({disabled: true})
    setTimeout(() => {
      expect(wrapper.find('#ldap-login-button-0').attributes().disabled).toBe("disabled")
      window.eventHub.$emit('login-failure');
      expect(wrapper.find('#ldap-login-button-0').attributes().disabled).toBe(undefined)
      done();
    }, 1)
  })

  it('enabled login button when `login-failure` event is fired',(done)=>{
    mountComponent();
    wrapper.setData({disabled: true})
    setTimeout(() => {
      expect(wrapper.find('#ldap-login-button-0').attributes().disabled).toBe("disabled")
      window.eventHub.$emit('login-failure');
      expect(wrapper.find('#ldap-login-button-0').attributes().disabled).toBe(undefined)
      done();
    }, 1)
  })

  it('sets login_via_ldap to false when `login-failure` event is fired',(done)=>{
    mountComponent();
    wrapper.setData({login_via_ldap: true})
    setTimeout(() => {
      window.eventHub.$emit('login-failure');
      expect(wrapper.vm.login_via_ldap).toBe(false)
      done();
    }, 1)
  })
})
