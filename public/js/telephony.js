webpackJsonp([7],{

/***/ 234:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = Timer;


/**
 * A constructor function for the timer service
 * @param { Function } callback callback function to execute after some `delay`
 * @param { Number } delay How many second to delay the function exection
 * @param { Boolean } canUserStopTimer can user able to stop the timer or not?
 * @param { Boolean } canPauseResume can user pause/resume the timer?
 * @param { Boolean } showTimer falg to show/hide timer
 */

function Timer(callback, delay, canUserStopTimer) {
  var canPauseResume = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;
  var showTimer = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : true;

  this.timerId;
  this.remaining = delay;
  this.canUserStopTimer = canUserStopTimer;
  this.canPauseResume = canPauseResume;
  this.showTimer = showTimer;

  // Pause the timer service
  this.pause = function () {
    // Clear timer instance if paused
    clearTimeout(this.timerId);
  };

  // Returns the remaining time left
  this.getRemaining = function () {
    return this.remaining;
  };

  // Resume the timer service
  this.resume = function () {
    var _this = this;

    clearTimeout(this.timerId);
    this.timerId = setInterval(function () {
      _this.remaining -= 1;
      if (_this.remaining <= 0) {
        clearTimeout(_this.timerId);
        callback();
      }
    }, 1000);
  };

  // Start the timer as soon as the Timer instance is created 
  this.resume();
};

/***/ }),

/***/ 2847:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2848);


/***/ }),

/***/ 2848:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__store_callStore__ = __webpack_require__(2849);

var bootstrap = __webpack_require__(29);







__WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */].registerModule('callStore', __WEBPACK_IMPORTED_MODULE_2__store_callStore__["a" /* default */]);

var app = new Vue({
    el: '#telephony-settings',
    store: __WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */],
    components: {
        'telephony-settings': __webpack_require__(2850),
        'telephone-alert': __webpack_require__(2866)
    }
});

/***/ }),

/***/ 2849:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

/**
 * This store is used to control(for now) telephonic popup behaviour
 * May be used as a STACK for handling data
 */

var state = {
	list: []
};

var getters = {
	getItems: function getItems(state) {
		return state.list;
	}
};

var mutations = {
	addUpdateElement: function addUpdateElement(state, data) {
		var isNewItem = true;

		for (var i = 0; i < state.list.length; i++) {
			if (state.list[i].id === data.id) {
				clearTimeout(state.list[i].timer.timerId);
				isNewItem = false;
				state.list[i].data = data.data;
				state.list[i].status = data.status;
				state.list[i].timer = data.timer;
				break;
			}
		}

		if (isNewItem) {
			state.list.push(data);
		}

		if (state.list.length > 3) {
			state.list.shift();
		}
	},
	removeElement: function removeElement(state, item) {
		clearTimeout(item.timer);
		state.list = state.list.filter(function (v) {
			return v.id !== item.id;
		});
	},
	clearAll: function clearAll(state) {
		state.list = [];
	}
};

var actions = {
	addUpdateElement: function addUpdateElement(_ref, data) {
		var commit = _ref.commit;

		commit('addUpdateElement', data);
	},
	removeElement: function removeElement(_ref2, item) {
		var commit = _ref2.commit;

		commit('removeElement', item);
	},
	clearAll: function clearAll(_ref3) {
		var commit = _ref3.commit;

		commit('clearAll');
	}
};

/* harmony default export */ __webpack_exports__["a"] = ({ state: state, getters: getters, mutations: mutations, actions: actions });

/***/ }),

/***/ 2850:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2851)
/* template */
var __vue_template__ = __webpack_require__(2865)
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
Component.options.__file = "app/Plugins/Telephony/views/js/components/TelephonySettings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0f1eb7f5", Component.options)
  } else {
    hotAPI.reload("data-v-0f1eb7f5", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2851:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vue__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_vue__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//





__WEBPACK_IMPORTED_MODULE_3_vue___default.a.component('telephony-table-actions', __webpack_require__(2852));
/* harmony default export */ __webpack_exports__["default"] = ({
	data: function data() {
		return {

			showModal: false,

			data: { id: 0, name: '' },

			/**
   * columns required for datatable
   * @type {Array}
   */
			columns: ['name', 'action'],

			options: {
				headings: { name: 'Name', action: 'Action' },
				templates: {
					action: 'telephony-table-actions'
				},
				filterable: false,
				sortable: [],
				pagination: { chunk: 5, nav: 'scroll' },
				requestAdapter: function requestAdapter(data) {
					return {
						page: data.page,
						limit: data.limit
					};
				},
				responseAdapter: function responseAdapter(_ref) {
					var data = _ref.data;

					return {
						data: data.data.data.map(function (data) {
							data.edit_modal = 'api/get-providers-list';
							return data;
						}),
						count: data.data.total
					};
				}
			},

			/**
    * api url for ajax calls
   	 * @type {String}
    */
			apiUrl: '/telephony/api/get-providers-list'
		};
	},


	components: {
		"alert": __webpack_require__(6),
		"custom-loader": __webpack_require__(9),
		'data-table': __webpack_require__(17),
		"tool-tip": __webpack_require__(25)
	}
});

/***/ }),

