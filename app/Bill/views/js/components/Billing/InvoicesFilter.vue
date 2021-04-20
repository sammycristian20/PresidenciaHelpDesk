<template>
  <div class="card card-light">
    
    <div class="card-header">
        
        <h3 class="card-title">{{lang('filter')}}</h3>
    </div>
    <div class="card-body">

      <div class="row" v-if="loading" style="margin-top: 80px;margin-bottom: 94px;">
        
        <h3 class="text-center">Loading filters...</h3>
      </div>

      <div class="row" v-if="!loading" id="invoice_filter">
        
        <date-time-field :label="lang('created_date')" :value="created_date" type="datetime" name="created_date"
            :onChange="onChange" range :required="false" format="YYYY-MM-DD HH:mm:ss" classname="col-sm-4" 
            :clearable="true" :disabled="false" :editable="true":currentYearDate="false" :time-picker-options="timeOptions">
              
        </date-time-field>

        <date-time-field :label="lang('due_date')" :value="due_date" type="datetime" name="due_date"
            :onChange="onChange" range :required="false" format="YYYY-MM-DD HH:mm:ss" classname="col-sm-4" 
            :clearable="true" :disabled="false" :editable="true":currentYearDate="false" :time-picker-options="timeOptions">
              
        </date-time-field>

        <number-field :label="lang('amount_paid')" :value="amount_paid" name="amount_paid" classname="col-sm-2"
          :onChange="onChange" type="number">
        
        </number-field>

        <number-field :label="lang('payable_amount')" :value="payable_amount" name="payable_amount" classname="col-sm-2"
          :onChange="onChange" type="number">
        
        </number-field>

      </div>

      <div class="row" v-if="!loading">

        <static-select :label="lang('status')"  :elements="statuses" name="status"
          :value="status" classname="col-sm-4 invoice_select" :onChange="onChange" :required="false">
        
        </static-select>

        <dynamic-select name="payment_mode" classname="col-sm-4" :elements="modes" :multiple="true"
          :prePopulate="false" :label="lang('payment_mode')" :value="payment_mode" :onChange="onChange">
        
        </dynamic-select>

        <dynamic-select name="users" apiEndpoint="api/dependency/users?meta=true" classname="col-sm-4"  :multiple="true"
          :prePopulate="true" :label="lang('users')" :value="users" :onChange="onChange">
        
        </dynamic-select>

      </div>
    </div>

    <div class="card-footer" v-if="!loading">
      
      <span class="single-btn">
        
        <button id="apply-btn" class="btn btn-primary round-btn" type="button" @click="onApply">
          
          <span class="fas fa-check"></span>&nbsp; {{ lang('apply')}}
        </button>
      </span>
      
      <span class="single-btn">
       
        <button id="apply-btn" class="btn btn-primary single-btn round-btn" type="button" @click="onReset">
          
          <span class="fas fa-undo"></span>&nbsp; {{ lang('reset')}}
        </button>
      </span>
      
      <span class="single-btn">
       
        <button id="apply-btn" class="btn btn-danger single-btn round-btn" type="button" @click="onCancel">
          
          <span class="fas fa-times"></span>&nbsp; {{ lang('cancel')}}
        </button>
      </span>
    </div>
  </div>
</template>

<script>

    export default {
        
        name : "invoices-filter",

        description : "",

        components: {
        
        'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),

        'static-select': require("components/MiniComponent/FormField/StaticSelect"),
        
        'number-field': require("components/MiniComponent/FormField/NumberField"),
        
        'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),

        'loader':require('components/Client/Pages/ReusableComponents/Loader'),
      },

      props:{
        
        apiChange : { type : Function},

        hideFilter : { type : Function},

        data : { type : String| Object, default : '' }
      },

      data(){
        
        return{

          created_date : '',

          due_date : '',

          amount_paid : '',

          payable_amount : '',

          statuses : [
            {id: 'paid', name: 'Paid', queryPerm: 'status=1'},
            {id: 'unpaid', name: 'Unpaid', queryPerm: 'status=0'}
          ],

          status : '',

          modes : [
            {id: 'online', name: 'Online', queryPerm: 'payment_mode[0]=online'},
            {id: 'cash', name: 'Cash', queryPerm: 'payment_mode[1]=cash'},
            {id: 'bank_transfer', name: 'Bank transfer', queryPerm: 'payment_mode[2]=bank_transfer'},
            {id: 'marked_paid_by_agent', name:'Marked paid by agent', queryPerm: 'payment_mode[3]=marked_paid_by_agent'},
          ],

          payment_mode : [],

          users : [],

          timeOptions:{ start: '00:00', step: '00:30', end: '23:30' },

          loading : false,

          filterApplied : false
        }
      },
      
      beforeMount(){

        if(window.location.search.substring(1) === 'status=0'){
          
          this.status = 'unpaid'

          this.apiChange(this.$data);
        }

        if(this.data){

          const self = this;

          const stateData = this.$data;
          
          Object.keys(this.data).map(key => {
            
            if (stateData.hasOwnProperty(key)) {
              self[key] = this.data[key];
            }
      });
        }
      },

      methods:{

        resetData(){

          this.filterApplied = false;

          this.created_date = '';

          this.due_date = '';

          this.amount_paid = '';

          this.payable_amount = '';

          this.status = '';

          this.payment_mode = [];

          this.users = [];

          setTimeout(()=>{

            this.loading = false
          },1000)
        },
        
        onChange(value, name){

          this[name] = value;
        },

        onCancel() {

          this.hideFilter(this.$data);

          if(this.filterApplied){
            
            this.apiChange(this.$data);
         
          } else {
         
            this.resetData()
          }
        },

        onApply() {

          this.filterApplied = true;

          this.apiChange(this.$data);
        },
        onReset() {
          
          this.loading = true;

          this.resetData();

          this.apiChange(this.$data);
        }
      },
    };
</script>