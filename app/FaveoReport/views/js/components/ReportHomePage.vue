<template>
  <div>
    <!-- Alert -->
    <alert componentName="report-home-page" />

    <custom-loader v-if="isLoading" :duration="4000"></custom-loader>

    <div v-if="reportList.length > 0">
      <div class="card card-light " v-for="reportCategory in reportList" :key="reportCategory.id">
        <div class="card-header">
          <h3 class="card-title">{{reportCategory.category}}</h3>
        </div>
        <div class="card-body">

          <div class="table-responsive">
            <table class="table">
              <tbody>
                <tr class="Default" v-for="report in reportCategory.reports" :key="report.id">
                  <td>
                    <span class="fa-stack fa-2x">
                      <i :class="getIconClass(report.icon_class)"></i>
                    </span>
                  </td>
                  <td>
                    <dl>
                      <dt class="text-uppercase">
                        <a :href="basePath() + '/' + report.view_url" class="fw-500">
                        {{report.name}}</a> 
                      </dt>
                      <dd class="text-overflow">{{report.description}}
                        <br>
                        <small class="float-right report-modify">
                          {{lang('last_modified_on')}}: <strong>{{formattedTime(report.updated_at)}}</strong>
                        </small>
                      </dd>
                    </dl>
                  </td>
                  <td>
                    <div class="badge badge-info" v-if="report.is_default">{{lang('default')}}</div>
                    <button v-else class="btn btn-danger ml-6" v-tooltip="lang('delete')" @click="deleteCustomReport(report.id)">
                      <i class="fas fa-trash" aria-hidden="true"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

</template>

<script>

import axios from 'axios';
import { errorHandler, successHandler } from 'helpers/responseHandler';
import {mapGetters} from "vuex"

export default {

  name: 'report-home-page',

  components: {
    'custom-loader': require('components/MiniComponent/Loader'),
    'alert': require("components/MiniComponent/Alert"),
  },

  data: () => {
    return {
      reportList: [],
      isLoading: false,
    }
  },

  computed: {
    ...mapGetters(["formattedTime"])
  },

  beforeMount() {
    this.getReportList();
  },

  methods: {

    getIconClass(value) {

      return value == 'fa fa-support fa-stack-1x' ? 'fas fa-life-ring fa-stack-1x' : value == 'fa fa-bank fa-stack-1x' ? 'fas fa-university  fa-stack-1x' : value
    },

    getReportList() {
      this.isLoading = true;
      axios.get('api/agent/report-list')
      .then(res => {
        this.reportList = res.data.data;
      }).catch(err => {
        errorHandler(err, 'report-home-page');
      }).finally(res => {
        this.isLoading = false;
      });
    },

    deleteCustomReport(reportId) {
      const isConfirmed = confirm('Are you sure you want to delete this report?')
      if(isConfirmed) {
        this.isLoading = true;
        axios.delete('api/report/' + reportId)
        .then(res => {
          this.getReportList();
          successHandler(res, 'report-home-page');
        }).catch(err => {
          errorHandler(err, 'report-home-page');
          this.isLoading = false;
        })
      }
    },

  }
};
</script>

<style lang="css" scoped>
  .report-modify {
    position: relative;
    left: 68px;
  }
  .ml-6 { margin-left: 6px; }

  .fw-500 { font-weight: 500 !important; }
</style>