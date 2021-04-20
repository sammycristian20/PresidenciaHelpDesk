<template lang="html">
   <div class="col-sm-4 main-container">
      <label v-if="labels">{{ isDatePicker ? lang(labels[1]) : lang(labels[0]) }}
        <i
          @click="onClearField"
          class="fa fa-times clear-btn"
          title="Clear"
          aria-hidden="true"
          v-if="fieldCanBeCleared">
        </i>
      </label>
      <div class="input-group">

        <select
          class="form-control last-next-width"
          v-if="!isDatePicker && isLastNextSelector"
          v-model="nextOrLast"
          v-on:change="onValueChange()">
          <option value='last' selected>{{lang('last')}}</option>
          <option value='next'>{{lang('next')}}</option>
        </select>

        <input
          v-if="!isDatePicker"
          v-on:input="onValueChange()"
          placeholder="n"
          type="number"
          @keypress="allowPositiveNumberOnly"
          class="form-control measure-input hide-number-spinner"
          v-model="measure"
          aria-label="..."
        />

        <select
          class="form-control"
          v-if="!isDatePicker"
          v-model="timeUnit"
          v-on:change="onValueChange()">
          <option
            v-for="unit in unitListArr"
            v-bind:key="unit.id"
            :value="unit.id">
            {{ lang(unit.value) }}
          </option>
        </select>

        <date-picker
          v-show="isDatePicker"
          v-model="timeRange"
          :time-picker-options="{ start: '00:00', step: '00:30', end: '23:30' }"
          :not-before="notBefore"
          :not-after="notAfter"
          v-on:confirm="onDatePickerConfirm()"
          input-class="form-control cust-datepicker"
          :format="dateTimeFormat"
          lang="en"
          type="datetime"
          placeholder="Enter time range"
          :clearable="false"
          confirm
          range
          range-separator="~">
        </date-picker>

        <button
          class="btn btn-default range_btn"
          type="button"
          @click="isDatePicker=!isDatePicker">
            <i
              class="fas fa-pencil-alt"
              title="Enter by hand"
              aria-hidden="true"
              v-if="isDatePicker">
            </i>
            <i
              class="far fa-calendar"
              title="Use datepicker"
              aria-hidden="true"
              v-else>
            </i>
        </button>
      </div>
   </div>
</template>

<script lang="js">
import moment from 'moment';
import DatePicker from 'vue2-datepicker'

