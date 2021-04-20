webpackJsonp([8],{

/***/ 2442:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2443);


/***/ }),

/***/ 2443:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_store__ = __webpack_require__(10);

var bootstrap = __webpack_require__(29);





var app = new Vue({
    el: '#ldap-settings',
    store: __WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */],
    components: {
        'ldap-list-page': __webpack_require__(2444),
        'ldap-settings': __webpack_require__(2449)
    }
});

// injecting ldap-login component into login-box at login page on login-box-mounted event
bootstrap.injectComponentIntoView('ldap-login', __webpack_require__(2466), 'login-box-mounted', 'login-box');

/***/ }),

/***/ 2444:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2445)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2447)
/* template */
var __vue_template__ = __webpack_require__(2448)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-2a535e75"
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
Component.options.__file = "app/Plugins/Ldap/views/js/components/LdapListPage.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2a535e75", Component.options)
  } else {
    hotAPI.reload("data-v-2a535e75", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2445:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2446);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("82ad593a", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2a535e75\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./LdapListPage.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2a535e75\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./LdapListPage.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2446:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.list-group-item[data-v-2a535e75] {\n    border: none;\n    border-top: 2px solid #FAFAFA;\n}\n.list-group-item[data-v-2a535e75]:first-child {\n    border-top: none;\n}\n.list-group-item[data-v-2a535e75]:nth-child(odd) {\n    background: #FAFAFA;\n}\n.list-group-item[data-v-2a535e75]:nth-child(even) {\n    background: #FFFFFF;\n}\n.data-block[data-v-2a535e75] {\n    padding-top: 1.5rem;\n}\n.action-block[data-v-2a535e75] {\n    padding-top: 2.5rem;\n}\n.list-group-item[data-v-2a535e75]:hover {\n    background: #F5F5F5;\n}\n.list-group-item-heading[data-v-2a535e75] {\n    padding-top: .5rem;\n}\n.description-block>.description-text[data-v-2a535e75] {\n    text-transform: inherit;\n}\n.hide-checkbox[data-v-2a535e75]{ height : 25px;\n}\n.w-100[data-v-2a535e75] { width : 100% !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2447:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_components_MiniComponent_FaveoBox__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

	name: 'ldap-list-page',

	data: function data() {
		return {
			dataToDisplay: ['domain', 'port', 'encryption', 'username'],
			adList: [],
			hideDefaultLogin: false,
			isLoading: false
		};
	},

	beforeMount: function beforeMount() {
		this.getLdapList();
	},


	methods: {
		getLdapList: function getLdapList() {
			var _this = this;

			this.isLoading = true;
			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('api/ldap/settings').then(function (response) {
				_this.adList = response.data.data.ldap_list;
				_this.hideDefaultLogin = response.data.data.hide_default_login;
			}).catch(function (error) {
				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(error, 'ldap-list-page');
			}).finally(function () {
				_this.isLoading = false;
			});
		},
		deleteItem: function deleteItem(ldapId) {
			var _this2 = this;

			var isConfirmed = confirm('Are you sure you want to delete?');
			if (isConfirmed) {
				this.isLoading = true;
				__WEBPACK_IMPORTED_MODULE_0_axios___default.a.delete('api/ldap/settings/' + ldapId).then(function (response) {
					Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(response, 'ldap-list-page');
					_this2.getLdapList();
				}).catch(function (error) {
					Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(error, 'ldap-list-page');
				}).finally(function () {
					_this2.isLoading = false;
				});
			}
		},
		updateHideDefaultLogin: function updateHideDefaultLogin() {
			var _this3 = this;

			this.isLoading = true;
			var params = {
				hide_default_login: this.hideDefaultLogin
			};
			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.post('api/ldap/hide-default-login', params).then(function (response) {
				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(response, 'ldap-list-page');
			}).catch(function (error) {
				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(error, 'ldap-list-page');
			}).finally(function () {
				_this3.isLoading = false;
			});
		}
	},

	components: {
		'faveo-box': __WEBPACK_IMPORTED_MODULE_2_components_MiniComponent_FaveoBox___default.a,
		'alert': __webpack_require__(6),
		'loader': __webpack_require__(9)
	}

});

/***/ }),

