webpackJsonp([4],{

/***/ 230:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2698)
/* template */
var __vue_template__ = __webpack_require__(2718)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/Agent/BillingPackages.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-9e88a1d6", Component.options)
  } else {
    hotAPI.reload("data-v-9e88a1d6", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 231:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2708)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2710)
/* template */
var __vue_template__ = __webpack_require__(2711)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-27a5dbbd"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/Agent/Tables/MiniComponents/OrdersModal.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-27a5dbbd", Component.options)
  } else {
    hotAPI.reload("data-v-27a5dbbd", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2696:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2697);


/***/ }),

/***/ 2697:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_store__ = __webpack_require__(10);
var bootstrap = __webpack_require__(29);





bootstrap.injectComponentIntoView('billing-packages', __webpack_require__(230), 'user-page-mounted', 'user-page-table');

Vue.component('package-index', __webpack_require__(2719));

Vue.component('package', __webpack_require__(2722));

Vue.component('payment', __webpack_require__(2731));

Vue.component('payment-index', __webpack_require__(2736));

Vue.component('billing-packages', __webpack_require__(230));

Vue.component('package-invoice', __webpack_require__(2745));

Vue.component('order-details', __webpack_require__(2762));

Vue.component('invoices', __webpack_require__(2767));

__WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */].dispatch('deleteUser');
__WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */].dispatch('updateUser');

var app = new Vue({
    el: '#app-billing',
    store: __WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */]
});

/***/ }),

/***/ 2698:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//








/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'billing-packages',

	description: 'Billing packages datatable',

	props: {

		id: { type: String | Number, default: '' }
	},

	data: function data() {

		return {

			category: 'orders',

			tabs: [],

			user_id: '',

			loading: true,

			apiUrl: '',

			showModal: false
		};
	},


	watch: {
		category: function category(newValue, oldValue) {
			return newValue;
		}
	},

	computed: {
		currentTableComponent: function currentTableComponent() {
			return this.category === 'orders' ? 'orders-table' : 'invoices-table';
		}
	},

	beforeMount: function beforeMount() {

		var path = window.location.pathname;

		this.user_id = Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["m" /* getIdFromUrl */])(path);

		this.getCount();

		this.getTableData(this.category);
	},


	methods: {
		getTableData: function getTableData(category) {

			this.apiUrl = category === 'orders' ? '/bill/package/get-user-packages?user_id=' + this.user_id : '/bill/package/get-user-invoice?users[0]=' + this.user_id;
		},
		packages: function packages(category) {
			this.category = category;
			this.getCount();
			this.getTableData(category);
		},
		getCount: function getCount() {
			var _this = this;

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('/bill/package/get-all-count/' + this.user_id).then(function (res) {
				_this.tabs = [{ category: 'orders', title: 'orders', b_class: 'badge bg-orange', count: res.data.data.userpackage }, { category: 'invoice', title: 'invoice', b_class: 'badge bg-red', count: res.data.data.invoice }];
			}).catch(function (err) {
				_this.tabs = [];
			});
		},
		onClose: function onClose() {
			this.showModal = false;
			this.packages('invoice');
			this.$store.dispatch('unsetAlert');
		},
		addInvoice: function addInvoice() {
			this.showModal = true;
		}
	},

	components: {

		'billing-packages-table': __webpack_require__(2699),

		'orders-table': __webpack_require__(2704),

		'invoices-table': __webpack_require__(2713),

		'order-modal': __webpack_require__(231)
	}
});

/***/ }),

/***/ 2699:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2700)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2702)
/* template */
var __vue_template__ = __webpack_require__(2703)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/Agent/BillingPackagesTable.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1d7e7e2e", Component.options)
  } else {
    hotAPI.reload("data-v-1d7e7e2e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2700:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2701);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("548a6d63", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1d7e7e2e\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./BillingPackagesTable.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1d7e7e2e\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./BillingPackagesTable.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2701:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.ticket-updated{\n  width:20% !important;\n  word-break: break-all;\n}\n.ticket-number{\n  width:20% !important;\n  word-break: break-all;\n}\n.ticket-title{\n  width:30% !important;\n  word-break: break-all;\n}\n.ticket-user{\n  width:15% !important;\n  word-break: break-all;\n}\n.ticket-assigned{\n  width:15% !important;\n  word-break: break-all;\n}  \n", ""]);

// exports


/***/ }),

/***/ 2702:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vuex__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

//
//
//
//
//
//






/* harmony default export */ __webpack_exports__["default"] = ({

  name: 'billing-packages-table',

  description: 'Billing packages table page',

  props: {

    category: { type: String, default: '' },

    columnArray: { type: Array | String, default: '' },

    apiEndpoint: { type: String, default: '' },

    optionsObj: { type: Object | String, default: '' }
  },

  data: function data() {
    return {

      base: window.axios.defaults.baseURL,

      columns: this.columnArray,

      options: {},

      apiUrl: this.apiEndpoint
    };
  },


  watch: {
    optionsObj: function optionsObj(newValue, oldValue) {
      this.updateTable();
      return newValue;
    }
  },

  beforeMount: function beforeMount() {

    this.updateTable();
  },


  computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_0_vuex__["b" /* mapGetters */])(['formattedTime', 'formattedDate'])),

  methods: {
    updateTable: function updateTable() {

      var self = this;

      this.options = {

        sortIcon: {
          base: 'glyphicon',
          up: 'glyphicon-chevron-down',
          down: 'glyphicon-chevron-up'
        },

        headings: self.optionsObj.headings,

        texts: {

          filter: '',

          limit: ''
        },

        templates: self.optionsObj.templates,

        sortable: self.optionsObj.sortable,

        filterable: self.optionsObj.filterable,

        pagination: { chunk: 5, nav: 'scroll' },

        requestAdapter: function requestAdapter(data) {
          return {

            'sort-field': data.orderBy ? data.orderBy : 'id',

            'sort-order': data.ascending ? 'desc' : 'asc',

            'search-query': data.query.trim(),

            'page': data.page,

            'limit': data.limit
          };
        },
        responseAdapter: function responseAdapter(_ref) {
          var data = _ref.data;


          return {

            data: data.data.data,

            count: data.data.total
          };
        }
      };
    }
  },

  components: {
    'data-table': __webpack_require__(17)
  }
});

/***/ }),

/***/ 2703:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("data-table", {
    attrs: {
      url: _vm.apiEndpoint,
      dataColumns: _vm.columnArray,
      option: _vm.options,
      scroll_to: "packages_title"
    }
  })
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-1d7e7e2e", module.exports)
  }
}

/***/ }),

/***/ 2704:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2705)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2707)
/* template */
var __vue_template__ = __webpack_require__(2712)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/Agent/Tables/Orders.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3549d23c", Component.options)
  } else {
    hotAPI.reload("data-v-3549d23c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2705:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2706);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("4bdfcae2", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../../node_modules/css-loader/index.js!../../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3549d23c\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Orders.vue", function() {
     var newContent = require("!!../../../../../../../../node_modules/css-loader/index.js!../../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3549d23c\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Orders.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2706:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.order-type{\n  /*width:15% !important;*/\n  word-break: break-all;\n}\n.order-credit{\n  /*width:10% !important;*/\n  word-break: break-all;\n}\n.order-amount{\n  /*width:15% !important;*/\n  word-break: break-all;\n}\n.order-name{\n  /*width:20% !important;*/\n  word-break: break-all;\n}\n.order-date{\n   /*width:15% !important;*/\n  word-break: break-all;\n}\n.order-status{\n   /*width:10% !important;*/\n  word-break: break-all;\n}\n.order-pack .VueTables .table-responsive {\n  overflow-x: scroll;\n}\n.order-pack .VueTables .table-responsive > table{\n  width : -webkit-max-content;\n  width : -moz-max-content;\n  width : max-content;\n  min-width : 100%;\n  max-width : -webkit-max-content;\n  max-width : -moz-max-content;\n  max-width : max-content;\n}\n#delete_btn{\n  margin-right: 10px !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2707:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vuex__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//






/* harmony default export */ __webpack_exports__["default"] = ({

  name: 'orders-table',

  description: 'Orders table page',

  props: {

    category: { type: String, default: '' },

    apiEndpoint: { type: String, default: '' }
  },

  data: function data() {
    return {

      base: window.axios.defaults.baseURL,

      options: {},

      apiUrl: this.apiEndpoint,

      columns: ['name', 'invoice', 'credit_type', 'credit', 'total_amount', 'expiry_date'],

      orderIds: [],

      order_id: 0,

      showModal: false
    };
  },
  beforeMount: function beforeMount() {
    var _options;

    var self = this;

    this.options = (_options = {

      sortIcon: {

        base: 'glyphicon',

        up: 'glyphicon-chevron-down',

        down: 'glyphicon-chevron-up'
      },

      headings: {

        due_by: 'Due date'
      },

      columnsClasses: {

        invoice: 'order-invoice',

        credit_type: 'order-type',

        credit: 'order-credit',

        total_amount: 'order-amount',

        name: 'order-name',

        expiry_date: 'order-date'

      },

      texts: {

        filter: '',

        limit: ''
      },

      templates: {

        invoice: function invoice(createElement, row) {

          return createElement('a', {
            attrs: {

              href: self.base + '/bill/package/' + row.id + '/user-invoice',
              target: '_blank'
            }
          }, 'Invoice#' + row.id);
        },

        credit_type: function credit_type(h, row) {

          return row.credit_type;
        },
        credit: function credit(h, row) {

          return row.credit;
        },
        name: function name(createElement, row) {

          return createElement('a', {
            attrs: {

              href: self.base + '/bill/order/' + row.id,

              target: '_blank'
            }
          }, row.package.name);
        },
        expiry_date: function expiry_date(createElement, row) {

          var currentDate = new Date();

          var expiry = self.formattedTime(row.expiry_date);

          var date = void 0;

          if (currentDate > expiry) {

            date = createElement('span', {
              attrs: {
                class: 'text-red'
              }
            }, self.formattedTime(row.expiry_date));
          } else {

            date = createElement('span', {
              attrs: {
                class: 'text-green'
              }
            }, self.formattedTime(row.expiry_date));
          }

          return date;
        },
        total_amount: function total_amount(h, row) {

          return row.invoice.total_amount;
        }
      },

      sortable: ['credit_type', 'credit', 'total_amount', 'expiry_date', 'status'],

      filterable: ['credit_type', 'credit', 'total_amount', 'expiry_date', 'status'],

      pagination: { chunk: 5, nav: 'scroll' }

    }, _defineProperty(_options, 'headings', {
      'name': Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["q" /* lang */])('package_name')
    }), _defineProperty(_options, 'requestAdapter', function requestAdapter(data) {
      return {

        'sort-field': data.orderBy ? data.orderBy : 'id',

        'sort-order': data.ascending ? 'desc' : 'asc',

        'search-query': data.query.trim(),

        'page': data.page,

        'limit': data.limit
      };
    }), _defineProperty(_options, 'responseAdapter', function responseAdapter(_ref) {
      var data = _ref.data;


      return {

        data: data.data.data,

        count: data.data.total
      };
    }), _options);
  },


  computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_0_vuex__["b" /* mapGetters */])(['formattedTime', 'formattedDate'])),

  methods: {
    orderData: function orderData(data) {

      this.orderIds = data;
    },
    onClick: function onClick(row) {

      this.order_id = row.id;

      this.showModal = true;
    },
    onClose: function onClose() {

      this.showModal = false;

      this.$store.dispatch('unsetValidationError');
    },
    deleteInvoices: function deleteInvoices() {

      prompt('Are you sure to delete this', 'yes ');
    }
  },

  components: {
    'data-table': __webpack_require__(17),

    'orders-modal': __webpack_require__(231)
  }
});

/***/ }),

/***/ 2708:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2709);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("1f5976ff", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../../../node_modules/css-loader/index.js!../../../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-27a5dbbd\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./OrdersModal.vue", function() {
     var newContent = require("!!../../../../../../../../../node_modules/css-loader/index.js!../../../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-27a5dbbd\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./OrdersModal.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2709:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.label_align[data-v-27a5dbbd] {\n\tdisplay: block; padding-left: 15px; text-indent: -15px; font-weight: normal !important; padding-top: 6px;\n}\n.checkbox_align[data-v-27a5dbbd] {\n\twidth: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;\n}\n", ""]);

// exports


/***/ }),

/***/ 2710:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_axios__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//






/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'settings-modal',

	description: 'Settings Modal component',

	props: {

		showModal: { type: Boolean, default: false },

		onClose: { type: Function },

		title: { type: String, default: '' },

		id: { type: String | Number, default: '' },

		userId: { type: String, default: 0 }
	},

	data: function data() {
		return {

			containerStyle: { width: '800px' },

			loading: false,

			size: 60,

			status: 0,

			packages: [],

			packageId: 0,

			submitDisabled: true,

			checked: false
		};
	},

	beforeMount: function beforeMount() {

		this.getData();
	},


	methods: {
		getData: function getData() {
			var _this = this;

			this.loading = true;

			__WEBPACK_IMPORTED_MODULE_1_axios___default.a.get('bill/package/get-active-packages').then(function (res) {
				_this.loading = false;
				_this.packages = res.data.data.data;
			}).catch(function (error) {
				_this.submitDisabled = true;
				_this.loading = false;
				Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["a" /* errorHandler */])(error, 'add-invoice');
			});
		},
		createInvoice: function createInvoice() {
			var _this2 = this;

			var meta = this.checked ? 1 : 0;
			this.loading = true;
			__WEBPACK_IMPORTED_MODULE_1_axios___default.a.get('bill/package/user-checkout?package_id=' + this.packageId + "&user_id=" + this.userId + "&meta=" + meta).then(function (res) {
				_this2.loading = false;

				Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["b" /* successHandler */])(res, 'add-invoice');

				window.eventHub.$emit('refreshData');

				setTimeout(function () {

					_this2.onClose();
				}, 1500);
			}).catch(function (error) {
				console.log(error);
				_this2.submitDisabled = true;
				Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["a" /* errorHandler */])(error, 'add-invoice');
				_this2.loading = false;
			});
		},
		usePackage: function usePackage(value, name) {
			this.submitDisabled = value != '' ? false : true;
			this.packageId = value;
		}
	},

	components: {

		'modal': __webpack_require__(13),
		"static-select": __webpack_require__(26),
		'alert': __webpack_require__(6),
		'loader': __webpack_require__(8)
	}
});

/***/ }),

