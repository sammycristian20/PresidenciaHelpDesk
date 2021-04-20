webpackJsonp([12],{

/***/ 2889:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2890);


/***/ }),

/***/ 2890:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_store__ = __webpack_require__(10);

var bootstrap = __webpack_require__(29);





var app = new Vue({
    el: '#azure-active-directory-settings',
    store: __WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */],
    components: {
        'azure-active-directory-settings': __webpack_require__(2891),
        'azure-active-directory-index': __webpack_require__(2897)
    }
});

bootstrap.injectComponentIntoView('azure-login', __webpack_require__(2902), 'login-box-mounted', 'login-box');

/***/ }),

/***/ 2891:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2892)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2894)
/* template */
var __vue_template__ = __webpack_require__(2896)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-48499a6b"
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
Component.options.__file = "app/Plugins/AzureActiveDirectory/views/js/components/AzureActiveDirectorySettings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-48499a6b", Component.options)
  } else {
    hotAPI.reload("data-v-48499a6b", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2892:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2893);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("069d10fe", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-48499a6b\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AzureActiveDirectorySettings.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-48499a6b\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AzureActiveDirectorySettings.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2893:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),

/***/ 2894:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__validator_azureActiveDirectoryRules__ = __webpack_require__(2895);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__resources_assets_js_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__resources_assets_js_helpers_extraLogics__ = __webpack_require__(4);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    name: "AzureActiveDirectorySettings",

    data: function data() {
        return {
            app_name: '',
            app_id: '',
            app_secret: '',
            tenant_id: '',
            login_button_label: '',
            hasDataPopulated: true,
            loading: false,
            azureAdId: null
        };
    },

    beforeMount: function beforeMount() {
        // if edit mode
        var azureAdId = Object(__WEBPACK_IMPORTED_MODULE_4__resources_assets_js_helpers_extraLogics__["m" /* getIdFromUrl */])(window.location.pathname);
        this.azureAdId = azureAdId || '';
        if (this.azureAdId) {
            this.getConfiguration();
        }
    },


    methods: {

        /**
         * populates the states corresponding to 'name' with 'value'
         * @param  {string} value
         * @param  {[type]} name
         * @return {void}
         */
        onChange: function onChange(value, name) {
            this[name] = value;
        },


        /**check if the validations are proper
         * @returns {Boolean}
         */
        isValid: function isValid() {
            return Object(__WEBPACK_IMPORTED_MODULE_1__validator_azureActiveDirectoryRules__["a" /* validateAzureSettings */])(this.$data).isValid;
        },
        saveConfiguration: function saveConfiguration() {
            var _this = this;

            var shallImport = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

            if (this.isValid()) {
                this.loading = true;
                __WEBPACK_IMPORTED_MODULE_2_axios___default.a.post("api/azure-active-directory/settings", {
                    id: this.azureAdId,
                    app_name: this.app_name,
                    tenant_id: this.tenant_id,
                    app_id: this.app_id,
                    app_secret: this.app_secret,
                    login_button_label: this.login_button_label,
                    import: shallImport
                }).then(function (res) {
                    _this.loading = false;
                    _this.hasDataPopulated = true;
                    Object(__WEBPACK_IMPORTED_MODULE_3__resources_assets_js_helpers_responseHandler__["b" /* successHandler */])(res, 'AzureActiveDirectorySettings');
                    if (_this.azureAdId) {
                        _this.redirect('/azure-active-directory/settings');
                    }
                    _this.azureAdId = res.data.data.azure_ad_id;
                    _this.getConfiguration();
                }).catch(function (err) {
                    console.log(err);
                    _this.loading = false;
                    Object(__WEBPACK_IMPORTED_MODULE_3__resources_assets_js_helpers_responseHandler__["a" /* errorHandler */])(err, 'AzureActiveDirectorySettings');
                }).finally(function () {
                    _this.loading = false;
                });
            }
        },
        getConfiguration: function getConfiguration() {
            var _this2 = this;

            this.loading = true;

            __WEBPACK_IMPORTED_MODULE_2_axios___default.a.get('api/azure-active-directory/settings/' + this.azureAdId).then(function (res) {
                _this2.app_name = res.data.data.app_name;
                _this2.tenant_id = res.data.data.tenant_id;
                _this2.app_id = res.data.data.app_id;
                _this2.app_secret = res.data.data.app_secret;
                _this2.login_button_label = res.data.data.login_button_label;
            }).catch(function (err) {
                Object(__WEBPACK_IMPORTED_MODULE_3__resources_assets_js_helpers_responseHandler__["a" /* errorHandler */])(err, 'AzureActiveDirectorySettings');
            }).finally(function () {
                _this2.loading = false;
            });
        }
    },

    components: {
        "static-select": __webpack_require__(26),
        "text-field": __webpack_require__(11),
        alert: __webpack_require__(6),
        "custom-loader": __webpack_require__(9),
        checkbox: __webpack_require__(41),
        'faveo-box': __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox___default.a
    }
});