/***/ 2448:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "faveo-box",
    { attrs: { title: _vm.trans("ldap_list") } },
    [
      _c("alert", { attrs: { componentName: "ldap-list-page" } }),
      _vm._v(" "),
      _vm.isLoading ? _c("loader", { attrs: { duration: 4000 } }) : _vm._e(),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "card-tools d-flex mt-1",
          attrs: { slot: "headerMenu" },
          slot: "headerMenu"
        },
        [
          _c(
            "a",
            {
              directives: [
                {
                  name: "tooltip",
                  rawName: "v-tooltip",
                  value: _vm.trans("configure_new_ldap"),
                  expression: "trans('configure_new_ldap')"
                }
              ],
              staticClass: "btn btn-tool",
              attrs: { href: _vm.basePath() + "/ldap/settings/create" }
            },
            [
              _c("i", {
                staticClass: "fas fa-plus",
                attrs: { "aria-hidden": "true" }
              })
            ]
          ),
          _vm._v(" "),
          _c(
            "span",
            {
              directives: [
                {
                  name: "tooltip",
                  rawName: "v-tooltip",
                  value: _vm.trans("hide_default_login"),
                  expression: "trans('hide_default_login')"
                }
              ],
              staticClass: "btn-tool"
            },
            [
              _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.hideDefaultLogin,
                    expression: "hideDefaultLogin"
                  }
                ],
                staticClass: "hide-checkbox",
                attrs: { type: "checkbox" },
                domProps: {
                  checked: Array.isArray(_vm.hideDefaultLogin)
                    ? _vm._i(_vm.hideDefaultLogin, null) > -1
                    : _vm.hideDefaultLogin
                },
                on: {
                  change: [
                    function($event) {
                      var $$a = _vm.hideDefaultLogin,
                        $$el = $event.target,
                        $$c = $$el.checked ? true : false
                      if (Array.isArray($$a)) {
                        var $$v = null,
                          $$i = _vm._i($$a, $$v)
                        if ($$el.checked) {
                          $$i < 0 && (_vm.hideDefaultLogin = $$a.concat([$$v]))
                        } else {
                          $$i > -1 &&
                            (_vm.hideDefaultLogin = $$a
                              .slice(0, $$i)
                              .concat($$a.slice($$i + 1)))
                        }
                      } else {
                        _vm.hideDefaultLogin = $$c
                      }
                    },
                    function($event) {
                      _vm.updateHideDefaultLogin()
                    }
                  ]
                }
              })
            ]
          )
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "list-group" },
        _vm._l(_vm.adList, function(list) {
          return _c("div", { key: list.id, staticClass: "list-group-item" }, [
            _c(
              "div",
              { staticClass: "row" },
              [
                _c(
                  "div",
                  {
                    directives: [
                      {
                        name: "tooltip",
                        rawName: "v-tooltip",
                        value: _vm.trans(list.schema),
                        expression: "trans(list.schema)"
                      }
                    ],
                    staticClass: "col-md-2"
                  },
                  [
                    _c("div", { staticClass: "ldap-schema-img" }, [
                      _c("img", {
                        staticClass: "w-100",
                        attrs: { src: list.image_url, alt: list.schema }
                      })
                    ])
                  ]
                ),
                _vm._v(" "),
                _vm._l(_vm.dataToDisplay, function(key) {
                  return _c(
                    "div",
                    {
                      key: key,
                      staticClass: "col-md-2 description-block data-block"
                    },
                    [
                      _c("h5", { staticClass: "description-header" }, [
                        _vm._v(_vm._s(_vm.trans(key)))
                      ]),
                      _vm._v(" "),
                      _c("span", { staticClass: "description-text" }, [
                        _vm._v(_vm._s(list[key] ? list[key] : "---"))
                      ])
                    ]
                  )
                }),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticClass:
                      "col-md-2 description-block action-block float-right"
                  },
                  [
                    _c(
                      "a",
                      {
                        staticClass: "btn btn-primary btn-sm",
                        attrs: {
                          href:
                            _vm.basePath() +
                            "/ldap/settings/" +
                            list.id +
                            "/edit"
                        }
                      },
                      [
                        _c("i", {
                          directives: [
                            {
                              name: "tooltip",
                              rawName: "v-tooltip",
                              value: _vm.trans("edit"),
                              expression: "trans('edit')"
                            }
                          ],
                          staticClass: "fas fa-edit",
                          attrs: { "aria-hidden": "true" }
                        })
                      ]
                    ),
                    _vm._v(" "),
                    _c(
                      "button",
                      {
                        staticClass: "btn btn-danger btn-sm",
                        on: {
                          click: function($event) {
                            _vm.deleteItem(list.id)
                          }
                        }
                      },
                      [
                        _c("i", {
                          directives: [
                            {
                              name: "tooltip",
                              rawName: "v-tooltip",
                              value: _vm.trans("delete"),
                              expression: "trans('delete')"
                            }
                          ],
                          staticClass: "fas fa-trash",
                          attrs: { "aria-hidden": "true" }
                        })
                      ]
                    )
                  ]
                )
              ],
              2
            )
          ])
        })
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
    require("vue-hot-reload-api")      .rerender("data-v-2a535e75", module.exports)
  }
}

/***/ }),

/***/ 2449:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2450)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2452)
/* template */
var __vue_template__ = __webpack_require__(2465)
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
Component.options.__file = "app/Plugins/Ldap/views/js/components/LdapSettings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-534d916a", Component.options)
  } else {
    hotAPI.reload("data-v-534d916a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2450:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2451);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("1312ee08", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-534d916a\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./LdapSettings.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-534d916a\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./LdapSettings.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2451:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),

/***/ 2452:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics_js__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__validator_ldapSettingsRules_js__ = __webpack_require__(2453);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_components_MiniComponent_FaveoBox__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//







var LDAP_API = 'api/ldap/settings/';

/* harmony default export */ __webpack_exports__["default"] = ({
  name: "ldap-settings",

  description: "ldap setting page",

  beforeMount: function beforeMount() {
    // this.getDepartmentList();
    // this.getOrganizationsList();
    var LDAP_ID = Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics_js__["m" /* getIdFromUrl */])(window.location.pathname);

    this.ldapId = LDAP_ID || '';

    // call getldapSetting fn in case of edit
    if (this.ldapId !== '') {
      this.getldapSetting();
    } else {
      // case of create
      this.hasDataPopulated = true;
      this.loading = false;
    }
  },


  data: function data() {
    return {
      domain: "", // domain name
      is_valid: 0, // parameter used for showing the search base query the valid i default set to false and would be true once the save configuration is done
      password: "", //password
      port: null,
      encryption: null,
      schema: 'active_directory',

      ldap_label: "", //ldap label at login page
      forgot_password_link: "", // forgot password link for ldap
      prefix: '',
      suffix: '',

      username: "", //username
      user_type: "", // default value set to user
      search_bases: [], // search basis array
      show_organization: true,
      show_department: true,
      show_role: true,
      loading: true, //to show the loader
      loadingSpeed: 4000, // loader speed
      hasDataPopulated: false, // variable use to display data once the getldapsetting is successfull
      message: '',

      ldapId: '',
      tableHeadings: {
        faveo: {
          header: 'faveo_attribute',
          description: 'faveo_attribute_description'
        },
        thirdParty: {
          header: 'active_directory_attribute',
          description: 'active_directory_attribute_description'
        },
        overwrite: {
          header: 'overwrite',
          description: 'ldap_overwrite_description'
        }
      }
    };
  },
  watch: {},
  methods: {
    /**to fetch data for the search query basis
     * @returns {void}
     *
     */
    getldapSetting: function getldapSetting() {
      var _this = this;

      this.loading = true;
      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get(LDAP_API + this.ldapId).then(function (res) {
        _this.loading = false;
        _this.hasDataPopulated = true;
        _this.updateStatesWithData(res.data.data);

        _this.message = res.data.message;

        if (res.data.data.search_bases.length > 0) {} else {
          setTimeout(function () {
            res.data.data.search_bases.push({
              id: "",
              search_base: "",
              filter: "",
              user_type: "user",
              departments: [],
              organizations: []
            });
          }, 5);
        }
      }).catch(function (err) {
        _this.loading = false;
      });
    },


    /**
     * @param {Object} ldapSettingsData
     * function helps us to direactly access the data through its key.
     */
    updateStatesWithData: function updateStatesWithData(ldapSettingsData) {
      var self = this;
      var stateData = this.$data;
      Object.keys(ldapSettingsData).map(function (key) {
        if (stateData.hasOwnProperty(key)) {
          self[key] = ldapSettingsData[key];
        }
      });
    },


    /**
     * methods used to add a new user in search query user
     * ie: push the object in the array
     */
    addUser: function addUser(data) {
      this.search_bases.push(data);
    },


    /**
     * populates the states corresponding to 'name' with 'value'
     * @param  {string} value
     * @param  {[type]} name
     * @return {void}
     */
    onChange: function onChange(value, name) {
      this[name] = value;
    },


    /**
     * method to get the server response for the single search base query
     * @param {string}
     * @returns {void}
     */
    confirmLdapPing: function confirmLdapPing(data) {
      var _this2 = this;

      this.loading = true;
      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get("api/ldap/search-base/ping", {
        params: {
          search_base: data
        }
      }).then(function (res) {
        _this2.loading = false;
        Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["b" /* successHandler */])(res, "searchbase");
      }).catch(function (err) {
        _this2.loading = false;
        Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(err, "searchbase");
      });
    },


    /**
     * method to delete the user for the search base query array
     * @param {Number}
     * ie Number is the index value of the element which needs to be deleted from the array
     */
    deleteUser: function deleteUser(index) {
      this.search_bases.splice(index, 1);
    },


    /**
     * saves ldap configuration
     *
     */
    saveConfiguration: function saveConfiguration() {
      var _this3 = this;

      if (this.isValid()) {
        this.loading = true;
        __WEBPACK_IMPORTED_MODULE_0_axios___default.a.post(LDAP_API, this.getSaveApiParams()).then(function (res) {
          _this3.loading = false;
          _this3.showSearchQueryBlock = true;
          Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["b" /* successHandler */])(res, "ldap");

          _this3.ldapId = res.data.data.ldap_id;

          _this3.getldapSetting();
        }).catch(function (err) {
          _this3.loading = false;
          Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(err, "ldap");
        });
      }
    },
    getSaveApiParams: function getSaveApiParams() {
      var params = {
        id: this.ldapId !== '' ? this.ldapId : undefined,
        domain: this.domain,
        username: this.username,
        password: this.password,
        port: this.port,
        encryption: this.encryption,
        schema: this.schema,

        ldap_label: this.ldap_label,
        forgot_password_link: this.forgot_password_link,
        prefix: this.prefix,
        suffix: this.suffix
      };

      return params;
    },

    /**check if the validations are proper
     * @returns {Boolean}
     */
    isValid: function isValid() {
      var _validateLdapSettings = Object(__WEBPACK_IMPORTED_MODULE_3__validator_ldapSettingsRules_js__["a" /* validateLdapSettings */])(this.$data),
          errors = _validateLdapSettings.errors,
          isValid = _validateLdapSettings.isValid;

      if (!isValid) {
        return false;
      }
      return true;
    }
  },

  computed: {
    schemas: function schemas() {
      return [{ id: 'active_directory', name: 'ActiveDirectory' }, { id: 'open_ldap', name: 'OpenLDAP' }, { id: 'free_ipa', name: 'FreeIPA' }];
    },
    encryptions: function encryptions() {
      return [{ id: null, name: 'None' }, { id: 'ssl', name: 'SSL' }, { id: 'tls', name: 'TLS' }];
    }
  },

  components: {
    "static-select": __webpack_require__(26),
    "text-field": __webpack_require__(11),
    alert: __webpack_require__(6),
    "custom-loader": __webpack_require__(9),
    "search-basis": __webpack_require__(2454),
    "user-import-mapper": __webpack_require__(204),
    "ldap-table": __webpack_require__(2460),
    checkbox: __webpack_require__(41),
    'faveo-box': __WEBPACK_IMPORTED_MODULE_4_components_MiniComponent_FaveoBox___default.a
  }
});