/***/ 2711:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.showModal
    ? _c(
        "modal",
        {
          attrs: {
            showModal: _vm.showModal,
            onClose: _vm.onClose,
            containerStyle: _vm.containerStyle
          },
          on: {
            close: function($event) {
              _vm.showModal = false
            }
          }
        },
        [
          _c("div", { attrs: { slot: "title" }, slot: "title" }, [
            _c("h4", { staticClass: "modal-title" }, [
              _vm._v(_vm._s(_vm.lang(_vm.title)) + _vm._s(_vm.id))
            ])
          ]),
          _vm._v(" "),
          !_vm.loading
            ? _c("div", { attrs: { slot: "fields" }, slot: "fields" }, [
                _c(
                  "div",
                  { attrs: { slot: "alert" }, slot: "alert" },
                  [_c("alert", { attrs: { componentName: "add-invoice" } })],
                  1
                ),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "row" },
                  [
                    _c("static-select", {
                      attrs: {
                        label: _vm.lang("select_package"),
                        elements: _vm.packages,
                        name: "package",
                        value: _vm.id,
                        classname: "col-sm-12",
                        onChange: _vm.usePackage,
                        required: true
                      }
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c("div", { staticClass: "row" }, [
                  _c(
                    "div",
                    {
                      staticClass: "form-group col-md-12",
                      attrs: { id: "align" }
                    },
                    [
                      _c("label", { staticClass: "label_align" }, [
                        _c("input", {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.checked,
                              expression: "checked"
                            }
                          ],
                          staticClass: "checkbox_align",
                          attrs: { type: "checkbox", name: "meta" },
                          domProps: {
                            checked: Array.isArray(_vm.checked)
                              ? _vm._i(_vm.checked, null) > -1
                              : _vm.checked
                          },
                          on: {
                            change: function($event) {
                              var $$a = _vm.checked,
                                $$el = $event.target,
                                $$c = $$el.checked ? true : false
                              if (Array.isArray($$a)) {
                                var $$v = null,
                                  $$i = _vm._i($$a, $$v)
                                if ($$el.checked) {
                                  $$i < 0 && (_vm.checked = $$a.concat([$$v]))
                                } else {
                                  $$i > -1 &&
                                    (_vm.checked = $$a
                                      .slice(0, $$i)
                                      .concat($$a.slice($$i + 1)))
                                }
                              } else {
                                _vm.checked = $$c
                              }
                            }
                          }
                        }),
                        _vm._v(
                          "\n\t\t\t\t\t\t\t " +
                            _vm._s(_vm.lang("send_invoice_to_client")) +
                            "\n\t\t\t\t\t"
                        )
                      ])
                    ]
                  )
                ])
              ])
            : _vm._e(),
          _vm._v(" "),
          _c("div", { attrs: { slot: "controls" }, slot: "controls" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-primary",
                attrs: { type: "button", disabled: _vm.submitDisabled },
                on: {
                  click: function($event) {
                    _vm.createInvoice()
                  }
                }
              },
              [
                _c("i", { staticClass: "fas fa-save" }),
                _vm._v(" " + _vm._s(_vm.lang("create")) + "\n         ")
              ]
            )
          ]),
          _vm._v(" "),
          _vm.loading
            ? _c(
                "div",
                {
                  staticClass: "row",
                  attrs: { slot: "fields" },
                  slot: "fields"
                },
                [
                  _c("loader", {
                    attrs: {
                      "animation-duration": 4000,
                      color: "#1d78ff",
                      size: _vm.size
                    }
                  })
                ],
                1
              )
            : _vm._e()
        ]
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-27a5dbbd", module.exports)
  }
}

/***/ }),

/***/ 2712:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "order-pack" },
    [
      _c("data-table", {
        attrs: {
          url: _vm.apiEndpoint,
          dataColumns: _vm.columns,
          option: _vm.options,
          scroll_to: "packages_title",
          tickets: _vm.orderData
        }
      }),
      _vm._v(" "),
      _c(
        "transition",
        { attrs: { name: "modal" } },
        [
          _vm.showModal
            ? _c("orders-modal", {
                attrs: {
                  title: "order_details",
                  onClose: _vm.onClose,
                  showModal: _vm.showModal,
                  id: _vm.order_id
                }
              })
            : _vm._e()
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-3549d23c", module.exports)
  }
}

/***/ }),

/***/ 2713:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2714)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2716)
/* template */
var __vue_template__ = __webpack_require__(2717)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/Agent/Tables/Invoices.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2f0c5406", Component.options)
  } else {
    hotAPI.reload("data-v-2f0c5406", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2714:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2715);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("2b4a98ca", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../../node_modules/css-loader/index.js!../../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2f0c5406\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Invoices.vue", function() {
     var newContent = require("!!../../../../../../../../node_modules/css-loader/index.js!../../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2f0c5406\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Invoices.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2715:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.invoice-name,.invoice-create,.invoice-due,.invoice-amount,.invoice-status{ max-width: 250px; word-break: break-all;\n}\n.invoice-pack .VueTables .table-responsive {\n  overflow-x: auto;\n}\n.invoice-pack .VueTables .table-responsive > table{\n  width : -webkit-max-content;\n  width : -moz-max-content;\n  width : max-content;\n  min-width : 100%;\n  max-width : -webkit-max-content;\n  max-width : -moz-max-content;\n  max-width : max-content;\n  overflow: auto !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2716:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vuex__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//






/* harmony default export */ __webpack_exports__["default"] = ({

  name: 'invoices-table',

  description: 'Invoices table page',

  props: {

    category: { type: String, default: '' },

    apiEndpoint: { type: String, default: '' }
  },

  data: function data() {
    return {

      base: window.axios.defaults.baseURL,

      options: {},

      apiUrl: this.apiEndpoint,

      columns: ['name', 'created_at', 'due_by', 'total_amount', 'payment_mode', 'status'],

      orderIds: []
    };
  },
  beforeMount: function beforeMount() {

    var self = this;

    this.options = {

      sortIcon: {

        base: 'glyphicon',

        up: 'glyphicon-chevron-down',

        down: 'glyphicon-chevron-up'
      },

      headings: {
        payment_mode: Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["q" /* lang */])('payment_mode'),
        due_by: Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["q" /* lang */])('due_by')
      },

      columnsClasses: {

        name: 'invoice-name',

        created_at: 'order-create',

        due_by: 'order-due',

        total_amount: 'order-amount',

        status: 'order-status'
      },

      texts: {

        filter: '',

        limit: ''
      },

      templates: {

        status: function status(createElement, row) {

          var span = createElement('span', {
            attrs: {
              'class': row.order.status === 1 ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
            }
          }, row.order.status === 1 ? 'Paid' : 'Unpaid');

          return createElement('a', {}, [span]);
        },

        created_at: function created_at(h, row) {

          return self.formattedTime(row.created_at);
        },
        due_by: function due_by(h, row) {

          return self.formattedTime(row.due_by);
        },
        payment_mode: function payment_mode(h, row) {
          return Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["q" /* lang */])(row.payment_mode);
        },


        name: function name(createElement, row) {

          return createElement('a', {
            attrs: {

              href: self.base + '/bill/package/' + row.id + '/user-invoice',
              target: '_blank'
            }
          }, 'Invoice#' + row.name);
        }
      },

      sortable: ['name', 'created_at', 'due_by', 'total_amount', 'payment_mode', 'status'],

      filterable: ['name', 'created_at', 'due_by', 'total_amount', 'payment_mode'],

      pagination: { chunk: 5, nav: 'scroll' },

      requestAdapter: function requestAdapter(data) {
        return {

          'sort-field': data.orderBy ? data.orderBy : 'id',

          'sort-order': data.ascending ? 'desc' : 'asc',

          'search-query': data.query.trim(),

          'page': data.page,

          'limit': data.limit
        };
      },
      responseAdapter: function responseAdapter(_ref) {
        var data = _ref.data;


        return {

          data: data.data.data,

          count: data.data.total
        };
      }
    };
  },


  computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_0_vuex__["b" /* mapGetters */])(['formattedTime', 'formattedDate'])),

  methods: {
    orderData: function orderData(data) {

      this.orderIds = data;
    },
    deleteOrders: function deleteOrders() {
      prompt('Are you sure to delte this', 'yes');
    }
  },

  components: {
    'data-table': __webpack_require__(17)
  }
});

/***/ }),

/***/ 2717:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "invoice-pack" },
    [
      _c("data-table", {
        attrs: {
          url: _vm.apiEndpoint,
          dataColumns: _vm.columns,
          option: _vm.options,
          scroll_to: "packages_title",
          tickets: _vm.orderData
        }
      })
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-2f0c5406", module.exports)
  }
}

/***/ }),

/***/ 2718:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "card card-light ", attrs: { id: "billing-packages" } },
    [
      _c("div", { staticClass: "card-header" }, [
        _c("h3", { staticClass: "card-title" }, [
          _vm._v(_vm._s(_vm.lang("packages")))
        ]),
        _vm._v(" "),
        _vm.category === "invoice"
          ? _c("div", { staticClass: "card-tools" }, [
              _c(
                "a",
                {
                  directives: [
                    {
                      name: "tooltip",
                      rawName: "v-tooltip",
                      value: _vm.lang("create_invoice"),
                      expression: "lang('create_invoice')"
                    }
                  ],
                  staticClass: "btn-tool",
                  attrs: { href: "javascript:;" },
                  on: {
                    click: function($event) {
                      _vm.addInvoice()
                    }
                  }
                },
                [_c("i", { staticClass: "fas fa-plus" })]
              )
            ])
          : _vm._e()
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "card-body" }, [
        _c(
          "ul",
          { staticClass: "nav nav-tabs", attrs: { role: "tablist" } },
          _vm._l(_vm.tabs, function(section) {
            return _c("li", { staticClass: "nav-item" }, [
              _c(
                "a",
                {
                  staticClass: "nav-link",
                  class: { active: _vm.category === section.category },
                  attrs: {
                    "data-toggle": "pill",
                    role: "tab",
                    href: "javascript:;"
                  },
                  on: {
                    click: function($event) {
                      _vm.packages(section.category)
                    }
                  }
                },
                [
                  _vm._v(
                    "\n\n\t\t\t\t\t" + _vm._s(_vm.lang(section.title)) + " "
                  ),
                  _c("span", { staticClass: "badge badge-primary" }, [
                    _vm._v(_vm._s(section.count))
                  ])
                ]
              )
            ])
          })
        ),
        _vm._v(" "),
        _c("div", { staticClass: "tab-content" }, [
          _c(
            "div",
            { staticClass: "active tab-pane" },
            [
              _c(_vm.currentTableComponent, {
                tag: "component",
                attrs: {
                  id: _vm.user_id,
                  category: _vm.category,
                  apiEndpoint: _vm.apiUrl
                }
              })
            ],
            1
          )
        ])
      ]),
      _vm._v(" "),
      _c(
        "transition",
        { attrs: { name: "modal" } },
        [
          _vm.showModal
            ? _c("order-modal", {
                attrs: {
                  showModal: _vm.showModal,
                  onClose: _vm.onClose,
                  userId: _vm.user_id,
                  title: _vm.lang("create_invoice")
                }
              })
            : _vm._e()
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-9e88a1d6", module.exports)
  }
}

/***/ }),

/***/ 2719:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2720)
/* template */
var __vue_template__ = __webpack_require__(2721)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/PackageIndex.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-240ca076", Component.options)
  } else {
    hotAPI.reload("data-v-240ca076", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2720:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//






/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'pacakges',

	description: 'Pacakges data table component',

	props: {},

	data: function data() {

		return {

			base: window.axios.defaults.baseURL,

			columns: ['id', 'name', 'validity', 'allowed_tickets', 'price', 'status', 'action'],

			options: {},

			apiUrl: '/bill/package/get-inbox-data',

			selectedData: [],

			showModal: false,

			deleteUrl: ''
		};
	},


	computed: {},

	watch: {},

	beforeMount: function beforeMount() {

		var self = this;

		this.options = {

			headings: {

				name: 'Name',

				validity: 'Validity',

				allowed_tickets: 'Incident credit',

				price: 'Price',

				status: 'Status',

				action: 'Action'
			},

			texts: {

				filter: '',

				limit: ''
			},

			sortIcon: {

				base: 'glyphicon',

				up: 'glyphicon-chevron-down',

				down: 'glyphicon-chevron-up'
			},

			templates: {

				status: 'data-table-status',

				action: 'data-table-actions',

				validity: function validity(h, row) {

					return row.validity === null ? Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["q" /* lang */])('one_time') : Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["q" /* lang */])(row.validity);
				}
			},

			sortable: ['name', 'validity', 'allowed_tickets', 'price', 'status'],

			filterable: ['name', 'validity', 'allowed_tickets', 'price', 'status'],

			pagination: { chunk: 5, nav: 'fixed', edge: true },

			requestAdapter: function requestAdapter(data) {

				return {

					'sort-field': data.orderBy ? data.orderBy : 'id',

					'sort-order': data.ascending ? 'desc' : 'asc',

					'search-query': data.query.trim(),

					'page': data.page,

					'limit': data.limit
				};
			},
			responseAdapter: function responseAdapter(_ref) {
				var data = _ref.data;


				return {

					data: data.message.data.map(function (data) {

						data.edit_url = window.axios.defaults.baseURL + '/bill/package/' + data.id + '/edit';

						return data;
					}),

					count: data.message.total
				};
			}
		};
	},


	methods: {
		packages: function packages(data) {

			this.selectedData = data;
		},
		deletePackage: function deletePackage() {

			this.deleteUrl = 'bill/package/delete?package_ids=' + this.selectedData;

			this.showModal = true;
		},
		onClose: function onClose() {

			this.showModal = false;

			this.$store.dispatch('unsetValidationError');
		}
	},

	components: {

		'data-table': __webpack_require__(17),

		"alert": __webpack_require__(6),

		'delete-modal': __webpack_require__(65)
	}
});

/***/ }),

/***/ 2721:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("alert", { attrs: { componentName: "dataTableModal" } }),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "card card-light" },
        [
          _c("div", { staticClass: "card-header" }, [
            _c(
              "h3",
              { staticClass: "card-title", attrs: { id: "pack-title" } },
              [_vm._v(_vm._s(_vm.lang("list_of_packages")))]
            ),
            _vm._v(" "),
            _c("div", { staticClass: "card-tools" }, [
              _c(
                "a",
                {
                  directives: [
                    {
                      name: "tooltip",
                      rawName: "v-tooltip",
                      value: _vm.lang("create-package"),
                      expression: "lang('create-package')"
                    }
                  ],
                  staticClass: "btn btn-tool",
                  attrs: { href: _vm.base + "/bill/package/create" }
                },
                [_c("span", { staticClass: "glyphicon glyphicon-plus" })]
              ),
              _vm._v(" "),
              _vm.selectedData.length > 0
                ? _c(
                    "a",
                    {
                      directives: [
                        {
                          name: "tooltip",
                          rawName: "v-tooltip",
                          value: _vm.lang("delete-package"),
                          expression: "lang('delete-package')"
                        }
                      ],
                      staticClass: "btn btn-tool",
                      on: {
                        click: function($event) {
                          _vm.deletePackage()
                        }
                      }
                    },
                    [_c("span", { staticClass: "fas fa-trash" })]
                  )
                : _vm._e()
            ])
          ]),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "card-body" },
            [
              _c("data-table", {
                attrs: {
                  url: _vm.apiUrl,
                  dataColumns: _vm.columns,
                  option: _vm.options,
                  scroll_to: "pack-title",
                  tickets: _vm.packages
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "transition",
            { attrs: { name: "modal" } },
            [
              _vm.showModal
                ? _c("delete-modal", {
                    attrs: {
                      onClose: _vm.onClose,
                      showModal: _vm.showModal,
                      deleteUrl: _vm.deleteUrl
                    }
                  })
                : _vm._e()
            ],
            1
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-240ca076", module.exports)
  }
}

/***/ }),

