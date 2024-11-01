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

var SearchFilterVertical = /*#__PURE__*/function (_React$Component) {
  _inherits(SearchFilterVertical, _React$Component);

  var _super = _createSuper(SearchFilterVertical);

  function SearchFilterVertical(props) {
    _classCallCheck(this, SearchFilterVertical);

    return _super.call(this, props);
  }

  _createClass(SearchFilterVertical, [{
    key: "getDistanceFilters",
    value: function getDistanceFilters() {
      var _this = this;

      var radiuses = [50, 25, 10, 0];
      var distanceUnit = jbdUtils.getProperty('metric') == 1 ? JBD.JText._('LNG_MILES') : JBD.JText._('LNG_KM');
      return /*#__PURE__*/React.createElement("div", {
        className: "filter-criteria"
      }, /*#__PURE__*/React.createElement("div", {
        className: "filter-header"
      }, JBD.JText._('LNG_DISTANCE')), /*#__PURE__*/React.createElement("ul", null, radiuses.map(function (radius, index) {
        var radiusText = radius + ' ' + distanceUnit;

        if (radius == 0) {
          radiusText = JBD.JText._('LNG_ALL');
        }

        return /*#__PURE__*/React.createElement("li", {
          key: Math.random() + '-' + index
        }, _this.props.radius != radius ? /*#__PURE__*/React.createElement("a", {
          className: "cursor-pointer",
          onClick: function onClick() {
            return jbdListings.setRadius(radius);
          }
        }, radiusText) : /*#__PURE__*/React.createElement("strong", null, radiusText));
      })));
    }
  }, {
    key: "getFilterMonths",
    value: function getFilterMonths() {
      var filterMonths = this.props.filterMonths;
      var startDate = this.props.startDate;

      if (filterMonths == null) {
        return null;
      }

      return /*#__PURE__*/React.createElement("div", {
        className: "filter-criteria"
      }, /*#__PURE__*/React.createElement("div", {
        className: "filter-header"
      }, JBD.JText._('LNG_MONTHS')), /*#__PURE__*/React.createElement("ul", null, filterMonths.map(function (month, index) {
        var liClass = '';
        var divClass = '';
        var removeText = '';
        var action = jbdEvents.setSearchDates;
        var paramStartDate = '';
        var paramEndDate = '';

        if (month.start_date == startDate) {
          action = jbdEvents.setSearchDates;
          liClass = "selectedlink";
          divClass = "selected";
          removeText = /*#__PURE__*/React.createElement("span", {
            className: "cross"
          }, "(remove)");
          paramStartDate = month.start_date;
          paramEndDate = month.end_date;
        }

        return /*#__PURE__*/React.createElement("li", {
          key: Math.random() + '-' + index,
          className: liClass
        }, /*#__PURE__*/React.createElement("div", {
          className: divClass
        }, /*#__PURE__*/React.createElement("a", {
          className: "cursor-pointer",
          onClick: function onClick() {
            return action(paramStartDate, paramEndDate);
          }
        }, month.name, " ", removeText)));
      })));
    }
  }, {
    key: "render",
    value: function render() {
      var searchFilterClasses = ['search-filter'];

      if (jbdUtils.getProperty('search_filter_view') == 2) {
        searchFilterClasses.push('style-2');
      }

      var distanceFilters = '';

      if (this.props.location != null && this.props.location['latitude'] != null) {
        distanceFilters = this.getDistanceFilters();
      }

      var cityValueField = "city";
      var regionValueField = "region";
      var monthFilters = '';
      var searchFilterItems = jbdUtils.getProperty('search_filter_items');
      var searchType = jbdUtils.getProperty('search_type');

      if (this.props.itemType == JBDConstants.ITEM_TYPE_EVENT) {
        cityValueField = "cityName";
        regionValueField = "regionName";
        monthFilters = this.getFilterMonths();
        searchFilterItems = jbdUtils.getProperty('event_search_filter_items');
        searchType = jbdUtils.getProperty('event_search_type');
      } else if (this.props.itemType == JBDConstants.ITEM_TYPE_OFFER) {
        cityValueField = "cityName";
        regionValueField = "regionName";
        searchFilterItems = jbdUtils.getProperty('offer_search_filter_items');
        searchType = jbdUtils.getProperty('offer_search_type');
      }

      return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
        id: "filter-switch",
        className: "filter-switch",
        onClick: function onClick() {
          return jbdUtils.toggleFilter();
        }
      }, JBD.JText._("LNG_SHOW_FILTER")), /*#__PURE__*/React.createElement("div", {
        id: "search-filter",
        className: searchFilterClasses.join(' ')
      }, /*#__PURE__*/React.createElement("div", {
        className: "filter-fav clear",
        style: {
          display: 'none'
        }
      }, "/* TODO is this section needed? */"), /*#__PURE__*/React.createElement("div", {
        className: "search-category-box"
      }, distanceFilters, monthFilters, /*#__PURE__*/React.createElement("div", {
        id: "filterCategoryItems"
      }, this.props.searchFilter != null && this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterVerticalCategories, {
        categories: this.props.searchFilter['categories'],
        category: this.props.category,
        selectedCategories: this.props.selectedCategories,
        searchFilterItems: searchFilterItems,
        searchType: searchType
      }) : null, this.props.searchFilter != null && this.props.searchFilter['starRating'] != null && this.props.searchFilter['starRating'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterVerticalItems, {
        items: this.props.searchFilter['starRating'],
        selectedItems: this.props.selectedParams['starRating'],
        title: JBD.JText._('LNG_STAR_RATING'),
        type: "starRating",
        valueField: "reviewScore",
        nameField: "reviewScore",
        customText: JBD.JText._('LNG_STARS'),
        expandItems: false,
        searchFilterItems: searchFilterItems
      }) : null, this.props.searchFilter != null && this.props.searchFilter['types'] != null && this.props.searchFilter['types'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterVerticalItems, {
        items: this.props.searchFilter['types'],
        selectedItems: this.props.selectedParams['type'],
        title: JBD.JText._('LNG_TYPES'),
        type: "type",
        valueField: "typeId",
        nameField: "typeName",
        expandItems: true,
        showMoreId: "extra_types_params",
        showMoreBtn: "showMoreTypes",
        categoryId: this.props.categoryId,
        category: this.props.category,
        searchFilterItems: searchFilterItems
      }) : null, this.props.searchFilter != null && this.props.searchFilter['countries'] != null && this.props.searchFilter['countries'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterVerticalItems, {
        items: this.props.searchFilter['countries'],
        selectedItems: this.props.selectedParams['country'],
        title: JBD.JText._('LNG_COUNTRIES'),
        type: "country",
        valueField: "countryId",
        nameField: "countryName",
        expandItems: true,
        showMoreId: "extra_countries_params",
        showMoreBtn: "showMoreCountries",
        searchFilterItems: searchFilterItems
      }) : null, this.props.searchFilter != null && this.props.searchFilter['regions'] != null && this.props.searchFilter['regions'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterVerticalItems, {
        items: this.props.searchFilter['regions'],
        selectedItems: this.props.selectedParams['region'],
        title: JBD.JText._('LNG_REGIONS'),
        type: "region",
        valueField: regionValueField,
        nameField: "regionName",
        expandItems: true,
        showMoreId: "extra_regions_params",
        showMoreBtn: "showMoreRegions",
        categoryId: this.props.categoryId,
        category: this.props.category,
        searchFilterItems: searchFilterItems
      }) : null, this.props.searchFilter != null && this.props.searchFilter['cities'] != null && this.props.searchFilter['cities'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterVerticalItems, {
        items: this.props.searchFilter['cities'],
        selectedItems: this.props.selectedParams['city'],
        title: JBD.JText._('LNG_CITIES'),
        type: "city",
        valueField: cityValueField,
        nameField: "cityName",
        expandItems: true,
        showMoreId: "extra_cities_params",
        showMoreBtn: "showMoreCities",
        categoryId: this.props.categoryId,
        category: this.props.category,
        searchFilterItems: searchFilterItems
      }) : null, this.props.searchFilter != null && this.props.searchFilter['areas'] != null && this.props.searchFilter['areas'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterVerticalItems, {
        items: this.props.searchFilter['areas'],
        selectedItems: this.props.selectedParams['area'],
        title: JBD.JText._('LNG_AREA'),
        type: "area",
        valueField: "areaName",
        nameField: "areaName",
        expandItems: true,
        showMoreId: "extra_areas_params",
        showMoreBtn: "showMoreAreas",
        categoryId: this.props.categoryId,
        category: this.props.category,
        searchFilterItems: searchFilterItems
      }) : null, this.props.searchFilter != null && this.props.searchFilter['provinces'] != null && this.props.searchFilter['provinces'].length > 0 ? /*#__PURE__*/React.createElement(SearchFilterVerticalItems, {
        items: this.props.searchFilter['provinces'],
        selectedItems: this.props.selectedParams['province'],
        title: JBD.JText._('LNG_PROVINCE'),
        type: "province",
        valueField: "provinceName",
        nameField: "provinceName",
        expandItems: true,
        showMoreId: "extra_provinces_params",
        showMoreBtn: "showMoreProvinces",
        searchFilterItems: searchFilterItems
      }) : null))));
    }
  }]);

  return SearchFilterVertical;
}(React.Component);
