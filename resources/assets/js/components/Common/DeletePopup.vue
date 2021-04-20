<template>
	<div class="modal fade" :id="modalId">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{lang('delete') }}</h4>
                </div>
                <div class="modal-body">
                  <p>{{lang(deleteContent)}}</p>
                </div>
                <div class="modal-footer">
                     <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true">&nbsp;&nbsp;</i>{{lang('close') }}</button>
                     <button type="button" @click="deleteFile()" class="btn btn-danger pull-right" data-dismiss="modal">
                      <i class="fas fa-trash" aria-hidden="true">&nbsp;&nbsp;</i>{{lang('delete') }}</button>
                </div>   
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</template>
<script>
  export default{
      props:['deleteContent','modalId'],
      data(){
         return{

         }
      },
      methods:{
      	 //delete 
      	 deleteFile(){
             this.$emit('delete');
      	 }
      }
  }
</script>