/***/ 2852:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2853)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2855)
/* template */
var __vue_template__ = __webpack_require__(2864)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-855079fe"
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
Component.options.__file = "app/Plugins/Telephony/views/js/components/MiniComponents/TelephonyActions.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-855079fe", Component.options)
  } else {
    hotAPI.reload("data-v-855079fe", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2853:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2854);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("8624ab30", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-855079fe\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TelephonyActions.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-855079fe\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TelephonyActions.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2854:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.block[data-v-855079fe]{\n\tdisplay: block !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2855:
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






/* harmony default export */ __webpack_exports__["default"] = ({

	name: "data-table-actions",

	description: "Contains edit, delete and view buttons as group which can be used as a component as whole. It is built basically for displaying edit, delete and view button in a datable.",

	props: {

		data: { type: Object, required: true }
	},

	data: function data() {

		return {

			showSettingsModal: false,

			showEditModal: false
		};
	},


	methods: {
		onClose: function onClose() {

			this.showSettingsModal = false;

			this.showEditModal = false;

			this.$store.dispatch('unsetValidationError');
		}
	},

	components: {

		'telephony-settings-modal': __webpack_require__(2856),

		'telephony-edit-modal': __webpack_require__(2861)
	}
});

/***/ }),

/***/ 2856:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2857)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2859)
/* template */
var __vue_template__ = __webpack_require__(2860)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-662956ce"
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
Component.options.__file = "app/Plugins/Telephony/views/js/components/MiniComponents/TelephonySettingsModal.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-662956ce", Component.options)
  } else {
    hotAPI.reload("data-v-662956ce", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2857:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2858);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("6839f413", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-662956ce\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TelephonySettingsModal.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-662956ce\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TelephonySettingsModal.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2858:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.telephony_btn[data-v-662956ce] {\n\tborder-top-left-radius: 0;\n   border-bottom-left-radius: 0;\n   padding-bottom: 7px;\n}\n", ""]);

// exports


/***/ }),

/***/ 2859:
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


/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'telephony-settings-modal',

	description: 'Telephony settings modal component',

	props: {

		showModal: { type: Boolean, default: false },

		onClose: { type: Function },

		data: { type: Object, default: function _default() {} }

	},

	data: function data() {

		return {

			department: '',

			helptopic: '',

			containerStyle: { width: '800px' },

			loading: false,

			dept_url: this.data.base_url,

			topic_url: this.data.base_url
		};
	},


	methods: {
		onChange: function onChange(value, name) {

			this[name] = value;

			if (name === 'department') {

				if (value) {

					this.dept_url = this.data.base_url + '/' + value.id + '/department';
				} else {

					this.dept_url = this.data.base_url;
				}
			}

			if (name === 'helptopic') {

				if (value) {

					this.topic_url = this.data.base_url + '/' + value.id + '/helptopic';
				} else {

					this.topic_url = this.data.base_url;
				}
			}
		},
		copyDeptUrl: function copyDeptUrl() {

			this.$refs.dept_text.select();

			document.execCommand('copy');
		},
		copyTopicUrl: function copyTopicUrl() {

			this.$refs.topic_text.select();

			document.execCommand('copy');
		}
	},

	components: {

		'modal': __webpack_require__(13),

		'loader': __webpack_require__(8),

		'text-field': __webpack_require__(11),

		'dynamic-select': __webpack_require__(14)
	}
});

/***/ }),

