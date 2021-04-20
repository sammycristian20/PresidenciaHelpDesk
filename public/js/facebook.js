webpackJsonp([9],{

/***/ 2471:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2472);


/***/ }),

/***/ 2472:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_store__ = __webpack_require__(10);

var bootstrap = __webpack_require__(29);





var app = new Vue({
  el: '#facebook-settings',
  store: __WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */],
  components: {
    'facebook-settings': __webpack_require__(2473),
    'facebook-create-edit': __webpack_require__(2481),
    'facebook-general-settings': __webpack_require__(2486)
  }
});

/***/ }),

/***/ 2473:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2474)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2476)
/* template */
var __vue_template__ = __webpack_require__(2480)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-7133f6aa"
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
Component.options.__file = "app/Plugins/Facebook/views/js/components/FacebookSettings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7133f6aa", Component.options)
  } else {
    hotAPI.reload("data-v-7133f6aa", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2474:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2475);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("f45e6402", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7133f6aa\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./FacebookSettings.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7133f6aa\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./FacebookSettings.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2475:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.mr-2[data-v-7133f6aa] {\n    margin-right: 5px;\n}\n.page-name[data-v-7133f6aa]{\n    width:30% !important;\n    word-break: break-all;\n}\n.page-id[data-v-7133f6aa]{\n    width:30% !important;\n    word-break: break-all;\n}\n.page-active[data-v-7133f6aa] {\n    width:20% !important;\n    word-break: break-all;\n}\n.page-action[data-v-7133f6aa]{\n    width:20% !important;\n    word-break: break-all;\n}\n\n", ""]);

// exports


/***/ }),

/***/ 2476:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vuex__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_moment__ = __webpack_require__(19);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_moment___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_moment__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_vue__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_5_components_MiniComponent_FaveoBox__);
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









__WEBPACK_IMPORTED_MODULE_4_vue___default.a.component('switch-action', __webpack_require__(2477)); //for implementing on-off switch
__WEBPACK_IMPORTED_MODULE_4_vue___default.a.component('table-actions-actual', __webpack_require__(27));

/* harmony default export */ __webpack_exports__["default"] = ({

    name: 'FacebookSettings',
    description: 'Component listing all facebook pages',

    computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_0_vuex__["b" /* mapGetters */])(['formattedTime', 'formattedDate'])),

    data: function data() {
        return {

            apiUrl: 'facebook/api/integration',
            loading: false,
            columns: ['page_name', 'page_id', 'status', 'action'],
            options: {},
            dataTableVisible: true,
            duration: 4000

        };
    },


    methods: {
        addPage: function addPage() {
            this.redirect('/facebook/integration/create');
        },
        gotoSecurity: function gotoSecurity() {
            this.redirect('/facebook/security-settings');
        }
    },

    beforeMount: function beforeMount() {

        var self = this;

        this.options = {

            headings: { page_name: 'Name', page_id: 'Page ID', status: 'Status', action: 'Actions' },

            columnsClasses: {

                page_name: 'page-name',

                page_id: 'page-id',

                status: 'page-active',

                action: 'page-action'
            },

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            templates: {

                status: 'switch-action',
                action: 'table-actions-actual'
            },

            requestAdapter: function requestAdapter(data) {

                return {

                    'sort-field': data.orderBy ? data.orderBy : 'id',

                    'sort-order': data.ascending ? 'desc' : 'asc',

                    'search-query': data.query.trim(),

                    page: data.page,

                    limit: data.limit
                };
            },
            responseAdapter: function responseAdapter(_ref) {
                var data = _ref.data;


                return {
                    data: data.data.pages.map(function (data) {

                        data.delete_url = self.basePath() + '/facebook/api/integration/' + data.id;
                        data.edit_url = self.basePath() + '/facebook/integration/edit/' + data.id;
                        return data;
                    }),

                    count: data.data.total
                };
            },


            sortable: ['page_name', 'page_id'],

            filterable: ['page_name', 'page_id'],

            pagination: { chunk: 5, nav: 'fixed', edge: true }
        };
    },


    components: {

        "alert": __webpack_require__(6),
        "data-table": __webpack_require__(17),
        'faveo-box': __WEBPACK_IMPORTED_MODULE_5_components_MiniComponent_FaveoBox___default.a

    }
});

/***/ }),