/***/ }),

/***/ 2453:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = validateLdapSettings;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_easy_validator_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);
/**
 * This Files contain validation specific to LdapSettings.vue only
 */




/**
 * @param {object} data      ldapsettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */

function validateLdapSettings(data) {
  // console.log(data.search_bases.length, "inside ldap rule setting")
  var domain = data.domain,
      username = data.username,
      password = data.password,
      ldap_label = data.ldap_label,
      forgot_password_link = data.forgot_password_link;

  //rules has to apply only after checking conditions

  var validatingData = {
    domain: [domain, 'isRequired'],
    username: [username, 'isRequired'],
    password: [password, 'isRequired'],
    ldap_label: [ldap_label, 'max(24)']
    // forgot_password_link:[forgot_password_link, 'isUrl'],
  };

  //creating a validator instance and pasing lang method to it
  var validator = new __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__["Validator"](__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["q" /* lang */]);

  var _validator$validate = validator.validate(validatingData),
      errors = _validator$validate.errors,
      isValid = _validator$validate.isValid;

  // write to vuex if errors


  __WEBPACK_IMPORTED_MODULE_0_store__["a" /* store */].dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

  return { errors: errors, isValid: isValid };
}

/***/ }),

/***/ 2454:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2455)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2457)
/* template */
var __vue_template__ = __webpack_require__(2459)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-d9b2a5b6"
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
Component.options.__file = "app/Plugins/Ldap/views/js/components/SearchBasis.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-d9b2a5b6", Component.options)
  } else {
    hotAPI.reload("data-v-d9b2a5b6", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2455:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2456);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("4f3a35bf", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-d9b2a5b6\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./SearchBasis.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-d9b2a5b6\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./SearchBasis.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2456:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.ldap-import-query[data-v-d9b2a5b6] {\n    margin-bottom: 10px;\n}\n.delete-search-base-modal-title[data-v-d9b2a5b6] {\n\t  padding-top: 1rem;\n    padding-bottom: 2rem;\n}\n\n", ""]);

// exports


/***/ }),