/***/ }),

/***/ 2895:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = validateAzureSettings;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_easy_validator_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);




/**
 * @param {object} data      ldapsettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
function validateAzureSettings(data) {

    //rules has to apply only after checking conditions
    var appName = data.appName,
        appId = data.appId,
        appSecret = data.appSecret,
        tenantId = data.tenantId;

    var validatingData = {
        appName: [appName, 'isRequired'],
        appId: [appId, 'isRequired'],
        appSecret: [appSecret, 'isRequired'],
        tenantId: [tenantId, 'isRequired']
    };

    //creating a validator instance and pasing lang method to it
    var validator = new __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__["Validator"](__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["q" /* lang */]);

    var _validator$validate = validator.validate(validatingData),
        errors = _validator$validate.errors,
        isValid = _validator$validate.isValid;

    // write to vuex if errors


    __WEBPACK_IMPORTED_MODULE_0_store__["a" /* store */].dispatch('setValidationError', errors);

    return { errors: errors, isValid: isValid };
}

/***/ }),

/***/ 2896:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _vm.hasDataPopulated === false || _vm.loading === true
        ? _c("div", { staticClass: "row" }, [_c("custom-loader")], 1)
        : _vm._e(),
      _vm._v(" "),
      _c("alert", { attrs: { componentName: "AzureActiveDirectorySettings" } }),
      _vm._v(" "),
      _vm.hasDataPopulated
        ? _c(
            "div",
            [
              _c(
                "faveo-box",
                { attrs: { title: _vm.lang("configuration_settings") } },
                [
                  _c(
                    "div",
                    { staticClass: "row" },
                    [
                      _c("text-field", {
                        attrs: {
                          label: _vm.trans("app_name"),
                          value: _vm.app_name,
                          type: "text",
                          name: "app_name",
                          onChange: _vm.onChange,
                          classname: "col-md-6",
                          required: true,
                          id: "app-name"
                        }
                      }),
                      _vm._v(" "),
                      _c("text-field", {
                        attrs: {
                          label: _vm.lang("tenant_id"),
                          value: _vm.tenant_id,
                          type: "text",
                          name: "tenant_id",
                          onChange: _vm.onChange,
                          classname: "col-sm-6",
                          required: true,
                          id: "tenant-id"
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
                          label: _vm.trans("app_id"),
                          value: _vm.app_id,
                          type: "text",
                          name: "app_id",
                          onChange: _vm.onChange,
                          classname: "col-sm-6",
                          required: true,
                          id: "app-id"
                        }
                      }),
                      _vm._v(" "),
                      _c("text-field", {
                        attrs: {
                          label: _vm.trans("app_secret"),
                          value: _vm.app_secret,
                          type: "password",
                          name: "app_secret",
                          onChange: _vm.onChange,
                          classname: "col-sm-6",
                          required: true,
                          id: "app-secret"
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
                          label: _vm.trans("login_button_label"),
                          value: _vm.login_button_label,
                          type: "text",
                          name: "login_button_label",
                          onChange: _vm.onChange,
                          classname: "col-sm-6",
                          id: "login-button-label"
                        }
                      }),
                      _vm._v(" "),
                      _c("text-field", {
                        attrs: {
                          label: _vm.trans("azure_redirect_uri"),
                          hint: _vm.trans("azure_redirect_uri_hint"),
                          value:
                            _vm.basePath() +
                            "/azure-active-directory/auth-token/callback",
                          type: "text",
                          name: "login_button_label",
                          onChange: _vm.onChange,
                          classname: "col-sm-6",
                          disabled: "true"
                        }
                      })
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
                            id: "azure-settings-save",
                            disabled: _vm.loading
                          },
                          on: {
                            click: function() {
                              return _vm.saveConfiguration(false)
                            }
                          }
                        },
                        [
                          _c("span", { staticClass: "fas fa-save" }),
                          _vm._v(
                            " \n                    " +
                              _vm._s(_vm.lang("save")) +
                              "\n                "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "button",
                        {
                          staticClass: "btn btn-primary",
                          attrs: {
                            id: "azure-settings-submit",
                            disabled: _vm.loading
                          },
                          on: {
                            click: function() {
                              return _vm.saveConfiguration(true)
                            }
                          }
                        },
                        [
                          _c("span", { staticClass: "fas fa-save" }),
                          _vm._v(
                            " \n                    " +
                              _vm._s(_vm.lang("save_and_import")) +
                              "\n                "
                          )
                        ]
                      )
                    ]
                  )
                ]
              )
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
    require("vue-hot-reload-api")      .rerender("data-v-48499a6b", module.exports)
  }
}