/***/ 2477:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2478)
/* template */
var __vue_template__ = __webpack_require__(2479)
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
Component.options.__file = "app/Plugins/Facebook/views/js/components/SwitchAction.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1dfff2a8", Component.options)
  } else {
    hotAPI.reload("data-v-1dfff2a8", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2478:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FormField_Switch__ = __webpack_require__(38);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FormField_Switch___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FormField_Switch__);
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





/* harmony default export */ __webpack_exports__["default"] = ({
    components: {
        "status-switch": __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FormField_Switch___default.a
    },
    props: ['data'],

    methods: {
        onChange: function onChange(value, name) {
            this.statusChange(name);
        },
        statusChange: function statusChange(id) {
            axios.get('facebook/api/integration/status/' + id).then(function (res) {
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'dataTableModal');
                window.eventHub.$emit('refreshData');
            }).catch(function (err) {
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'dataTableModal');
            });
        }
    }

});

/***/ }),

/***/ 2479:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "btn-group" },
    [
      _c("status-switch", {
        key: Math.random(),
        attrs: {
          name: _vm.data.page_id,
          value: _vm.data.active,
          onChange: _vm.onChange
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
    require("vue-hot-reload-api")      .rerender("data-v-1dfff2a8", module.exports)
  }
}

/***/ }),

/***/ 2480:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _vm.loading
        ? _c(
            "div",
            { staticClass: "row" },
            [_c("custom-loader", { attrs: { duration: _vm.duration } })],
            1
          )
        : _vm._e(),
      _vm._v(" "),
      _c("alert", { attrs: { componentName: "dataTableModal" } }),
      _vm._v(" "),
      _c("faveo-box", { attrs: { title: _vm.lang("facebook_pages_list") } }, [
        _c(
          "div",
          {
            staticClass: "card-tools",
            attrs: { slot: "headerMenu" },
            slot: "headerMenu"
          },
          [
            _c(
              "button",
              {
                directives: [
                  {
                    name: "tooltip",
                    rawName: "v-tooltip",
                    value: _vm.lang("facebook_add_page"),
                    expression: "lang('facebook_add_page')"
                  }
                ],
                staticClass: "btn btn-tool",
                on: { click: _vm.addPage }
              },
              [_c("i", { staticClass: "fas fa-plus" })]
            ),
            _vm._v(" "),
            _c(
              "button",
              {
                directives: [
                  {
                    name: "tooltip",
                    rawName: "v-tooltip",
                    value: _vm.lang("facebook_go_to_security_settings"),
                    expression: "lang('facebook_go_to_security_settings')"
                  }
                ],
                staticClass: "btn btn-tool",
                on: {
                  click: function($event) {
                    $event.preventDefault()
                    return _vm.gotoSecurity($event)
                  }
                }
              },
              [_c("i", { staticClass: "fas fa-shield-alt" })]
            )
          ]
        ),
        _vm._v(" "),
        _c(
          "div",
          { attrs: { id: "page-view" } },
          [
            _vm.dataTableVisible
              ? _c("data-table", {
                  attrs: {
                    url: _vm.apiUrl,
                    dataColumns: _vm.columns,
                    option: _vm.options,
                    scroll_to: "page-view"
                  }
                })
              : _vm._e()
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
    require("vue-hot-reload-api")      .rerender("data-v-7133f6aa", module.exports)
  }
}

/***/ }),

