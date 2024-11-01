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

var SearchFilter = /*#__PURE__*/function (_React$Component) {
  _inherits(SearchFilter, _React$Component);

  var _super = _createSuper(SearchFilter);

  function SearchFilter(props) {
    var _this;

    _classCallCheck(this, SearchFilter);

    _this = _super.call(this, props);
    _this.state = {
      radius: null,
      location: null,
      searchFilter: [],
      category: null,
      categoryId: null,
      selectedCategories: [],
      selectedParams: [],
      filterMonths: null,
      startDate: null,
      searchKeyword: null,
      customAttributesValues: null,
      zipCode: null,
      err: null,
      isLoading: false
    };
    return _this;
  }

  _createClass(SearchFilter, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;

      this.setState({
        isLoading: true
      });
      var url = jbdUtils.getAjaxUrl('getSearchFilter', 'search');

      if (this.props.itemType == JBDConstants.ITEM_TYPE_EVENT) {
        url = jbdUtils.getAjaxUrl('getSearchFilter', 'events');
      } else if (this.props.itemType == JBDConstants.ITEM_TYPE_OFFER) {
        url = jbdUtils.getAjaxUrl('getSearchFilter', 'offers');
      }

      fetch(url).then(function (res) {
        if (res.status >= 400) {
          throw new Error("Server responded with error!");
        }

        return res.json();
      }).then(function (response) {
        var searchFilter = null;

        if (response.data.searchFilter != null) {
          searchFilter = [];

          for (var key in response.data.searchFilter) {
            if (response.data.searchFilter.hasOwnProperty(key)) {
              searchFilter[key] = Object.values(response.data.searchFilter[key]);
            }
          }
        }

        _this2.setState({
          radius: response.data.radius,
          location: response.data.location,
          searchFilter: searchFilter,
          category: response.data.category,
          categoryId: typeof response.data.categoryId !== 'undefined' ? response.data.categoryId : null,
          selectedCategories: response.data.selectedCategories,
          selectedParams: response.data.selectedParams,
          filterMonths: typeof response.data.filterMonths !== 'undefined' ? response.data.filterMonths : null,
          startDate: typeof response.data.startDate !== 'undefined' ? response.data.startDate : null,
          searchKeyword: typeof response.data.searchKeyword !== 'undefined' ? response.data.searchKeyword : null,
          customAttributesValues: typeof response.data.customAttributesValues !== 'undefined' ? response.data.customAttributesValues : null,
          zipCode: typeof response.data.zipCode !== 'undefined' ? response.data.zipCode : null,
          isLoading: false
        });
      }, function (err) {
        _this2.setState({
          err: err,
          isLoading: false
        });
      });
    }
  }, {
    key: "render",
    value: function render() {
      if (this.state.isLoading) {
        return /*#__PURE__*/React.createElement(Loading, null);
      } else {
        return /*#__PURE__*/React.createElement("div", null, jbdUtils.getProperty('search_filter_type') == 2 || this.props.itemType != 1 ? /*#__PURE__*/React.createElement(SearchFilterVertical, {
          radius: this.state.radius,
          location: this.state.location,
          searchFilter: this.state.searchFilter,
          category: this.state.category,
          categoryId: this.state.categoryId,
          selectedCategories: this.state.selectedCategories,
          selectedParams: this.state.selectedParams,
          filterMonths: this.state.filterMonths,
          startDate: this.state.startDate,
          itemType: this.props.itemType
        }) : /*#__PURE__*/React.createElement(SearchFilterHorizontal, {
          searchKeyword: this.state.searchKeyword,
          radius: this.state.radius,
          location: this.state.location,
          searchFilter: this.state.searchFilter,
          category: this.state.category,
          categoryId: this.state.categoryId,
          selectedCategories: this.state.selectedCategories,
          selectedParams: this.state.selectedParams,
          customAttributesValues: this.state.customAttributesValues,
          zipCode: this.state.zipCode,
          itemType: this.props.itemType
        }));
      }
    }
  }]);

  return SearchFilter;
}(React.Component);
