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

var SearchFilterHorizontalCatItems = /*#__PURE__*/function (_React$Component) {
  _inherits(SearchFilterHorizontalCatItems, _React$Component);

  var _super = _createSuper(SearchFilterHorizontalCatItems);

  function SearchFilterHorizontalCatItems(props) {
    _classCallCheck(this, SearchFilterHorizontalCatItems);

    return _super.call(this, props);
  }

  _createClass(SearchFilterHorizontalCatItems, [{
    key: "render",
    value: function render() {
      var nameField = this.props.nameField;
      var valueField = this.props.valueField;
      var selectedItems = typeof this.props.selectedItems !== "undefined" ? this.props.selectedItems : null;
      var type = this.props.type;
      var title = this.props.title;
      var items = Object.values(this.props.items);
      var selectedItem = null;

      if (selectedItems != null) {
        selectedItem = selectedItems[0];
      }

      var liClassSub = ""; // console.debug("selected items " + selectedItems);

      var values = [];

      if (selectedItems) {
        if (selectedItems.toString().indexOf(",") != -1) {
          values = selectedItems.toString().split(",").map(Number);
        } else {
          values = [parseInt(selectedItems)];
        }
      }

      var addFilterAction = jbdUtils.addFilterRule;
      var removeFilterAction = jbdUtils.removeFilterRule;
      return /*#__PURE__*/React.createElement("li", {
        key: Math.random()
      }, /*#__PURE__*/React.createElement("div", {
        className: "main-cat-container"
      }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
        className: "filter-main-cat cursor-pointer"
      }, title)), /*#__PURE__*/React.createElement("i", {
        className: "icon"
      })), /*#__PURE__*/React.createElement("ul", {
        className: "submenu",
        key: 'horizontal-' + type
      }, items.map(function (item) {
        if (item[valueField] != null) {
          var action = addFilterAction;
          var itemValue = parseInt(item[valueField]);

          if (values.includes(itemValue)) {
            action = removeFilterAction;
          }

          return /*#__PURE__*/React.createElement("li", {
            key: Math.random(),
            className: liClassSub
          }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("input", {
            className: "cursor-pointer",
            name: "cat",
            type: "checkbox",
            checked: values.includes(itemValue),
            onChange: function onChange() {
              return action(type, item[valueField], true);
            }
          }), " \xA0", /*#__PURE__*/React.createElement("a", {
            className: "cursor-pointer",
            onClick: function onClick() {
              return action(type, item[valueField], true);
            }
          }, item[nameField])));
        }
      })));
    }
  }]);

  return SearchFilterHorizontalCatItems;
}(React.Component);