/***/ 2481:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2482)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2484)
/* template */
var __vue_template__ = __webpack_require__(2485)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-67a6cfa4"
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
Component.options.__file = "app/Plugins/Facebook/views/js/components/FacebookCreateEdit.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-67a6cfa4", Component.options)
  } else {
    hotAPI.reload("data-v-67a6cfa4", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2482:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2483);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("dfe9cbba", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-67a6cfa4\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./FacebookCreateEdit.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-67a6cfa4\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./FacebookCreateEdit.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2483:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),

/***/ 2484:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__ = __webpack_require__(5);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

    name: "FacebookCreateEdit",

    components: {
        "text-field": __webpack_require__(11),
        'alert': __webpack_require__(6),
        "custom-loader": __webpack_require__(9),
        'dynamic-select': __webpack_require__(14),
        "modal": __webpack_require__(13),
        'faveo-box': __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox___default.a
    },

    props: {
        integrationData: {
            type: String,
            default: ''
        }
    },

    data: function data() {
        return {
            id: '',
            loadingSpeed: 4000,
            loading: false,
            required: true,
            page_access_token: '',
            page_name: '',
            page_id: '',
            elements_new_ticket: [{
                name: "One Day",
                value: "1"
            }, {
                name: "Five Days",
                value: "5"
            }, {
                name: "Ten Days",
                value: "10"
            }, {
                name: "Fifteen Days",
                value: "15"
            }, {
                name: "Thirty Days",
                value: "30"
            }]
        };
    },
    beforeMount: function beforeMount() {
        this.fillFieldsIfEdit();
    },


    methods: {
        filterValues: function filterValues(array, value) {
            return array.filter(function (x) {
                if (x.value == value) return x;
            })[0];
        },
        fillFieldsIfEdit: function fillFieldsIfEdit() {
            if (this.integrationData) {
                var integrationObject = JSON.parse(this.integrationData);
                this.id = integrationObject.id;
                this.page_access_token = integrationObject.page_access_token;
                this.page_id = integrationObject.page_id;
                this.page_name = integrationObject.page_name;
                this.new_ticket_interval = this.filterValues(this.elements_new_ticket, integrationObject.new_ticket_interval);
            }
        },
        onChange: function onChange(value, name) {
            this[name] = value;
        },
        submit: function submit() {
            var _this = this;

            this.loading = true;
            var formDataObj = {
                page_id: this.page_id,
                page_access_token: this.page_access_token,
                page_name: this.page_name,
                new_ticket_interval: this.new_ticket_interval ? this.new_ticket_interval.value : null,
                verify_token: this.verify_token
            };

            var url = void 0,
                method = void 0;
            if (this.id) {
                url = 'facebook/api/integration/' + this.id;
                method = 'PUT';
            } else {
                url = 'facebook/api/integration';
                method = 'POST';
            }

            __WEBPACK_IMPORTED_MODULE_1_axios___default.a.request({
                method: method,
                url: url,
                data: formDataObj
            }).then(function (res) {
                _this.loading = false;
                Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["b" /* successHandler */])(res, 'facebookCreateEdit');
                _this.loading = false;
                if (!_this.id) {
                    //means this form is create form
                    setTimeout(function () {
                        _this.redirect('/facebook/settings');
                    }, 1200);
                } else {
                    _this.$store.dispatch('unsetValidationError');
                }
            }).catch(function (err) {
                _this.loading = false;
                Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(err, 'facebookCreateEdit');
            });
        }
    }
});

/***/ }),