/***/ 2457:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics_js__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__validator_ldapSearchBaseRules_js__ = __webpack_require__(2458);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_5_components_MiniComponent_FaveoBox__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

    ldapId: { type: String, default: '' },

    /**
     * Add User is function which helps to add a new query in the searchbases table
     */
    addUser: { type: Function, default: function _default() {
        return null;
      } },

    /**
     * List of query in the search_bases
     */
    searchBaseArray: { type: Array, default: [] },

    /**
     * get ldap setting api call , function is passed in as  a prop because when the user delete the
     * searchbase then after the modal get closed new data should be reflected
     */
    getLdap: { type: Function, default: function _default() {
        return null;
      } },

    /**
     * decides if organization shold be visible or not
     */
    showOrganization: { type: Boolean, default: true },

    /**
     * decides if department shold be visible or not
     */
    showDepartment: { type: Boolean, default: true },

    /**
     * if role field should be visible
     */
    showRole: { type: Boolean, default: true },

    loadingValue: { type: Boolean, default: true }

  },
  data: function data() {
    return {
      /**
       * Component Name
       */
      name: "searchbase",

      iconClass: "fa fa-trash",
      is_valid: "", // parameter used for showing the search base query the valid i default set to false and would be true once the save configuration is done
      search_base: "",
      loading: this.loadingValue, //to show the loader
      loadingSpeed: 4000, // loader speed
      usersArray: [{
        id: "user",
        name: "user"
      }, {
        id: "admin",
        name: "admin"
      }, {
        id: "agent",
        name: "agent"
      }], // static user type
      deletePopup: false,
      containerStyle: {
        width: "500px"
      },

      searchValueId: "", //state used to store the id send by the backend
      indexValue: "", //state used to store the index of the single item row in searchbases table
      organization_ids: [] //organization id
    };
  },
  watch: {
    searchBaseArray: function searchBaseArray(newvalue) {
      return newvalue;
    }
  },
  methods: {
    /**
     * Addquery method would add a new user in the searchbases table with help of addUser function,
     * which is being passed as prop
     */
    addQuery: function addQuery() {
      this.addUser({
        id: "",
        search_base: "",
        filter: "",
        user_type: "user",
        departments: [],
        organizations: []
      });
    },


    /**
     * populate the states corresponding to 'name' ith 'value'
     * here we have been sending the index to ensure the two way binding with the search_base tag
     * index helps us to update that value in an array
     * @param {string} value
     * @param {index} name
     * @returns {void}
     */

    onSearchBase: function onSearchBase(value, name) {
      var newindexValue = name.replace("searchbase", "");
      this.searchBaseArray[newindexValue].search_base = value;
    },


    /**
     * populate the states corresponding to 'name' ith 'value'
     * here we have been sending the index to ensure the two way binding with the search_base tag
     * index helps us to update that value in an array
     * @param {string} value
     * @param {index} name
     * @returns {void}
     */

    onFilter: function onFilter(value, name) {
      var newindexValue = name.replace("filter-", "");
      this.searchBaseArray[newindexValue].filter = value;
    },


    /**
     * populates the state corresponding to 'name' with 'value'
     * @param {string} value
     * @param {[type]} name
     * @return {void}
     */
    onOrganizationChange: function onOrganizationChange(value, name) {
      this.searchBaseArray[name].organizations = value;
    },

    /**
     * populates the states corresponding to 'name' with 'value'
     * @param  {string} value
     * @param  {[type]} name
     * @return {void}
     */
    onDepartmentChange: function onDepartmentChange(value, name) {
      this.searchBaseArray[name].departments = value;
    },

    /**
     * populate the states corresponding to 'name' with 'value'
     * here we have been sending the index to ensure the two way binding with the user_type tag
     * index helps us to updat that value in an array
     * @param {string} value
     * @param {index} name
     * @returns {void}
     */
    onHandle: function onHandle(value, name) {
      this.searchBaseArray[name].user_type = value;
    },


    /**
     * method to get the server response for the single search base query
     * @param {string}
     * @returns {void}
     */
    confirmLdapPing: function confirmLdapPing(searchBase, filter) {
      var _this = this;

      this.loading = true;
      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get("api/ldap/search-base/ping/" + this.ldapId, {
        params: {
          search_base: searchBase,
          filter: filter
        }
      }).then(function (res) {
        _this.loading = false;
        Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["b" /* successHandler */])(res, "searchbase");
      }).catch(function (err) {
        _this.loading = false;
        Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(err, "searchbase");
      });
    },


    /**
     * Mehtod will make the modal pop vairiable true and assign the searchId ie (value send through backend) to indexValue
     * @param {Number}
     * ie Number is the searchId value of the element which needs to be deleted from the array
     */
    deleteUser: function deleteUser(searchId, index) {
      this.deletePopup = true;
      if (searchId) {
        this.deletePopup = true;
        this.searchValueId = searchId;
      } else {
        this.indexValue = index;
      }
    },


    /**
     * Method helps to delete the particular row in the searchbase table, with help
     * of searchId(ie it is an id which is being sent by backend)
     */
    onSubmitDelete: function onSubmitDelete() {
      var _this2 = this;

      if (this.searchValueId) {
        __WEBPACK_IMPORTED_MODULE_0_axios___default.a.delete("api/ldap/search-base/" + this.searchValueId).then(function (res) {
          _this2.deletePopup = false;
          Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["b" /* successHandler */])(res, "searchbase");
          _this2.getLdap();
          _this2.searchValueId = "";
        }).catch(function (err) {
          Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(err, "searchbase");
          // console.log("error");
        });
      } else {
        this.deletePopup = false;
        __WEBPACK_IMPORTED_MODULE_3_store__["a" /* store */].dispatch("setAlert", {
          type: "success",
          message: "Successfully Deleted",
          component_name: "searchbase"
        });
        this.searchBaseArray.splice(this.indexValue, 1);
      }
    },


    /**
     * Method helps to close the modal pop
     */
    onClose: function onClose() {
      this.deletePopup = false;
      this.searchValueId = "";
    },


    //method use to validate the data
    isValidSearchBase: function isValidSearchBase() {
      var _validateLdapSettings = Object(__WEBPACK_IMPORTED_MODULE_4__validator_ldapSearchBaseRules_js__["a" /* validateLdapSettingsSearchBase */])(this.searchBaseArray),
          errors = _validateLdapSettings.errors,
          isValid = _validateLdapSettings.isValid;

      if (!isValid) {
        return false;
      }
      return true;
    },


    /**to save the query search base
     * @param {Array}
     * @param {Boolean}
     * @returns {void}
     */
    saveandImport: function saveandImport(data, status) {
      var _this3 = this;

      this.loading = true;
      var newdata = _.each(data, function (key) {
        if (key.user_type === "user") {
          key.department_ids = [];
        }
        if (key.user_type !== "user") {
          key.organization_ids = [];
        }
        if (key.departments) {
          key.department_ids = Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics_js__["h" /* extractOnlyId */])(key.departments);
        }
        if (key.organizations) {
          key.organization_ids = Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics_js__["h" /* extractOnlyId */])(key.organizations);
        }
      });
      if (this.isValidSearchBase()) {
        var SEARCH_BASE_API = '/api/ldap/search-bases/' + this.ldapId;
        __WEBPACK_IMPORTED_MODULE_0_axios___default.a.post(SEARCH_BASE_API, {
          search_bases: newdata,
          import: status
        }).then(function (res) {
          _this3.loading = false;
          Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["b" /* successHandler */])(res, "searchbase");
          _this3.getLdap();
        }).catch(function (err) {
          _this3.loading = false;
          Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(err, "searchbase");
        });
      } else {
        this.loading = false;
      }
    }
  },

  /**
   * Components required in this vue file
   */
  components: {
    "static-select": __webpack_require__(26),
    "text-field": __webpack_require__(11),
    "dynamic-select": __webpack_require__(14),
    alert: __webpack_require__(6),
    "custom-loader": __webpack_require__(9),
    modal: __webpack_require__(13),
    'faveo-box': __WEBPACK_IMPORTED_MODULE_5_components_MiniComponent_FaveoBox___default.a
  }
});

