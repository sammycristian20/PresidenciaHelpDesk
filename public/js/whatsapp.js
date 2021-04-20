webpackJsonp([14],{

/***/ 2611:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2612);


/***/ }),

/***/ 2612:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
var bootstrap = __webpack_require__(29);


new Vue({
    el: '#whatsapp',
    store: __WEBPACK_IMPORTED_MODULE_0_store__["a" /* store */],
    components: {
        'settings': __webpack_require__(2613)
    }
});

/***/ }),

/***/ 2613:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2614)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2616)
/* template */
var __vue_template__ = __webpack_require__(2618)
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
Component.options.__file = "app/Plugins/Whatsapp/views/js/components/WhatsappSettings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-8b1f6faa", Component.options)
  } else {
    hotAPI.reload("data-v-8b1f6faa", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2614:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2615);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("5ef25985", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-8b1f6faa\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./WhatsappSettings.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-8b1f6faa\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./WhatsappSettings.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2615:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.search {\n    display: none !important;\n}\n#H5{\n\tmargin-left:16px;\n\t/*margin-bottom:18px !important;*/\n}\n.spin{\n\tleft:0% !important;right: 43% !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2616:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__mixins_whatsappMixin__ = __webpack_require__(2617);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

    mixins: [__WEBPACK_IMPORTED_MODULE_2__mixins_whatsappMixin__["a" /* WhatsappMixin */]],

    components: {
        "text-field": __webpack_require__(11),
        'alert': __webpack_require__(6),
        "custom-loader": __webpack_require__(9),
        'dynamic-select': __webpack_require__(14),
        "data-table": __webpack_require__(17),
        "modal": __webpack_require__(13),
        'loader': __webpack_require__(8)
    },

    beforeMount: function beforeMount() {
        this.getValuesForEdit();
        this.webhook_url = this.basePath() + "/whatsapp";
    },


    methods: {
        onChange: function onChange(value, name) {
            this[name] = value;
        },
        submit: function submit() {
            var _this = this;

            this.loading = true;

            var formObj = {
                sid: this.sid,
                token: this.token,
                name: this.name,
                business_phone: this.business_phone,
                is_image_inline: this.is_image_inline.value,
                reply_interval: this.new_ticket_interval.value,
                template: this.template
            };

            var url = void 0,
                method = void 0;

            if (this.app_exists) {
                url = 'whatsapp/api/update/';
                method = 'PUT';
            } else {
                url = '/whatsapp/api/create';
                method = 'POST';
            }
            __WEBPACK_IMPORTED_MODULE_0_axios___default.a.request({
                method: method,
                url: url,
                data: formObj
            }).then(function (res) {
                _this.loading = false;
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'dataTableModal');
                setTimeout(function () {
                    location.reload();
                }, 1200);
            }).catch(function (err) {
                _this.loading = false;
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'dataTableModal');
            });
        },
        onSubmit: function onSubmit() {
            var _this2 = this;

            //for reset
            this.isLoading = true;
            this.isDisabled = true;
            __WEBPACK_IMPORTED_MODULE_0_axios___default.a.delete("whatsapp/api/delete").then(function (res) {

                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, "dataTableModal");
                _this2.isLoading = false;
                _this2.showModal = false;
                _this2.redirect('/whatsapp/settings');
            }).catch(function (err) {

                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, "dataTableModal");
                _this2.showModal = false;
                _this2.isLoading = false;
            });
        },
        getValuesForEdit: function getValuesForEdit() {
            var _this3 = this;

            this.loading = true;
            __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('whatsapp/api/accounts').then(function (res) {

                if (parseInt(res.data.data.total) >= 1) {
                    _this3.app_exists = true;
                    _this3.data = res.data.data.accounts[0];
                    _this3.sid = _this3.data.sid;
                    _this3.token = _this3.data.token;
                    _this3.name = _this3.data.name;
                    _this3.is_image_inline = _this3.filterValues(_this3.elements_is_image_inline, _this3.data.is_image_inline);
                    _this3.new_ticket_interval = _this3.filterValues(_this3.elements_new_ticket, _this3.data.new_ticket_interval);
                    _this3.business_phone = _this3.data.business_phone;
                    _this3.template = _this3.data.template;
                }
                _this3.loading = false;
            }).catch(function (err) {
                return _this3.loading = false;
            });
        },
        filterValues: function filterValues(array, value) {
            return array.filter(function (x) {
                if (x.value == value) return x;
            })[0];
        },
        toggleModal: function toggleModal() {
            this.showModal = !this.showModal;
        },
        onClose: function onClose() {
            this.showModal = false;
        }
    }

});

/***/ }),