/***/ 2860:
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
              _vm._v(_vm._s(_vm.lang("settings")))
            ])
          ]),
          _vm._v(" "),
          !_vm.loading
            ? _c("div", { attrs: { slot: "fields" }, slot: "fields" }, [
                _c(
                  "div",
                  { staticClass: "row" },
                  [
                    _c("dynamic-select", {
                      attrs: {
                        label: _vm.lang("department"),
                        multiple: false,
                        name: "department",
                        prePopulate: true,
                        classname: "col-sm-6",
                        apiEndpoint: "/api/dependency/departments",
                        value: _vm.department,
                        onChange: _vm.onChange,
                        clearable: _vm.department ? true : false
                      }
                    }),
                    _vm._v(" "),
                    _c("dynamic-select", {
                      attrs: {
                        label: _vm.lang("helptopics"),
                        multiple: false,
                        name: "helptopic",
                        prePopulate: true,
                        classname: "col-sm-6",
                        apiEndpoint: "/api/dependency/help-topics",
                        value: _vm.helptopic,
                        onChange: _vm.onChange,
                        clearable: _vm.helptopic ? true : false
                      }
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c("div", { staticClass: "row" }, [
                  _c("div", { staticClass: "col-sm-12" }, [
                    _c("label", [_vm._v("Department Url")]),
                    _vm._v(" "),
                    _c("div", { staticClass: "input-group" }, [
                      _c("input", {
                        ref: "dept_text",
                        staticClass: "form-control",
                        attrs: { type: "text", readonly: "" },
                        domProps: { value: _vm.dept_url }
                      }),
                      _vm._v(" "),
                      _c("div", { staticClass: "input-group-btn" }, [
                        _c(
                          "button",
                          {
                            directives: [
                              {
                                name: "tooltip",
                                rawName: "v-tooltip",
                                value: _vm.lang("copy"),
                                expression: "lang('copy')"
                              }
                            ],
                            staticClass: "btn btn-default telephony_btn",
                            on: { click: _vm.copyDeptUrl }
                          },
                          [_c("i", { staticClass: "fas fa-copy" })]
                        )
                      ])
                    ])
                  ])
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "row" }, [
                  _c("div", { staticClass: "col-sm-12" }, [
                    _c("label", [_vm._v("Helptopic Url")]),
                    _vm._v(" "),
                    _c("div", { staticClass: "input-group" }, [
                      _c("input", {
                        ref: "topic_text",
                        staticClass: "form-control",
                        attrs: { type: "text", readonly: "" },
                        domProps: { value: _vm.topic_url }
                      }),
                      _vm._v(" "),
                      _c("div", { staticClass: "input-group-btn" }, [
                        _c(
                          "button",
                          {
                            directives: [
                              {
                                name: "tooltip",
                                rawName: "v-tooltip",
                                value: _vm.lang("copy"),
                                expression: "lang('copy')"
                              }
                            ],
                            staticClass: "btn btn-default telephony_btn",
                            on: { click: _vm.copyTopicUrl }
                          },
                          [_c("i", { staticClass: "fas fa-copy" })]
                        )
                      ])
                    ])
                  ])
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
    require("vue-hot-reload-api")      .rerender("data-v-662956ce", module.exports)
  }
}

/***/ }),

/***/ 2861:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2862)
/* template */
var __vue_template__ = __webpack_require__(2863)
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
Component.options.__file = "app/Plugins/Telephony/views/js/components/MiniComponents/TelephonyEditModal.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-d5a7a272", Component.options)
  } else {
    hotAPI.reload("data-v-d5a7a272", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2862:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

	name: 'telephony-settings-modal',

	description: 'Telephony settings modal component',

	props: {

		showModal: { type: Boolean, default: false },

		onClose: { type: Function },

		data: { type: Object, default: function _default() {} }
	},

	data: function data() {

		return {
			app_id: '',

			token: '',

			log_miss_call: '',

			iso: '',

			containerStyle: { width: '1000px' },

			loading: false,

			isDisabled: false,

			radioOptions: [{ name: 'yes', value: 1 }, { name: 'no', value: 0 }],

			conversion_waiting_time: 0
		};
	},
	beforeMount: function beforeMount() {

		this.getValues();
	},


	methods: {
		getValues: function getValues() {
			var _this = this;

			this.loading = true;

			this.isDisabled = true;

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('/telephony/api/get-provider-details/' + this.data.short).then(function (res) {

				_this.token = Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["i" /* findObjectByKey */])(res.data.data, 'key', 'token').value;

				_this.log_miss_call = Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["i" /* findObjectByKey */])(res.data.data, 'key', 'log_miss_call').value;

				_this.app_id = Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["i" /* findObjectByKey */])(res.data.data, 'key', 'app_id').value;

				_this.iso = Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["i" /* findObjectByKey */])(res.data.data, 'key', 'iso').value;

				_this.fullName = Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["i" /* findObjectByKey */])(res.data.data, 'key', 'name').value;

				_this.shortName = Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["i" /* findObjectByKey */])(res.data.data, 'key', 'short').value;

				_this.conversion_waiting_time = Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["i" /* findObjectByKey */])(res.data.data, 'key', 'conversion_waiting_time').value;

				_this.loading = false;

				_this.isDisabled = false;
			}).catch(function (error) {

				_this.loading = false;

				_this.isDisabled = false;

				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(error, 'telephony-edit');
			});
		},
		onChange: function onChange(value, name) {

			this[name] = value;
		},
		onSubmit: function onSubmit() {
			var _this2 = this;

			this.loading = true;

			this.isDisabled = true;

			if (!this.iso) {
				this.loading = false;
				this.isDisabled = false;
				this.$store.dispatch('setValidationError', { 'iso': '' });
				return false;
			}

			var data = {
				"app_id": this.app_id,
				"token": this.token,
				"log_miss_call": this.log_miss_call,
				"iso": this.iso.iso,
				"conversion_waiting_time": this.conversion_waiting_time
			};

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.post('/telephony/api/update-provider-details/' + this.data.short, data).then(function (res) {

				_this2.loading = false;

				_this2.isDisabled = false;

				_this2.onClose();

				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'dataTableModal');
			}).catch(function (error) {

				_this2.loading = false;

				_this2.isDisabled = false;

				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(error, 'telephony-edit');
			});
		}
	},

	components: {

		'modal': __webpack_require__(13),

		'loader': __webpack_require__(8),

		'text-field': __webpack_require__(11),

		'radio-button': __webpack_require__(21),

		'dynamic-select': __webpack_require__(14),

		'alert': __webpack_require__(6)
	}
});

/***/ }),

