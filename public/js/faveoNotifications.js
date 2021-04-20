webpackJsonp([13],{

/***/ 2469:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2470);


/***/ }),

/***/ 2470:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_es6_promise_auto__ = __webpack_require__(30);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_vue_notifications__ = __webpack_require__(3323);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_vue_notifications___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_vue_notifications__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_mini_toastr__ = __webpack_require__(3324);
__webpack_require__(27);







var toastTypes = {
    success: 'success',
    error: 'error',
    info: 'info',
    warn: 'warn'
};

__WEBPACK_IMPORTED_MODULE_3_mini_toastr__["a" /* default */].init({ types: toastTypes });

function toast(_ref) {
    var title = _ref.title,
        message = _ref.message,
        type = _ref.type,
        timeout = _ref.timeout,
        cb = _ref.cb;

    return __WEBPACK_IMPORTED_MODULE_3_mini_toastr__["a" /* default */][type](message, title, timeout, cb);
}

var options = {
    success: toast,
    error: toast,
    info: toast,
    warn: toast
};

__WEBPACK_IMPORTED_MODULE_0_vue___default.a.use(__WEBPACK_IMPORTED_MODULE_2_vue_notifications__["default"], options);

__WEBPACK_IMPORTED_MODULE_0_vue___default.a.component('faveo-notifications', __webpack_require__(2472));

new __WEBPACK_IMPORTED_MODULE_0_vue___default.a({
    el: '#faveo-notifications'
});

/***/ }),

/***/ 2472:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2473)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2475)
/* template */
var __vue_template__ = __webpack_require__(2476)
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
Component.options.__file = "resources/assets/js/components/Notifications/FaveoNotifications.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1b506fc1", Component.options)
  } else {
    hotAPI.reload("data-v-1b506fc1", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2473:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2474);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("37e262d6", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1b506fc1\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./FaveoNotifications.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1b506fc1\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./FaveoNotifications.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2474:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),

/***/ 2475:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {};
  },

  created: function created() {
    Echo.channel('home').listen('NewMessage', function (e) {
      alert(e.message);
    });
  },


  notifications: {
    showMessage: {
      title: title,
      message: message,
      type: type
    }
  }
});

/***/ }),

/***/ 2476:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div")
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-1b506fc1", module.exports)
  }
}

/***/ }),

/***/ 3323:
/***/ (function(module, exports) {

throw new Error("Module parse failed: Unexpected token (38:23)\nYou may need an appropriate loader to handle this file type.\n|     Object.keys(config).forEach((field) => {\r\n|         if (field === 'cb') {\r\n|             result = { ...result, [field]: config[field].bind(vueApp) };\r\n|         }\r\n|         else {\r");

/***/ }),

/***/ 3324:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export fadeOut */
/* unused harmony export flatten */
/* unused harmony export makeCss */
/* unused harmony export appendStyles */
/* unused harmony export makeNode */
/* unused harmony export createIcon */
/* unused harmony export addElem */
/* unused harmony export getTypeClass */
function fadeOut (element, cb) {
  if (element.style.opacity && element.style.opacity > 0.05) {
    element.style.opacity = element.style.opacity - 0.05
  } else if (element.style.opacity && element.style.opacity <= 0.1) {
    if (element.parentNode) {
      element.parentNode.removeChild(element)
      if (cb) cb()
    }
  } else {
    element.style.opacity = 0.9
  }
  setTimeout(() => fadeOut.apply(this, [element, cb]), 1000 / 30
  )
}

const LIB_NAME = 'mini-toastr'
/* unused harmony export LIB_NAME */


const ERROR = 'error'
/* unused harmony export ERROR */

const WARN = 'warn'
/* unused harmony export WARN */

const SUCCESS = 'success'
/* unused harmony export SUCCESS */

const INFO = 'info'
/* unused harmony export INFO */

const CONTAINER_CLASS = LIB_NAME
/* unused harmony export CONTAINER_CLASS */

const NOTIFICATION_CLASS = `${LIB_NAME}__notification`
/* unused harmony export NOTIFICATION_CLASS */

const TITLE_CLASS = `${LIB_NAME}-notification__title`
/* unused harmony export TITLE_CLASS */

const ICON_CLASS = `${LIB_NAME}-notification__icon`
/* unused harmony export ICON_CLASS */

const MESSAGE_CLASS = `${LIB_NAME}-notification__message`
/* unused harmony export MESSAGE_CLASS */

const ERROR_CLASS = `-${ERROR}`
/* unused harmony export ERROR_CLASS */

const WARN_CLASS = `-${WARN}`
/* unused harmony export WARN_CLASS */

const SUCCESS_CLASS = `-${SUCCESS}`
/* unused harmony export SUCCESS_CLASS */

const INFO_CLASS = `-${INFO}`
/* unused harmony export INFO_CLASS */

const DEFAULT_TIMEOUT = 3000
/* unused harmony export DEFAULT_TIMEOUT */


const EMPTY_STRING = ''

function flatten (obj, into, prefix) {
  into = into || {}
  prefix = prefix || EMPTY_STRING

  for (const k in obj) {
    if (obj.hasOwnProperty(k)) {
      const prop = obj[k]
      if (prop && typeof prop === 'object' && !(prop instanceof Date || prop instanceof RegExp)) {
        flatten(prop, into, prefix + k + ' ')
      } else {
        if (into[prefix] && typeof into[prefix] === 'object') {
          into[prefix][k] = prop
        } else {
          into[prefix] = {}
          into[prefix][k] = prop
        }
      }
    }
  }

  return into
}

