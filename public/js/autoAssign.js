webpackJsonp([16],{

/***/ 2911:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2912);


/***/ }),

/***/ 2912:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_store__ = __webpack_require__(10);
var bootstrap = __webpack_require__(29);





Vue.component('auto-assign', __webpack_require__(2913));

__WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */].dispatch('deleteUser');

__WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */].dispatch('updateUser');

var app = new Vue({

    el: '#app-auto-assign',

    store: __WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */]
});

/***/ }),

/***/ 2913:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2914)
/* template */
var __vue_template__ = __webpack_require__(2916)
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
Component.options.__file = "app/AutoAssign/views/js/components/AutoAssign.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-528c1177", Component.options)
  } else {
    hotAPI.reload("data-v-528c1177", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2914:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__helpers_validator_autoAssignRules__ = __webpack_require__(2915);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

	name: 'auto-assign',

	data: function data() {

		return {

			status: 0,

			only_login: 0,

			assign_not_accept: 0,

			assign_with_type: 0,

			is_location: 0,

			assign_department_option: "all",

			threshold: '',

			department_list: [],

			radioOptions: [{ name: 'yes', value: 1 }, { name: 'no', value: 0 }],

			deptOptions: [{ name: 'all', value: 'all' }, { name: 'specific', value: 'specific' }],

			loading: true,

			hasDataPopulated: false,

			pageLoad: false
		};
	},
	beforeMount: function beforeMount() {

		this.getValues();
	},


	methods: {
		getValues: function getValues() {
			var _this = this;

			__WEBPACK_IMPORTED_MODULE_1_axios___default.a.get('/api/get-auto-assign').then(function (res) {

				_this.loading = false;

				_this.hasDataPopulated = true;

				_this.updateStatesWithData(res.data.data.autoAssign);
			}).catch(function (err) {

				_this.loading = false;

				_this.hasDataPopulated = true;
			});
		},
		updateStatesWithData: function updateStatesWithData(assignData) {

			var self = this;

			var stateData = this.$data;

			Object.keys(assignData).map(function (key) {

				if (stateData.hasOwnProperty(key)) {

					self[key] = key != 'assign_department_option' && key != 'department_list' ? parseInt(assignData[key]) : assignData[key];
				}
			});

			this.threshold = this.threshold ? this.threshold : '';
		},
		onChange: function onChange(value, name) {

			this[name] = value;

			if (this.assign_department_option == 'all') {

				this.department_list = [];
			}
		},
		isValid: function isValid() {
			var _validateAutoAssignSe = Object(__WEBPACK_IMPORTED_MODULE_2__helpers_validator_autoAssignRules__["a" /* validateAutoAssignSettings */])(this.$data),
			    errors = _validateAutoAssignSe.errors,
			    isValid = _validateAutoAssignSe.isValid;

			return isValid;
		},
		onSubmit: function onSubmit() {
			var _this2 = this;

			if (this.isValid()) {

				this.pageLoad = true;

				var data = {};

				data['status'] = this.status;
				data['only_login'] = this.only_login;
				data['assign_not_accept'] = this.assign_not_accept;
				data['assign_with_type'] = this.assign_with_type;
				data['is_location'] = this.is_location;
				data['threshold'] = this.threshold;
				data['assign_department_option'] = this.assign_department_option;

				if (this.assign_department_option === 'specific') {

					data['department_list'] = this.department_list.map(function (a) {
						return a.id;
					});
				}

				__WEBPACK_IMPORTED_MODULE_1_axios___default.a.post('/api/auto-assign', data).then(function (res) {

					_this2.pageLoad = false;

					Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["b" /* successHandler */])(res, "AutoAssign");

					_this2.getValues();
				}).catch(function (err) {

					Object(__WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__["a" /* errorHandler */])(err, "AutoAssign");

					_this2.pageLoad = false;
				});
			}
		}
	},

	components: {

		'radio-button': __webpack_require__(21),

		'number-field': __webpack_require__(32),

		'dynamic-select': __webpack_require__(14),

		'loader': __webpack_require__(8),

		"alert": __webpack_require__(6),

		'custom-loader': __webpack_require__(9)
	}
});

/***/ }),

/***/ 2915:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = validateAutoAssignSettings;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_easy_validator_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);






function validateAutoAssignSettings(data) {
    var threshold = data.threshold,
        assign_department_option = data.assign_department_option,
        department_list = data.department_list;

    var validatingData = {};

    if (data.assign_department_option === 'specific') {

        validatingData['department_list'] = [data.department_list, 'isRequired'];
    }

    var validator = new __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__["Validator"](__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["q" /* lang */]);

    var _validator$validate = validator.validate(validatingData),
        errors = _validator$validate.errors,
        isValid = _validator$validate.isValid;

    __WEBPACK_IMPORTED_MODULE_0_store__["a" /* store */].dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

    return { errors: errors, isValid: isValid };
};