/***/ 2863:
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
              _vm._v(_vm._s(_vm.lang("edit")) + " " + _vm._s(this.data.name))
            ])
          ]),
          _vm._v(" "),
          !_vm.loading
            ? _c("div", { attrs: { slot: "fields" }, slot: "fields" }, [
                _c(
                  "div",
                  { attrs: { slot: "alert" }, slot: "alert" },
                  [_c("alert", { attrs: { componentName: "telephony-edit" } })],
                  1
                ),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "row" },
                  [
                    _c("text-field", {
                      attrs: {
                        label: _vm.lang("app-id"),
                        value: _vm.app_id,
                        type: "text",
                        name: "app_id",
                        onChange: _vm.onChange,
                        classname: "col-sm-6",
                        hint: _vm.lang("telephony_app_id_hint")
                      }
                    }),
                    _vm._v(" "),
                    _c("text-field", {
                      attrs: {
                        label: _vm.lang("api_token"),
                        value: _vm.token,
                        type: "text",
                        name: "token",
                        onChange: _vm.onChange,
                        classname: "col-sm-6",
                        hint: _vm.lang("telephony_app_token_hint")
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
                        label: _vm.lang("conversion_waiting_time"),
                        value: _vm.conversion_waiting_time,
                        type: "text",
                        name: "conversion_waiting_time",
                        onChange: _vm.onChange,
                        classname: "col-sm-6",
                        hint: _vm.lang("conversion_waiting_time_hint")
                      }
                    }),
                    _vm._v(" "),
                    _c("dynamic-select", {
                      attrs: {
                        label: _vm.lang("select_default_region"),
                        multiple: false,
                        name: "iso",
                        required: true,
                        prePopulate: true,
                        classname: "col-sm-6",
                        apiEndpoint: "/telephony/api/get-regions-list",
                        value: _vm.iso,
                        onChange: _vm.onChange,
                        clearable: _vm.iso ? true : false,
                        hint: _vm.lang("default_region")
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
                        label: _vm.lang("log_miss_call"),
                        name: "log_miss_call",
                        value: _vm.log_miss_call,
                        onChange: _vm.onChange,
                        classname: "form-group col-sm-6",
                        hint: _vm.lang("telephony_log_missed_call_hint")
                      }
                    })
                  ],
                  1
                )
              ])
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
                _c("i", { staticClass: "fas fa-save" }),
                _vm._v(" " + _vm._s(_vm.lang("save")) + "\n\t\t")
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
                      size: 60
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
    require("vue-hot-reload-api")      .rerender("data-v-d5a7a272", module.exports)
  }
}

/***/ }),

/***/ 2864:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "block" },
    [
      _c(
        "a",
        {
          directives: [
            {
              name: "tooltip",
              rawName: "v-tooltip",
              value: _vm.lang("edit"),
              expression: "lang('edit')"
            }
          ],
          staticClass: "btn btn-primary btn-sm",
          attrs: { href: "javascript:;" },
          on: {
            click: function($event) {
              _vm.showEditModal = true
            }
          }
        },
        [_c("i", { staticClass: "fas fa-edit" })]
      ),
      _vm._v(" "),
      _c(
        "a",
        {
          directives: [
            {
              name: "tooltip",
              rawName: "v-tooltip",
              value: _vm.lang("get_webhook_url"),
              expression: "lang('get_webhook_url')"
            }
          ],
          staticClass: "btn btn-primary btn-sm",
          attrs: { id: "settings-modal-button", href: "javascript:;" },
          on: {
            click: function($event) {
              _vm.showSettingsModal = true
            }
          }
        },
        [_c("i", { staticClass: "fas fa-link" })]
      ),
      _vm._v(" "),
      _c(
        "transition",
        { attrs: { name: "modal" } },
        [
          _vm.showSettingsModal
            ? _c("telephony-settings-modal", {
                attrs: {
                  onClose: _vm.onClose,
                  showModal: _vm.showSettingsModal,
                  data: _vm.data
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
          _vm.showEditModal
            ? _c("telephony-edit-modal", {
                attrs: {
                  onClose: _vm.onClose,
                  showModal: _vm.showEditModal,
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
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-855079fe", module.exports)
  }
}

/***/ }),

/***/ 2865:
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
      _c("div", { staticClass: "card card-light" }, [
        _c(
          "div",
          { staticClass: "card-header" },
          [
            _c("h3", { staticClass: "card-title" }, [
              _vm._v(_vm._s(_vm.lang("telephony_providers")))
            ]),
            _vm._v(" "),
            _c("tool-tip", {
              attrs: {
                message: _vm.lang("telephony_providers_attributes_description"),
                size: "large"
              }
            })
          ],
          1
        ),
        _vm._v(" "),
        _c(
          "div",
          { staticClass: "card-body" },
          [
            _c("data-table", {
              attrs: {
                url: _vm.apiUrl,
                dataColumns: _vm.columns,
                option: _vm.options
              }
            })
          ],
          1
        )
      ])
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
    require("vue-hot-reload-api")      .rerender("data-v-0f1eb7f5", module.exports)
  }
}

/***/ }),

/***/ 2866:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2867)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2869)
/* template */
var __vue_template__ = __webpack_require__(2880)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-e8b22482"
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
Component.options.__file = "app/Plugins/Telephony/views/js/components/CallAlert/TelephoneAlert.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-e8b22482", Component.options)
  } else {
    hotAPI.reload("data-v-e8b22482", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2867:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2868);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("0c8a8b19", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-e8b22482\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TelephoneAlert.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-e8b22482\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TelephoneAlert.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2868:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n#telephone-alert-box__position[data-v-e8b22482] {\n  position: fixed;\n  bottom: 0;\n  right: 2px;\n  z-index: 9999;\n}\n#telephone-alert-box[data-v-e8b22482] {\n  margin-bottom: 0.5rem;\n}\n.slide-fade-enter-active[data-v-e8b22482] {\n  -webkit-transition: all .10s ease;\n  transition: all .10s ease;\n}\n.slide-fade-leave-active[data-v-e8b22482] {\n  -webkit-transition: all .5s cubic-bezier(1.0, 0.5, 0.8, 1.0);\n  transition: all .5s cubic-bezier(1.0, 0.5, 0.8, 1.0);\n}\n.slide-fade-enter[data-v-e8b22482], .slide-fade-leave-to[data-v-e8b22482] {\n  -webkit-transform: translateX(100%);\n          transform: translateX(100%);\n}\n", ""]);

// exports


/***/ }),

