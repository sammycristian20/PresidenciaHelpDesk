import FacebookCreateEdit from './../../views/js/components/FacebookCreateEdit';
import { shallowMount } from '@vue/test-utils';
import moxios from 'moxios'
import Vue from 'vue';
import sinon from 'sinon';

window.eventHub = new Vue()

const fakeRequestData = {
    'success':true,
    'data':{}
}

const fillFieldsIfEdit = jest.fn();

describe('FacebookCreateEdit', () => {

    let wrapper;
  
    beforeEach(() => {
        moxios.install()

        wrapper = shallowMount(FacebookCreateEdit,{
            mocks:{
                stubs:['dynamic-select','text-field','faveo-box','alert','custom-loader'],
                lang:(string)=>string,
                methods: {
                    fillFieldsIfEdit
                }
            },
        })
  
    })

    it('Dynamic-select should exists when page created', () => {
    
        expect(wrapper.find('dynamic-select-stub').exists()).toBe(true)
        expect(wrapper.find('text-field-stub').exists()).toBe(true)
        expect(wrapper.find('faveo-box-stub').exists()).toBe(true)

    });

    it('should call `PUT` apiEndpoint when the form is of type update', async () => {

        const id = 1;

        const url = `facebook/api/integration/${id}`;

        moxios.stubRequest(url,{
            status:200,
            response:fakeRequestData
        })

        wrapper.setData({
            id: id
        });

        wrapper.vm.submit();
        await wrapper.vm.$nextTick()
        expect(moxios.requests.mostRecent().config.url).toBe(url);

    });

    it('should call `POST` apiEndpoint when the form is of type create', async () => {

        const url = `facebook/api/integration`;

        moxios.stubRequest(url,{
            status:200,
            response:fakeRequestData
        })

        wrapper.vm.submit();
        await wrapper.vm.$nextTick()
        expect(moxios.requests.mostRecent().config.url).toBe(url);

    });
})