/***/ 2722:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2723)
/* template */
var __vue_template__ = __webpack_require__(2730)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/Package.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-6926843d", Component.options)
  } else {
    hotAPI.reload("data-v-6926843d", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2723:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_faveoBilling_helpers_validator_packageCreateRules__ = __webpack_require__(2724);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_vuex__ = __webpack_require__(7);
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//












/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'pacakges',

	description: 'Pacakges data table component',

	props: {},

	data: function data() {

		return {

			base: '',

			name: '',

			credit_type: 1,

			description: '',

			display_order: '',

			status: 0,

			// radioOptions : [{name:'incident_credit',value:1},{name:'time_credit',value:0}],

			radioOptions: [{ name: 'incident_credit', value: 1 }],

			cycle_options: [{ id: "one_time", name: "One time" }, { id: "monthly", name: "Monthly" }, { id: "quarterly", name: "Quarterly" }, { id: "semi_annually", name: "Semi annually" }, { id: "annually", name: "Annually" }],

			validity: 'one_time',

			title: 'create_new_package',

			iconClass: 'fas fa-save',

			btnName: 'save',

			loading: false, //loader status

			loadingSpeed: 4000,

			hasDataPopulated: true,

			package_id: '',

			formStyle: { width: '15%' },

			price: '',

			allowed_tickets: '',

			package_pic: '',

			kb_link: '',

			departments: []
		};
	},


	computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_4_vuex__["b" /* mapGetters */])(['getUserData'])),

	watch: {
		getUserData: function getUserData(newValue, oldValue) {

			this.base = newValue.system_url;

			return newValue;
		}
	},

	beforeMount: function beforeMount() {

		var path = window.location.pathname;

		this.getValues(path);
	},
	created: function created() {

		if (this.getUserData.system_url) {
			this.base = this.getUserData.system_url;
		}
	},


	methods: {
		getValues: function getValues(path) {

			var packageId = Object(__WEBPACK_IMPORTED_MODULE_3_helpers_extraLogics__["m" /* getIdFromUrl */])(path);

			if (path.indexOf("edit") >= 0) {

				this.title = 'edit-package';

				this.iconClass = 'fas fa-sync';

				this.btnName = 'update';

				this.hasDataPopulated = false;

				this.getInitialValues(packageId);
			} else {

				this.loading = false;

				this.hasDataPopulated = true;
			}
		},
		getInitialValues: function getInitialValues(id) {
			var _this = this;

			this.loading = true;

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('/api/bill/package/edit/' + id).then(function (res) {

				_this.package_id = id;

				_this.hasDataPopulated = true;

				_this.loading = false;

				_this.updateStatesWithData(res.data.data);
			}).catch(function (err) {

				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err);

				_this.hasDataPopulated = true;

				_this.loading = false;
			});
		},
		updateStatesWithData: function updateStatesWithData(packageData) {

			var self = this;

			var stateData = this.$data;

			Object.keys(packageData).map(function (key) {

				if (stateData.hasOwnProperty(key)) {

					self[key] = packageData[key];
				}
			});
		},
		isValid: function isValid() {
			var _validatePackageCreat = Object(__WEBPACK_IMPORTED_MODULE_2_faveoBilling_helpers_validator_packageCreateRules__["a" /* validatePackageCreateSettings */])(this.$data),
			    errors = _validatePackageCreat.errors,
			    isValid = _validatePackageCreat.isValid;

			if (!isValid) {

				return false;
			}

			return true;
		},
		onSubmit: function onSubmit() {
			var _this2 = this;

			this.kb_link = this.kb_link === null ? '' : this.kb_link;

			if (this.isValid()) {

				this.loadingSpeed = 8000;

				this.loading = true;

				var fd = new FormData();

				if (this.package_id != '') {

					fd.append('id', parseInt(this.package_id));
				}

				fd.append('name', this.name);

				fd.append('status', this.status === true || this.status === 1 ? 1 : 0);

				fd.append('description', this.description);

				fd.append('display_order', this.display_order);

				fd.append('price', parseInt(this.price));

				fd.append('validity', this.validity);

				if (this.departments.length > 0) {

					fd.append('departments', this.departments.map(function (a) {
						return a.id;
					}));
				}

				this.credit_type === 1 ? fd.append('allowed_tickets', this.allowed_tickets) : fd.append('validity', this.validity);
				//Faveo ke purvajon Bhagwaan iskliye tumhe kabhi maaf nhi krega
				this.package_pic.length === 1 ? fd.append('package_pic', this.package_pic.shift()) : fd.append('package_pic', this.package_pic);

				fd.append('kb_link', this.kb_link);

				var config = { headers: { 'Content-Type': 'multipart/form-data' } };

				__WEBPACK_IMPORTED_MODULE_0_axios___default.a.post('/bill/package/store-update', fd, config).then(function (res) {

					_this2.loading = false;

					Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'package');

					if (_this2.package_id === '') {

						_this2.redirect('/bill/package/inbox');
					} else {
						_this2.getInitialValues(_this2.package_id);
					}
				}).catch(function (err) {

					_this2.loading = false;

					Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'category');
				});
			}
		},
		onChange: function onChange(value, name) {

			this[name] = value;
		}
	},

	components: {

		'text-field': __webpack_require__(11),

		'number-field': __webpack_require__(32),

		'radio-button': __webpack_require__(21),

		'alert': __webpack_require__(6),

		"custom-loader": __webpack_require__(9),

		'static-select': __webpack_require__(26),

		'dynamic-select': __webpack_require__(14),

		'status-switch': __webpack_require__(38),

		'time-field': __webpack_require__(2725),

		'file-upload': __webpack_require__(194)

	}
});

/***/ }),

/***/ 2724:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = validatePackageCreateSettings;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_easy_validator_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);
/**
 * This file contains all the validation rules specific to form.
 *
 * RULES : method name for the form should be 'validateFormName'
 * */







/**
 * @param {object} data      emailSettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
function validatePackageCreateSettings(data) {
    var name = data.name,
        display_order = data.display_order,
        description = data.description,
        allowed_tickets = data.allowed_tickets,
        price = data.price,
        validity = data.validity,
        kb_link = data.kb_link;
    //rules has to apply only after checking conditions

    var validatingData = {
        name: [name, 'isRequired'],
        display_order: [display_order, 'minValue(1)', 'isRequired'],
        description: [description, 'isRequired'],
        price: [price, 'isRequired'],
        allowed_tickets: [allowed_tickets, 'isRequired'],
        validity: [validity, 'isRequired'],
        kb_link: [kb_link, 'isUrl']
    };

    // if(data.credit_type === 1){

    //     validatingData['allowed_tickets'] = [allowed_tickets, 'isRequired']
    // } else {
    //     console.log(data)
    //     validatingData['validity'] = [validity, 'isRequired']

    // }

    //creating a validator instance and pasing lang method to it
    var validator = new __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__["Validator"](__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["q" /* lang */]);

    var _validator$validate = validator.validate(validatingData),
        errors = _validator$validate.errors,
        isValid = _validator$validate.isValid;

    // write to vuex if errors


    __WEBPACK_IMPORTED_MODULE_0_store__["a" /* store */].dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

    return { errors: errors, isValid: isValid };
};

/***/ }),

/***/ 2725:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2726)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2728)
/* template */
var __vue_template__ = __webpack_require__(2729)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-2b80245f"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/MiniComponent/FormField/DayHourMinuteField.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2b80245f", Component.options)
  } else {
    hotAPI.reload("data-v-2b80245f", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2726:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2727);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("d1b9168e", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2b80245f\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./DayHourMinuteField.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2b80245f\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./DayHourMinuteField.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2727:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.inline[data-v-2b80245f] {\n        display:inline;\n}\n.form-control[data-v-2b80245f] {\n        display:inline !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2728:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'time-field',

  description: 'time field component along with error block',

  props: {

    /**
              * the label that needs to be displayed
              * @type {String}
              */
    label: { type: String, required: true },

    /**
     * Hint regarding what the field is about (it will be shown as tooltip message)
     * @type {String}
     */
    hint: { type: String, default: '' }, //for tooltip message

    /**
     * selected value of the field.
     * list of already selected element ids that has to be displayed
     * @type {Number|Boolean}
     */
    value: { required: true },

    /**
     * the name of the state in parent class
     * @type {String}
     */
    name: { type: String, required: true },

    /**
     * Type of the text field. Available options : text, textarea, password, number
     * @type {String}
     */
    type: { type: String, default: 'text' },

    /**
     * The function which will be called as soon as value of the field changes
     * It should have two arguments `value` and `name`
     *     `value` will be the updated value of the field
     *     `name` will be thw name of the state in the parent class
     *
     * An example function :  
     *         onChange(value, name){
     *             this[name]= selectedValue
     *         }
     *         
     * @type {Function}
     */
    onChange: { type: Function, Required: true },

    /**
     * classname of the form field. It can be used to give this component any bootstrap class or a custom class
     * whose css will be defined in parent class
     * @type {String}
     */
    classname: { type: String, default: '' },

    /**
     * Whether the given field is required or not.
     * If passed yes, an asterik will be displayed after the label
     * @type {Boolean}
     */
    required: { type: Boolean, default: false },

    /**
     * for show labels of the fields
     * @type {Object}
     */
    labelStyle: { type: Object },

    /**
     * for width of the fields
     * @type {Object}
     */
    formStyle: { type: Object }

  },
  data: function data() {
    return {

      yearsValue: 0,

      monthsValue: 0,
      /**
       * The initial value in the hours field
       * @type {String}
       */
      daysValue: 0,

      /**
       * The initial value in the hours field
       * @type {String}
       */
      hoursValue: 0,
      /**
      * The initial value in the minutes field
      * @type {String}
      */
      minutesValue: 0
    };
  },
  created: function created() {
    window.eventHub.$on('removeVal', this.initialState);
  },
  mounted: function mounted() {

    if (this.value === '') {
      this.yearsValue = 0;
      this.monthsValue = 0;
      this.daysValue = 0;
      this.hoursValue = 0;
    } else {
      this.yearsValue = this.value.years;
      this.monthsValue = this.value.months;
      this.daysValue = this.value.days;
      this.hoursValue = this.value.hours;
    }

    // if(this.value !== '' && this.value !== 0){
    //         this.daysValue=Math.floor(this.value/24);
    //         this.hoursValue=Math.floor(this.value/60);
    //         this.minutesValue = this.value%60;
    //    } else {
    //         this.hoursValue=0;
    //         this.minutesValue = 0;
    //         this.minutesValue = 0;
    //    }
  },


  methods: {
    /**
     * for updating hours and minutes value when values changed in the fields
     * @return {[type]} [description]
     */
    onInput: function onInput() {
      var time = {
        years: this.yearsValue,
        months: this.monthsValue,
        days: this.daysValue,
        hours: this.hoursValue
      };

      this.onChange(time, this.name);
      // this.onChange((+this.daysValue * 24 * 60) + (+this.hoursValue * 60) + +this.minutesValue, this.name);
    },


    /**
     * method for allowing users to entering only numbers
     * @param  {Event} event 
     * @return {Boolean}
     */
    checkValue: function checkValue(evt) {
      evt = evt ? evt : window.event;
      var charCode = evt.which ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault();;
      } else {
        return true;
      }
    },


    /**
     * method for check values on paste 
     * @return {Boolean}
     */
    onPaste: function onPaste(evt) {
      evt = evt ? evt : window.event;
      evt.preventDefault();
    },


    /**
     * initial state of the data
     * @return {Void}
     */
    initialState: function initialState() {
      this.hoursValue = 0;
      this.monthsValue = 0;
      this.yearssValue = 0;
      this.daysValue = 0;
    }
  },
  components: {
    'form-field-template': __webpack_require__(30)
  }
});

/***/ }),

/***/ 2729:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "form-field-template",
    {
      attrs: {
        label: _vm.label,
        labelStyle: _vm.labelStyle,
        name: _vm.name,
        classname: _vm.classname,
        hint: _vm.hint,
        required: _vm.required
      }
    },
    [
      _c("span", { staticClass: "inline" }, [
        _vm.type === "checkbox"
          ? _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.yearsValue,
                  expression: "yearsValue"
                }
              ],
              staticClass: "form-control",
              style: _vm.formStyle,
              attrs: {
                maxlength: "4",
                id: "txtYears",
                placeholder: "0",
                type: "checkbox"
              },
              domProps: {
                checked: Array.isArray(_vm.yearsValue)
                  ? _vm._i(_vm.yearsValue, null) > -1
                  : _vm.yearsValue
              },
              on: {
                input: function($event) {
                  _vm.onInput()
                },
                keypress: _vm.checkValue,
                paste: _vm.onPaste,
                change: function($event) {
                  var $$a = _vm.yearsValue,
                    $$el = $event.target,
                    $$c = $$el.checked ? true : false
                  if (Array.isArray($$a)) {
                    var $$v = null,
                      $$i = _vm._i($$a, $$v)
                    if ($$el.checked) {
                      $$i < 0 && (_vm.yearsValue = $$a.concat([$$v]))
                    } else {
                      $$i > -1 &&
                        (_vm.yearsValue = $$a
                          .slice(0, $$i)
                          .concat($$a.slice($$i + 1)))
                    }
                  } else {
                    _vm.yearsValue = $$c
                  }
                }
              }
            })
          : _vm.type === "radio"
            ? _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.yearsValue,
                    expression: "yearsValue"
                  }
                ],
                staticClass: "form-control",
                style: _vm.formStyle,
                attrs: {
                  maxlength: "4",
                  id: "txtYears",
                  placeholder: "0",
                  type: "radio"
                },
                domProps: { checked: _vm._q(_vm.yearsValue, null) },
                on: {
                  input: function($event) {
                    _vm.onInput()
                  },
                  keypress: _vm.checkValue,
                  paste: _vm.onPaste,
                  change: function($event) {
                    _vm.yearsValue = null
                  }
                }
              })
            : _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.yearsValue,
                    expression: "yearsValue"
                  }
                ],
                staticClass: "form-control",
                style: _vm.formStyle,
                attrs: {
                  maxlength: "4",
                  id: "txtYears",
                  placeholder: "0",
                  type: _vm.type
                },
                domProps: { value: _vm.yearsValue },
                on: {
                  input: [
                    function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.yearsValue = $event.target.value
                    },
                    function($event) {
                      _vm.onInput()
                    }
                  ],
                  keypress: _vm.checkValue,
                  paste: _vm.onPaste
                }
              }),
        _vm._v(" " + _vm._s(_vm.lang("years")) + "\n\n             "),
        _vm.type === "checkbox"
          ? _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.monthsValue,
                  expression: "monthsValue"
                }
              ],
              staticClass: "form-control",
              style: _vm.formStyle,
              attrs: {
                maxlength: "4",
                id: "txtMonths",
                placeholder: "0",
                type: "checkbox"
              },
              domProps: {
                checked: Array.isArray(_vm.monthsValue)
                  ? _vm._i(_vm.monthsValue, null) > -1
                  : _vm.monthsValue
              },
              on: {
                input: function($event) {
                  _vm.onInput()
                },
                keypress: _vm.checkValue,
                paste: _vm.onPaste,
                change: function($event) {
                  var $$a = _vm.monthsValue,
                    $$el = $event.target,
                    $$c = $$el.checked ? true : false
                  if (Array.isArray($$a)) {
                    var $$v = null,
                      $$i = _vm._i($$a, $$v)
                    if ($$el.checked) {
                      $$i < 0 && (_vm.monthsValue = $$a.concat([$$v]))
                    } else {
                      $$i > -1 &&
                        (_vm.monthsValue = $$a
                          .slice(0, $$i)
                          .concat($$a.slice($$i + 1)))
                    }
                  } else {
                    _vm.monthsValue = $$c
                  }
                }
              }
            })
          : _vm.type === "radio"
            ? _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.monthsValue,
                    expression: "monthsValue"
                  }
                ],
                staticClass: "form-control",
                style: _vm.formStyle,
                attrs: {
                  maxlength: "4",
                  id: "txtMonths",
                  placeholder: "0",
                  type: "radio"
                },
                domProps: { checked: _vm._q(_vm.monthsValue, null) },
                on: {
                  input: function($event) {
                    _vm.onInput()
                  },
                  keypress: _vm.checkValue,
                  paste: _vm.onPaste,
                  change: function($event) {
                    _vm.monthsValue = null
                  }
                }
              })
            : _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.monthsValue,
                    expression: "monthsValue"
                  }
                ],
                staticClass: "form-control",
                style: _vm.formStyle,
                attrs: {
                  maxlength: "4",
                  id: "txtMonths",
                  placeholder: "0",
                  type: _vm.type
                },
                domProps: { value: _vm.monthsValue },
                on: {
                  input: [
                    function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.monthsValue = $event.target.value
                    },
                    function($event) {
                      _vm.onInput()
                    }
                  ],
                  keypress: _vm.checkValue,
                  paste: _vm.onPaste
                }
              }),
        _vm._v(" " + _vm._s(_vm.lang("months")) + "\n\n            "),
        _vm.type === "checkbox"
          ? _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.daysValue,
                  expression: "daysValue"
                }
              ],
              staticClass: "form-control",
              style: _vm.formStyle,
              attrs: {
                maxlength: "4",
                id: "txtDays",
                placeholder: "0",
                type: "checkbox"
              },
              domProps: {
                checked: Array.isArray(_vm.daysValue)
                  ? _vm._i(_vm.daysValue, null) > -1
                  : _vm.daysValue
              },
              on: {
                input: function($event) {
                  _vm.onInput()
                },
                keypress: _vm.checkValue,
                paste: _vm.onPaste,
                change: function($event) {
                  var $$a = _vm.daysValue,
                    $$el = $event.target,
                    $$c = $$el.checked ? true : false
                  if (Array.isArray($$a)) {
                    var $$v = null,
                      $$i = _vm._i($$a, $$v)
                    if ($$el.checked) {
                      $$i < 0 && (_vm.daysValue = $$a.concat([$$v]))
                    } else {
                      $$i > -1 &&
                        (_vm.daysValue = $$a
                          .slice(0, $$i)
                          .concat($$a.slice($$i + 1)))
                    }
                  } else {
                    _vm.daysValue = $$c
                  }
                }
              }
            })
          : _vm.type === "radio"
            ? _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.daysValue,
                    expression: "daysValue"
                  }
                ],
                staticClass: "form-control",
                style: _vm.formStyle,
                attrs: {
                  maxlength: "4",
                  id: "txtDays",
                  placeholder: "0",
                  type: "radio"
                },
                domProps: { checked: _vm._q(_vm.daysValue, null) },
                on: {
                  input: function($event) {
                    _vm.onInput()
                  },
                  keypress: _vm.checkValue,
                  paste: _vm.onPaste,
                  change: function($event) {
                    _vm.daysValue = null
                  }
                }
              })
            : _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.daysValue,
                    expression: "daysValue"
                  }
                ],
                staticClass: "form-control",
                style: _vm.formStyle,
                attrs: {
                  maxlength: "4",
                  id: "txtDays",
                  placeholder: "0",
                  type: _vm.type
                },
                domProps: { value: _vm.daysValue },
                on: {
                  input: [
                    function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.daysValue = $event.target.value
                    },
                    function($event) {
                      _vm.onInput()
                    }
                  ],
                  keypress: _vm.checkValue,
                  paste: _vm.onPaste
                }
              }),
        _vm._v(" " + _vm._s(_vm.lang("days")) + "\n\n            "),
        _vm.type === "checkbox"
          ? _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.hoursValue,
                  expression: "hoursValue"
                }
              ],
              staticClass: "form-control",
              style: _vm.formStyle,
              attrs: {
                maxlength: "4",
                id: "txtHours",
                placeholder: "0",
                type: "checkbox"
              },
              domProps: {
                checked: Array.isArray(_vm.hoursValue)
                  ? _vm._i(_vm.hoursValue, null) > -1
                  : _vm.hoursValue
              },
              on: {
                input: function($event) {
                  _vm.onInput()
                },
                keypress: _vm.checkValue,
                paste: _vm.onPaste,
                change: function($event) {
                  var $$a = _vm.hoursValue,
                    $$el = $event.target,
                    $$c = $$el.checked ? true : false
                  if (Array.isArray($$a)) {
                    var $$v = null,
                      $$i = _vm._i($$a, $$v)
                    if ($$el.checked) {
                      $$i < 0 && (_vm.hoursValue = $$a.concat([$$v]))
                    } else {
                      $$i > -1 &&
                        (_vm.hoursValue = $$a
                          .slice(0, $$i)
                          .concat($$a.slice($$i + 1)))
                    }
                  } else {
                    _vm.hoursValue = $$c
                  }
                }
              }
            })
          : _vm.type === "radio"
            ? _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.hoursValue,
                    expression: "hoursValue"
                  }
                ],
                staticClass: "form-control",
                style: _vm.formStyle,
                attrs: {
                  maxlength: "4",
                  id: "txtHours",
                  placeholder: "0",
                  type: "radio"
                },
                domProps: { checked: _vm._q(_vm.hoursValue, null) },
                on: {
                  input: function($event) {
                    _vm.onInput()
                  },
                  keypress: _vm.checkValue,
                  paste: _vm.onPaste,
                  change: function($event) {
                    _vm.hoursValue = null
                  }
                }
              })
            : _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.hoursValue,
                    expression: "hoursValue"
                  }
                ],
                staticClass: "form-control",
                style: _vm.formStyle,
                attrs: {
                  maxlength: "4",
                  id: "txtHours",
                  placeholder: "0",
                  type: _vm.type
                },
                domProps: { value: _vm.hoursValue },
                on: {
                  input: [
                    function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.hoursValue = $event.target.value
                    },
                    function($event) {
                      _vm.onInput()
                    }
                  ],
                  keypress: _vm.checkValue,
                  paste: _vm.onPaste
                }
              }),
        _vm._v(" " + _vm._s(_vm.lang("hours")) + "\n    ")
      ])
    ]
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-2b80245f", module.exports)
  }
}

