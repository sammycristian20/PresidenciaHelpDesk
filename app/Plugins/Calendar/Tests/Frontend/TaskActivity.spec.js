import { shallow, createLocalVue,  mount } from '@vue/test-utils'

import sinon from 'sinon'

import Vue from 'vue'

import Vuex from 'vuex'

import moxios from 'moxios';

import * as extraLogics from "helpers/extraLogics";

import TaskActivity from "../../views/js/components/TaskActivity";

let localVue = createLocalVue()

localVue.use(Vuex)

window.HTMLElement.prototype.scrollIntoView = function() {};

window.eventHub = new Vue();

describe('TaskActivity',() => {

    let wrapper;

    let store;

    let getters;

    getters = {

        formattedTime: () => () => {return ''},

        formattedDate:()=> () => {return ''},
    }

    store = new Vuex.Store({ getters })

    const updateWrapper = () =>{

        wrapper = mount(TaskActivity,{

            stubs: ['vuejs-uib-pagination','loader','custom-loader', 'faveo-image-element'],

            mocks:{

                lang:(string)=>string
            },

            methods : {

                basePath : jest.fn()
            },

            propsData : {

                taskId : 1
            },

            attachToDocument: true,

            localVue, store
        })
    }

    beforeEach(() => {

        updateWrapper();

        moxios.install();


    })

    afterEach(() => {

        moxios.uninstall()
    })

    it('is vue instance',() => {

        expect(wrapper.isVueInstance()).toBeTruthy();
    });

    it('initial values',()=>{

        expect(wrapper.vm.paramsObj).toEqual({"page": 1})

        expect(wrapper.vm.PerPage).toEqual(10)

        expect(wrapper.vm.Records).toEqual(0)
    })

    it('calls `getValues` method when `commonFilter` called',()=>{

        wrapper.vm.getValues = jest.fn();

        wrapper.vm.commonFilter();

        expect(wrapper.vm.filtering).toEqual(true)

        expect(wrapper.vm.pagination.currentPage).toEqual(1)

        expect(wrapper.vm.paramsObj).toEqual({"page": 1})

        expect(wrapper.vm.getValues).toHaveBeenCalledWith(wrapper.vm.paramsObj)

    })

    it('updates `paramsObj` value and calls `commonFilter` method when `logLimit` method called',()=>{

        wrapper.vm.commonFilter = jest.fn();

        wrapper.vm.logLimit(25);

        expect(wrapper.vm.paramsObj).toEqual({"limit": 25, "page": 1})

        expect(wrapper.vm.commonFilter).toHaveBeenCalled()

    })

    it('updates `paramsObj` value and calls `commonFilter` method when `orderBy` method called',()=>{

        wrapper.vm.commonFilter = jest.fn();

        wrapper.vm.orderBy('asc');

        expect(wrapper.vm.sort_key).toEqual('asc')

        expect(wrapper.vm.paramsObj).toEqual({"sort_order": "asc", "page": 1})

        expect(wrapper.vm.commonFilter).toHaveBeenCalled()

    })

    it('makes an API call', (done) => {

        updateWrapper();

        wrapper.vm.getValues(wrapper.vm.paramsObj);

        stubRequest();

        setTimeout(()=>{

            expect(moxios.requests.mostRecent().url).toBe('/tasks/api/activity/1?page=1')

            expect(wrapper.vm.loading).toBe(false)

            expect(wrapper.vm.filtering).toBe(false)

            expect(wrapper.vm.total).toEqual(1)

            expect(wrapper.vm.perPage).toEqual(10)

            expect(wrapper.vm.activity_log).toEqual([{"creator": {"id": 1, "user_name": "name", 'profile_pic' : 'pic'}, "created_at": "20-10-2019", "description": "description"}])

            done();
        },50)
    })

    it("updates `data` values if api returns error response",(done)=>{

        updateWrapper();

        wrapper.vm.getValues(wrapper.vm.paramsObj);

        stubRequest(400);

        setTimeout(()=>{

            expect(wrapper.vm.loading).toBe(false)

            expect(wrapper.vm.filtering).toBe(false)

            expect(wrapper.vm.total).toEqual(0)

            expect(wrapper.vm.perPage).toEqual('10')

            done();
        },50)
    })

    it('`checkDate` method returns true if it called with zero',()=>{

        expect(wrapper.vm.checkDate(0)).toEqual(true)
    })

    it('`checkDate` method returns undefined(formattedDate method returns empty value so i am assuming it retuns undefined) if it called with one',()=>{

        wrapper.setData( { activity_log : [{ description : 'description', created_at : '20-10-2019',causer_name : { id : 1, user_name :'name'}},{ description : 'description', created_at : '20-10-2019',causer_name : { id : 1, user_name :'name'}}]})

        expect(wrapper.vm.checkDate(1)).toEqual(undefined)
    })

    it('`showThreadEnd` method returns true if index is equal to activity_log length',()=>{

        wrapper.setData({ activity_log : [ { created_at : '2019-10-10', description : 'test', causer_name : { id :1, user_name:'user'}}]})

        expect(wrapper.vm.showThreadEnd(0)).toEqual(true);
    })

    it('calls `getValues` method when page changed',()=>{

        wrapper.vm.getValues = jest.fn()

        wrapper.setData({ pagination : {currentPage : 2}})

        expect(wrapper.vm.paramsObj).toEqual({"page": 2})

        expect(wrapper.vm.getValues).toHaveBeenCalledWith(wrapper.vm.paramsObj)

    })

    function stubRequest(status = 200,url = '/tasks/api/activity/1?page=1'){

        moxios.uninstall();

        moxios.install();

        moxios.stubRequest(url,{

            status: status,

            response : {

                data : {

                    data : [{ description : 'description', created_at : '20-10-2019',creator : { id : 1, user_name :'name', profile_pic : 'pic'}}],

                    total : 1,

                    per_page : 10,
                }
            }
        })
    }
})