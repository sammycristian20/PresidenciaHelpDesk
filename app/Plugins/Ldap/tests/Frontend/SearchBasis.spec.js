import { mount } from '@vue/test-utils'
import SearchBasis from './../../views/js/components/SearchBasis'
import jsdom from 'jsdom';
import sinon from 'sinon'
import moxios from 'moxios';
import { wrap } from 'module';



let wrapper;

const fakeRequestData = {
  message: "0 users found with this query",
  success: true
}

const fakeDeleteRequest = {
  message: "deleted successfully",
  success: true
}

const fakesuccessQuery = {
  message: "0 new users imported from the active directory",
  success: true
}

const JSDOMEnvironment = require('jest-environment-jsdom');

const populateWrapper = () => {
  wrapper = mount(SearchBasis, {
    stubs: ['alert', 'text-field', 'dynamic-select', 'custom-loader', 'modal', 'static-select'],
    propsData: {
      ldapId: '',
      addUser: jest.fn(),
      searchBaseArray: [
        {
          'id': 23,
          'ldap_id': 1,
          'organizations': [],
          'departments': [],
          'user_type': 'admin',
          'search_base': ''
        }
      ],
      showOrganization: true,
      showDepartment: true,

      getLdap: jest.fn(),
    },
    mocks: {
      lang: (string) => string
    }
  });
}

describe('SearchBasis', () => {
  beforeEach(() => {
    populateWrapper();
    moxios.install()
  })

  afterEach(() => {
    moxios.uninstall()
  })


  it('is a vue instance', () => {
    expect(wrapper.isVueInstance()).toBeTruthy()
  })

  /**
   * In SearchBase Arrray the search base field is empty hence the ping button ould not be shown,
   */
  it('the ping button is only visible when  search_basefield is not empty ', () => {
    expect(wrapper.find('#ping-0').exists()).toBe(false);

    /**
     * Setting the serchbase value to make the ping button visible
     */
    wrapper.setProps({
      searchBaseArray: [{
        'id': 23,
        'ldap_id': 1,
        'organizations': [],
        'departments': ["1", "2"],
        'user_type': 'admin',
        'search_base': 'navin'
      }]
    })
    expect(wrapper.find('#ping-0').exists()).toBe(true);
  })

  /**
   * When the User click the Ping Button An api is called to check the satus;
   */
  it('if the uer click the ping button , we have to verify whether the user exists or not', () => {
    wrapper.vm.$data.loading = true;
    wrapper.setProps({
      searchBaseArray: [{
        'id': 23,
        'ldap_id': 1,
        'organizations': [],
        'departments': ["1", "2"],
        'user_type': 'admin',
        'search_base': 'navin'
      }]
    })
    wrapper.find('#ping-0').trigger('click')
    moxios.stubRequest('api/ldap/search-base/ping/1?search_base=something', {
      status: 200,
      response: fakeRequestData
    })

  })

  /**
   * If the Id is present then on delete with help of modal the delete api is being called and,
   * item is removed from array and again the get data is being called
   */

  it('if the id is present  in searchBaseArray then modal would open and through the delete api the particular row of that id will be deleted', () => {
    // expect(wrapper.vm.$data);
    expect(wrapper.vm.$data.deletePopup).toBe(false);

    wrapper.find('#delete-0').trigger('click')

    moxios.stubRequest('api/ldap/search-base/23', {
      status: 200,
      response: fakeDeleteRequest
    })
    sinon.spy(wrapper.vm.$props.getLdap)
    expect(wrapper.vm.$data.deletePopup).toBe(true);

  });

  it('if the id is not present at start then if the user click the delete button modal pop up and on submit click of modal the splice method is being called  ', () => {

    expect(wrapper.vm.$data.deletePopup).toBe(false);
    wrapper.find('#delete-0').trigger('click');
    sinon.spy(wrapper.vm.onSubmitDelete);
    expect(wrapper.vm.$data.deletePopup).toBe(true);
    sinon.spy(wrapper.vm.$props.getLdap)
  });


  it('shows `organization` tab only when `showOrganization` is true and user_type is `user`', () => {
    wrapper.setProps({
      showOrganization: true,
      searchBaseArray: [{ id: 23, organization_ids: [], department_ids: [], user_type: 'user', search_base: '' }]
    });
    expect(wrapper.find('#search-base-0-organization').exists()).toBe(true);
  })
  it('does not show `organization` tab when `showOrganization` is false and user_type is `user`', () => {
    wrapper.setProps({
      showOrganization: false,
      searchBaseArray: [{ id: 23, organization_ids: [], department_ids: [], user_type: 'user', search_base: '' }]
    });
    expect(wrapper.find('#search-base-0-organization').exists()).toBe(false);
  })
  it('shows `department` tab only when `showDepartment` is true and user_type is not equal to `user`', () => {
    wrapper.setProps({
      showDepartment: true,
      searchBaseArray: [{ id: 23, organization_ids: [], department_ids: [], user_type: 'agent', search_base: '' }]
    });
    expect(wrapper.find('#search-base-0-department').exists()).toBe(true);
  })
  it('does not show `department` tab when `showDepartment` is false and user_type is not equal to `user`', () => {
    wrapper.setProps({
      showDepartment: false,
      searchBaseArray: [{ id: 23, organization_ids: [], department_ids: [], user_type: 'agent', search_base: '' }]
    });
    expect(wrapper.find('#search-base-0-department').exists()).toBe(false);
  })

  it('`onSearchBase` filters index  if it is in double digits', () => {
    wrapper.setProps({
      searchBaseArray: [
        { search_base: 'one' }, { search_base: 'two' }, { search_base: 'three' }, { search_base: 'four' },  { search_base: 'five' },
        { search_base: 'six' }, { search_base: 'seven' }, { search_base: 'eight' }, { search_base: 'nine' },  { search_base: 'ten' },
        { search_base: 'eleven' }
      ]
    });
    wrapper.vm.onSearchBase('new_eleven', 'searchbase10')

    expect(wrapper.vm.searchBaseArray[10].search_base).toBe('new_eleven');
  })


})
