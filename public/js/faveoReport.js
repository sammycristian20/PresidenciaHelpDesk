webpackJsonp([5],{

/***/ 117:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return getFilterObjectToArray; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return getValidFilterObject; });
/* unused harmony export isValidFilterFieldValue */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return getColumnClass; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__ = __webpack_require__(4);
/**
 * Common utilty for report
 */





/**
 * returns filter array as key value pair
 * e.g; [{key: created_at, value: last::13~day}]
 * @param {Object} filterParams 
 */
var getFilterObjectToArray = function getFilterObjectToArray() {
  var filterParams = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

  var filterArray = [];
  for (var key in filterParams) {
    if (filterParams.hasOwnProperty(key) && isValidFilterFieldValue(filterParams[key])) {
      var filterObj = {};
      filterObj.key = key;
      filterObj.value = filterParams[key];
      filterArray.push(filterObj);
    }
  }
  return filterArray;
};

/**
 * returns only filter objects
 * @param {Object} filterParams 
 */
var getValidFilterObject = function getValidFilterObject() {
  var filterParams = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

  var filterObj = {};
  for (var key in filterParams) {
    if (filterParams.hasOwnProperty(key)) {
      if (isValidFilterFieldValue(filterParams[key])) {
        filterObj[key] = filterParams[key];
      }
    }
  }
  return filterObj;
};

// Check if filter filed has a valid value
var isValidFilterFieldValue = function isValidFilterFieldValue(value) {
  // also considering the fields having 'yes'/ 'no' option
  return value === 0 || Object(__WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__["c" /* boolean */])(value);
};

/**
 * column class like col-md-12, col-md-6
 * @param {String} layout Examle:- n*3 for col-md-4
 */
var getColumnClass = function getColumnClass() {
  var layout = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'n*1';

  var column_count = layout.split('*')[1];
  var col_partition = 12 / Number(column_count);
  return 'col-md-' + col_partition;
};

/***/ }),

/***/ 232:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2812)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2814)
/* template */
var __vue_template__ = __webpack_require__(2831)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-7e1beb5c"
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
Component.options.__file = "app/FaveoReport/views/js/components/Common/TimeSeriesChart.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7e1beb5c", Component.options)
  } else {
    hotAPI.reload("data-v-7e1beb5c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 233:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2815)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2817)
/* template */
var __vue_template__ = __webpack_require__(2830)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-63eeea3a"
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
Component.options.__file = "resources/assets/js/ChartFactory/FaveoChart.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-63eeea3a", Component.options)
  } else {
    hotAPI.reload("data-v-63eeea3a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2777:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(2778);


/***/ }),

/***/ 2778:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_es6_promise_auto__ = __webpack_require__(31);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_es6_promise_auto___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_es6_promise_auto__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vue_select__ = __webpack_require__(66);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vue_select___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_vue_select__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_chartjs_plugin_datalabels__ = __webpack_require__(2781);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_chartjs_plugin_datalabels___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_chartjs_plugin_datalabels__);


var bootstrap = __webpack_require__(29);

__webpack_require__(2779);







__WEBPACK_IMPORTED_MODULE_0_vue___default.a.component('v-select', __WEBPACK_IMPORTED_MODULE_3_vue_select___default.a);

// Adding chart js data label plugin


__WEBPACK_IMPORTED_MODULE_0_vue___default.a.component('report-home-page', __webpack_require__(2782));
__WEBPACK_IMPORTED_MODULE_0_vue___default.a.component('report-entry-page', __webpack_require__(2787));
// for report settings page
__WEBPACK_IMPORTED_MODULE_0_vue___default.a.component('report-settings', __webpack_require__(2843));

var app = new __WEBPACK_IMPORTED_MODULE_0_vue___default.a({
    el: '#faveo-report',
    store: __WEBPACK_IMPORTED_MODULE_2_store__["a" /* store */]
});

/***/ }),

/***/ 2779:
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(2780);

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(49)(content, options);

if(content.locals) module.exports = content.locals;

if(false) {
	module.hot.accept("!!../../../../node_modules/css-loader/index.js!./reportCommon.css", function() {
		var newContent = require("!!../../../../node_modules/css-loader/index.js!./reportCommon.css");

		if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];

		var locals = (function(a, b) {
			var key, idx = 0;

			for(key in a) {
				if(!b || a[key] !== b[key]) return false;
				idx++;
			}

			for(key in b) idx--;

			return idx === 0;
		}(content.locals, newContent.locals));

		if(!locals) throw new Error('Aborting CSS HMR due to changed css-modules locals.');

		update(newContent);
	});

	module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2780:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "/* Common css classes for report module */\n\n\n.panel,\n.panel-group .panel,\n.panel-group .panel-heading,\n.panel-group .panel-body\n{\n  -webkit-border-radius: 0;\n  -moz-border-radius: 0;\n  border-radius: 0;\n}\n\n/* Bootstrap panel title same as faveo standard*/\n.panel-title {\n  font-family: Tahoma;\n}\n\n", ""]);

// exports


/***/ }),

