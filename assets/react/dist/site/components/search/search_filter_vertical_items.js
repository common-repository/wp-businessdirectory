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

var SearchFilterVerticalItems = /*#__PURE__*/function (_React$Component) {
  _inherits(SearchFilterVerticalItems, _React$Component);

  var _super = _createSuper(SearchFilterVerticalItems);

  function SearchFilterVerticalItems(props) {
    _classCallCheck(this, SearchFilterVerticalItems);

    return _super.call(this, props);
  }

  _createClass(SearchFilterVerticalItems, [{
    key: "getFilters",
    value: function getFilters(items) {
      var nameField = this.props.nameField;
      var valueField = this.props.valueField;
      var selectedItems = this.props.selectedItems;
      var customText = this.props.customText;
      var type = this.props.type;
      items = Object.values(items);
      var setCategory = typeof this.props.category !== 'undefined' && this.props.category != null ? 1 : 0;
      var categId = typeof this.props.categoryId !== 'undefined' && this.props.categoryId != null ? this.props.categoryId : 0;
      var addFilterAction = typeof this.props.addFilterAction !== 'undefined' ? this.props.addFilterAction : jbdUtils.addFilterRule;
      var removeFilterAction = typeof this.props.removeFilterAction !== 'undefined' ? this.props.removeFilterAction : jbdUtils.removeFilterRule;
      return /*#__PURE__*/React.createElement("span", null, items.map(function (item, index) {
        if (item[valueField] != null) {
          var liClass = '';
          var divClass = '';
          var action = addFilterAction;
          var removeText = '';

          if (selectedItems != null && selectedItems.some(function (selectedItem) {
            return selectedItem == item[valueField];
          })) {
            liClass = "selectedlink";
            divClass = "selected";
            action = removeFilterAction;
            removeText = /*#__PURE__*/React.createElement("span", {
              className: "cross"
            }, "(remove)");
          }

          return /*#__PURE__*/React.createElement("li", {
            key: index,
            className: liClass
          }, /*#__PURE__*/React.createElement("div", {
            className: divClass
          }, /*#__PURE__*/React.createElement("a", {
            className: "cursor-pointer",
            onClick: function onClick() {
              return action(type, item[valueField], setCategory, categId);
            }
          }, item[nameField], " ", customText, " ", removeText)));
        }
      }));
    }
  }, {
    key: "getExpandedFilters",
    value: function getExpandedFilters() {
      var items = this.props.items;
      var showMoreBtn = this.props.showMoreBtn;
      var showMoreId = this.props.showMoreId;
      items = Object.values(items);
      var result = [];
      var filters = '';
      var moreFilters = '';
      var counterItems = 0;
      var visibleItems = [];
      var hiddenItems = [];

      for (var i = 0; i < items.length; i++) {
        var item = items[i];

        if (counterItems < this.props.searchFilterItems) {
          visibleItems.push(item);
        } else {
          hiddenItems.push(item);
        }

        counterItems++;
      }

      filters = this.getFilters(visibleItems);
      result.push(filters);

      if (hiddenItems.length > 0) {
        moreFilters = this.getFilters(hiddenItems);
        result.push( /*#__PURE__*/React.createElement("a", {
          id: showMoreBtn,
          className: "filterExpand cursor-pointer",
          onClick: function onClick() {
            return jbdUtils.showMoreParams(showMoreId, showMoreBtn);
          }
        }, JBD.JText._('LNG_MORE'), " (+)"));
        result.push( /*#__PURE__*/React.createElement("div", {
          style: {
            display: "none"
          },
          id: showMoreId
        }, moreFilters, /*#__PURE__*/React.createElement("a", {
          id: showMoreBtn,
          className: "filterExpand cursor-pointer",
          onClick: function onClick() {
            return jbdUtils.showLessParams(showMoreId, showMoreBtn);
          }
        }, JBD.JText._('LNG_LESS'), " (-)")));
      }

      return result;
    }
  }, {
    key: "render",
    value: function render() {
      var items = this.props.items;
      var title = this.props.title;
      var expandItems = this.props.expandItems;
      var filters = '';

      if (expandItems) {
        filters = this.getExpandedFilters(items);
      } else {
        filters = this.getFilters(items);
      }

      return /*#__PURE__*/React.createElement("div", {
        className: "filter-criteria"
      }, /*#__PURE__*/React.createElement("div", {
        className: "filter-header"
      }, title), /*#__PURE__*/React.createElement("ul", null, filters), /*#__PURE__*/React.createElement("div", {
        className: "clear"
      }));
    }
  }]);

  return SearchFilterVerticalItems;
}(React.Component);
