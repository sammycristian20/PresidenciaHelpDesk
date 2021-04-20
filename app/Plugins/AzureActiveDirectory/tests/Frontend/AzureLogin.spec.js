import { mount, createLocalVue } from '@vue/test-utils'
import AzureLogin from "../../views/js/components/AzureLogin";
import Vue from 'vue';
import Vuex from "vuex";


window.eventHub  = new Vue();

let localVue = createLocalVue()
localVue.use(Vuex)

let wrapper;


describe('LdapLogin',()=>{

    function mountComponent(label = "", hideDefaultLogin = false) {

        let getters = {getButtonStyle: jest.fn(), getLinkStyle: jest.fn()}

        let store = new Vuex.Store({getters})

        wrapper = mount(AzureLogin, {
            propsData: {
                data: JSON.stringify({ azure_meta_settings: {
                    hide_default_login: hideDefaultLogin,
                    directory_settings: [{id: 1, login_button_label: label}]
                }})
            },
            mocks:{
                lang:(string)=>string
            },
            methods : {
                hideDefaultLogin: jest.fn(),
                basePath: () => '',
              trans: string => string
            },
            store, localVue
        })
    }

    it('displays login_via_azure when label is empty',()=>{
        mountComponent();
        expect(wrapper.find('#azure-login-button-0').text()).toContain('login_via_azure')
    })

    it('displays label when label is  non empty',()=>{
        mountComponent('test');
        expect(wrapper.find('#azure-login-button-0').text()).toContain('test')
    })

    it('displays azure image when hide_default_login is true',()=>{
        mountComponent('test', true);
        expect(wrapper.find('img').exists()).toBe(true);
    })

    it('doesnt display azure image when hide_default_login is false',()=>{
        mountComponent('test', false);
        expect(wrapper.find('img').exists()).toBe(false);
    })
})