/***/ }),

/***/ 2730:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _vm.hasDataPopulated === false || _vm.loading === true
        ? _c(
            "div",
            { staticClass: "row" },
            [_c("custom-loader", { attrs: { duration: _vm.loadingSpeed } })],
            1
          )
        : _vm._e(),
      _vm._v(" "),
      _c("alert", { attrs: { componentName: "package" } }),
      _vm._v(" "),
      _vm.hasDataPopulated === true
        ? _c("div", { staticClass: "card card-light" }, [
            _c("div", { staticClass: "card-header" }, [
              _c("h3", { staticClass: "card-title" }, [
                _vm._v(_vm._s(_vm.lang(_vm.title)))
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "card-body" }, [
              _c(
                "div",
                { staticClass: "row" },
                [
                  _c("text-field", {
                    attrs: {
                      label: "Name",
                      value: _vm.name,
                      type: "text",
                      name: "name",
                      onChange: _vm.onChange,
                      classname: "col-sm-4",
                      required: true
                    }
                  }),
                  _vm._v(" "),
                  _c("number-field", {
                    attrs: {
                      label: _vm.lang("display_order"),
                      value: _vm.display_order,
                      name: "display_order",
                      classname: "col-sm-4",
                      onChange: _vm.onChange,
                      type: "number",
                      required: true
                    }
                  }),
                  _vm._v(" "),
                  _c("dynamic-select", {
                    attrs: {
                      label: _vm.lang("departments"),
                      multiple: true,
                      name: "departments",
                      prePopulate: true,
                      classname: "col-sm-4",
                      apiEndpoint: "/api/dependency/departments",
                      value: _vm.departments,
                      onChange: _vm.onChange
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "row" },
                [
                  _c("text-field", {
                    attrs: {
                      label: _vm.lang("description"),
                      value: _vm.description,
                      type: "textarea",
                      name: "description",
                      onChange: _vm.onChange,
                      classname: "col-sm-12",
                      required: true
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "row" },
                [
                  _c("static-select", {
                    attrs: {
                      label: _vm.lang("billing_cycle"),
                      elements: _vm.cycle_options,
                      name: "validity",
                      value: _vm.validity,
                      classname: "col-sm-6",
                      onChange: _vm.onChange,
                      required: true
                    }
                  }),
                  _vm._v(" "),
                  _c("radio-button", {
                    attrs: {
                      options: _vm.radioOptions,
                      label: _vm.lang("credit_type"),
                      name: "credit_type",
                      value: _vm.credit_type,
                      onChange: _vm.onChange,
                      classname: "form-group col-sm-3"
                    }
                  }),
                  _vm._v(" "),
                  _c("div", { staticClass: "col-sm-3" }, [
                    _c(
                      "label",
                      {
                        staticClass: "col-sm-12 control-label",
                        attrs: { for: "package" }
                      },
                      [_vm._v(_vm._s(_vm.lang("status")))]
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "col-sm-2" },
                      [
                        _c("status-switch", {
                          attrs: {
                            name: "status",
                            value: _vm.status,
                            onChange: _vm.onChange,
                            classname: "pull-left",
                            bold: true
                          }
                        })
                      ],
                      1
                    )
                  ])
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "row" },
                [
                  _vm.credit_type === 0
                    ? _c("time-field", {
                        attrs: {
                          label: _vm.lang("time"),
                          value: _vm.validity,
                          formStyle: _vm.formStyle,
                          type: "text",
                          name: "validity",
                          onChange: _vm.onChange,
                          classname: "col-sm-6",
                          required: true
                        }
                      })
                    : _c("number-field", {
                        attrs: {
                          label: _vm.lang("incident_credit"),
                          value: _vm.allowed_tickets,
                          name: "allowed_tickets",
                          classname: "col-sm-6",
                          onChange: _vm.onChange,
                          type: "number",
                          required: true
                        }
                      }),
                  _vm._v(" "),
                  _c("number-field", {
                    attrs: {
                      label: _vm.lang("price"),
                      value: _vm.price,
                      name: "price",
                      classname: "col-sm-6",
                      onChange: _vm.onChange,
                      type: "number",
                      required: true
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "row" },
                [
                  _c("text-field", {
                    attrs: {
                      label: _vm.lang("terms_conditions_page_link"),
                      value: _vm.kb_link,
                      type: "text",
                      name: "kb_link",
                      onChange: _vm.onChange,
                      classname: "col-sm-6"
                    }
                  }),
                  _vm._v(" "),
                  _c("file-upload", {
                    attrs: {
                      label: _vm.lang("image"),
                      value: _vm.package_pic,
                      name: "package_pic",
                      onChange: _vm.onChange,
                      classname: "col-sm-6",
                      accept: "image/*"
                    }
                  })
                ],
                1
              )
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "card-footer" }, [
              _c(
                "button",
                {
                  staticClass: "btn btn-primary",
                  attrs: {
                    type: "button",
                    id: "submit_btn",
                    disabled: _vm.loading
                  },
                  on: {
                    click: function($event) {
                      _vm.onSubmit()
                    }
                  }
                },
                [
                  _c("span", { class: _vm.iconClass }),
                  _vm._v(
                    " " +
                      _vm._s(_vm.lang(_vm.btnName)) +
                      "\n\t\t\t\t\t\n\t\t\t\t"
                  )
                ]
              )
            ])
          ])
        : _vm._e()
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-6926843d", module.exports)
  }
}

/***/ }),

/***/ 2731:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2732)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2734)
/* template */
var __vue_template__ = __webpack_require__(2735)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-1710ffbd"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Payment/Payment.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1710ffbd", Component.options)
  } else {
    hotAPI.reload("data-v-1710ffbd", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2732:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2733);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("533ea01a", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1710ffbd\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Payment.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1710ffbd\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Payment.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2733:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),

/***/ 2734:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'pacakges',

	description: 'Pacakges data table component',

	props: {},

	data: function data() {

		return {};
	},


	computed: {},

	watch: {},

	beforeMont: function beforeMont() {},


	methods: {},

	components: {}
});

/***/ }),

/***/ 2735:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div")
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-1710ffbd", module.exports)
  }
}

/***/ }),

/***/ 2736:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2737)
/* template */
var __vue_template__ = __webpack_require__(2744)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Payment/PaymentIndex.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-20d06445", Component.options)
  } else {
    hotAPI.reload("data-v-20d06445", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2737:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'payments',

	description: 'Payments data table component',

	data: function data() {

		return {

			base: window.axios.defaults.baseURL,

			columns: ['name', 'gateway_name', 'is_default', 'status', 'action'],

			options: {},

			apiUrl: '/bill/get-gateways-list',

			showModal: false,

			data: {}
		};
	},
	beforeMount: function beforeMount() {

		var self = this;

		this.options = {

			texts: {

				filter: '',

				limit: ''
			},

			sortIcon: {

				base: 'glyphicon',

				up: 'glyphicon-chevron-down',

				down: 'glyphicon-chevron-up'
			},

			templates: {

				status: 'data-table-status',

				action: function action(createElement, row) {

					var i = createElement('i', {
						attrs: {
							'class': 'fa fa-cogs'
						}
					});

					return createElement('button', {
						attrs: {
							class: 'btn btn-primary btn-xs'
						},
						on: {
							click: function click() {
								self.onClick(row);
							}
						}
					}, [i]);
				},

				is_default: 'data-table-is-default'
			},

			sortable: ['name', 'gateway_name', 'is_default', 'status'],

			filterable: ['name', 'gateway_name', 'is_default', 'status'],

			pagination: { chunk: 5, nav: 'fixed', edge: true },

			requestAdapter: function requestAdapter(data) {

				return {

					'sort-field': data.orderBy ? data.orderBy : 'id',

					'sort-order': data.ascending ? 'desc' : 'asc',

					'search-query': data.query.trim(),

					'page': data.page,

					'limit': data.limit
				};
			},
			responseAdapter: function responseAdapter(_ref) {
				var data = _ref.data;


				return {

					data: data.data.data,

					count: data.data.data.length
				};
			}
		};
	},


	methods: {
		onClick: function onClick(data) {

			this.data = data;

			this.showModal = true;
		},
		onClose: function onClose() {
			this.showModal = false;
			this.$store.dispatch('unsetValidationError');
		}
	},

	components: {

		'data-table': __webpack_require__(17),

		"alert": __webpack_require__(6),

		'payment-settings-modal': __webpack_require__(2738)
	}
});

/***/ }),

/***/ 2738:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2739)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2741)
/* template */
var __vue_template__ = __webpack_require__(2743)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-4163be86"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Payment/PaymentSettingsModal.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4163be86", Component.options)
  } else {
    hotAPI.reload("data-v-4163be86", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2739:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2740);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("57a0e596", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4163be86\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./PaymentSettingsModal.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4163be86\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./PaymentSettingsModal.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2740:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.label_align[data-v-4163be86] {\n\tdisplay: block; padding-left: 15px; text-indent: -15px; font-weight: normal !important; padding-top: 6px;\n}\n.checkbox_align[data-v-4163be86] {\n\twidth: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;\n}\n", ""]);

// exports


/***/ }),

/***/ 2741:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_faveoBilling_helpers_validator_validatePaymentGatewayRules_js__ = __webpack_require__(2742);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_axios__);
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//








/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'settings-modal',

	description: 'Settings Modal component',

	props: {

		showModal: { type: Boolean, default: false },

		onClose: { type: Function },

		title: { type: String, default: '' },

		data: { type: Object }
	},

	data: function data() {
		var _ref;

		return _ref = {

			isDisabled: true,

			containerStyle: { width: '800px' },

			loading: true,

			size: 60,

			status: 0,

			name: '',

			gateway_name: ''

		}, _defineProperty(_ref, 'status', 0), _defineProperty(_ref, 'is_default', false), _defineProperty(_ref, 'checkDisabled', false), _defineProperty(_ref, 'extraTextFields', []), _defineProperty(_ref, 'extraSwitchFields', []), _defineProperty(_ref, 'extraFields', {}), _ref;
	},

	beforeMount: function beforeMount() {

		this.getValues();
	},


	methods: {
		getValues: function getValues() {
			var _this = this;

			var url = this.data.gateway_name ? '/bill/gateway/' + this.data.name + '/' + this.data.gateway_name : '/bill/gateway/' + this.data.name;
			__WEBPACK_IMPORTED_MODULE_2_axios___default.a.get(url).then(function (res) {

				_this.loading = false;

				_this.isDisabled = false;

				var result = res.data.data;

				_this.name = result.name;

				_this.gateway_name = result.gateway_name;

				_this.status = result.status;

				_this.is_default = result.is_default;

				_this.checkDisabled = result.is_default === 0 ? false : true;

				_this.extraFields = result.extra;

				for (var i in result.extra) {
					if (result.extra[i].name !== 'testMode') {
						result.extra[i].value = result.extra[i].value == null ? '' : result.extra[i].value;
						_this.extraTextFields.push(result.extra[i]);
					} else {
						_this.extraSwitchFields.push(result.extra[i]);
					}
				}
			}).catch(function (error) {

				_this.loading = false;

				_this.isDisabled = false;
			});
		},
		isValid: function isValid() {
			var _validatePaymentGatew = Object(__WEBPACK_IMPORTED_MODULE_1_faveoBilling_helpers_validator_validatePaymentGatewayRules_js__["a" /* validatePaymentGatewaySettings */])(this.$data),
			    errors = _validatePaymentGatew.errors,
			    isValid = _validatePaymentGatew.isValid;

			if (!isValid) {

				return false;
			}
			return true;
		},
		onChange: function onChange(value, name) {

			this[name] = value;

			for (var i in this.extraTextFields) {
				if (this.extraTextFields[i].name === name) {
					this.extraTextFields[i].value = value;
				}
			}

			for (var i in this.extraSwitchFields) {
				if (this.extraSwitchFields[i].name === name) {
					this.extraSwitchFields[i].value = value;
				}
			}
		},
		onSubmit: function onSubmit() {
			var _this2 = this;

			if (this.isValid()) {

				var extra = {};

				for (var i in this.extraFields) {
					extra[this.extraFields[i].name] = this.extraFields[i].value;
				}

				this.loading = true;

				this.isDisabled = true;

				var data = {};

				data['name'] = this.name;

				if (this.gateway_name) {
					data['gateway_name'] = this.gateway_name;
				}
				data['is_default'] = this.is_default === false || this.is_default === 0 ? 0 : 1;

				data['status'] = this.status == false ? 0 : 1;
				if (Object.keys(extra).length > 0) {
					data['extra'] = extra;
				}
				__WEBPACK_IMPORTED_MODULE_2_axios___default.a.post('/bill/update/gateway', data).then(function (res) {

					_this2.loading = false;

					_this2.isDisabled = false;

					Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["b" /* successHandler */])(res, 'dataTableModal');

					window.eventHub.$emit('refreshData');

					_this2.onClose();
				}).catch(function (err) {

					_this2.loading = false;

					_this2.isDisabled = false;

					Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["a" /* errorHandler */])(err, 'dataTableModal');
				});
			}
		}
	},

	components: {

		'modal': __webpack_require__(13),

		'alert': __webpack_require__(6),

		'loader': __webpack_require__(8),

		'text-field': __webpack_require__(11),

		"dynamic-select": __webpack_require__(14),

		'status-switch': __webpack_require__(38)
	}
});

