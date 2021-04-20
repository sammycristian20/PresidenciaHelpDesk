webpackJsonp([11],{

/***/ 223:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2420)
/* template */
var __vue_template__ = __webpack_require__(2421)
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
Component.options.__file = "resources/assets/js/components/Navigation/NavigationLayout.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-ad29d39c", Component.options)
  } else {
    hotAPI.reload("data-v-ad29d39c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 224:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2422)
/* template */
var __vue_template__ = __webpack_require__(2423)
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
Component.options.__file = "resources/assets/js/components/Navigation/Navigation.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-67371fe8", Component.options)
  } else {
    hotAPI.reload("data-v-67371fe8", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2414:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2415);


/***/ }),

/***/ 2415:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_v_tooltip__ = __webpack_require__(53);
/**
 * Contains all Navigation related components
 * REASON :  if we put these components inside agent or admin bundle and if a plugin
 * is loading, we don't need rest of the data in the bundle so keeping this seperate makes
 * it more efficient
 * We cannot load navigation dynamically because it will be required on each and every page,
 * so better to load this bundle before any bundle for better user experience
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */

__webpack_require__(29);





Vue.component('admin-navigation-bar', __webpack_require__(2416));

Vue.component('agent-navigation-bar', __webpack_require__(2425));



Vue.use(__WEBPACK_IMPORTED_MODULE_2_v_tooltip__["a" /* default */]);

new Vue({
  el: '#navigation-container',
  store: __WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */]
});

/***/ }),

/***/ 2416:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2417)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2419)
/* template */
var __vue_template__ = __webpack_require__(2424)
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
Component.options.__file = "resources/assets/js/components/Navigation/AdminNavigationBar.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-20bcb224", Component.options)
  } else {
    hotAPI.reload("data-v-20bcb224", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2417:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2418);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("e11d093a", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-20bcb224\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AdminNavigationBar.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-20bcb224\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AdminNavigationBar.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2418:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.admin-navigation{\n    margin-top : 200px !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2419:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__NavigationLayout__ = __webpack_require__(223);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__NavigationLayout___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2__NavigationLayout__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

	name: 'admin-navigation-bar',

	description: 'Admin Navigation Bar on admin panel',

	data: function data() {

		return {

			navigationArray: [],

			loading: true
		};
	},
	created: function created() {

		window.eventHub.$on('update-sidebar', this.refreshSidebar);
	},
	beforeMount: function beforeMount() {

		// this.loading = true;

		this.getDataFromServer();
	},


	methods: {

		/**
  	* Gets data from server and populate in the component state
  	* NOTE: Making it a diffent method to improve readablity
  	* @return {Promise}
  */
		refreshSidebar: function refreshSidebar() {

			this.getDataFromServer();
		},


		/**
  	* Gets data from server and populate in the component state
  	* @return {Promise}
  	*/
		getDataFromServer: function getDataFromServer() {
			var _this = this;

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get("/api/admin/navigation").then(function (res) {

				_this.loading = false;

				_this.navigationArray = res.data.data;
			}).catch(function (err) {

				_this.loading = false;

				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err);
			});
		}
	},

	components: {

		'navigation': __webpack_require__(224),

		'navigation-layout': __WEBPACK_IMPORTED_MODULE_2__NavigationLayout___default.a,

		'loader': __webpack_require__(8)
	}
});

/***/ }),

/***/ 2420:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__ = __webpack_require__(4);
//
//
//
//
//
//
//
//
//
//
//
//
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

	computed: {
		sidebarStyle: function sidebarStyle() {

			if (this.isRtlLayout) {

				return { 'margin-right': '-8px' };
			}

			return { 'margin-left': '-8px' };
		},
		fullName: function fullName() {

			return sessionStorage.getItem('full_name');
		},
		profilePic: function profilePic() {
			return sessionStorage.getItem('profile_pic');
		}
	},

	methods: {
		subString: function subString(value) {

			return Object(__WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__["o" /* getSubStringValue */])(value, 20);
		}
	},

	components: {

		'faveo-image-element': __webpack_require__(22)
	}
});

/***/ }),

/***/ 2421:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "sidebar" },
    [
      _c("div", { staticClass: "user-panel mt-3 pb-3 mb-3 d-flex" }, [
        _c(
          "div",
          { staticClass: "image" },
          [
            _c("faveo-image-element", {
              attrs: {
                id: "sidebar-profile-img",
                "source-url": _vm.profilePic,
                classes: ["img-circle elevation-2"],
                "alternative-text": "User Image"
              }
            })
          ],
          1
        ),
        _vm._v(" "),
        _c("div", { staticClass: "info" }, [
          _c(
            "a",
            {
              staticClass: "d-block",
              attrs: { title: _vm.fullName, href: _vm.basePath() + "/profile" }
            },
            [_vm._v(_vm._s(_vm.subString(_vm.fullName)))]
          )
        ])
      ]),
      _vm._v(" "),
      _vm._t("default")
    ],
    2
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-ad29d39c", module.exports)
  }
}