/***/ 2781:
/***/ (function(module, exports, __webpack_require__) {

/*!
 * chartjs-plugin-datalabels v0.7.0
 * https://chartjs-plugin-datalabels.netlify.com
 * (c) 2019 Chart.js Contributors
 * Released under the MIT license
 */
(function (global, factory) {
 true ? module.exports = factory(__webpack_require__(72)) :
typeof define === 'function' && define.amd ? define(['chart.js'], factory) :
(global = global || self, global.ChartDataLabels = factory(global.Chart));
}(this, function (Chart) { 'use strict';

Chart = Chart && Chart.hasOwnProperty('default') ? Chart['default'] : Chart;

var helpers = Chart.helpers;

var devicePixelRatio = (function() {
	if (typeof window !== 'undefined') {
		if (window.devicePixelRatio) {
			return window.devicePixelRatio;
		}

		// devicePixelRatio is undefined on IE10
		// https://stackoverflow.com/a/20204180/8837887
		// https://github.com/chartjs/chartjs-plugin-datalabels/issues/85
		var screen = window.screen;
		if (screen) {
			return (screen.deviceXDPI || 1) / (screen.logicalXDPI || 1);
		}
	}

	return 1;
}());

var utils = {
	// @todo move this in Chart.helpers.toTextLines
	toTextLines: function(inputs) {
		var lines = [];
		var input;

		inputs = [].concat(inputs);
		while (inputs.length) {
			input = inputs.pop();
			if (typeof input === 'string') {
				lines.unshift.apply(lines, input.split('\n'));
			} else if (Array.isArray(input)) {
				inputs.push.apply(inputs, input);
			} else if (!helpers.isNullOrUndef(inputs)) {
				lines.unshift('' + input);
			}
		}

		return lines;
	},

	// @todo move this method in Chart.helpers.canvas.toFont (deprecates helpers.fontString)
	// @see https://developer.mozilla.org/en-US/docs/Web/CSS/font
	toFontString: function(font) {
		if (!font || helpers.isNullOrUndef(font.size) || helpers.isNullOrUndef(font.family)) {
			return null;
		}

		return (font.style ? font.style + ' ' : '')
			+ (font.weight ? font.weight + ' ' : '')
			+ font.size + 'px '
			+ font.family;
	},

	// @todo move this in Chart.helpers.canvas.textSize
	// @todo cache calls of measureText if font doesn't change?!
	textSize: function(ctx, lines, font) {
		var items = [].concat(lines);
		var ilen = items.length;
		var prev = ctx.font;
		var width = 0;
		var i;

		ctx.font = font.string;

		for (i = 0; i < ilen; ++i) {
			width = Math.max(ctx.measureText(items[i]).width, width);
		}

		ctx.font = prev;

		return {
			height: ilen * font.lineHeight,
			width: width
		};
	},

	// @todo move this method in Chart.helpers.options.toFont
	parseFont: function(value) {
		var global = Chart.defaults.global;
		var size = helpers.valueOrDefault(value.size, global.defaultFontSize);
		var font = {
			family: helpers.valueOrDefault(value.family, global.defaultFontFamily),
			lineHeight: helpers.options.toLineHeight(value.lineHeight, size),
			size: size,
			style: helpers.valueOrDefault(value.style, global.defaultFontStyle),
			weight: helpers.valueOrDefault(value.weight, null),
			string: ''
		};

		font.string = utils.toFontString(font);
		return font;
	},

	/**
	 * Returns value bounded by min and max. This is equivalent to max(min, min(value, max)).
	 * @todo move this method in Chart.helpers.bound
	 * https://doc.qt.io/qt-5/qtglobal.html#qBound
	 */
	bound: function(min, value, max) {
		return Math.max(min, Math.min(value, max));
	},

	/**
	 * Returns an array of pair [value, state] where state is:
	 * * -1: value is only in a0 (removed)
	 * *  1: value is only in a1 (added)
	 */
	arrayDiff: function(a0, a1) {
		var prev = a0.slice();
		var updates = [];
		var i, j, ilen, v;

		for (i = 0, ilen = a1.length; i < ilen; ++i) {
			v = a1[i];
			j = prev.indexOf(v);

			if (j === -1) {
				updates.push([v, 1]);
			} else {
				prev.splice(j, 1);
			}
		}

		for (i = 0, ilen = prev.length; i < ilen; ++i) {
			updates.push([prev[i], -1]);
		}

		return updates;
	},

	/**
	 * https://github.com/chartjs/chartjs-plugin-datalabels/issues/70
	 */
	rasterize: function(v) {
		return Math.round(v * devicePixelRatio) / devicePixelRatio;
	}
};

function orient(point, origin) {
	var x0 = origin.x;
	var y0 = origin.y;

	if (x0 === null) {
		return {x: 0, y: -1};
	}
	if (y0 === null) {
		return {x: 1, y: 0};
	}

	var dx = point.x - x0;
	var dy = point.y - y0;
	var ln = Math.sqrt(dx * dx + dy * dy);

	return {
		x: ln ? dx / ln : 0,
		y: ln ? dy / ln : -1
	};
}

function aligned(x, y, vx, vy, align) {
	switch (align) {
	case 'center':
		vx = vy = 0;
		break;
	case 'bottom':
		vx = 0;
		vy = 1;
		break;
	case 'right':
		vx = 1;
		vy = 0;
		break;
	case 'left':
		vx = -1;
		vy = 0;
		break;
	case 'top':
		vx = 0;
		vy = -1;
		break;
	case 'start':
		vx = -vx;
		vy = -vy;
		break;
	case 'end':
		// keep natural orientation
		break;
	default:
		// clockwise rotation (in degree)
		align *= (Math.PI / 180);
		vx = Math.cos(align);
		vy = Math.sin(align);
		break;
	}

	return {
		x: x,
		y: y,
		vx: vx,
		vy: vy
	};
}

// Line clipping (Cohen–Sutherland algorithm)
// https://en.wikipedia.org/wiki/Cohen–Sutherland_algorithm

var R_INSIDE = 0;
var R_LEFT = 1;
var R_RIGHT = 2;
var R_BOTTOM = 4;
var R_TOP = 8;

function region(x, y, rect) {
	var res = R_INSIDE;

	if (x < rect.left) {
		res |= R_LEFT;
	} else if (x > rect.right) {
		res |= R_RIGHT;
	}
	if (y < rect.top) {
		res |= R_TOP;
	} else if (y > rect.bottom) {
		res |= R_BOTTOM;
	}

	return res;
}

function clipped(segment, area) {
	var x0 = segment.x0;
	var y0 = segment.y0;
	var x1 = segment.x1;
	var y1 = segment.y1;
	var r0 = region(x0, y0, area);
	var r1 = region(x1, y1, area);
	var r, x, y;

	// eslint-disable-next-line no-constant-condition
	while (true) {
		if (!(r0 | r1) || (r0 & r1)) {
			// both points inside or on the same side: no clipping
			break;
		}

		// at least one point is outside
		r = r0 || r1;

		if (r & R_TOP) {
			x = x0 + (x1 - x0) * (area.top - y0) / (y1 - y0);
			y = area.top;
		} else if (r & R_BOTTOM) {
			x = x0 + (x1 - x0) * (area.bottom - y0) / (y1 - y0);
			y = area.bottom;
		} else if (r & R_RIGHT) {
			y = y0 + (y1 - y0) * (area.right - x0) / (x1 - x0);
			x = area.right;
		} else if (r & R_LEFT) {
			y = y0 + (y1 - y0) * (area.left - x0) / (x1 - x0);
			x = area.left;
		}

		if (r === r0) {
			x0 = x;
			y0 = y;
			r0 = region(x0, y0, area);
		} else {
			x1 = x;
			y1 = y;
			r1 = region(x1, y1, area);
		}
	}

	return {
		x0: x0,
		x1: x1,
		y0: y0,
		y1: y1
	};
}

function compute(range, config) {
	var anchor = config.anchor;
	var segment = range;
	var x, y;

	if (config.clamp) {
		segment = clipped(segment, config.area);
	}

	if (anchor === 'start') {
		x = segment.x0;
		y = segment.y0;
	} else if (anchor === 'end') {
		x = segment.x1;
		y = segment.y1;
	} else {
		x = (segment.x0 + segment.x1) / 2;
		y = (segment.y0 + segment.y1) / 2;
	}

	return aligned(x, y, range.vx, range.vy, config.align);
}

var positioners = {
	arc: function(vm, config) {
		var angle = (vm.startAngle + vm.endAngle) / 2;
		var vx = Math.cos(angle);
		var vy = Math.sin(angle);
		var r0 = vm.innerRadius;
		var r1 = vm.outerRadius;

		return compute({
			x0: vm.x + vx * r0,
			y0: vm.y + vy * r0,
			x1: vm.x + vx * r1,
			y1: vm.y + vy * r1,
			vx: vx,
			vy: vy
		}, config);
	},

	point: function(vm, config) {
		var v = orient(vm, config.origin);
		var rx = v.x * vm.radius;
		var ry = v.y * vm.radius;

		return compute({
			x0: vm.x - rx,
			y0: vm.y - ry,
			x1: vm.x + rx,
			y1: vm.y + ry,
			vx: v.x,
			vy: v.y
		}, config);
	},

	rect: function(vm, config) {
		var v = orient(vm, config.origin);
		var x = vm.x;
		var y = vm.y;
		var sx = 0;
		var sy = 0;

		if (vm.horizontal) {
			x = Math.min(vm.x, vm.base);
			sx = Math.abs(vm.base - vm.x);
		} else {
			y = Math.min(vm.y, vm.base);
			sy = Math.abs(vm.base - vm.y);
		}

		return compute({
			x0: x,
			y0: y + sy,
			x1: x + sx,
			y1: y,
			vx: v.x,
			vy: v.y
		}, config);
	},

	fallback: function(vm, config) {
		var v = orient(vm, config.origin);

		return compute({
			x0: vm.x,
			y0: vm.y,
			x1: vm.x,
			y1: vm.y,
			vx: v.x,
			vy: v.y
		}, config);
	}
};

var helpers$1 = Chart.helpers;
var rasterize = utils.rasterize;

function boundingRects(model) {
	var borderWidth = model.borderWidth || 0;
	var padding = model.padding;
	var th = model.size.height;
	var tw = model.size.width;
	var tx = -tw / 2;
	var ty = -th / 2;

	return {
		frame: {
			x: tx - padding.left - borderWidth,
			y: ty - padding.top - borderWidth,
			w: tw + padding.width + borderWidth * 2,
			h: th + padding.height + borderWidth * 2
		},
		text: {
			x: tx,
			y: ty,
			w: tw,
			h: th
		}
	};
}

function getScaleOrigin(el) {
	var horizontal = el._model.horizontal;
	var scale = el._scale || (horizontal && el._xScale) || el._yScale;

	if (!scale) {
		return null;
	}

	if (scale.xCenter !== undefined && scale.yCenter !== undefined) {
		return {x: scale.xCenter, y: scale.yCenter};
	}

	var pixel = scale.getBasePixel();
	return horizontal ?
		{x: pixel, y: null} :
		{x: null, y: pixel};
}

function getPositioner(el) {
	if (el instanceof Chart.elements.Arc) {
		return positioners.arc;
	}
	if (el instanceof Chart.elements.Point) {
		return positioners.point;
	}
	if (el instanceof Chart.elements.Rectangle) {
		return positioners.rect;
	}
	return positioners.fallback;
}

function drawFrame(ctx, rect, model) {
	var bgColor = model.backgroundColor;
	var borderColor = model.borderColor;
	var borderWidth = model.borderWidth;

	if (!bgColor && (!borderColor || !borderWidth)) {
		return;
	}

	ctx.beginPath();

	helpers$1.canvas.roundedRect(
		ctx,
		rasterize(rect.x) + borderWidth / 2,
		rasterize(rect.y) + borderWidth / 2,
		rasterize(rect.w) - borderWidth,
		rasterize(rect.h) - borderWidth,
		model.borderRadius);

	ctx.closePath();

	if (bgColor) {
		ctx.fillStyle = bgColor;
		ctx.fill();
	}

	if (borderColor && borderWidth) {
		ctx.strokeStyle = borderColor;
		ctx.lineWidth = borderWidth;
		ctx.lineJoin = 'miter';
		ctx.stroke();
	}
}

function textGeometry(rect, align, font) {
	var h = font.lineHeight;
	var w = rect.w;
	var x = rect.x;
	var y = rect.y + h / 2;

	if (align === 'center') {
		x += w / 2;
	} else if (align === 'end' || align === 'right') {
		x += w;
	}

	return {
		h: h,
		w: w,
		x: x,
		y: y
	};
}

function drawTextLine(ctx, text, cfg) {
	var shadow = ctx.shadowBlur;
	var stroked = cfg.stroked;
	var x = rasterize(cfg.x);
	var y = rasterize(cfg.y);
	var w = rasterize(cfg.w);

	if (stroked) {
		ctx.strokeText(text, x, y, w);
	}

	if (cfg.filled) {
		if (shadow && stroked) {
			// Prevent drawing shadow on both the text stroke and fill, so
			// if the text is stroked, remove the shadow for the text fill.
			ctx.shadowBlur = 0;
		}

		ctx.fillText(text, x, y, w);

		if (shadow && stroked) {
			ctx.shadowBlur = shadow;
		}
	}
}

function drawText(ctx, lines, rect, model) {
	var align = model.textAlign;
	var color = model.color;
	var filled = !!color;
	var font = model.font;
	var ilen = lines.length;
	var strokeColor = model.textStrokeColor;
	var strokeWidth = model.textStrokeWidth;
	var stroked = strokeColor && strokeWidth;
	var i;

	if (!ilen || (!filled && !stroked)) {
		return;
	}

	// Adjust coordinates based on text alignment and line height
	rect = textGeometry(rect, align, font);

	ctx.font = font.string;
	ctx.textAlign = align;
	ctx.textBaseline = 'middle';
	ctx.shadowBlur = model.textShadowBlur;
	ctx.shadowColor = model.textShadowColor;

	if (filled) {
		ctx.fillStyle = color;
	}
	if (stroked) {
		ctx.lineJoin = 'round';
		ctx.lineWidth = strokeWidth;
		ctx.strokeStyle = strokeColor;
	}

	for (i = 0, ilen = lines.length; i < ilen; ++i) {
		drawTextLine(ctx, lines[i], {
			stroked: stroked,
			filled: filled,
			w: rect.w,
			x: rect.x,
			y: rect.y + rect.h * i
		});
	}
}

var Label = function(config, ctx, el, index) {
	var me = this;

	me._config = config;
	me._index = index;
	me._model = null;
	me._rects = null;
	me._ctx = ctx;
	me._el = el;
};

helpers$1.extend(Label.prototype, {
	/**
	 * @private
	 */
	_modelize: function(display, lines, config, context) {
		var me = this;
		var index = me._index;
		var resolve = helpers$1.options.resolve;
		var font = utils.parseFont(resolve([config.font, {}], context, index));
		var color = resolve([config.color, Chart.defaults.global.defaultFontColor], context, index);

		return {
			align: resolve([config.align, 'center'], context, index),
			anchor: resolve([config.anchor, 'center'], context, index),
			area: context.chart.chartArea,
			backgroundColor: resolve([config.backgroundColor, null], context, index),
			borderColor: resolve([config.borderColor, null], context, index),
			borderRadius: resolve([config.borderRadius, 0], context, index),
			borderWidth: resolve([config.borderWidth, 0], context, index),
			clamp: resolve([config.clamp, false], context, index),
			clip: resolve([config.clip, false], context, index),
			color: color,
			display: display,
			font: font,
			lines: lines,
			offset: resolve([config.offset, 0], context, index),
			opacity: resolve([config.opacity, 1], context, index),
			origin: getScaleOrigin(me._el),
			padding: helpers$1.options.toPadding(resolve([config.padding, 0], context, index)),
			positioner: getPositioner(me._el),
			rotation: resolve([config.rotation, 0], context, index) * (Math.PI / 180),
			size: utils.textSize(me._ctx, lines, font),
			textAlign: resolve([config.textAlign, 'start'], context, index),
			textShadowBlur: resolve([config.textShadowBlur, 0], context, index),
			textShadowColor: resolve([config.textShadowColor, color], context, index),
			textStrokeColor: resolve([config.textStrokeColor, color], context, index),
			textStrokeWidth: resolve([config.textStrokeWidth, 0], context, index)
		};
	},

	update: function(context) {
		var me = this;
		var model = null;
		var rects = null;
		var index = me._index;
		var config = me._config;
		var value, label, lines;

		// We first resolve the display option (separately) to avoid computing
		// other options in case the label is hidden (i.e. display: false).
		var display = helpers$1.options.resolve([config.display, true], context, index);

		if (display) {
			value = context.dataset.data[index];
			label = helpers$1.valueOrDefault(helpers$1.callback(config.formatter, [value, context]), value);
			lines = helpers$1.isNullOrUndef(label) ? [] : utils.toTextLines(label);

			if (lines.length) {
				model = me._modelize(display, lines, config, context);
				rects = boundingRects(model);
			}
		}

		me._model = model;
		me._rects = rects;
	},

	geometry: function() {
		return this._rects ? this._rects.frame : {};
	},

	rotation: function() {
		return this._model ? this._model.rotation : 0;
	},

	visible: function() {
		return this._model && this._model.opacity;
	},

	model: function() {
		return this._model;
	},

	draw: function(chart, center) {
		var me = this;
		var ctx = chart.ctx;
		var model = me._model;
		var rects = me._rects;
		var area;

		if (!this.visible()) {
			return;
		}

		ctx.save();

		if (model.clip) {
			area = model.area;
			ctx.beginPath();
			ctx.rect(
				area.left,
				area.top,
				area.right - area.left,
				area.bottom - area.top);
			ctx.clip();
		}

		ctx.globalAlpha = utils.bound(0, model.opacity, 1);
		ctx.translate(rasterize(center.x), rasterize(center.y));
		ctx.rotate(model.rotation);

		drawFrame(ctx, rects.frame, model);
		drawText(ctx, model.lines, rects.text, model);

		ctx.restore();
	}
});

var helpers$2 = Chart.helpers;

var MIN_INTEGER = Number.MIN_SAFE_INTEGER || -9007199254740991; // eslint-disable-line es/no-number-minsafeinteger
var MAX_INTEGER = Number.MAX_SAFE_INTEGER || 9007199254740991;  // eslint-disable-line es/no-number-maxsafeinteger

function rotated(point, center, angle) {
	var cos = Math.cos(angle);
	var sin = Math.sin(angle);
	var cx = center.x;
	var cy = center.y;

	return {
		x: cx + cos * (point.x - cx) - sin * (point.y - cy),
		y: cy + sin * (point.x - cx) + cos * (point.y - cy)
	};
}

function projected(points, axis) {
	var min = MAX_INTEGER;
	var max = MIN_INTEGER;
	var origin = axis.origin;
	var i, pt, vx, vy, dp;

	for (i = 0; i < points.length; ++i) {
		pt = points[i];
		vx = pt.x - origin.x;
		vy = pt.y - origin.y;
		dp = axis.vx * vx + axis.vy * vy;
		min = Math.min(min, dp);
		max = Math.max(max, dp);
	}

	return {
		min: min,
		max: max
	};
}

function toAxis(p0, p1) {
	var vx = p1.x - p0.x;
	var vy = p1.y - p0.y;
	var ln = Math.sqrt(vx * vx + vy * vy);

	return {
		vx: (p1.x - p0.x) / ln,
		vy: (p1.y - p0.y) / ln,
		origin: p0,
		ln: ln
	};
}

var HitBox = function() {
	this._rotation = 0;
	this._rect = {
		x: 0,
		y: 0,
		w: 0,
		h: 0
	};
};

helpers$2.extend(HitBox.prototype, {
	center: function() {
		var r = this._rect;
		return {
			x: r.x + r.w / 2,
			y: r.y + r.h / 2
		};
	},

	update: function(center, rect, rotation) {
		this._rotation = rotation;
		this._rect = {
			x: rect.x + center.x,
			y: rect.y + center.y,
			w: rect.w,
			h: rect.h
		};
	},

	contains: function(point) {
		var me = this;
		var margin = 1;
		var rect = me._rect;

		point = rotated(point, me.center(), -me._rotation);

		return !(point.x < rect.x - margin
			|| point.y < rect.y - margin
			|| point.x > rect.x + rect.w + margin * 2
			|| point.y > rect.y + rect.h + margin * 2);
	},

	// Separating Axis Theorem
	// https://gamedevelopment.tutsplus.com/tutorials/collision-detection-using-the-separating-axis-theorem--gamedev-169
	intersects: function(other) {
		var r0 = this._points();
		var r1 = other._points();
		var axes = [
			toAxis(r0[0], r0[1]),
			toAxis(r0[0], r0[3])
		];
		var i, pr0, pr1;

		if (this._rotation !== other._rotation) {
			// Only separate with r1 axis if the rotation is different,
			// else it's enough to separate r0 and r1 with r0 axis only!
			axes.push(
				toAxis(r1[0], r1[1]),
				toAxis(r1[0], r1[3])
			);
		}

		for (i = 0; i < axes.length; ++i) {
			pr0 = projected(r0, axes[i]);
			pr1 = projected(r1, axes[i]);

			if (pr0.max < pr1.min || pr1.max < pr0.min) {
				return false;
			}
		}

		return true;
	},

	/**
	 * @private
	 */
	_points: function() {
		var me = this;
		var rect = me._rect;
		var angle = me._rotation;
		var center = me.center();

		return [
			rotated({x: rect.x, y: rect.y}, center, angle),
			rotated({x: rect.x + rect.w, y: rect.y}, center, angle),
			rotated({x: rect.x + rect.w, y: rect.y + rect.h}, center, angle),
			rotated({x: rect.x, y: rect.y + rect.h}, center, angle)
		];
	}
});

function coordinates(view, model, geometry) {
	var point = model.positioner(view, model);
	var vx = point.vx;
	var vy = point.vy;

	if (!vx && !vy) {
		// if aligned center, we don't want to offset the center point
		return {x: point.x, y: point.y};
	}

	var w = geometry.w;
	var h = geometry.h;

	// take in account the label rotation
	var rotation = model.rotation;
	var dx = Math.abs(w / 2 * Math.cos(rotation)) + Math.abs(h / 2 * Math.sin(rotation));
	var dy = Math.abs(w / 2 * Math.sin(rotation)) + Math.abs(h / 2 * Math.cos(rotation));

	// scale the unit vector (vx, vy) to get at least dx or dy equal to
	// w or h respectively (else we would calculate the distance to the
	// ellipse inscribed in the bounding rect)
	var vs = 1 / Math.max(Math.abs(vx), Math.abs(vy));
	dx *= vx * vs;
	dy *= vy * vs;

	// finally, include the explicit offset
	dx += model.offset * vx;
	dy += model.offset * vy;

	return {
		x: point.x + dx,
		y: point.y + dy
	};
}

function collide(labels, collider) {
	var i, j, s0, s1;

	// IMPORTANT Iterate in the reverse order since items at the end of the
	// list have an higher weight/priority and thus should be less impacted
	// by the overlapping strategy.

	for (i = labels.length - 1; i >= 0; --i) {
		s0 = labels[i].$layout;

		for (j = i - 1; j >= 0 && s0._visible; --j) {
			s1 = labels[j].$layout;

			if (s1._visible && s0._box.intersects(s1._box)) {
				collider(s0, s1);
			}
		}
	}

	return labels;
}

function compute$1(labels) {
	var i, ilen, label, state, geometry, center;

	// Initialize labels for overlap detection
	for (i = 0, ilen = labels.length; i < ilen; ++i) {
		label = labels[i];
		state = label.$layout;

		if (state._visible) {
			geometry = label.geometry();
			center = coordinates(label._el._model, label.model(), geometry);
			state._box.update(center, geometry, label.rotation());
		}
	}

	// Auto hide overlapping labels
	return collide(labels, function(s0, s1) {
		var h0 = s0._hidable;
		var h1 = s1._hidable;

		if ((h0 && h1) || h1) {
			s1._visible = false;
		} else if (h0) {
			s0._visible = false;
		}
	});
}

var layout = {
	prepare: function(datasets) {
		var labels = [];
		var i, j, ilen, jlen, label;

		for (i = 0, ilen = datasets.length; i < ilen; ++i) {
			for (j = 0, jlen = datasets[i].length; j < jlen; ++j) {
				label = datasets[i][j];
				labels.push(label);
				label.$layout = {
					_box: new HitBox(),
					_hidable: false,
					_visible: true,
					_set: i,
					_idx: j
				};
			}
		}

		// TODO New `z` option: labels with a higher z-index are drawn
		// of top of the ones with a lower index. Lowest z-index labels
		// are also discarded first when hiding overlapping labels.
		labels.sort(function(a, b) {
			var sa = a.$layout;
			var sb = b.$layout;

			return sa._idx === sb._idx
				? sb._set - sa._set
				: sb._idx - sa._idx;
		});

		this.update(labels);

		return labels;
	},

	update: function(labels) {
		var dirty = false;
		var i, ilen, label, model, state;

		for (i = 0, ilen = labels.length; i < ilen; ++i) {
			label = labels[i];
			model = label.model();
			state = label.$layout;
			state._hidable = model && model.display === 'auto';
			state._visible = label.visible();
			dirty |= state._hidable;
		}

		if (dirty) {
			compute$1(labels);
		}
	},

	lookup: function(labels, point) {
		var i, state;

		// IMPORTANT Iterate in the reverse order since items at the end of
		// the list have an higher z-index, thus should be picked first.

		for (i = labels.length - 1; i >= 0; --i) {
			state = labels[i].$layout;

			if (state && state._visible && state._box.contains(point)) {
				return labels[i];
			}
		}

		return null;
	},

	draw: function(chart, labels) {
		var i, ilen, label, state, geometry, center;

		for (i = 0, ilen = labels.length; i < ilen; ++i) {
			label = labels[i];
			state = label.$layout;

			if (state._visible) {
				geometry = label.geometry();
				center = coordinates(label._el._view, label.model(), geometry);
				state._box.update(center, geometry, label.rotation());
				label.draw(chart, center);
			}
		}
	}
};

var helpers$3 = Chart.helpers;

var formatter = function(value) {
	if (helpers$3.isNullOrUndef(value)) {
		return null;
	}

	var label = value;
	var keys, klen, k;
	if (helpers$3.isObject(value)) {
		if (!helpers$3.isNullOrUndef(value.label)) {
			label = value.label;
		} else if (!helpers$3.isNullOrUndef(value.r)) {
			label = value.r;
		} else {
			label = '';
			keys = Object.keys(value);
			for (k = 0, klen = keys.length; k < klen; ++k) {
				label += (k !== 0 ? ', ' : '') + keys[k] + ': ' + value[keys[k]];
			}
		}
	}

	return '' + label;
};

/**
 * IMPORTANT: make sure to also update tests and TypeScript definition
 * files (`/test/specs/defaults.spec.js` and `/types/options.d.ts`)
 */

var defaults = {
	align: 'center',
	anchor: 'center',
	backgroundColor: null,
	borderColor: null,
	borderRadius: 0,
	borderWidth: 0,
	clamp: false,
	clip: false,
	color: undefined,
	display: true,
	font: {
		family: undefined,
		lineHeight: 1.2,
		size: undefined,
		style: undefined,
		weight: null
	},
	formatter: formatter,
	labels: undefined,
	listeners: {},
	offset: 4,
	opacity: 1,
	padding: {
		top: 4,
		right: 4,
		bottom: 4,
		left: 4
	},
	rotation: 0,
	textAlign: 'start',
	textStrokeColor: undefined,
	textStrokeWidth: 0,
	textShadowBlur: 0,
	textShadowColor: undefined
};

/**
 * @see https://github.com/chartjs/Chart.js/issues/4176
 */

var helpers$4 = Chart.helpers;
var EXPANDO_KEY = '$datalabels';
var DEFAULT_KEY = '$default';

function configure(dataset, options) {
	var override = dataset.datalabels;
	var listeners = {};
	var configs = [];
	var labels, keys;

	if (override === false) {
		return null;
	}
	if (override === true) {
		override = {};
	}

	options = helpers$4.merge({}, [options, override]);
	labels = options.labels || {};
	keys = Object.keys(labels);
	delete options.labels;

	if (keys.length) {
		keys.forEach(function(key) {
			if (labels[key]) {
				configs.push(helpers$4.merge({}, [
					options,
					labels[key],
					{_key: key}
				]));
			}
		});
	} else {
		// Default label if no "named" label defined.
		configs.push(options);
	}

	// listeners: {<event-type>: {<label-key>: <fn>}}
	listeners = configs.reduce(function(target, config) {
		helpers$4.each(config.listeners || {}, function(fn, event) {
			target[event] = target[event] || {};
			target[event][config._key || DEFAULT_KEY] = fn;
		});

		delete config.listeners;
		return target;
	}, {});

	return {
		labels: configs,
		listeners: listeners
	};
}

function dispatchEvent(chart, listeners, label) {
	if (!listeners) {
		return;
	}

	var context = label.$context;
	var groups = label.$groups;
	var callback;

	if (!listeners[groups._set]) {
		return;
	}

	callback = listeners[groups._set][groups._key];
	if (!callback) {
		return;
	}

	if (helpers$4.callback(callback, [context]) === true) {
		// Users are allowed to tweak the given context by injecting values that can be
		// used in scriptable options to display labels differently based on the current
		// event (e.g. highlight an hovered label). That's why we update the label with
		// the output context and schedule a new chart render by setting it dirty.
		chart[EXPANDO_KEY]._dirty = true;
		label.update(context);
	}
}

function dispatchMoveEvents(chart, listeners, previous, label) {
	var enter, leave;

	if (!previous && !label) {
		return;
	}

	if (!previous) {
		enter = true;
	} else if (!label) {
		leave = true;
	} else if (previous !== label) {
		leave = enter = true;
	}

	if (leave) {
		dispatchEvent(chart, listeners.leave, previous);
	}
	if (enter) {
		dispatchEvent(chart, listeners.enter, label);
	}
}

function handleMoveEvents(chart, event) {
	var expando = chart[EXPANDO_KEY];
	var listeners = expando._listeners;
	var previous, label;

	if (!listeners.enter && !listeners.leave) {
		return;
	}

	if (event.type === 'mousemove') {
		label = layout.lookup(expando._labels, event);
	} else if (event.type !== 'mouseout') {
		return;
	}

	previous = expando._hovered;
	expando._hovered = label;
	dispatchMoveEvents(chart, listeners, previous, label);
}

function handleClickEvents(chart, event) {
	var expando = chart[EXPANDO_KEY];
	var handlers = expando._listeners.click;
	var label = handlers && layout.lookup(expando._labels, event);
	if (label) {
		dispatchEvent(chart, handlers, label);
	}
}

// https://github.com/chartjs/chartjs-plugin-datalabels/issues/108
function invalidate(chart) {
	if (chart.animating) {
		return;
	}

	// `chart.animating` can be `false` even if there is animation in progress,
	// so let's iterate all animations to find if there is one for the `chart`.
	var animations = Chart.animationService.animations;
	for (var i = 0, ilen = animations.length; i < ilen; ++i) {
		if (animations[i].chart === chart) {
			return;
		}
	}

	// No render scheduled: trigger a "lazy" render that can be canceled in case
	// of hover interactions. The 1ms duration is a workaround to make sure an
	// animation is created so the controller can stop it before any transition.
	chart.render({duration: 1, lazy: true});
}

Chart.defaults.global.plugins.datalabels = defaults;

var plugin = {
	id: 'datalabels',

	beforeInit: function(chart) {
		chart[EXPANDO_KEY] = {
			_actives: []
		};
	},

	beforeUpdate: function(chart) {
		var expando = chart[EXPANDO_KEY];
		expando._listened = false;
		expando._listeners = {};     // {<event-type>: {<dataset-index>: {<label-key>: <fn>}}}
		expando._datasets = [];      // per dataset labels: [Label[]]
		expando._labels = [];        // layouted labels: Label[]
	},

	afterDatasetUpdate: function(chart, args, options) {
		var datasetIndex = args.index;
		var expando = chart[EXPANDO_KEY];
		var labels = expando._datasets[datasetIndex] = [];
		var visible = chart.isDatasetVisible(datasetIndex);
		var dataset = chart.data.datasets[datasetIndex];
		var config = configure(dataset, options);
		var elements = args.meta.data || [];
		var ctx = chart.ctx;
		var i, j, ilen, jlen, cfg, key, el, label;

		ctx.save();

		for (i = 0, ilen = elements.length; i < ilen; ++i) {
			el = elements[i];
			el[EXPANDO_KEY] = [];

			if (visible && el && !el.hidden && !el._model.skip) {
				for (j = 0, jlen = config.labels.length; j < jlen; ++j) {
					cfg = config.labels[j];
					key = cfg._key;

					label = new Label(cfg, ctx, el, i);
					label.$groups = {
						_set: datasetIndex,
						_key: key || DEFAULT_KEY
					};
					label.$context = {
						active: false,
						chart: chart,
						dataIndex: i,
						dataset: dataset,
						datasetIndex: datasetIndex
					};

					label.update(label.$context);
					el[EXPANDO_KEY].push(label);
					labels.push(label);
				}
			}
		}

		ctx.restore();

		// Store listeners at the chart level and per event type to optimize
		// cases where no listeners are registered for a specific event.
		helpers$4.merge(expando._listeners, config.listeners, {
			merger: function(event, target, source) {
				target[event] = target[event] || {};
				target[event][args.index] = source[event];
				expando._listened = true;
			}
		});
	},

	afterUpdate: function(chart, options) {
		chart[EXPANDO_KEY]._labels = layout.prepare(
			chart[EXPANDO_KEY]._datasets,
			options);
	},

	// Draw labels on top of all dataset elements
	// https://github.com/chartjs/chartjs-plugin-datalabels/issues/29
	// https://github.com/chartjs/chartjs-plugin-datalabels/issues/32
	afterDatasetsDraw: function(chart) {
		layout.draw(chart, chart[EXPANDO_KEY]._labels);
	},

	beforeEvent: function(chart, event) {
		// If there is no listener registered for this chart, `listened` will be false,
		// meaning we can immediately ignore the incoming event and avoid useless extra
		// computation for users who don't implement label interactions.
		if (chart[EXPANDO_KEY]._listened) {
			switch (event.type) {
			case 'mousemove':
			case 'mouseout':
				handleMoveEvents(chart, event);
				break;
			case 'click':
				handleClickEvents(chart, event);
				break;
			default:
			}
		}
	},

	afterEvent: function(chart) {
		var expando = chart[EXPANDO_KEY];
		var previous = expando._actives;
		var actives = expando._actives = chart.lastActive || [];  // public API?!
		var updates = utils.arrayDiff(previous, actives);
		var i, ilen, j, jlen, update, label, labels;

		for (i = 0, ilen = updates.length; i < ilen; ++i) {
			update = updates[i];
			if (update[1]) {
				labels = update[0][EXPANDO_KEY] || [];
				for (j = 0, jlen = labels.length; j < jlen; ++j) {
					label = labels[j];
					label.$context.active = (update[1] === 1);
					label.update(label.$context);
				}
			}
		}

		if (expando._dirty || updates.length) {
			layout.update(expando._labels);
			invalidate(chart);
		}

		delete expando._dirty;
	}
};

// TODO Remove at version 1, we shouldn't automatically register plugins.
// https://github.com/chartjs/chartjs-plugin-datalabels/issues/42
Chart.plugins.register(plugin);

return plugin;

}));


/***/ }),

