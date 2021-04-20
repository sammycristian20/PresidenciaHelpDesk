import { mount, createLocalVue } from '@vue/test-utils'

import MailHover from 'components/LogsTables/ReusableComponent/MailHover.vue'

import moxios from 'moxios'

import Vue from 'vue'

import Vuex from 'vuex'

Vue.use(Vuex)

describe('MailHover',()=>{

	let wrapper;

	const updateWrapper = () =>{

	  	let store

	  	let getters

    	getters = {
      		getUserData: () => () => {return {}},
    	}

	   	store = new Vuex.Store({
	      getters
	    })

		wrapper = mount(MailHover,{
			stubs:['loader','popover'],
			propsData : {
				data : {reciever_mail:'test@gmail.com'},
				objectKey: "reciever_mail"
			},
			mocks:{
            	lang: (string) => string,
        	},
        	store
		});
	}

	beforeEach(()=>{
		moxios.install();
		updateWrapper()
	});

	afterEach(() => {
			moxios.uninstall()
	})


	it('is a vue instance',()=>{

		expect(wrapper.isVueInstance()).toBeTruthy();
	});

	it('calls `popOver` method on mouseeneter event',() => {

		updateWrapper()

	    wrapper.vm.popOver =jest.fn()

	    wrapper.find('#main').trigger('mouseenter')

	    expect(wrapper.vm.popOver).toHaveBeenCalled()
	});

	it('calls `popOver` method on mouseleave event',() => {

		updateWrapper()

	    wrapper.vm.popOver =jest.fn()

	    wrapper.find('#main').trigger('mouseleave')

	    expect(wrapper.vm.popOver).toHaveBeenCalled()
	});

	it('calls `popOver` method with number on `mouseenter` event',()=>{

			updateWrapper()

			wrapper.setProps({data:{id:12}})

			wrapper.vm.popOver =jest.fn()

			wrapper.find('#main').trigger('mouseenter')

			expect(wrapper.vm.popOver).toHaveBeenCalledWith(12)
	});

	it('calls `popOver` method with `null` value on `mouseleave` event',()=>{

			updateWrapper()

			wrapper.setProps({data:{id:12}})

			wrapper.vm.popOver =jest.fn()

			wrapper.find('#main').trigger('mouseleave')

			expect(wrapper.vm.popOver).toHaveBeenCalledWith(null)
	});

	it('calls `getData` method when click on `reciever_mail` value',() => {

		updateWrapper()

	    wrapper.vm.getData =jest.fn()

	    wrapper.find('#action_mail').trigger('click')

	    expect(wrapper.vm.getData).toHaveBeenCalled()
	});

	it('calls `getData` method with correct value',() => {

		updateWrapper()

		wrapper.setProps({data:{id:12,reciever_mail:'test@gmail.com'}})

	    wrapper.vm.getData =jest.fn()

	    wrapper.find('#action_mail').trigger('click')

	    expect(wrapper.vm.getData).toHaveBeenCalledWith('test@gmail.com',12)
	});

	it('show `popover` when ids are matching',() => {
		updateWrapper()

		wrapper.setProps({data:{id:12,reciever_mail:'test@gmail.com'}})

		wrapper.setData({popId : 12})
		expect(wrapper.find('popover-stub').exists()).toBe(true)
	});

	it('does not show `popover` when ids are not matching',() => {
		updateWrapper()

		wrapper.setProps({data:{id:12,reciever_mail:'test@gmail.com'}})

		wrapper.setData({popId : 10})
		expect(wrapper.find('popover-stub').exists()).toBe(false)
	});

	it('makes an API call when `getData` method called', (done) => {

	    updateWrapper();

	    wrapper.setProps({data:{id:12,reciever_mail:'test@gmail.com'}})

	    wrapper.vm.getData('test@gmail.com',12);

	    setTimeout(()=>{
	      expect(moxios.requests.__items.length).toBe(1)
	      expect(moxios.requests.mostRecent().url).toBe('/api/get-user-by-email?email=test@gmail.com')
	      done();
	    },50)
	  });

	it('shows `loader` if loading is true, `popId` and `data.id` are equal ',()=>{
	    updateWrapper();
	    wrapper.setProps({data:{id:12,reciever_mail:'test@gmail.com'}})

		wrapper.setData({popId : 12})
	    wrapper.vm.loading = true;
	    expect(wrapper.find('loader-stub').exists()).toBe(true)
	  });

	it('does not show `loader` if loading is true, `popId` and `data.id` are not equal ',()=>{
	    updateWrapper();
	    wrapper.setProps({data:{id:12,reciever_mail:'test@gmail.com'}})

		wrapper.setData({popId : 10})
	    wrapper.vm.loading = true;
	    expect(wrapper.find('loader-stub').exists()).toBe(false)
	  });

});
