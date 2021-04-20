<script>
  /**
   * Importing HorizontalBar and mixins class from the vue-chartjs wrapper
   */
  import { HorizontalBar, mixins } from 'vue-chartjs';
  
  /**
   * Getting the reactiveProp mixin from the mixins module.
   * The reactiveProp mixin extends the logic of your chart component,
   * Automatically creates a prop as named chartData, and adds a Vue watcher to this prop. 
   * All the data needed will be inside the chartData prop.
   * 
   * USE:- <horizontal-bar-chart :chart-data="datacollection"></horizontal-bar-chart>
   */
  const { reactiveProp } = mixins;

  import { redirectToURL, truncateString, hoverHandler } from './utils';

  //Exporting this so it can be used in other components
  export default {

    extends: HorizontalBar,

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
        // Chart.js options that controls the appearance of the chart
        options: {
          title: this.chartTitle,
          plugins: {
            datalabels: {
              color: '#000',
              anchor: 'end',
              clamp: true,
              align: 'end'
            }
          },
          layout: {
            padding: {
              left: 0,
              right: 30,
              top: 0,
              bottom: 0
            }
          },
          scales: {
            yAxes: [{
              barThickness: 25,
              gridLines: {
                display: false
              },
              ticks: {
                callback: (value, index, values) => {
                  return truncateString(value);
                }
              }
            }],
            xAxes: [{
              scaleLabel: {
                display: true,
                labelString: this.labelString
              },
              ticks: {
                beginAtZero: true,
                precision: 0,
              },
              gridLines: {
                display: false
              }
            }],
          },
          legend: {
            display: false
          },
          responsive: true,
          maintainAspectRatio: false,
          onClick: this.clickHandler,
          tooltips: {
            callbacks: {
              title: function(tooltipItems, data) {
                return data.labels[tooltipItems[0].index]
              }
            }
          },
          hover: {
            onHover: this.onHover
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
      onHover(event, items) {
        hoverHandler(items, this.chartId, this.redirectUrls);
      }
    }
  }
</script>