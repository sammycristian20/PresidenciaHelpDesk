import { mount } from "@vue/test-utils"
import LogStatus from "components/LogsTables/ReusableComponent/LogStatus"

let wrapper;

describe("LogStatus",()=>{

	beforeEach(()=>{
    wrapper = mount(LogStatus,{
      propsData : { data : {id : 1, status:"test_status"}},
      mocks : { lang:(string)=> string },
    });
	});

  it("shows class `btn-success` if status is `sent`", ()=>{
    wrapper.setProps({data : {id: 1, status: "sent"}});
    expect(wrapper.find("#log-status-1").classes()).toContain("btn-success");
  })

  it("shows class `btn-success` if status is `accepted`", ()=>{
    wrapper.setProps({data : {id: 1, status: "accepted"}})
    expect(wrapper.find("#log-status-1").classes()).toContain("btn-success")
  })

  it("shows class `btn-warning` if status is `queued`", ()=>{
    wrapper.setProps({data : {id: 1, status: "queued"}})
    expect(wrapper.find("#log-status-1").classes()).toContain("btn-warning")
  })

  it("shows class `btn-danger` if status is `rejected`", ()=>{
    wrapper.setProps({data : {id: 1, status: "rejected"}})
    expect(wrapper.find("#log-status-1").classes()).toContain("btn-danger")
  })

  it("shows class `btn-danger` if status is `failed`", ()=>{
    wrapper.setProps({data : {id: 1, status: "failed"}})
    expect(wrapper.find("#log-status-1").classes()).toContain("btn-danger")
  })

  it('`getStatusTitle` will return proper title for the log status', () => {
    wrapper.setProps({ data : { id: 1, status: 'failed', exception: { line: 13 } }});
    expect(wrapper.vm.getStatusTitle).toBe('failed: Click to view exception details.');

    wrapper.setProps({ data : { id: 1, status: 'accepted', exception: null }});
    expect(wrapper.vm.getStatusTitle).toBe('accepted');
  })

});