/***/ }),

/***/ 2742:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = validatePaymentGatewaySettings;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_easy_validator_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);







function validatePaymentGatewaySettings(data) {
    var extraTextFields = data.extraTextFields;

    console.log(data.extraTextFields, 's0');

    var validatingData = {};

    for (var i in data.extraTextFields) {
        validatingData[data.extraTextFields[i].name] = [data.extraTextFields[i].value, 'isRequired'];
    }
    // if(data.credit_type === 1){

    //     validatingData['allowed_tickets'] = [allowed_tickets, 'isRequired']
    // } else {
    //     console.log(data)
    //     validatingData['validity'] = [validity, 'isRequired']

    // }

    var validator = new __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__["Validator"](__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["q" /* lang */]);

    var _validator$validate = validator.validate(validatingData),
        errors = _validator$validate.errors,
        isValid = _validator$validate.isValid;

    __WEBPACK_IMPORTED_MODULE_0_store__["a" /* store */].dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

    return { errors: errors, isValid: isValid };
};

/***/ }),

/***/ 2743:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.showModal
    ? _c(
        "modal",
        {
          attrs: {
            showModal: _vm.showModal,
            onClose: _vm.onClose,
            containerStyle: _vm.containerStyle
          },
          on: {
            close: function($event) {
              _vm.showModal = false
            }
          }
        },
        [
          _c("div", { attrs: { slot: "title" }, slot: "title" }, [
            _c("h4", { staticClass: "modal-title" }, [
              _vm._v(_vm._s(_vm.lang(_vm.title)))
            ])
          ]),
          _vm._v(" "),
          !_vm.loading
            ? _c("div", { attrs: { slot: "fields" }, slot: "fields" }, [
                _c(
                  "div",
                  { staticClass: "row" },
                  [
                    _c("text-field", {
                      attrs: {
                        label: _vm.lang("name"),
                        value: _vm.name,
                        type: "text",
                        name: "name",
                        disabled: true,
                        onChange: _vm.onChange,
                        classname: "col-sm-5"
                      }
                    }),
                    _vm._v(" "),
                    _c("text-field", {
                      attrs: {
                        label: _vm.lang("gateway_name"),
                        value: _vm.gateway_name,
                        type: "text",
                        name: "gateway_name",
                        disabled: true,
                        onChange: _vm.onChange,
                        classname: "col-sm-5"
                      }
                    }),
                    _vm._v(" "),
                    _c("div", { staticClass: "col-sm-2" }, [
                      _c(
                        "label",
                        {
                          staticClass: "col-sm-12 control-label",
                          attrs: { for: "package" }
                        },
                        [_vm._v(_vm._s(_vm.lang("status")))]
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        { staticClass: "col-sm-2" },
                        [
                          _c("status-switch", {
                            attrs: {
                              name: "status",
                              value: _vm.status,
                              onChange: _vm.onChange,
                              classname: "pull-left",
                              bold: true
                            }
                          })
                        ],
                        1
                      )
                    ])
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "row" },
                  [
                    _vm._l(_vm.extraTextFields, function(field, index) {
                      return _c("text-field", {
                        key: field.index,
                        attrs: {
                          label: _vm.lang(field.name),
                          value: field.value,
                          type: "text",
                          name: field.name,
                          onChange: _vm.onChange,
                          classname: "col-sm-5",
                          required: true
                        }
                      })
                    }),
                    _vm._v(" "),
                    _vm._l(_vm.extraSwitchFields, function(field, index) {
                      return _c(
                        "div",
                        { key: field.index, staticClass: "col-sm-2" },
                        [
                          _c(
                            "label",
                            { staticClass: "col-sm-12 control-label" },
                            [_vm._v(_vm._s(_vm.lang(field.name)))]
                          ),
                          _vm._v(" "),
                          _c(
                            "div",
                            { staticClass: "col-sm-2" },
                            [
                              _c("status-switch", {
                                attrs: {
                                  name: field.name,
                                  value: field.value,
                                  onChange: _vm.onChange,
                                  classname: "pull-left",
                                  bold: true
                                }
                              })
                            ],
                            1
                          )
                        ]
                      )
                    })
                  ],
                  2
                ),
                _vm._v(" "),
                _c("div", { staticClass: "row" }, [
                  _c(
                    "div",
                    {
                      staticClass: "form-group col-md-12",
                      attrs: { id: "align" }
                    },
                    [
                      _c("label", { staticClass: "label_align" }, [
                        _c("input", {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.is_default,
                              expression: "is_default"
                            }
                          ],
                          staticClass: "checkbox_align",
                          attrs: {
                            type: "checkbox",
                            name: "default",
                            disabled: _vm.checkDisabled
                          },
                          domProps: {
                            checked: Array.isArray(_vm.is_default)
                              ? _vm._i(_vm.is_default, null) > -1
                              : _vm.is_default
                          },
                          on: {
                            change: function($event) {
                              var $$a = _vm.is_default,
                                $$el = $event.target,
                                $$c = $$el.checked ? true : false
                              if (Array.isArray($$a)) {
                                var $$v = null,
                                  $$i = _vm._i($$a, $$v)
                                if ($$el.checked) {
                                  $$i < 0 &&
                                    (_vm.is_default = $$a.concat([$$v]))
                                } else {
                                  $$i > -1 &&
                                    (_vm.is_default = $$a
                                      .slice(0, $$i)
                                      .concat($$a.slice($$i + 1)))
                                }
                              } else {
                                _vm.is_default = $$c
                              }
                            }
                          }
                        }),
                        _vm._v(
                          "\n\t\t\t\t\t\t " +
                            _vm._s(_vm.lang("make-default-payment-gateway")) +
                            "\n\t\t\t\t"
                        )
                      ])
                    ]
                  )
                ])
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.loading
            ? _c(
                "div",
                {
                  staticClass: "row",
                  attrs: { slot: "fields" },
                  slot: "fields"
                },
                [
                  _c("loader", {
                    attrs: {
                      "animation-duration": 4000,
                      color: "#1d78ff",
                      size: _vm.size
                    }
                  })
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _c("div", { attrs: { slot: "controls" }, slot: "controls" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-primary",
                attrs: {
                  type: "button",
                  id: "submit_btn",
                  disabled: _vm.isDisabled
                },
                on: {
                  click: function($event) {
                    _vm.onSubmit()
                  }
                }
              },
              [
                _c("i", {
                  staticClass: "fas fa-sync",
                  attrs: { "aria-hidden": "true" }
                }),
                _vm._v(" " + _vm._s(_vm.lang("update")) + "\n\t\t")
              ]
            )
          ])
        ]
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-4163be86", module.exports)
  }
}

/***/ }),

/***/ 2744:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("alert", { attrs: { componentName: "dataTableModal" } }),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "card card-light" },
        [
          _c("div", { staticClass: "card-header" }, [
            _c(
              "h3",
              { staticClass: "card-title", attrs: { id: "payment-title" } },
              [_vm._v(_vm._s(_vm.lang("list_of_payment_gateways")))]
            )
          ]),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "card-body" },
            [
              _c("data-table", {
                attrs: {
                  url: _vm.apiUrl,
                  dataColumns: _vm.columns,
                  option: _vm.options,
                  scroll_to: "payment-title"
                }
              })
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "transition",
            { attrs: { name: "modal" } },
            [
              _vm.showModal
                ? _c("payment-settings-modal", {
                    attrs: {
                      title: "settings",
                      onClose: _vm.onClose,
                      showModal: _vm.showModal,
                      data: _vm.data
                    }
                  })
                : _vm._e()
            ],
            1
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-20d06445", module.exports)
  }
}

/***/ }),

/***/ 2745:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2746)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2748)
/* template */
var __vue_template__ = __webpack_require__(2761)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-0bd7208a"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/Agent/PackageInvoice.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0bd7208a", Component.options)
  } else {
    hotAPI.reload("data-v-0bd7208a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2746:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2747);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("4b5dbf58", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0bd7208a\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./PackageInvoice.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0bd7208a\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./PackageInvoice.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2747:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n#pdf[data-v-0bd7208a],#print[data-v-0bd7208a],#pay[data-v-0bd7208a],#mail[data-v-0bd7208a], #unpaid[data-v-0bd7208a]{\n\t\tcursor: pointer;\n}\n#product_price[data-v-0bd7208a]{\n\t\twidth : 25%;\n}\n.invoice[data-v-0bd7208a] {\n    \tborder: unset !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2748:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_faveoBilling_helpers_validator_invoiceRules__ = __webpack_require__(2749);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_vuex__ = __webpack_require__(7);
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//












/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'package-invoice',

	description: 'Package invoice page',

	props: {},

	data: function data() {

		return {

			hasDataPopulated: false,

			loading: false,

			loadingSpeed: 4000,

			company_name: '',

			from: {},

			to: {},

			invoice_id: 0,

			order_id: 0,

			paid_date: '',

			packages: {},

			transactions: [],

			total_amount: 0,

			paid_amount: 0,

			labelStyle: { display: 'none' },

			showModal: false,

			base: '',

			payable_amount: '',

			showUnpaidModal: false,

			currency: null
		};
	},


	computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_4_vuex__["b" /* mapGetters */])(['formattedTime', 'formattedDate', 'getUserData'])),

	watch: {
		getUserData: function getUserData(newValue, oldValue) {
			this.base = newValue.system.url;
			return newValue;
		}
	},

	beforeMount: function beforeMount() {

		var path = window.location.pathname;

		this.invoiceId = Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["m" /* getIdFromUrl */])(path);

		this.getData();
	},
	created: function created() {

		window.eventHub.$on('reloadInvoiceData', this.getData);

		if (this.getUserData.system) {
			this.base = this.getUserData.system.url;
		}
	},


	methods: {
		getData: function getData() {
			var _this = this;

			this.loading = true;

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('/bill/package/user-invoice-info/' + this.invoiceId).then(function (res) {

				var result = res.data.data;

				_this.company_name = result.company_name;

				_this.from = result.from;

				_this.to = result.order.user;

				_this.invoice_id = result.invoice_id;

				_this.order_id = result.order_id;

				_this.paid_date = result.paid_date;

				_this.payable_amount = result.payable_amount;

				_this.packages = result.order.package;

				_this.transactions = result.transactions;

				_this.total_amount = result.payable_amount;

				_this.paid_amount = result.amount_paid;

				_this.hasDataPopulated = true;

				_this.currency = result.currency;

				_this.loading = false;
			}).catch(function (err) {

				Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(err);

				_this.hasDataPopulated = true;

				_this.loading = false;
			});
		},
		isValid: function isValid() {
			var _validateInvoiceSetti = Object(__WEBPACK_IMPORTED_MODULE_3_faveoBilling_helpers_validator_invoiceRules__["a" /* validateInvoiceSettings */])(this.$data),
			    errors = _validateInvoiceSetti.errors,
			    isValid = _validateInvoiceSetti.isValid;

			if (!isValid) {

				return false;
			}

			return true;
		},
		onClose: function onClose() {

			this.showModal = false;

			this.showUnpaidModal = false;

			this.$store.dispatch('unsetValidationError');
		},
		refreshPage: function refreshPage() {
			console.log('claed');
			this.getData();
		},
		onChange: function onChange(value, name) {

			this.payable_amount = value;
		},
		printInvoice: function printInvoice(divID) {

			var divElements = document.getElementById(divID).innerHTML;

			var newWindow = window.open('', '', 'width=auto,height=auto');

			newWindow.document.write("<html><head><title></title></head><body>" + divElements + "</body>");

			newWindow.document.close();

			newWindow.focus();

			newWindow.print();
			setTimeout(function () {
				newWindow.close();
			}, 5);
		},
		sendMail: function sendMail() {
			var _this2 = this;

			this.loading = true;

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('/invoice/send/' + this.invoice_id).then(function (res) {

				_this2.loading = false;

				Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["b" /* successHandler */])(res, 'invoice');
			}).catch(function (error) {

				Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(error, 'invoice');

				_this2.loading = false;
			});
		},
		onSubmit: function onSubmit() {

			if (this.isValid()) {

				this.showModal = true;
			}
		},
		onUpdate: function onUpdate() {
			var _this3 = this;

			if (this.isValid()) {

				this.loading = true;

				var data = {};

				data['invoice_id'] = this.invoice_id;

				data['payable_amount'] = this.payable_amount;

				__WEBPACK_IMPORTED_MODULE_0_axios___default.a.put('/invoice/update', data).then(function (res) {

					_this3.loading = false;

					_this3.refreshPage();

					Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["b" /* successHandler */])(res, 'invoice');
				}).catch(function (err) {

					_this3.loading = false;

					Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(err, 'invoice');
				});
			}
		},
		getFormattedCurrency: function getFormattedCurrency(value, currency) {
			return Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["f" /* currencyFormatter */])(value, currency, localStorage.getItem("LANGUAGE"));
		}
	},

	components: {

		'alert': __webpack_require__(6),

		"custom-loader": __webpack_require__(9),

		'number-field': __webpack_require__(32),

		'invoice-modal': __webpack_require__(2750),

		'unpaid-modal': __webpack_require__(2756)
	}
});

/***/ }),

/***/ 2749:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = validateInvoiceSettings;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_easy_validator_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);







function validateInvoiceSettings(data) {
    var packages = data.packages;

    var validatingData = {};

    if (data.packages) {

        validatingData['price'] = [data.packages.price, 'isRequired'];
    }

    var validator = new __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__["Validator"](__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["q" /* lang */]);

    var _validator$validate = validator.validate(validatingData),
        errors = _validator$validate.errors,
        isValid = _validator$validate.isValid;

    __WEBPACK_IMPORTED_MODULE_0_store__["a" /* store */].dispatch('setValidationError', errors);

    return { errors: errors, isValid: isValid };
};

/***/ }),

/***/ 2750:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2751)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2753)
/* template */
var __vue_template__ = __webpack_require__(2755)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-11cf494a"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/Agent/Tables/MiniComponents/InvoiceModal.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-11cf494a", Component.options)
  } else {
    hotAPI.reload("data-v-11cf494a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2751:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2752);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("4cd371e7", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../../../node_modules/css-loader/index.js!../../../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-11cf494a\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./InvoiceModal.vue", function() {
     var newContent = require("!!../../../../../../../../../node_modules/css-loader/index.js!../../../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-11cf494a\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./InvoiceModal.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2752:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.has-feedback .form-control[data-v-11cf494a] {\n\tpadding-right: 0px !important;\n}\n#H5[data-v-11cf494a]{\n\tmargin-left:16px; margin-bottom:18px !important;\n}\n.margin[data-v-11cf494a] {\n\tmargin-right: 16px !important;margin-left: 0px !important;\n}\n.label_align[data-v-11cf494a] {\n\tdisplay: block; padding-left: 15px; text-indent: -15px; font-weight: normal !important; padding-top: 6px;\n}\n.checkbox_align[data-v-11cf494a] {\n\twidth: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;\n}\n#align[data-v-11cf494a]{\n\tmargin-left: 15px !important\n}\n", ""]);

// exports


/***/ }),