/***/ 2782:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2783)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2785)
/* template */
var __vue_template__ = __webpack_require__(2786)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-0e381668"
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
Component.options.__file = "app/FaveoReport/views/js/components/ReportHomePage.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0e381668", Component.options)
  } else {
    hotAPI.reload("data-v-0e381668", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2783:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2784);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("03a118c8", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0e381668\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ReportHomePage.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0e381668\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ReportHomePage.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2784:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.report-modify[data-v-0e381668] {\n  position: relative;\n  left: 68px;\n}\n.ml-6[data-v-0e381668] { margin-left: 6px;\n}\n.fw-500[data-v-0e381668] { font-weight: 500 !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2785:
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






/* harmony default export */ __webpack_exports__["default"] = ({

  name: 'report-home-page',

  components: {
    'custom-loader': __webpack_require__(9),
    'alert': __webpack_require__(6)
  },

  data: function data() {
    return {
      reportList: [],
      isLoading: false
    };
  },

  computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_2_vuex__["b" /* mapGetters */])(["formattedTime"])),

  beforeMount: function beforeMount() {
    this.getReportList();
  },


  methods: {
    getIconClass: function getIconClass(value) {

      return value == 'fa fa-support fa-stack-1x' ? 'fas fa-life-ring fa-stack-1x' : value == 'fa fa-bank fa-stack-1x' ? 'fas fa-university  fa-stack-1x' : value;
    },
    getReportList: function getReportList() {
      var _this = this;

      this.isLoading = true;
      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('api/agent/report-list').then(function (res) {
        _this.reportList = res.data.data;
      }).catch(function (err) {
        Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'report-home-page');
      }).finally(function (res) {
        _this.isLoading = false;
      });
    },
    deleteCustomReport: function deleteCustomReport(reportId) {
      var _this2 = this;

      var isConfirmed = confirm('Are you sure you want to delete this report?');
      if (isConfirmed) {
        this.isLoading = true;
        __WEBPACK_IMPORTED_MODULE_0_axios___default.a.delete('api/report/' + reportId).then(function (res) {
          _this2.getReportList();
          Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'report-home-page');
        }).catch(function (err) {
          Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'report-home-page');
          _this2.isLoading = false;
        });
      }
    }
  }
});

/***/ }),

/***/ 2786:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("alert", { attrs: { componentName: "report-home-page" } }),
      _vm._v(" "),
      _vm.isLoading
        ? _c("custom-loader", { attrs: { duration: 4000 } })
        : _vm._e(),
      _vm._v(" "),
      _vm.reportList.length > 0
        ? _c(
            "div",
            _vm._l(_vm.reportList, function(reportCategory) {
              return _c(
                "div",
                { key: reportCategory.id, staticClass: "card card-light " },
                [
                  _c("div", { staticClass: "card-header" }, [
                    _c("h3", { staticClass: "card-title" }, [
                      _vm._v(_vm._s(reportCategory.category))
                    ])
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "card-body" }, [
                    _c("div", { staticClass: "table-responsive" }, [
                      _c("table", { staticClass: "table" }, [
                        _c(
                          "tbody",
                          _vm._l(reportCategory.reports, function(report) {
                            return _c(
                              "tr",
                              { key: report.id, staticClass: "Default" },
                              [
                                _c("td", [
                                  _c(
                                    "span",
                                    { staticClass: "fa-stack fa-2x" },
                                    [
                                      _c("i", {
                                        class: _vm.getIconClass(
                                          report.icon_class
                                        )
                                      })
                                    ]
                                  )
                                ]),
                                _vm._v(" "),
                                _c("td", [
                                  _c("dl", [
                                    _c(
                                      "dt",
                                      { staticClass: "text-uppercase" },
                                      [
                                        _c(
                                          "a",
                                          {
                                            staticClass: "fw-500",
                                            attrs: {
                                              href:
                                                _vm.basePath() +
                                                "/" +
                                                report.view_url
                                            }
                                          },
                                          [
                                            _vm._v(
                                              "\n                      " +
                                                _vm._s(report.name)
                                            )
                                          ]
                                        )
                                      ]
                                    ),
                                    _vm._v(" "),
                                    _c("dd", { staticClass: "text-overflow" }, [
                                      _vm._v(
                                        _vm._s(report.description) +
                                          "\n                      "
                                      ),
                                      _c("br"),
                                      _vm._v(" "),
                                      _c(
                                        "small",
                                        {
                                          staticClass:
                                            "float-right report-modify"
                                        },
                                        [
                                          _vm._v(
                                            "\n                        " +
                                              _vm._s(
                                                _vm.lang("last_modified_on")
                                              ) +
                                              ": "
                                          ),
                                          _c("strong", [
                                            _vm._v(
                                              _vm._s(
                                                _vm.formattedTime(
                                                  report.updated_at
                                                )
                                              )
                                            )
                                          ])
                                        ]
                                      )
                                    ])
                                  ])
                                ]),
                                _vm._v(" "),
                                _c("td", [
                                  report.is_default
                                    ? _c(
                                        "div",
                                        { staticClass: "badge badge-info" },
                                        [_vm._v(_vm._s(_vm.lang("default")))]
                                      )
                                    : _c(
                                        "button",
                                        {
                                          directives: [
                                            {
                                              name: "tooltip",
                                              rawName: "v-tooltip",
                                              value: _vm.lang("delete"),
                                              expression: "lang('delete')"
                                            }
                                          ],
                                          staticClass: "btn btn-danger ml-6",
                                          on: {
                                            click: function($event) {
                                              _vm.deleteCustomReport(report.id)
                                            }
                                          }
                                        },
                                        [
                                          _c("i", {
                                            staticClass: "fas fa-trash",
                                            attrs: { "aria-hidden": "true" }
                                          })
                                        ]
                                      )
                                ])
                              ]
                            )
                          })
                        )
                      ])
                    ])
                  ])
                ]
              )
            })
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
    require("vue-hot-reload-api")      .rerender("data-v-0e381668", module.exports)
  }
}

/***/ }),

/***/ 2787:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2788)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2790)
/* template */
var __vue_template__ = __webpack_require__(2842)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-017811b7"
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
Component.options.__file = "app/FaveoReport/views/js/components/ReportEntryPage.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-017811b7", Component.options)
  } else {
    hotAPI.reload("data-v-017811b7", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2788:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2789);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("fa6971a4", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-017811b7\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ReportEntryPage.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-017811b7\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ReportEntryPage.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2789:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),

/***/ 2790:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__helpers_utils__ = __webpack_require__(117);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

  name: 'report-entry-page',

  components: {
    'ticket-filter': __webpack_require__(88),
    'tabular-report-layout': __webpack_require__(2791),
    'time-series-chart': __webpack_require__(232),
    'category-based-report': __webpack_require__(2832),
    'save-report-modal': __webpack_require__(2837),
    'alert': __webpack_require__(6),
    "tool-tip": __webpack_require__(25)
  },

  data: function data() {
    return {
      reportConfigObj: null,
      clonedReportConfigOnj: null, // usded to save/update report
      isLoading: true,
      openSaveReportModal: false,
      modalMode: '',
      filterParams: {},
      isShowFilter: false,
      dataCount: 0
    };
  },

  beforeMount: function beforeMount() {
    this.dashboardInit();
  },
  created: function created() {
    window.eventHub.$on('onColumnUpdate', this.onColumnUpdate);
    window.eventHub.$on('refreshReportEntryPage', this.dashboardInit);
    window.eventHub.$on('dataCount', this.getCount);
  },


  computed: {

    /**
     * function to show export button only in tabular report
     * remove this logic once export in chart will be implemented
     */
    showExportButton: function showExportButton() {
      if (this.reportConfigObj && Array.isArray(this.reportConfigObj.sub_reports)) {
        for (var i = 0; i < this.reportConfigObj.sub_reports.length; i++) {
          if (this.reportConfigObj.sub_reports[i].data_type === 'datatable') {
            return true;
          }
        }
      }
      return false;
    }
  },

  methods: {
    getCount: function getCount(value) {

      this.dataCount = value;
    },
    toggleFilterView: function toggleFilterView() {

      this.isShowFilter = !this.isShowFilter;
    },
    dashboardInit: function dashboardInit() {
      this.getReportConfiguration(Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["m" /* getIdFromUrl */])(window.location.pathname));
    },
    getLayoutClass: function getLayoutClass(layout) {
      return Object(__WEBPACK_IMPORTED_MODULE_3__helpers_utils__["a" /* getColumnClass */])(layout);
    },


    /** Get report configuration object from server */
    getReportConfiguration: function getReportConfiguration(reportId) {
      var _this = this;

      this.isLoading = true;
      var params = Object(__WEBPACK_IMPORTED_MODULE_3__helpers_utils__["c" /* getValidFilterObject */])(this.filterParams);
      params.include_filters = 1;

      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('api/agent/report-config/' + reportId, { params: params }).then(function (res) {
        _this.reportConfigObj = res.data.data;
        _this.clonedReportConfigOnj = JSON.parse(JSON.stringify(_this.reportConfigObj));
        _this.updateFilterObj();
      }).catch(function (err) {
        Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(err, 'report-entry-page');
      }).finally(function (res) {
        _this.isLoading = false;
      });
    },


    /** Update local copy of filter object with the filter-object recieved form api response */
    updateFilterObj: function updateFilterObj() {
      var filterObj = {};
      if (Array.isArray(this.reportConfigObj.filters)) {
        this.reportConfigObj.filters.forEach(function (element) {
          filterObj[element.key] = element.value;
        });
      }
      this.filterParams = filterObj;
    },


    /** Export report */
    exportReport: function exportReport() {
      var _this2 = this;

      this.isLoading = true;
      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.post(this.reportConfigObj.export_url, this.filterParams).then(function (res) {
        Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["b" /* successHandler */])(res, 'tabular-report-layout');
      }).catch(function (err) {
        Object(__WEBPACK_IMPORTED_MODULE_2_helpers_responseHandler__["a" /* errorHandler */])(err, 'tabular-report-layout');
      }).finally(function () {
        _this2.isLoading = false;
      });
    },


    /** Emit event for forking the report */
    forkUpdateAction: function forkUpdateAction(actionType) {
      window.eventHub.$emit('performApplyAction');
      this.modalMode = actionType;
      this.openSaveReportModal = true;
    },


    /** close save report modal */
    closeSaveReportModal: function closeSaveReportModal() {
      this.openSaveReportModal = false;
    },


    /** set filter values to report-config-object */
    setFilter: function setFilter(payload) {
      this.filterParams = JSON.parse(JSON.stringify(payload));
      this.clonedReportConfigOnj.filters = Object(__WEBPACK_IMPORTED_MODULE_3__helpers_utils__["b" /* getFilterObjectToArray */])(this.filterParams);
    },


    /** Update local copy of `key` property with the updated one */
    updateChangedValue: function updateChangedValue(newValue, reportIndex, key) {
      this.clonedReportConfigOnj.sub_reports[reportIndex][key] = newValue;
    },


    /** Update the local copy of the column list in case of tabular report */
    onColumnUpdate: function onColumnUpdate(columns, reportIndex) {
      this.clonedReportConfigOnj.sub_reports[reportIndex].columns = columns;
    }
  }
});

/***/ }),

/***/ 2791:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2792)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2794)
/* template */
var __vue_template__ = __webpack_require__(2811)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-5763f906"
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
Component.options.__file = "app/FaveoReport/views/js/components/Common/TabularReportLayout.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-5763f906", Component.options)
  } else {
    hotAPI.reload("data-v-5763f906", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2792:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2793);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("a7a4bd7e", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-5763f906\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TabularReportLayout.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-5763f906\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TabularReportLayout.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2793:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.tabular-report-layout-box[data-v-5763f906] {\n  padding: 0px !important;\n}\n.column-list-right[data-v-5763f906] { float : right;\n}\n", ""]);

// exports


/***/ }),

/***/ 2794:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox__);
//
//
//
//
//
//
//
//
//
//
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

  name: 'tabular-report-layout',

  description: 'Common layout component for tabular reports',

  props: {

    // Api endpoint for getting table data
    dataUrl: {
      type: String,
      required: true
    },

    // Api endpoint for getting table column list
    tableColumns: {
      type: Array,
      required: true
    },

    // Api endpoint for export call
    exportUrl: {
      type: String,
      required: true
    },

    // Api endpoint for saving cloumns order, visibility etc
    subReportId: {
      type: Number,
      required: true
    },

    /**
     * Url endpoint for adding custom column
     */
    addCustomColumnUrl: {
      type: String,
      required: true
    },

    /**
     * Url endpoint for deleting custom column
     */
    deleteCustomColumnUrl: {
      type: String,
      required: true
    },

    /**
     * Url endpoint for getting short codes
     */
    shortCodeUrl: {
      type: String,
      required: true
    },

    // Defualt filter field value objec
    filterParams: {
      type: Object,
      default: function _default() {}
    },

    reportIndex: {
      type: Number,
      required: true
    }

  },

  data: function data() {
    return {
      isLoading: false,
      columns: [],
      visibleColumns: {}
    };
  },
  created: function created() {
    window.eventHub.$on('onColumnUpdate', this.onColumnUpdate);
  },
  beforeMount: function beforeMount() {
    this.columns = this.tableColumns;
    this.updateVisibleColumns();
  },


  methods: {
    onColumnUpdate: function onColumnUpdate(columns) {
      this.columns = columns;
      this.updateVisibleColumns();
    },
    updateVisibleColumns: function updateVisibleColumns() {
      var _this = this;

      // setting visibleColumns to empty so that old values can be removed
      this.visibleColumns = {};

      this.columns.map(function (column) {
        if (Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["c" /* boolean */])(column.is_visible)) {
          _this.visibleColumns[column.key] = column.label;
        }
      });
    }
  },

  components: {
    'ticket-filter': __webpack_require__(88),
    'dynamic-datatable': __webpack_require__(2795),
    'alert': __webpack_require__(6),
    'custom-loader': __webpack_require__(9),
    'column-list': __webpack_require__(2800)
  }
});

/***/ }),

/***/ 2795:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2796)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2798)
/* template */
var __vue_template__ = __webpack_require__(2799)
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
Component.options.__file = "resources/assets/js/components/Extra/DynamicDatatable.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-dcba13ca", Component.options)
  } else {
    hotAPI.reload("data-v-dcba13ca", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2796:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2797);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("19cc9ffe", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-dcba13ca\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./DynamicDatatable.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-dcba13ca\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./DynamicDatatable.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2797:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n#dynamic-datatable-container .ticket_number {\n    min-width: 130px;\n}\n#dynamic-datatable-container .subject {\n    min-width: 300px;\n}\n#dynamic-datatable-container td{\n    min-width: -webkit-max-content !important;\n    min-width: -moz-max-content !important;\n    min-width: max-content !important;\n}\n#dynamic-datatable-container .VueTables__sortable {\n    min-width: 100px;\n}\n\n  /* overwriting datatable loader class  */\n.faveo-datatable-loader {\n\t\tmargin-top: 0px !important;\n\t\tmargin-bottom: 0px !important;\n}\n#dynamic-datatable {\n    padding-bottom: 45px;\n}\n", ""]);

// exports


/***/ }),

/***/ 2798:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
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






/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {

      /**
       * Name of the columns in array format
       * @type {Array}
       */
      columnNames: [],

      /**
       * Datatable options
       * @type {Object}
       */
      options: {},

      /**
       * Contains the key and component of the data
       * for eg. for priority, we dynamically create a component with name priority-hyperlink,
       * not the mapping of this, will be stored in this variable. Like so :
       * { "priority" : "priority-hyperlink" }
       * @type {Object}
       */
      templateObject: {}
    };
  },


  props: {

    /**
     * Api endpoint for getting table data
     */
    dataUrl: {
      type: String,
      required: true
    },

    /**
     * Object with key and label of a column
     * for eg. { ticket_number : "Ticket Number" }
     * @type {Object}
     */
    columns: { type: Object, required: true },

    /**
     * Array of objects of column
     * @type {Object}
     */
    columnsMeta: { type: Array, required: true },

    /**
     * ticket filter paramaters
     * @type {Object}
     */
    filterParams: { type: Object, required: true }
  },

  beforeMount: function beforeMount() {

    this.prepareDynamicComponents();

    this.updateColumnNames();

    this.updateOptions();
  },


  methods: {

    /**
     * Prepares component dynamically so that additional files should not be written for that
     * @return {Void}
     */
    prepareDynamicComponents: function prepareDynamicComponents() {
      var _this = this;

      // loop over all columns and whichever is html,
      // render it like html
      // whichever is timestamp, render it like timestamp
      this.columnsMeta.map(function (column) {

        // making "." replace with a "-", so that it can be a valid component name
        var key = column.key.replace(/\./g, '-');

        if (column.is_html) {
          _this.getHyperlinkInstance(key, column.key);
        }

        // formatting columns which are non custom, custom columns will be formatted from backend
        if (column.is_timestamp) {

          var timestampFormat = Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["d" /* carbonToMomentFormatter */])(column.timestamp_format);
          _this.getTimestampInstance(key, column.key, timestampFormat);
        }
      });
    },


    /**
     * takes column keys and assign to columnNames state
     * @return {void}
     */
    updateColumnNames: function updateColumnNames() {
      this.columnNames = Object.keys(this.columns);
    },
    sortableFields: function sortableFields() {
      var sortables = [];

      this.columnsMeta.map(function (column) {
        if (column.is_sortable) {
          sortables.push(column.key);
        }
      });
      return sortables;
    },


    /**
     * create hyperlink vue instance of dependencies dynamically
     * @param  {string} componentName
     * @param  {string} hyperlinkPath path of the dependency in the API
     *                                for eg. in {priority: {id:1, name:'hyperlink'}},
     *                                it will be priority.name
     * @return {void}
     */
    getHyperlinkInstance: function getHyperlinkInstance(componentName, keyName) {

      // mapping keyname with componentName in template so that datatable can mount it as a component
      this.templateObject[keyName] = componentName;

      var absoluteHyperlinkPath = 'data.' + keyName;

      return __WEBPACK_IMPORTED_MODULE_0_vue___default.a.component(componentName, { props: ['data'],
        template: "<span>" + "<span v-if=data." + keyName + " v-html=" + absoluteHyperlinkPath + "></span>" + "<span v-else>--</span>" + "</span>"
      });
    },


    /**
     * create hyperlink vue instance of dependencies dynamically
     * @param  {string} componentName
     * @param  {string} hyperlinkPath path of the dependency in the API
     *                                for eg. in {priority: {id:1, name:'hyperlink'}},
     *                                it will be priority.name
     * @return {void}
     */
    getTimestampInstance: function getTimestampInstance(componentName, keyName, timestampFormat) {

      // mapping keyname with componentName in template so that datatable can mount it as a component
      this.templateObject[keyName] = componentName;

      var value = 'data.' + keyName;
      var timestampFormatVariable = 'timestampFormat';

      __WEBPACK_IMPORTED_MODULE_0_vue___default.a.component('report-timestamp-' + componentName, { props: ['data', 'timestampFormat'],
        template: "<span v-html=customFormattedTime(" + value + "," + timestampFormatVariable + ")></span>",
        computed: _extends({}, Object(__WEBPACK_IMPORTED_MODULE_2_vuex__["b" /* mapGetters */])(['customFormattedTime']))
      });

      return __WEBPACK_IMPORTED_MODULE_0_vue___default.a.component(componentName, { props: ['data'],
        template: "<report-timestamp-" + componentName + " :data='data' timestampFormat='" + timestampFormat + "'></report-timestamp-" + componentName + ">"
      });
    },
    updateOptions: function updateOptions() {
      var _this2 = this;

      this.options = {

        headings: this.columns,

        perPageValues: [10, 25, 50, 100, 200, 500],

        sortIcon: {

          base: 'glyphicon',

          up: 'glyphicon-chevron-down',

          down: 'glyphicon-chevron-up'
        },

        templates: this.templateObject,

        texts: {
          'filter': '',
          'limit': ''
        },

        responseAdapter: function responseAdapter(_ref) {
          var data = _ref.data;

          window.eventHub.$emit('dataCount', data.data.total);
          return {

            data: data.data.data,

            count: data.data.total
          };
        },


        requestAdapter: function requestAdapter(data) {

          var defaultParams = {
            "sort-field": data.orderBy,

            "sort-order": data.ascending ? 'desc' : 'asc',

            "search-query": data.query.trim(),

            "page": data.page,

            "limit": data.limit
          };

          return _extends({}, defaultParams, _this2.filterParams);
        },
        // we need this only for body of the ticket. For others its width can
        // be taken as max content
        columnsClasses: {
          ticket_number: 'ticket_number',
          subject: 'subject'
        },

        sortable: this.sortableFields()
      };
    }
  },

  watch: {
    filterParams: function filterParams() {
      window.eventHub.$emit('refreshData');
    },
    columns: function columns() {
      this.prepareDynamicComponents();
      this.updateColumnNames();
      this.updateOptions();
      window.eventHub.$emit('refreshData');
    }
  },

  components: {
    'data-table': __webpack_require__(17)
  }
});