/***/ 2869:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vuex__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(234);
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





/* harmony default export */ __webpack_exports__["default"] = ({

  name: 'telephone-alert',

  data: function data() {
    return {};
  },

  props: {
    /**
     * the id of user subscribing to the user-notifications channel
     * @type {Number}
     */
    user: { type: Number, required: true }
  },

  mounted: function mounted() {
    var _this = this;

    window.Echo.private('user-notifications.' + this.user).listen('.call-started', function (response) {
      console.debug('call-started', response);
      _this.addUpdateItem(response, 'started');
    }).listen('.call-ended', function (response) {
      console.debug('call-ended', response);
      _this.addUpdateItem(response, 'ended');
    });
  },


  computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_0_vuex__["b" /* mapGetters */])(['getItems'])),

  methods: {
    onMouseEnter: function onMouseEnter(item) {
      item.timer.canPauseResume && item.timer.canUserStopTimer && item.timer.pause();
    },
    onMouseLeave: function onMouseLeave(item) {
      item.timer.canPauseResume && item.timer.canUserStopTimer && item.timer.resume();
    },
    addUpdateItem: function addUpdateItem(response, status) {
      var _this2 = this;

      if (this.isDuplicate(response.call_id, status)) return;

      var item = {
        id: response.call_id,
        data: response,
        status: status
      };

      item.timer = new __WEBPACK_IMPORTED_MODULE_1__utils__["a" /* Timer */](function () {
        return _this2.distoryItem(item);
      }, 15, true);

      this.$store.dispatch('addUpdateElement', item);
    },
    isDuplicate: function isDuplicate(id, status) {
      var item = this.getItems.find(function (v) {
        return v.id === id;
      });

      if (typeof item === 'undefined') return false;

      if (item.status === status) return true;

      return false;
    },
    distoryItem: function distoryItem(item) {
      this.$store.dispatch('removeElement', item);
    }
  },

  beforeDestroy: function beforeDestroy() {
    this.$store.dispatch('clearAll');
  },


  components: {
    'call-popup': __webpack_require__(2870)
  }

});

/***/ }),