/***/ 2753:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_faveoBilling_helpers_validator_validateInvoicePaymentRules_js__ = __webpack_require__(2754);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_axios__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//








/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'settings-modal',

	description: 'Settings Modal component',

	props: {

		showModal: { type: Boolean, default: false },

		onClose: { type: Function },

		title: { type: String, default: '' },

		id: { type: String | Number, default: '' }
	},

	data: function data() {
		return {

			isDisabled: false,

			containerStyle: { width: '600px' },

			loading: false,

			size: 60,

			status: 0,

			transaction_id: '',

			amount: '',

			add_to_credit: false,

			payment_gateway: [],

			gateway: '',

			comment: ''
		};
	},

	methods: {
		isValid: function isValid() {
			var _validateInvoicePayme = Object(__WEBPACK_IMPORTED_MODULE_1_faveoBilling_helpers_validator_validateInvoicePaymentRules_js__["a" /* validateInvoicePaymentSettings */])(this.$data),
			    errors = _validateInvoicePayme.errors,
			    isValid = _validateInvoicePayme.isValid;

			if (!isValid) {

				return false;
			}
			return true;
		},
		onChange: function onChange(value, name) {

			this[name] = value;
		},
		onSubmit: function onSubmit() {
			var _this = this;

			if (this.isValid()) {

				this.loading = true;

				this.isDisabled = true;

				var data = {};

				data['method'] = this.gateway;

				data['transactionId'] = this.transaction_id;

				data['invoice'] = this.id;

				data['amount'] = parseInt(this.amount);

				data['comment'] = this.comment;

				__WEBPACK_IMPORTED_MODULE_2_axios___default.a.post('/bill/package/add-payment', data).then(function (res) {

					_this.loading = false;

					_this.isDisabled = false;

					Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["b" /* successHandler */])(res, 'invoice');

					window.eventHub.$emit('reloadInvoiceData');

					_this.onClose();
				}).catch(function (err) {

					_this.loading = false;

					_this.isDisabled = false;

					Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["a" /* errorHandler */])(err, 'invoice');

					_this.onClose();
				});
			}
		}
	},

	components: {

		'modal': __webpack_require__(13),

		'loader': __webpack_require__(8),

		'text-field': __webpack_require__(11),

		"static-select": __webpack_require__(26),

		'number-field': __webpack_require__(32)
	},

	beforeMount: function beforeMount() {
		var _this2 = this;

		// get payment getway list object
		__WEBPACK_IMPORTED_MODULE_2_axios___default.a.get('bill/get-gateways-list', {
			params: {
				"active": 1
			}
		}).then(function (res) {
			res.data.data.data.forEach(function (element) {
				_this2.payment_gateway.push({ 'id': element.name, 'name': element.name });
			});
		}).catch(function (err) {
			Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["a" /* errorHandler */])(err, 'invoice');
		});
	}
});

/***/ }),

/***/ 2754:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = validateInvoicePaymentSettings;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_easy_validator_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);







function validateInvoicePaymentSettings(data) {
    var gateway = data.gateway,
        transaction_id = data.transaction_id,
        amount = data.amount;

    var validatingData = {

        gateway: [gateway, 'isRequired'],

        transaction_id: [transaction_id, 'isRequired'],

        amount: [amount, 'isRequired']
    };

    var validator = new __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__["Validator"](__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["q" /* lang */]);

    var _validator$validate = validator.validate(validatingData),
        errors = _validator$validate.errors,
        isValid = _validator$validate.isValid;

    __WEBPACK_IMPORTED_MODULE_0_store__["a" /* store */].dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

    return { errors: errors, isValid: isValid };
};

/***/ }),

/***/ 2755:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.showModal
    ? _c(
        "modal",
        {
          attrs: {
            showModal: _vm.showModal,
            onClose: _vm.onClose,
            containerStyle: _vm.containerStyle
          },
          on: {
            close: function($event) {
              _vm.showModal = false
            }
          }
        },
        [
          _c("div", { attrs: { slot: "title" }, slot: "title" }, [
            _c("h4", { staticClass: "modal-title" }, [
              _vm._v(_vm._s(_vm.lang(_vm.title)))
            ])
          ]),
          _vm._v(" "),
          !_vm.loading
            ? _c("div", { attrs: { slot: "fields" }, slot: "fields" }, [
                _c(
                  "div",
                  { staticClass: "row" },
                  [
                    _c("static-select", {
                      attrs: {
                        label: _vm.lang("payment_gateway"),
                        elements: _vm.payment_gateway,
                        name: "gateway",
                        value: _vm.gateway,
                        classname: "col-sm-12",
                        onChange: _vm.onChange,
                        required: true
                      }
                    }),
                    _vm._v(" "),
                    _c("text-field", {
                      attrs: {
                        label: _vm.lang("transaction_id"),
                        value: _vm.transaction_id,
                        type: "text",
                        name: "transaction_id",
                        onChange: _vm.onChange,
                        required: true,
                        classname: "col-sm-12"
                      }
                    }),
                    _vm._v(" "),
                    _c("number-field", {
                      attrs: {
                        label: _vm.lang("amount"),
                        value: _vm.amount,
                        name: "amount",
                        classname: "col-sm-12",
                        onChange: _vm.onChange,
                        type: "number",
                        required: true
                      }
                    }),
                    _vm._v(" "),
                    _c("text-field", {
                      attrs: {
                        label: _vm.lang("comment"),
                        value: _vm.comment,
                        type: "textarea",
                        name: "comment",
                        onChange: _vm.onChange,
                        required: true,
                        classname: "col-sm-12"
                      }
                    })
                  ],
                  1
                )
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.loading
            ? _c(
                "div",
                {
                  staticClass: "row",
                  attrs: { slot: "fields" },
                  slot: "fields"
                },
                [
                  _c("loader", {
                    attrs: {
                      "animation-duration": 4000,
                      color: "#1d78ff",
                      size: _vm.size
                    }
                  })
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _c("div", { attrs: { slot: "controls" }, slot: "controls" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-primary",
                attrs: {
                  type: "button",
                  id: "submit_btn",
                  disabled: _vm.isDisabled
                },
                on: {
                  click: function($event) {
                    _vm.onSubmit()
                  }
                }
              },
              [
                _c("i", {
                  staticClass: "fas fa-sync",
                  attrs: { "aria-hidden": "true" }
                }),
                _vm._v(" " + _vm._s(_vm.lang("ok")) + "\n\t\t\t")
              ]
            )
          ])
        ]
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-11cf494a", module.exports)
  }
}

/***/ }),

/***/ 2756:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2757)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2759)
/* template */
var __vue_template__ = __webpack_require__(2760)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/Agent/Tables/MiniComponents/UnpaidModal.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3e5901bd", Component.options)
  } else {
    hotAPI.reload("data-v-3e5901bd", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2757:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2758);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("34f0bce8", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../../../node_modules/css-loader/index.js!../../../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3e5901bd\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./UnpaidModal.vue", function() {
     var newContent = require("!!../../../../../../../../../node_modules/css-loader/index.js!../../../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3e5901bd\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./UnpaidModal.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2758:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.has-feedback .form-control {\n\tpadding-right: 0px !important;\n}\n#H5{\n\tmargin-left:16px; \n\t/*margin-bottom:18px !important;*/\n}\n.fulfilling-bouncing-circle-spinner{\n\tmargin: auto !important;\n}\n.margin {\n\tmargin-right: 16px !important;margin-left: 0px !important;\n}\n.spin{\n\tleft:0% !important;right: 43% !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2759:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__ = __webpack_require__(5);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'unpaid-modal',

	description: 'Unpaid Modal component',

	props: {

		/**
   * status of the modal popup
   * @type {Object}
   */
		showModal: { type: Boolean, default: false },

		/**
   * status of the delete popup modal
   * @type {Object}
   */
		title: { type: String, default: '' },

		id: { type: Number | String, default: '' },

		/**
   * The function which will be called as soon as user click on the close button        
   * @type {Function}
  */
		onClose: { type: Function },

		refreshPage: { type: Function }

	},

	data: function data() {
		return {

			/**
    * buttons disabled state
    * @type {Boolean}
    */
			isDisabled: false,

			/**
    * width of the modal container
    * @type {Object}
    */
			containerStyle: {
				width: '500px'
			},

			/**
    * initial state of loader
    * @type {Boolean}
    */
			loading: false,

			/**
    * size of the loader
    * @type {Number}
    */
			size: 60,

			/**
    * for rtl support
    * @type {String}
   */
			lang_locale: ''

		};
	},

	created: function created() {
		// getting locale from localStorage
		this.lang_locale = localStorage.getItem('LANGUAGE');
	},


	methods: {
		/**
   * api calls happens here
   * @return {Void} 
   */
		onSubmit: function onSubmit() {
			var _this = this;

			//for delete
			this.loading = true;
			this.isDisabled = true;

			var data = {};

			data['invoice_id'] = this.id;

			data['amount_paid'] = 0;

			axios.put('/invoice/update', data).then(function (res) {

				_this.loading = false;

				_this.isDisabled = true;

				_this.refreshPage();

				_this.onClose();

				Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["b" /* successHandler */])(res, 'invoice');
			}).catch(function (err) {

				_this.loading = false;

				_this.isDisabled = true;

				Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["a" /* errorHandler */])(err, 'invoice');
			});
		}
	},

	components: {
		'modal': __webpack_require__(13),
		'alert': __webpack_require__(6),
		'loader': __webpack_require__(8)
	}

});

/***/ }),

/***/ 2760:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _vm.showModal
        ? _c(
            "modal",
            {
              attrs: {
                showModal: _vm.showModal,
                onClose: _vm.onClose,
                containerStyle: _vm.containerStyle
              }
            },
            [
              _c("div", { attrs: { slot: "title" }, slot: "title" }, [
                _c("h4", [_vm._v(_vm._s(_vm.lang(_vm.title)))])
              ]),
              _vm._v(" "),
              !_vm.loading
                ? _c("div", { attrs: { slot: "fields" }, slot: "fields" }, [
                    _c(
                      "h5",
                      {
                        class: { margin: _vm.lang_locale == "ar" },
                        attrs: { id: "H5" }
                      },
                      [_vm._v(_vm._s(_vm.lang("are_you_sure")))]
                    )
                  ])
                : _vm._e(),
              _vm._v(" "),
              _vm.loading
                ? _c(
                    "div",
                    {
                      staticClass: "row",
                      attrs: { slot: "fields" },
                      slot: "fields"
                    },
                    [
                      _c("loader", {
                        class: { spin: _vm.lang_locale == "ar" },
                        attrs: {
                          "animation-duration": 4000,
                          color: "#1d78ff",
                          size: _vm.size
                        }
                      })
                    ],
                    1
                  )
                : _vm._e(),
              _vm._v(" "),
              _c("div", { attrs: { slot: "controls" }, slot: "controls" }, [
                _c(
                  "button",
                  {
                    staticClass: "btn btn-primary",
                    attrs: { type: "button", disabled: _vm.isDisabled },
                    on: { click: _vm.onSubmit }
                  },
                  [
                    _c("i", { staticClass: "fa fa-save" }),
                    _vm._v(" " + _vm._s(_vm.lang("proceed")))
                  ]
                )
              ])
            ]
          )
        : _vm._e()
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-3e5901bd", module.exports)
  }
}

/***/ }),