/***/ }),

/***/ 2897:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2898)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2900)
/* template */
var __vue_template__ = __webpack_require__(2901)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-4df8898c"
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
Component.options.__file = "app/Plugins/AzureActiveDirectory/views/js/components/AzureActiveDirectoryIndex.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4df8898c", Component.options)
  } else {
    hotAPI.reload("data-v-4df8898c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2898:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2899);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("81dcc622", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4df8898c\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AzureActiveDirectoryIndex.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4df8898c\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AzureActiveDirectoryIndex.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2899:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.right[data-v-4df8898c]{\n    float: right;\n}\n.hide-checkbox[data-v-4df8898c]{ height : 25px;\n}\n\n", ""]);

// exports


/***/ }),

/***/ 2900:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vuex__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox__);
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







/* harmony default export */ __webpack_exports__["default"] = ({

    name: "AzureActiveDirectoryIndex",

    data: function data() {
        var self = this;
        return {

            hideDefaultLogin: false,

            columns: ['app_name', 'app_id', 'created_at', 'updated_at', 'action'],

            options: {
                texts: { filter: '', limit: '' },

                headings: {
                    app_name: 'App Name',
                    app_id: 'App Id',
                    created_at: 'Created At',
                    updated_at: 'Updated At',
                    action: 'Action'
                },

                templates: {
                    action: 'data-table-actions',

                    created_at: function created_at(h, row) {

                        return self.formattedTime(row.created_at);
                    },
                    updated_at: function updated_at(h, row) {

                        return self.formattedTime(row.updated_at);
                    }
                },

                sortable: ['app_name', 'app_id', 'created_at', 'updated_at'],

                filterable: ['app_name', 'app_id', 'created_at', 'updated_at'],

                pagination: { chunk: 5, nav: 'fixed', edge: true },

                requestAdapter: function requestAdapter(data) {
                    return {
                        sort_field: data.orderBy ? data.orderBy : 'id',
                        sort_order: data.ascending ? 'desc' : 'asc',
                        search_query: data.query.trim(),
                        page: data.page,
                        limit: data.limit
                    };
                },
                responseAdapter: function responseAdapter(_ref) {
                    var _this = this;

                    var data = _ref.data;

                    self.hideDefaultLogin = data.data.hide_default_login;

                    return {
                        data: data.data.directories.data.map(function (data) {

                            data.edit_url = _this.basePath() + '/azure-active-directory/' + data.id + '/edit';

                            data.delete_url = _this.basePath() + '/api/azure-active-directory/settings/' + data.id;

                            return data;
                        }),
                        count: data.data.directories.total
                    };
                }
            },

            /**
             * api url for ajax calls
             * @type {String}
             */
            apiUrl: 'api/azure-active-directory/settings'
        };
    },


    methods: {
        updateHideDefaultLogin: function updateHideDefaultLogin() {
            var _this2 = this;

            this.isLoading = true;
            var params = {
                hide_default_login: this.hideDefaultLogin
            };
            __WEBPACK_IMPORTED_MODULE_1_axios___default.a.post('api/azure-active-directory/hide-default-login', params).then(function (response) {
                Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["b" /* successHandler */])(response, 'azure-list-page');
            }).catch(function (error) {
                Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(error, 'azure-list-page');
            }).finally(function () {
                _this2.isLoading = false;
            });
        }
    },

    computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_0_vuex__["b" /* mapGetters */])(['formattedTime'])),

    components: {
        'data-table': __webpack_require__(17),
        "alert": __webpack_require__(6),
        'faveo-box': __WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox___default.a
    }
});

