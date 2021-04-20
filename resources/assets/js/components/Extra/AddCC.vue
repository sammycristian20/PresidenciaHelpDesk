<template>
<div class="modal fade" id="addccc" aria-hidden="false"><div class="modal-backdrop fade"></div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" id="cc-close" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{lang('add_collaborator')}}</h4>
                </div>
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#oldUser" data-toggle="tab" id="aa" aria-expanded="true"><i class="fa fa-users"> </i> {{lang('search_existing_users')}}</a></li>
                        <li class=""><a href="#newUser" data-toggle="tab" style="color:orange;" aria-expanded="false"><i class="fa fa-user-plus"> </i> {{lang('add_new_user')}}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="oldUser">
                            <div class="modal-body">
                                <div class="callout callout-info" ><i class="icon fa fa-info"> </i>&nbsp;&nbsp;&nbsp; {{lang('search_existing_users_or_add_new_users')}}</div>
                                <!-- Notification popup-->
                               <alert-notification  :successShow="successDisplay1" :errorShow="errorDisplay1" :successMsg="success1" :errorMsg="error1"></alert-notification>
                               <!-- loader -->
                                <div  v-if="loader1">
                                  <loader :size="60" :duration="4000" :color="'#1d78ff'"></loader>
                                </div>
                                <form accept-charset="UTF-8" v-if="content1">
                                      <select class="form-control" name="search" id="tags" :placeholder="lang('search_by_email')" style="width: 100%" v-model="exitUser.search"  multiple="true"></select><br>
                                      <input type="button" class="btn btn-submit btn-primary" :value="lang('submit')" @click="oldUser()">
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane" id="newUser">
                            <div class="modal-body" >
                                <h4 class="modal-title pull-left">{{lang('add_new_user')}}</h4>            
                                <br><br>
                                 <!-- Notification popup-->
                                 <alert-notification  :successShow="successDisplay2" :errorShow="errorDisplay2" :successMsg="success2" :errorMsg="error2" :extraData="notification2"></alert-notification>
                                 <!-- loader -->
                                  <div v-if="loader2">
                                      <loader :size="60" :duration="4000" :color="'#1d78ff'"></loader>
                                  </div>
                                <form >
                                    
                                    <div id="hide12345">
                                        <input type="text" name="name" class="form-control" v-model="newUser.name" :placeholder="lang('name')" required="">
                                        <input type="text" name="email" class="form-control" v-model="newUser.email" :placeholder="lang('email')" required=""><br>
                                        <input type="submit" class="btn btn-primary" :value="lang('submit')" @click="newUserReg()">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</template>
<script>
  export default{
     props:['treadId'],
     data(){
        return{
             exitUser:{search:[]},
             newUser:{name:"",email:""},
             loader1:false,
             loader2:false,
             error1:'',
             errorDisplay1:'none',
             error2:'',
             content1:true,
             content2:true,
             errorDisplay2:'none',
             success1:'',
             successDisplay1:'none',
             success2:'',
             successDisplay2:'none',
             notification2:'',
        }
     },
     mounted(){
          var vm=this;
          $("#tags").select2({
                    minimumInputLength: 1,
                    ajax: {
                            url: 'ticket/form/requester?type=requester',
                            data: function(term) {
                                    return term;
                            },
                            processResults: function(data) {
                                    return {
                                    results: $.map(data, function (value) {
                                         return {
                                               image : value.profile_pic,
                                               text  : value.first_name+" "+value.last_name,
                                               id    : value.id,
                                               email : value.email
                                             }
                                          })
                                        }
                                      },
                                    cache:true
                            },
                    templateResult: this.formatState, 
                }).on("change", function(event) {
                                 var values = [];    
                        $(event.currentTarget).find("option:selected").each(function(i, selected){ 
                                values[i] = $(selected).val();
                        });
                         vm.exitUser.search=values;
                      })
     },
     components: {
            'loader':require('./Loader.vue'),
        },
     methods:{
        formatState(state){
              var $state = $( '<div><div style="width: 8%;display: inline-block;"><img src='+state.image+' width="35px" height="35px" style="vertical-align:inherit"></div><div style="width: 90%;display: inline-block;"><div>'+state.text+'</div><div>'+state.email+'</div></div></div>');
                   return $state;
        },
         //submit new user
         newUserReg(){
                    var vm=this;
                     vm.newUser['ticket-id']=vm.treadId;
                     vm.loader2=true;
                     vm.content2=false;
                    axios.post('api/ticket/create-add-collaborator',vm.newUser).then((resp)=>{
                         vm.success2=resp.data.message;
                         vm.successDisplay2="block";
                         vm.content2=true;
                         setTimeout(()=>{
                             vm.successDisplay2="none";
                         },2000)
                         vm.loader2=false;
                         window.eventHub.$emit('getTicketInfo',vm.treadId);
                   }).catch((resp)=>{
                        vm.loader2=false;
                        vm.error2=resp.response.data.message;
                        vm.errorDisplay2="block";
                         setTimeout(()=>{
                             vm.errorDisplay2="none";
                         },2000)
                   })
         },
         //submit old user
         oldUser(){
                   var obj={},vm=this;
                   vm.loader1=true;
                   obj['ticket-id']=vm.treadId;
                   obj['collaborator-ids']=vm.exitUser.search;
                   axios.post('api/ticket/collaborators',obj).then((resp)=>{
                         vm.success1=resp.data.message;
                         vm.successDisplay1="block";
                         setTimeout(()=>{
                             vm.successDisplay1="none";
                         },2000)
                         vm.loader1=false;
                         window.eventHub.$emit('getTicketInfo',vm.treadId);
                   }).catch((resp)=>{
                        vm.loader1=false;
                        vm.error1=resp.response.data.message;
                        vm.errorDisplay1="block";
                         setTimeout(()=>{
                             vm.errorDisplay1="none";
                         },2000)
                   })
         }
     }
  }
</script>