/***/ 2870:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2871)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2873)
/* template */
var __vue_template__ = __webpack_require__(2879)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-2aa7b892"
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
Component.options.__file = "app/Plugins/Telephony/views/js/components/CallAlert/CallPopup.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2aa7b892", Component.options)
  } else {
    hotAPI.reload("data-v-2aa7b892", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2871:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2872);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("68f7e478", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2aa7b892\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./CallPopup.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2aa7b892\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./CallPopup.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2872:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.telephone-alert-box[data-v-2aa7b892] {\n  min-width: 400px;\n  font-size: 16px;\n  border-radius: 2px;\n  background-color: #222d32;\n  color: #b8c7ce;\n  -webkit-box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);\n          box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);\n  -webkit-transition: -webkit-box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);\n  transition: -webkit-box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);\n  transition: box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);\n  transition: box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1), -webkit-box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);\n}\n.telephone-alert-box-header[data-v-2aa7b892] {\n  border-bottom: 1px solid #393939;\n  padding: 1rem 1rem;\n}\n.telephone-alert-box-body[data-v-2aa7b892] {\n  padding: 0.7rem 0.7rem;\n}\n.caller-info[data-v-2aa7b892] {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -ms-flex-wrap: nowrap;\n      flex-wrap: nowrap;\n}\n.caller-profile-image-avatar[data-v-2aa7b892] {\n  vertical-align: middle;\n  width: 50px;\n  height: 50px;\n  border-radius: 100%;\n  border: 3px solid #CBCBDA;\n  padding: 3px;\n}\n.user-name-and-email[data-v-2aa7b892] {\n  padding-left: 0.7rem;\n}\n.unknown-user[data-v-2aa7b892] {\n  line-height: 50px;\n  padding-left: 0.7rem;\n}\n#telephone-alert-internal-note[data-v-2aa7b892] {\n  font-size: 14px;\n}\n.pointer-cursor[data-v-2aa7b892] {\n  cursor: pointer;\n}\n.padding-top-7[data-v-2aa7b892] {\n  padding-top: 0.7rem;\n}\n.ticket-unlink-btn[data-v-2aa7b892] {\n  padding-left: 0.3rem;\n}\n#call-pop-countdown[data-v-2aa7b892] {\n  float: right;\n  color: red;\n}\n.recent-ticket-list-btn[data-v-2aa7b892] {\n  position: fixed;\n  right: 0.7rem;\n}\n.spinner-grow-small[data-v-2aa7b892] {\n  width: .5rem;\n  height: .5rem;\n}\n", ""]);

// exports


/***/ }),

/***/ 2873:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__utils__ = __webpack_require__(234);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

  name: 'call-popup',

  props: {
    callData: { type: Object, required: true }
  },

  data: function data() {
    return {
      userNotificationData: null,
      internalNote: '',
      linkedTicketObj: null,
      loading: false,
      showUnlinkBtn: false,
      isUserActive: false
    };
  },

  beforeMount: function beforeMount() {
    this.userNotificationData = this.callData;
    this.linkedTicketObj = this.callData.data.user.linked_ticket;
  },


  watch: {
    callData: {
      handler: function handler(newValue, oldValue) {
        this.userNotificationData = newValue;
        var LINKED_TICKET_OBJ = newValue.data.user.linked_ticket;
        if (LINKED_TICKET_OBJ) {
          this.linkedTicketObj = LINKED_TICKET_OBJ;
        }
      },
      deep: true
    }
  },

  methods: {
    onStopTimerClick: function onStopTimerClick() {
      var _this = this;

      this.isUserActive = true;
      clearTimeout(this.userNotificationData.timer.timerId);
      if (this.userNotificationData.status === 'ended') {
        this.userNotificationData.timer = new __WEBPACK_IMPORTED_MODULE_0__utils__["a" /* Timer */](function () {
          return _this.onSubmit();
        }, this.userNotificationData.data.conversion_waiting_time, false);
      } else {
        this.userNotificationData.timer.showTimer = false;
      }
      this.userNotificationData.timer.canPauseResume = false;
    },
    unLinkTicket: function unLinkTicket() {
      this.linkedTicketObj = this.callData.data.user.linked_ticket;
      this.showUnlinkBtn = false;
    },
    onTicketClick: function onTicketClick(ticket) {
      if (this.callData.data.user.linked_ticket) {
        return;
      }
      this.linkedTicketObj = ticket;
      this.showUnlinkBtn = true;
    },
    onSubmit: function onSubmit() {
      var _this2 = this;

      clearTimeout(this.userNotificationData.timer.timerId);

      if (!this.userNotificationData.data.allow_ticket_conversion) {
        this.$store.dispatch('removeElement', this.userNotificationData);
        return;
      };

      this.loading = true;
      var params = {
        link_ticket: this.linkedTicketObj ? this.linkedTicketObj.id : undefined,
        notes: this.internalNote ? this.internalNote : undefined
      };
      axios.post('telephony/api/convert-call-to-ticket/' + this.userNotificationData.id, params).then(function (response) {
        Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(response, 'root-alert-container');
      }).catch(function (error) {
        Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(error, 'root-alert-container');
      }).finally(function () {
        _this2.loading = false;
        _this2.$store.dispatch('removeElement', _this2.userNotificationData);
      });
    }
  },

  computed: {
    getUserProfileUrl: function getUserProfileUrl() {
      return this.userNotificationData.data.user.id ? this.basePath() + '/user/' + this.userNotificationData.data.user.id : '';
    },
    getUserProfiePic: function getUserProfiePic() {
      return this.userNotificationData.data.user.profile_pic || '';
    }
  },

  components: {
    'faveo-image-element': __webpack_require__(22),
    'recent-ticket-list': __webpack_require__(2874)
  }

});

/***/ }),

/***/ 2874:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2875)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2877)
/* template */
var __vue_template__ = __webpack_require__(2878)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-ae59e156"
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
Component.options.__file = "app/Plugins/Telephony/views/js/components/CallAlert/RecentTickets.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-ae59e156", Component.options)
  } else {
    hotAPI.reload("data-v-ae59e156", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2875:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2876);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("7812c653", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ae59e156\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./RecentTickets.vue", function() {
     var newContent = require("!!../../../../../../../node_modules/css-loader/index.js!../../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ae59e156\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./RecentTickets.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2876:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.rt_dropdown-content[data-v-ae59e156] {\n    display: none;\n    position: absolute;\n    right: 0;\n    bottom: 0;\n    max-width: 450px;\n    max-height: 60vh;\n    min-width: 350px;\n    overflow-y: auto;\n    background-color: #FFFFFF;\n    -webkit-box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);\n            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);\n    z-index: 1;\n}\n\n/* Links inside the dropdown */\n.rt_dropdown-content .ticket-details-element[data-v-ae59e156]  {\n    color: #333;\n    text-decoration: none;\n    display: block;\n    border-bottom: 1px solid #dedede;\n}\n.ticket-details-element[data-v-ae59e156] {\n    cursor: pointer;\n}\n.ticket-details-element-table[data-v-ae59e156] {\n    padding-right: 1rem;\n}\n.rt-ticket-number[data-v-ae59e156] {\n    color: #b8c7ce;\n}\n.rt-ticket-number[data-v-ae59e156]:hover {\n    color: #3c8dbc;\n}\n.rt_show-list[data-v-ae59e156] {\n    display: block;\n}\n.link-ticket-btn[data-v-ae59e156] {\n    cursor: pointer;\n}\n", ""]);

// exports


/***/ }),