/***/ }),

/***/ 2799:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { attrs: { id: "dynamic-datatable-container" } },
    [
      _c("data-table", {
        attrs: {
          id: "dynamic-datatable",
          url: _vm.dataUrl,
          dataColumns: _vm.columnNames,
          option: _vm.options,
          scroll_to: "dynamic-datatable"
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
    require("vue-hot-reload-api")      .rerender("data-v-dcba13ca", module.exports)
  }
}

/***/ }),

/***/ 2800:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2801)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2803)
/* template */
var __vue_template__ = __webpack_require__(2810)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-5a49dc42"
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
Component.options.__file = "app/FaveoReport/views/js/components/Common/ColumnList.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-5a49dc42", Component.options)
  } else {
    hotAPI.reload("data-v-5a49dc42", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2801:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2802);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("6e8a29fa", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-5a49dc42\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ColumnList.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-5a49dc42\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ColumnList.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2802:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.report-column-element[data-v-5a49dc42]{\n\t min-width: -webkit-max-content;\n\t min-width: -moz-max-content;\n\t min-width: max-content;\n\t min-height: 30px;\n}\n.column-label[data-v-5a49dc42] {\n\t cursor: pointer;\n}\n#report-columns-dropdown[data-v-5a49dc42] {\n\t padding: 10px;\n\t max-height: 55vh;\n\t overflow: scroll;\n\t border: 1px solid gainsboro;\n}\n.drag-btn[data-v-5a49dc42] {\n\t cursor: move;\n\t visibility: hidden;\n}\n.report-column-list:hover>.drag-btn[data-v-5a49dc42]{\n\t visibility: visible;\n}\n.column-list-btn-element[data-v-5a49dc42] {\n\t margin: 1rem 0.8rem 0.8rem 0;\n}\n\n", ""]);

// exports


/***/ }),

/***/ 2803:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vuedraggable__ = __webpack_require__(56);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_vuedraggable___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_vuedraggable__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
	name: 'column-list',

	data: function data() {
		return {

			/**
    * If waiting for server to respond
    */
			loading: false,

			/**
    * If first time api call has been made
    */
			hasDataPopulated: false,

			/**
    * List of columns
    */
			columns: [],

			/**
    * Currently Editing column
    */
			column: {},

			/**
    * If custom column pop is visible
    */
			showAddCustomColumn: false,

			// If a modal is opened in edit mode
			isEditingCustomColumn: false
		};
	},


	props: {

		/**
   * Url endpoint for getting table columns
   */
		tableColumns: {
			type: Array,
			required: true
		},

		/**
   * Url endpoint for adding custom column
   */
		addCustomColumnUrl: {
			type: String,
			required: true
		},

		/**
   * Url endpoint for deleting custom column
   */
		deleteCustomColumnUrl: {
			type: String,
			required: true
		},

		/**
   * Url endpoint for getting short codes
   */
		shortCodeUrl: {
			type: String,
			required: true
		},

		/**
   * Report id 
   * used for column apply/update
   */
		subReportId: {
			type: Number,
			required: true
		},

		/**
   * Index value of the report
   * used for updating changed value
   */
		reportIndex: {
			type: Number,
			required: true
		}
	},

	beforeMount: function beforeMount() {

		this.hasDataPopulated = false;

		this.columns = this.tableColumns;
	},
	created: function created() {
		window.eventHub.$on('refresh-report', this.getDataFromServer);
	},


	methods: {

		/**
   * Gets data from server
   * @return {Void}
   */
		getDataFromServer: function getDataFromServer() {
			var _this = this;

			this.loading = true;

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('api/agent/report-columns/' + this.subReportId).then(function (res) {

				_this.columns = res.data.data;

				window.eventHub.$emit('onColumnUpdate', _this.columns, _this.reportIndex);
			}).catch(function (err) {

				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, "tabular-report-layout");
			}).finally(function (res) {

				_this.loading = false;
				_this.hasDataPopulated = true;
			});
		},


		/**
   * Saves selected column on the server
   * @return {Void}
   */
		saveColumns: function saveColumns() {
			var _this2 = this;

			this.columns.forEach(function (element, index) {
				element.order = index + 1;
			});

			window.eventHub.$emit('onColumnUpdate', this.columns, this.reportIndex);

			this.loading = true;

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.post('api/agent/report-columns/' + this.subReportId, this.columns).then(function (res) {
				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'tabular-report-layout');
			}).catch(function (err) {
				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'tabular-report-layout');
			}).finally(function () {
				_this2.loading = false;
			});
		},
		onDelete: function onDelete(id) {
			var _this3 = this;

			this.loading = true;
			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.delete(this.deleteCustomColumnUrl + '/' + id).then(function (res) {
				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'tabular-report-layout');
				_this3.getDataFromServer();
			}).catch(function (err) {
				Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'tabular-report-layout');
			}).finally(function () {
				_this3.loading = false;
			});
		},
		onLabelClick: function onLabelClick(event, clickedColumn) {
			clickedColumn.is_visible = !clickedColumn.is_visible;
			this.preventToCloseBox(event);
		},
		preventToCloseBox: function preventToCloseBox(event) {
			event.stopPropagation();
		},
		onEdit: function onEdit(column) {
			this.showAddCustomColumn = true;
			this.isEditingCustomColumn = true;
			this.column = column;
		},
		hideCustomColumn: function hideCustomColumn() {
			this.showAddCustomColumn = false;
			this.isEditingCustomColumn = false;
			this.column = {};
		}
	},

	components: {
		'custom-column': __webpack_require__(2804),
		'custom-loader': __webpack_require__(9),
		'draggable-element': __WEBPACK_IMPORTED_MODULE_3_vuedraggable___default.a
	}
});

/***/ }),

/***/ 2804:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2805)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2807)
/* template */
var __vue_template__ = __webpack_require__(2809)
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
Component.options.__file = "app/FaveoReport/views/js/components/Common/CustomReportColumn.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-60117969", Component.options)
  } else {
    hotAPI.reload("data-v-60117969", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2805:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2806);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("c8fee9c2", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-60117969\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./CustomReportColumn.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-60117969\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./CustomReportColumn.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2806:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.custom-column-modal-body {\n  max-height: 60vh;\n  overflow-y: auto;\n}\n.form-control { -webkit-box-shadow : none !important; box-shadow : none !important;\n}\n", ""]);

// exports


/***/ }),

/***/ 2807:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_components_MiniComponent_FaveoBox__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__helpers_validator_customColumnRules__ = __webpack_require__(2808);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_components_Common_Modal__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_components_Common_Modal___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_components_Common_Modal__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_5_axios__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

  name: 'custom-report-column',

  description: 'handles adding custom columns in reports',

  props: {

    /**
     * handler for closing the component view
     */
    closeView: { type: Function, required: true },

    /**
     * The column which is getting edited
     */
    column: { type: Object, default: function _default() {
        return {};
      } },

    /**
     * If a column is getting edited
     */
    isEditing: { type: Boolean, default: false },

    /**
     * Url endpoint for adding custom columns
     */
    addCustomColumnUrl: {
      type: String,
      required: true
    },

    /**
     * Url endpoint for getting short codes
     */
    shortCodeUrl: {
      type: String,
      required: true
    }
  },

  data: function data() {
    return {

      /**
       * Id of the record (0 for a new record)
       */
      id: 0,

      /**
       * Name of the column
       */
      name: "",

      /**
       * airthmetic equation of the columns
       */
      equation: "",

      /**
       * If the column created is a timestamp
       */
      isTimestamp: false,

      // Timestamp formats if the column created is timestamp
      timestampFormats: ["F j, Y g:i  a", "Y-m-d g:i a", "d-m-Y g:i a", "m-d-Y g:i a", "F j, Y", "Y-m-d", "d-m-Y", "m-d-Y", "g:i  a"],

      // Selected timestamp format
      selectedTimestampFormat: '',

      /**
       * if waiting for api call response
       */
      loading: false,

      /**
       * List of available short-codes in report
       */
      shortCodes: []
    };
  },
  beforeMount: function beforeMount() {
    this.getShortCodes();

    // Set default value for timestamp_format
    this.selectedTimestampFormat = this.timestampFormats[0];

    if (this.isEditing) {
      this.populateColumnData();
    }
  },


  methods: {

    // get date-time example in human understandable form corresponding to it's carbon format
    getTimestampExample: function getTimestampExample() {
      return Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["l" /* getCurrentFormattedTime */])(Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["d" /* carbonToMomentFormatter */])(this.selectedTimestampFormat));
    },


    /**
     * Populates local state with edit data
     * @return {Void}
     */
    populateColumnData: function populateColumnData() {
      this.id = this.column.id;
      this.name = this.column.label;
      this.equation = this.column.equation;
      this.isTimestamp = this.column.is_timestamp;
      this.selectedTimestampFormat = this.column.timestamp_format;
    },


    /**
     * checks if custom column data is valid
     * @return {Boolean}
     */
    isValid: function isValid() {
      var _validateCustomColumn = Object(__WEBPACK_IMPORTED_MODULE_3__helpers_validator_customColumnRules__["a" /* validateCustomColumn */])(this.$data),
          errors = _validateCustomColumn.errors,
          isValid = _validateCustomColumn.isValid;

      if (!isValid) {
        return false;
      }
      return true;
    },


    /**
     * Submits column data to server
     * @return {Void}
     */
    onSubmit: function onSubmit() {
      var _this = this;

      if (this.isValid()) {

        this.loading = true;

        var params = { name: this.name, equation: this.equation,
          is_timestamp: this.isTimestamp, id: this.id };

        // If isTimestamp is true add selectedTimestampFormat to params
        if (this.isTimestamp) {
          params.timestamp_format = this.selectedTimestampFormat;
        }

        __WEBPACK_IMPORTED_MODULE_5_axios___default.a.post(this.addCustomColumnUrl, params).then(function (res) {

          // giving 2 seconds to display success message
          setTimeout(function () {
            return _this.closeView();
          }, 2000);
          Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, "custom-report-column");
          window.eventHub.$emit('refresh-report');
        }).catch(function (err) {

          Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, "custom-report-column");
        }).finally(function (res) {

          _this.loading = false;
          _this.hasDataPopulated = true;
        });
      }
    },


    /**
     * Gets shortcodes from server
     * @return {Void}
     */
    getShortCodes: function getShortCodes() {
      var _this2 = this;

      __WEBPACK_IMPORTED_MODULE_5_axios___default.a.get(this.shortCodeUrl).then(function (res) {
        _this2.shortCodes = res.data.data;
      }).catch(function (err) {
        Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err);
      });
    },


    /**
     * handle state change through form
     * @param  {String} value
     * @param  {String} name
     * @return {Void}
     */
    onChange: function onChange(value, name) {
      this[name] = value;
    }
  },

  components: {
    "text-field": __webpack_require__(11),
    "checkbox": __webpack_require__(41),
    "modal": __WEBPACK_IMPORTED_MODULE_4_components_Common_Modal___default.a,
    "custom-loader": __webpack_require__(9),
    "relative-loader": __webpack_require__(34),
    'dynamic-select': __webpack_require__(14)
  }
});

/***/ }),

/***/ 2808:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = validateCustomColumn;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_easy_validator_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);




/**
 * @param {object} data      emailSettings component data
 * @return {object}          object of errors and isValid (form is valid or not)
 * */
function validateCustomColumn(data) {
    var name = data.name,
        equation = data.equation;

    //rules has to apply only after checking conditions

    var validatingData = {
        name: [name, 'isRequired', 'max(60)'],
        equation: [equation, 'isRequired', 'max(200)']
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

/***/ 2809:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { attrs: { id: "custom-report-column" } },
    [
      _c(
        "modal",
        {
          attrs: {
            showModal: true,
            onClose: _vm.closeView,
            modalBodyClass: "custom-column-modal-body"
          }
        },
        [
          _c(
            "div",
            { attrs: { slot: "title" }, slot: "title" },
            [
              _c("h4", { staticClass: "modal-title" }, [
                _vm._v(_vm._s(_vm.lang("custom_column")))
              ]),
              _vm._v(" "),
              _vm.loading ? _c("custom-loader") : _vm._e()
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            { attrs: { slot: "alert" }, slot: "alert" },
            [_c("alert", { attrs: { componentName: "custom-report-column" } })],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            {
              attrs: {
                slot: "fields",
                id: "link-container",
                refs: "modalBodyRef"
              },
              slot: "fields"
            },
            [
              _c("div", { attrs: { id: "short-code-container" } }, [
                _c("div", { staticClass: "card card-light " }, [
                  _c("div", { staticClass: "card-header" }, [
                    _c("h3", { staticClass: "card-title" }, [
                      _vm._v(_vm._s(_vm.lang("list_of_available_shortcodes")))
                    ])
                  ]),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticClass: "card-body" },
                    [
                      !_vm.shortCodes.length
                        ? _c("relative-loader")
                        : _c(
                            "ul",
                            {
                              staticClass: "row",
                              attrs: { id: "report-shortcode-list" }
                            },
                            _vm._l(_vm.shortCodes, function(shortcode) {
                              return _c("li", { staticClass: "col col-sm-6" }, [
                                _vm._v(_vm._s(shortcode))
                              ])
                            })
                          )
                    ],
                    1
                  )
                ])
              ]),
              _vm._v(" "),
              _c("text-field", {
                attrs: {
                  label: _vm.lang("column_name"),
                  value: _vm.name,
                  name: "name",
                  onChange: _vm.onChange,
                  classname: "col-sm-12",
                  hint: _vm.lang("report_custom_column_name_hint")
                }
              }),
              _vm._v(" "),
              _c("text-field", {
                attrs: {
                  label: _vm.lang("equation"),
                  value: _vm.equation,
                  name: "equation",
                  onChange: _vm.onChange,
                  classname: "col-sm-12",
                  type: "textarea",
                  hint: _vm.lang("report_custom_column_equation_hint")
                }
              }),
              _vm._v(" "),
              _c("checkbox", {
                attrs: {
                  name: "isTimestamp",
                  value: _vm.isTimestamp,
                  label: _vm.lang("is_timestamp"),
                  onChange: _vm.onChange,
                  classname: "col-sm-12"
                }
              }),
              _vm._v(" "),
              _c(
                "div",
                {
                  directives: [
                    {
                      name: "show",
                      rawName: "v-show",
                      value: _vm.isTimestamp,
                      expression: "isTimestamp"
                    }
                  ],
                  staticClass: "row"
                },
                [
                  _c("div", { staticClass: "col-sm-6" }, [
                    _c("label", { attrs: { for: "timestamp_format" } }, [
                      _vm._v(_vm._s(_vm.lang("timestamp_format")))
                    ]),
                    _vm._v(" "),
                    _c(
                      "select",
                      {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.selectedTimestampFormat,
                            expression: "selectedTimestampFormat"
                          }
                        ],
                        staticClass: "form-control",
                        attrs: {
                          id: "timestamp_format",
                          name: "timestamp_format"
                        },
                        on: {
                          change: function($event) {
                            var $$selectedVal = Array.prototype.filter
                              .call($event.target.options, function(o) {
                                return o.selected
                              })
                              .map(function(o) {
                                var val = "_value" in o ? o._value : o.value
                                return val
                              })
                            _vm.selectedTimestampFormat = $event.target.multiple
                              ? $$selectedVal
                              : $$selectedVal[0]
                          }
                        }
                      },
                      _vm._l(_vm.timestampFormats, function(item, index) {
                        return _c(
                          "option",
                          { key: index, domProps: { value: item } },
                          [_vm._v(_vm._s(item))]
                        )
                      })
                    )
                  ]),
                  _vm._v(" "),
                  _c("text-field", {
                    attrs: {
                      name: "timestamp_format_example",
                      label: _vm.lang("preview"),
                      value: _vm.getTimestampExample(),
                      classname: "col-sm-6",
                      disabled: true,
                      onChange: function() {}
                    }
                  })
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c("div", { attrs: { slot: "controls" }, slot: "controls" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-primary update-btn",
                attrs: {
                  type: "button",
                  id: "custom-column-submit",
                  disabled: _vm.loading
                },
                on: { click: _vm.onSubmit }
              },
              [
                _c("span", { staticClass: "fas fa-sync" }),
                _vm._v(" " + _vm._s(_vm.lang("submit")) + "\n      ")
              ]
            )
          ])
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
    require("vue-hot-reload-api")      .rerender("data-v-60117969", module.exports)
  }
}

/***/ }),

