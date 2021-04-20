webpackJsonp([15],{

/***/ 2881:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2882);


/***/ }),

/***/ 2882:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_store__ = __webpack_require__(10);

var bootstrap = __webpack_require__(29);





var app = new Vue({
    el: '#twitter-settings',
    store: __WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */],
    components: {
        'twitter-settings': __webpack_require__(2883)
    }
});

/***/ }),

/***/ 2883:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2884)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2886)
/* template */
var __vue_template__ = __webpack_require__(2888)
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
Component.options.__file = "app/Plugins/Twitter/views/js/components/TwitterSettingsPage.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-79809acc", Component.options)
  } else {
    hotAPI.reload("data-v-79809acc", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2884:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2885);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("ab3fc72a", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-79809acc\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TwitterSettingsPage.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-79809acc\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TwitterSettingsPage.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2885:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.search {\n    display: none !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2886:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__Mixins_TwitterMixin_js__ = __webpack_require__(2887);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

    mixins: [__WEBPACK_IMPORTED_MODULE_2__Mixins_TwitterMixin_js__["a" /* TwitterMixin */]],

    components: {
        "text-field": __webpack_require__(11),
        'alert': __webpack_require__(6),
        "custom-loader": __webpack_require__(9),
        'dynamic-select': __webpack_require__(14),
        "data-table": __webpack_require__(17),
        'loader': __webpack_require__(8),
        "modal": __webpack_require__(13)
    },

    beforeMount: function beforeMount() {

        this.hitApi();
    },


    methods: {
        hitApi: function hitApi() {
            this.getAppDetails();
        },
        onChange: function onChange(value, name) {
            this[name] = value;
        },
        deleteTwitter: function deleteTwitter() {
            var _this = this;

            //for reset
            this.isLoading = true;
            this.isDisabled = true;
            __WEBPACK_IMPORTED_MODULE_0_axios___default.a.delete(this.deleteUrl + '/' + this.appID).then(function (res) {
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, "TwitterSettingsPageAlert");
                _this.isLoading = false;
                _this.showModal = false;
                _this.loading = true;
                _this.hitApi();
            }).catch(function (err) {
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, "TwitterSettingsPageAlert");
                _this.showModal = false;
                _this.isLoading = false;
            });
        },
        onSubmit: function onSubmit() {
            var _this2 = this;

            var formDataObj = {
                consumer_api_key: this.consumer_api_key,
                consumer_api_secret: this.consumer_api_secret,
                access_token: this.access_token,
                access_token_secret: this.access_token_secret,
                hashtag_text: this.hashtag_text.length ? this.hashtag_text.map(function (item) {
                    return item.name;
                }) : [],
                cron_confirm: this.cron_confirm.value,
                reply_interval: this.new_ticket_interval.value
            };

            this.loading = true;

            var url = void 0,
                method = void 0;

            if (this.app_exists) {
                url = 'twitter/api/update/' + this.appID;
                method = 'PUT';
            } else {
                url = 'twitter/api/create';
                method = 'POST';
            }

            __WEBPACK_IMPORTED_MODULE_0_axios___default.a.request({
                method: method,
                url: url,
                data: formDataObj
            }).then(function (res) {
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'TwitterSettingsPageAlert');
                _this2.$store.dispatch('unsetValidationError');
                _this2.hitApi();
            }).catch(function (err) {
                _this2.loading = false;
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'TwitterSettingsPageAlert');
            });
        },
        getAppDetails: function getAppDetails() {
            var _this3 = this;

            this.loading = true;
            __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('twitter/api/app').then(function (res) {
                var data = res.data.data.data[0];
                if (data) {
                    _this3.app_exists = true;
                    _this3.appID = data.id;
                    _this3.consumer_api_key = data.consumer_api_key;
                    _this3.consumer_api_secret = data.consumer_api_secret;
                    _this3.new_ticket_interval = _this3.filterValues(_this3.elements_new_ticket, data.reply_interval);
                    _this3.access_token = data.access_token;
                    _this3.access_token_secret = data.access_token_secret;
                    _this3.hashtag_text = data.hashtags;
                    _this3.cron_confirm = _this3.filterValues(_this3.elements_cron, data.cron);
                    _this3.loading = false;
                } else {
                    _this3.app_exists = false;
                    Object.assign(_this3.$data, _this3.$options.data.apply(_this3));
                    _this3.loading = false;
                }
            }).catch(function (err) {
                _this3.loading = false;
                Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'TwitterSettingsPage');
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

/***/ 2887:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return TwitterMixin; });
var TwitterMixin = {
    data: function data() {
        return {
            loadingSpeed: 4000,
            app_exists: false,
            isLoading: false,
            loading: false,
            consumer_api_key: '',
            consumer_api_secret: '',
            access_token: '',
            access_token_secret: '',
            required: true,
            mode: 'create',
            twitterModelId: null,
            hashtag_text: [],
            elements_hashtag: [],
            isDisabled: false,
            appID: '',
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

            elements_cron: [{
                name: "Yes",
                value: 1
            }, {
                name: "No",
                value: 0
            }],

            new_ticket_interval: {
                name: "Ten Days",
                value: "10"
            },

            cron_confirm: {
                name: "Yes",
                value: 1
            },

            showModal: false,
            deleteUrl: 'twitter/api/delete'

        };
    }
};

