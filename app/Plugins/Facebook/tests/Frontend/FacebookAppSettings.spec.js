import FacebookAppSettings from './../../views/js/components/FacebookAppSettings';
import { shallowMount } from '@vue/test-utils';
import moxios from 'moxios'

const fakeRequestData = {
    'success':true,
    'data':{}
}

const getAppDetails = jest.fn();

describe('FacebookAppSettings', () => {

    let wrapper;

    beforeEach(() => {
        moxios.install()

        wrapper = shallowMount(FacebookAppSettings,{
            mocks:{
                stubs:['text-field','faveo-box','alert','custom-loader'],
                lang:(string)=>string,
                methods: {
                    getAppDetails
                }
            },
        })

    })

    it('Dynamic-select should exists when page created', () => {

        expect(wrapper.find('text-field-stub').exists()).toBe(true)
        expect(wrapper.find('faveo-box-stub').exists()).toBe(true)

    });

    it('checks for facebook verify token', async() => {
        moxios.stubRequest('facebook/api/security-settings/index',{
            status:200,
            response:fakeRequestData
        })

        wrapper.vm.getAppDetails();
        await wrapper.vm.$nextTick()
        expect(moxios.requests.mostRecent().config.url).toBe('facebook/api/security-settings/index');

    })

})