export default {
    name: 'time-range-filter',

    components: {
        DatePicker,
        'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
        'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),
    },
    props: {
        labels: {
            type: Array | String,
            required: true
        },
        identifier: {
            type: String,
            required: true
        },
        options: {
            type: Array,
            required: true
        },
        value: {
            required: true
        },
        reset: {
            type: Boolean,
            required: true
        },
        /**
         * if true, user can select last or next field
         */
        isLastNextSelector: {
          type: Boolean,
          default: () => false
        }
    },
    beforeMount() {
      if (this.value) {
        this.setInitialValue();
      }
    },
    mounted() {
        const nextTimeRangeOptions = ['created-at', 'updated-at'];
        if (nextTimeRangeOptions.includes(this.identifier)) {
            // this.notBefore = moment().subtract(1, 'years'); // creted/updated at not before 1 year
            this.notAfter = this.presentTime;
        } else if (this.identifier === 'due-on') {
            this.notBefore = this.presentTime;
            // this.notAfter = moment().add(6, 'months'); // Due on not after 6 months
        }
    },
    data() {
        const valueType = {
            value2date: value => {
                return value ? moment(new Date(value), this.dateTimeFormat).toDate() : null;
            },
            date2value: date => {
                return date ? moment(date).format(this.dateTimeFormat) : null;
            }
        }
        return {
            measure: null,
            isDatePicker: false,
            timeUnit: null,
            timeRange: null,
            nextOrLast: 'last',
            unitListArr: this.options,
            notBefore: this.presentTime,
            notAfter: this.presentTime,
            dateTimeFormat: 'YYYY-MM-DD HH:mm:ss',
            presentTime: moment().format(this.dateTimeFormat)
        }
    },
    methods: {
        onValueChange() {
          if (Number(this.measure) > 0 && this.timeUnit) {
            this.saveUserInput();
          } else {
            this.emitEmptyValue();
          }
        },

        onDatePickerConfirm() {
            if (!this.timeRange) return;
            let data = {};
            const formatedDateTime = 'date::' + this.timeRange.map(date => moment(date).format(this.dateTimeFormat)).join('~');
            data[this.identifier] = formatedDateTime ;
            this.eventEmitter('selected', { data: data, isTimeRangeFilter: true });
        },

        eventEmitter(eventName, dataToEmit) {
            this.$emit(eventName, dataToEmit);
        },

        saveUserInput() {
            let data = {};
            data[this.identifier] = this.getPrefixText() + this.measure + '~' + this.timeUnit;
            this.eventEmitter('selected', { data: data, isTimeRangeFilter: true });
        },

        getPrefixText() {
          let prefixtext = this.identifier === 'due-on' ? 'next::' : 'last::';
            if (this.isLastNextSelector) {
              prefixtext = this.nextOrLast + '::';
            }
            return prefixtext;
        },

        setInitialValue() {
          let data = {};
          data[this.identifier] = this.value;
          this.eventEmitter('selected', { data: data, isTimeRangeFilter: true });
          const splitByDColon = this.value.split('::');
          if (splitByDColon[0] === 'date') {
            this.isDatePicker = true;
            this.timeRange = splitByDColon[1].split('~').map(date => moment(date).format(this.dateTimeFormat));
          } else {
            this.isDatePicker = false;
            const splitByTilda = splitByDColon[1].split('~');
            this.measure = splitByTilda[0];
            this.timeUnit = splitByTilda[1];
          }
        },
        onClearField() {
          if (this.isDatePicker) {
            this.timeRange = null;
          } else {
            this.measure = null;
            this.timeUnit = null;
          }
          this.emitEmptyValue();
        },

        emitEmptyValue() {
          let data = {};
          data[this.identifier] = undefined;
          this.eventEmitter('selected', { data: data, isTimeRangeFilter: true });
        },

        allowPositiveNumberOnly(event) {
        const keyVal = event.which || event.keyCode;
          if ((keyVal > 31 && (keyVal < 48 || keyVal > 57))) {
            event.preventDefault();
          } else {
            return true;
          }
        }
    },
    computed: {
      fieldCanBeCleared: function() {
        return ( this.isDatePicker && this.timeRange );
      }
    },
    watch: {
        reset(value) {
            if (value) {
                this.measure = null;
                this.isDatePicker = false;
                this.timeUnit = null;
                this.timeRange = null;
            }
        }
    }
}
</script>

<style scoped lang="css">
.range_btn { border-top-left-radius : 0;border-bottom-left-radius : 0; height : 36px !important;}
.main-container {
  padding-bottom: 1rem;
}
.time-range-filter {
  display: flex;
  max-width: 100%;
  margin-bottom: 5px;
  font-weight: 700;
}
.input-group {
  display: flex;
}
.last-next-width {
  width: 8rem;
}
.measure-input {
  width: 5rem;
}
.toggle-btn {
  width: 4rem;
}
.clear-btn {
  cursor: pointer;
}
.clear-btn:hover {
  padding: .1rem;
  border-radius: 50%;
  background: #c2c2c2 radial-gradient(circle, transparent 1%, #47a7f5 1%) center/15000%;
}
</style>

<style lang="css">
.mx-shortcuts-wrapper {
  display: none !important;
}
.mx-input-append {
  display: none !important;
}
.mx-datepicker-range {
  width: 279px !important;
}
.hide-number-spinner::-webkit-outer-spin-button,
.hide-number-spinner::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.hide-number-spinner {
    -moz-appearance:textfield;
}
</style>