/***/ 2810:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "span",
    { attrs: { id: "column-list" } },
    [
      _c("div", { staticClass: "btn-group float-right" }, [
        _c(
          "button",
          {
            staticClass: "btn btn-default dropdown-toggle",
            attrs: {
              type: "button",
              "data-toggle": "dropdown",
              "aria-expanded": "true",
              id: "dropdown-menu-columns"
            }
          },
          [
            _c("i", { staticClass: "fas fa-columns" }),
            _vm._v(" " + _vm._s(_vm.lang("columns")) + "\n\t\t")
          ]
        ),
        _vm._v(" "),
        _c(
          "div",
          {
            staticClass: "dropdown-menu dropdown-menu-right",
            attrs: {
              role: "menu",
              "x-placement": "bottom-end",
              "aria-labelledby": "dropdown-menu-columns",
              id: "report-columns-dropdown"
            }
          },
          [
            _c(
              "draggable-element",
              {
                staticClass: "report-column-element",
                attrs: { handle: ".drag-btn" },
                model: {
                  value: _vm.columns,
                  callback: function($$v) {
                    _vm.columns = $$v
                  },
                  expression: "columns"
                }
              },
              [
                _vm._l(_vm.columns, function(column) {
                  return [
                    _c(
                      "a",
                      {
                        key: column.id,
                        staticClass: "dropdown-item report-column-list",
                        attrs: { href: "javascript:;" }
                      },
                      [
                        _c("input", {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: column.is_visible,
                              expression: "column.is_visible"
                            }
                          ],
                          attrs: { type: "checkbox" },
                          domProps: {
                            value: column.key,
                            checked: Array.isArray(column.is_visible)
                              ? _vm._i(column.is_visible, column.key) > -1
                              : column.is_visible
                          },
                          on: {
                            change: function($event) {
                              var $$a = column.is_visible,
                                $$el = $event.target,
                                $$c = $$el.checked ? true : false
                              if (Array.isArray($$a)) {
                                var $$v = column.key,
                                  $$i = _vm._i($$a, $$v)
                                if ($$el.checked) {
                                  $$i < 0 &&
                                    _vm.$set(
                                      column,
                                      "is_visible",
                                      $$a.concat([$$v])
                                    )
                                } else {
                                  $$i > -1 &&
                                    _vm.$set(
                                      column,
                                      "is_visible",
                                      $$a
                                        .slice(0, $$i)
                                        .concat($$a.slice($$i + 1))
                                    )
                                }
                              } else {
                                _vm.$set(column, "is_visible", $$c)
                              }
                            }
                          }
                        }),
                        _vm._v(" "),
                        _c(
                          "span",
                          {
                            staticClass: "column-label",
                            on: {
                              click: function(event) {
                                return _vm.onLabelClick(event, column)
                              }
                            }
                          },
                          [_vm._v(_vm._s(column.label))]
                        ),
                        _vm._v(" "),
                        column.is_custom
                          ? _c(
                              "button",
                              {
                                staticClass:
                                  "btn btn-danger btn-xs float-right",
                                on: {
                                  click: function() {
                                    return _vm.onDelete(column.id)
                                  }
                                }
                              },
                              [_c("i", { staticClass: "fas fa-trash" })]
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        column.is_custom
                          ? _c(
                              "button",
                              {
                                staticClass:
                                  "btn btn-primary btn-xs float-right margin-horizontal-5",
                                on: {
                                  click: function() {
                                    return _vm.onEdit(column)
                                  }
                                }
                              },
                              [_c("i", { staticClass: "fas fa-edit" })]
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _c(
                          "span",
                          {
                            staticClass:
                              "float-right margin-horizontal-5 drag-btn",
                            attrs: { title: "Move this column" },
                            on: { click: _vm.preventToCloseBox }
                          },
                          [
                            _c("i", {
                              staticClass: "fas fa-arrows-alt-v",
                              attrs: { "aria-hidden": "true" }
                            })
                          ]
                        )
                      ]
                    )
                  ]
                }),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticClass: "column-list-btn-element",
                    attrs: { slot: "footer" },
                    slot: "footer"
                  },
                  [
                    _c(
                      "button",
                      {
                        staticClass: "btn btn-sm btn-primary",
                        attrs: { type: "button", id: "save-columns" },
                        on: { click: _vm.saveColumns }
                      },
                      [
                        _c("i", { staticClass: "fas fa-sync" }),
                        _vm._v(" " + _vm._s(_vm.lang("apply")) + "\n\t\t\t\t\t")
                      ]
                    ),
                    _vm._v(" "),
                    _c(
                      "button",
                      {
                        staticClass: "btn btn-sm btn-primary",
                        attrs: { id: "add-custom-column" },
                        on: {
                          click: function() {
                            return (_vm.showAddCustomColumn = true)
                          }
                        }
                      },
                      [
                        _c("i", { staticClass: "fas fa-plus" }),
                        _vm._v(
                          " " +
                            _vm._s(_vm.lang("add_custom_column")) +
                            "\n\t\t\t\t\t"
                        )
                      ]
                    )
                  ]
                )
              ],
              2
            )
          ],
          1
        )
      ]),
      _vm._v(" "),
      _vm.showAddCustomColumn
        ? _c("custom-column", {
            attrs: {
              "add-custom-column-url": _vm.addCustomColumnUrl,
              "short-code-url": _vm.shortCodeUrl,
              column: _vm.column,
              closeView: _vm.hideCustomColumn,
              isEditing: _vm.isEditingCustomColumn
            }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.loading && _vm.hasDataPopulated ? _c("custom-loader") : _vm._e()
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
    require("vue-hot-reload-api")      .rerender("data-v-5a49dc42", module.exports)
  }
}

/***/ }),

/***/ 2811:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("alert", { attrs: { componentName: "tabular-report-layout" } }),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "column-list-right" },
        [
          _c("column-list", {
            attrs: {
              "table-columns": _vm.columns,
              "sub-report-id": _vm.subReportId,
              "add-custom-column-url": _vm.addCustomColumnUrl,
              "delete-custom-column-url": _vm.deleteCustomColumnUrl,
              "short-code-url": _vm.shortCodeUrl,
              "report-index": _vm.reportIndex
            }
          })
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "div",
        [
          _vm.columns.length
            ? _c("dynamic-datatable", {
                attrs: {
                  "data-url": _vm.dataUrl,
                  columns: _vm.visibleColumns,
                  columnsMeta: _vm.columns,
                  filterParams: _vm.filterParams
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
    require("vue-hot-reload-api")      .rerender("data-v-5763f906", module.exports)
  }
}

/***/ }),

/***/ 2812:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2813);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("7374f00f", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7e1beb5c\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TimeSeriesChart.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7e1beb5c\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./TimeSeriesChart.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2813:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.panel-heading[data-v-7e1beb5c] {\n  padding: 6px 15px;\n}\n.panel-heading.row[data-v-7e1beb5c] {\n  margin: 0;\n}\n.rpt_icon[data-v-7e1beb5c] {\n  cursor: pointer;\n}\n.chart-panel-heading[data-v-7e1beb5c] {\n  padding-left: 0;\n  padding-top: 0.5rem;\n}\n.chart-list[data-v-7e1beb5c] {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -ms-flex-wrap: wrap;\n      flex-wrap: wrap;\n}\n#chart-container[data-v-7e1beb5c] {\n  margin: 1rem -5px 1rem -5px;\n}\n\n", ""]);

// exports


/***/ }),

/***/ 2814:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_ChartFactory_utils__ = __webpack_require__(51);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__helpers_utils__ = __webpack_require__(117);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

  name: 'time-series-chart',

  components: {
    'faveo-chart': __webpack_require__(233),
    'relative-loader': __webpack_require__(34),
    'data-widget': __webpack_require__(111)
  },

  props: {

    // Api endpoint to fetch chart data
    chartDataApi: {
      type: String,
      required: true
    },

    // Api endpoint to fetch data widget data 
    dataWidgetApi: {
      type: String,
      default: function _default() {
        return undefined;
      }
    },

    // Category array 
    categories: {
      type: Array,
      default: function _default() {
        return [];
      }
    },

    // Default category option
    defaultCategory: {
      type: String,
      default: function _default() {
        return '';
      }
    },

    /**
     * string to show in the category dropdown
     * used as key for fetching data from server basis of category
     */
    categoryPrefix: {
      type: String,
      default: function _default() {
        return 'view_by';
      }
    },

    // Defualt filter field value objec
    filterParams: {
      type: Object,
      default: function _default() {}
    },

    /**
     * Report array index
     */
    reportIndex: {
      type: Number,
      required: true
    }
  },

  data: function data() {
    return {
      chartApiData: null,
      isLoading: true,
      chartData: null,
      selectedCategory: '',
      dataWidgetData: null
    };
  },

  beforeMount: function beforeMount() {

    // Assign defualt category to selected category
    this.selectedCategory = this.defaultCategory;

    // Make api call to fetch chart data
    this.getDataFromServer();
  },


  methods: {

    // Fetch widget/chart data when category changes
    onCategoryChange: function onCategoryChange(value) {
      this.selectedCategory = value;
      this.$emit('updateChangedValue', this.selectedCategory, this.reportIndex, 'selected_view_by');
      this.getDataFromServer();
    },
    getDataFromServer: function getDataFromServer() {
      var _this = this;

      this.isLoading = true;

      // Fetch data widget data if datawidget api endpoint provided 
      if (Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["c" /* boolean */])(this.dataWidgetApi)) {
        __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get(this.dataWidgetApi, { params: this.getUrlParams() }).then(function (res) {
          _this.dataWidgetData = res.data.data;
        }).catch(function (err) {
          Object(__WEBPACK_IMPORTED_MODULE_3_helpers_responseHandler__["a" /* errorHandler */])(err, 'category-based-report');
        });
      }

      // Fetch chart data
      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.get(this.chartDataApi, { params: this.getUrlParams() }).then(function (res) {
        _this.chartApiData = res.data.data;
        _this.processChartData();
      }).catch(function (err) {
        Object(__WEBPACK_IMPORTED_MODULE_3_helpers_responseHandler__["a" /* errorHandler */])(err, 'category-based-report');
        _this.isLoading = false;
      });
    },
    processChartData: function processChartData() {
      try {
        // Sorted(basis of time) list of time labels
        var timeSeriesLabels = Object(__WEBPACK_IMPORTED_MODULE_1_ChartFactory_utils__["b" /* getTimeLabels */])(this.chartApiData.data);

        // Chart object
        this.chartData = {
          data: Object(__WEBPACK_IMPORTED_MODULE_1_ChartFactory_utils__["e" /* parseTimeSeriesChartData */])(this.chartApiData.data, timeSeriesLabels),
          labels: timeSeriesLabels,
          categoryLabel: this.chartApiData.categoryLabel,
          dataLabel: this.chartApiData.dataLabel,
          panelTitle: this.chartApiData.name
        };
      } catch (error) {
        console.error('TimeSeriesChart | processChartData ', error);
      }

      this.isLoading = false;
    },


    /**
     * get Url parameters
     * may contain slected filter values, selected category limit
     */
    getUrlParams: function getUrlParams() {
      var params = Object(__WEBPACK_IMPORTED_MODULE_4__helpers_utils__["c" /* getValidFilterObject */])(this.filterParams);
      params[this.categoryPrefix] = this.selectedCategory;
      return params;
    }
  },

  watch: {
    filterParams: function filterParams() {
      this.getDataFromServer();
    }
  }
});

/***/ }),

/***/ 2815:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2816);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("4f87e63e", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-63eeea3a\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./FaveoChart.vue", function() {
     var newContent = require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-63eeea3a\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./FaveoChart.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2816:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n/* .faveo-chart {\n  margin-bottom: 1rem;\n  transition: box-shadow 280ms cubic-bezier(.4,0,.2,1);\n  display: block;\n  border-radius: 4px;\n  background: #fff;\n  color: rgba(0,0,0,.87);\n  box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);\n} */\n.no-data-div[data-v-63eeea3a] {\n  padding-top: 10px;\n  text-align: center;\n}\n.no-data-title[data-v-63eeea3a] {\n  color: #666;\n  font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;\n  font-weight: 600;\n  line-height: 1.2;\n  font-size: 14px;\n}\n", ""]);

// exports


/***/ }),

/***/ 2817:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_ChartFactory_BarChart__ = __webpack_require__(2818);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_ChartFactory_BarChart___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_ChartFactory_BarChart__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_ChartFactory_HorizontalBarChart__ = __webpack_require__(2822);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_ChartFactory_HorizontalBarChart___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_ChartFactory_HorizontalBarChart__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_ChartFactory_DoughnutChart__ = __webpack_require__(2824);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_ChartFactory_DoughnutChart___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_ChartFactory_DoughnutChart__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_ChartFactory_PieChart__ = __webpack_require__(2826);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_ChartFactory_PieChart___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_4_ChartFactory_PieChart__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_ChartFactory_LineChart__ = __webpack_require__(2828);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_ChartFactory_LineChart___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_5_ChartFactory_LineChart__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  name: 'faveo-chart',
  components: {
    BarChart: __WEBPACK_IMPORTED_MODULE_1_ChartFactory_BarChart___default.a,
    HorizontalBarChart: __WEBPACK_IMPORTED_MODULE_2_ChartFactory_HorizontalBarChart___default.a,
    DoughnutChart: __WEBPACK_IMPORTED_MODULE_3_ChartFactory_DoughnutChart___default.a,
    PieChart: __WEBPACK_IMPORTED_MODULE_4_ChartFactory_PieChart___default.a,
    LineChart: __WEBPACK_IMPORTED_MODULE_5_ChartFactory_LineChart___default.a
  },

  props: {

    /**
     * Chart data object
     * must includes `chartDataKeys` elements as keys
     */
    chartData: {
      type: Object | Array,
      required: true
      // validator: chartData => chartDataKeys.every(key => key in chartData)
    },

    chartType: {
      type: String,
      required: true,
      validator: function validator(value) {
        // The value must match one of these chart types
        return ['bar', 'horizontal_bar', 'pie', 'doughnut', 'line', 'area'].indexOf(value) !== -1;
      }
    }

  },

  data: function data() {
    return {};
  },

  methods: {
    /**
     * Getiing chart datasets
     */
    getCategoryChartData: function getCategoryChartData() {
      return {
        labels: this.chartData.labels,
        datasets: [{
          backgroundColor: Object(__WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__["n" /* getRandomColor */])(this.chartData.labels.length),
          label: this.chartData.axisLabel,
          data: this.chartData.data
        }]
      };
    },
    getTimeChartData: function getTimeChartData() {
      var _data = [];
      var _redirectURLs = [];
      for (var i = 0; i < this.chartData.data.length; i++) {
        var chartColor = Object(__WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__["n" /* getRandomColor */])();
        var timeSereis = {
          fill: false,
          label: this.chartData.data[i].label,
          borderColor: chartColor,
          backgroundColor: chartColor,
          pointBorderColor: chartColor,
          pointBackgroundColor: chartColor,
          pointBorderWidth: 1,
          pointHoverRadius: 5,
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: chartColor,
          pointHoverBorderWidth: 2,
          pointRadius: 3,
          pointHitRadius: 5,
          //Data to be represented on y-axis
          data: this.chartData.data[i].values
        };
        _data.push(timeSereis);
        var redirectURLs = this.chartData.data[i].redirectTo;
        _redirectURLs.push(redirectURLs);
      }
      return {
        data: _data,
        labels: this.chartData.labels,
        categoryLabel: this.chartData.categoryLabel,
        dataLabel: this.chartData.dataLabel,
        redirectURLs: _redirectURLs
      };
    }
  },

  computed: {

    getChartTitleObj: function getChartTitleObj() {
      var titleObj = { display: false };
      if (this.chartData.labels.length === 0) {
        titleObj = {
          display: true,
          text: 'No Data Available',
          fontSize: 14,
          padding: 15
        };
      }
      return titleObj;
    }
  }

});

/***/ }),

/***/ 2818:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2819)
/* template */
var __vue_template__ = null
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
Component.options.__file = "resources/assets/js/ChartFactory/BarChart.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3332b92c", Component.options)
  } else {
    hotAPI.reload("data-v-3332b92c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2819:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__ = __webpack_require__(73);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(51);

/**
 * Importing Bar and mixins class from the vue-chartjs wrapper
 */

/**
 * Getting the reactiveProp mixin from the mixins module.
 * The reactiveProp mixin extends the logic of your chart component,
 * Automatically creates a prop as named chartData, and adds a Vue watcher to this prop. 
 * All the data needed will be inside the chartData prop.
 * 
 * USE:- <bar-chart :chart-data="datacollection"></bar-chart>
 */
var reactiveProp = __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__["f" /* mixins */].reactiveProp;




//Exporting this so it can be used in other components
/* harmony default export */ __webpack_exports__["default"] = ({

  extends: __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__["a" /* Bar */],

  mixins: [reactiveProp],

  props: {
    /**
     * Redirect URLs for the chart click handler
     */
    redirectUrls: {
      type: Array,
      default: function _default() {
        return [];
      }
    },

    chartOptions: {
      type: Object,
      default: null
    },

    // This is will be axis(x or y) label
    labelString: {
      type: String,
      default: function _default() {
        return '';
      }
    },

    /**
     * Canvas Id 
     */
    chartId: {
      type: String,
      required: true
    },

    /** This prop is used for adding title to a chart
     * This title will be visible in canvas image
     */
    chartTitle: {
      type: Object,
      default: function _default() {
        return { display: false };
      }
    }
  },

  data: function data() {
    return {
      //Chart.js options that controls the appearance of the chart
      options: {
        title: this.chartTitle,
        plugins: {
          datalabels: {
            color: '#000',
            anchor: 'end',
            clamp: true,
            align: 'top'
          }
        },
        layout: {
          padding: {
            left: 0,
            right: 0,
            top: 24,
            bottom: 0
          }
        },
        scales: {
          yAxes: [{
            scaleLabel: {
              display: true,
              labelString: this.labelString
            },
            ticks: {
              beginAtZero: true,
              precision: 0
            },
            gridLines: {
              display: false
            }
          }],
          xAxes: [{
            barThickness: 25,
            gridLines: {
              display: false
            },
            ticks: {
              callback: function callback(value, index, values) {
                return Object(__WEBPACK_IMPORTED_MODULE_1__utils__["g" /* truncateString */])(value);
              }
            }
          }]
        },
        legend: {
          display: false
        },
        responsive: true,
        maintainAspectRatio: false,
        onClick: this.clickHandler,
        tooltips: {
          callbacks: {
            title: function title(tooltipItems, data) {
              return data.labels[tooltipItems[0].index];
            }
          }
        },
        hover: {
          onHover: this.onHover
        }
      }
    };
  },
  beforeMount: function beforeMount() {
    /**
     * If prop `chartOptions` is not passed, use default chart option
     */
    if (this.chartOptions !== null) {
      this.options = this.chartOptions;
    }
  },
  mounted: function mounted() {
    var _this = this;

    //renderChart function renders the chart with the chartData and options object.
    setTimeout(function () {
      Chart.defaults.global.plugins.datalabels.display = true;
      _this.renderChart(_this.chartData, _this.options);
    }, 1000);
  },


  methods: {
    /**
     * Handle click event on chart
     * open embeded link in new tab
     */
    clickHandler: function clickHandler(point, event) {
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["f" /* redirectToURL */])(event, this.redirectUrls);
    },
    onHover: function onHover(event, items) {
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["c" /* hoverHandler */])(items, this.chartId, this.redirectUrls);
    }
  }
});

/***/ }),

