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

var SearchFilterHorizontal = /*#__PURE__*/function (_React$Component) {
  _inherits(SearchFilterHorizontal, _React$Component);

  var _super = _createSuper(SearchFilterHorizontal);

  function SearchFilterHorizontal(props) {
    _classCallCheck(this, SearchFilterHorizontal);

    return _super.call(this, props);
  }

  _createClass(SearchFilterHorizontal, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      jQuery(".chosen-react").on('change', function (e) {
        var type = jQuery(this).attr('name');
        var val = jQuery(this).chosen().val();

        switch (type) {
          case "categories":
            jbdUtils.chooseCategory(val);
            break;

          case "distance":
            jbdListings.setRadius(val);
            break;

          default:
            jbdUtils.addFilterRule(type, val);
        }
      });
      jQuery(".chosen-react").chosen({
        width: "155px",
        disable_search_threshold: 5,
        inherit_select_classes: true,
        placeholder_text_single: JBD.JText._('LNG_SELECT_OPTION'),
        placeholder_text_multiple: JBD.JText._('LNG_SELECT_OPTION')
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this = this;

      var showClearFilter = false;

      if (this.props.searchKeyword != null || this.props.zipCode != null || this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0 || this.props.searchFilter['starRating'] != null && this.props.searchFilter['starRating'].length > 0 || this.props.searchFilter['types'] != null && this.props.searchFilter['types'].length > 0 || this.props.searchFilter['countries'] != null && this.props.searchFilter['countries'].length > 0 || this.props.searchFilter['regions'] != null && this.props.searchFilter['regions'].length > 0 || this.props.searchFilter['cities'] != null && this.props.searchFilter['cities'].length > 0 || this.props.searchFilter['areas'] != null && this.props.searchFilter['areas'].length > 0 || this.props.searchFilter['provinces'] != null && this.props.searchFilter['provinces'].length > 0 || this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0 || this.props.location != null && this.props.location['latitude'] != null) {
        showClearFilter = true;
      }

      var selectedCategory = null;

      if (this.props.category != null) {
        selectedCategory = this.props.category.id;
      }

      var cityValueField = "city";
      var regionValueField = "region";
      return /*#__PURE__*/React.createElement("div", {
        id: "search-filter-horizontal",
        className: "search-filter-horizontal"
      }, this.props.searchKeyword != null ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("div", {
        className: "jbd-input-box"
      }, /*#__PURE__*/React.createElement("i", {
        className: "la la-pencil"
      }), /*#__PURE__*/React.createElement("a", {
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('keyword');
        }
      }, this.props.searchKeyword, " x"))) : null, this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0 ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("div", {
        className: "jbd-select-box"
      }, /*#__PURE__*/React.createElement("i", {
        className: "la la-list"
      }), /*#__PURE__*/React.createElement("select", {
        name: "categories",
        className: "chosen-react",
        value: selectedCategory,
        onChange: function onChange() {
          return jbdUtils.chooseCategory(_this.value);
        }
      }, /*#__PURE__*/React.createElement("option", {
        value: ""
      }, JBD.JText._('LNG_CATEGORY')), this.props.searchFilter['categories'].map(function (filterCriteria) {
        if (filterCriteria[1] > 0) {
          return /*#__PURE__*/React.createElement("option", {
            value: filterCriteria[0][0].id
          }, filterCriteria[0][0].name);
        } else {
          return null;
        }
      })))) : null, this.props.searchFilter['starRating'] != null && this.props.searchFilter['starRating'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['starRating'],
        selectedItems: this.props.selectedParams['starRating'],
        title: JBD.JText._('LNG_SELECT_RATING'),
        type: "starRating",
        valueField: "reviewScore",
        nameField: "reviewScore"
      }) : null, this.props.searchFilter['types'] != null && this.props.searchFilter['types'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['types'],
        selectedItems: this.props.selectedParams['type'],
        title: JBD.JText._('LNG_SELECT_TYPE'),
        type: "type",
        valueField: "typeId",
        nameField: "typeName"
      }) : null, this.props.searchFilter['countries'] != null && this.props.searchFilter['countries'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['countries'],
        selectedItems: this.props.selectedParams['country'],
        title: JBD.JText._('LNG_SELECT_COUNTRY'),
        type: "country",
        valueField: "countryId",
        nameField: "countryName"
      }) : null, this.props.searchFilter['regions'] != null && this.props.searchFilter['regions'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['regions'],
        selectedItems: this.props.selectedParams['region'],
        title: JBD.JText._('LNG_SELECT_REGION'),
        type: "region",
        valueField: regionValueField,
        nameField: "regionName"
      }) : null, this.props.searchFilter['cities'] != null && this.props.searchFilter['cities'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['cities'],
        selectedItems: this.props.selectedParams['city'],
        title: JBD.JText._('LNG_SELECT_CITY'),
        type: "city",
        valueField: cityValueField,
        nameField: "cityName"
      }) : null, this.props.searchFilter['areas'] != null && this.props.searchFilter['areas'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['areas'],
        selectedItems: this.props.selectedParams['area'],
        title: JBD.JText._('LNG_SELECT_AREA'),
        type: "area",
        valueField: "areaName",
        nameField: "areaName"
      }) : null, this.props.searchFilter['provinces'] != null && this.props.searchFilter['provinces'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['provinces'],
        selectedItems: this.props.selectedParams['province'],
        title: JBD.JText._('LNG_PROVINCE'),
        type: "province",
        valueField: "provinceName",
        nameField: "provinceName"
      }) : null, this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0 ? /*#__PURE__*/React.createElement("span", null, this.props.customAttributesValues.map(function (attribute, index) {
        if (attribute != null) {
          return /*#__PURE__*/React.createElement("div", {
            className: "search-options-item",
            key: index
          }, /*#__PURE__*/React.createElement("div", {
            className: "jbd-input-box"
          }, /*#__PURE__*/React.createElement("a", {
            className: "filter-type-elem",
            onClick: function onClick() {
              return jbdUtils.removeAttrCond(attribute.attribute_id);
            }
          }, attribute.name, " x")));
        } else {
          return null;
        }
      })) : null, this.props.zipCode != null ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("div", {
        className: "jbd-input-box"
      }, /*#__PURE__*/React.createElement("i", {
        className: "la la-map-marker"
      }), /*#__PURE__*/React.createElement("a", {
        className: "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('zipcode');
        }
      }, this.props.zipCode, " x"))) : null, this.props.location != null && this.props.location['latitude'] != null ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("div", {
        className: "jbd-select-box"
      }, /*#__PURE__*/React.createElement("i", {
        className: "la la-list"
      }), /*#__PURE__*/React.createElement("select", {
        name: "distance",
        className: "chosen-react",
        onChange: function onChange() {
          return jbdListings.setRadius(_this.value);
        }
      }, /*#__PURE__*/React.createElement("option", {
        value: "0"
      }, JBD.JText._('LNG_RADIUS')), /*#__PURE__*/React.createElement("option", {
        value: "10"
      }, "10"), /*#__PURE__*/React.createElement("option", {
        value: "25"
      }, "25"), /*#__PURE__*/React.createElement("option", {
        value: "50"
      }, "50")))) : null, showClearFilter ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("a", {
        className: "clear-search cursor-pointer",
        onClick: function onClick() {
          return jbdUtils.resetFilters(true, true);
        },
        style: {
          textDecoration: "none"
        }
      }, /*#__PURE__*/React.createElement("i", {
        className: "la la-close"
      }))) : null);
    }
  }]);

  return SearchFilterHorizontal;
}(React.Component);
