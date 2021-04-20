<template>
	 <div class="modal fade" id="surrender2" aria-hidden="false" ><div class="modal-backdrop fade"></div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="updateCollabrator()"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">{{lang('list_of_collaborators_of_this_ticket')}}</h4>
                </div>
                 <div class="modal-body" >
                         <!-- alert and notice -->
                        <alert-notification  :successShow="statusSuccessAlert" :errorShow="statusErrorAlert" :successMsg="success" :errorMsg="error"></alert-notification>
                      <div v-for="(collaborator,index) in ticket.collaborators">
                        <div id="alert11" class="alert alert-dismissable" style="background-color:#F2F2F2">
					                        <button id="dismiss11" type="button" class="close"  @click="remove_collaborator(collaborator,index)" aria-hidden="true">×</button>
                                                <i class="icon fa fa-user"></i><span v-if="collaborator.first_name!=''">{{collaborator.first_name}} {{collaborator.last_name}}</span><span></span>
                                                <div id="message-success1">{{collaborator.email}}</div>
                        </div>
                      </div>
                 </div>
                
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</template>
<script>
  
  export default{
  	  props:['ticket','treadId'],
  	  data(){
  	  	  return{
              statusSuccessAlert:'none',
              statusErrorAlert:'none',
              success:'',
              error:''
  	  	  }
  	  },
      methods:{
         //remove collablretor
         remove_collaborator(x,y){
              var vm=this;
              vm.ticket.collaborators.splice(y,1)
              axios.post("remove-user",{'data1':x.collaborator_id}).then(function(resp){
                         if(resp.data==1){
                            vm.statusSuccessAlert='block';
                            vm.success='collaborator_removed_successfully';
                            setTimeout(()=>{
                                  vm.statusSuccessAlert='none';
                                  vm.success='';
                            },2000)
                         }
                      }).catch(function(resp){
                         console.log(resp.response.data);
                         vm.loaderOption=false;
                      })
         },
         //update collabrator
         updateCollabrator(){
                window.eventHub.$emit('getTickets');
         }
      }
  }
</script>