/***/ 2820:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export reactiveData */
/* unused harmony export reactiveProp */
function dataHandler(newData, oldData) {
  if (oldData) {
    var chart = this.$data._chart;
    var newDatasetLabels = newData.datasets.map(function (dataset) {
      return dataset.label;
    });
    var oldDatasetLabels = oldData.datasets.map(function (dataset) {
      return dataset.label;
    });
    var oldLabels = JSON.stringify(oldDatasetLabels);
    var newLabels = JSON.stringify(newDatasetLabels);

    if (newLabels === oldLabels && oldData.datasets.length === newData.datasets.length) {
      newData.datasets.forEach(function (dataset, i) {
        var oldDatasetKeys = Object.keys(oldData.datasets[i]);
        var newDatasetKeys = Object.keys(dataset);
        var deletionKeys = oldDatasetKeys.filter(function (key) {
          return key !== '_meta' && newDatasetKeys.indexOf(key) === -1;
        });
        deletionKeys.forEach(function (deletionKey) {
          delete chart.data.datasets[i][deletionKey];
        });

        for (var attribute in dataset) {
          if (dataset.hasOwnProperty(attribute)) {
            chart.data.datasets[i][attribute] = dataset[attribute];
          }
        }
      });

      if (newData.hasOwnProperty('labels')) {
        chart.data.labels = newData.labels;
        this.$emit('labels:update');
      }

      if (newData.hasOwnProperty('xLabels')) {
        chart.data.xLabels = newData.xLabels;
        this.$emit('xlabels:update');
      }

      if (newData.hasOwnProperty('yLabels')) {
        chart.data.yLabels = newData.yLabels;
        this.$emit('ylabels:update');
      }

      chart.update();
      this.$emit('chart:update');
    } else {
      if (chart) {
        chart.destroy();
        this.$emit('chart:destroy');
      }

      this.renderChart(this.chartData, this.options);
      this.$emit('chart:render');
    }
  } else {
    if (this.$data._chart) {
      this.$data._chart.destroy();

      this.$emit('chart:destroy');
    }

    this.renderChart(this.chartData, this.options);
    this.$emit('chart:render');
  }
}

var reactiveData = {
  data: function data() {
    return {
      chartData: null
    };
  },
  watch: {
    'chartData': dataHandler
  }
};
var reactiveProp = {
  props: {
    chartData: {
      type: Object,
      required: true,
      default: function _default() {}
    }
  },
  watch: {
    'chartData': dataHandler
  }
};
/* harmony default export */ __webpack_exports__["a"] = ({
  reactiveData: reactiveData,
  reactiveProp: reactiveProp
});

/***/ }),

/***/ 2821:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["j"] = generateChart;
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Bar; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return HorizontalBar; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return Doughnut; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return Line; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "f", function() { return Pie; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "g", function() { return PolarArea; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "h", function() { return Radar; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return Bubble; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "i", function() { return Scatter; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_chart_js__ = __webpack_require__(72);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_chart_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_chart_js__);

function generateChart(chartId, chartType) {
  return {
    render: function render(createElement) {
      return createElement('div', {
        style: this.styles,
        class: this.cssClasses
      }, [createElement('canvas', {
        attrs: {
          id: this.chartId,
          width: this.width,
          height: this.height
        },
        ref: 'canvas'
      })]);
    },
    props: {
      chartId: {
        default: chartId,
        type: String
      },
      width: {
        default: 400,
        type: Number
      },
      height: {
        default: 400,
        type: Number
      },
      cssClasses: {
        type: String,
        default: ''
      },
      styles: {
        type: Object
      },
      plugins: {
        type: Array,
        default: function _default() {
          return [];
        }
      }
    },
    data: function data() {
      return {
        _chart: null,
        _plugins: this.plugins
      };
    },
    methods: {
      addPlugin: function addPlugin(plugin) {
        this.$data._plugins.push(plugin);
      },
      generateLegend: function generateLegend() {
        if (this.$data._chart) {
          return this.$data._chart.generateLegend();
        }
      },
      renderChart: function renderChart(data, options) {
        if (this.$data._chart) this.$data._chart.destroy();
        this.$data._chart = new __WEBPACK_IMPORTED_MODULE_0_chart_js___default.a(this.$refs.canvas.getContext('2d'), {
          type: chartType,
          data: data,
          options: options,
          plugins: this.$data._plugins
        });
      }
    },
    beforeDestroy: function beforeDestroy() {
      if (this.$data._chart) {
        this.$data._chart.destroy();
      }
    }
  };
}
var Bar = generateChart('bar-chart', 'bar');
var HorizontalBar = generateChart('horizontalbar-chart', 'horizontalBar');
var Doughnut = generateChart('doughnut-chart', 'doughnut');
var Line = generateChart('line-chart', 'line');
var Pie = generateChart('pie-chart', 'pie');
var PolarArea = generateChart('polar-chart', 'polarArea');
var Radar = generateChart('radar-chart', 'radar');
var Bubble = generateChart('bubble-chart', 'bubble');
var Scatter = generateChart('scatter-chart', 'scatter');
/* unused harmony default export */ var _unused_webpack_default_export = ({
  Bar: Bar,
  HorizontalBar: HorizontalBar,
  Doughnut: Doughnut,
  Line: Line,
  Pie: Pie,
  PolarArea: PolarArea,
  Radar: Radar,
  Bubble: Bubble,
  Scatter: Scatter
});

/***/ }),

/***/ 2822:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2823)
/* template */
var __vue_template__ = null
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
Component.options.__file = "resources/assets/js/ChartFactory/HorizontalBarChart.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-84a5a7a0", Component.options)
  } else {
    hotAPI.reload("data-v-84a5a7a0", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2823:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__ = __webpack_require__(73);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(51);

/**
 * Importing HorizontalBar and mixins class from the vue-chartjs wrapper
 */


/**
 * Getting the reactiveProp mixin from the mixins module.
 * The reactiveProp mixin extends the logic of your chart component,
 * Automatically creates a prop as named chartData, and adds a Vue watcher to this prop. 
 * All the data needed will be inside the chartData prop.
 * 
 * USE:- <horizontal-bar-chart :chart-data="datacollection"></horizontal-bar-chart>
 */
var reactiveProp = __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__["f" /* mixins */].reactiveProp;




//Exporting this so it can be used in other components
/* harmony default export */ __webpack_exports__["default"] = ({

  extends: __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__["c" /* HorizontalBar */],

  mixins: [reactiveProp],

  props: {
    /**
     * Redirect URLs for the chart click handler
     */
    redirectUrls: {
      type: Array,
      default: function _default() {
        return [];
      }
    },

    // Chart.js options that controls the appearance of the chart
    chartOptions: {
      type: Object,
      default: null
    },

    // This is will be axis(x or y) label
    labelString: {
      type: String,
      default: function _default() {
        return '';
      }
    },

    /** This prop is used for adding title to a chart
     * This title will be visible in canvas image
     */
    chartTitle: {
      type: Object,
      default: function _default() {
        return { display: false };
      }
    },

    /**
     * Canvas Id 
     */
    chartId: {
      type: String,
      required: true
    }
  },

  data: function data() {
    return {
      // Chart.js options that controls the appearance of the chart
      options: {
        title: this.chartTitle,
        plugins: {
          datalabels: {
            color: '#000',
            anchor: 'end',
            clamp: true,
            align: 'end'
          }
        },
        layout: {
          padding: {
            left: 0,
            right: 30,
            top: 0,
            bottom: 0
          }
        },
        scales: {
          yAxes: [{
            barThickness: 25,
            gridLines: {
              display: false
            },
            ticks: {
              callback: function callback(value, index, values) {
                return Object(__WEBPACK_IMPORTED_MODULE_1__utils__["g" /* truncateString */])(value);
              }
            }
          }],
          xAxes: [{
            scaleLabel: {
              display: true,
              labelString: this.labelString
            },
            ticks: {
              beginAtZero: true,
              precision: 0
            },
            gridLines: {
              display: false
            }
          }]
        },
        legend: {
          display: false
        },
        responsive: true,
        maintainAspectRatio: false,
        onClick: this.clickHandler,
        tooltips: {
          callbacks: {
            title: function title(tooltipItems, data) {
              return data.labels[tooltipItems[0].index];
            }
          }
        },
        hover: {
          onHover: this.onHover
        }
      }
    };
  },
  beforeMount: function beforeMount() {
    /**
     * If prop `chartOptions` is not passed, use default chart option
     */
    if (this.chartOptions !== null) {
      this.options = this.chartOptions;
    }
  },
  mounted: function mounted() {
    var _this = this;

    //renderChart function renders the chart with the chartData and options object.
    setTimeout(function () {
      Chart.defaults.global.plugins.datalabels.display = true;
      _this.renderChart(_this.chartData, _this.options);
    }, 1000);
  },


  methods: {
    /**
     * Handle click event on chart
     * open embeded link in new tab
     */
    clickHandler: function clickHandler(point, event) {
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["f" /* redirectToURL */])(event, this.redirectUrls);
    },
    onHover: function onHover(event, items) {
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["c" /* hoverHandler */])(items, this.chartId, this.redirectUrls);
    }
  }
});

/***/ }),

/***/ 2824:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2825)
/* template */
var __vue_template__ = null
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
Component.options.__file = "resources/assets/js/ChartFactory/DoughnutChart.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-586a76ab", Component.options)
  } else {
    hotAPI.reload("data-v-586a76ab", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2825:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__ = __webpack_require__(73);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(51);

/**
 * Importing Doughnut and mixins class from the vue-chartjs wrapper
 */


/**
 * Getting the reactiveProp mixin from the mixins module.
 * The reactiveProp mixin extends the logic of your chart component,
 * Automatically creates a prop as named chartData, and adds a Vue watcher to this prop. 
 * All the data needed will be inside the chartData prop.
 * 
 * USE:- <doughnut-chart :chart-data="datacollection"></doughnut-chart>
 */
var reactiveProp = __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__["f" /* mixins */].reactiveProp;




//Exporting this so it can be used in other components
/* harmony default export */ __webpack_exports__["default"] = ({

  extends: __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__["b" /* Doughnut */],

  mixins: [reactiveProp],

  props: {
    /**
     * Redirect URLs for the chart click handler
     */
    redirectUrls: {
      type: Array,
      default: function _default() {
        return [];
      }
    },

    chartOptions: {
      type: Object,
      default: null
    },

    /** This prop is used for adding title to a chart
     * This title will be visible in canvas image
     */
    chartTitle: {
      type: Object,
      default: function _default() {
        return { display: false };
      }
    },

    /**
     * Canvas Id 
     */
    chartId: {
      type: String,
      required: true
    }
  },

  data: function data() {
    return {
      //Chart.js options that controls the appearance of the chart
      options: {
        title: this.chartTitle,
        plugins: {
          datalabels: {
            display: 'auto',
            color: '#fff',
            anchor: 'end',
            clamp: true,
            align: 'start',
            offset: 10,
            formatter: this.dataLabelFormatter
          }
        },
        legend: {
          display: true,
          position: 'top'
        },
        hover: {
          onHover: this.onHover
        },
        responsive: true,
        maintainAspectRatio: false,
        onClick: this.clickHandler
      }
    };
  },
  beforeMount: function beforeMount() {
    /**
     * If prop `chartOptions` is not passed, use default chart option
     */
    if (this.chartOptions !== null) {
      this.options = this.chartOptions;
    }
  },
  mounted: function mounted() {
    var _this = this;

    //renderChart function renders the chart with the chartData and options object.
    setTimeout(function () {
      Chart.defaults.global.plugins.datalabels.display = true;
      _this.renderChart(_this.chartData, _this.options);
    }, 1000);
  },


  methods: {
    /**
     * Handle click event on chart
     * open embeded link in new tab
     */
    clickHandler: function clickHandler(point, event) {
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["f" /* redirectToURL */])(event, this.redirectUrls);
    },
    dataLabelFormatter: function dataLabelFormatter(value, context) {
      return Object(__WEBPACK_IMPORTED_MODULE_1__utils__["a" /* getDatalabelInPercentage */])(value, context);
    },
    onHover: function onHover(event, items) {
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["c" /* hoverHandler */])(items, this.chartId, this.redirectUrls);
    }
  }
});

/***/ }),

/***/ 2826:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2827)
/* template */
var __vue_template__ = null
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
Component.options.__file = "resources/assets/js/ChartFactory/PieChart.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4356c85a", Component.options)
  } else {
    hotAPI.reload("data-v-4356c85a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2827:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__ = __webpack_require__(73);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(51);

/**
 * Importing Pie and mixins class from the vue-chartjs wrapper
 */


/**
 * Getting the reactiveProp mixin from the mixins module.
 * The reactiveProp mixin extends the logic of your chart component,
 * Automatically creates a prop as named chartData, and adds a Vue watcher to this prop. 
 * All the data needed will be inside the chartData prop.
 * 
 * USE:- <pie-chart :chart-data="datacollection"></pie-chart>
 */
var reactiveProp = __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__["f" /* mixins */].reactiveProp;




//Exporting this so it can be used in other components
/* harmony default export */ __webpack_exports__["default"] = ({

  extends: __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__["e" /* Pie */],

  mixins: [reactiveProp],

  props: {
    /**
     * Redirect URLs for the chart click handler
     */
    redirectUrls: {
      type: Array,
      default: function _default() {
        return [];
      }
    },

    // Chart.js options that controls the appearance of the chart
    chartOptions: {
      type: Object,
      default: null
    },

    /** This prop is used for adding title to a chart
     * This title will be visible in canvas image
     */
    chartTitle: {
      type: Object,
      default: function _default() {
        return { display: false };
      }
    },

    /**
     * Canvas Id 
     */
    chartId: {
      type: String,
      required: true
    }
  },

  data: function data() {
    return {
      // Chart.js options that controls the appearance of the chart
      options: {
        title: this.chartTitle,
        plugins: {
          datalabels: {
            display: 'auto',
            color: '#fff',
            anchor: 'end',
            clamp: true,
            align: 'start',
            offset: 10,
            formatter: this.dataLabelFormatter
          }
        },
        legend: {
          display: true,
          position: 'top'
        },
        hover: {
          onHover: this.onHover
        },
        responsive: true,
        maintainAspectRatio: false,
        onClick: this.clickHandler
      }
    };
  },
  beforeMount: function beforeMount() {
    /**
     * If prop `chartOptions` is not passed, use default chart option
     */
    if (this.chartOptions !== null) {
      this.options = this.chartOptions;
    }
  },
  mounted: function mounted() {
    var _this = this;

    //renderChart function renders the chart with the chartData and options object.
    setTimeout(function () {
      Chart.defaults.global.plugins.datalabels.display = true;
      _this.renderChart(_this.chartData, _this.options);
    }, 1000);
  },


  methods: {
    /**
     * Handle click event on chart
     * open embeded link in new tab
     */
    clickHandler: function clickHandler(point, event) {
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["f" /* redirectToURL */])(event, this.redirectUrls);
    },
    dataLabelFormatter: function dataLabelFormatter(value, context) {
      return Object(__WEBPACK_IMPORTED_MODULE_1__utils__["a" /* getDatalabelInPercentage */])(value, context);
    },
    onHover: function onHover(event, items) {
      Object(__WEBPACK_IMPORTED_MODULE_1__utils__["c" /* hoverHandler */])(items, this.chartId, this.redirectUrls);
    }
  }
});

/***/ }),

/***/ 2828:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2829)
/* template */
var __vue_template__ = null
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
Component.options.__file = "resources/assets/js/ChartFactory/LineChart.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-736f4b0e", Component.options)
  } else {
    hotAPI.reload("data-v-736f4b0e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2829:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__ = __webpack_require__(73);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__utils__ = __webpack_require__(51);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_chart_js__ = __webpack_require__(72);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_chart_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_chart_js__);

//Importing Line class from the vue-chartjs wrapper


var reactiveProp = __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__["f" /* mixins */].reactiveProp;






//Exporting this so it can be used in other components
/* harmony default export */ __webpack_exports__["default"] = ({

  extends: __WEBPACK_IMPORTED_MODULE_0_vue_chartjs__["d" /* Line */],

  mixins: [reactiveProp],

  props: {
    /**
     * Redirect URLs for the chart click handler
     */
    redirectUrls: {
      type: Array,
      default: function _default() {
        return [];
      }
    },

    chartOptions: {
      type: Object,
      default: null
    },

    // This is will be axis(x or y) label
    labelString: {
      type: String,
      default: function _default() {
        return '';
      }
    },

    /** This prop is used for adding title to a chart
     * This title will be visible in canvas image
     */
    chartTitle: {
      type: Object,
      default: function _default() {
        return { display: false };
      }
    },

    /**
     * Canvas Id 
     */
    chartId: {
      type: String,
      required: true
    }
  },

  data: function data() {
    var _this = this;

    return {
      datacollection: {

        //Data to be represented on x-axis
        labels: this.chartData.labels,

        // Array of chart data
        datasets: this.chartData.data
      },

      //Chart.js options that controls the appearance of the chart
      options: {
        title: this.chartTitle,
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true,
              precision: 0
            },
            gridLines: {
              display: true
            },
            scaleLabel: {
              display: true,
              labelString: this.chartData.dataLabel
            }
          }],
          xAxes: [{
            gridLines: {
              display: false
            },
            scaleLabel: {
              display: true,
              labelString: this.chartData.categoryLabel
            }
          }]
        },
        legend: {
          display: true
        },
        hover: {
          onHover: this.onHover
        },
        responsive: true,
        maintainAspectRatio: false,
        onClick: function onClick(point, event) {
          _this.clickHandler(point, event);
        }
      }
    };
  },
  beforeMount: function beforeMount() {
    /**
     * If prop `chartOptions` is not passed, use default chart option
     */
    if (this.chartOptions !== null) {
      this.options = this.chartOptions;
    }
  },
  mounted: function mounted() {
    var _this2 = this;

    //renderChart function renders the chart with the datacollection and options object.
    setTimeout(function () {
      __WEBPACK_IMPORTED_MODULE_2_chart_js___default.a.defaults.global.plugins.datalabels.display = false;
      _this2.renderChart(_this2.datacollection, _this2.options);
    }, 1000);
  },


  methods: {
    clickHandler: function clickHandler(event, items) {
      var datasetIndex = this.getDatasetIndex(event);
      if (typeof datasetIndex !== 'undefined') {
        Object(__WEBPACK_IMPORTED_MODULE_1__utils__["f" /* redirectToURL */])(items, this.chartData.redirectURLs[datasetIndex]);
      }
    },
    onHover: function onHover(event, items) {
      try {
        var el = document.getElementById(this.chartId);
        var datasetIndex = this.getDatasetIndex(event);
        el.style.cursor = 'default';
        if (typeof datasetIndex !== 'undefined' && this.chartData.redirectURLs[datasetIndex]) {
          el.style.cursor = 'pointer';
        }
      } catch (error) {
        // Do nothing
      }
    },
    getDatasetIndex: function getDatasetIndex(event) {
      var activePoints = this.$data._chart.getElementsAtEvent(event);
      if (activePoints.length > 0) {
        return this.$data._chart.getDatasetAtEvent(event)[0]._datasetIndex;
      }
    }
  }
});

/***/ }),

