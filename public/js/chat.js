webpackJsonp([10],{

/***/ 2619:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2620);


/***/ }),

/***/ 2620:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_store__ = __webpack_require__(10);
var bootstrap = __webpack_require__(29);





var app = new Vue({
    el: '#chat-settings',
    store: __WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */],
    components: {
        'chat-settings': __webpack_require__(2621),
        'chat-edit': __webpack_require__(2634)
    }
});

/***/ }),

/***/ 2621:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2622)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2624)
/* template */
var __vue_template__ = __webpack_require__(2633)
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
Component.options.__file = "app/Plugins/Chat/views/js/components/ChatSettings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0c5a3f2a", Component.options)
  } else {
    hotAPI.reload("data-v-0c5a3f2a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2622:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2623);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("34123efd", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0c5a3f2a\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ChatSettings.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0c5a3f2a\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ChatSettings.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2623:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.mr-2 {\n    margin-right: 5px;\n}\n.name{\n    width:15% !important;\n    word-break: break-all;\n}\n.url{\n    width:39% !important;\n    word-break: break-all;\n}\n.status {\n    width:10% !important;\n    word-break: break-all;\n}\n.action{\n    width:10% !important;\n    word-break: break-all;\n}\n.helptopic {\n    width: 13% !important;\n    word-break: break-all;\n}\n.department {\n    width: 13% !important;\n    word-break: break-all;\n}\n    \n", ""]);

// exports


/***/ }),

/***/ 2624:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vuex__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_vue__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_vue__);
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








__WEBPACK_IMPORTED_MODULE_4_vue___default.a.component('table-actions-clone', __webpack_require__(2625)); //for implementing on-off switch
__WEBPACK_IMPORTED_MODULE_4_vue___default.a.component('table-actions-actual', __webpack_require__(27));
__WEBPACK_IMPORTED_MODULE_4_vue___default.a.component('click-to-copy', __webpack_require__(2628));

/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        return {
            apiUrl: 'chat/api/chats',
            loading: false,
            columns: ['name', 'url', 'department', 'helptopic', 'status', 'action'],
            options: {},
            duration: 4000
        };
    },


    computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_0_vuex__["b" /* mapGetters */])(['formattedTime', 'formattedDate'])),

    methods: {
        subString: function subString(value) {
            var length = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 30;


            return Object(__WEBPACK_IMPORTED_MODULE_3_helpers_extraLogics__["o" /* getSubStringValue */])(value, length);
        }
    },

    beforeMount: function beforeMount() {

        var self = this;
        this.options = {

            headings: { name: 'Name', url: 'URL', status: 'Status', action: 'Action', helptopic: "HelpTopic", department: "Department" },

            columnsClasses: {

                name: 'name',
                url: 'url',
                status: 'status',
                action: 'action',
                department: 'department',
                helptopic: 'helptopic'

            },

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            templates: {

                url: 'click-to-copy',

                department: function department(h, row, index) {
                    if (!row.department) return "--";
                    return row.department.name;
                },

                helptopic: function helptopic(h, row, index) {
                    if (!row.helptopic) return "--";
                    return row.helptopic.name;
                },

                status: 'table-actions-clone',
                action: 'table-actions-actual'

            },
            requestAdapter: function requestAdapter(data) {

                return {

                    'sort_field': data.orderBy ? data.orderBy : 'id',

                    'sort_order': data.ascending ? 'desc' : 'asc',

                    'search_term': data.query.trim(),

                    page: data.page,

                    limit: data.limit
                };
            },
            responseAdapter: function responseAdapter(_ref) {
                var data = _ref.data;

                return {
                    data: data.data.chats.map(function (data) {
                        data.edit_url = self.basePath() + '/chat/edit/' + data.id;
                        data.textToCopy = data.url;
                        return data;
                    }),
                    count: data.data.total
                };
            },

            sortable: ['name', 'department', 'helptopic'],

            filterable: ['name', 'department', 'helptopic'],

            pagination: { chunk: 5, nav: 'fixed', edge: true }
        };
    },

    components: {
        "alert": __webpack_require__(6),
        "data-table": __webpack_require__(17)
    }

});

/***/ }),

/***/ 2625:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2626)
/* template */
var __vue_template__ = __webpack_require__(2627)
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
Component.options.__file = "app/Plugins/Chat/views/js/components/ChatSwitchHolder.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7c890fc8", Component.options)
  } else {
    hotAPI.reload("data-v-7c890fc8", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2626:
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
            axios.get('chat/api/status/' + id).then(function (res) {
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'dataTableModal');
                window.eventHub.$emit('refreshData');
            }).catch(function (err) {
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'dataTableModal');
            });
        }
    }

});

/***/ }),