/***/ }),

/***/ 2888:
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
      _c("alert", { attrs: { componentName: "TwitterSettingsPageAlert" } }),
      _vm._v(" "),
      _c("div", { staticClass: "card card-light" }, [
        _c("div", { staticClass: "card-header" }, [
          _c("h3", { staticClass: "card-title" }, [
            _vm._v(
              "\n                " +
                _vm._s(_vm.lang("twitter_settings")) +
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
                  label: _vm.lang("consumer_api_key"),
                  onChange: _vm.onChange,
                  value: _vm.consumer_api_key,
                  type: "text",
                  name: "consumer_api_key",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("consumer_api_key"),
                  id: "consumer_api_key"
                }
              }),
              _vm._v(" "),
              _c("text-field", {
                attrs: {
                  label: _vm.lang("consumer_api_secret"),
                  onChange: _vm.onChange,
                  value: _vm.consumer_api_secret,
                  type: "text",
                  name: "consumer_api_secret",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("consumer_api_secret"),
                  id: "consumer_api_secret"
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
                  label: _vm.lang("access_token"),
                  onChange: _vm.onChange,
                  value: _vm.access_token,
                  type: "text",
                  name: "access_token",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("access_token"),
                  id: "access_token"
                }
              }),
              _vm._v(" "),
              _c("text-field", {
                attrs: {
                  label: _vm.lang("access_token_secret"),
                  onChange: _vm.onChange,
                  value: _vm.access_token_secret,
                  type: "text",
                  name: "access_token_secret",
                  required: _vm.required,
                  classname: "col-sm-6",
                  hint: _vm.lang("access_token_secret"),
                  id: "access_token_secret"
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
                  name: "hashtag_text",
                  classname: "col-sm-6",
                  elements: _vm.elements_hashtag,
                  multiple: true,
                  prePopulate: false,
                  label: _vm.lang("hashtags"),
                  value: _vm.hashtag_text,
                  onChange: _vm.onChange,
                  taggable: true,
                  searchable: true,
                  disableNoOptionsMessage: true,
                  hint: _vm.lang("hashtag_hint")
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
                  label: _vm.lang("time_check_twitter"),
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
                  name: "cron_confirm",
                  classname: "col-sm-6",
                  elements: _vm.elements_cron,
                  multiple: false,
                  prePopulate: false,
                  label: _vm.lang("cron_label_twitter"),
                  value: _vm.cron_confirm,
                  onChange: _vm.onChange,
                  searchable: false,
                  clearable: false
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
              on: { click: _vm.onSubmit }
            },
            [
              _c("i", { staticClass: "fas fa-save" }),
              _vm._v(
                "\n                " +
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
                        on: { click: _vm.deleteTwitter }
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
    require("vue-hot-reload-api")      .rerender("data-v-79809acc", module.exports)
  }
}

/***/ })

},[2881]);