/***/ 2877:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vuex__ = __webpack_require__(7);
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



/* harmony default export */ __webpack_exports__["default"] = ({

    props: {
        ticketList: { type: Array, default: function _default() {
                return [];
            } },
        onTicketClick: { type: Function, required: true }
    },

    data: function data() {
        return {};
    },

    mounted: function mounted() {
        // Will close the dropdown if clicked outside
        window.onclick = function (event) {
            if (!event.target.matches('.link-ticket-btn')) {
                var dropdowns = document.getElementsByClassName("rt_dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('rt_show-list')) {
                        openDropdown.classList.remove('rt_show-list');
                    }
                }
            }
        };
    },


    computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_0_vuex__["b" /* mapGetters */])(['formattedTime'])),

    methods: {
        openTicketList: function openTicketList() {
            document.getElementById("ticket-list-dropdown").classList.toggle("rt_show-list");
        }
    }

});

/***/ }),

/***/ 2878:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.ticketList && _vm.ticketList.length > 0
    ? _c("div", { staticClass: "link-ticket dropdown" }, [
        _c("i", {
          staticClass: "fa fa-ticket-alt fa-ticket link-ticket-btn",
          attrs: {
            id: "link-ticket-btn",
            "aria-hidden": "true",
            title: _vm.lang("recent_tickets")
          },
          on: {
            click: function($event) {
              _vm.openTicketList()
            }
          }
        }),
        _vm._v(" "),
        _c(
          "div",
          {
            staticClass: "rt_dropdown-content",
            attrs: { id: "ticket-list-dropdown" }
          },
          _vm._l(_vm.ticketList, function(ticket) {
            return _c(
              "div",
              {
                key: ticket.id,
                staticClass: "ticket-details-element",
                on: {
                  click: function($event) {
                    _vm.onTicketClick(ticket)
                  }
                }
              },
              [
                _c("div", { staticClass: "ticket-details-element-table" }, [
                  _c("table", [
                    _c("tr", [
                      _c("td", {
                        style: {
                          "border-left": "5px solid " + ticket.priority_color
                        },
                        attrs: {
                          rowspan: "3",
                          title: "Priority: " + ticket.priority
                        }
                      }),
                      _vm._v(" "),
                      _c(
                        "td",
                        {
                          staticStyle: {
                            "padding-top": "1rem",
                            "padding-left": "1rem"
                          }
                        },
                        [_c("b", [_vm._v(_vm._s(ticket.title))])]
                      )
                    ]),
                    _vm._v(" "),
                    _c("tr", [
                      _c("td", { staticStyle: { "padding-left": "1rem" } }, [
                        _c("small", [
                          _c(
                            "a",
                            {
                              staticClass: "rt-ticket-number",
                              attrs: {
                                href: _vm.basePath() + "/thread/" + ticket.id,
                                target: "_blank"
                              }
                            },
                            [_vm._v("#" + _vm._s(ticket.ticket_number))]
                          ),
                          _vm._v(" "),
                          _c("i", {
                            class: ticket.status_icon,
                            style: { color: ticket.status_icon_color },
                            attrs: { title: "Status: " + ticket.status }
                          })
                        ])
                      ])
                    ]),
                    _vm._v(" "),
                    _c("tr", [
                      _c(
                        "td",
                        {
                          staticStyle: {
                            "padding-bottom": "1rem",
                            "padding-left": "1rem"
                          }
                        },
                        [
                          _c("small", [
                            _vm._v(_vm._s(_vm.lang("created_at")) + ": "),
                            _c("b", [
                              _vm._v(
                                _vm._s(_vm.formattedTime(ticket.created_at))
                              )
                            ])
                          ])
                        ]
                      )
                    ])
                  ])
                ])
              ]
            )
          })
        )
      ])
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-ae59e156", module.exports)
  }
}

/***/ }),

