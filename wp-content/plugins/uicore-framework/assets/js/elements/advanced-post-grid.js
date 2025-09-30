uicoreJsonp([21],{

/***/ 442:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

__webpack_require__(443);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

window.addEventListener('DOMContentLoaded', function () {
    var APG = function (_elementorModules$fro) {
        _inherits(APG, _elementorModules$fro);

        function APG() {
            _classCallCheck(this, APG);

            return _possibleConstructorReturn(this, (APG.__proto__ || Object.getPrototypeOf(APG)).apply(this, arguments));
        }

        _createClass(APG, [{
            key: 'bindEvents',
            value: function bindEvents() {
                if (this.getElementSettings('animate_items') == 'ui-e-grid-animate') {
                    this.setIndex();
                    this.animate();
                }

                if (this.getElementSettings('masonry') == 'ui-e-maso') {
                    this.doMaso();
                    var _this = this;
                    jQuery(window).on('resize', function () {
                        _this.doMaso();
                    });
                }
            }
        }, {
            key: 'getDefaultElements',
            value: function getDefaultElements() {
                return {
                    $grid: this.$element.find('.ui-e-adv-grid'),
                    $items: this.$element.find('.ui-e-post-item')
                };
            }
        }, {
            key: 'onElementChange',
            value: function onElementChange(propertyName) {
                var _this3 = this;

                if (0 === propertyName.indexOf('animate_')) {
                    this.elements.$items.attr('class', 'ui-e-post-item elementor-invisible');
                    setTimeout(function () {
                        _this3.animate();
                    }, 200);
                    return;
                }

                if (propertyName == 'columns') {
                    this.setIndex();
                }

                if (!['content_bg'].includes(propertyName) && 0 != propertyName.indexOf('animate_')) {

                    //TODO - CONTINUE ONLY FOR PROPERTY THAT MAY CHANGE THE HEIGHT !!!!!
                    var isMaso = this.getElementSettings('masonry') == 'ui-e-maso' ? true : false;
                    if (isMaso) {

                        this.doMaso();
                        this.animate();
                        return;
                    } else {
                        this.$element.removeClass('ui-e-maso');
                    }
                }
            }
        }, {
            key: 'animate',
            value: function animate() {
                var animationName = this.getElementSettings('animate_item_type');
                if (animationName) this.elements.$items.each(function (i, el) {
                    new Waypoint({
                        element: el,
                        handler: function handler(direction) {
                            el.classList.remove('elementor-invisible');
                            el.classList.add('ui-e-animated');
                            el.classList.add(animationName);
                        },
                        offset: "90%"
                    });
                });
            }
        }, {
            key: 'doMaso',
            value: function doMaso() {
                var _this4 = this;

                this.$element.addClass('ui-e-maso');
                var col = this.elements.$grid.css("grid-template-columns").split(" ").length;
                var gap = Math.floor(this.elements.$grid.css("gap").split(' ')[0].slice(0, -2));
                this.elements.$items.css('margin-top', '');
                this.elements.$items.each(function (i, el) {
                    if (i + 1 > col) {
                        var prev_fin = _this4.elements.$items[i - col].getBoundingClientRect().bottom;
                        var curr_ini = _this4.elements.$items[i].getBoundingClientRect().top;
                        el.style.marginTop = prev_fin + gap - curr_ini + 'px';
                    } else {
                        el.style.removeProperty('margin-top');
                    }
                });
            }
        }, {
            key: 'setIndex',
            value: function setIndex() {
                var col = this.elements.$grid.css("grid-template-columns").split(" ").length;
                this.elements.$items.each(function (i, el) {
                    el.style.setProperty('---ui-index', i - Math.floor(i / col) * col);
                });
            }
        }]);

        return APG;
    }(elementorModules.frontend.handlers.Base);

    jQuery(window).on('elementor/frontend/init', function () {
        var addHandler = function addHandler($element) {
            elementorFrontend.elementsHandler.addHandler(APG, { $element: $element });
        };
        elementorFrontend.hooks.addAction('frontend/element_ready/uicore-advanced-post-grid.default', addHandler);
    });
}, false);

/***/ }),

/***/ 443:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

},[442]);