/***/ 2761:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _vm.hasDataPopulated === false || _vm.loading === true
        ? _c(
            "div",
            { staticClass: "row" },
            [_c("custom-loader", { attrs: { duration: _vm.loadingSpeed } })],
            1
          )
        : _vm._e(),
      _vm._v(" "),
      _c("alert", { attrs: { componentName: "invoice" } }),
      _vm._v(" "),
      _vm.hasDataPopulated === true
        ? _c("section", { staticClass: "invoice" }, [
            _c("div", { staticClass: "invoice p-3 mb-3" }, [
              _c("div", { staticClass: "row" }, [
                _c("div", { staticClass: "col-12" }, [
                  _c("h4", [
                    _c("i", { staticClass: "fas fa-globe" }),
                    _vm._v(
                      " " + _vm._s(_vm.company_name) + "\n\n\t\t\t\t\t\t  \t\t"
                    ),
                    _c("div", { staticClass: "dropdown float-right" }, [
                      _vm._m(0),
                      _vm._v(" "),
                      _c(
                        "div",
                        { staticClass: "dropdown-menu dropdown-menu-right" },
                        [
                          _vm.total_amount - _vm.paid_amount > 0
                            ? _c(
                                "a",
                                {
                                  staticClass: "dropdown-item",
                                  attrs: { id: "pay", href: "javascript:;" },
                                  on: {
                                    click: function($event) {
                                      _vm.onSubmit()
                                    }
                                  }
                                },
                                [
                                  _c("i", {
                                    staticClass: "fas fa-credit-card"
                                  }),
                                  _vm._v(
                                    " " +
                                      _vm._s(_vm.lang("add_payment")) +
                                      "\n\t\t\t\t\t\t          \t"
                                  )
                                ]
                              )
                            : _vm._e(),
                          _vm._v(" "),
                          _vm.payable_amount - _vm.paid_amount <= 0
                            ? _c(
                                "a",
                                {
                                  staticClass: "dropdown-item",
                                  attrs: { id: "unpaid", href: "javascript:;" },
                                  on: {
                                    click: function($event) {
                                      _vm.showUnpaidModal = true
                                    }
                                  }
                                },
                                [
                                  _c("i", {
                                    staticClass: "fas fa-thumbs-down"
                                  }),
                                  _vm._v(
                                    " " +
                                      _vm._s(_vm.lang("mark_as_unpaid")) +
                                      " \n\t\t\t\t\t\t\t\t\t\t"
                                  )
                                ]
                              )
                            : _vm._e(),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              staticClass: "dropdown-item",
                              attrs: { id: "print" },
                              on: {
                                click: function($event) {
                                  _vm.printInvoice("print-invoice")
                                }
                              }
                            },
                            [
                              _c("i", { staticClass: "fas fa-print" }),
                              _vm._v(
                                " " +
                                  _vm._s(_vm.lang("print")) +
                                  " \n\t\t\t\t\t\t\t\t\t\t"
                              )
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              staticClass: "dropdown-item",
                              attrs: {
                                id: "pdf",
                                href:
                                  _vm.base + "/invoice/pdf/" + _vm.invoice_id
                              }
                            },
                            [
                              _c("i", { staticClass: "fas fa-download" }),
                              _vm._v(
                                " " +
                                  _vm._s(_vm.lang("generate_pdf")) +
                                  " \n\t\t\t\t\t\t\t\t\t\t"
                              )
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              staticClass: "dropdown-item",
                              attrs: { id: "mail" },
                              on: {
                                click: function($event) {
                                  _vm.sendMail()
                                }
                              }
                            },
                            [
                              _c("i", { staticClass: "fas fa-reply" }),
                              _vm._v(
                                " " +
                                  _vm._s(_vm.lang("send_mail")) +
                                  " \n\t\t\t\t\t\t\t\t\t\t"
                              )
                            ]
                          )
                        ]
                      )
                    ])
                  ])
                ])
              ]),
              _vm._v(" "),
              _c(
                "div",
                {
                  staticClass: "row invoice-info",
                  attrs: { id: "print-invoice" }
                },
                [
                  _c("div", { staticClass: "col-sm-4 invoice-col" }, [
                    _vm._v(
                      "\n\t\t\t\t\t\t" +
                        _vm._s(_vm.lang("from")) +
                        "\n\t\t\t\t\t\t"
                    ),
                    _c(
                      "address",
                      [
                        _c("strong", [_vm._v(_vm._s(_vm.from.name))]),
                        _c("br"),
                        _vm._v(" "),
                        _vm.from.address ? void 0 : _vm._e(),
                        _vm.from.address ? _c("br") : _vm._e(),
                        _vm._v(
                          "\n\t\t\t\t\t\t  " +
                            _vm._s(_vm.lang("phone")) +
                            ": " +
                            _vm._s(_vm.from.phone)
                        ),
                        _c("br"),
                        _vm._v(
                          "\n\t\t\t\t\t\t  " +
                            _vm._s(_vm.lang("website")) +
                            ": " +
                            _vm._s(_vm.from.website) +
                            "\n\t\t\t\t\t\t"
                        )
                      ],
                      2
                    )
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "col-sm-4 invoice-col" }, [
                    _vm._v(
                      "\n\t\t\t\t\t\t" +
                        _vm._s(_vm.lang("to")) +
                        "\n\n\t\t\t\t\t\t"
                    ),
                    _c("address", [
                      _c("strong", [
                        _vm._v(
                          _vm._s(_vm.to.first_name) + _vm._s(_vm.to.last_name)
                        )
                      ]),
                      _c("br"),
                      _vm._v(
                        "\n\t\t\t\t\t\t  " +
                          _vm._s(_vm.lang("phone")) +
                          ": " +
                          _vm._s(_vm.to.phone)
                      ),
                      _c("br"),
                      _vm._v(
                        "\n\t\t\t\t\t\t  " +
                          _vm._s(_vm.lang("email")) +
                          ": " +
                          _vm._s(_vm.to.email) +
                          "\n\t\t\t\t\t\t"
                      )
                    ])
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "col-sm-4 invoice-col" }, [
                    _c("b", [_vm._v(_vm._s(_vm.lang("invoice_details")))]),
                    _c("br"),
                    _vm._v(" "),
                    _c("br"),
                    _vm._v(" "),
                    _c("b", [_vm._v(_vm._s(_vm.lang("invoice")) + ":")]),
                    _vm._v(" #" + _vm._s(_vm.invoice_id)),
                    _c("br"),
                    _vm._v(" "),
                    _c("b", [_vm._v(_vm._s(_vm.lang("order_id")) + ":")]),
                    _vm._v(" " + _vm._s(_vm.order_id)),
                    _c("br"),
                    _vm._v(" "),
                    _c("b", [_vm._v(_vm._s(_vm.lang("paid_on")) + ":")]),
                    _vm._v(
                      " " +
                        _vm._s(_vm.formattedTime(_vm.paid_date)) +
                        "\n\t\t\t\t\t "
                    )
                  ])
                ]
              ),
              _vm._v(" "),
              _c("div", { staticClass: "card card-light" }, [
                _c("div", { staticClass: "card-header" }, [
                  _c("h3", { staticClass: "card-title" }, [
                    _vm._v(_vm._s(_vm.lang("product")))
                  ])
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "card-body" }, [
                  _c("div", { staticClass: "table-responsive" }, [
                    _c("table", { staticClass: "table table-striped" }, [
                      _c("thead", [
                        _c("tr", [
                          _c("th", [_vm._v(_vm._s(_vm.lang("name")))]),
                          _vm._v(" "),
                          _c("th", [_vm._v(_vm._s(_vm.lang("description")))]),
                          _vm._v(" "),
                          _c("th", [_vm._v(_vm._s(_vm.lang("validity")))]),
                          _vm._v(" "),
                          _c("th", [_vm._v(_vm._s(_vm.lang("price")))])
                        ])
                      ]),
                      _vm._v(" "),
                      _c("tbody", [
                        _c("tr", [
                          _c("td", [_vm._v(_vm._s(_vm.packages.name))]),
                          _vm._v(" "),
                          _c("td", [_vm._v(_vm._s(_vm.packages.description))]),
                          _vm._v(" "),
                          _c("td", [
                            _vm._v(
                              _vm._s(
                                _vm.packages.validity === null
                                  ? "One time"
                                  : _vm.packages.validity
                              )
                            )
                          ]),
                          _vm._v(" "),
                          _c(
                            "td",
                            { attrs: { id: "product_price" } },
                            [
                              _c("number-field", {
                                attrs: {
                                  label: _vm.lang("price"),
                                  value: _vm.payable_amount,
                                  name: "payable_amount",
                                  classname: "",
                                  labelStyle: _vm.labelStyle,
                                  onChange: _vm.onChange,
                                  type: "number",
                                  required: true
                                }
                              })
                            ],
                            1
                          )
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "card card-light" }, [
                _c("div", { staticClass: "card-header" }, [
                  _c("h3", { staticClass: "card-title" }, [
                    _vm._v(_vm._s(_vm.lang("transactions")))
                  ])
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "card-body" }, [
                  _c("div", { staticClass: "table-responsive" }, [
                    _c("table", { staticClass: "table table-striped" }, [
                      _c("thead", [
                        _c("tr", [
                          _c("th", [_vm._v(_vm._s(_vm.lang("id")))]),
                          _vm._v(" "),
                          _c("th", [
                            _vm._v(_vm._s(_vm.lang("payment_method")))
                          ]),
                          _vm._v(" "),
                          _c("th", [_vm._v(_vm._s(_vm.lang("amount_paid")))]),
                          _vm._v(" "),
                          _c("th", [_vm._v(_vm._s(_vm.lang("transacted_by")))]),
                          _vm._v(" "),
                          _c("th", [_vm._v(_vm._s(_vm.lang("pay_date")))]),
                          _vm._v(" "),
                          _c("th", [_vm._v(_vm._s(_vm.lang("status")))])
                        ])
                      ]),
                      _vm._v(" "),
                      _c(
                        "tbody",
                        _vm._l(_vm.transactions, function(transaction) {
                          return _c("tr", [
                            _c("td", [
                              _vm._v(_vm._s(transaction.transactionId))
                            ]),
                            _vm._v(" "),
                            _c("td", [
                              _vm._v(_vm._s(transaction.payment_method))
                            ]),
                            _vm._v(" "),
                            _c("td", [_vm._v(_vm._s(transaction.amount_paid))]),
                            _vm._v(" "),
                            _c("td", [
                              _c(
                                "a",
                                {
                                  attrs: {
                                    href:
                                      _vm.base +
                                      "/user/" +
                                      transaction.paid_by.id
                                  }
                                },
                                [_vm._v(_vm._s(transaction.paid_by.full_name))]
                              )
                            ]),
                            _vm._v(" "),
                            _c("td", [
                              _vm._v(
                                _vm._s(
                                  _vm.formattedTime(transaction.created_at)
                                )
                              )
                            ]),
                            _vm._v(" "),
                            _c("td", [
                              _c("a", { attrs: { id: "status" } }, [
                                _c(
                                  "span",
                                  {
                                    class: {
                                      "btn btn-success btn-xs":
                                        transaction.status !== 0,
                                      "btn btn-danger btn-xs":
                                        transaction.status === 0
                                    },
                                    attrs: { title: "Status", id: "edit_btn" }
                                  },
                                  [
                                    _vm._v(
                                      _vm._s(
                                        transaction.status
                                          ? _vm.lang("successful")
                                          : _vm.lang("fail")
                                      ) + "\n\t\t\t\t\t\t\t\t\t\t\t\t"
                                    )
                                  ]
                                )
                              ])
                            ])
                          ])
                        })
                      )
                    ])
                  ])
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "card card-light" }, [
                _c("div", { staticClass: "card-header" }, [
                  _c("h3", { staticClass: "card-title" }, [
                    _vm._v(
                      _vm._s(_vm.lang("payment_details")) +
                        " " +
                        _vm._s(_vm.formattedTime(_vm.paid_date))
                    )
                  ])
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "card-body" }, [
                  _c("div", { staticClass: "table-responsive" }, [
                    _c("table", { staticClass: "table" }, [
                      _c("tbody", [
                        _c("tr", [
                          _c("th", { staticStyle: { width: "50%" } }, [
                            _vm._v(_vm._s(_vm.lang("paid_amount")) + " : ")
                          ]),
                          _vm._v(" "),
                          _c("td", [
                            _vm._v(
                              _vm._s(
                                _vm.getFormattedCurrency(
                                  _vm.paid_amount,
                                  _vm.currency
                                )
                              )
                            )
                          ])
                        ]),
                        _vm._v(" "),
                        _c("tr", [
                          _c("th", { staticStyle: { width: "50%" } }, [
                            _vm._v(_vm._s(_vm.lang("balance")) + " : ")
                          ]),
                          _vm._v(" "),
                          _c("td", [
                            _vm._v(
                              _vm._s(
                                _vm.getFormattedCurrency(
                                  _vm.paid_amount - _vm.total_amount,
                                  _vm.currency
                                )
                              )
                            )
                          ])
                        ]),
                        _vm._v(" "),
                        _c("tr", [
                          _c("th", [_vm._v("Total:")]),
                          _vm._v(" "),
                          _c("td", [
                            _vm._v(
                              _vm._s(
                                _vm.getFormattedCurrency(
                                  _vm.total_amount,
                                  _vm.currency
                                )
                              )
                            )
                          ])
                        ])
                      ])
                    ])
                  ])
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "card-footer" }, [
                  _c(
                    "button",
                    {
                      staticClass: "btn btn-primary",
                      attrs: { type: "button", id: "update_btn" },
                      on: {
                        click: function($event) {
                          _vm.onUpdate()
                        }
                      }
                    },
                    [
                      _c("i", { staticClass: "fas fa-sync" }),
                      _vm._v(
                        " " + _vm._s(_vm.lang("update")) + "\n\t\t\t\t\t\t\t\t"
                      )
                    ]
                  )
                ])
              ])
            ])
          ])
        : _vm._e(),
      _vm._v(" "),
      _c(
        "transition",
        { attrs: { name: "modal" } },
        [
          _vm.showModal
            ? _c("invoice-modal", {
                attrs: {
                  title: "add_payment",
                  onClose: _vm.onClose,
                  showModal: _vm.showModal,
                  id: _vm.invoice_id
                }
              })
            : _vm._e()
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "transition",
        { attrs: { name: "modal" } },
        [
          _vm.showUnpaidModal
            ? _c("unpaid-modal", {
                attrs: {
                  title: "Mark as Unpaid",
                  onClose: _vm.onClose,
                  refreshPage: _vm.refreshPage,
                  showModal: _vm.showUnpaidModal,
                  id: _vm.invoice_id
                }
              })
            : _vm._e()
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c(
      "button",
      {
        staticClass: "btn btn-default dropdown-toggle",
        attrs: { type: "button", "data-toggle": "dropdown" }
      },
      [_c("i", { staticClass: "fas fa-bars" })]
    )
  }
]
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-0bd7208a", module.exports)
  }
}

/***/ }),

/***/ 2762:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2763)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2765)
/* template */
var __vue_template__ = __webpack_require__(2766)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-f4e0d852"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Package/Agent/OrderDetails.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-f4e0d852", Component.options)
  } else {
    hotAPI.reload("data-v-f4e0d852", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2763:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2764);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("8a030d28", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-f4e0d852\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./OrderDetails.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-f4e0d852\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./OrderDetails.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2764:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.has-feedback .form-control[data-v-f4e0d852] {\n\tpadding-right: 0px !important;\n}\n#H5[data-v-f4e0d852]{\n\tmargin-left:16px; margin-bottom:18px !important;\n}\n.margin[data-v-f4e0d852] {\n\tmargin-right: 16px !important;margin-left: 0px !important;\n}\n.label_align[data-v-f4e0d852] {\n\tdisplay: block; padding-left: 15px; text-indent: -15px; font-weight: normal !important; padding-top: 6px;\n}\n.checkbox_align[data-v-f4e0d852] {\n\twidth: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;\n}\n#align[data-v-f4e0d852]{\n\tmargin-left: 15px !important\n}\n", ""]);

// exports


/***/ }),

/***/ 2765:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_vuex__ = __webpack_require__(7);
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//








/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'order-details',

	description: 'Order details component',

	data: function data() {
		return {

			loading: false,

			size: 60,

			order_id: 0,

			orderData: {}
		};
	},

	computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_2_vuex__["b" /* mapGetters */])(['formattedTime'])),

	beforeMount: function beforeMount() {

		var path = window.location.pathname;

		this.order_id = Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["m" /* getIdFromUrl */])(path);

		this.getData();
	},


	methods: {
		getData: function getData() {
			var _this = this;

			this.loading = true;

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('/bill/package/user-order-info/' + this.order_id).then(function (res) {

				_this.loading = false;

				_this.orderData = res.data.data;
			}).catch(function (error) {

				_this.loading = false;
			});
		}
	},

	components: {

		'loader': __webpack_require__(8)
	}
});

/***/ }),

/***/ 2766:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "box box-primary" }, [
    _c("div", { staticClass: "box-header" }, [
      _c("h2", { staticClass: "box-title" }, [
        _vm._v(_vm._s(_vm.lang("order")))
      ])
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "box-body" }, [
      !_vm.loading
        ? _c("div", { attrs: { slot: "fields" }, slot: "fields" }, [
            _c(
              "div",
              {
                staticClass: "callout",
                staticStyle: {
                  "background-color": "rgb(0, 191, 239)",
                  color: "rgb(249, 249, 249)"
                }
              },
              [
                _c("div", { staticClass: "row" }, [
                  _c("div", { staticClass: "col-md-3" }, [
                    _c("b", [_vm._v(_vm._s(_vm.lang("package")) + " : ")]),
                    _vm._v(" " + _vm._s(_vm.orderData.package.name) + " ")
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "col-md-3" }, [
                    _c("b", [_vm._v(_vm._s(_vm.lang("credit_type")) + " : ")]),
                    _vm._v(" " + _vm._s(_vm.orderData.credit_type) + " ")
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "col-md-3" }, [
                    _c("b", [_vm._v(_vm._s(_vm.lang("credit_left")) + " : ")]),
                    _vm._v(" " + _vm._s(_vm.orderData.credit) + " ")
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "col-md-3" }, [
                    _c("b", [_vm._v(_vm._s(_vm.lang("expiry_date")) + ": ")]),
                    _vm._v(
                      " " +
                        _vm._s(_vm.formattedTime(_vm.orderData.expiry_date)) +
                        " "
                    )
                  ])
                ])
              ]
            ),
            _vm._v(" "),
            _c("div", { staticClass: "row" }, [
              _c("div", { staticClass: "col-md-12" }, [
                _c("div", { staticClass: "invoice_box box-solid" }, [
                  _c("div", { staticClass: "box-body" }, [
                    _c("table", { staticClass: "table table-striped" }, [
                      _c("thead", [
                        _c("tr", [
                          _c("th", [_vm._v(_vm._s(_vm.lang("title")))]),
                          _vm._v(" "),
                          _c("th", [_vm._v(_vm._s(_vm.lang("ticket_no")))]),
                          _vm._v(" "),
                          _c("th", [_vm._v(_vm._s(_vm.lang("created_at")))]),
                          _vm._v(" "),
                          _c("th", [_vm._v(_vm._s(_vm.lang("updated_at")))])
                        ])
                      ]),
                      _vm._v(" "),
                      _c(
                        "tbody",
                        _vm._l(_vm.orderData.order_tickets, function(ticket) {
                          return _c("tr", [
                            _c("td", [
                              _vm._v(_vm._s(ticket.ticket.first_thread.title))
                            ]),
                            _vm._v(" "),
                            _c("td", [
                              _vm._v(_vm._s("#" + ticket.ticket.ticket_number))
                            ]),
                            _vm._v(" "),
                            _c("td", [
                              _vm._v(
                                _vm._s(
                                  _vm.formattedTime(ticket.ticket.created_at)
                                )
                              )
                            ]),
                            _vm._v(" "),
                            _c("td", [
                              _vm._v(
                                _vm._s(
                                  _vm.formattedTime(ticket.ticket.updated_at)
                                )
                              )
                            ])
                          ])
                        })
                      )
                    ])
                  ])
                ])
              ])
            ])
          ])
        : _vm._e()
    ]),
    _vm._v(" "),
    _vm.loading
      ? _c(
          "div",
          { staticClass: "row", attrs: { slot: "fields" }, slot: "fields" },
          [
            _c("loader", {
              attrs: {
                "animation-duration": 4000,
                color: "#1d78ff",
                size: _vm.size
              }
            })
          ],
          1
        )
      : _vm._e()
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-f4e0d852", module.exports)
  }
}

/***/ }),