/***/ 2485:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _vm.loading === true
        ? _c(
            "div",
            { staticClass: "row" },
            [_c("custom-loader", { attrs: { duration: _vm.loadingSpeed } })],
            1
          )
        : _vm._e(),
      _vm._v(" "),
      _c("alert", { attrs: { componentName: "facebookCreateEdit" } }),
      _vm._v(" "),
      _c(
        "faveo-box",
        {
          attrs: {
            title: _vm.id
              ? _vm.lang("facebook_page_edit")
              : _vm.lang("facebook_page_create")
          }
        },
        [
          _c(
            "div",
            { staticClass: "row" },
            [
              _c("text-field", {
                attrs: {
                  label: _vm.lang("facebook_page_name"),
                  onChange: _vm.onChange,
                  value: _vm.page_name,
                  type: "text",
                  name: "page_name",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("facebook_page_name_hint"),
                  id: "facebook_page_name"
                }
              }),
              _vm._v(" "),
              _c("text-field", {
                attrs: {
                  label: _vm.lang("facebook_page_id"),
                  onChange: _vm.onChange,
                  value: _vm.page_id,
                  type: "text",
                  name: "page_id",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("facebook_page_id_hint"),
                  id: "page_id"
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
                  label: _vm.lang("facebook_page_access_token"),
                  onChange: _vm.onChange,
                  value: _vm.page_access_token,
                  type: "password",
                  name: "page_access_token",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("facebook_page_access_token_hint"),
                  id: "facebook_page_access_token"
                }
              }),
              _vm._v(" "),
              _c("dynamic-select", {
                attrs: {
                  name: "new_ticket_interval",
                  classname: "col-sm-6",
                  elements: _vm.elements_new_ticket,
                  multiple: false,
                  prePopulate: false,
                  label: _vm.lang("facebook_new_ticket_interval"),
                  value: _vm.new_ticket_interval,
                  onChange: _vm.onChange,
                  clearable: false,
                  searchable: false,
                  hint: _vm.lang("facebook_new_ticket_interval_hint"),
                  required: _vm.required
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
                { staticClass: "btn btn-primary", on: { click: _vm.submit } },
                [
                  _c("i", { staticClass: "fas fa-save" }),
                  _vm._v(
                    "\n                " +
                      _vm._s(_vm.lang("save")) +
                      "\n            "
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
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-67a6cfa4", module.exports)
  }
}

/***/ }),

/***/ 2486:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2487)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2489)
/* template */
var __vue_template__ = __webpack_require__(2490)
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
Component.options.__file = "app/Plugins/Facebook/views/js/components/FacebookAppSettings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3a9116e8", Component.options)
  } else {
    hotAPI.reload("data-v-3a9116e8", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2487:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2488);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("043c4f2a", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3a9116e8\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./FacebookAppSettings.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3a9116e8\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./FacebookAppSettings.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2488:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.mt-5 {\n  margin-top: 5rem;\n}\n.app-id{\n  width:17% !important;\n  word-break: break-all;\n}\n.app-secret {\n  width: 25% !important;\n  word-break: break-all;\n}\n.app-created {\n  width: 18%;\n  word-break: break-all;\n}\n.app-action {\n  width: 10%;\n  word-break : break-all;\n}\n.app-cron {\n  width: 10% !important;\n  word-break: break-all;\n}\n.app-new-ticket-interval{\n  width: 20% !important;\n  word-break: break-all;\n}\n.search {\n  display: none !important;\n}\n#H5{\n  margin-left:16px;\n  /*margin-bottom:18px !important;*/\n}\n\n", ""]);

// exports


/***/ }),

/***/ 2489:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_components_MiniComponent_FaveoBox__);
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

  computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_3_vuex__["b" /* mapGetters */])(['formattedTime', 'formattedDate'])),

  data: function data() {
    return {
      fb_secret: '',
      hub_verify_token: '',
      appExists: false,
      loadingSpeed: 4000,
      showModal: false,
      loading: false,
      settingsId: ''
    };
  },


  components: {
    "text-field": __webpack_require__(11),
    'alert': __webpack_require__(6),
    "custom-loader": __webpack_require__(9),
    'dynamic-select': __webpack_require__(14),
    "modal": __webpack_require__(13),
    "faveo-box": __WEBPACK_IMPORTED_MODULE_2_components_MiniComponent_FaveoBox___default.a,
    "tool-tip": __webpack_require__(25)
  },

  beforeMount: function beforeMount() {

    this.getAppDetails();
  },


  methods: {
    redirectToPages: function redirectToPages() {
      this.redirect('/facebook/pages/list');
    },
    onChange: function onChange(value, name) {
      this[name] = value;
    },
    gotoPageSettingsPage: function gotoPageSettingsPage() {
      this.redirect('/facebook/settings');
    },
    getAppDetails: function getAppDetails() {
      var _this = this;

      this.loading = true;
      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('facebook/api/security-settings/index').then(function (res) {
        var data = res.data.data;
        if (data) {
          _this.appExists = true;
          _this.fb_secret = data.fb_secret;
          _this.hub_verify_token = data.hub_verify_token;
          _this.settingsId = data.id;
          _this.loading = false;
        }
      }).catch(function (err) {
        Object.assign(_this.$data, _this.$options.data.apply(_this));
        _this.appExists = false;
        _this.loading = false;
        _this.hub_verify_token = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
      });
    },
    deleteFacebook: function deleteFacebook() {
      var _this2 = this;

      //for reset
      this.isLoading = true;
      this.isDisabled = true;
      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.delete("facebook/api/security-settings/delete/" + this.settingsId).then(function (res) {
        Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, "facebookAppSettings");
      }).catch(function (err) {
        Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, "facebookAppSettings");
      }).finally(function () {
        _this2.isLoading = false;
        _this2.showModal = false;
        _this2.getAppDetails();
      });
    },
    toggleModal: function toggleModal() {
      this.showModal = !this.showModal;
    },
    onClose: function onClose() {
      this.showModal = false;
    },
    submitter: function submitter() {
      var _this3 = this;

      var formDataObj = {
        fb_secret: this.fb_secret,
        hub_verify_token: this.hub_verify_token

      };

      this.loading = true;
      var url = void 0,
          method = void 0;

      if (this.appExists) {
        url = "facebook/api/security-settings/update/" + this.settingsId;
        method = 'PUT';
      } else {
        url = 'facebook/api/security-settings/create';
        method = 'POST';
      }

      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.request({
        method: method,
        url: url,
        data: formDataObj
      }).then(function (res) {
        Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'facebookAppSettings');
        _this3.$store.dispatch('unsetValidationError');
      }).catch(function (err) {
        Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'facebookAppSettings');
      }).finally(function () {
        _this3.loading = false;
        _this3.getAppDetails();
      });
    }
  }
});

/***/ }),

