<template>
	<div id="recur-details">
    <div class="row">
      <text-field :label="trans('name')" type="text" name="name" :value="name" :onChange="onChange" :required="true" classname="col-sm-2" rules="required"></text-field>

      <dynamic-select :label="trans('interval')" :multiple="false" name="interval" :elements="intervals" :value="interval" :onChange="onChange" :required="true" classname="col-sm-2" rules="required"></dynamic-select>

      <dynamic-select v-if="interval && (interval.id === 'weekly' || interval.id === 'monthly' || interval.id === 'yearly')" :label="trans('every')" :multiple="false" name="delivery_on" :elements="every" :value="delivery_on" :onChange="onChange" :required="true" classname="col-sm-2" rules="required"></dynamic-select>

      <!-- start date with datetime picker-->
      <div class="col-sm-3">
        <date-time-field :label="lang('start_with')" :value="start_date" type="date" name="start_date" :onChange="onChange" :required="true" format="YYYY-MM-DD" :clearable="true" :disabled="false" :not-after="startDateBefore" rules="required">
        </date-time-field>
      </div>

      <!-- end date with datetime picker -->
      <div class="col-sm-3">
        <date-time-field :label="lang('end_date')" :value="end_date" type="date" name="end_date" :onChange="onChange" :required="false" format="YYYY-MM-DD" :clearable="true" :disabled="false" :not-before="endDateAfter"></date-time-field>
      </div>

    </div>

    <div class="row">
      <div class="col-sm-3">
        <date-time-field :label="lang('execution_time')" :value="execution_time" type="time" name="execution_time" :onChange="onChange" format="HH:mm:ss" :required="false" :clearable="true" :disabled="false">
        </date-time-field>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import { mapGetters } from 'vuex'

export default {

  data () {

    return {
      name: '',
      interval: '',
      delivery_on: '',

      start_date: '',
      startDateBefore: '',
      end_date: '',
      endDateAfter: '',
      execution_time: '',

      // interval options
      intervals: [
        { id: "daily", name: "Daily" },
        { id: "weekly", name: "Weekly" },
        { id: "monthly", name: "Monthly" },
        { id: "yearly", name: "Yearly" }
      ],

      every: [],

      // weekly options
      weekly: [
        { id: "sunday", name: "Sunday" },
        { id: "monday", name: "Monday" },
        { id: "tuesday", name: "Tuesday" },
        { id: "wednesday", name: "Wednesday" },
        { id: "thursday", name: "Thursday" },
        { id: "friday", name: "Friday" },
        { id: "saturday", name: "Saturday" }
      ],

      // monthly options
      monthly: [],

      //  yearly options
      yearly: [
        { id: "january", name: "January" },
        { id: "february", name: "February" },
        { id: "march", name: "March" },
        { id: "april", name: "April" },
        { id: "may", name: "May" },
        { id: "june", name: "June" },
        { id: "july", name: "July" },
        { id: "august", name: "August" },
        { id: "september", name: "September" },
        { id: "october", name: "October" },
        { id: "november", name: "November" },
        { id: "december", name: "December" }
      ],
    };
  },

  components: {
    'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
    'text-field': require('components/MiniComponent/FormField/TextField'),
    'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
  },

  beforeMount () {

    this.formatState(this.getRecurAdditionalInfo);

    for (var i = 1; i <= 31; i++) {
      this.monthly.push({ id: i, name: i });
    }
  },


  computed: {
    ...mapGetters({ getRecurAdditionalInfo: 'getRecurAdditionalInfo' })
  },

  methods: {

    onChange (value, name) {
      this[name] = value;

      this.setFormattedStaticFields(name, value);

      if (this[name] && this[name].id) {
        this.$store.dispatch('updateRecurProperties', { key: name, value: this[name].id });
      } else {
        this.$store.dispatch('updateRecurProperties', { key: name, value: this[name] });
      }
    },

    setFormattedStaticFields(name, value)
    {
      // map over the array and return the element
      if(value && !value.id){
        if(name === "interval"){
          this.interval = this.intervals.find(interval => interval.id == value)

          if (value && (value === 'monthly' || value === 'yearly' || value === 'weekly')) {
            this.every = this[value];
          } else {
            this.every = [];
          }

        } else if(name === "delivery_on") {
          this.delivery_on = this.every.find(object => object.id == value);
        } else if (name === 'start_date') {
          this.start_date = moment(value).format("YYYY-MM-DD");
          this.endDateAfter = value;
        } else if (name === 'end_date') {
          this.end_date = moment(value).format("YYYY-MM-DD");
          this.startDateBefore = value;
        } else if (name === 'execution_time') {
          this.execution_time = this.getExecutionTime(value);
          //For storing only time in `fromRender.js` store file
          this.$store.dispatch('updateRecurProperties', { key: 'execution_time', value: moment(this.execution_time).format("HH:mm:ss") });
        }
      }
    },

    formatState(valueObject) {
      for (const [name, value] of Object.entries(valueObject)) {
        this[name] = value;
        this.setFormattedStaticFields(name, value);
      }
    },

    getExecutionTime(value) {
      return moment(value).format('YYYY-MM-DD HH:mm:ss') == 'Invalid date' ? moment(value, 'HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') : moment(value).format('YYYY-MM-DD HH:mm:ss');
    }
  },

  watch: {
    getRecurAdditionalInfo: {
      handler(valueObject) {
        this.formatState(valueObject);
      },
      deep: true
    }
  }
};
</script>

<style scoped>
.text-red {
  color: red;
}
.ticket-title {
  clear: both;
  padding-top: 20px;
}
.time-period {
  padding-bottom: 5px;
}
#recur-details {
  margin-bottom: 2rem;
}
</style>