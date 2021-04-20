import { mount } from '@vue/test-utils'

import LogsModal from 'components/LogsTables/ReusableComponent/LogsModal'

import sinon from 'sinon'

import moxios from 'moxios'

let wrapper;

describe('LogsModal',()=>{

	beforeEach(()=>{

		moxios.install()

	});

	afterEach(()=>{
		moxios.uninstall()
	});

	const populateWrapper = ()=>{
	   	wrapper = mount(LogsModal,{
	   		propsData : { data : { id:1,trace : 'trace'}},
	   		mocks : { lang:(string)=> string },
			stubs:['modal','loader'],
		});
	}


	it('Is a vue instance',()=>{

		populateWrapper()

		expect(wrapper.isVueInstance()).toBe(true)
	});

	it('Does not show modal popup if showModal is false',() => {

		expect(wrapper.find('modal-stub').exists()).toBe(false)
	});

	it('Show modal popup if showModal is true',() => {

		populateWrapper()

		wrapper.setProps({ showModal : true })

		expect(wrapper.find('modal-stub').exists()).toBe(true)
	});

	it('Initialy loading value should be false', () => {

		expect(wrapper.vm.loading).toBe(false);
	});

	it('Calls `getLogsContent` method when title is `logs_content` button',()=>{

		populateWrapper();

		wrapper.setProps({ title : 'logs_content', data : { id :1} })

		wrapper.vm.getLogsContent = jest.fn()

		wrapper.vm.checkTitle();

		expect(wrapper.vm.getLogsContent).toHaveBeenCalled()
	});

	it('Makes an API call when `getLogsContent` method called', (done) => {

	    populateWrapper();

		wrapper.vm.getLogsContent(1)

	    setTimeout(()=>{

	      expect(moxios.requests.__items.length).toBe(1)

	      expect(moxios.requests.mostRecent().url).toBe('/api/get-log-mail-body/1')

	      done();
	    },50)
	});

});