function makeCss (obj) {
  const flat = flatten(obj)
  let str = JSON.stringify(flat, null, 2)
  str = str.replace(/"([^"]*)": {/g, '$1 {')
    .replace(/"([^"]*)"/g, '$1')
    .replace(/(\w*-?\w*): ([\w\d .#]*),?/g, '$1: $2;')
    .replace(/},/g, '}\n')
    .replace(/ &([.:])/g, '$1')

  str = str.substr(1, str.lastIndexOf('}') - 1)

  return str
}

function appendStyles (css) {
  let head = document.head || document.getElementsByTagName('head')[0]
  let styleElem = makeNode('style')
  styleElem.id = `${LIB_NAME}-styles`
  styleElem.type = 'text/css'

  if (styleElem.styleSheet) {
    styleElem.styleSheet.cssText = css
  } else {
    styleElem.appendChild(document.createTextNode(css))
  }

  head.appendChild(styleElem)
}

const config = {
  types: {ERROR, WARN, SUCCESS, INFO},
  animation: fadeOut,
  timeout: DEFAULT_TIMEOUT,
  icons: {},
  appendTarget: document.body,
  node: makeNode(),
  allowHtml: false,
  style: {
    [`.${CONTAINER_CLASS}`]: {
      position: 'fixed',
      'z-index': 99999,
      right: '12px',
      top: '12px'
    },
    [`.${NOTIFICATION_CLASS}`]: {
      cursor: 'pointer',
      padding: '12px 18px',
      margin: '0 0 6px 0',
      'background-color': '#000',
      opacity: 0.8,
      color: '#fff',
      'border-radius': '3px',
      'box-shadow': '#3c3b3b 0 0 12px',
      width: '300px',
      [`&.${ERROR_CLASS}`]: {
        'background-color': '#D5122B'
      },
      [`&.${WARN_CLASS}`]: {
        'background-color': '#F5AA1E'
      },
      [`&.${SUCCESS_CLASS}`]: {
        'background-color': '#7AC13E'
      },
      [`&.${INFO_CLASS}`]: {
        'background-color': '#4196E1'
      },
      '&:hover': {
        opacity: 1,
        'box-shadow': '#000 0 0 12px'
      }
    },
    [`.${TITLE_CLASS}`]: {
      'font-weight': '500'
    },
    [`.${MESSAGE_CLASS}`]: {
      display: 'inline-block',
      'vertical-align': 'middle',
      width: '240px',
      padding: '0 12px'
    }
  }
}
/* unused harmony export config */


function makeNode (type = 'div') {
  return document.createElement(type)
}

function createIcon (node, type, config) {
  const iconNode = makeNode(config.icons[type].nodeType)
  const attrs = config.icons[type].attrs

  for (const k in attrs) {
    if (attrs.hasOwnProperty(k)) {
      iconNode.setAttribute(k, attrs[k])
    }
  }

  node.appendChild(iconNode)
}

function addElem (node, text, className, config) {
  const elem = makeNode()
  elem.className = className
  if (config.allowHtml) {
    elem.innerHTML = text
  } else {
    elem.appendChild(document.createTextNode(text))
  }
  node.appendChild(elem)
}

function getTypeClass (type) {
  if (type === SUCCESS) return SUCCESS_CLASS
  if (type === WARN) return WARN_CLASS
  if (type === ERROR) return ERROR_CLASS
  if (type === INFO) return INFO_CLASS

  return EMPTY_STRING
}

const miniToastr = {
  config,
  isInitialised: false,
  showMessage (message, title, type, timeout, cb, overrideConf) {
    const config = {}
    Object.assign(config, this.config)
    Object.assign(config, overrideConf)

    const notificationElem = makeNode()
    notificationElem.className = `${NOTIFICATION_CLASS} ${getTypeClass(type)}`

    notificationElem.onclick = function () {
      config.animation(notificationElem, null)
    }

    if (title) addElem(notificationElem, title, TITLE_CLASS, config)
    if (config.icons[type]) createIcon(notificationElem, type, config)
    if (message) addElem(notificationElem, message, MESSAGE_CLASS, config)

    config.node.insertBefore(notificationElem, config.node.firstChild)
    setTimeout(() => config.animation(notificationElem, cb), timeout || config.timeout
    )

    if (cb) cb()
    return this
  },
  init (aConfig) {
    const newConfig = {}
    Object.assign(newConfig, config)
    Object.assign(newConfig, aConfig)
    this.config = newConfig

    const cssStr = makeCss(newConfig.style)
    appendStyles(cssStr)

    newConfig.node.id = CONTAINER_CLASS
    newConfig.node.className = CONTAINER_CLASS
    newConfig.appendTarget.appendChild(newConfig.node)

    Object.keys(newConfig.types).forEach(v => {
      this[newConfig.types[v]] = function (message, title, timeout, cb, config) {
        this.showMessage(message, title, newConfig.types[v], timeout, cb, config)
        return this
      }.bind(this)
    }
    )

    this.isInitialised = true

    return this
  },
  setIcon (type, nodeType = 'i', attrs = []) {
    attrs.class = attrs.class ? attrs.class + ' ' + ICON_CLASS : ICON_CLASS

    this.config.icons[type] = {nodeType, attrs}
  }
}

/* harmony default export */ __webpack_exports__["a"] = (miniToastr);

/***/ })

},[2469]);