/***/ 2879:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "telephone-alert-box" }, [
    _c("div", { staticClass: "telephone-alert-box-header" }, [
      _c("header", [
        _vm._v("\n      " + _vm._s(_vm.lang("call_from")) + " "),
        _c("b", [_vm._v(_vm._s(_vm.userNotificationData.data.call_from))]),
        _vm._v(" "),
        _vm.userNotificationData.timer.showTimer
          ? _c("span", [
              _c("span", { attrs: { id: "call-pop-countdown" } }, [
                _c("b", [
                  _vm._v(
                    _vm._s(_vm.userNotificationData.timer.getRemaining()) + " "
                  )
                ]),
                _vm._v(" "),
                _vm.userNotificationData.timer.canUserStopTimer
                  ? _c("i", {
                      staticClass: "fa fa-stop pointer-cursor",
                      attrs: {
                        "aria-hidden": "true",
                        title: _vm.lang(
                          "click_to_stop_counter_otherwise_popup_will_close"
                        )
                      },
                      on: { click: _vm.onStopTimerClick }
                    })
                  : _vm._e()
              ])
            ])
          : _vm._e()
      ])
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "telephone-alert-box-body" }, [
      _c(
        "div",
        { staticClass: "caller-info" },
        [
          _c("faveo-image-element", {
            staticClass: "caller-profile-image-avatar",
            attrs: {
              id: "caller-profile-image",
              "source-url": _vm.getUserProfiePic
            }
          }),
          _vm._v(" "),
          _vm.userNotificationData.data.is_registered_user
            ? _c("div", { staticClass: "user-name-and-email" }, [
                _c(
                  "a",
                  { attrs: { href: _vm.getUserProfileUrl, target: "_blank" } },
                  [
                    _vm._v(
                      "\n          " +
                        _vm._s(_vm.userNotificationData.data.user.name) +
                        "\n          "
                    ),
                    _vm.userNotificationData.status === "started" &&
                    _vm.userNotificationData.timer.showTimer
                      ? _c("sup", [
                          _c("span", {
                            staticClass:
                              "spinner-grow text-warning spinner-grow-small"
                          })
                        ])
                      : _vm._e()
                  ]
                ),
                _c("br"),
                _vm._v(" "),
                _c("small", [
                  _vm._v(_vm._s(_vm.userNotificationData.data.user.email))
                ])
              ])
            : _c("div", { staticClass: "unknown-user" }, [
                _vm._v(
                  "\n        " + _vm._s(_vm.lang("unknown_user")) + "\n        "
                ),
                _vm.userNotificationData.status === "started" &&
                _vm.userNotificationData.timer.showTimer
                  ? _c("sup", [
                      _c("span", {
                        staticClass:
                          "spinner-grow text-warning spinner-grow-small"
                      })
                    ])
                  : _vm._e()
              ]),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "recent-ticket-list-btn" },
            [
              _vm.isUserActive
                ? _c("recent-ticket-list", {
                    attrs: {
                      "ticket-list":
                        _vm.userNotificationData.data.user.recent_tickets,
                      "on-ticket-click": _vm.onTicketClick
                    }
                  })
                : _vm._e()
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _vm.linkedTicketObj && _vm.isUserActive
        ? _c("div", { staticClass: "linked-ticket-block padding-top-7" }, [
            _c("small", [
              _vm._v(_vm._s(_vm.lang("linked_ticket")) + ": #"),
              _c(
                "a",
                {
                  attrs: {
                    href: _vm.basePath() + "/thread/" + _vm.linkedTicketObj.id,
                    target: "_blank"
                  }
                },
                [_vm._v(_vm._s(_vm.linkedTicketObj.ticket_number))]
              ),
              _vm._v(" "),
              _vm.showUnlinkBtn
                ? _c("i", {
                    staticClass: "fa fa-times ticket-unlink-btn pointer-cursor",
                    attrs: { title: "remove_linked_ticket" },
                    on: {
                      click: function($event) {
                        _vm.unLinkTicket()
                      }
                    }
                  })
                : _vm._e()
            ])
          ])
        : _vm._e(),
      _vm._v(" "),
      _vm.isUserActive
        ? _c("div", { staticClass: "take-note padding-top-7" }, [
            _c("textarea", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.internalNote,
                  expression: "internalNote"
                }
              ],
              staticClass: "form-control",
              staticStyle: { resize: "none" },
              attrs: {
                id: "telephone-alert-internal-note",
                placeholder: "Add internal note"
              },
              domProps: { value: _vm.internalNote },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.internalNote = $event.target.value
                }
              }
            })
          ])
        : _vm._e(),
      _vm._v(" "),
      _vm.userNotificationData.status === "ended" && _vm.isUserActive
        ? _c("div", { staticClass: "telephone-alert-submit padding-top-7" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-primary btn-sm",
                attrs: { disabled: _vm.loading },
                on: {
                  click: function($event) {
                    _vm.onSubmit()
                  }
                }
              },
              [
                _vm._v(
                  "\n        " +
                    _vm._s(
                      _vm.loading ? _vm.lang("submitting") : _vm.lang("submit")
                    ) +
                    "\n      "
                )
              ]
            )
          ])
        : _vm._e()
    ])
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-2aa7b892", module.exports)
  }
}

/***/ }),

/***/ 2880:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { attrs: { id: "telephone-alert-box__position" } },
    [
      _c(
        "transition-group",
        { attrs: { name: "slide-fade" } },
        [
          _vm.getItems.length > 0
            ? _vm._l(_vm.getItems, function(item) {
                return _c(
                  "div",
                  {
                    key: item.id,
                    attrs: { id: "telephone-alert-box" },
                    on: {
                      mouseenter: function($event) {
                        _vm.onMouseEnter(item)
                      },
                      mouseleave: function($event) {
                        _vm.onMouseLeave(item)
                      }
                    }
                  },
                  [
                    _c("call-popup", {
                      key: item.id,
                      attrs: { "call-data": item }
                    })
                  ],
                  1
                )
              })
            : _vm._e()
        ],
        2
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
    require("vue-hot-reload-api")      .rerender("data-v-e8b22482", module.exports)
  }
}

/***/ })

},[2847]);