/***/ }),

/***/ 2422:
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






/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'navigation',

	props: {

		navigation: { type: Object, default: function _default() {} },

		toggleParent: { type: Function, default: function _default() {} }
	},

	data: function data() {

		return {

			menuOpened: false,

			active: false
		};
	},
	mounted: function mounted() {

		this.markNavigationActiveIfRequired();
	},


	methods: {

		/**
     	* Marks active and menuOpened as true.
     	* NOTE: this method is passed to children so that chilren can call this method and mark parent as active
     	* @return {undefined}
     	*/
		toggleActive: function toggleActive() {

			this.active = true;

			this.menuOpened = true;

			this.toggleParent();
		},
		isActive: function isActive(navigation) {

			if (this.getCurrentRouteUrl() == navigation.redirectUrl) {

				return true;
			}
		},


		/**
   * Redirects if has no child elements
   * @param  {String} redirectUrl
   * @return {String}
   */
		onNavigationClick: function onNavigationClick(navigation) {

			// for logout
			if (navigation.routeString == 'auth/logout') {

				__WEBPACK_IMPORTED_MODULE_0_axios___default.a.post('/auth/logout').then(function (res) {

					window.location.replace(res.data.data);
				}).catch(function (error) {});
			}

			// if hasChildren is false, then redirect
			if (Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["c" /* boolean */])(navigation.hasChildren)) {

				this.menuOpened = !this.menuOpened;
			}
		},


		/**
     * Gets redirect link for the anchor tag
     * @return {String}
     */
		getLink: function getLink(navigation) {

			if (!Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["c" /* boolean */])(navigation.hasChildren)) {

				return navigation.redirectUrl;
			}
			return 'javascript:void(0);';
		},


		/**
   * Gets current url
   * @return {String}
   */
		getCurrentRouteUrl: function getCurrentRouteUrl() {
			return window.location.href;
		},
		subStr: function subStr(name, count) {
			return Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["o" /* getSubStringValue */])(name, parseInt(count));
		},


		/**
      * Checks if navigation is active, if yes mark that as active
      * @return {undefined}
    */
		markNavigationActiveIfRequired: function markNavigationActiveIfRequired() {

			if (this.getCurrentRouteUrl() == this.navigation.redirectUrl) {

				this.active = true;

				this.toggleParent();
			}
		}
	},

	watch: {
		active: function active(newVal) {

			if (newVal) {
				// waiting for the DOM to render completely so that active-navigation-element can be present
				setTimeout(function () {

					var activeElements = document.getElementsByClassName('active-navigation-element');

					if (activeElements !== undefined) {

						activeElements[activeElements.length - 1].scrollIntoView({ behavior: "smooth" });
					}
				}, 10);
			}

			return newVal;
		}
	}
});

/***/ }),

/***/ 2423:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return !_vm.navigation.hasChildren
    ? _c("li", { staticClass: "nav-item" }, [
        _c(
          "a",
          {
            class: [
              "nav-link",
              { "active active-navigation-element": _vm.active }
            ],
            attrs: { href: _vm.getLink(_vm.navigation) },
            on: {
              click: function($event) {
                _vm.onNavigationClick(_vm.navigation)
              }
            }
          },
          [
            _c("i", { class: "nav-icon " + _vm.navigation.iconClass }),
            _vm._v(" "),
            _c("p", [
              _vm._v(
                " " +
                  _vm._s(
                    _vm.navigation.hasCount
                      ? _vm.subStr(_vm.navigation.name, 16)
                      : _vm.navigation.name
                  ) +
                  "\n\t\t\t\t\t\t\t\t\n\t\t\t"
              ),
              _vm.boolean(_vm.navigation.hasCount)
                ? _c(
                    "span",
                    {
                      staticClass: "right badge badge-success",
                      attrs: { id: "nav_count" }
                    },
                    [_vm._v(_vm._s(_vm.navigation.count) + "\n\n\t\t\t")]
                  )
                : _vm._e()
            ])
          ]
        )
      ])
    : _c(
        "li",
        { class: ["nav-item has-treeview", { "menu-open": _vm.menuOpened }] },
        [
          _c(
            "a",
            {
              class: ["nav-link", { active: _vm.menuOpened }],
              attrs: { href: "javascript:;", id: "nav_child" }
            },
            [
              _c("i", { class: "nav-icon " + _vm.navigation.iconClass }),
              _vm._v(" "),
              _c("p", [
                _vm._v(_vm._s(_vm.navigation.name) + "\n\t\t\t\t\n\t\t\t"),
                _c("i", { staticClass: "right fas fa-angle-left" })
              ])
            ]
          ),
          _vm._v(" "),
          _c(
            "ul",
            {
              staticClass: "nav nav-treeview",
              style: [
                _vm.menuOpened
                  ? { display: "block !important" }
                  : { display: "none !important" }
              ]
            },
            _vm._l(_vm.navigation.children, function(nav, index) {
              return _c("navigation", {
                key: index,
                attrs: { navigation: nav, toggleParent: _vm.toggleActive }
              })
            })
          )
        ]
      )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-67371fe8", module.exports)
  }
}

