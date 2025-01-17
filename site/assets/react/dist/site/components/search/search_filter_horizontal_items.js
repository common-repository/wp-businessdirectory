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

var SearchFilterHorizontalItems = /*#__PURE__*/function (_React$Component) {
  _inherits(SearchFilterHorizontalItems, _React$Component);

  var _super = _createSuper(SearchFilterHorizontalItems);

  function SearchFilterHorizontalItems(props) {
    var _this;

    _classCallCheck(this, SearchFilterHorizontalItems);

    _this = _super.call(this, props);
    _this.changeHandler = _this.changeHandler.bind(_assertThisInitialized(_this));
    return _this;
  }

  _createClass(SearchFilterHorizontalItems, [{
    key: "changeHandler",
    value: function changeHandler(e) {
      console.debug("Change performed");
      jbdUtils.addFilterRule(this.props.type, e.target.value, e.target.options[e.target.selectedIndex].text); //this.props.fetchData();
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var nameField = this.props.nameField;
      var valueField = this.props.valueField;
      var selectedItems = typeof this.props.selectedItems !== "undefined" ? this.props.selectedItems : null;
      var type = this.props.type;
      var title = this.props.title; // console.debug(nameField);
      // console.debug(this.props.items);

      var itemDisabled = false;

      if (jQuery.isEmptyObject(this.props.items)) {
        itemDisabled = true;
      }

      var items = Object.values(this.props.items);
      var selectedItem = null;

      if (selectedItems != null) {//selectedItem = selectedItems[0];
      }

      return /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("div", {
        className: "jbd-select-box"
      }, /*#__PURE__*/React.createElement("i", {
        className: "la la-list"
      }), /*#__PURE__*/React.createElement("select", {
        name: type,
        className: "chosen-react",
        value: selectedItem,
        key: 'horizontal-' + type,
        disabled: itemDisabled,
        onChange: function onChange(e) {
          return _this2.changeHandler(e);
        }
      }, /*#__PURE__*/React.createElement("option", {
        value: ""
      }, title), items.map(function (item) {
        return /*#__PURE__*/React.createElement("option", {
          className: type + "-" + item[valueField],
          value: item[valueField]
        }, item[nameField]);
      }))));
    }
  }]);

  return SearchFilterHorizontalItems;
}(React.Component);
