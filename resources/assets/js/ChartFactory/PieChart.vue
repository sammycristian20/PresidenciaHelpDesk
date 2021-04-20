<script>
  /**
   * Importing Pie and mixins class from the vue-chartjs wrapper
   */
  import { Pie, mixins } from 'vue-chartjs';
  
  /**
   * Getting the reactiveProp mixin from the mixins module.
   * The reactiveProp mixin extends the logic of your chart component,
   * Automatically creates a prop as named chartData, and adds a Vue watcher to this prop. 
   * All the data needed will be inside the chartData prop.
   * 
   * USE:- <pie-chart :chart-data="datacollection"></pie-chart>
   */
  const { reactiveProp } = mixins;

  import { redirectToURL, getDatalabelInPercentage, hoverHandler } from './utils';

  //Exporting this so it can be used in other components
  export default {

    extends: Pie,

    mixins: [ reactiveProp ],

    props: {
      /**
       * Redirect URLs for the chart click handler
       */
      redirectUrls: {
        type: Array,
        default: () => []
      },

      // Chart.js options that controls the appearance of the chart
      chartOptions: {
        type: Object,
        default: null
      },

      /** This prop is used for adding title to a chart
       * This title will be visible in canvas image
       */
      chartTitle: {
        type: Object,
        default: function () {
          return { display: false }
        }
      },

      /**
       * Canvas Id 
       */
      chartId: {
        type: String,
        required: true
      },
    },

    data () {
      return {
        // Chart.js options that controls the appearance of the chart
        options: {
          title: this.chartTitle,
          plugins: {
            datalabels: {
              display: 'auto',
              color: '#fff',
              anchor: 'end',
              clamp: true,
              align: 'start',
              offset: 10,
              formatter: this.dataLabelFormatter
            }
          },
          legend: {
            display: true,
            position: 'top'
          },
          hover: {
            onHover: this.onHover
          },
          responsive: true,
          maintainAspectRatio: false,
          onClick: this.clickHandler,
        }
      }
    },
    beforeMount() {
      /**
       * If prop `chartOptions` is not passed, use default chart option
       */
      if (this.chartOptions !== null) {
        this.options = this.chartOptions;
      }
    },
    mounted() {
      //renderChart function renders the chart with the chartData and options object.
      setTimeout(() => {
        Chart.defaults.global.plugins.datalabels.display = true;
        this.renderChart(this.chartData, this.options);
      }, 1000);
    },

    methods: {
      /**
       * Handle click event on chart
       * open embeded link in new tab
       */
      clickHandler (point, event) {
        redirectToURL(event, this.redirectUrls);
      },

      dataLabelFormatter(value, context) {
        return getDatalabelInPercentage(value, context);
      },

      onHover(event, items) {
        hoverHandler(items, this.chartId, this.redirectUrls);
      }
    }
  }
</script>