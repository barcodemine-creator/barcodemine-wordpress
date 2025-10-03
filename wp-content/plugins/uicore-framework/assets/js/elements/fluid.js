uicoreJsonp([25],{

/***/ 442:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//Converting colors to proper format
function normalizeColor(hexCode) {
    return [(hexCode >> 16 & 255) / 255, (hexCode >> 8 & 255) / 255, (255 & hexCode) / 255];
}["SCREEN", "LINEAR_LIGHT"].reduce(function (hexCode, t, n) {
    return Object.assign(hexCode, _defineProperty({}, t, n));
}, {});

//Essential functionality of WebGl
//t = width
//n = height

var MiniGl = function () {
    function MiniGl(canvas, width, height) {
        var debug = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

        _classCallCheck(this, MiniGl);

        var _miniGl = this,
            debug_output = -1 !== document.location.search.toLowerCase().indexOf("debug=webgl");
        _miniGl.canvas = canvas, _miniGl.gl = _miniGl.canvas.getContext("webgl", {
            antialias: true
        }), _miniGl.meshes = [];
        var context = _miniGl.gl;
        width && height && this.setSize(width, height), _miniGl.lastDebugMsg, _miniGl.debug = debug && debug_output ? function (e) {
            var _console;

            var t = new Date();
            t - _miniGl.lastDebugMsg > 1e3 && console.log("---"), (_console = console).log.apply(_console, [t.toLocaleTimeString() + Array(Math.max(0, 32 - e.length)).join(" ") + e + ": "].concat(_toConsumableArray(Array.from(arguments).slice(1)))), _miniGl.lastDebugMsg = t;
        } : function () {}, Object.defineProperties(_miniGl, {
            Material: {
                enumerable: false,
                value: function () {
                    function value(vertexShaders, fragments) {
                        var uniforms = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};

                        _classCallCheck(this, value);

                        var material = this;
                        function getShaderByType(type, source) {
                            var shader = context.createShader(type);
                            return context.shaderSource(shader, source), context.compileShader(shader), context.getShaderParameter(shader, context.COMPILE_STATUS) || console.error(context.getShaderInfoLog(shader)), _miniGl.debug("Material.compileShaderSource", {
                                source: source
                            }), shader;
                        }
                        function getUniformVariableDeclarations(uniforms, type) {
                            return Object.entries(uniforms).map(function (_ref) {
                                var _ref2 = _slicedToArray(_ref, 2),
                                    uniform = _ref2[0],
                                    value = _ref2[1];

                                return value.getDeclaration(uniform, type);
                            }).join("\n");
                        }
                        material.uniforms = uniforms, material.uniformInstances = [];

                        var prefix = "\n              precision highp float;\n            ";
                        material.vertexSource = "\n              " + prefix + "\n              attribute vec4 position;\n              attribute vec2 uv;\n              attribute vec2 uvNorm;\n              " + getUniformVariableDeclarations(_miniGl.commonUniforms, "vertex") + "\n              " + getUniformVariableDeclarations(uniforms, "vertex") + "\n              " + vertexShaders + "\n            ", material.Source = "\n              " + prefix + "\n              " + getUniformVariableDeclarations(_miniGl.commonUniforms, "fragment") + "\n              " + getUniformVariableDeclarations(uniforms, "fragment") + "\n              " + fragments + "\n            ", material.vertexShader = getShaderByType(context.VERTEX_SHADER, material.vertexSource), material.fragmentShader = getShaderByType(context.FRAGMENT_SHADER, material.Source), material.program = context.createProgram(), context.attachShader(material.program, material.vertexShader), context.attachShader(material.program, material.fragmentShader), context.linkProgram(material.program), context.getProgramParameter(material.program, context.LINK_STATUS) || console.error(context.getProgramInfoLog(material.program)), context.useProgram(material.program), material.attachUniforms(void 0, _miniGl.commonUniforms), material.attachUniforms(void 0, material.uniforms);
                    }
                    //t = uniform


                    _createClass(value, [{
                        key: "attachUniforms",
                        value: function attachUniforms(name, uniforms) {
                            //n  = material
                            var material = this;
                            void 0 === name ? Object.entries(uniforms).forEach(function (_ref3) {
                                var _ref4 = _slicedToArray(_ref3, 2),
                                    name = _ref4[0],
                                    uniform = _ref4[1];

                                material.attachUniforms(name, uniform);
                            }) : "array" == uniforms.type ? uniforms.value.forEach(function (uniform, i) {
                                return material.attachUniforms(name + "[" + i + "]", uniform);
                            }) : "struct" == uniforms.type ? Object.entries(uniforms.value).forEach(function (_ref5) {
                                var _ref6 = _slicedToArray(_ref5, 2),
                                    uniform = _ref6[0],
                                    i = _ref6[1];

                                return material.attachUniforms(name + "." + uniform, i);
                            }) : (_miniGl.debug("Material.attachUniforms", {
                                name: name,
                                uniform: uniforms
                            }), material.uniformInstances.push({
                                uniform: uniforms,
                                location: context.getUniformLocation(material.program, name)
                            }));
                        }
                    }]);

                    return value;
                }()
            },
            Uniform: {
                enumerable: !1,
                value: function () {
                    function value(e) {
                        _classCallCheck(this, value);

                        this.type = "float", Object.assign(this, e);
                        this.typeFn = {
                            float: "1f",
                            int: "1i",
                            vec2: "2fv",
                            vec3: "3fv",
                            vec4: "4fv",
                            mat4: "Matrix4fv"
                        }[this.type] || "1f", this.update();
                    }

                    _createClass(value, [{
                        key: "update",
                        value: function update(value) {
                            void 0 !== this.value && context["uniform" + this.typeFn](value, 0 === this.typeFn.indexOf("Matrix") ? this.transpose : this.value, 0 === this.typeFn.indexOf("Matrix") ? this.value : null);
                        }
                        //e - name
                        //t - type
                        //n - length

                    }, {
                        key: "getDeclaration",
                        value: function getDeclaration(name, type, length) {
                            var uniform = this;
                            if (uniform.excludeFrom !== type) {
                                if ("array" === uniform.type) return uniform.value[0].getDeclaration(name, type, uniform.value.length) + ("\nconst int " + name + "_length = " + uniform.value.length + ";");
                                if ("struct" === uniform.type) {
                                    var name_no_prefix = name.replace("u_", "");
                                    return name_no_prefix = name_no_prefix.charAt(0).toUpperCase() + name_no_prefix.slice(1), "uniform struct " + name_no_prefix + " \n                                  {\n" + Object.entries(uniform.value).map(function (_ref7) {
                                        var _ref8 = _slicedToArray(_ref7, 2),
                                            name = _ref8[0],
                                            uniform = _ref8[1];

                                        return uniform.getDeclaration(name, type).replace(/^uniform/, "");
                                    }).join("") + ("\n} " + name + (length > 0 ? "[" + length + "]" : "") + ";");
                                }
                                return "uniform " + uniform.type + " " + name + (length > 0 ? "[" + length + "]" : "") + ";";
                            }
                        }
                    }]);

                    return value;
                }()
            },
            PlaneGeometry: {
                enumerable: !1,
                value: function () {
                    function value(width, height, n, i, orientation) {
                        _classCallCheck(this, value);

                        context.createBuffer(), this.attributes = {
                            position: new _miniGl.Attribute({
                                target: context.ARRAY_BUFFER,
                                size: 3
                            }),
                            uv: new _miniGl.Attribute({
                                target: context.ARRAY_BUFFER,
                                size: 2
                            }),
                            uvNorm: new _miniGl.Attribute({
                                target: context.ARRAY_BUFFER,
                                size: 2
                            }),
                            index: new _miniGl.Attribute({
                                target: context.ELEMENT_ARRAY_BUFFER,
                                size: 3,
                                type: context.UNSIGNED_SHORT
                            })
                        }, this.setTopology(n, i), this.setSize(width, height, orientation);
                    }

                    _createClass(value, [{
                        key: "setTopology",
                        value: function setTopology() {
                            var e = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;
                            var t = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;

                            var n = this;
                            n.xSegCount = e, n.ySegCount = t, n.vertexCount = (n.xSegCount + 1) * (n.ySegCount + 1), n.quadCount = n.xSegCount * n.ySegCount * 2, n.attributes.uv.values = new Float32Array(2 * n.vertexCount), n.attributes.uvNorm.values = new Float32Array(2 * n.vertexCount), n.attributes.index.values = new Uint16Array(3 * n.quadCount);
                            for (var _e = 0; _e <= n.ySegCount; _e++) {
                                for (var _t = 0; _t <= n.xSegCount; _t++) {
                                    var i = _e * (n.xSegCount + 1) + _t;
                                    if (n.attributes.uv.values[2 * i] = _t / n.xSegCount, n.attributes.uv.values[2 * i + 1] = 1 - _e / n.ySegCount, n.attributes.uvNorm.values[2 * i] = _t / n.xSegCount * 2 - 1, n.attributes.uvNorm.values[2 * i + 1] = 1 - _e / n.ySegCount * 2, _t < n.xSegCount && _e < n.ySegCount) {
                                        var _s = _e * n.xSegCount + _t;
                                        n.attributes.index.values[6 * _s] = i, n.attributes.index.values[6 * _s + 1] = i + 1 + n.xSegCount, n.attributes.index.values[6 * _s + 2] = i + 1, n.attributes.index.values[6 * _s + 3] = i + 1, n.attributes.index.values[6 * _s + 4] = i + 1 + n.xSegCount, n.attributes.index.values[6 * _s + 5] = i + 2 + n.xSegCount;
                                    }
                                }
                            }n.attributes.uv.update(), n.attributes.uvNorm.update(), n.attributes.index.update(), _miniGl.debug("Geometry.setTopology", {
                                uv: n.attributes.uv,
                                uvNorm: n.attributes.uvNorm,
                                index: n.attributes.index
                            });
                        }
                    }, {
                        key: "setSize",
                        value: function setSize() {
                            var width = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;
                            var height = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
                            var orientation = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "xz";

                            var geometry = this;
                            geometry.width = width, geometry.height = height, geometry.orientation = orientation, geometry.attributes.position.values && geometry.attributes.position.values.length === 3 * geometry.vertexCount || (geometry.attributes.position.values = new Float32Array(3 * geometry.vertexCount));
                            var o = width / -2,
                                r = height / -2,
                                segment_width = width / geometry.xSegCount,
                                segment_height = height / geometry.ySegCount;
                            for (var yIndex = 0; yIndex <= geometry.ySegCount; yIndex++) {
                                var t = r + yIndex * segment_height;
                                for (var xIndex = 0; xIndex <= geometry.xSegCount; xIndex++) {
                                    var _r = o + xIndex * segment_width,
                                        l = yIndex * (geometry.xSegCount + 1) + xIndex;
                                    geometry.attributes.position.values[3 * l + "xyz".indexOf(orientation[0])] = _r, geometry.attributes.position.values[3 * l + "xyz".indexOf(orientation[1])] = -t;
                                }
                            }
                            geometry.attributes.position.update(), _miniGl.debug("Geometry.setSize", {
                                position: geometry.attributes.position
                            });
                        }
                    }]);

                    return value;
                }()
            },
            Mesh: {
                enumerable: !1,
                value: function () {
                    function value(geometry, material) {
                        _classCallCheck(this, value);

                        var mesh = this;
                        mesh.geometry = geometry, mesh.material = material, mesh.wireframe = !1, mesh.attributeInstances = [], Object.entries(mesh.geometry.attributes).forEach(function (_ref9) {
                            var _ref10 = _slicedToArray(_ref9, 2),
                                e = _ref10[0],
                                attribute = _ref10[1];

                            mesh.attributeInstances.push({
                                attribute: attribute,
                                location: attribute.attach(e, mesh.material.program)
                            });
                        }), _miniGl.meshes.push(mesh), _miniGl.debug("Mesh.constructor", {
                            mesh: mesh
                        });
                    }

                    _createClass(value, [{
                        key: "draw",
                        value: function draw() {
                            context.useProgram(this.material.program), this.material.uniformInstances.forEach(function (_ref11) {
                                var e = _ref11.uniform,
                                    t = _ref11.location;
                                return e.update(t);
                            }), this.attributeInstances.forEach(function (_ref12) {
                                var e = _ref12.attribute,
                                    t = _ref12.location;
                                return e.use(t);
                            }), context.drawElements(this.wireframe ? context.LINES : context.TRIANGLES, this.geometry.attributes.index.values.length, context.UNSIGNED_SHORT, 0);
                        }
                    }, {
                        key: "remove",
                        value: function remove() {
                            var _this = this;

                            _miniGl.meshes = _miniGl.meshes.filter(function (e) {
                                return e != _this;
                            });
                        }
                    }]);

                    return value;
                }()
            },
            Attribute: {
                enumerable: !1,
                value: function () {
                    function value(e) {
                        _classCallCheck(this, value);

                        this.type = context.FLOAT, this.normalized = !1, this.buffer = context.createBuffer(), Object.assign(this, e), this.update();
                    }

                    _createClass(value, [{
                        key: "update",
                        value: function update() {
                            void 0 !== this.values && (context.bindBuffer(this.target, this.buffer), context.bufferData(this.target, this.values, context.STATIC_DRAW));
                        }
                    }, {
                        key: "attach",
                        value: function attach(e, t) {
                            var n = context.getAttribLocation(t, e);
                            return this.target === context.ARRAY_BUFFER && (context.enableVertexAttribArray(n), context.vertexAttribPointer(n, this.size, this.type, this.normalized, 0, 0)), n;
                        }
                    }, {
                        key: "use",
                        value: function use(e) {
                            context.bindBuffer(this.target, this.buffer), this.target === context.ARRAY_BUFFER && (context.enableVertexAttribArray(e), context.vertexAttribPointer(e, this.size, this.type, this.normalized, 0, 0));
                        }
                    }]);

                    return value;
                }()
            }
        });
        var a = [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];
        _miniGl.commonUniforms = {
            projectionMatrix: new _miniGl.Uniform({
                type: "mat4",
                value: a
            }),
            modelViewMatrix: new _miniGl.Uniform({
                type: "mat4",
                value: a
            }),
            resolution: new _miniGl.Uniform({
                type: "vec2",
                value: [1, 1]
            }),
            aspectRatio: new _miniGl.Uniform({
                type: "float",
                value: 1
            })
        };
    }

    _createClass(MiniGl, [{
        key: "setSize",
        value: function setSize() {
            var e = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 640;
            var t = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 480;

            this.width = e, this.height = t, this.canvas.width = e, this.canvas.height = t, this.gl.viewport(0, 0, e, t), this.commonUniforms.resolution.value = [e, t], this.commonUniforms.aspectRatio.value = e / t, this.debug("MiniGL.setSize", {
                width: e,
                height: t
            });
        }
        //left, right, top, bottom, near, far

    }, {
        key: "setOrthographicCamera",
        value: function setOrthographicCamera() {
            var e = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
            var t = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
            var n = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;
            var i = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : -2e3;
            var s = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 2e3;

            this.commonUniforms.projectionMatrix.value = [2 / this.width, 0, 0, 0, 0, 2 / this.height, 0, 0, 0, 0, 2 / (i - s), 0, e, t, n, 1], this.debug("setOrthographicCamera", this.commonUniforms.projectionMatrix.value);
        }
    }, {
        key: "render",
        value: function render() {
            this.gl.clearColor(0, 0, 0, 0), this.gl.clearDepth(1), this.meshes.forEach(function (e) {
                return e.draw();
            });
        }
    }]);

    return MiniGl;
}();

//Sets initial properties


function e(object, propertyName, val) {
    return propertyName in object ? Object.defineProperty(object, propertyName, {
        value: val,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : object[propertyName] = val, object;
}

//Gradient object

var Gradient = function () {
    function Gradient() {
        var _this2 = this;

        _classCallCheck(this, Gradient);

        e(this, "el", void 0), e(this, "cssVarRetries", 0), e(this, "maxCssVarRetries", 200), e(this, "angle", 0), e(this, "isLoadedClass", !1), e(this, "isScrolling", !1), /*e(this, "isStatic", o.disableAmbientAnimations()),*/e(this, "scrollingTimeout", void 0), e(this, "scrollingRefreshDelay", 200), e(this, "isIntersecting", !1), e(this, "shaderFiles", void 0), e(this, "vertexShader", void 0), e(this, "sectionColors", void 0), e(this, "computedCanvasStyle", void 0), e(this, "conf", void 0), e(this, "uniforms", void 0), e(this, "t", 1253106), e(this, "last", 0), e(this, "width", void 0), e(this, "minWidth", 1111), e(this, "height", 600), e(this, "xSegCount", void 0), e(this, "ySegCount", void 0), e(this, "mesh", void 0), e(this, "material", void 0), e(this, "geometry", void 0), e(this, "minigl", void 0), e(this, "scrollObserver", void 0), e(this, "amp", 320), e(this, "seed", 5), e(this, "freqX", 14e-5), e(this, "freqY", 29e-5), e(this, "freqDelta", 1e-5), e(this, "activeColors", [1, 1, 1, 1]), e(this, "isMetaKey", !1), e(this, "isGradientLegendVisible", !1), e(this, "isMouseDown", !1), e(this, "handleScroll", function () {
            clearTimeout(_this2.scrollingTimeout), _this2.scrollingTimeout = setTimeout(_this2.handleScrollEnd, _this2.scrollingRefreshDelay), _this2.isGradientLegendVisible && _this2.hideGradientLegend(), _this2.conf.playing && (_this2.isScrolling = !0, _this2.pause());
        }), e(this, "handleScrollEnd", function () {
            _this2.isScrolling = !1, _this2.isIntersecting && _this2.play();
        }), e(this, "resize", function () {
            _this2.width = window.innerWidth, _this2.minigl.setSize(_this2.width, _this2.height), _this2.minigl.setOrthographicCamera(), _this2.xSegCount = Math.ceil(_this2.width * _this2.conf.density[0]), _this2.ySegCount = Math.ceil(_this2.height * _this2.conf.density[1]), _this2.mesh.geometry.setTopology(_this2.xSegCount, _this2.ySegCount), _this2.mesh.geometry.setSize(_this2.width, _this2.height), _this2.mesh.material.uniforms.u_shadow_power.value = _this2.width < 600 ? 5 : 6;
        }), e(this, "handleMouseDown", function (e) {
            _this2.isGradientLegendVisible && (_this2.isMetaKey = e.metaKey, _this2.isMouseDown = !0, !1 === _this2.conf.playing && requestAnimationFrame(_this2.animate));
        }), e(this, "handleMouseUp", function () {
            _this2.isMouseDown = !1;
        }), e(this, "animate", function (e) {
            if (!_this2.shouldSkipFrame(e) || _this2.isMouseDown) {
                if (_this2.t += Math.min(e - _this2.last, 1e3 / 15), _this2.last = e, _this2.isMouseDown) {
                    var _e2 = 160;
                    _this2.isMetaKey && (_e2 = -160), _this2.t += _e2;
                }
                _this2.mesh.material.uniforms.u_time.value = _this2.t, _this2.minigl.render();
            }
            if (0 !== _this2.last && _this2.isStatic) return _this2.minigl.render(), void _this2.disconnect();
            (_this2.isIntersecting && _this2.conf.playing || _this2.isMouseDown) && requestAnimationFrame(_this2.animate);
        }), e(this, "addIsLoadedClass", function () {
            _this2.isIntersecting && !_this2.isLoadedClass && (_this2.isLoadedClass = !0, _this2.el.classList.add("isLoaded"), setTimeout(function () {
                _this2.el.parentElement.classList.add("isLoaded");
            }, 3e3));
        }), e(this, "pause", function () {
            _this2.conf.playing = false;
        }), e(this, "play", function () {
            requestAnimationFrame(_this2.animate), _this2.conf.playing = true;
        }), e(this, "initGradient", function (selector) {
            _this2.el = document.querySelector(selector);
            _this2.connect();
            return _this2;
        });
    }

    _createClass(Gradient, [{
        key: "connect",
        value: async function connect() {
            var _this3 = this;

            this.shaderFiles = {
                vertex: "varying vec3 v_color;\n\nvoid main() {\n  float time = u_time * u_global.noiseSpeed;\n\n  vec2 noiseCoord = resolution * uvNorm * u_global.noiseFreq;\n\n  vec2 st = 1. - uvNorm.xy;\n\n  //\n  // Tilting the plane\n  //\n\n  // Front-to-back tilt\n  float tilt = resolution.y / 2.0 * uvNorm.y;\n\n  // Left-to-right angle\n  float incline = resolution.x * uvNorm.x / 2.0 * u_vertDeform.incline;\n\n  // Up-down shift to offset incline\n  float offset = resolution.x / 2.0 * u_vertDeform.incline * mix(u_vertDeform.offsetBottom, u_vertDeform.offsetTop, uv.y);\n\n  //\n  // Vertex noise\n  //\n\n  float noise = snoise(vec3(\n    noiseCoord.x * u_vertDeform.noiseFreq.x + time * u_vertDeform.noiseFlow,\n    noiseCoord.y * u_vertDeform.noiseFreq.y,\n    time * u_vertDeform.noiseSpeed + u_vertDeform.noiseSeed\n  )) * u_vertDeform.noiseAmp;\n\n  // Fade noise to zero at edges\n  noise *= 1.0 - pow(abs(uvNorm.y), 2.0);\n\n  // Clamp to 0\n  noise = max(0.0, noise);\n\n  vec3 pos = vec3(\n    position.x,\n    position.y + tilt + incline + noise - offset,\n    position.z\n  );\n\n  //\n  // Vertex color, to be passed to fragment shader\n  //\n\n  if (u_active_colors[0] == 1.) {\n    v_color = u_baseColor;\n  }\n\n  for (int i = 0; i < u_waveLayers_length; i++) {\n    if (u_active_colors[i + 1] == 1.) {\n      WaveLayers layer = u_waveLayers[i];\n\n      float noise = smoothstep(\n        layer.noiseFloor,\n        layer.noiseCeil,\n        snoise(vec3(\n          noiseCoord.x * layer.noiseFreq.x + time * layer.noiseFlow,\n          noiseCoord.y * layer.noiseFreq.y,\n          time * layer.noiseSpeed + layer.noiseSeed\n        )) / 2.0 + 0.5\n      );\n\n      v_color = blendNormal(v_color, layer.color, pow(noise, 4.));\n    }\n  }\n\n  //\n  // Finish\n  //\n\n  gl_Position = projectionMatrix * modelViewMatrix * vec4(pos, 1.0);\n}",
                noise: "//\n// Description : Array and textureless GLSL 2D/3D/4D simplex\n//               noise functions.\n//      Author : Ian McEwan, Ashima Arts.\n//  Maintainer : stegu\n//     Lastmod : 20110822 (ijm)\n//     License : Copyright (C) 2011 Ashima Arts. All rights reserved.\n//               Distributed under the MIT License. See LICENSE file.\n//               https://github.com/ashima/webgl-noise\n//               https://github.com/stegu/webgl-noise\n//\n\nvec3 mod289(vec3 x) {\n  return x - floor(x * (1.0 / 289.0)) * 289.0;\n}\n\nvec4 mod289(vec4 x) {\n  return x - floor(x * (1.0 / 289.0)) * 289.0;\n}\n\nvec4 permute(vec4 x) {\n    return mod289(((x*34.0)+1.0)*x);\n}\n\nvec4 taylorInvSqrt(vec4 r)\n{\n  return 1.79284291400159 - 0.85373472095314 * r;\n}\n\nfloat snoise(vec3 v)\n{\n  const vec2  C = vec2(1.0/6.0, 1.0/3.0) ;\n  const vec4  D = vec4(0.0, 0.5, 1.0, 2.0);\n\n// First corner\n  vec3 i  = floor(v + dot(v, C.yyy) );\n  vec3 x0 =   v - i + dot(i, C.xxx) ;\n\n// Other corners\n  vec3 g = step(x0.yzx, x0.xyz);\n  vec3 l = 1.0 - g;\n  vec3 i1 = min( g.xyz, l.zxy );\n  vec3 i2 = max( g.xyz, l.zxy );\n\n  //   x0 = x0 - 0.0 + 0.0 * C.xxx;\n  //   x1 = x0 - i1  + 1.0 * C.xxx;\n  //   x2 = x0 - i2  + 2.0 * C.xxx;\n  //   x3 = x0 - 1.0 + 3.0 * C.xxx;\n  vec3 x1 = x0 - i1 + C.xxx;\n  vec3 x2 = x0 - i2 + C.yyy; // 2.0*C.x = 1/3 = C.y\n  vec3 x3 = x0 - D.yyy;      // -1.0+3.0*C.x = -0.5 = -D.y\n\n// Permutations\n  i = mod289(i);\n  vec4 p = permute( permute( permute(\n            i.z + vec4(0.0, i1.z, i2.z, 1.0 ))\n          + i.y + vec4(0.0, i1.y, i2.y, 1.0 ))\n          + i.x + vec4(0.0, i1.x, i2.x, 1.0 ));\n\n// Gradients: 7x7 points over a square, mapped onto an octahedron.\n// The ring size 17*17 = 289 is close to a multiple of 49 (49*6 = 294)\n  float n_ = 0.142857142857; // 1.0/7.0\n  vec3  ns = n_ * D.wyz - D.xzx;\n\n  vec4 j = p - 49.0 * floor(p * ns.z * ns.z);  //  mod(p,7*7)\n\n  vec4 x_ = floor(j * ns.z);\n  vec4 y_ = floor(j - 7.0 * x_ );    // mod(j,N)\n\n  vec4 x = x_ *ns.x + ns.yyyy;\n  vec4 y = y_ *ns.x + ns.yyyy;\n  vec4 h = 1.0 - abs(x) - abs(y);\n\n  vec4 b0 = vec4( x.xy, y.xy );\n  vec4 b1 = vec4( x.zw, y.zw );\n\n  //vec4 s0 = vec4(lessThan(b0,0.0))*2.0 - 1.0;\n  //vec4 s1 = vec4(lessThan(b1,0.0))*2.0 - 1.0;\n  vec4 s0 = floor(b0)*2.0 + 1.0;\n  vec4 s1 = floor(b1)*2.0 + 1.0;\n  vec4 sh = -step(h, vec4(0.0));\n\n  vec4 a0 = b0.xzyw + s0.xzyw*sh.xxyy ;\n  vec4 a1 = b1.xzyw + s1.xzyw*sh.zzww ;\n\n  vec3 p0 = vec3(a0.xy,h.x);\n  vec3 p1 = vec3(a0.zw,h.y);\n  vec3 p2 = vec3(a1.xy,h.z);\n  vec3 p3 = vec3(a1.zw,h.w);\n\n//Normalise gradients\n  vec4 norm = taylorInvSqrt(vec4(dot(p0,p0), dot(p1,p1), dot(p2, p2), dot(p3,p3)));\n  p0 *= norm.x;\n  p1 *= norm.y;\n  p2 *= norm.z;\n  p3 *= norm.w;\n\n// Mix final noise value\n  vec4 m = max(0.6 - vec4(dot(x0,x0), dot(x1,x1), dot(x2,x2), dot(x3,x3)), 0.0);\n  m = m * m;\n  return 42.0 * dot( m*m, vec4( dot(p0,x0), dot(p1,x1),\n                                dot(p2,x2), dot(p3,x3) ) );\n}",
                blend: "//\n// https://github.com/jamieowen/glsl-blend\n//\n\n// Normal\n\nvec3 blendNormal(vec3 base, vec3 blend) {\n\treturn blend;\n}\n\nvec3 blendNormal(vec3 base, vec3 blend, float opacity) {\n\treturn (blendNormal(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Screen\n\nfloat blendScreen(float base, float blend) {\n\treturn 1.0-((1.0-base)*(1.0-blend));\n}\n\nvec3 blendScreen(vec3 base, vec3 blend) {\n\treturn vec3(blendScreen(base.r,blend.r),blendScreen(base.g,blend.g),blendScreen(base.b,blend.b));\n}\n\nvec3 blendScreen(vec3 base, vec3 blend, float opacity) {\n\treturn (blendScreen(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Multiply\n\nvec3 blendMultiply(vec3 base, vec3 blend) {\n\treturn base*blend;\n}\n\nvec3 blendMultiply(vec3 base, vec3 blend, float opacity) {\n\treturn (blendMultiply(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Overlay\n\nfloat blendOverlay(float base, float blend) {\n\treturn base<0.5?(2.0*base*blend):(1.0-2.0*(1.0-base)*(1.0-blend));\n}\n\nvec3 blendOverlay(vec3 base, vec3 blend) {\n\treturn vec3(blendOverlay(base.r,blend.r),blendOverlay(base.g,blend.g),blendOverlay(base.b,blend.b));\n}\n\nvec3 blendOverlay(vec3 base, vec3 blend, float opacity) {\n\treturn (blendOverlay(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Hard light\n\nvec3 blendHardLight(vec3 base, vec3 blend) {\n\treturn blendOverlay(blend,base);\n}\n\nvec3 blendHardLight(vec3 base, vec3 blend, float opacity) {\n\treturn (blendHardLight(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Soft light\n\nfloat blendSoftLight(float base, float blend) {\n\treturn (blend<0.5)?(2.0*base*blend+base*base*(1.0-2.0*blend)):(sqrt(base)*(2.0*blend-1.0)+2.0*base*(1.0-blend));\n}\n\nvec3 blendSoftLight(vec3 base, vec3 blend) {\n\treturn vec3(blendSoftLight(base.r,blend.r),blendSoftLight(base.g,blend.g),blendSoftLight(base.b,blend.b));\n}\n\nvec3 blendSoftLight(vec3 base, vec3 blend, float opacity) {\n\treturn (blendSoftLight(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Color dodge\n\nfloat blendColorDodge(float base, float blend) {\n\treturn (blend==1.0)?blend:min(base/(1.0-blend),1.0);\n}\n\nvec3 blendColorDodge(vec3 base, vec3 blend) {\n\treturn vec3(blendColorDodge(base.r,blend.r),blendColorDodge(base.g,blend.g),blendColorDodge(base.b,blend.b));\n}\n\nvec3 blendColorDodge(vec3 base, vec3 blend, float opacity) {\n\treturn (blendColorDodge(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Color burn\n\nfloat blendColorBurn(float base, float blend) {\n\treturn (blend==0.0)?blend:max((1.0-((1.0-base)/blend)),0.0);\n}\n\nvec3 blendColorBurn(vec3 base, vec3 blend) {\n\treturn vec3(blendColorBurn(base.r,blend.r),blendColorBurn(base.g,blend.g),blendColorBurn(base.b,blend.b));\n}\n\nvec3 blendColorBurn(vec3 base, vec3 blend, float opacity) {\n\treturn (blendColorBurn(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Vivid Light\n\nfloat blendVividLight(float base, float blend) {\n\treturn (blend<0.5)?blendColorBurn(base,(2.0*blend)):blendColorDodge(base,(2.0*(blend-0.5)));\n}\n\nvec3 blendVividLight(vec3 base, vec3 blend) {\n\treturn vec3(blendVividLight(base.r,blend.r),blendVividLight(base.g,blend.g),blendVividLight(base.b,blend.b));\n}\n\nvec3 blendVividLight(vec3 base, vec3 blend, float opacity) {\n\treturn (blendVividLight(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Lighten\n\nfloat blendLighten(float base, float blend) {\n\treturn max(blend,base);\n}\n\nvec3 blendLighten(vec3 base, vec3 blend) {\n\treturn vec3(blendLighten(base.r,blend.r),blendLighten(base.g,blend.g),blendLighten(base.b,blend.b));\n}\n\nvec3 blendLighten(vec3 base, vec3 blend, float opacity) {\n\treturn (blendLighten(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Linear burn\n\nfloat blendLinearBurn(float base, float blend) {\n\t// Note : Same implementation as BlendSubtractf\n\treturn max(base+blend-1.0,0.0);\n}\n\nvec3 blendLinearBurn(vec3 base, vec3 blend) {\n\t// Note : Same implementation as BlendSubtract\n\treturn max(base+blend-vec3(1.0),vec3(0.0));\n}\n\nvec3 blendLinearBurn(vec3 base, vec3 blend, float opacity) {\n\treturn (blendLinearBurn(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Linear dodge\n\nfloat blendLinearDodge(float base, float blend) {\n\t// Note : Same implementation as BlendAddf\n\treturn min(base+blend,1.0);\n}\n\nvec3 blendLinearDodge(vec3 base, vec3 blend) {\n\t// Note : Same implementation as BlendAdd\n\treturn min(base+blend,vec3(1.0));\n}\n\nvec3 blendLinearDodge(vec3 base, vec3 blend, float opacity) {\n\treturn (blendLinearDodge(base, blend) * opacity + base * (1.0 - opacity));\n}\n\n// Linear light\n\nfloat blendLinearLight(float base, float blend) {\n\treturn blend<0.5?blendLinearBurn(base,(2.0*blend)):blendLinearDodge(base,(2.0*(blend-0.5)));\n}\n\nvec3 blendLinearLight(vec3 base, vec3 blend) {\n\treturn vec3(blendLinearLight(base.r,blend.r),blendLinearLight(base.g,blend.g),blendLinearLight(base.b,blend.b));\n}\n\nvec3 blendLinearLight(vec3 base, vec3 blend, float opacity) {\n\treturn (blendLinearLight(base, blend) * opacity + base * (1.0 - opacity));\n}",
                fragment: "varying vec3 v_color;\n\nvoid main() {\n  vec3 color = v_color;\n  if (u_darken_top == 1.0) {\n    vec2 st = gl_FragCoord.xy/resolution.xy;\n    color.g -= pow(st.y + sin(-12.0) * st.x, u_shadow_power) * 0.4;\n  }\n  gl_FragColor = vec4(color, 1.0);\n}"
            }, this.conf = {
                presetName: "",
                wireframe: false,
                density: [.06, .16],
                zoom: 1,
                rotation: 0,
                playing: true
            }, document.querySelectorAll("canvas").length < 1 ? console.log("DID NOT LOAD FLUID CANVAS") : (this.minigl = new MiniGl(this.el, null, null, !0), requestAnimationFrame(function () {
                _this3.el && (_this3.computedCanvasStyle = getComputedStyle(_this3.el), _this3.waitForCssVars());
            }), this.scrollObserver = await s.create(.1, !1), this.scrollObserver.observe(this.el), this.scrollObserver.onSeparate(function () {
                window.removeEventListener("scroll", _this3.handleScroll), window.removeEventListener("mousedown", _this3.handleMouseDown), window.removeEventListener("mouseup", _this3.handleMouseUp), window.removeEventListener("keydown", _this3.handleKeyDown), _this3.isIntersecting = !1, _this3.conf.playing && _this3.pause();
            }), this.scrollObserver.onIntersect(function () {
                window.addEventListener("scroll", _this3.handleScroll), window.addEventListener("mousedown", _this3.handleMouseDown), window.addEventListener("mouseup", _this3.handleMouseUp), window.addEventListener("keydown", _this3.handleKeyDown), _this3.isIntersecting = !0, _this3.addIsLoadedClass(), _this3.play();
            })
            //   */

            );
        }
    }, {
        key: "disconnect",
        value: function disconnect() {
            this.scrollObserver && (window.removeEventListener("scroll", this.handleScroll), window.removeEventListener("mousedown", this.handleMouseDown), window.removeEventListener("mouseup", this.handleMouseUp), window.removeEventListener("keydown", this.handleKeyDown), this.scrollObserver.disconnect()), window.removeEventListener("resize", this.resize);
        }
    }, {
        key: "initMaterial",
        value: function initMaterial() {
            this.uniforms = {
                u_time: new this.minigl.Uniform({
                    value: 0
                }),
                u_shadow_power: new this.minigl.Uniform({
                    value: 5
                }),
                u_darken_top: new this.minigl.Uniform({
                    value: "" === this.el.dataset.jsDarkenTop ? 1 : 0
                }),
                u_active_colors: new this.minigl.Uniform({
                    value: this.activeColors,
                    type: "vec4"
                }),
                u_global: new this.minigl.Uniform({
                    value: {
                        noiseFreq: new this.minigl.Uniform({
                            value: [this.freqX, this.freqY],
                            type: "vec2"
                        }),
                        noiseSpeed: new this.minigl.Uniform({
                            value: 5e-6
                        })
                    },
                    type: "struct"
                }),
                u_vertDeform: new this.minigl.Uniform({
                    value: {
                        incline: new this.minigl.Uniform({
                            value: Math.sin(this.angle) / Math.cos(this.angle)
                        }),
                        offsetTop: new this.minigl.Uniform({
                            value: -.5
                        }),
                        offsetBottom: new this.minigl.Uniform({
                            value: -.5
                        }),
                        noiseFreq: new this.minigl.Uniform({
                            value: [3, 4],
                            type: "vec2"
                        }),
                        noiseAmp: new this.minigl.Uniform({
                            value: this.amp
                        }),
                        noiseSpeed: new this.minigl.Uniform({
                            value: 10
                        }),
                        noiseFlow: new this.minigl.Uniform({
                            value: 3
                        }),
                        noiseSeed: new this.minigl.Uniform({
                            value: this.seed
                        })
                    },
                    type: "struct",
                    excludeFrom: "fragment"
                }),
                u_baseColor: new this.minigl.Uniform({
                    value: this.sectionColors[0],
                    type: "vec3",
                    excludeFrom: "fragment"
                }),
                u_waveLayers: new this.minigl.Uniform({
                    value: [],
                    excludeFrom: "fragment",
                    type: "array"
                })
            };
            for (var _e3 = 1; _e3 < this.sectionColors.length; _e3 += 1) {
                this.uniforms.u_waveLayers.value.push(new this.minigl.Uniform({
                    value: {
                        color: new this.minigl.Uniform({
                            value: this.sectionColors[_e3],
                            type: "vec3"
                        }),
                        noiseFreq: new this.minigl.Uniform({
                            value: [2 + _e3 / this.sectionColors.length, 3 + _e3 / this.sectionColors.length],
                            type: "vec2"
                        }),
                        noiseSpeed: new this.minigl.Uniform({
                            value: 11 + .3 * _e3
                        }),
                        noiseFlow: new this.minigl.Uniform({
                            value: 6.5 + .3 * _e3
                        }),
                        noiseSeed: new this.minigl.Uniform({
                            value: this.seed + 10 * _e3
                        }),
                        noiseFloor: new this.minigl.Uniform({
                            value: .1
                        }),
                        noiseCeil: new this.minigl.Uniform({
                            value: .63 + .07 * _e3
                        })
                    },
                    type: "struct"
                }));
            }return this.vertexShader = [this.shaderFiles.noise, this.shaderFiles.blend, this.shaderFiles.vertex].join("\n\n"), new this.minigl.Material(this.vertexShader, this.shaderFiles.fragment, this.uniforms);
        }
    }, {
        key: "initMesh",
        value: function initMesh() {
            this.material = this.initMaterial(), this.geometry = new this.minigl.PlaneGeometry(), this.mesh = new this.minigl.Mesh(this.geometry, this.material);
        }
    }, {
        key: "shouldSkipFrame",
        value: function shouldSkipFrame(e) {
            return !!window.document.hidden || !this.conf.playing || parseInt(e, 10) % 2 == 0 || void 0;
        }
    }, {
        key: "updateFrequency",
        value: function updateFrequency(e) {
            this.freqX += e, this.freqY += e;
        }
    }, {
        key: "toggleColor",
        value: function toggleColor(index) {
            this.activeColors[index] = 0 === this.activeColors[index] ? 1 : 0;
        }
    }, {
        key: "showGradientLegend",
        value: function showGradientLegend() {
            this.width > this.minWidth && (this.isGradientLegendVisible = !0, document.body.classList.add("isGradientLegendVisible"));
        }
    }, {
        key: "hideGradientLegend",
        value: function hideGradientLegend() {
            this.isGradientLegendVisible = !1, document.body.classList.remove("isGradientLegendVisible");
        }
    }, {
        key: "init",
        value: function init() {
            this.initGradientColors(), this.initMesh(), this.resize(), requestAnimationFrame(this.animate), window.addEventListener("resize", this.resize);
        }
        /*
        * Waiting for the css variables to become available, usually on page load before we can continue.
        * Using default colors assigned below if no variables have been found after maxCssVarRetries
        */

    }, {
        key: "waitForCssVars",
        value: function waitForCssVars() {
            var _this4 = this;

            if (this.computedCanvasStyle && -1 !== this.computedCanvasStyle.getPropertyValue("--ui-fluid-1").indexOf("#")) this.init(), this.addIsLoadedClass();else {
                if (this.cssVarRetries += 1, this.cssVarRetries > this.maxCssVarRetries) {
                    return this.sectionColors = [16711680, 16711680, 16711935, 65280, 255], void this.init();
                }
                requestAnimationFrame(function () {
                    return _this4.waitForCssVars();
                });
            }
        }
        /*
        * Initializes the four section colors by retrieving them from css variables.
        */

    }, {
        key: "initGradientColors",
        value: function initGradientColors() {
            var _this5 = this;

            this.sectionColors = ["--ui-fluid-1", "--ui-fluid-2", "--ui-fluid-3", "--ui-fluid-4"].map(function (cssPropertyName) {
                var hex = _this5.computedCanvasStyle.getPropertyValue(cssPropertyName).trim();
                //Check if shorthand hex value was used and double the length so the conversion in normalizeColor will work.
                if (4 === hex.length) {
                    var hexTemp = hex.substr(1).split("").map(function (hexTemp) {
                        return hexTemp + hexTemp;
                    }).join("");
                    hex = "#" + hexTemp;
                }
                return hex && "0x" + hex.substr(1);
            }).filter(Boolean).map(normalizeColor);
        }
    }]);

    return Gradient;
}();

var r = function () {
    function r(_ref13) {
        var _this6 = this;

        var _r2 = _ref13.threshold,
            s = _ref13.requireThreshold,
            t = _ref13.onlyOnce,
            o = _ref13.rootMargin,
            n = _ref13.root;

        _classCallCheck(this, r);

        e(this, "__intersectHandlers", []), e(this, "__separateHandlers", []), e(this, "__observer", void 0), this.__observer = new IntersectionObserver(function (e) {
            !s || e[0].intersectionRatio >= _r2 ? (_this6.__intersectHandlers.forEach(function (r) {
                r(e);
            }), t && _this6.disconnect()) : _this6.__separateHandlers.forEach(function (r) {
                r(e);
            });
        }, {
            threshold: _r2,
            rootMargin: o,
            root: n
        });
    }

    _createClass(r, [{
        key: "observe",
        value: function observe(e) {
            this.__observer.observe(e);
        }
    }, {
        key: "onIntersect",
        value: function onIntersect(e) {
            this.__intersectHandlers.push(e);
        }
    }, {
        key: "onSeparate",
        value: function onSeparate(e) {
            this.__separateHandlers.push(e);
        }
    }, {
        key: "disconnect",
        value: function disconnect() {
            this.__observer.disconnect();
        }
    }]);

    return r;
}();

var s = {
    create: function create() {
        var e = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;
        var s = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : !1;
        var t = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : !0;
        var o = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : "0px 0px 0px 0px";
        var n = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;

        return new Promise(function (i) {
            function c() {
                i(new r({
                    threshold: e,
                    requireThreshold: t,
                    onlyOnce: s,
                    rootMargin: o,
                    root: n
                }));
            }
            c();
        });
    }
};
window.addEventListener('DOMContentLoaded', function () {
    var FLUID = function (_elementorModules$fro) {
        _inherits(FLUID, _elementorModules$fro);

        function FLUID() {
            _classCallCheck(this, FLUID);

            return _possibleConstructorReturn(this, (FLUID.__proto__ || Object.getPrototypeOf(FLUID)).apply(this, arguments));
        }

        _createClass(FLUID, [{
            key: "bindEvents",
            value: function bindEvents() {
                if (this.getElementSettings('section_fluid_on') === 'yes') {
                    this.Init();
                }
            }
        }, {
            key: "onElementChange",
            value: function onElementChange(prop) {

                var is_fluid = this.getElementSettings('section_fluid_on') === 'yes';
                if (prop.indexOf('fluid') >= 0 && is_fluid) {
                    this.Init();
                }
                return;
            }
        }, {
            key: "Init",
            value: function Init() {
                this.$element.find('.ui-e-fluid-canvas').remove();
                var id = Math.floor(1000 + Math.random() * 9000);
                if (this.$element.attr('class').indexOf('ui-fluid-animation-6') >= 0) {
                    this.$element.prepend("<canvas class='ui-e-fluid-canvas' id='ui-fluid-canvas-" + id + "' data-transition-in />");
                    var gradient = new Gradient();
                    gradient.initGradient("#ui-fluid-canvas-" + id);
                } else {
                    this.$element.prepend('<div class="ui-fluid-gradient-wrapper ui-e-fluid-canvas"><div class="ui-fluid-gradient"></div></div>');
                }
            }
        }]);

        return FLUID;
    }(elementorModules.frontend.handlers.Base);

    jQuery(window).on('elementor/frontend/init', function () {
        var addHandler = function addHandler($element) {
            elementorFrontend.elementsHandler.addHandler(FLUID, { $element: $element });
        };
        elementorFrontend.hooks.addAction('frontend/element_ready/section', addHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/container', addHandler);
    });
}, false);

/***/ })

},[442]);