/***/ 2617:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return WhatsappMixin; });
var WhatsappMixin = {
    data: function data() {
        return {
            template: '',
            password: 'password',
            name: '',
            sid: '',
            token: '',
            business_phone: '',
            webhook_url: '',
            app_exists: false,
            required: true,
            loading: false,
            isLoading: false,
            loadingSpeed: 4000,
            elements_new_ticket: [{
                name: "One Day",
                value: 1
            }, {
                name: "Five Days",
                value: 5
            }, {
                name: "Ten Days",
                value: 10
            }, {
                name: "Fifteen Days",
                value: 15
            }, {
                name: "Thirty Days",
                value: 30
            }],

            showModal: false,

            elements_is_image_inline: [{
                name: "Yes",
                value: 1
            }, {
                name: "No",
                value: 0
            }],

            new_ticket_interval: {
                name: "One Day",
                value: "1"
            },

            is_image_inline: {
                name: "Yes",
                value: 1
            }
        };
    },

    methods: {
        resetFields: function resetFields() {

            this.name = '';
            this.sid = '';
            this.token = '';
            this.business_phone = '';
            this.is_image_inline = 1;
            this.new_ticket_interval = "";
            this.template = '';
        }
    }
};

/***/ }),

/***/ 2618:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _vm.loading
        ? _c("custom-loader", { attrs: { duration: _vm.loadingSpeed } })
        : _vm._e(),
      _vm._v(" "),
      _c("alert", { attrs: { componentName: "dataTableModal" } }),
      _vm._v(" "),
      _c("div", { staticClass: "card card-light" }, [
        _c("div", { staticClass: "card-header" }, [
          _c("h3", { staticClass: "card-title" }, [
            _vm._v(
              "\n                " +
                _vm._s(_vm.lang("whatsapp_settings")) +
                "\n            "
            )
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
                  label: _vm.lang("name"),
                  onChange: _vm.onChange,
                  value: _vm.name,
                  type: "text",
                  name: "name",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("name_hint"),
                  id: "name"
                }
              }),
              _vm._v(" "),
              _c("text-field", {
                attrs: {
                  label: _vm.lang("account_sid"),
                  onChange: _vm.onChange,
                  value: _vm.sid,
                  type: "text",
                  name: "sid",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("sid_hint"),
                  id: "sid"
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
                  label: _vm.lang("auth_token"),
                  onChange: _vm.onChange,
                  value: _vm.token,
                  type: "password",
                  name: "token",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("token_hint"),
                  id: "token"
                }
              }),
              _vm._v(" "),
              _c("text-field", {
                attrs: {
                  label: _vm.lang("business_phone"),
                  onChange: _vm.onChange,
                  value: _vm.business_phone,
                  type: "text",
                  name: "business_phone",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("business_phone_hint"),
                  id: "business_phone"
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
                  label: _vm.lang("webhook_url"),
                  onChange: _vm.onChange,
                  value: _vm.webhook_url,
                  type: "text",
                  name: "webhook_url",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("webhook_url_hint"),
                  id: "webhook_url",
                  disabled: true
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
                  label: _vm.lang("new_ticket_interval"),
                  value: _vm.new_ticket_interval,
                  onChange: _vm.onChange,
                  searchable: false,
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
              _c("dynamic-select", {
                attrs: {
                  name: "is_image_inline",
                  classname: "col-sm-6",
                  elements: _vm.elements_is_image_inline,
                  multiple: false,
                  prePopulate: false,
                  label: _vm.lang("is_image_inline"),
                  value: _vm.is_image_inline,
                  onChange: _vm.onChange,
                  searchable: false,
                  clearable: false
                }
              }),
              _vm._v(" "),
              _c("text-field", {
                attrs: {
                  label: _vm.lang("approved_template_for_whatsapp"),
                  onChange: _vm.onChange,
                  value: _vm.template,
                  type: "textarea",
                  name: "template",
                  classname: "col-sm-6",
                  hint: _vm.lang("approved_template_hint"),
                  id: "template",
                  disabled: true
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
              attrs: { type: "button" },
              on: { click: _vm.submit }
            },
            [
              _c("i", { staticClass: "fas fa-save" }),
              _vm._v(
                " \n                " +
                  _vm._s(
                    !_vm.app_exists ? _vm.lang("save") : _vm.lang("upd8")
                  ) +
                  "\n            "
              )
            ]
          ),
          _vm._v(" "),
          _c(
            "button",
            {
              staticClass: "btn btn-primary",
              attrs: { disabled: !_vm.app_exists },
              on: { click: _vm.toggleModal }
            },
            [
              _c("i", { staticClass: "glyphicon glyphicon-repeat" }),
              _vm._v(
                " \n                " +
                  _vm._s(_vm.lang("reset")) +
                  "\n            "
              )
            ]
          )
        ])
      ]),
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
                    _c("h4", { staticClass: "modal-title" }, [
                      _vm._v(_vm._s(_vm.lang("reset")))
                    ])
                  ]),
                  _vm._v(" "),
                  !_vm.isLoading
                    ? _c("div", { attrs: { slot: "fields" }, slot: "fields" }, [
                        _c("span", [
                          _vm._v(
                            "\n                    " +
                              _vm._s(_vm.lang("reset_confirm")) +
                              "\n                  "
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
                        attrs: { type: "button" },
                        on: { click: _vm.onSubmit }
                      },
                      [
                        _c("i", {
                          staticClass: "glyphicon glyphicon-repeat",
                          attrs: { "aria-hidden": "true" }
                        }),
                        _vm._v(
                          "\n                   " +
                            _vm._s(_vm.lang("reset")) +
                            "\n                "
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
    require("vue-hot-reload-api")      .rerender("data-v-8b1f6faa", module.exports)
  }
}

/***/ })

},[2611]);