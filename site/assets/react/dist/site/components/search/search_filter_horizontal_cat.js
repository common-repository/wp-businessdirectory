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

var SearchFilterHorizontalCat = /*#__PURE__*/function (_React$Component) {
  _inherits(SearchFilterHorizontalCat, _React$Component);

  var _super = _createSuper(SearchFilterHorizontalCat);

  function SearchFilterHorizontalCat(props) {
    _classCallCheck(this, SearchFilterHorizontalCat);

    return _super.call(this, props);
  }

  _createClass(SearchFilterHorizontalCat, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      jQuery(".chosen-react").on('change', function (e) {
        var type = jQuery(this).attr('name');
        var val = jQuery(this).chosen().val();

        switch (type) {
          case "categories":
            jbdUtils.chooseCategory(val);
            break;

          default:
            jbdUtils.addFilterRule(type, val);
        }
      });
      jQuery(".filter-categories i.icon").click(function (e) {
        $hasOpenClass = jQuery(this).parent().parent().hasClass('open');
        jQuery(".filter-categories li").removeClass('open');

        if (!$hasOpenClass) {
          jQuery(this).parent().parent().toggleClass("open");
        }

        e.stopPropagation();
      });
      jQuery(".filter-main-cat").click(function (e) {
        $hasOpenClass = jQuery(this).parent().parent().parent().hasClass('open');
        jQuery(".filter-categories li").removeClass('open');

        if (!$hasOpenClass) {
          jQuery(this).parent().parent().parent().toggleClass("open");
        }

        e.stopPropagation();
      });
      jQuery("body").click(function (e) {
        jQuery(".filter-categories li").removeClass('open');
      });
    }
  }, {
    key: "getCategoryFilters",
    value: function getCategoryFilters(categories) {
      var _this = this;

      var counterCategories = 0;
      var categoryFilters = [];

      var _loop = function _loop(i) {
        var filterCriteria = categories[i];
        filterCriteria[0]["subCategories"] = Object.values(filterCriteria[0]["subCategories"]);

        if (counterCategories < 100) {
          var liClass = '';
          var divClass = '';
          var action = jbdUtils.addFilterRuleCategory;
          var removeText = '';
          var checkedMain = false;

          if (_this.props.selectedCategories.some(function (cat) {
            return cat == filterCriteria[0][0].id;
          })) {
            liClass = "selectedlink";
            divClass = "selected";
            action = jbdUtils.removeFilterRuleCategory;
            removeText = /*#__PURE__*/React.createElement("span", {
              className: "cross"
            });
            checkedMain = true;
          }

          var subCategoriesFilters = [];

          if (filterCriteria[0]["subCategories"] != null) {
            var _loop2 = function _loop2(j) {
              var subCategory = filterCriteria[0]["subCategories"][j];
              var liClassSub = '';
              var divClassSub = '';
              var actionSub = jbdUtils.addFilterRuleCategory;
              var removeTextSub = '';
              var checked = false;

              if (_this.props.selectedCategories.some(function (cat) {
                return cat == subCategory[0].id;
              })) {
                liClassSub = "selectedlink";
                divClassSub = "selected";
                actionSub = jbdUtils.removeFilterRuleCategory;
                removeTextSub = /*#__PURE__*/React.createElement("span", {
                  className: "cross"
                });
                checked = true;
              }

              subCategoriesFilters.push( /*#__PURE__*/React.createElement("li", {
                key: Math.random() + '-' + i,
                className: liClassSub
              }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("input", {
                className: "cursor-pointer",
                name: "cat",
                type: "checkbox",
                checked: checked,
                onChange: function onChange() {
                  return actionSub(subCategory[0].id);
                }
              }), " \xA0", /*#__PURE__*/React.createElement("a", {
                className: "cursor-pointer",
                onClick: function onClick() {
                  return actionSub(subCategory[0].id);
                }
              }, subCategory[0].name, " ", removeTextSub))));
            };

            for (var j = 0; j < filterCriteria[0]["subCategories"].length; j++) {
              _loop2(j);
            }
          }

          categoryFilters.push( /*#__PURE__*/React.createElement("li", {
            key: Math.random() + '-' + i,
            className: "multi-column"
          }, /*#__PURE__*/React.createElement("div", {
            className: "main-cat-container"
          }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
            className: "filter-main-cat cursor-pointer"
          }, filterCriteria[0][0].name)), /*#__PURE__*/React.createElement("i", {
            className: "icon"
          })), /*#__PURE__*/React.createElement("ul", {
            className: "submenu"
          }, /*#__PURE__*/React.createElement("li", {
            key: Math.random() + '-' + i
          }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("input", {
            className: "cursor-pointer",
            name: "cat",
            type: "checkbox",
            checked: checkedMain,
            onChange: function onChange() {
              return action(filterCriteria[0][0].id);
            }
          }), " \xA0", /*#__PURE__*/React.createElement("a", {
            className: "cursor-pointer",
            onClick: function onClick() {
              return action(filterCriteria[0][0].id);
            }
          }, filterCriteria[0][0].name))), subCategoriesFilters)));
          counterCategories++;
        }
      };

      for (var i = 0; i < categories.length; i++) {
        _loop(i);
      }

      return categoryFilters;
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var showClearFilter = false;
      var categoriesFilter = "";

      if (this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0) {
        categoriesFilter = this.getCategoryFilters(this.props.searchFilter['categories']);
      }

      return /*#__PURE__*/React.createElement("div", {
        id: "category-filter-horizontal",
        className: "category-filter-horizontal"
      }, /*#__PURE__*/React.createElement("ul", {
        key: Math.random() * 100,
        className: "filter-categories"
      }, this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0 ? this.getCategoryFilters(this.props.searchFilter['categories']) : null, this.props.searchFilter['memberships'] != null && this.props.searchFilter['memberships'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterHorizontalCatItems, {
        items: this.props.searchFilter['memberships'],
        selectedItems: this.props.selectedParams['membership'],
        title: JBD.JText._('LNG_SELECT_MEMBERSHIP'),
        type: "membership",
        valueField: "membership_id",
        nameField: "membership_name"
      }) : null, this.props.searchFilter != null && this.props.searchFilter['attributes'] != null && this.props.searchFilter['attributes'].length > 0 ? this.props.searchFilter['attributes'].map(function (items) {
        var item = Object.values(items)[0];
        var nameField = "value"; //console.debug(item["optionName"]);

        if (item["optionName"] != null) {
          nameField = "optionName";
        }

        var type = "attribute_" + item["id"]; //console.debug(type);
        //console.debug(nameField);

        return /*#__PURE__*/React.createElement(SearchFilterHorizontalCatItems, {
          items: items,
          selectedItems: _this2.props.selectedParams[type],
          title: item["name"],
          type: type,
          valueField: "value",
          nameField: nameField
        });
      }) : null), showClearFilter ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("a", {
        className: "clear-search cursor-pointer",
        onClick: function onClick() {
          return jbdUtils.resetFilters(true, true);
        },
        style: {
          textDecoration: "none"
        }
      }, JBD.JText._('LNG_CLEAR'))) : null);
    }
  }]);

  return SearchFilterHorizontalCat;
}(React.Component);