/***/ }),

/***/ 2424:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("navigation-layout", [
    _c("nav", { staticClass: "mt-2" }, [
      _vm.loading
        ? _c("div", { staticClass: "admin-navigation" }, [_c("loader")], 1)
        : _vm._e(),
      _vm._v(" "),
      _c(
        "ul",
        {
          staticClass: "nav nav-pills nav-sidebar flex-column nav-child-indent",
          attrs: {
            "data-widget": "treeview",
            role: "menu",
            "data-accordion": "false"
          }
        },
        [
          _vm._l(_vm.navigationArray, function(navigationCategory, index) {
            return [
              _c("li", { staticClass: "nav-header" }, [
                _vm._v(_vm._s(navigationCategory.name.toUpperCase()))
              ]),
              _vm._v(" "),
              _vm._l(navigationCategory.navigations, function(
                navigation,
                index
              ) {
                return [
                  navigation.name
                    ? _c("navigation", {
                        key: navigation.name,
                        attrs: { navigation: navigation }
                      })
                    : _vm._e()
                ]
              })
            ]
          })
        ],
        2
      )
    ])
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-20bcb224", module.exports)
  }
}

/***/ }),

/***/ 2425:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2426)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2428)
/* template */
var __vue_template__ = __webpack_require__(2429)
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
Component.options.__file = "resources/assets/js/components/Navigation/AgentNavigationBar.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-ad8fd5a4", Component.options)
  } else {
    hotAPI.reload("data-v-ad8fd5a4", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2426:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2427);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("79838732", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ad8fd5a4\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AgentNavigationBar.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ad8fd5a4\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AgentNavigationBar.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2427:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.agent-navigation{\n    margin-top : 200px !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2428:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__NavigationLayout__ = __webpack_require__(223);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__NavigationLayout___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2__NavigationLayout__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

	name: 'agent-navigation-bar',

	description: 'Agent Navigation Bar on agent panel',

	data: function data() {

		return {

			navigationArray: [],

			loading: true
		};
	},
	beforeMount: function beforeMount() {

		window.eventHub.$on('refreshTicket', this.refreshSidebar);

		// if any piece of code requires sidebar to be updated,
		// they can simply fire this event
		window.eventHub.$on('update-sidebar', this.refreshSidebar);

		// this.loading = true;

		this.getDataFromServer();
	},


	methods: {

		/**
   * Gets data from server and populate in the component state
   * NOTE: Making it a diffent method to improve readablity
   * @return {Promise}
   */
		refreshSidebar: function refreshSidebar() {

			this.getDataFromServer();
		},


		/**
   * Gets data from server and populate in the component state
   * @return {Promise}
   */
		getDataFromServer: function getDataFromServer() {
			var _this = this;

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get("/api/agent/navigation").then(function (res) {

				_this.loading = false;

				_this.navigationArray = res.data.data;
			}).catch(function (err) {

				_this.loading = false;

				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err);
			});
		}
	},

	components: {

		'navigation': __webpack_require__(224),

		'navigation-layout': __WEBPACK_IMPORTED_MODULE_2__NavigationLayout___default.a,

		'loader': __webpack_require__(8)
	}
});

/***/ }),

/***/ 2429:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("navigation-layout", [
    _c("nav", { staticClass: "mt-2" }, [
      _vm.loading
        ? _c("div", { staticClass: "agent-navigation" }, [_c("loader")], 1)
        : _vm._e(),
      _vm._v(" "),
      _c(
        "ul",
        {
          staticClass: "nav nav-pills nav-sidebar flex-column nav-child-indent",
          attrs: {
            "data-widget": "treeview",
            role: "menu",
            "data-accordion": "false"
          }
        },
        [
          _vm._l(_vm.navigationArray, function(navigationCategory, index) {
            return [
              _c("li", { staticClass: "nav-header" }, [
                _vm._v(_vm._s(navigationCategory.name.toUpperCase()))
              ]),
              _vm._v(" "),
              _vm._l(navigationCategory.navigations, function(
                navigation,
                index
              ) {
                return [
                  navigation.name
                    ? _c("navigation", {
                        key: navigation.name,
                        attrs: { navigation: navigation }
                      })
                    : _vm._e()
                ]
              })
            ]
          })
        ],
        2
      )
    ])
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-ad8fd5a4", module.exports)
  }
}

/***/ })

},[2414]);