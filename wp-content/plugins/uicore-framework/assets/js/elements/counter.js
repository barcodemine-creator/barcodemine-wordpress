uicoreJsonp([7],{

/***/ 438:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

__webpack_require__(439);

var _countUpUmd = __webpack_require__(440);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

window.addEventListener('DOMContentLoaded', function () {
    var UICounterHandler = function (_elementorModules$fro) {
        _inherits(UICounterHandler, _elementorModules$fro);

        function UICounterHandler() {
            _classCallCheck(this, UICounterHandler);

            return _possibleConstructorReturn(this, (UICounterHandler.__proto__ || Object.getPrototypeOf(UICounterHandler)).apply(this, arguments));
        }

        _createClass(UICounterHandler, [{
            key: 'updateCounter',
            value: function updateCounter() {
                var _this2 = this;

                // Get the animation type and the number wrapper
                var $animation = this.getElementSettings('counter_animation');
                var $wrapper = this.$element.find('.ui-e-num');

                if ($animation == 'motion') {
                    // Motion relies on waypoint() and some CSS style

                    new Waypoint({

                        element: jQuery(this.$element),
                        handler: function handler() {

                            jQuery(_this2.$element).addClass('ui-e-active');
                        },
                        offset: "90%"
                    });
                } else {
                    // Simple and Odometer options depends of countUp.js

                    $wrapper.text(this.getElementSettings('count_start')); // Add the start number inside the number wrapper

                    var options = { // Set the basic countUp options
                        startVal: this.getElementSettings('count_start'),
                        decimalPlaces: this.getElementSettings('decimal_places'),
                        useGrouping: this.getElementSettings('use_grouping') || "false",
                        separator: this.getElementSettings('counter_separator') || '',
                        decimal: this.getElementSettings('decimal_symbol') || ",",
                        duration: this.getElementSettings('duration'),
                        prefix: this.getElementSettings('counter_prefix') || '',
                        suffix: this.getElementSettings('counter_suffix') || '',
                        enableScrollSpy: true,
                        scrollSpyOnce: true,
                        scrollSpyDelay: 0
                    };

                    if ($animation == 'odometer') {
                        // Odometer requires the odometer.js plugin
                        var Odometer = window.Odometer || {};
                        options = _extends({}, options, {
                            plugin: new Odometer({
                                duration: this.getElementSettings('duration') / 2,
                                lastDigitDelay: 0
                            })
                        });
                    }

                    var widget = new _countUpUmd.CountUp($wrapper[0], this.getElementSettings('count_end'), options); // Creates the function var but it is not necessary to start it because of scrollSpy
                }
            }
        }, {
            key: 'bindEvents',
            value: function bindEvents() {
                this.updateCounter();
            }
        }, {
            key: 'onElementChange',
            value: function onElementChange(propertyName) {
                // Controls that on change need to trigger the animation again by removing the 'ui-e-active' before rendering the widget
                var $properties = ['count_number', 'counter_html_tag', 'counter_animation', 'use_grouping', 'decimal_symbol', 'counter_separator', 'decimal_places', 'duration', 'counter_prefix', 'counter_suffix'];

                if ($properties.includes(propertyName)) {
                    jQuery(this.$element).removeClass('ui-e-active');
                }
            }
        }]);

        return UICounterHandler;
    }(elementorModules.frontend.handlers.Base);

    jQuery(window).on('elementor/frontend/init', function () {
        var addHandler = function addHandler($element) {

            elementorFrontend.elementsHandler.addHandler(UICounterHandler, {
                $element: $element
            });
        };
        elementorFrontend.hooks.addAction('frontend/element_ready/uicore-counter.default', addHandler);
    });
});

/***/ }),

/***/ 439:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 440:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

