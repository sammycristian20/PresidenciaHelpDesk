<template>
	<div class="col-sm-4 margin-bottom-15">
	    <div>
          <label>{{label}}</label>

          <v-select
		  	ref='faveoDynamicSelect'
						class='faveo-dynamic-select'
						label="text"
						:options="filter_options"
            @input="function(val) { tagsChanged(keyname,val); }"
            v-model="filter_selected"
          >
          </v-select>

	    </div>
	    <div class="col-sm-3" v-show="customRange">
  	     <label>Time period for {{label}}</label>
         <input type="text" :id="dateRange"  class="form-control" v-model="timeRange">
	    </div>
	 </div>
</template>
<script>
     import moment from 'moment';
     import momentTimezone from 'moment-timezone';
		 import {boolean} from 'helpers/extraLogics';
		 
		 import vSelect from "vue-select";

     export default{
     	props:['filterArray','label','keyname','dateRange',

        /**
         * selected options(Gets updated to `filter_selected` as soon as its value changes) (Array of objects)
         * @type {Array}
         */
        'value',

        /**
         * method that gets called as soon as any option is selected here, to notify parent about the updated values
         * @type {Function}
         */
        'updateFilter',

        /**
         * If this component is required to be reset
         * @type {Boolean}
         */
        'reset'
      ],
		data(){
			return{
				filter_options:this.filterArray,
				filter_selected: null,
				filter_obj:{},
				customRange:false,
        timeRange:''
			}
		},
		mounted(){
			    			if(this.keyname=="last-modified-start"){
				$('#custom-update-range').daterangepicker({
			        timePicker: true,
			        timePickerIncrement: 15,
			        startDate: moment().startOf('hour'),
    				endDate: moment().startOf('hour').add(32, 'hour'),
			        locale: {
			            format: 'YYYY-MM-DD HH:mm:SS'
			        },
			        maxDate: moment()
			    }).on('apply.daterangepicker', (ev, picker)=> {
                          this.filter_obj['last-modified-start']= moment(picker.startDate._d).format('YYYY-MM-DD HH:mm:ss');
                          this.filter_obj['last-modified-end']= moment(picker.endDate._d).format('YYYY-MM-DD HH:mm:ss');
			  	          this.$emit('selected',this.filter_obj);
                });
			} else {
				$('#custom-created-range').daterangepicker({
			        timePicker: true,
			        timePickerIncrement: 15,
			        startDate: moment().startOf('hour'),
    				endDate: moment().startOf('hour').add(32, 'hour'),
			        locale: {
			            format: 'YYYY-MM-DD HH:mm:SS'
			        },
			        maxDate: moment()
			    }).on('apply.daterangepicker', (ev, picker)=> {
                          this.filter_obj['created-start']= moment(picker.startDate._d).format('YYYY-MM-DD HH:mm:ss');
                          this.filter_obj['created-end']= moment(picker.endDate._d).format('YYYY-MM-DD HH:mm:ss');
			  	          this.$emit('selected',this.filter_obj);
                });
			}
		},

    watch:{

      value(val){
		  	/**
			 * Triger `input` event if value changed
			 * Used in case of autofill the selected values
			 * Though it's not a good practice to emit event of plugin wrapper, we don't have any solution to update the sected valyes to the parent
			 * This should be ommited when this component will be rewritten
			 */
			this.$refs.faveoDynamicSelect.$emit("input", val);
		},

      reset(val){
        if(val){
          this.filter_selected = null;
          this.filter_obj = {};
        }
      }
    },

		methods:{
			  tagsChanged(x,arrayList){
          this.filter_selected = arrayList;
          if(arrayList!=null){
			  	 if(x!='due-on'&&x!='created-start'&&x!='last-modified-start'){
			    	for(var i in arrayList){
			    		if(i=='id'){
			    		   this.filter_obj[x]=arrayList[i];
			    	   }
			    	};
			    	console.log(this.filter_obj);
			    	this.$emit('selected',this.filter_obj);
			    }
			    else if(x=='due-on'){
			    	for(var i in arrayList){
			    		if(i=='id'){
			    			if(arrayList[i]!='today'){
			    			   this.getTime(x,arrayList[i])
			    			}
			    			else{
                               this.getEoD(x,new Date());
			    			}
			    	   }
			    	};

			    }
			    else if(x=='created-start'||x=='last-modified-start'){
			    	for(var i in arrayList){
			    		if(i=='id'){
			    			if(arrayList[i]=='custom'){
			    			    this.customRange=true;
			    			    var timezone=this.$store.getters.timezone;
			    			    if(this.keyname=="created-start"){
			                          this.filter_obj['created-start']=moment.tz(timezone).startOf('day').format('YYYY-MM-DD HH:mm:ss');
			                          this.filter_obj['created-end']= moment().format('YYYY-MM-DD HH:mm:ss');
			                          console.log(this.filter_obj);
						  	          this.$emit('selected',this.filter_obj);
						    	 }
						    	 else{
			                          this.filter_obj['last-modified-start']= moment.tz(timezone).startOf('day').format('YYYY-MM-DD HH:mm:ss');
			                          this.filter_obj['last-modified-end']= moment().format('YYYY-MM-DD HH:mm:ss');
			                          console.log(this.filter_obj);
						  	          this.$emit('selected',this.filter_obj);
						    	 }

			    			}
			    			else if(arrayList[i]=='today'){
                                this.customRange=false;
                                this.getEoD(x,new Date());
			    			}
			    			else if(arrayList[i]=='5'||arrayList[i]=='10'||arrayList[i]=='15'||arrayList[i]=='30'){
                                 this.customRange=false;
                                 this.getMint(x,arrayList[i]);
			    			}
			    			else{
                                  this.customRange=false;
                                  this.getTime(x,arrayList[i]);
			    			}
			    	   }
			    	};
			      }
		     	}
			    else{
			    	 this.customRange=false;
			    	if(this.keyname=="created-start"){
			                this.filter_obj['created-start']="";
			                this.filter_obj['created-end']= "";

						}
						else if(this.keyname=="last-modified-start"){
			                    this.filter_obj['last-modified-start']= "";
			                    this.filter_obj['last-modified-end']="";

						 }
						else{
                                 this.filter_obj[x]="";
						}
                    this.$emit('selected',this.filter_obj);
			    }

          //TODO: find a way for custom date range
          let selectedFilterWithKey = {};
          selectedFilterWithKey[this.keyname] = this.filter_selected;
          this.updateFilter(selectedFilterWithKey);
			  },
			  //get minutes
			  getMint(x,y){
                      var day=new Date();

				  	  var timezone=this.$store.getters.timezone;

				  	  var minutes=moment(day.setMinutes(day.getMinutes()-y)).tz(timezone).format("YYYY-MM-DD HH:mm:ss");
				  	  if(this.keyname=="created-start"){
			                this.filter_obj['created-start']= String(minutes);
			                this.filter_obj['created-end']= moment().tz(timezone).format('YYYY-MM-DD HH:mm:ss');

						}
						else if(this.keyname=="last-modified-start"){
			                    this.filter_obj['last-modified-start']=String(minutes);
			                    this.filter_obj['last-modified-end']=moment().tz(timezone).format('YYYY-MM-DD HH:mm:ss');

						 }
                        else{
                        	    this.filter_obj[x]= String(minutes);
                        }
                        console.log(this.filter_obj)
				  	  this.$emit('selected',this.filter_obj);
			  },
			  //get time
			  getTime(x,y){
			  	  var day=new Date();
			  	  var timezone=this.$store.getters.timezone;
			  	  var hour=moment(day.setHours(day.getHours()-y)).tz(timezone).format("YYYY-MM-DD HH:mm:ss");
			  	  if(this.keyname=="created-start"){
			                this.filter_obj['created-start']=String(hour);
			                this.filter_obj['created-end']=moment().tz(timezone).format('YYYY-MM-DD HH:mm:ss') ;

						}
						else if(this.keyname=="last-modified-start"){
			                    this.filter_obj['last-modified-start']=String(hour);
			                    this.filter_obj['last-modified-end']=moment().tz(timezone).format('YYYY-MM-DD HH:mm:ss');

						 }
                        else{
                        	    this.filter_obj[x]= String(hour);
                        }
                        console.log(this.filter_obj)
			  	  this.$emit('selected',this.filter_obj);
			  },
			  //get end of day
			  getEoD(x,y){
					var timezone=this.$store.getters.timezone;
					//var start = zone.clone().startOf('day').utc();
					var start = moment.tz(y,timezone).startOf('day').format("YYYY-MM-DD HH:mm:ss");
					var end = moment.tz(y,timezone).endOf('day').format("YYYY-MM-DD HH:mm:ss");
					if(this.keyname=="created-start"){
			                this.filter_obj['created-start']=String(start);
			                this.filter_obj['created-end']= moment().tz(timezone).format('YYYY-MM-DD HH:mm:ss');

						}
						else if(this.keyname=="last-modified-start"){
			                    this.filter_obj['last-modified-start']=String(start);
			                    this.filter_obj['last-modified-end']=moment().tz(timezone).format('YYYY-MM-DD HH:mm:ss');


						 }
                        else{
                        	    this.filter_obj[x]= String(end);
                        }
			  	    this.$emit('selected',this.filter_obj);
              },
            //show placeholder
		    showPlaceholder(){
		    	return "Search or select "+this.label.toLowerCase();
		    }

		},

		components: {
			'v-select': vSelect,
		},
	}
</script>

<style scoped>
	#faveo-dynamic-select-img {
		border: 1px solid transparent;
		width: 18px;
		border-radius: 10px;
		margin-top: -1px;
	}

</style>