/***/ }),

/***/ 2901:
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
        "faveo-box",
        { attrs: { title: _vm.trans("list_of_azure_active_directories") } },
        [
          _c("alert", { attrs: { componentName: "azure-list-page" } }),
          _vm._v(" "),
          _vm.isLoading
            ? _c("loader", { attrs: { duration: 4000 } })
            : _vm._e(),
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
                      value: _vm.trans("create_new_directory"),
                      expression: "trans('create_new_directory')"
                    }
                  ],
                  staticClass: "btn btn-tool",
                  attrs: {
                    href: _vm.basePath() + "/azure-active-directory/create"
                  }
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
                              $$i < 0 &&
                                (_vm.hideDefaultLogin = $$a.concat([$$v]))
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
    require("vue-hot-reload-api")      .rerender("data-v-4df8898c", module.exports)
  }
}

/***/ }),

/***/ 2902:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2903)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2905)
/* template */
var __vue_template__ = __webpack_require__(2906)
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
Component.options.__file = "app/Plugins/AzureActiveDirectory/views/js/components/AzureLogin.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-e2c3196c", Component.options)
  } else {
    hotAPI.reload("data-v-e2c3196c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2903:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2904);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("02a640e8", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-e2c3196c\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AzureLogin.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-e2c3196c\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./AzureLogin.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2904:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.azure-login-button{\n    margin-top: 5px;\n    margin-bottom: 3px;\n}\n.azure-login-block{\n    text-align : left;\n    margin-bottom: 10px;\n}\n", ""]);

// exports


/***/ }),

/***/ 2905:
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




/* harmony default export */ __webpack_exports__["default"] = ({

    props: ['data'],

    data: function data() {
        return {
            azureSettings: null
        };
    },
    beforeMount: function beforeMount() {
        this.azureSettings = JSON.parse(this.data).azure_meta_settings;
        this.azureSettings.hide_default_login && this.hideDefaultLogin();
    },


    methods: {

        /**
         * Calls default login button simply
         * @return {undefined}
         */
        azureLoginSubmit: function azureLoginSubmit(redirectUrl) {
            // redirect to the redirect URL
            window.location = redirectUrl;
        },
        hideDefaultLogin: function hideDefaultLogin() {
            document.getElementById('user_name').style.display = 'none';
            document.getElementById('password').style.display = 'none';
            document.getElementById('remember_me').style.display = 'none';
            document.getElementById('default-login-button').style.display = 'none';
            document.getElementById('default-forgot-password').style.display = 'none';
        }
    },

    computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_0_vuex__["b" /* mapGetters */])({ buttonStyle: 'getButtonStyle', linkStyle: 'getLinkStyle' }))
});

/***/ }),

/***/ 2906:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.azureSettings
    ? _c(
        "div",
        { attrs: { id: "azure-login" } },
        [
          _vm.azureSettings.hide_default_login
            ? _c("img", {
                staticStyle: { margin: "-60px 0 -10px 92px" },
                attrs: {
                  src: _vm.basePath() + "/images/azure.png",
                  alt: _vm.trans("azure_active_directory"),
                  width: "256",
                  height: "256"
                }
              })
            : _vm._e(),
          _vm._v(" "),
          _vm._l(_vm.azureSettings.directory_settings, function(
            azureSetting,
            index
          ) {
            return _c("span", { staticClass: "azure-login-block" }, [
              _c(
                "button",
                {
                  staticClass:
                    "azure-login-button btn btn-custom btn-block btn-flat",
                  style: _vm.buttonStyle,
                  attrs: { id: "azure-login-button-" + index },
                  on: {
                    click: function() {
                      return _vm.azureLoginSubmit(azureSetting.login_url)
                    }
                  }
                },
                [
                  _c("i", {
                    staticClass: "fa fa-server",
                    attrs: { "aria-hidden": "true" }
                  }),
                  _vm._v(
                    "\n        " +
                      _vm._s(
                        azureSetting.login_button_label !== ""
                          ? azureSetting.login_button_label
                          : _vm.lang("login_via_azure")
                      ) +
                      "\n    "
                  )
                ]
              )
            ])
          })
        ],
        2
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-e2c3196c", module.exports)
  }
}

/***/ })

},[2889]);