/***/ 2627:
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
          name: _vm.data.id,
          value: _vm.data.status,
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
    require("vue-hot-reload-api")      .rerender("data-v-7c890fc8", module.exports)
  }
}

/***/ }),

/***/ 2628:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2629)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2631)
/* template */
var __vue_template__ = __webpack_require__(2632)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-e9b7c770"
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
Component.options.__file = "resources/assets/js/components/MiniComponent/ClickToCopy.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-e9b7c770", Component.options)
  } else {
    hotAPI.reload("data-v-e9b7c770", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2629:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2630);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("489ed61e", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-e9b7c770\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ClickToCopy.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-e9b7c770\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ClickToCopy.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2630:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.url-paragraph[data-v-e9b7c770] {\n    display: inline !important;\n}\n.green[data-v-e9b7c770] {\n    color: green;\n}\n.unstyled-button[data-v-e9b7c770] {\n    border: none;\n    padding: 0;\n    background: none;\n    outline: none !important;\n}\n\n", ""]);

// exports


/***/ }),

/***/ 2631:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_clipboard_copy__ = __webpack_require__(207);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_clipboard_copy___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_clipboard_copy__);
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





/* harmony default export */ __webpack_exports__["default"] = ({
    name: 'click-to-copy',
    description: 'Reusable component which handles copying of text content to clipboard on click.',
    props: {
        //Components other than Datatable which are willing to use this, should send props like this {textToCopy:'Text to copy'}
        //If textToCopy property is not set, this wont work
        data: {
            type: Object,
            required: true
        }
    },
    data: function data() {
        return {
            copied: false
        };
    },

    methods: {
        copyToClipboard: function copyToClipboard() {
            __WEBPACK_IMPORTED_MODULE_0_clipboard_copy___default()(this.data.textToCopy);
            this.copied = true;
            this.showCopyIconAfterCopying();
        },
        subString: function subString(value) {
            var length = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 15;

            return Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["o" /* getSubStringValue */])(value, length);
        },
        showCopyIconAfterCopying: function showCopyIconAfterCopying() {
            var _this = this;

            //for showing copy icon after 5 seconds.
            setTimeout(function () {
                _this.copied = false;
            }, 5000);
        }
    }
});

/***/ }),

/***/ 2632:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("div", { staticClass: "wrapping-div" }, [
      _c("p", { staticClass: "text-left url-paragraph" }, [
        _vm._v(
          _vm._s(
            _vm.data.textToCopy ? _vm.subString(_vm.data.textToCopy, 50) : "--"
          )
        )
      ]),
      _vm._v(" "),
      _vm.data.textToCopy
        ? _c(
            "button",
            {
              staticClass: "pull-right unstyled-button",
              attrs: { title: "Click to copy" },
              on: {
                click: function($event) {
                  $event.preventDefault()
                  return _vm.copyToClipboard($event)
                }
              }
            },
            [
              _c("i", {
                class: _vm.copied
                  ? "glyphicon glyphicon-ok green"
                  : "fa fa-clipboard"
              })
            ]
          )
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
    require("vue-hot-reload-api")      .rerender("data-v-e9b7c770", module.exports)
  }
}

/***/ }),