/***/ 2490:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _vm.loading === true
        ? _c(
            "div",
            { staticClass: "row" },
            [_c("custom-loader", { attrs: { duration: _vm.loadingSpeed } })],
            1
          )
        : _vm._e(),
      _vm._v(" "),
      _c("alert", { attrs: { componentName: "facebookAppSettings" } }),
      _vm._v(" "),
      _c(
        "faveo-box",
        { attrs: { title: _vm.lang("facebook_app_settings") } },
        [
          _c("tool-tip", {
            attrs: {
              slot: "headerTooltip",
              message: _vm.lang("facebook_security_tooltip"),
              size: "large"
            },
            slot: "headerTooltip"
          }),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "card-tools",
              attrs: { slot: "headerMenu" },
              slot: "headerMenu"
            },
            [
              _c(
                "button",
                {
                  staticClass: "btn btn-tool btn-default",
                  on: {
                    click: function($event) {
                      $event.preventDefault()
                      return _vm.gotoPageSettingsPage($event)
                    }
                  }
                },
                [
                  _c("i", { staticClass: "fab fa-facebook-square" }),
                  _vm._v(
                    "\n        " +
                      _vm._s(_vm.lang("facebook_go_to_page_settings")) +
                      "\n      "
                  )
                ]
              )
            ]
          ),
          _vm._v(" "),
          _c("div", [
            _c(
              "div",
              { staticClass: "row" },
              [
                _c("text-field", {
                  attrs: {
                    label: _vm.lang("facebook_verify_token"),
                    onChange: _vm.onChange,
                    value: _vm.hub_verify_token,
                    type: "text",
                    name: "hub_verify_token",
                    required: true,
                    classname: "col-sm-6",
                    hint: _vm.lang("facebook_verify_token_hint"),
                    id: "facebook_verify_token",
                    disabled: true
                  }
                }),
                _vm._v(" "),
                _c("text-field", {
                  attrs: {
                    label: _vm.lang("facebook_app_secret"),
                    onChange: _vm.onChange,
                    value: _vm.fb_secret,
                    type: "password",
                    name: "fb_secret",
                    required: true,
                    classname: "col-sm-6",
                    hint: _vm.lang("facebook_app_secret_hint"),
                    id: "facebook_app_secret"
                  }
                })
              ],
              1
            )
          ]),
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
                  on: {
                    click: function($event) {
                      $event.preventDefault()
                      return _vm.submitter($event)
                    }
                  }
                },
                [
                  _c("i", { staticClass: "fas fa-save" }),
                  _vm._v(" " + _vm._s(_vm.lang("facebook_save")) + "\n      ")
                ]
              ),
              _vm._v(" "),
              _c(
                "button",
                {
                  staticClass: "btn btn-primary",
                  attrs: { disabled: !_vm.appExists },
                  on: { click: _vm.toggleModal }
                },
                [
                  _c("i", { staticClass: "glyphicon glyphicon-repeat" }),
                  _vm._v(
                    " \n        " +
                      _vm._s(_vm.lang("facebook_reset")) +
                      "\n      "
                  )
                ]
              )
            ]
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "transition",
        { attrs: { name: "modal" } },
        [
          _vm.showModal
            ? _c(
                "modal",
                {
                  attrs: {
                    showModal: true,
                    onClose: function() {
                      return (_vm.showModal = false)
                    },
                    containerStyle: "width: 500px"
                  }
                },
                [
                  _c("div", { attrs: { slot: "title" }, slot: "title" }, [
                    _c("h4", [_vm._v(_vm._s(_vm.lang("facebook_reset")))])
                  ]),
                  _vm._v(" "),
                  !_vm.isLoading
                    ? _c("div", { attrs: { slot: "fields" }, slot: "fields" }, [
                        _c("h5", { attrs: { id: "H5" } }, [
                          _vm._v(
                            "\n          " +
                              _vm._s(_vm.lang("facebook_reset_confirm")) +
                              "\n        "
                          )
                        ])
                      ])
                    : _vm._e(),
                  _vm._v(" "),
                  _vm.isLoading
                    ? _c(
                        "div",
                        {
                          staticClass: "row",
                          attrs: { slot: "fields" },
                          slot: "fields"
                        },
                        [
                          _c("loader", {
                            class: { spin: _vm.lang_locale === "ar" },
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
                        attrs: { type: "button" },
                        on: { click: _vm.deleteFacebook }
                      },
                      [
                        _c("i", {
                          staticClass: "glyphicon glyphicon-repeat",
                          attrs: { "aria-hidden": "true" }
                        }),
                        _vm._v(
                          "\n           " +
                            _vm._s(_vm.lang("reset")) +
                            "\n        "
                        )
                      ]
                    )
                  ])
                ]
              )
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
    require("vue-hot-reload-api")      .rerender("data-v-3a9116e8", module.exports)
  }
}

/***/ })

},[2471]);