/***/ }),

/***/ 2458:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = validateLdapSettingsSearchBase;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_easy_validator_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);
/**
 * This Files contain validation specific to LdapSettings.vue only
 */




/**
 * @param {object} data      ldapsettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
function validateLdapSettingsSearchBase(data) {
  //rules has to apply only after checking conditions
  var validatingData = {};

  for (var i = 0; i < data.length; i++) {
    var x = [];
    x[0] = data[i].search_base;
    x[1] = 'isRequired';
    validatingData['searchbase' + [i]] = x;
  }

  //creating a validator instance and pasing lang method to it
  var validator = new __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__["Validator"](__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["q" /* lang */]);

  var _validator$validate = validator.validate(validatingData),
      errors = _validator$validate.errors,
      isValid = _validator$validate.isValid;

  // write to vuex if errors


  __WEBPACK_IMPORTED_MODULE_0_store__["a" /* store */].dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

  return { errors: errors, isValid: isValid };
}

/***/ }),

/***/ 2459:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("alert", { attrs: { componentName: "searchbase" } }),
      _vm._v(" "),
      _vm.loading === true
        ? _c(
            "div",
            { staticClass: "row" },
            [_c("custom-loader", { attrs: { duration: _vm.loadingSpeed } })],
            1
          )
        : _vm._e(),
      _vm._v(" "),
      _vm.deletePopup
        ? _c(
            "modal",
            {
              attrs: {
                classname: "modal-sm",
                containerStyle: _vm.containerStyle,
                showModal: _vm.deletePopup,
                onClose: _vm.onClose
              }
            },
            [
              _c("div", { attrs: { slot: "title" }, slot: "title" }, [
                _c("h4", { staticClass: "modal-title" }, [
                  _vm._v(_vm._s(_vm.lang("delete_search_base")))
                ])
              ]),
              _vm._v(" "),
              _c("div", { attrs: { slot: "fields" }, slot: "fields" }, [
                _c("span", [
                  _vm._v(_vm._s(_vm.trans("delete_confirmation_message")))
                ])
              ]),
              _vm._v(" "),
              _c("div", { attrs: { slot: "controls" }, slot: "controls" }, [
                _c(
                  "button",
                  {
                    staticClass: "btn btn-danger",
                    attrs: { type: "button" },
                    on: {
                      click: function($event) {
                        _vm.onSubmitDelete()
                      }
                    }
                  },
                  [
                    _c("i", {
                      class: _vm.iconClass,
                      attrs: { "aria-hidden": "true" }
                    }),
                    _vm._v("  " + _vm._s(_vm.lang("delete")))
                  ]
                )
              ])
            ]
          )
        : _vm._e(),
      _vm._v(" "),
      _c("faveo-box", { attrs: { title: _vm.lang("import_settings") } }, [
        _c(
          "div",
          _vm._l(_vm.searchBaseArray, function(user, index) {
            return _c(
              "div",
              { key: index, staticClass: "clearfix ldap-import-query" },
              [
                _c(
                  "div",
                  { staticClass: "row" },
                  [
                    _c("text-field", {
                      attrs: {
                        id: "searchbase-" + index,
                        label: _vm.lang("search_base"),
                        value: user.search_base,
                        type: "text",
                        name: "searchbase" + index,
                        onChange: _vm.onSearchBase,
                        classname: "col-sm-4",
                        required: true
                      }
                    }),
                    _vm._v(" "),
                    _c("text-field", {
                      attrs: {
                        id: "filter-" + index,
                        label: _vm.lang("filter"),
                        value: user.filter,
                        type: "text",
                        name: "filter-" + index,
                        onChange: _vm.onFilter,
                        classname: "col-sm-6",
                        hint: _vm.lang("ldap_filter_hint")
                      }
                    }),
                    _vm._v(" "),
                    _c("div", { staticClass: "form-group col-sm-2" }, [
                      _c("label", { attrs: { for: "actions" } }, [
                        _vm._v("Actions")
                      ]),
                      _vm._v(" "),
                      _c("div", [
                        user.search_base
                          ? _c(
                              "span",
                              {
                                staticClass: "btn btn-primary",
                                attrs: {
                                  id: "ping-" + index,
                                  title: "Ping User"
                                },
                                on: {
                                  click: function($event) {
                                    _vm.confirmLdapPing(
                                      user.search_base,
                                      user.filter
                                    )
                                  }
                                }
                              },
                              [_c("i", { staticClass: "fa fa-paper-plane" })]
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _c(
                          "span",
                          {
                            staticClass: "btn btn-danger",
                            attrs: {
                              id: "delete-" + index,
                              title: "Delete User"
                            },
                            on: {
                              click: function($event) {
                                _vm.deleteUser(user.id, index)
                              }
                            }
                          },
                          [_c("i", { staticClass: "fa fa-trash" })]
                        )
                      ])
                    ])
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "row" },
                  [
                    _vm.showRole
                      ? _c("static-select", {
                          attrs: {
                            id: "user-type-" + index,
                            label: _vm.lang("user_type"),
                            elements: _vm.usersArray,
                            name: index,
                            hideEmptySelect: true,
                            value: user.user_type,
                            classname: "col-sm-4",
                            onChange: _vm.onHandle,
                            required: true
                          }
                        })
                      : _vm._e(),
                    _vm._v(" "),
                    user.user_type !== "user" && _vm.showDepartment
                      ? [
                          _c("dynamic-select", {
                            key: "search-base-" + index + "-department",
                            attrs: {
                              id: "search-base-" + index + "-department",
                              apiEndpoint: "/api/dependency/departments",
                              label: _vm.lang("department"),
                              multiple: true,
                              name: index,
                              prePopulate: false,
                              classname: "col-sm-6",
                              value: user.departments,
                              onChange: _vm.onDepartmentChange
                            }
                          })
                        ]
                      : _vm._e(),
                    _vm._v(" "),
                    user.user_type === "user" && _vm.showOrganization
                      ? [
                          _c("dynamic-select", {
                            key: "search-base-" + index + "-organization",
                            attrs: {
                              id: "search-base-" + index + "-organization",
                              apiEndpoint: "/api/dependency/organizations",
                              label: _vm.lang("organizations"),
                              multiple: true,
                              name: index,
                              prePopulate: false,
                              classname: "col-sm-6",
                              value: user.organizations,
                              onChange: _vm.onOrganizationChange
                            }
                          })
                        ]
                      : _vm._e()
                  ],
                  2
                )
              ]
            )
          })
        ),
        _vm._v(" "),
        _c(
          "div",
          {
            staticClass: "card-footer",
            attrs: { slot: "actions" },
            slot: "actions"
          },
          [
            _c(
              "button",
              {
                staticClass: "btn btn-primary update-btn",
                attrs: { id: "query", disabled: _vm.loading },
                on: {
                  click: function($event) {
                    _vm.saveandImport(_vm.searchBaseArray, false)
                  }
                }
              },
              [
                _c("span", { staticClass: "fas fa-save" }),
                _vm._v(" " + _vm._s(_vm.lang("save")) + " ")
              ]
            ),
            _vm._v(" "),
            _c(
              "button",
              {
                staticClass: "btn btn-primary update-btn",
                attrs: { id: "importquery", disabled: _vm.loading },
                on: {
                  click: function($event) {
                    _vm.saveandImport(_vm.searchBaseArray, true)
                  }
                }
              },
              [
                _c("span", { staticClass: "fas fa-save" }),
                _vm._v(" " + _vm._s(_vm.lang("save_and_import")))
              ]
            ),
            _vm._v(" "),
            _c(
              "button",
              {
                staticClass: "btn btn-primary update-btn",
                attrs: { id: "importquery", disabled: _vm.loading },
                on: { click: _vm.addQuery }
              },
              [
                _c("span", { staticClass: "fas fa-plus" }),
                _vm._v(" " + _vm._s(_vm.lang("add_more")))
              ]
            )
          ]
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
    require("vue-hot-reload-api")      .rerender("data-v-d9b2a5b6", module.exports)
  }
}

/***/ }),