/***/ 2767:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2768)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2770)
/* template */
var __vue_template__ = __webpack_require__(2774)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-389b5dea"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Billing/Invoices.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-389b5dea", Component.options)
  } else {
    hotAPI.reload("data-v-389b5dea", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2768:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2769);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("55ace4f4", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-389b5dea\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Invoices.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-389b5dea\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Invoices.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2769:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),

/***/ 2770:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_vuex__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_moment__ = __webpack_require__(19);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_moment___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_moment__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_helpers_extraLogics__ = __webpack_require__(4);
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//










/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'pacakges',

	description: 'Pacakges data table component',

	props: {},

	data: function data() {

		return {

			base: window.axios.defaults.baseURL,

			columns: ['name', 'user', 'payment_mode', 'total_amount', 'payable_amount', 'amount_paid', 'due_by', 'status'],

			options: {},

			apiUrl: '',

			selectedData: [],

			showModal: false,

			deleteUrl: '',

			isShowFilter: false,

			hideData: '',

			show: false,

			filterStyle: {}
		};
	},


	computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_1_vuex__["b" /* mapGetters */])(['formattedTime', 'formattedDate'])),

	watch: {},

	beforeMount: function beforeMount() {

		if (window.location.search.substring(1) === 'status=0') {

			this.isShowFilter = !this.isShowFilter;
		} else {

			this.apiUrl = '/bill/package/get-user-invoice?meta=' + true + '&all-users=' + 1;
		}
		var self = this;

		this.options = {

			headings: {

				name: 'Name',

				validity: 'Validity',

				allowed_tickets: 'Incident credit',

				price: 'Price',

				status: 'Status',

				action: 'Action'
			},

			texts: {

				filter: '',

				limit: ''
			},

			sortIcon: {

				base: 'glyphicon',

				up: 'glyphicon-chevron-down',

				down: 'glyphicon-chevron-up'
			},

			templates: {
				name: function name(createElement, row) {

					return createElement('a', {

						attrs: {
							href: self.base + '/bill/package/' + row.id + '/user-invoice',
							target: '_blank'
						}
					}, 'Invoice#' + row.id);
				},

				due_by: function due_by(h, row) {
					return self.formattedTime(row.due_by);
				},
				payment_mode: function payment_mode(h, row) {
					return Object(__WEBPACK_IMPORTED_MODULE_3_helpers_extraLogics__["q" /* lang */])(row.payment_mode);
				},

				status: function status(createElement, row) {

					var span = createElement('span', {
						attrs: {
							'class': row.order.status === 1 ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
						}
					}, row.order.status === 1 ? 'Paid' : 'Unpaid');

					return createElement('a', {}, [span]);
				},

				user: function user(createElement, row) {

					return createElement('a', {
						attrs: {
							href: self.base + '/user/' + row.order.user.id,
							target: '_blank'
						}
					}, row.order.user.full_name);
				}
			},

			sortable: ['name', 'payment_mode', 'total_amount', 'payable_amount', 'amount_paid', 'due_by', 'user', 'status'],

			filterable: ['name', 'validity', 'allowed_tickets', 'price', 'status'],

			pagination: { chunk: 5, nav: 'fixed', edge: true },

			requestAdapter: function requestAdapter(data) {

				return {

					'sort-field': data.orderBy ? data.orderBy : 'id',

					'sort-order': data.ascending ? 'desc' : 'asc',

					'search-query': data.query.trim(),

					'page': data.page,

					'limit': data.limit
				};
			},
			responseAdapter: function responseAdapter(_ref) {
				var data = _ref.data;

				return {

					data: data.data.data,

					count: data.data.total
				};
			}
		};
	},


	methods: {
		packages: function packages(data) {

			this.selectedData = data;
		},
		deletePackage: function deletePackage() {

			this.deleteUrl = 'bill/package/delete?package_ids=' + this.selectedData;

			this.showModal = true;
		},
		onClose: function onClose() {

			this.showModal = false;

			this.$store.dispatch('unsetValidationError');
		},
		toggleFilterView: function toggleFilterView() {

			this.isShowFilter = true;

			this.show = !this.show;

			if (this.show) {

				this.filterStyle = { display: 'block' };
			} else {

				this.filterStyle = { display: 'none' };
			}
		},
		hideFilter: function hideFilter(data) {

			this.isShowFilter = false;

			this.show = false;

			this.hideData = data;
		},
		apiChange: function apiChange(value) {

			var baseUrlForFilter = '/bill/package/get-user-invoice?meta=' + true + '&all-users=' + 1 + '&';

			var params = '';

			if (value.status) {

				var val = value.status === 'unpaid' ? 0 : 1;

				params += 'status=' + val + '&';
			}

			if (value.payable_amount) {

				params += 'payable_amount=' + value.payable_amount + '&';
			}

			if (value.payment_mode) {

				value.payment_mode.forEach(function (element, index) {

					params += 'payment_mode[' + index + ']=' + element.id + '&';
				});
			}

			if (value.users.length > 0) {

				baseUrlForFilter = '/bill/package/get-user-invoice?meta=' + true + '&';

				value.users.forEach(function (element, index) {

					params += 'users[' + index + ']=' + element.id + '&';
				});
			}

			if (value.amount_paid) {

				params += 'amount_paid=' + value.amount_paid + '&';
			}

			if (value.created_date) {

				var create = value.created_date;

				var created_at_start = create[0] !== null ? __WEBPACK_IMPORTED_MODULE_2_moment___default()(create[0]).format('YYYY-MM-DD+HH:mm:ss') : '';

				var created_at_end = create[1] !== null ? __WEBPACK_IMPORTED_MODULE_2_moment___default()(create[1]).format('YYYY-MM-DD+HH:mm:ss') : '';

				params += 'created_at_start=' + created_at_start + '&created_at_end=' + created_at_end + '&';
			}

			if (value.due_date) {

				var due = value.due_date;

				var due_by_start = due[0] !== null ? __WEBPACK_IMPORTED_MODULE_2_moment___default()(due[0]).format('YYYY-MM-DD+HH:mm:ss') : '';

				var due_by_end = due[1] !== null ? __WEBPACK_IMPORTED_MODULE_2_moment___default()(due[1]).format('YYYY-MM-DD+HH:mm:ss') : '';

				params += 'due_by_start=' + due_by_start + '&due_by_end=' + due_by_end + '&';
			}

			if (params[params.length - 1] === '&') {

				params = params.slice(0, -1);
			}

			this.apiUrl = baseUrlForFilter + params;
		}
	},

	components: {

		'data-table': __webpack_require__(17),

		"alert": __webpack_require__(6),

		'delete-modal': __webpack_require__(65),

		'invoices-filter': __webpack_require__(2771)
	}
});

/***/ }),

/***/ 2771:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2772)
/* template */
var __vue_template__ = __webpack_require__(2773)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "app/Bill/views/js/components/Billing/InvoicesFilter.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-c9f377fc", Component.options)
  } else {
    hotAPI.reload("data-v-c9f377fc", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2772:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({

  name: "invoices-filter",

  description: "",

  components: {

    'dynamic-select': __webpack_require__(14),

    'static-select': __webpack_require__(26),

    'number-field': __webpack_require__(32),

    'date-time-field': __webpack_require__(28),

    'loader': __webpack_require__(8)
  },

  props: {

    apiChange: { type: Function },

    hideFilter: { type: Function },

    data: { type: String | Object, default: '' }
  },

  data: function data() {

    return {

      created_date: '',

      due_date: '',

      amount_paid: '',

      payable_amount: '',

      statuses: [{ id: 'paid', name: 'Paid', queryPerm: 'status=1' }, { id: 'unpaid', name: 'Unpaid', queryPerm: 'status=0' }],

      status: '',

      modes: [{ id: 'online', name: 'Online', queryPerm: 'payment_mode[0]=online' }, { id: 'cash', name: 'Cash', queryPerm: 'payment_mode[1]=cash' }, { id: 'bank_transfer', name: 'Bank transfer', queryPerm: 'payment_mode[2]=bank_transfer' }, { id: 'marked_paid_by_agent', name: 'Marked paid by agent', queryPerm: 'payment_mode[3]=marked_paid_by_agent' }],

      payment_mode: [],

      users: [],

      timeOptions: { start: '00:00', step: '00:30', end: '23:30' },

      loading: false,

      filterApplied: false
    };
  },
  beforeMount: function beforeMount() {
    var _this = this;

    if (window.location.search.substring(1) === 'status=0') {

      this.status = 'unpaid';

      this.apiChange(this.$data);
    }

    if (this.data) {

      var self = this;

      var stateData = this.$data;

      Object.keys(this.data).map(function (key) {

        if (stateData.hasOwnProperty(key)) {
          self[key] = _this.data[key];
        }
      });
    }
  },


  methods: {
    resetData: function resetData() {
      var _this2 = this;

      this.filterApplied = false;

      this.created_date = '';

      this.due_date = '';

      this.amount_paid = '';

      this.payable_amount = '';

      this.status = '';

      this.payment_mode = [];

      this.users = [];

      setTimeout(function () {

        _this2.loading = false;
      }, 1000);
    },
    onChange: function onChange(value, name) {

      this[name] = value;
    },
    onCancel: function onCancel() {

      this.hideFilter(this.$data);

      if (this.filterApplied) {

        this.apiChange(this.$data);
      } else {

        this.resetData();
      }
    },
    onApply: function onApply() {

      this.filterApplied = true;

      this.apiChange(this.$data);
    },
    onReset: function onReset() {

      this.loading = true;

      this.resetData();

      this.apiChange(this.$data);
    }
  }
});

/***/ }),

/***/ 2773:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "card card-light" }, [
    _c("div", { staticClass: "card-header" }, [
      _c("h3", { staticClass: "card-title" }, [
        _vm._v(_vm._s(_vm.lang("filter")))
      ])
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "card-body" }, [
      _vm.loading
        ? _c(
            "div",
            {
              staticClass: "row",
              staticStyle: { "margin-top": "80px", "margin-bottom": "94px" }
            },
            [
              _c("h3", { staticClass: "text-center" }, [
                _vm._v("Loading filters...")
              ])
            ]
          )
        : _vm._e(),
      _vm._v(" "),
      !_vm.loading
        ? _c(
            "div",
            { staticClass: "row", attrs: { id: "invoice_filter" } },
            [
              _c("date-time-field", {
                attrs: {
                  label: _vm.lang("created_date"),
                  value: _vm.created_date,
                  type: "datetime",
                  name: "created_date",
                  onChange: _vm.onChange,
                  range: "",
                  required: false,
                  format: "YYYY-MM-DD HH:mm:ss",
                  classname: "col-sm-4",
                  clearable: true,
                  disabled: false,
                  editable: true,
                  currentYearDate: false,
                  "time-picker-options": _vm.timeOptions
                }
              }),
              _vm._v(" "),
              _c("date-time-field", {
                attrs: {
                  label: _vm.lang("due_date"),
                  value: _vm.due_date,
                  type: "datetime",
                  name: "due_date",
                  onChange: _vm.onChange,
                  range: "",
                  required: false,
                  format: "YYYY-MM-DD HH:mm:ss",
                  classname: "col-sm-4",
                  clearable: true,
                  disabled: false,
                  editable: true,
                  currentYearDate: false,
                  "time-picker-options": _vm.timeOptions
                }
              }),
              _vm._v(" "),
              _c("number-field", {
                attrs: {
                  label: _vm.lang("amount_paid"),
                  value: _vm.amount_paid,
                  name: "amount_paid",
                  classname: "col-sm-2",
                  onChange: _vm.onChange,
                  type: "number"
                }
              }),
              _vm._v(" "),
              _c("number-field", {
                attrs: {
                  label: _vm.lang("payable_amount"),
                  value: _vm.payable_amount,
                  name: "payable_amount",
                  classname: "col-sm-2",
                  onChange: _vm.onChange,
                  type: "number"
                }
              })
            ],
            1
          )
        : _vm._e(),
      _vm._v(" "),
      !_vm.loading
        ? _c(
            "div",
            { staticClass: "row" },
            [
              _c("static-select", {
                attrs: {
                  label: _vm.lang("status"),
                  elements: _vm.statuses,
                  name: "status",
                  value: _vm.status,
                  classname: "col-sm-4 invoice_select",
                  onChange: _vm.onChange,
                  required: false
                }
              }),
              _vm._v(" "),
              _c("dynamic-select", {
                attrs: {
                  name: "payment_mode",
                  classname: "col-sm-4",
                  elements: _vm.modes,
                  multiple: true,
                  prePopulate: false,
                  label: _vm.lang("payment_mode"),
                  value: _vm.payment_mode,
                  onChange: _vm.onChange
                }
              }),
              _vm._v(" "),
              _c("dynamic-select", {
                attrs: {
                  name: "users",
                  apiEndpoint: "api/dependency/users?meta=true",
                  classname: "col-sm-4",
                  multiple: true,
                  prePopulate: true,
                  label: _vm.lang("users"),
                  value: _vm.users,
                  onChange: _vm.onChange
                }
              })
            ],
            1
          )
        : _vm._e()
    ]),
    _vm._v(" "),
    !_vm.loading
      ? _c("div", { staticClass: "card-footer" }, [
          _c("span", { staticClass: "single-btn" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-primary round-btn",
                attrs: { id: "apply-btn", type: "button" },
                on: { click: _vm.onApply }
              },
              [
                _c("span", { staticClass: "fas fa-check" }),
                _vm._v("  " + _vm._s(_vm.lang("apply")) + "\n      ")
              ]
            )
          ]),
          _vm._v(" "),
          _c("span", { staticClass: "single-btn" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-primary single-btn round-btn",
                attrs: { id: "apply-btn", type: "button" },
                on: { click: _vm.onReset }
              },
              [
                _c("span", { staticClass: "fas fa-undo" }),
                _vm._v("  " + _vm._s(_vm.lang("reset")) + "\n      ")
              ]
            )
          ]),
          _vm._v(" "),
          _c("span", { staticClass: "single-btn" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-danger single-btn round-btn",
                attrs: { id: "apply-btn", type: "button" },
                on: { click: _vm.onCancel }
              },
              [
                _c("span", { staticClass: "fas fa-times" }),
                _vm._v("  " + _vm._s(_vm.lang("cancel")) + "\n      ")
              ]
            )
          ])
        ])
      : _vm._e()
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-c9f377fc", module.exports)
  }
}

/***/ }),

/***/ 2774:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("alert", { attrs: { componentName: "dataTableModal" } }),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "card card-light" },
        [
          _c("div", { staticClass: "card-header" }, [
            _c(
              "h3",
              { staticClass: "card-title", attrs: { id: "pack-title" } },
              [_vm._v(_vm._s(_vm.lang("invoice_list")))]
            ),
            _vm._v(" "),
            _c("div", { staticClass: "card-tools" }, [
              _c(
                "a",
                {
                  staticClass: "btn btn-tool",
                  attrs: { id: "advance-filter-btn" },
                  on: { click: _vm.toggleFilterView }
                },
                [_c("i", { staticClass: "glyphicon glyphicon-filter" })]
              )
            ])
          ]),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "card-body" },
            [
              _c(
                "div",
                { style: _vm.filterStyle },
                [
                  _vm.isShowFilter
                    ? _c("invoices-filter", {
                        attrs: {
                          id: "filter-box",
                          apiChange: _vm.apiChange,
                          hideFilter: _vm.hideFilter,
                          data: _vm.hideData
                        }
                      })
                    : _vm._e()
                ],
                1
              ),
              _vm._v(" "),
              _vm.apiUrl
                ? _c("data-table", {
                    attrs: {
                      url: _vm.apiUrl,
                      dataColumns: _vm.columns,
                      option: _vm.options,
                      scroll_to: "pack-title",
                      tickets: _vm.packages
                    }
                  })
                : _vm._e()
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "transition",
            { attrs: { name: "modal" } },
            [
              _vm.showModal
                ? _c("delete-modal", {
                    attrs: {
                      onClose: _vm.onClose,
                      showModal: _vm.showModal,
                      deleteUrl: _vm.deleteUrl
                    }
                  })
                : _vm._e()
            ],
            1
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-389b5dea", module.exports)
  }
}

/***/ })

},[2696]);