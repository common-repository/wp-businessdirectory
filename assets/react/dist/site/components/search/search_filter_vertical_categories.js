"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

var SearchFilterVerticalCategories = /*#__PURE__*/function (_React$Component) {
  _inherits(SearchFilterVerticalCategories, _React$Component);

  var _super = _createSuper(SearchFilterVerticalCategories);

  function SearchFilterVerticalCategories(props) {
    _classCallCheck(this, SearchFilterVerticalCategories);

    return _super.call(this, props);
  }

  _createClass(SearchFilterVerticalCategories, [{
    key: "getRegularFilters",
    value: function getRegularFilters(categories) {
      var _this = this;

      var counterCategories = 0;
      var categoryFilters = [];
      var moreCategoryFilters = [];

      var _loop = function _loop(i) {
        var filterCriteria = categories[i];

        if (counterCategories < _this.props.searchFilterItems) {
          if (filterCriteria[1] > 0) {
            categoryFilters.push( /*#__PURE__*/React.createElement("li", {
              key: Math.random() + '-' + i
            }, _this.props.category != null && filterCriteria[0][0].id == _this.props.category.id ? /*#__PURE__*/React.createElement("strong", null, filterCriteria[0][0].name) : /*#__PURE__*/React.createElement("a", {
              className: "cursor-pointer",
              onClick: function onClick() {
                return jbdUtils.chooseCategory(filterCriteria[0][0].id);
              }
            }, filterCriteria[0][0].name)));
          }

          counterCategories++;
        } else {
          categoryFilters.push( /*#__PURE__*/React.createElement("a", {
            id: "showMoreCategories",
            className: "filterExpand cursor-pointer",
            onClick: function onClick() {
              return jbdUtils.showMoreParams('extra_categories_params', 'showMoreCategories');
            }
          }, JBD.JText._('LNG_MORE'), " (+)"));
          return "break";
        }
      };

      for (var i = 0; i < categories.length; i++) {
        var _ret = _loop(i);

        if (_ret === "break") break;
      }

      var _loop2 = function _loop2(_i) {
        var filterCriteria = categories[_i];
        counterCategories--;

        if (counterCategories < 0) {
          if (filterCriteria[1] > 0) {
            moreCategoryFilters.push( /*#__PURE__*/React.createElement("li", {
              key: Math.random() + '-' + _i
            }, _this.props.category != null && filterCriteria[0][0] == _this.props.category.id ? /*#__PURE__*/React.createElement("strong", null, filterCriteria[0][0].name) : /*#__PURE__*/React.createElement("a", {
              className: "cursor-pointer",
              onClick: function onClick() {
                return jbdUtils.chooseCategory(filterCriteria[0][0].id);
              }
            }, filterCriteria[0][0].name)));
          }
        }
      };

      for (var _i = 0; _i < categories.length; _i++) {
        _loop2(_i);
      }

      return /*#__PURE__*/React.createElement("ul", null, categoryFilters, /*#__PURE__*/React.createElement("div", {
        style: {
          display: "none"
        },
        id: "extra_categories_params"
      }, moreCategoryFilters, /*#__PURE__*/React.createElement("a", {
        id: "showLessCategories",
        className: "filterExpand cursor-pointer",
        onClick: function onClick() {
          return jbdUtils.showLessParams('extra_categories_params', 'showMoreCategories');
        }
      }, JBD.JText._('LNG_LESS'), " (-)")));
    }
  }, {
    key: "getFacetedFilters",
    value: function getFacetedFilters(categories) {
      var _this2 = this;

      var counterCategories = 0;
      var categoryFilters = [];
      var moreCategoryFilters = [];

      var _loop3 = function _loop3(i) {
        var filterCriteria = categories[i];
        filterCriteria[0]["subCategories"] = Object.values(filterCriteria[0]["subCategories"]);

        if (counterCategories < _this2.props.searchFilterItems) {
          var liClass = '';
          var divClass = '';
          var action = jbdUtils.addFilterRuleCategory;
          var removeText = '';

          if (_this2.props.selectedCategories.some(function (cat) {
            return cat == filterCriteria[0][0].id;
          })) {
            liClass = "selectedlink";
            divClass = "selected";
            action = jbdUtils.removeFilterRuleCategory;
            removeText = /*#__PURE__*/React.createElement("span", {
              className: "cross"
            }, "(remove)");
          }

          var subCategoriesFilters = [];

          if (filterCriteria[0]["subCategories"] != null) {
            var _loop5 = function _loop5(j) {
              var subCategory = filterCriteria[0]["subCategories"][j];
              var liClassSub = '';
              var divClassSub = '';
              var actionSub = jbdUtils.addFilterRuleCategory;
              var removeTextSub = '';

              if (_this2.props.selectedCategories.some(function (cat) {
                return cat == subCategory[0].id;
              })) {
                liClassSub = "selectedlink";
                divClassSub = "selected";
                actionSub = jbdUtils.removeFilterRuleCategory;
                removeTextSub = /*#__PURE__*/React.createElement("span", {
                  className: "cross"
                }, "(remove)");
              }

              subCategoriesFilters.push( /*#__PURE__*/React.createElement("li", {
                className: liClassSub
              }, /*#__PURE__*/React.createElement("div", {
                className: divClassSub
              }, /*#__PURE__*/React.createElement("a", {
                className: "cursor-pointer",
                onClick: function onClick() {
                  return actionSub(subCategory[0].id);
                }
              }, subCategory[0].name, " ", removeText))));
            };

            for (var j = 0; j < filterCriteria[0]["subCategories"].length; j++) {
              _loop5(j);
            }
          }

          categoryFilters.push( /*#__PURE__*/React.createElement("li", {
            key: Math.random() + '-' + i,
            className: liClass
          }, /*#__PURE__*/React.createElement("div", {
            className: divClass
          }, /*#__PURE__*/React.createElement("a", {
            className: "filter-main-cat cursor-pointer",
            onClick: function onClick() {
              return action(filterCriteria[0][0].id);
            }
          }, filterCriteria[0][0].name, " ", removeText)), subCategoriesFilters));
          counterCategories++;
        } else {
          categoryFilters.push( /*#__PURE__*/React.createElement("a", {
            id: "showMoreCategories1",
            className: "filterExpand cursor-pointer",
            onClick: function onClick() {
              return jbdUtils.showMoreParams('extra_categories_params1', 'showMoreCategories1');
            }
          }, JBD.JText._('LNG_MORE'), " (+)"));
          return "break";
        }
      };

      for (var i = 0; i < categories.length; i++) {
        var _ret2 = _loop3(i);

        if (_ret2 === "break") break;
      }

      var _loop4 = function _loop4(_i2) {
        var filterCriteria = categories[_i2];
        counterCategories--;
        filterCriteria[0]["subCategories"] = Object.values(filterCriteria[0]["subCategories"]);

        if (counterCategories < 0) {
          if (filterCriteria[1] > 0) {
            (function () {
              var liClass = '';
              var divClass = '';
              var action = jbdUtils.addFilterRuleCategory;
              var removeText = '';

              if (_this2.props.selectedCategories.some(function (cat) {
                return cat == filterCriteria[0][0].id;
              })) {
                liClass = "selectedlink";
                divClass = "selected";
                action = jbdUtils.removeFilterRuleCategory;
                removeText = /*#__PURE__*/React.createElement("span", {
                  className: "cross"
                }, "(remove)");
              }

              var subCategoriesFilters = [];

              if (filterCriteria[0]["subCategories"] != null) {
                var _loop6 = function _loop6(j) {
                  var subCategory = filterCriteria[0]["subCategories"][j];
                  var liClassSub = '';
                  var divClassSub = '';
                  var actionSub = jbdUtils.addFilterRuleCategory;
                  var removeTextSub = '';

                  if (_this2.props.selectedCategories.some(function (cat) {
                    return cat == subCategory[0].id;
                  })) {
                    liClassSub = "selectedlink";
                    divClassSub = "selected";
                    actionSub = jbdUtils.removeFilterRuleCategory;
                    removeTextSub = /*#__PURE__*/React.createElement("span", {
                      className: "cross"
                    }, "(remove)");
                  }

                  subCategoriesFilters.push( /*#__PURE__*/React.createElement("li", {
                    key: Math.random() + '-' + _i2,
                    className: liClassSub
                  }, /*#__PURE__*/React.createElement("div", {
                    className: divClassSub
                  }, /*#__PURE__*/React.createElement("a", {
                    className: "cursor-pointer",
                    onClick: function onClick() {
                      return action(subCategory[0].id);
                    }
                  }, subCategory[0].name, " ", removeText))));
                };

                for (var j = 0; j < filterCriteria[0]["subCategories"].length; j++) {
                  _loop6(j);
                }
              }

              moreCategoryFilters.push( /*#__PURE__*/React.createElement("li", {
                key: Math.random() + '-' + _i2,
                className: liClass
              }, /*#__PURE__*/React.createElement("div", {
                className: divClass
              }, /*#__PURE__*/React.createElement("a", {
                className: "filter-main-cat cursor-pointer",
                onClick: function onClick() {
                  return action(filterCriteria[0][0].id);
                }
              }, filterCriteria[0][0].name, " ", removeText)), subCategoriesFilters));
            })();
          }
        }
      };

      for (var _i2 = 0; _i2 < categories.length; _i2++) {
        _loop4(_i2);
      }

      return /*#__PURE__*/React.createElement("ul", {
        className: "filter-categories"
      }, categoryFilters, /*#__PURE__*/React.createElement("div", {
        style: {
          display: "none"
        },
        id: "extra_categories_params1"
      }, moreCategoryFilters, /*#__PURE__*/React.createElement("a", {
        id: "showLessCategories1",
        className: "filterExpand cursor-pointer",
        onClick: function onClick() {
          return jbdUtils.showLessParams('extra_categories_params1', 'showMoreCategories1');
        }
      }, JBD.JText._('LNG_LESS'), " (-)")));
    }
  }, {
    key: "render",
    value: function render() {
      var categories = this.props.categories;
      var categoryFilters = '';

      if (this.props.searchType == 0) {
        categoryFilters = this.getRegularFilters(categories);
      } else {
        categoryFilters = this.getFacetedFilters(categories);
      }

      return /*#__PURE__*/React.createElement("div", {
        className: "filter-criteria"
      }, /*#__PURE__*/React.createElement("div", {
        className: "filter-header"
      }, JBD.JText._('LNG_CATEGORIES')), categoryFilters, /*#__PURE__*/React.createElement("div", {
        className: "clear"
      }));
    }
  }]);

  return SearchFilterVerticalCategories;
}(React.Component);