/***/ 2830:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "faveo-chart" },
    [
      _vm.chartType === "bar"
        ? _c("bar-chart", {
            attrs: {
              "chart-id": _vm.chartData.chartId,
              "chart-data": _vm.getCategoryChartData(),
              "redirect-urls": _vm.chartData.redirectURLs,
              "label-string": _vm.chartData.axisLabel,
              "chart-title": _vm.getChartTitleObj
            }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.chartType === "horizontal_bar"
        ? _c("horizontal-bar-chart", {
            attrs: {
              "chart-id": _vm.chartData.chartId,
              "chart-data": _vm.getCategoryChartData(),
              "redirect-urls": _vm.chartData.redirectURLs,
              "label-string": _vm.chartData.axisLabel,
              "chart-title": _vm.getChartTitleObj
            }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.chartType === "pie"
        ? _c("pie-chart", {
            attrs: {
              "chart-id": _vm.chartData.chartId,
              "chart-data": _vm.getCategoryChartData(),
              "redirect-urls": _vm.chartData.redirectURLs,
              "chart-title": _vm.getChartTitleObj
            }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.chartType === "doughnut"
        ? _c("doughnut-chart", {
            attrs: {
              "chart-id": _vm.chartData.chartId,
              "chart-data": _vm.getCategoryChartData(),
              "redirect-urls": _vm.chartData.redirectURLs,
              "chart-title": _vm.getChartTitleObj
            }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.chartType === "line"
        ? _c("line-chart", {
            attrs: {
              "chart-id": _vm.chartData.data[0].chartId,
              "chart-labels": _vm.chartData.labels,
              "chart-data": _vm.getTimeChartData(),
              "redirect-urls": _vm.chartData.redirectURLs,
              "chart-title": _vm.getChartTitleObj
            }
          })
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
    require("vue-hot-reload-api")      .rerender("data-v-63eeea3a", module.exports)
  }
}

/***/ }),

/***/ 2831:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "row" }, [
    _c("div", { staticClass: "col-md-12" }, [
      _c(
        "div",
        { staticClass: "card card-light ", attrs: { id: "chart-panel-main" } },
        [
          _c("div", { staticClass: "card-header" }, [
            _c("h3", { staticClass: "card-title" }, [
              _vm._v(_vm._s(_vm.chartApiData ? _vm.chartApiData.name : ""))
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "card-tools" }, [
              _vm.categories.length
                ? _c("div", { staticClass: "btn-group" }, [
                    _c(
                      "button",
                      {
                        staticClass: "btn btn-tool dropdown-toggle",
                        attrs: {
                          type: "button",
                          "data-toggle": "dropdown",
                          "aria-expanded": "true"
                        }
                      },
                      [
                        _c("i", { staticClass: "fas fa-eye" }),
                        _vm._v(
                          " " + _vm._s(_vm.lang(_vm.categoryPrefix)) + " "
                        ),
                        _c("label", [
                          _vm._v(" " + _vm._s(_vm.lang(_vm.selectedCategory)))
                        ])
                      ]
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      {
                        staticClass: "dropdown-menu dropdown-menu-right",
                        attrs: { role: "menu", "x-placement": "bottom-end" }
                      },
                      [
                        _vm._l(_vm.categories, function(item, index) {
                          return [
                            _c(
                              "a",
                              {
                                key: index,
                                staticClass: "dropdown-item",
                                class:
                                  _vm.selectedCategory === item
                                    ? "active text-light"
                                    : "text-dark",
                                attrs: { href: "javascript:;" },
                                on: {
                                  click: function($event) {
                                    _vm.onCategoryChange(item)
                                  }
                                }
                              },
                              [
                                _vm._v(
                                  "\n                  " +
                                    _vm._s(
                                      _vm.lang(_vm.categoryPrefix) +
                                        _vm.lang(item)
                                    ) +
                                    "\n                "
                                )
                              ]
                            )
                          ]
                        })
                      ],
                      2
                    )
                  ])
                : _vm._e()
            ])
          ]),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "card-body" },
            [
              _vm.isLoading
                ? _c("relative-loader")
                : _c(
                    "div",
                    [
                      _vm.dataWidgetApi
                        ? _c(
                            "section",
                            [
                              _c("data-widget", {
                                attrs: {
                                  "data-widget-data": _vm.dataWidgetData
                                }
                              })
                            ],
                            1
                          )
                        : _vm._e(),
                      _vm._v(" "),
                      _c("faveo-chart", {
                        attrs: {
                          "chart-data": _vm.chartData,
                          "chart-type": "line"
                        }
                      })
                    ],
                    1
                  )
            ],
            1
          )
        ]
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
    require("vue-hot-reload-api")      .rerender("data-v-7e1beb5c", module.exports)
  }
}

/***/ }),

/***/ 2832:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2833)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2835)
/* template */
var __vue_template__ = __webpack_require__(2836)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-4a046b2b"
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
Component.options.__file = "app/FaveoReport/views/js/components/Common/CategoryBasedReport.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4a046b2b", Component.options)
  } else {
    hotAPI.reload("data-v-4a046b2b", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2833:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2834);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("dc6bede8", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4a046b2b\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./CategoryBasedReport.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4a046b2b\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./CategoryBasedReport.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2834:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.btn-group[data-v-4a046b2b] {\n  padding-left: 3px;\n  padding-right: 3px;\n}\n.report-box-primary[data-v-4a046b2b] {\n  padding: 0px !important;\n}\n.ml-10[data-v-4a046b2b] { margin-left : 11px;\n}\n", ""]);

// exports


/***/ }),

/***/ 2835:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_ChartFactory_utils__ = __webpack_require__(51);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__helpers_utils__ = __webpack_require__(117);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

	name: 'category-based-report',

	components: {
		'ticket-filter': __webpack_require__(88),
		'time-series-chart': __webpack_require__(232),
		'faveo-chart': __webpack_require__(233),
		'loader': __webpack_require__(9),
		'data-widget': __webpack_require__(111)
	},

	props: {

		// Api endpoint to fetch category based charts
		categoryChartDataApi: {
			type: String,
			default: function _default() {
				return '';
			}
		},

		// Api endpoint to fetch time series charts
		timeSeriesChartDataApi: {
			type: String,
			default: function _default() {
				return '';
			}
		},

		widgetDataApi: {
			type: String,
			default: function _default() {
				return undefined;
			}
		},

		// Array of categories; decides the current view of chart
		categories: {
			type: Array,
			default: function _default() {
				return [];
			}
		},

		// Default category
		defaultCategory: {
			type: Number | String,
			default: function _default() {
				return undefined;
			}
		},

		/**
   * string to show in the category dropdown
   * used as key for fetching data from server basis of category
   */
		categoryPrefix: {
			type: String,
			default: ''
		},

		defaultChartType: {
			type: String,
			default: 'bar'
		},

		layoutClass: {
			type: String,
			required: true
		},

		// Defualt filter field value objec
		filterParams: {
			type: Object,
			default: function _default() {}
		},

		reportIndex: {
			type: Number,
			required: true
		}

	},

	data: function data() {
		return {

			// selected chart type, default is bar chart
			selectedChartType: '',

			// selected caytegory option
			selectedCategory: '',

			// some of the chart types supported by chart js
			chartTypes: ['bar', 'horizontal_bar', 'pie', 'doughnut'],

			isLoading: true,
			chartApiData: null, // Api response chart data
			dataWidgetData: null
		};
	},

	beforeMount: function beforeMount() {
		// Assign defualt category to selected category
		this.selectedCategory = this.defaultCategory;

		this.selectedChartType = this.defaultChartType;

		// Make api call to fetch chart data
		this.getDataFromServer();
	},


	methods: {
		getColumnClass: function (_getColumnClass) {
			function getColumnClass() {
				return _getColumnClass.apply(this, arguments);
			}

			getColumnClass.toString = function () {
				return _getColumnClass.toString();
			};

			return getColumnClass;
		}(function () {
			return getColumnClass();
		}),


		// Fetch data from server
		getDataFromServer: function getDataFromServer() {
			var _this = this;

			this.isLoading = true;

			// Fetch data widget data if datawidget api endpoint provided 
			if (Object(__WEBPACK_IMPORTED_MODULE_1_helpers_extraLogics__["c" /* boolean */])(this.widgetDataApi)) {
				__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get(this.widgetDataApi, { params: this.getUrlParams() }).then(function (res) {
					_this.dataWidgetData = res.data.data;
				}).catch(function (err) {
					Object(__WEBPACK_IMPORTED_MODULE_3_helpers_responseHandler__["a" /* errorHandler */])(err, 'category-based-report');
				});
			}

			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get(this.categoryChartDataApi, { params: this.getUrlParams() }).then(function (res) {
				_this.chartApiData = Object(__WEBPACK_IMPORTED_MODULE_2_ChartFactory_utils__["d" /* parseCategoryBasedChartApiData */])(res.data.data);
				_this.isLoading = false;
			}).catch(function (err) {
				Object(__WEBPACK_IMPORTED_MODULE_3_helpers_responseHandler__["a" /* errorHandler */])(err, 'category-based-report');
			});
		},


		// Fetch chart data basis of category
		onCategoryChange: function onCategoryChange(value) {
			this.selectedCategory = value;
			this.$emit('updateChangedValue', this.selectedCategory, this.reportIndex, 'selected_view_by');
			this.getDataFromServer();
		},
		onChangeChartType: function onChangeChartType(value) {
			this.selectedChartType = value;
			this.$emit('updateChangedValue', this.selectedChartType, this.reportIndex, 'selected_chart_type');
		},


		/**
   * get Url parameters
   * may contain slected filter values, selected category limit
   */
		getUrlParams: function getUrlParams() {
			var params = Object(__WEBPACK_IMPORTED_MODULE_4__helpers_utils__["c" /* getValidFilterObject */])(this.filterParams);

			// set category type for fetching chart data, if categoryPrefix is valid
			if (typeof this.categoryPrefix !== 'undefined') {
				params[this.categoryPrefix] = this.selectedCategory;
			}
			return params;
		}
	},

	watch: {
		filterParams: function filterParams() {
			this.getDataFromServer();
		}
	}

});

/***/ }),

/***/ 2836:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _vm.isLoading ? _c("loader", { attrs: { duration: 4000 } }) : _vm._e(),
      _vm._v(" "),
      _c("div", { staticClass: "row mb-3" }, [
        _c("div", { staticClass: "col-sm-12" }, [
          _vm.categories && _vm.categories.length
            ? _c("div", { staticClass: "btn-group float-right" }, [
                _c(
                  "button",
                  {
                    staticClass:
                      "btn btn-sm btn-default dropdown-toggle text-capitalize",
                    attrs: {
                      type: "button",
                      "data-toggle": "dropdown",
                      id: "category-dropdown"
                    }
                  },
                  [
                    _vm._v(
                      "\n\t\t\t\t\t" +
                        _vm._s(_vm.lang(_vm.categoryPrefix)) +
                        " " +
                        _vm._s(_vm.selectedCategory) +
                        "\n\t\t\t\t"
                    )
                  ]
                ),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticClass: "dropdown-menu dropdown-menu-right",
                    attrs: { "aria-labelledby": "category-dropdown" }
                  },
                  [
                    _vm._l(_vm.categories, function(item, index) {
                      return [
                        _c(
                          "a",
                          {
                            key: index,
                            staticClass: "dropdown-item text-capitalize",
                            class:
                              _vm.selectedCategory === item ? "active" : "",
                            attrs: { href: "javascript:;" },
                            on: {
                              click: function($event) {
                                _vm.onCategoryChange(item)
                              }
                            }
                          },
                          [
                            _vm._v(
                              "\n\t\t\t\t\t\t\t" +
                                _vm._s(_vm.lang(_vm.categoryPrefix)) +
                                " " +
                                _vm._s(item) +
                                "\n\t\t\t\t\t\t"
                            )
                          ]
                        )
                      ]
                    })
                  ],
                  2
                )
              ])
            : _vm._e(),
          _vm._v(" "),
          _c("div", { staticClass: "float-right btn-group" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-sm btn-default dropdown-toggle",
                attrs: { id: "chart-type-dropdown", "data-toggle": "dropdown" }
              },
              [
                _vm._v(
                  "\n\t\t\t\t\t" + _vm._s(_vm.lang("chart_type")) + "\n\t\t\t\t"
                )
              ]
            ),
            _vm._v(" "),
            _c(
              "div",
              {
                staticClass: "dropdown-menu dropdown-menu-right",
                attrs: { "aria-labelledby": "chart-type-dropdown" }
              },
              [
                _vm._l(_vm.chartTypes, function(item) {
                  return [
                    _c(
                      "a",
                      {
                        key: item,
                        staticClass: "dropdown-item",
                        class: _vm.selectedChartType === item ? "active" : "",
                        attrs: { href: "javascript:;" },
                        on: {
                          click: function($event) {
                            _vm.onChangeChartType(item)
                          }
                        }
                      },
                      [
                        _vm._v(
                          "\n\t\t\t\t\t\t\t" +
                            _vm._s(_vm.lang(_vm.categoryPrefix)) +
                            " " +
                            _vm._s(_vm.lang(item)) +
                            "\n\t\t\t\t\t\t"
                        )
                      ]
                    )
                  ]
                })
              ],
              2
            )
          ])
        ])
      ]),
      _vm._v(" "),
      _c(
        "div",
        [
          _vm.widgetDataApi
            ? _c("data-widget", {
                attrs: { "data-widget-data": _vm.dataWidgetData }
              })
            : _vm._e()
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "row" },
        _vm._l(_vm.chartApiData, function(panel) {
          return _c("div", { key: panel.id, class: _vm.layoutClass }, [
            _c("div", { staticClass: "card card-light " }, [
              _c("div", { staticClass: "card-header" }, [
                _c("h3", { staticClass: "card-title" }, [
                  _vm._v(_vm._s(panel.panelTitle))
                ])
              ]),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "card-body" },
                [
                  _c("faveo-chart", {
                    attrs: {
                      "chart-data": panel,
                      "chart-type": _vm.selectedChartType
                    }
                  })
                ],
                1
              )
            ])
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
    require("vue-hot-reload-api")      .rerender("data-v-4a046b2b", module.exports)
  }
}

/***/ }),

/***/ 2837:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(2838)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2840)
/* template */
var __vue_template__ = __webpack_require__(2841)
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
Component.options.__file = "app/FaveoReport/views/js/components/SaveReportModal.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-44e399f4", Component.options)
  } else {
    hotAPI.reload("data-v-44e399f4", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2838:
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(2839);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("e59d2ec0", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-44e399f4\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./SaveReportModal.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-44e399f4\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./SaveReportModal.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),

/***/ 2839:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),

/***/ 2840:
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
//





/* harmony default export */ __webpack_exports__["default"] = ({

  name: 'save-report-modal',

  props: {

    // fork(new report) or update existing one
    modalMode: {
      type: String,
      required: true
    },

    // report data oject need to be saved
    reportDataObj: {
      type: Object,
      required: true
    },

    // on clodse modal fn
    onClose: {
      type: Function,
      required: true
    }
  },

  data: function data() {
    return {
      title: '', // modal heading
      name: '', // report name
      description: '', // report description
      isPublic: true, // is public/private report
      isLoading: false
    };
  },
  beforeMount: function beforeMount() {
    this.setUpComponentPropertiesBasisOfMode();
  },


  methods: {
    setUpComponentPropertiesBasisOfMode: function setUpComponentPropertiesBasisOfMode() {
      if (this.modalMode === 'fork') {
        this.title = 'fork_this_report';
      } else if (this.modalMode === 'update') {
        this.name = this.reportDataObj.name;
        this.description = this.reportDataObj.description;
        this.isPublic = this.reportDataObj.is_public;
        this.title = 'update_this_report';
      }
    },
    onSubmit: function onSubmit() {
      var _this = this;

      this.isLoading = true;

      // if creating,
      var postConfigUrl = 'api/agent/report-config';

      if (this.modalMode === 'fork') {
        // if forking, we need parent id
        postConfigUrl = postConfigUrl + "/" + Object(__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["m" /* getIdFromUrl */])(window.location.pathname);
      }

      __WEBPACK_IMPORTED_MODULE_0_axios___default.a.post(postConfigUrl, this.getSaveReportParams()).then(function (res) {
        setTimeout(function () {
          return _this.onClose();
        }, 1000); // close the modal after 1 second
        Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res, 'save-report-modal');
        if (_this.modalMode === 'fork') {
          // redirect to report list page only if the case of `fork`
          _this.redirectToReporListPage();
        } else {
          // refresh the entry page while update completion
          window.eventHub.$emit('refreshReportEntryPage');
        }
      }).catch(function (err) {
        Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err, 'save-report-modal');
      }).finally(function (res) {
        _this.isLoading = false;
      });
    },


    /**
     * Get the parameters to be saved for the report
     */
    getSaveReportParams: function getSaveReportParams() {
      var clonedReportDataObj = JSON.parse(JSON.stringify(this.reportDataObj));
      if (this.modalMode === 'fork') {
        // assign null to report id to create a fresh new report
        clonedReportDataObj.id = null;
      }
      clonedReportDataObj.name = this.name;
      clonedReportDataObj.description = this.description;
      clonedReportDataObj.is_public = this.isPublic;

      return clonedReportDataObj;
    },
    redirectToReporListPage: function redirectToReporListPage() {
      window.location.href = window.axios.defaults.baseURL + '/report/get';
    },


    // Assign value to component properties
    onPropertyChange: function onPropertyChange(value, property) {
      this[property] = value;
    }
  },

  components: {
    'modal': __webpack_require__(13),
    'alert': __webpack_require__(6),
    'text-field': __webpack_require__(11),
    'checkbox': __webpack_require__(41),
    "custom-loader": __webpack_require__(9)
  }
});

/***/ }),