/***/ 2460:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2461)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2463)
/* template */
var __vue_template__ = __webpack_require__(2464)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-659f7f56"
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
Component.options.__file = "app/Plugins/Ldap/views/js/components/LdapTable.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-659f7f56", Component.options)
  } else {
    hotAPI.reload("data-v-659f7f56", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2461:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2462);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("7207f130", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-659f7f56\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./LdapTable.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-659f7f56\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./LdapTable.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2462:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.right[data-v-659f7f56]{\n\t\tfloat: right;\n}\n#toggle-list-button[data-v-659f7f56] {\n    cursor: pointer;\n}\n", ""]);

// exports


/***/ }),

/***/ 2463:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vuex__ = __webpack_require__(7);
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







/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'ldap-table',

	description: 'Ldap table component',

	props: {
		ldapId: {
			type: String,
			default: ''
		}
	},

	data: function data() {
		return {

			showModal: false,

			data: {
				id: 0,
				name: ''
			},

			/**
    * if the data is populated
    * @type {Boolean}
    */
			hasDataPopulated: false,

			/**
    * If the list is minimized
    * @type {Boolean}
    */
			minimized: true,

			/**
    * base url of the application
    * @type {String}
    */
			base: window.axios.defaults.baseURL,

			/**
    * columns required for datatable
    * @type {Array}
    */
			columns: ['name', 'created_at', 'updated_at', 'is_default', 'action'],

			options: {},

			/**
    * api url for ajax calls
    * @type {String}
    */
			apiUrl: '/api/dependency/ldap-directory-attributes/' + this.ldapId
		};
	},
	beforeMount: function beforeMount() {
		var _this = this;

		var that = this;
		this.options = {
			headings: {
				name: 'Name',
				created_at: 'Created At',
				updated_at: 'Updated At',
				is_default: 'Is default',
				action: 'Action'
			},
			texts: {
				filter: '',
				limit: ''
			},
			templates: {
				action: 'data-table-actions',
				is_default: 'data-table-is-default',
				created_at: function created_at(h, row) {
					return _this.formattedTime(row.created_at);
				},
				updated_at: function updated_at(h, row) {
					return _this.formattedTime(row.updated_at);
				}
			},
			sortable: ['name', 'created_at', 'updated_at', 'is_default'],
			filterable: ['name', 'created_at', 'updated_at'],
			pagination: {
				chunk: 5,
				nav: 'scroll'
			},
			requestAdapter: function requestAdapter(data) {
				return {
					sort_field: data.orderBy ? data.orderBy : 'is_default',
					sort_order: data.ascending ? 'desc' : 'asc',
					search_query: data.query.trim(),
					page: data.page,
					limit: data.limit
				};
			},
			responseAdapter: function responseAdapter(_ref) {
				var data = _ref.data;

				return {
					data: data.data.data.map(function (data) {

						data.edit_modal = 'api/ldap/ldap-directory-attribute/' + that.ldapId, data.delete_url = window.axios.defaults.baseURL + '/api/ldap/ldap-directory-attribute/' + data.id;

						data.active = data.active == '1' ? 'active' : 'inactive';

						return data;
					}),
					count: data.data.total
				};
			}
		};
	},


	methods: {

		/**
   * Toggles the list view
   * @return {undefined}
   */
		toggleList: function toggleList() {
			this.minimized = !this.minimized;
			if (!this.minimized) {
				this.hasDataPopulated = true;
			} else {
				this.hasDataPopulated = false;
			}
		},
		onClose: function onClose() {
			this.showModal = false;
			this.$store.dispatch('unsetValidationError');
		}
	},

	computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_3_vuex__["b" /* mapGetters */])(['formattedTime'])),

	components: {
		"alert": __webpack_require__(6),
		"custom-loader": __webpack_require__(9),
		'data-table': __webpack_require__(17),
		'data-table-modal': __webpack_require__(182),
		"tool-tip": __webpack_require__(25)
	}
});

/***/ }),

