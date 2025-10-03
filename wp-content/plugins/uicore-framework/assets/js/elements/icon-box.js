uicoreJsonp([20],{

/***/ 436:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

__webpack_require__(437);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

window.addEventListener('DOMContentLoaded', function () {
    var UIBoxIcon = function (_elementorModules$fro) {
        _inherits(UIBoxIcon, _elementorModules$fro);

        function UIBoxIcon() {
            _classCallCheck(this, UIBoxIcon);

            return _possibleConstructorReturn(this, (UIBoxIcon.__proto__ || Object.getPrototypeOf(UIBoxIcon)).apply(this, arguments));
        }

        _createClass(UIBoxIcon, [{
            key: 'animate',
            value: function animate() {

                var $descriptionHasAnimation = this.getElementSettings('description_animation') == 'ui-e-animation-description-show' ? true : false;
                var $readmoreHasAnimation = this.getElementSettings('readmore_animation') == 'ui-e-animation-rm-show' ? true : false;
                var $translateFlex = this.getElementSettings('position') == 'top' || this.getElementSettings('icon_inline') == 'yes' ? true : false;
                // ↖ Icon positioned at 'top' or 'left'/'right' with 'inline' true means description can be animated and icon,title and subtitle are inside $flex, therefore $translateFlex is true.
                // icon positioned at 'left' or 'right' without 'inline' disables description animation and means title and subtitle are outside $flex, therefore $translateFlex is false.

                if (!$descriptionHasAnimation && !$readmoreHasAnimation) {
                    return;
                }

                // Elements
                var $wrapper = this.$element.find('.ui-e-ico-box');
                var $content = $wrapper.find('.ui-e-box-content');
                var $flex = $wrapper.find('.ui-e-flex-wrp');
                var $description = $wrapper.find('.ui-e-description');
                var $titles = $wrapper.find('.ui-e-title-wrp');
                var $readmore = $wrapper.find('.ui-e-readmore');

                // Values
                var $padding = this.getElementSettings('content_padding');
                var $minHeight = 0;
                var $maxTranslate = 0;
                var $minTranslate = 0;

                // Some elements or values that might be empty or not exist needs to be check before calculation to avoid NaN erros
                var $paddingTop = $padding['top'] ? parseFloat($padding['top']) : 0;
                var $paddingBot = $padding['bottom'] ? parseFloat($padding['bottom']) : 0;
                var $readmoreHeight = $readmore.length ? $readmore.outerHeight(true) : 0;

                if ($translateFlex) {
                    $minHeight = $flex.outerHeight() + $content.outerHeight(true) + $paddingTop + $paddingBot;
                } else {
                    $minHeight = $titles.outerHeight(true) + $readmoreHeight + $paddingTop + $paddingBot;
                }

                if ($descriptionHasAnimation) {
                    // description has animation
                    $maxTranslate += $description.outerHeight(true);
                } else if ($description) {
                    // description doesn't has animation
                    $minTranslate = $description.outerHeight(true);
                    $maxTranslate += $description.outerHeight(true);
                    $minHeight += $description.outerHeight(true);
                    if ($translateFlex) {
                        // icon is on top or inline is true
                        $flex.css('transform', 'translate3d(0, -' + $minTranslate + 'px, 0)');
                    }
                }
                if ($readmoreHasAnimation) {
                    // has readmore with animation
                    $maxTranslate += $readmoreHeight;
                    if ($translateFlex == false) {
                        // icon is bottom or left/right without inline
                        $minTranslate = $description.outerHeight(true);
                        $maxTranslate = $description.outerHeight(true) + $readmoreHeight;
                        $titles.css('transform', 'translate3d(0, -' + $minTranslate + 'px, 0)');
                    }
                } else if ($readmore) {
                    // has readmore without animation
                    $minTranslate = $readmoreHeight;
                    $maxTranslate += $readmoreHeight;
                    $flex.css('transform', 'translate3d(0, -' + $minTranslate + 'px, 0)');
                }

                // sets the min height on the widget
                $wrapper.css('min-height', $minHeight + 'px');
                // makes the description move when readmore has animation and description does not
                $wrapper.css('--ui-e-ico-box-translate', -$readmoreHeight + 'px');

                this.$element.on('mouseenter', function () {
                    if ($translateFlex) {
                        // icon is on top or inline is true
                        $flex.css('transform', 'translate3d(0, -' + $maxTranslate + 'px, 0)');
                    } else {
                        // icon is bottom or left/right without inline
                        $titles.css('transform', 'translate3d(0, -' + $maxTranslate + 'px, 0)');
                    }
                });

                this.$element.on('mouseleave', function () {
                    if ($translateFlex) {
                        $flex.css('transform', 'translate3d(0, -' + $minTranslate + 'px, 0)');
                    } else {
                        $titles.css('transform', 'translate3d(0, -' + $minTranslate + 'px, 0)');
                    }
                });
            }
        }, {
            key: 'onElementChange',
            value: function onElementChange() {
                var _this2 = this;

                //deffer caling this multile times in a row to avoid performance issues ( wait 1.5sec before calling it again )
                //this is still in testing phase
                clearTimeout(this.timeout);
                this.timeout = setTimeout(function () {
                    _this2.animate();
                }, 1500);
                // this.animate();
            }
        }, {
            key: 'bindEvents',
            value: function bindEvents() {
                this.animate();
            }
        }]);

        return UIBoxIcon;
    }(elementorModules.frontend.handlers.Base);

    jQuery(window).on('elementor/frontend/init', function () {
        var addHandler = function addHandler($element) {
            elementorFrontend.elementsHandler.addHandler(UIBoxIcon, {
                $element: $element
            });
        };

        elementorFrontend.hooks.addAction('frontend/element_ready/uicore-icon-box.default', addHandler);
    });
});

/***/ }),

/***/ 437:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

},[436]);