/***/ 2841:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("modal", { attrs: { showModal: true, onClose: _vm.onClose } }, [
        _c("div", { attrs: { slot: "title" }, slot: "title" }, [
          _c("h4", { staticClass: "modal-title" }, [
            _vm._v(_vm._s(_vm.lang(_vm.title)))
          ])
        ]),
        _vm._v(" "),
        _c(
          "div",
          { attrs: { slot: "alert" }, slot: "alert" },
          [_c("alert", { attrs: { componentName: "save-report-modal" } })],
          1
        ),
        _vm._v(" "),
        _c(
          "div",
          { attrs: { slot: "fields" }, slot: "fields" },
          [_vm.isLoading ? _c("custom-loader") : _vm._e()],
          1
        ),
        _vm._v(" "),
        _c(
          "div",
          { attrs: { slot: "fields" }, slot: "fields" },
          [
            _c("text-field", {
              attrs: {
                id: "name",
                label: _vm.lang("name"),
                type: "text",
                name: "name",
                classname: "col-md-12",
                value: _vm.name,
                onChange: _vm.onPropertyChange,
                required: true
              }
            }),
            _vm._v(" "),
            _c("text-field", {
              attrs: {
                id: "description",
                label: _vm.lang("description"),
                type: "textarea",
                name: "description",
                classname: "col-md-12",
                value: _vm.description,
                onChange: _vm.onPropertyChange,
                required: true
              }
            }),
            _vm._v(" "),
            _c("checkbox", {
              attrs: {
                name: "isPublic",
                value: _vm.isPublic,
                label: _vm.lang("make_this_report_public"),
                onChange: _vm.onPropertyChange,
                classname: "col-md-12",
                id: "allow-only-ldap-login"
              }
            })
          ],
          1
        ),
        _vm._v(" "),
        _c("div", { attrs: { slot: "controls" }, slot: "controls" }, [
          _c(
            "button",
            {
              staticClass: "btn btn-primary",
              attrs: { id: "new-report-submit", disabled: _vm.isLoading },
              on: { click: _vm.onSubmit }
            },
            [
              _c("span", [_c("i", { staticClass: "fas fa-code-branch" })]),
              _vm._v("\n        " + _vm._s(_vm.trans("fork")) + "\n      ")
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
    require("vue-hot-reload-api")      .rerender("data-v-44e399f4", module.exports)
  }
}

/***/ }),

/***/ 2842:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("alert", { attrs: { componentName: "report-entry-page" } }),
      _vm._v(" "),
      _vm.reportConfigObj
        ? _c("div", { staticClass: "card card-light  report-box-primary" }, [
            _c("div", { staticClass: "card-header" }, [
              _c("h3", { staticClass: "card-title" }, [
                _vm._v(_vm._s(_vm.reportConfigObj.name) + "\n              "),
                _vm.reportConfigObj.helplink
                  ? _c(
                      "a",
                      {
                        attrs: {
                          href: _vm.reportConfigObj.helplink,
                          target: "__blank"
                        }
                      },
                      [
                        _c("tool-tip", {
                          attrs: {
                            slot: "headerTooltip",
                            message: _vm.lang(
                              "click_to_see_how_to_read_this_report"
                            ),
                            size: "medium"
                          },
                          slot: "headerTooltip"
                        })
                      ],
                      1
                    )
                  : _vm._e()
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "card-tools" }, [
                _c(
                  "button",
                  {
                    directives: [
                      {
                        name: "tooltip",
                        rawName: "v-tooltip",
                        value: _vm.lang("Report configuration/filter"),
                        expression: "lang('Report configuration/filter')"
                      }
                    ],
                    staticClass: "btn btn-tool",
                    attrs: { type: "button" },
                    on: {
                      click: function($event) {
                        _vm.toggleFilterView()
                      }
                    }
                  },
                  [_c("i", { staticClass: "fas fa-filter" })]
                ),
                _vm._v(" "),
                _vm.showExportButton && _vm.dataCount
                  ? _c(
                      "button",
                      {
                        directives: [
                          {
                            name: "tooltip",
                            rawName: "v-tooltip",
                            value: _vm.lang("export"),
                            expression: "lang('export')"
                          }
                        ],
                        staticClass: "btn btn-tool",
                        attrs: { id: "export-report" },
                        on: { click: _vm.exportReport }
                      },
                      [
                        _c("i", {
                          staticClass: "fas fa-paper-plane",
                          attrs: { "aria-hidden": "true" }
                        })
                      ]
                    )
                  : _vm._e()
              ])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "card-body" },
              [
                _vm.isShowFilter
                  ? _c(
                      "ticket-filter",
                      {
                        attrs: {
                          isApplyOnlyMode: true,
                          "prefilled-filter-object": _vm.filterParams,
                          "filter-dependencies-api-endpoint":
                            "/api/agent/filter-dependencies",
                          showFilter: _vm.isShowFilter,
                          closeFilterView: _vm.toggleFilterView
                        },
                        on: { filter: _vm.setFilter }
                      },
                      [
                        _c(
                          "span",
                          {
                            attrs: { slot: "filter-operation-btn-group" },
                            slot: "filter-operation-btn-group"
                          },
                          [
                            !_vm.reportConfigObj.is_default
                              ? _c(
                                  "button",
                                  {
                                    staticClass: "btn btn-primary",
                                    on: {
                                      click: function($event) {
                                        _vm.forkUpdateAction("update")
                                      }
                                    }
                                  },
                                  [
                                    _c("i", {
                                      staticClass: "fas fa-pencil-alt",
                                      attrs: { "aria-hidden": "true" }
                                    }),
                                    _vm._v(
                                      "\n               " +
                                        _vm._s(_vm.lang("update")) +
                                        "\n            "
                                    )
                                  ]
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            _c(
                              "button",
                              {
                                staticClass: "btn btn-primary",
                                on: {
                                  click: function($event) {
                                    _vm.forkUpdateAction("fork")
                                  }
                                }
                              },
                              [
                                _c("i", {
                                  staticClass: "fas fa-code-branch",
                                  attrs: { "aria-hidden": "true" }
                                }),
                                _vm._v(
                                  "\n               " +
                                    _vm._s(_vm.lang("fork")) +
                                    "\n            "
                                )
                              ]
                            )
                          ]
                        )
                      ]
                    )
                  : _vm._e(),
                _vm._v(" "),
                _vm._l(_vm.reportConfigObj.sub_reports, function(
                  report,
                  reportIndex
                ) {
                  return _c(
                    "section",
                    { key: report.id },
                    [
                      report.data_type === "datatable"
                        ? _c("tabular-report-layout", {
                            attrs: {
                              "data-url": report.data_url,
                              "table-columns": report.columns,
                              "sub-report-id": report.id,
                              "export-url": _vm.reportConfigObj.export_url,
                              "add-custom-column-url":
                                report.add_custom_column_url,
                              "delete-custom-column-url":
                                "api/delete-custom-column",
                              "short-code-url":
                                "api/report-shortcodes/" +
                                _vm.reportConfigObj.type,
                              filterParams: _vm.filterParams,
                              "report-index": reportIndex
                            }
                          })
                        : _vm._e(),
                      _vm._v(" "),
                      report.data_type === "category-chart"
                        ? _c("category-based-report", {
                            attrs: {
                              "category-chart-data-api": report.data_url,
                              "widget-data-api": report.data_widget_url,
                              categories: report.list_view_by,
                              "default-category": report.selected_view_by,
                              "default-chart-type": report.selected_chart_type,
                              "category-prefix": "view_by",
                              "layout-class": _vm.getLayoutClass(report.layout),
                              filterParams: _vm.filterParams,
                              "report-index": reportIndex
                            },
                            on: { updateChangedValue: _vm.updateChangedValue }
                          })
                        : _vm._e(),
                      _vm._v(" "),
                      report.data_type === "time-series-chart"
                        ? _c("time-series-chart", {
                            attrs: {
                              "chart-data-api": report.data_url,
                              "data-widget-api": report.data_widget_url,
                              categories: report.list_view_by,
                              "default-category": report.selected_view_by,
                              filterParams: _vm.filterParams,
                              "report-index": reportIndex
                            },
                            on: { updateChangedValue: _vm.updateChangedValue }
                          })
                        : _vm._e()
                    ],
                    1
                  )
                })
              ],
              2
            )
          ])
        : _vm._e(),
      _vm._v(" "),
      _vm.openSaveReportModal
        ? _c("save-report-modal", {
            attrs: {
              onClose: _vm.closeSaveReportModal,
              reportDataObj: _vm.clonedReportConfigOnj,
              "modal-mode": _vm.modalMode
            }
          })
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
    require("vue-hot-reload-api")      .rerender("data-v-017811b7", module.exports)
  }
}

/***/ }),

/***/ 2843:
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(2844)
/* template */
var __vue_template__ = __webpack_require__(2846)
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
Component.options.__file = "app/FaveoReport/views/js/components/ReportSettings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-75390806", Component.options)
  } else {
    hotAPI.reload("data-v-75390806", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),

/***/ 2844:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_axios___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_axios__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__helpers_validator_reportSettingsRules__ = __webpack_require__(2845);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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

	name: 'reports-settings',

	description: 'reports settings page',

	components: {
		FaveoBox: __WEBPACK_IMPORTED_MODULE_3_components_MiniComponent_FaveoBox___default.a,

		'static-select': __webpack_require__(26),

		'loader': __webpack_require__(9),

		'alert': __webpack_require__(6)
	},
	beforeMount: function beforeMount() {
		this.getInitialValues();
	},

	data: function data() {
		return {

			records: 0,

			/**
    * initial state of loader
    * @type {Boolean}
    */
			loading: false,

			/**
    * initial state of the button
    * @type {Boolean}
    */
			isDisabled: false,

			elements: [{ id: 200, name: 200 }, { id: 400, name: 400 }, { id: 600, name: 600 }, { id: 800, name: 800 }, { id: 1000, name: 1000 }, { id: 1200, name: 1200 }, { id: 1400, name: 1400 }, { id: 1600, name: 1600 }, { id: 1800, name: 1800 }, { id: 2000, name: 2000 }, { id: 2200, name: 2200 }, { id: 2400, name: 2400 }, { id: 2600, name: 2600 }, { id: 2800, name: 2800 }, { id: 3000, name: 3000 }]
		};
	},

	methods: {
		/**
  * gets initial state of states defined in this component
  * @return {void}
  */
		getInitialValues: function getInitialValues() {
			var _this = this;

			this.loading = true;
			this.isDisabled = true;
			__WEBPACK_IMPORTED_MODULE_0_axios___default.a.get('/api/report-settings').then(function (res) {
				_this.records = res.data.data.records_per_file;
				_this.loading = false;
				_this.isDisabled = false;
			}).catch(function (err) {
				return Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err);
			});
		},
		isValid: function isValid() {
			var _validateReportSettin = Object(__WEBPACK_IMPORTED_MODULE_2__helpers_validator_reportSettingsRules__["a" /* validateReportSettings */])(this.$data),
			    errors = _validateReportSettin.errors,
			    isValid = _validateReportSettin.isValid;

			if (!isValid) {
				return false;
			}
			return true;
		},
		onChange: function onChange(value, name) {
			this[name] = value;
		},


		/**
   * initial state of the component
   * @return {Void}
   */
		initialState: function initialState() {
			this.loading = false;
			this.isDisabled = false;
		},
		onSubmit: function onSubmit() {
			var _this2 = this;

			if (this.isValid()) {
				this.loading = true;
				this.isDisabled = true;
				__WEBPACK_IMPORTED_MODULE_0_axios___default.a.post('/api/report-settings', { records_per_file: this.records }).then(function (res) {
					Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["b" /* successHandler */])(res);
					_this2.initialState();
				}).catch(function (err) {
					Object(__WEBPACK_IMPORTED_MODULE_1_helpers_responseHandler__["a" /* errorHandler */])(err);
					_this2.initialState();
				});
			}
		}
	}
});

/***/ }),

/***/ 2845:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = validateReportSettings;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_store__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_easy_validator_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_easy_validator_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__ = __webpack_require__(4);
/**
 * This file contains all the validation rules specific to form.
 *
 * RULES : method name for the form should be 'validateFormName'
 * */





function validateReportSettings(data) {
    var records = data.records;

    //rules has to apply only after checking conditions

    var validatingData = {
        records: [records, 'isRequired', 'minValue(1)']
    };

    //creating a validator instance and pasing lang method to it
    var validator = new __WEBPACK_IMPORTED_MODULE_1_easy_validator_js__["Validator"](__WEBPACK_IMPORTED_MODULE_2_helpers_extraLogics__["q" /* lang */]);

    var _validator$validate = validator.validate(validatingData),
        errors = _validator$validate.errors,
        isValid = _validator$validate.isValid;

    // write to vuex if errors


    __WEBPACK_IMPORTED_MODULE_0_store__["a" /* store */].dispatch('setValidationError', errors); //if component is valid, an empty state will be sent

    return { errors: errors, isValid: isValid };
};

/***/ }),

/***/ 2846:
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("alert"),
      _vm._v(" "),
      _c("faveo-box", { attrs: { title: _vm.trans("settings") } }, [
        _c(
          "div",
          { staticClass: "row" },
          [
            _c("static-select", {
              attrs: {
                label: _vm.trans("record_per_export"),
                value: _vm.records,
                name: "records",
                classname: "col-sm-6",
                elements: _vm.elements,
                onChange: _vm.onChange,
                type: "number",
                required: true,
                hint: _vm.trans("record_per_export_tooltip")
              }
            }),
            _vm._v(" "),
            _vm.loading === true
              ? _c(
                  "div",
                  {
                    staticClass: "row",
                    attrs: { slot: "fields" },
                    slot: "fields"
                  },
                  [_c("loader", { attrs: { duration: 4000 } })],
                  1
                )
              : _vm._e()
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
                  type: "button",
                  id: "submit_btn",
                  disabled: _vm.isDisabled
                },
                on: {
                  click: function($event) {
                    _vm.onSubmit()
                  }
                }
              },
              [
                _c("i", {
                  staticClass: "fas fa-save",
                  attrs: { "aria-hidden": "true" }
                }),
                _vm._v(" " + _vm._s(_vm.trans("submit")))
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
    require("vue-hot-reload-api")      .rerender("data-v-75390806", module.exports)
  }
}

/***/ }),

/***/ 51:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export getCategoryBasedChartData */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return parseCategoryBasedChartApiData; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return getTimeLabels; });
/* unused harmony export getUniqueList */
/* harmony export (immutable) */ __webpack_exports__["e"] = parseTimeSeriesChartData;
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "f", function() { return redirectToURL; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "g", function() { return truncateString; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return getDatalabelInPercentage; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return hoverHandler; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__ = __webpack_require__(4);
/**
 * Common utilty for chart operations
 */





/**
* parse chart data
* @param {Array} `chartData`
* @returns {Object} `{series, labels, redirectURLs}`
*/
var getCategoryBasedChartData = function getCategoryBasedChartData(chartData) {
  var data = [];
  var labels = [];
  var redirectURLs = [];
  chartData.forEach(function (element) {
    data.push(element.value);
    labels.push(element.label);
    redirectURLs.push(element.redirectTo);
  });

  return { data: data, labels: labels, redirectURLs: redirectURLs };
};

/**
 * 
 * @param {Array} apiData 
 * 
 * parse api response data for category based charts
 */
var parseCategoryBasedChartApiData = function parseCategoryBasedChartApiData(apiData) {
  var chartArray = [];

  apiData.forEach(function (element) {
    var parsedData = getCategoryBasedChartData(element.data);
    var chartData = {
      chartId: element.id,
      panelTitle: element.name,
      axisLabel: element.dataLabel,
      data: parsedData.data,
      labels: parsedData.labels,
      redirectURLs: parsedData.redirectURLs
    };
    chartArray.push(chartData);
  });

  return chartArray;
};

/**
 * 
 * @param {Array} chartData
 * 
 * @return {Array} unique labels
 * 
 * Pick the labels from chart api data and return the array of labels
 */
var getTimeLabels = function getTimeLabels(chartData) {
  var chartApiData = JSON.parse(JSON.stringify(chartData));
  var labelsWithIds = [];
  for (var i = 0; i < chartApiData.length; i++) {
    for (var j = 0; j < chartApiData[i].data.length; j++) {
      var dataObj = chartApiData[i].data[j];
      var timestampObj = {
        id: dataObj.id,
        label: dataObj.label
      };
      labelsWithIds.push(timestampObj);
    }
  }

  var sortedList = labelsWithIds.sort(compareByDateOrNumber);

  var labels = getUniqueList(sortedList, 'label').map(function (item) {
    return item.label;
  });

  return labels;
};

var getUniqueList = function getUniqueList(list, key) {
  var uniqueList = Array.from(new Set(list.map(function (item) {
    return item[key];
  }))).map(function (k) {
    return list.find(function (element) {
      return element[key] === k;
    });
  });
  return uniqueList;
};

/**
 * Comparator for sorting array in ascending order
 */
var compareByDateOrNumber = function compareByDateOrNumber(a, b) {
  if (isNaN(Number(a.id))) {
    return new Date(a.id) - new Date(b.id);
  } else {
    return Number(a.id) - Number(b.id);
  }
};

function addTimeSeriesChartData(chartApiData, timeSeriesLabels, _chartData, i, j) {
  for (var k = 0; k < chartApiData[j].data.length + 1; k++) {
    if (k === chartApiData[j].data.length) {
      _chartData.values.push(0);
      _chartData.redirectTo.push(null);
    } else if (chartApiData[j].data[k].label === timeSeriesLabels[i]) {
      _chartData.values.push(chartApiData[j].data[k].value || 0);
      _chartData.redirectTo.push(chartApiData[j].data[k].redirectTo);
      break;
    }
  }
}

/**
 * 
 * @param {Array} chartData
 * @param {Array} timeSeriesLabels
 * 
 * Parse time series chart data
 */
function parseTimeSeriesChartData(chartData, timeSeriesLabels) {
  var chartApiData = JSON.parse(JSON.stringify(chartData));
  var dataCollections = [];
  for (var j = 0; j < chartApiData.length; j++) {

    // Chart js data object
    var _chartData = {
      chartId: '',
      label: '',
      values: [],
      redirectTo: []
    };

    // Add chart name
    _chartData.label = chartApiData[j].name;
    // Add chart id
    _chartData.chartId = chartApiData[j].id;

    for (var i = 0; i < timeSeriesLabels.length; i++) {
      addTimeSeriesChartData(chartApiData, timeSeriesLabels, _chartData, i, j);
    }

    dataCollections.push(_chartData);
  }
  return dataCollections;
}

/**
 * Open url which is embeded in chart
 * 
 * @param {Array | undefined} event 
 * @param {Array | undefined} redirectURLs 
 */
var redirectToURL = function redirectToURL(event, redirectURLs) {
  if (Object(__WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__["c" /* boolean */])(event) && Object(__WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__["c" /* boolean */])(redirectURLs)) {
    var redirectLink = redirectURLs[event[0]._index];
    if (Object(__WEBPACK_IMPORTED_MODULE_0_helpers_extraLogics__["c" /* boolean */])(redirectLink)) {
      open(redirectLink, '_blank');
    }
  }
};

/**
 * Truncate string to limit provided(default is 18)
 * Also ellipsify the text
 * 
 * @param {String} str 
 * @param {Number | undefined} stringLimit 
 */
var truncateString = function truncateString(str) {
  var stringLimit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 18;

  return str.length > stringLimit ? str.substr(0, stringLimit).trim() + '...' : str;
};

/**
 * get percentage value of the given dataset
 * @param {Number} value 
 * @param {Array} chartDataSet 
 */
var getDatalabelInPercentage = function getDatalabelInPercentage(value, chartDataSet) {
  var dotZeroZeroRegex = /\.00$/;
  var datasets = JSON.parse(JSON.stringify(chartDataSet.dataset.data));
  var totalSum = datasets.reduce(function (accumulator, currentValue) {
    return Number(accumulator) + Number(currentValue);
  });
  var percentageVal = (Number(value) / totalSum * 100).toFixed(2) + '';
  if (dotZeroZeroRegex.test(percentageVal)) {
    percentageVal = percentageVal.replace(dotZeroZeroRegex, '');
  }
  return percentageVal + '%';
};

var hoverHandler = function hoverHandler(items, chartId, redirectUrls) {
  try {
    var el = document.getElementById(chartId);
    el.style.cursor = 'default';
    if (items[0] && redirectUrls[items[0]._index]) {
      el.style.cursor = 'pointer';
    }
  } catch (error) {
    // Do nothing
  }
};

/***/ }),

/***/ 73:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export VueCharts */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__mixins_index_js__ = __webpack_require__(2820);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__BaseCharts__ = __webpack_require__(2821);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["a"]; });
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["d"]; });
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["c"]; });
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["e"]; });
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "e", function() { return __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["f"]; });
/* unused harmony reexport PolarArea */
/* unused harmony reexport Radar */
/* unused harmony reexport Bubble */
/* unused harmony reexport Scatter */
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "f", function() { return __WEBPACK_IMPORTED_MODULE_0__mixins_index_js__["a"]; });
/* unused harmony reexport generateChart */


var VueCharts = {
  Bar: __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["a" /* Bar */],
  HorizontalBar: __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["d" /* HorizontalBar */],
  Doughnut: __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["c" /* Doughnut */],
  Line: __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["e" /* Line */],
  Pie: __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["f" /* Pie */],
  PolarArea: __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["g" /* PolarArea */],
  Radar: __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["h" /* Radar */],
  Bubble: __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["b" /* Bubble */],
  Scatter: __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["i" /* Scatter */],
  mixins: __WEBPACK_IMPORTED_MODULE_0__mixins_index_js__["a" /* default */],
  generateChart: __WEBPACK_IMPORTED_MODULE_1__BaseCharts__["j" /* generateChart */],
  render: function render() {
    return console.error('[vue-chartjs]: This is not a vue component. It is the whole object containing all vue components. Please import the named export or access the components over the dot notation. For more info visit https://vue-chartjs.org/#/home?id=quick-start');
  }
};
/* unused harmony default export */ var _unused_webpack_default_export = (VueCharts);


/***/ })

},[2777]);