/***/ 2633:
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
        _c("div", { staticClass: "card-header" }, [
          _c("h3", { staticClass: "card-title" }, [
            _vm._v(" " + _vm._s(_vm.lang("chat_settings")))
          ])
        ]),
        _vm._v(" "),
        _c(
          "div",
          { staticClass: "card-body", attrs: { id: "chat-view" } },
          [
            _c("data-table", {
              attrs: {
                url: _vm.apiUrl,
                dataColumns: _vm.columns,
                option: _vm.options,
                scroll_to: "chat-view"
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
    require("vue-hot-reload-api")      .rerender("data-v-0c5a3f2a", module.exports)
  }
}

/***/ }),

/***/ 2634:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2635)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2637)
/* template */
var __vue_template__ = __webpack_require__(2638)
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
Component.options.__file = "app/Plugins/Chat/views/js/components/ChatEdit.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-be99425c", Component.options)
  } else {
    hotAPI.reload("data-v-be99425c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2635:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2636);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("967e188e", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-be99425c\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ChatEdit.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-be99425c\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ChatEdit.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2636:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.search {\n    display: none !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2637:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
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
//
//
//
//
//
//
//
//
//
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
    props: ['id'],
    data: function data() {

        return {
            departmentUrl: 'api/dependency/departments?meta=true',
            helptopicUrl: 'api/dependency/help-topics?meta=true',
            secret_key: '',
            secret_key_required: false,
            department: '',
            helptopic: '',
            required: true,
            url: '',
            depUrlBit: '',
            helpUrlBit: '',
            name: '',
            script: ''
        };
    },

    computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_2_vuex__["b" /* mapGetters */])(['formattedTime', 'formattedDate'])),

    watch: {
        department: function department(nv) {
            this.depUrlBit = nv.id;
            this.generateUrl();
        },
        helptopic: function helptopic(nv) {
            this.helpUrlBit = nv.id;
            this.generateUrl();
        }
    },

    methods: {
        onChange: function onChange(value, name) {
            this[name] = value;
        },
        generateUrl: function generateUrl() {

            this.url = this.basePath() + '/chat/' + this.name + '/' + this.depUrlBit + '/' + this.helpUrlBit;
        },
        submit: function submit() {
            var _this = this;

            __WEBPACK_IMPORTED_MODULE_0_axios___default.a.put('chat/api/update/' + this.id, {
                department: this.department,
                helptopic: this.helptopic,
                secret_key: this.secret_key,
                url: this.url,
                script: this.script
            }).then(function (res) {
                _this.$store.dispatch('unsetValidationError');
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, "chatEdit");
                setTimeout(function () {
                    return _this.redirect('/chat/settings');
                }, 1200);
            }).catch(function (err) {
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, "chatEdit");
            });
        },
        getChatDetails: function getChatDetails() {
            var _this2 = this;

            __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('/chat/api/chats?ids[]=' + this.id).then(function (res) {
                var data = res.data.data.chats[0];
                _this2.department = data.department;
                _this2.helptopic = data.helptopic;
                _this2.url = data.url;
                _this2.secret_key = data.secret_key;
                _this2.secret_key_required = data.secret_key_required ? true : false;
                _this2.name = data.short;
                _this2.script = data.script;
            });
        }
    },

    beforeMount: function beforeMount() {

        this.getChatDetails();
    },


    components: {
        "text-field": __webpack_require__(11),
        'dynamic-select': __webpack_require__(14),
        'alert': __webpack_require__(6)
    }
});

/***/ }),

/***/ 2638:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("alert", { attrs: { componentName: "chatEdit" } }),
      _vm._v(" "),
      _c("div", { staticClass: "card card-light" }, [
        _c("div", { staticClass: "card-header" }, [
          _c("h3", { staticClass: "card-title" }, [
            _vm._v(
              "\n                " +
                _vm._s(_vm.lang("edit")) +
                "    \n            "
            )
          ])
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "card-body" }, [
          _c(
            "div",
            { staticClass: "row" },
            [
              _c("dynamic-select", {
                attrs: {
                  name: "department",
                  classname: "col-sm-6",
                  apiEndpoint: _vm.departmentUrl,
                  multiple: false,
                  prePopulate: false,
                  label: _vm.lang("department"),
                  value: _vm.department,
                  onChange: _vm.onChange,
                  required: _vm.required,
                  clearable: false
                }
              }),
              _vm._v(" "),
              _c("dynamic-select", {
                attrs: {
                  name: "helptopic",
                  classname: "col-sm-6",
                  apiEndpoint: _vm.helptopicUrl,
                  multiple: false,
                  prePopulate: false,
                  label: _vm.lang("helptopic"),
                  value: _vm.helptopic,
                  onChange: _vm.onChange,
                  required: _vm.required,
                  clearable: false
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
                  label: _vm.lang("secret_key"),
                  onChange: _vm.onChange,
                  value: _vm.secret_key,
                  type: "text",
                  name: "secret_key",
                  classname: "col-sm-6",
                  disabled: !_vm.secret_key_required,
                  hint: _vm.lang("secret_key_hint"),
                  id: "secret_key",
                  required: _vm.required
                }
              }),
              _vm._v(" "),
              _c("text-field", {
                attrs: {
                  label: _vm.lang("url"),
                  onChange: _vm.onChange,
                  value: _vm.url,
                  type: "text",
                  name: "url",
                  disabled: true,
                  classname: "col-sm-6",
                  hint: _vm.lang("url_hint"),
                  id: "url"
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
                  label: _vm.lang("chat_widget_script"),
                  onChange: _vm.onChange,
                  value: _vm.script,
                  name: "script",
                  classname: "col-sm-12",
                  rows: "6",
                  type: "textarea",
                  hint: _vm.lang("chat_widget_script_hint"),
                  id: "script"
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
            { staticClass: "btn btn-primary", on: { click: _vm.submit } },
            [
              _c("i", { staticClass: "fas fa-save" }),
              _vm._v(" " + _vm._s(_vm.lang("save")) + "\n\n            ")
            ]
          )
        ])
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
    require("vue-hot-reload-api")      .rerender("data-v-be99425c", module.exports)
  }
}

/***/ })

},[2619]);