/***/ 2464:
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
          _c(
            "div",
            { staticClass: "card-header" },
            [
              _c("h3", { staticClass: "card-title" }, [
                _vm._v(_vm._s(_vm.lang("directory_attributes")))
              ]),
              _vm._v(" "),
              _c("tool-tip", {
                attrs: {
                  message: _vm.lang("directory_attributes_description"),
                  size: "large"
                }
              }),
              _vm._v(" "),
              _c(
                "a",
                {
                  attrs: { id: "toggle-list-button" },
                  on: { click: _vm.toggleList }
                },
                [
                  _vm.minimized
                    ? _c("span", {
                        staticClass: "pull-right glyphicon glyphicon-menu-down",
                        attrs: { title: _vm.lang("expand") }
                      })
                    : _c("span", {
                        staticClass: "pull-right glyphicon glyphicon-menu-up",
                        attrs: { title: _vm.lang("collapse") }
                      })
                ]
              )
            ],
            1
          ),
          _vm._v(" "),
          _c("div", { staticClass: "card-body" }, [
            _c(
              "div",
              { class: ["toggle", { "toggle-expand": _vm.hasDataPopulated }] },
              [
                _c("div", { staticClass: "box-header" }, [
                  _c("div", { staticClass: "row" }, [
                    !_vm.minimized
                      ? _c("div", { staticClass: "col-md-12" }, [
                          _c(
                            "a",
                            {
                              staticClass: "btn btn-primary right",
                              attrs: { href: "javascript:;" },
                              on: {
                                click: function($event) {
                                  _vm.showModal = true
                                }
                              }
                            },
                            [
                              _c("span", {
                                staticClass: "glyphicon glyphicon-plus"
                              }),
                              _vm._v(" " + _vm._s(_vm.lang("add_more")))
                            ]
                          )
                        ])
                      : _vm._e()
                  ])
                ]),
                _vm._v(" "),
                _vm.hasDataPopulated
                  ? _c("data-table", {
                      attrs: {
                        url: _vm.apiUrl,
                        dataColumns: _vm.columns,
                        option: _vm.options
                      }
                    })
                  : _vm._e()
              ],
              1
            )
          ]),
          _vm._v(" "),
          _vm.showModal
            ? _c("data-table-modal", {
                attrs: {
                  title: "create",
                  onClose: _vm.onClose,
                  showModal: _vm.showModal,
                  apiUrl: "api/ldap/ldap-directory-attribute/" + this.ldapId,
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
    require("vue-hot-reload-api")      .rerender("data-v-659f7f56", module.exports)
  }
}

/***/ }),

/***/ 2465:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      !_vm.hasDataPopulated || _vm.loading
        ? _c("custom-loader", { attrs: { duration: _vm.loadingSpeed } })
        : _vm._e(),
      _vm._v(" "),
      _c("alert", { attrs: { componentName: "ldap" } }),
      _vm._v(" "),
      _vm.hasDataPopulated
        ? _c(
            "div",
            [
              _c(
                "faveo-box",
                { attrs: { title: _vm.lang("ldap_configuration_settings") } },
                [
                  _vm.message
                    ? _c(
                        "h6",
                        {
                          staticClass: "alert alert-warning  text-uppercase",
                          attrs: { id: "ldap-configure-warning" }
                        },
                        [
                          _c("span", [
                            _c("i", {
                              staticClass: "fa fa-exclamation-triangle"
                            })
                          ]),
                          _vm._v(" "),
                          _c("span", [_vm._v(_vm._s(_vm.message))])
                        ]
                      )
                    : _vm._e(),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticStyle: { "margin-top": "2rem" } },
                    [
                      _c(
                        "faveo-box",
                        { attrs: { title: _vm.lang("connection_settings") } },
                        [
                          _c(
                            "div",
                            { staticClass: "row" },
                            [
                              _c("text-field", {
                                attrs: {
                                  label: _vm.lang("ldap_domain"),
                                  value: _vm.domain,
                                  type: "text",
                                  name: "domain",
                                  onChange: _vm.onChange,
                                  classname: "col-md-4",
                                  required: true,
                                  id: "domain"
                                }
                              }),
                              _vm._v(" "),
                              _c("text-field", {
                                attrs: {
                                  label: "Username",
                                  value: _vm.username,
                                  type: "text",
                                  name: "username",
                                  onChange: _vm.onChange,
                                  classname: "col-sm-4",
                                  required: true,
                                  id: "username"
                                }
                              }),
                              _vm._v(" "),
                              _c("text-field", {
                                attrs: {
                                  label: "Password",
                                  value: _vm.password,
                                  type: "password",
                                  name: "password",
                                  onChange: _vm.onChange,
                                  classname: "col-sm-4",
                                  required: true,
                                  id: "password"
                                }
                              }),
                              _vm._v(" "),
                              _c("static-select", {
                                attrs: {
                                  label: _vm.lang("ldap_schema"),
                                  elements: _vm.schemas,
                                  name: "schema",
                                  value: _vm.schema,
                                  classname: "col-sm-4",
                                  onChange: _vm.onChange,
                                  hint: _vm.lang("ldap_schema_description"),
                                  id: "schema"
                                }
                              }),
                              _vm._v(" "),
                              _c("text-field", {
                                attrs: {
                                  label: _vm.lang("port"),
                                  value: _vm.port,
                                  type: "number",
                                  name: "port",
                                  onChange: _vm.onChange,
                                  classname: "col-sm-4",
                                  hint: _vm.lang("ldap_port_hint"),
                                  id: "port"
                                }
                              }),
                              _vm._v(" "),
                              _c("static-select", {
                                attrs: {
                                  label: _vm.lang("encryption"),
                                  elements: _vm.encryptions,
                                  name: "encryption",
                                  value: _vm.encryption,
                                  classname: "col-sm-4",
                                  onChange: _vm.onChange,
                                  hint: _vm.lang("ldap_encryption_hint"),
                                  id: "encryption"
                                }
                              })
                            ],
                            1
                          )
                        ]
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticStyle: { "margin-top": "2rem" } },
                    [
                      _c(
                        "faveo-box",
                        { attrs: { title: _vm.lang("login_settings") } },
                        [
                          _c(
                            "div",
                            { staticClass: "row" },
                            [
                              _c("text-field", {
                                attrs: {
                                  label: _vm.lang("ldap_label"),
                                  value: _vm.ldap_label,
                                  type: "text",
                                  name: "ldap_label",
                                  onChange: _vm.onChange,
                                  classname: "col-sm-4",
                                  hint: _vm.lang("ldap_label_hint"),
                                  id: "label"
                                }
                              }),
                              _vm._v(" "),
                              _c("text-field", {
                                attrs: {
                                  label: _vm.lang("forgot_password_link"),
                                  value: _vm.forgot_password_link,
                                  type: "text",
                                  name: "forgot_password_link",
                                  onChange: _vm.onChange,
                                  classname: "col-sm-4",
                                  hint: _vm.lang(
                                    "forgot_password_link_description"
                                  ),
                                  id: "forgot-password-link"
                                }
                              }),
                              _vm._v(" "),
                              _c("text-field", {
                                attrs: {
                                  label: _vm.lang("username_prefix"),
                                  value: _vm.prefix,
                                  type: "text",
                                  name: "prefix",
                                  onChange: _vm.onChange,
                                  classname: "col-sm-4",
                                  hint: _vm.lang("username_prefix_description"),
                                  id: "prefix"
                                }
                              }),
                              _vm._v(" "),
                              _c("text-field", {
                                attrs: {
                                  label: _vm.lang("username_suffix"),
                                  value: _vm.suffix,
                                  type: "text",
                                  name: "suffix",
                                  onChange: _vm.onChange,
                                  classname: "col-sm-4",
                                  hint: _vm.lang("username_suffix_description"),
                                  id: "suffix"
                                }
                              })
                            ],
                            1
                          )
                        ]
                      )
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "div",
                    {
                      staticClass: "card-footer",
                      attrs: { slot: "actions" },
                      slot: "actions"
                    },
                    [
                      _c(
                        "button",
                        {
                          staticClass: "btn btn-primary",
                          attrs: {
                            id: "ldap-settings-submit",
                            disabled: _vm.loading
                          },
                          on: { click: _vm.saveConfiguration }
                        },
                        [
                          _c("span", { staticClass: "fas fa-save" }),
                          _vm._v(
                            " \n          " +
                              _vm._s(_vm.lang("save_configuration")) +
                              "\n        "
                          )
                        ]
                      )
                    ]
                  )
                ]
              ),
              _vm._v(" "),
              _vm.is_valid != 0
                ? _c("search-basis", {
                    attrs: {
                      "ldap-id": _vm.ldapId,
                      addUser: _vm.addUser,
                      searchBaseArray: _vm.search_bases,
                      getLdap: _vm.getldapSetting,
                      showOrganization: _vm.show_organization,
                      showDepartment: _vm.show_department,
                      showRole: _vm.show_role,
                      loadingValue: _vm.loading
                    }
                  })
                : _vm._e(),
              _vm._v(" "),
              _vm.is_valid != 0
                ? _c("user-import-mapper", {
                    attrs: {
                      "api-endpoint":
                        "/api/ldap/advanced-settings/" + _vm.ldapId,
                      updateParent: _vm.getldapSetting,
                      "table-headings": _vm.tableHeadings
                    }
                  })
                : _vm._e(),
              _vm._v(" "),
              _vm.is_valid != 0
                ? _c("ldap-table", { attrs: { "ldap-id": _vm.ldapId } })
                : _vm._e()
            ],
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
    require("vue-hot-reload-api")      .rerender("data-v-534d916a", module.exports)
  }
}

