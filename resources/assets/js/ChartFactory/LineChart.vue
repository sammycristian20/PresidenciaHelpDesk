<script>
  //Importing Line class from the vue-chartjs wrapper
  import { Line, mixins } from 'vue-chartjs'

  const { reactiveProp } = mixins;

  import { redirectToURL } from './utils';

  import Chart from 'chart.js';

  //Exporting this so it can be used in other components
  export default {

    extends: Line,

    mixins: [ reactiveProp ],

    props: {
      /**
       * Redirect URLs for the chart click handler
       */
      redirectUrls: {
        type: Array,
        default: () => []
      },

      chartOptions: {
        type: Object,
        default: null
      },

      // This is will be axis(x or y) label
      labelString: {
        type: String,
        default: () => ''
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
        datacollection: {

          //Data to be represented on x-axis
          labels: this.chartData.labels,

          // Array of chart data
          datasets: this.chartData.data
        },

        //Chart.js options that controls the appearance of the chart
        options: {
          title: this.chartTitle,
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                precision: 0,
              },
              gridLines: {
                display: true
              },
              scaleLabel: {
                display: true,
                labelString: this.chartData.dataLabel
              }
            }],
            xAxes: [{
              gridLines: {
                display: false
              },
              scaleLabel: {
                display: true,
                labelString: this.chartData.categoryLabel
              }
            }]
          },
          legend: {
            display: true,
          },
          hover: {
            onHover: this.onHover
          },
          responsive: true,
          maintainAspectRatio: false,
          onClick: (point, event) => {
            this.clickHandler(point, event);
          }
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

    mounted () {
      //renderChart function renders the chart with the datacollection and options object.
      setTimeout(() => {
        Chart.defaults.global.plugins.datalabels.display = false;
        this.renderChart(this.datacollection, this.options);
      }, 1000);
    },

    methods: {
      clickHandler(event, items) {
        let datasetIndex = this.getDatasetIndex(event);
        if(typeof datasetIndex !== 'undefined') {
          redirectToURL(items, this.chartData.redirectURLs[datasetIndex]);
        }
      },
      onHover(event, items) {
        try {
          const el = document.getElementById(this.chartId);
          let datasetIndex = this.getDatasetIndex(event);
          el.style.cursor = 'default';
          if(typeof datasetIndex !== 'undefined' && this.chartData.redirectURLs[datasetIndex]) {
            el.style.cursor = 'pointer';
          }
        } catch (error) {
          // Do nothing
        }
      },

      getDatasetIndex(event) {
        let activePoints = this.$data._chart.getElementsAtEvent(event);
        if(activePoints.length > 0) {
          return this.$data._chart.getDatasetAtEvent(event)[0]._datasetIndex;
        }
      }
    }
  }
</script>