/***/ }),

/***/ 2916:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("alert", { attrs: { componentName: "AutoAssign" } }),
      _vm._v(" "),
      _c("div", { staticClass: "card card-light" }, [
        _c("div", { staticClass: "card-header" }, [
          _c("h3", { staticClass: "card-title" }, [
            _vm._v(_vm._s(_vm.trans("settings")))
          ])
        ]),
        _vm._v(" "),
        _c(
          "div",
          { staticClass: "card-body" },
          [
            _vm.loading || !_vm.hasDataPopulated
              ? [
                  _c(
                    "div",
                    { staticClass: "row" },
                    [
                      _c("loader", {
                        attrs: { "animation-duration": 4000, size: 60 }
                      })
                    ],
                    1
                  )
                ]
              : _vm._e(),
            _vm._v(" "),
            _vm.hasDataPopulated
              ? [
                  _c(
                    "div",
                    { staticClass: "row" },
                    [
                      _c("radio-button", {
                        attrs: {
                          options: _vm.radioOptions,
                          label: _vm.trans("enable"),
                          name: "status",
                          value: _vm.status,
                          onChange: _vm.onChange,
                          classname: "form-group col-sm-6"
                        }
                      }),
                      _vm._v(" "),
                      _c("radio-button", {
                        attrs: {
                          options: _vm.radioOptions,
                          label: _vm.trans("only-login-agents"),
                          name: "only_login",
                          value: _vm.only_login,
                          onChange: _vm.onChange,
                          classname: "form-group col-sm-6"
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
                      _c("radio-button", {
                        attrs: {
                          options: _vm.radioOptions,
                          label: _vm.trans(
                            "assign-ticket-even-agent-in-non-acceptable-mode"
                          ),
                          name: "assign_not_accept",
                          value: _vm.assign_not_accept,
                          onChange: _vm.onChange,
                          classname: "form-group col-sm-6"
                        }
                      }),
                      _vm._v(" "),
                      _c("radio-button", {
                        attrs: {
                          options: _vm.radioOptions,
                          label: _vm.trans(
                            "assign-ticket-with-agent-having-type"
                          ),
                          name: "assign_with_type",
                          value: _vm.assign_with_type,
                          onChange: _vm.onChange,
                          classname: "form-group col-sm-6"
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
                      _c("radio-button", {
                        attrs: {
                          options: _vm.radioOptions,
                          label: _vm.trans(
                            "assign-ticket-with-agent-having-location"
                          ),
                          name: "is_location",
                          value: _vm.is_location,
                          onChange: _vm.onChange,
                          classname: "form-group col-sm-6"
                        }
                      }),
                      _vm._v(" "),
                      _c("radio-button", {
                        attrs: {
                          options: _vm.deptOptions,
                          label: _vm.trans("auto-assign-enabled-departments"),
                          name: "assign_department_option",
                          value: _vm.assign_department_option,
                          onChange: _vm.onChange,
                          classname: "form-group col-sm-6"
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
                      _c("number-field", {
                        attrs: {
                          label: _vm.trans(
                            "maximum-number-of-ticket-can-assign-to-agent"
                          ),
                          value: _vm.threshold,
                          name: "threshold",
                          onChange: _vm.onChange,
                          classname: "col-sm-6",
                          type: "number",
                          required: false,
                          placeholder: "n"
                        }
                      }),
                      _vm._v(" "),
                      _vm.assign_department_option === "specific"
                        ? _c("dynamic-select", {
                            attrs: {
                              label: _vm.trans("select-deparment"),
                              multiple: true,
                              name: "department_list",
                              classname: "col-sm-6",
                              apiEndpoint: "/api/dependency/departments",
                              value: _vm.department_list,
                              onChange: _vm.onChange,
                              strlength: 30,
                              required: true
                            }
                          })
                        : _vm._e()
                    ],
                    1
                  )
                ]
              : _vm._e()
          ],
          2
        ),
        _vm._v(" "),
        _c("div", { staticClass: "card-footer" }, [
          _c(
            "button",
            { staticClass: "btn btn-primary", on: { click: _vm.onSubmit } },
            [
              _c("i", { staticClass: "fas fa-sync" }),
              _vm._v(" " + _vm._s(_vm.trans("update")) + "\n\t\t\t")
            ]
          )
        ])
      ]),
      _vm._v(" "),
      _vm.pageLoad
        ? _c(
            "div",
            { staticClass: "row" },
            [_c("custom-loader", { attrs: { duration: 4000 } })],
            1
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
    require("vue-hot-reload-api")      .rerender("data-v-528c1177", module.exports)
  }
}

/***/ })

},[2911]);