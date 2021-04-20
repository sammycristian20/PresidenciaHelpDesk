webpackJsonp([13],{

/***/ 2430:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2431);


/***/ }),

/***/ 2431:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_vue__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vue_router__ = __webpack_require__(92);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_v_tooltip__ = __webpack_require__(53);
__webpack_require__(29);









__WEBPACK_IMPORTED_MODULE_2_vue___default.a.component('system-updates', __webpack_require__(2432));

__WEBPACK_IMPORTED_MODULE_2_vue___default.a.component('database-updates', __webpack_require__(2437));



__WEBPACK_IMPORTED_MODULE_2_vue___default.a.use(__WEBPACK_IMPORTED_MODULE_4_v_tooltip__["a" /* default */]);

__WEBPACK_IMPORTED_MODULE_2_vue___default.a.use(__WEBPACK_IMPORTED_MODULE_3_vue_router__["a" /* default */]);

new __WEBPACK_IMPORTED_MODULE_2_vue___default.a({
    el: '#header-container',
    store: __WEBPACK_IMPORTED_MODULE_1_store__["a" /* store */]
});

/***/ }),

/***/ 2432:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2433)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2435)
/* template */
var __vue_template__ = __webpack_require__(2436)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-3807acbf"
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
Component.options.__file = "resources/assets/js/components/HeaderNavigation/Updates/SystemUpdates.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3807acbf", Component.options)
  } else {
    hotAPI.reload("data-v-3807acbf", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2433:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2434);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("2af07416", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3807acbf\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./SystemUpdates.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3807acbf\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./SystemUpdates.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2434:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.update_badge[data-v-3807acbf] {\n\tright: 1px !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2435:
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


/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'syatem-updates',

	props: {

		updatesCount: { type: String | Number, default: '' }
	}
});

/***/ }),

/***/ 2436:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("li", { staticClass: "nav-item dropdown" }, [
    _c(
      "a",
      {
        directives: [
          {
            name: "tooltip",
            rawName: "v-tooltip",
            value: _vm.trans("application_updates"),
            expression: "trans('application_updates')"
          }
        ],
        staticClass: "nav-link",
        attrs: { "data-toggle": "dropdown", href: "javascript:;" }
      },
      [
        _c("i", { staticClass: "fas fa-sync-alt" }),
        _vm._v(" "),
        _c(
          "span",
          { staticClass: "badge badge-warning navbar-badge update_badge" },
          [_vm._v(_vm._s(_vm.updatesCount))]
        )
      ]
    ),
    _vm._v(" "),
    _c(
      "div",
      { staticClass: "dropdown-menu dropdown-menu-lg dropdown-menu-right" },
      [
        !_vm.updatesCount
          ? _c("span", { staticClass: "dropdown-header" }, [
              _vm._v("You have " + _vm._s(_vm.updatesCount) + " update(s)")
            ])
          : _vm._e(),
        _vm._v(" "),
        _vm.updatesCount
          ? [
              _c("span", { staticClass: "dropdown-header" }, [
                _vm._v("New version(s) available.")
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "dropdown-divider" }),
              _vm._v(" "),
              _c(
                "a",
                {
                  staticClass: "dropdown-item",
                  attrs: { href: "javascript:;" }
                },
                [
                  _vm._v("\n\t\t\t\t\n\t\t\t\tPlease "),
                  _c(
                    "a",
                    { attrs: { href: _vm.basePath() + "/check-updates" } },
                    [_vm._v(" click here ")]
                  ),
                  _vm._v(" to update your system.\n\t\t\t")
                ]
              )
            ]
          : _vm._e()
      ],
      2
    )
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-3807acbf", module.exports)
  }
}

/***/ }),

/***/ 2437:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2438)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2440)
/* template */
var __vue_template__ = __webpack_require__(2441)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-a5601a1a"
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
Component.options.__file = "resources/assets/js/components/HeaderNavigation/Updates/DatabaseUpdates.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-a5601a1a", Component.options)
  } else {
    hotAPI.reload("data-v-a5601a1a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2438:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2439);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("ff50484e", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a5601a1a\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./DatabaseUpdates.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a5601a1a\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./DatabaseUpdates.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2439:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.w_100[data-v-a5601a1a] {\n\t\twidth: 200px;\n    \theight: 120px;\n    \t-o-object-fit: contain;\n    \t   object-fit: contain;\n    \t-o-object-position: 50% 50%;\n    \t   object-position: 50% 50%;\n}\n.fs_title[data-v-a5601a1a] { font-size: 1.3rem !important;\n}\n.lockscreen-logo[data-v-a5601a1a] {\n\t\tmargin-bottom: 15px !important;\n}\n.lockscreen-wrapper[data-v-a5601a1a] { margin-top: 15% !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2440:
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






/* harmony default export */ __webpack_exports__["default"] = ({

	name: 'database-updates',

	data: function data() {

		return {

			loading: false,

			isDisabled: false
		};
	},


	methods: {
		onClick: function onClick() {
			var _this = this;

			this.loading = true;

			this.isDisabled = true;

			__WEBPACK_IMPORTED_MODULE_1_axios___default.a.post('/update-database').then(function (res) {

				_this.loading = false;

				_this.redirect('/auth/login');
			}).catch(function (err) {

				_this.loading = false;

				_this.isDisabled = false;

				_this.redirect('/');
			});
		}
	},

	components: {

		'custom-loader': __webpack_require__(9)
	}
});

/***/ }),

/***/ 2441:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "lockscreen-wrapper" }, [
    _vm.loading
      ? _c(
          "div",
          { staticClass: "row" },
          [_c("custom-loader", { attrs: { duration: 4000 } })],
          1
        )
      : _vm._e(),
    _vm._v(" "),
    _c("div", { staticClass: "card" }, [
      _vm._m(0),
      _vm._v(" "),
      _vm._m(1),
      _vm._v(" "),
      _c("div", { staticClass: "card-footer bg-white text-center" }, [
        _c(
          "button",
          {
            staticClass: "btn btn-primary btn-block",
            attrs: { disabled: _vm.isDisabled },
            on: { click: _vm.onClick }
          },
          [
            _c("i", { staticClass: "fas fa-sync" }),
            _vm._v(" Click here to update Database\n            \t")
          ]
        )
      ])
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "card-header" }, [
      _c("h3", { staticClass: "card-title fs_title" }, [
        _vm._v("Database Update Required")
      ])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "card-body box-profile" }, [
      _c("span", [
        _vm._v(
          "\n        \n                \tFile system has been updated but your database is still on the older version! Before we send you on your way, we have to update your database to the latest version.\n\n                \t"
        ),
        _c("br"),
        _c("br"),
        _vm._v(
          "\n                \tThe update process may take a little while, so please be patient. We will redirect you back once your database updation is complete.\n                "
        )
      ])
    ])
  }
]
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-a5601a1a", module.exports)
  }
}

/***/ })

},[2430]);