!function (t, i) {
  "object" == ( false ? "undefined" : _typeof(exports)) && "undefined" != typeof module ? i(exports) :  true ? !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (i),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)) : i((t = "undefined" != typeof globalThis ? globalThis : t || self).countUp = {});
}(undefined, function (t) {
  "use strict";
  var _i = function i() {
    return _i = Object.assign || function (t) {
      for (var i, n = 1, s = arguments.length; n < s; n++) {
        for (var e in i = arguments[n]) {
          Object.prototype.hasOwnProperty.call(i, e) && (t[e] = i[e]);
        }
      }return t;
    }, _i.apply(this, arguments);
  },
      n = function () {
    function t(t, n, s) {
      var e = this;this.endVal = n, this.options = s, this.version = "2.8.0", this.defaults = { startVal: 0, decimalPlaces: 0, duration: 2, useEasing: !0, useGrouping: !0, useIndianSeparators: !1, smartEasingThreshold: 999, smartEasingAmount: 333, separator: ",", decimal: ".", prefix: "", suffix: "", enableScrollSpy: !1, scrollSpyDelay: 200, scrollSpyOnce: !1 }, this.finalEndVal = null, this.useEasing = !0, this.countDown = !1, this.error = "", this.startVal = 0, this.paused = !0, this.once = !1, this.count = function (t) {
        e.startTime || (e.startTime = t);var i = t - e.startTime;e.remaining = e.duration - i, e.useEasing ? e.countDown ? e.frameVal = e.startVal - e.easingFn(i, 0, e.startVal - e.endVal, e.duration) : e.frameVal = e.easingFn(i, e.startVal, e.endVal - e.startVal, e.duration) : e.frameVal = e.startVal + (e.endVal - e.startVal) * (i / e.duration);var n = e.countDown ? e.frameVal < e.endVal : e.frameVal > e.endVal;e.frameVal = n ? e.endVal : e.frameVal, e.frameVal = Number(e.frameVal.toFixed(e.options.decimalPlaces)), e.printValue(e.frameVal), i < e.duration ? e.rAF = requestAnimationFrame(e.count) : null !== e.finalEndVal ? e.update(e.finalEndVal) : e.options.onCompleteCallback && e.options.onCompleteCallback();
      }, this.formatNumber = function (t) {
        var i,
            n,
            s,
            a,
            o = t < 0 ? "-" : "";i = Math.abs(t).toFixed(e.options.decimalPlaces);var r = (i += "").split(".");if (n = r[0], s = r.length > 1 ? e.options.decimal + r[1] : "", e.options.useGrouping) {
          a = "";for (var l = 3, u = 0, h = 0, p = n.length; h < p; ++h) {
            e.options.useIndianSeparators && 4 === h && (l = 2, u = 1), 0 !== h && u % l == 0 && (a = e.options.separator + a), u++, a = n[p - h - 1] + a;
          }n = a;
        }return e.options.numerals && e.options.numerals.length && (n = n.replace(/[0-9]/g, function (t) {
          return e.options.numerals[+t];
        }), s = s.replace(/[0-9]/g, function (t) {
          return e.options.numerals[+t];
        })), o + e.options.prefix + n + s + e.options.suffix;
      }, this.easeOutExpo = function (t, i, n, s) {
        return n * (1 - Math.pow(2, -10 * t / s)) * 1024 / 1023 + i;
      }, this.options = _i(_i({}, this.defaults), s), this.formattingFn = this.options.formattingFn ? this.options.formattingFn : this.formatNumber, this.easingFn = this.options.easingFn ? this.options.easingFn : this.easeOutExpo, this.startVal = this.validateValue(this.options.startVal), this.frameVal = this.startVal, this.endVal = this.validateValue(n), this.options.decimalPlaces = Math.max(this.options.decimalPlaces), this.resetDuration(), this.options.separator = String(this.options.separator), this.useEasing = this.options.useEasing, "" === this.options.separator && (this.options.useGrouping = !1), this.el = "string" == typeof t ? document.getElementById(t) : t, this.el ? this.printValue(this.startVal) : this.error = "[CountUp] target is null or undefined", "undefined" != typeof window && this.options.enableScrollSpy && (this.error ? console.error(this.error, t) : (window.onScrollFns = window.onScrollFns || [], window.onScrollFns.push(function () {
        return e.handleScroll(e);
      }), window.onscroll = function () {
        window.onScrollFns.forEach(function (t) {
          return t();
        });
      }, this.handleScroll(this)));
    }return t.prototype.handleScroll = function (t) {
      if (t && window && !t.once) {
        var i = window.innerHeight + window.scrollY,
            n = t.el.getBoundingClientRect(),
            s = n.top + window.pageYOffset,
            e = n.top + n.height + window.pageYOffset;e < i && e > window.scrollY && t.paused ? (t.paused = !1, setTimeout(function () {
          return t.start();
        }, t.options.scrollSpyDelay), t.options.scrollSpyOnce && (t.once = !0)) : (window.scrollY > e || s > i) && !t.paused && t.reset();
      }
    }, t.prototype.determineDirectionAndSmartEasing = function () {
      var t = this.finalEndVal ? this.finalEndVal : this.endVal;this.countDown = this.startVal > t;var i = t - this.startVal;if (Math.abs(i) > this.options.smartEasingThreshold && this.options.useEasing) {
        this.finalEndVal = t;var n = this.countDown ? 1 : -1;this.endVal = t + n * this.options.smartEasingAmount, this.duration = this.duration / 2;
      } else this.endVal = t, this.finalEndVal = null;null !== this.finalEndVal ? this.useEasing = !1 : this.useEasing = this.options.useEasing;
    }, t.prototype.start = function (t) {
      this.error || (this.options.onStartCallback && this.options.onStartCallback(), t && (this.options.onCompleteCallback = t), this.duration > 0 ? (this.determineDirectionAndSmartEasing(), this.paused = !1, this.rAF = requestAnimationFrame(this.count)) : this.printValue(this.endVal));
    }, t.prototype.pauseResume = function () {
      this.paused ? (this.startTime = null, this.duration = this.remaining, this.startVal = this.frameVal, this.determineDirectionAndSmartEasing(), this.rAF = requestAnimationFrame(this.count)) : cancelAnimationFrame(this.rAF), this.paused = !this.paused;
    }, t.prototype.reset = function () {
      cancelAnimationFrame(this.rAF), this.paused = !0, this.resetDuration(), this.startVal = this.validateValue(this.options.startVal), this.frameVal = this.startVal, this.printValue(this.startVal);
    }, t.prototype.update = function (t) {
      cancelAnimationFrame(this.rAF), this.startTime = null, this.endVal = this.validateValue(t), this.endVal !== this.frameVal && (this.startVal = this.frameVal, null == this.finalEndVal && this.resetDuration(), this.finalEndVal = null, this.determineDirectionAndSmartEasing(), this.rAF = requestAnimationFrame(this.count));
    }, t.prototype.printValue = function (t) {
      var i;if (this.el) {
        var n = this.formattingFn(t);if (null === (i = this.options.plugin) || void 0 === i ? void 0 : i.render) this.options.plugin.render(this.el, n);else if ("INPUT" === this.el.tagName) this.el.value = n;else "text" === this.el.tagName || "tspan" === this.el.tagName ? this.el.textContent = n : this.el.innerHTML = n;
      }
    }, t.prototype.ensureNumber = function (t) {
      return "number" == typeof t && !isNaN(t);
    }, t.prototype.validateValue = function (t) {
      var i = Number(t);return this.ensureNumber(i) ? i : (this.error = "[CountUp] invalid start or end value: ".concat(t), null);
    }, t.prototype.resetDuration = function () {
      this.startTime = null, this.duration = 1e3 * Number(this.options.duration), this.remaining = this.duration;
    }, t;
  }();t.CountUp = n, Object.defineProperty(t, "__esModule", { value: !0 });
});

/***/ })

},[438]);