/***/ }),

/***/ 2466:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2467)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2469)
/* template */
var __vue_template__ = __webpack_require__(2470)
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
Component.options.__file = "app/Plugins/Ldap/views/js/components/LdapLogin.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-75abed71", Component.options)
  } else {
    hotAPI.reload("data-v-75abed71", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2467:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2468);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("6ad03f05", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-75abed71\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./LdapLogin.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-75abed71\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./LdapLogin.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2468:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.ldap-login-button{\n    margin-top: 5px;\n    margin-bottom: 3px;\n}\n.ldap-login-block{\n    text-align : left;\n    margin-bottom: 10px;\n}\n", ""]);

// exports


/***/ }),

/***/ 2469:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_vuex__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_axios__);
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
  props: ['data'],

  data: function data() {
    return {
      disabled: false,
      ldapSettings: null,
      login_via_ldap: false
    };
  },
  beforeMount: function beforeMount() {
    this.ldapSettings = JSON.parse(this.data).ldap_meta_settings;
    this.ldapSettings.hide_default_login && this.hideDefaultLogin();
  },
  mounted: function mounted() {
    var _this = this;

    // disable button if login is success
    window.eventHub.$on("login-success", function () {
      _this.disabled = true;
    });

    // enable button when login is failure and mark login_via_ldap as false, so when clicking
    // on default login button it doesn't assume that as true already
    window.eventHub.$on("login-failure", function () {
      _this.disabled = false;
      _this.login_via_ldap = false;
    });

    window.eventHub.$on("logging-in-with-enter-key", function () {
      if (document.getElementById('default-login-button').style.display == "none") {
        document.getElementById('ldap-login-button-0').click();
      }
    });
  },


  methods: {

    /**
     * If Only Ldap Login is allowed
     * @return {undefined}
     */
    hideDefaultLogin: function hideDefaultLogin() {

      // hiding default login button
      document.getElementById('default-login-button').style.display = "none";

      // hiding default forgot password
      document.getElementById('default-forgot-password').style.display = "none";
    },


    /**
     * Calls default login button simply
     * @return {undefined}
     */
    ldapLoginSubmit: function ldapLoginSubmit(ldapId) {
      var _this2 = this;

      this.login_via_ldap = true;

      window.eventHub.$on("login-data-submitting", function (params) {
        params.ldap = _this2.login_via_ldap;
        params.ldap_id = ldapId;
      });

      document.getElementById('default-login-button').click();
    }
  },

  computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_1_vuex__["b" /* mapGetters */])({ buttonStyle: 'getButtonStyle', linkStyle: 'getLinkStyle' }))
});

/***/ }),

/***/ 2470:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { attrs: { id: "ldap-login" } },
    _vm._l(_vm.ldapSettings.directory_settings, function(ldapSetting, index) {
      return _c("span", { staticClass: "ldap-login-block" }, [
        _c(
          "button",
          {
            staticClass: "ldap-login-button btn btn-custom btn-block btn-flat",
            style: _vm.buttonStyle,
            attrs: { id: "ldap-login-button-" + index, disabled: _vm.disabled },
            on: {
              click: function() {
                return _vm.ldapLoginSubmit(ldapSetting.id)
              }
            }
          },
          [
            _vm._v(
              "\n        " +
                _vm._s(
                  ldapSetting.ldap_label !== ""
                    ? ldapSetting.ldap_label
                    : _vm.lang("login_via_ldap")
                ) +
                "\n    "
            )
          ]
        ),
        _vm._v(" "),
        ldapSetting.forgot_password_link !== ""
          ? _c(
              "a",
              {
                style: _vm.linkStyle,
                attrs: {
                  id: "ldap-forgot-password-" + index,
                  href: ldapSetting.forgot_password_link,
                  target: "_blank"
                }
              },
              [
                _vm._v(
                  "\n          " +
                    _vm._s(_vm.lang("forgot_ldap_password")) +
                    "\n    "
                )
              ]
            )
          : _vm._e()
      ])
    })
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-75abed71", module.exports)
  }
}

/***/ })

},[2442]);