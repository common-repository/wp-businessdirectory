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
      //console.debug("render horizontal mount");
      jQuery(".chosen-react").on('change', function (e) {
        var type = jQuery(this).attr('name');
        var val = jQuery(this).chosen().val(); // console.debug(type);
        // console.debug(val);

        if (val) {
          switch (type) {
            case "categories":
              jbdUtils.addFilterRuleCategory(val);
              break;

            case "distance":
              jbdListings.setRadius(val);
              break;

            default:
              jbdUtils.addFilterRule(type, val);
          }
        }
      });
      jQuery(".chosen-react").chosen({
        width: "165px",
        disable_search_threshold: 5,
        inherit_select_classes: true,
        placeholder_text_single: JBD.JText._('LNG_SELECT_OPTION'),
        placeholder_text_multiple: JBD.JText._('LNG_SELECT_OPTION')
      }); // tippy('.local-info', {
      //     content: document.getElementById('local-tooltip'),
      //     trigger: 'click',
      //     placement: 'left',
      //     interactive: true,
      //     onShow(instance) {
      //         instance.popper.querySelector('.close-tooltip').addEventListener('click', () => {
      //         instance.hide();
      //         });
      //     },
      //     onHide(instance) {
      //         instance.popper.querySelector('.close-tooltip').removeEventListener('click', () => {
      //         instance.hide();
      //         });
      //     },
      // });
    }
  }, {
    key: "render",
    value: function render() {
      var _this = this;

      var showClearFilter = false;
      var showOnlyLocal = typeof this.props.selectedParams['city'] !== 'undefined' && this.props.selectedParams['city'].length > 0 ? true : false; // console.debug(this.props.onlyLocal);

      var showOnlyLocalState = this.props.onlyLocal == 1 ? "checked" : ""; // console.debug(showOnlyLocalState);
      // console.debug("render horizontal");

      showOnlyLocal = false;

      if (this.props.searchKeyword != null || this.props.zipCode != null || this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0 || this.props.searchFilter['starRating'] != null && this.props.searchFilter['starRating'].length > 0 || this.props.searchFilter['types'] != null && this.props.searchFilter['types'].length > 0 || this.props.searchFilter['countries'] != null && this.props.searchFilter['countries'].length > 0 || this.props.searchFilter['provinces'] != null && this.props.searchFilter['provinces'].length > 0 || this.props.searchFilter['regions'] != null && this.props.searchFilter['regions'].length > 0 || this.props.searchFilter['cities'] != null && this.props.searchFilter['cities'].length > 0 || this.props.searchFilter['areas'] != null && this.props.searchFilter['areas'].length > 0 || this.props.searchFilter['companies'] != null && this.props.searchFilter['companies'].length > 0 || this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0 || this.props.location != null && this.props.location['latitude'] != null) {
        showClearFilter = false;
      }

      var selectedCategory = null;
      var selectedCategoryName = null;

      if (this.props.category != null) {
        selectedCategory = this.props.category.id;
        selectedCategoryName = this.props.category.name;
      } //disable selection


      selectedCategory = null;
      var cityValueField = "city";
      var regionValueField = "region"; //when the search type is dynamic it will not show the filters for the searched parameters
      // e.g. Searching for category will disable the category filter

      var searchType = "dynamic"; //let searchType = "dynamic";
      //console.debug("zipcode: " + this.props.zipCode);
      // console.debug(this.props.searchFilter['provinces']);
      // console.debug(this.props.searchFilter['provinces'] != null && this.props.searchFilter['provinces'].length > 0);

      return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
        id: "filter-switch-horizontal",
        className: "filter-switch",
        onClick: function onClick() {
          return jbdUtils.toggleHorizontalFilter();
        }
      }, JBD.JText._("LNG_SHOW_FILTER")), /*#__PURE__*/React.createElement("div", {
        id: "search-filter-horizontal",
        className: "search-filter-horizontal"
      }, /*#__PURE__*/React.createElement("div", {
        "class": "search-filter-label"
      }, /*#__PURE__*/React.createElement("i", {
        "class": "icon filters"
      }), " ", JBD.JText._('LNG_FILTERS')), /*#__PURE__*/React.createElement("div", {
        "class": "search-filter-fields"
      }, this.props.searchKeyword != undefined ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("div", {
        className: "jbd-input-box"
      }, /*#__PURE__*/React.createElement("i", {
        className: "icon pencil"
      }), /*#__PURE__*/React.createElement("a", {
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('keyword');
        }
      }, this.props.searchKeyword, " x"))) : null, this.props.searchFilter['categories'] != undefined && (this.props.categorySearch == 0 || this.props.categorySearch == null || searchType != "dynamic") ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("div", {
        className: "jbd-select-box"
      }, /*#__PURE__*/React.createElement("i", {
        className: "la la-list"
      }), /*#__PURE__*/React.createElement("select", {
        name: "categories",
        className: "chosen-react",
        value: selectedCategory,
        onChange: function onChange(e) {
          return jbdUtils.chooseCategory(e.target.value);
        }
      }, selectedCategory != null ? /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("option", {
        value: ""
      }, JBD.JText._("LNG_CATEGORY")), /*#__PURE__*/React.createElement("option", {
        value: selectedCategory
      }, selectedCategoryName)) : /*#__PURE__*/React.createElement("option", {
        value: ""
      }, JBD.JText._("LNG_CATEGORY")), this.props.searchFilter['categories'].map(function (filterCriteria) {
        if (filterCriteria[1] > 0 && filterCriteria[0][0].id != selectedCategory) {
          return /*#__PURE__*/React.createElement("option", {
            value: filterCriteria[0][0].id
          }, filterCriteria[0][0].name);
        } else {
          return null;
        }
      })))) : null, this.props.searchFilter['starRating'] !== undefined ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['starRating'],
        selectedItems: this.props.selectedParams['starRating'],
        title: JBD.JText._('LNG_SELECT_RATING'),
        type: "starRating",
        valueField: "reviewScore",
        nameField: "reviewScore"
      }) : null, this.props.searchFilter['types'] !== undefined ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        fetchData: this.props.fetchData,
        items: this.props.searchFilter['types'],
        selectedItems: this.props.selectedParams['type'],
        title: JBD.JText._('LNG_SELECT_TYPE'),
        type: "type",
        valueField: "typeId",
        nameField: "typeName"
      }) : null, this.props.searchFilter['packages'] !== undefined ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        fetchData: this.props.fetchData,
        items: this.props.searchFilter['packages'],
        selectedItems: this.props.selectedParams['package'],
        title: Joomla.JText._('LNG_PACKAGE'),
        type: "package",
        valueField: "package_id",
        nameField: "package_name"
      }) : null, this.props.searchFilter['countries'] !== undefined && (this.props.zipCode == null || searchType != "dynamic") ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['countries'],
        selectedItems: this.props.selectedParams['country'],
        title: JBD.JText._('LNG_SELECT_COUNTRY'),
        type: "country",
        valueField: "countryId",
        nameField: "countryName"
      }) : null, this.props.searchFilter['provinces'] !== undefined && (this.props.zipCode == null || searchType != "dynamic") ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['provinces'],
        selectedItems: this.props.selectedParams['province'],
        title: JBD.JText._('LNG_PROVINCE'),
        type: "province",
        valueField: "provinceName",
        nameField: "provinceName"
      }) : null, this.props.searchFilter['regions'] !== undefined && (this.props.zipCode == null || searchType != "dynamic") ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['regions'],
        selectedItems: this.props.selectedParams['region'],
        title: JBD.JText._('LNG_SELECT_REGION'),
        type: "region",
        valueField: regionValueField,
        nameField: "regionName"
      }) : null, this.props.searchFilter['cities'] !== undefined && (this.props.zipCode == null || searchType != "dynamic") ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['cities'],
        selectedItems: this.props.selectedParams['city'],
        title: JBD.JText._('LNG_SELECT_CITY'),
        type: "city",
        valueField: cityValueField,
        nameField: "cityName"
      }) : null, this.props.searchFilter['areas'] !== undefined ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['areas'],
        selectedItems: this.props.selectedParams['area'],
        title: JBD.JText._('LNG_SELECT_AREA'),
        type: "area",
        valueField: "areaName",
        nameField: "areaName"
      }) : null, this.props.searchFilter['memberships'] !== undefined ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['memberships'],
        selectedItems: this.props.selectedParams['membership'],
        title: JBD.JText._('LNG_SELECT_MEMBERSHIP'),
        type: "membership",
        valueField: "membership_id",
        nameField: "membership_name"
      }) : null, this.props.searchFilter['attributes'] != undefined ? this.props.searchFilter['attributes'].map(function (items) {
        var item = Object.values(items)[0];
        var nameField = "value"; //console.debug(item["optionName"]);

        if (item["optionName"] != null) {
          nameField = "optionName";
        }

        var type = "attribute_" + item["id"]; //console.debug(type);
        //console.debug(nameField);

        return /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
          items: items,
          selectedItems: _this.props.selectedParams[type],
          title: item["name"],
          type: type,
          valueField: "value",
          nameField: nameField
        });
      }) : null, this.props.searchFilter['companies'] !== undefined ? /*#__PURE__*/React.createElement(SearchFilterHorizontalItems, {
        items: this.props.searchFilter['companies'],
        selectedItems: this.props.selectedParams['company'],
        title: JBD.JText._('LNG_SELECT_COMPANY'),
        type: "company",
        valueField: "companyId",
        nameField: "companyName"
      }) : null, this.props.searchFilter['showDates'] != null && this.props.itemType == JBDConstants.ITEM_TYPE_REQUEST_QUOTE ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("div", {
        className: "jbd-date-box"
      }, /*#__PURE__*/React.createElement("input", {
        type: "date",
        value: this.props.startDate,
        onChange: function onChange(e) {
          return jbdUtils.setFilterDates('startDate', e.target.value);
        }
      }))) : null, this.props.searchFilter['showDates'] != null && this.props.itemType == JBDConstants.ITEM_TYPE_REQUEST_QUOTE ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item"
      }, /*#__PURE__*/React.createElement("div", {
        className: "jbd-date-box"
      }, /*#__PURE__*/React.createElement("input", {
        type: "date",
        value: this.props.endDate,
        onChange: function onChange(e) {
          return jbdUtils.setFilterDates('endDate', e.target.value);
        }
      }))) : null, this.props.location != undefined && this.props.location['latitude'] != undefined ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item radius"
      }, /*#__PURE__*/React.createElement("div", {
        className: "jbd-select-box"
      }, /*#__PURE__*/React.createElement("i", {
        className: "la la-list"
      }), /*#__PURE__*/React.createElement("select", {
        name: "distance",
        className: "chosen-react",
        onChange: function onChange(e) {
          return jbdListings.setRadius(e.target.value);
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
      }, JBD.JText._('LNG_CLEAR'))) : null, showOnlyLocal ? /*#__PURE__*/React.createElement("div", {
        id: "map-location",
        className: "search-options-item"
      }) : null, showOnlyLocal ? /*#__PURE__*/React.createElement("div", {
        className: "search-options-item show-local"
      }, /*#__PURE__*/React.createElement("label", {
        className: "toggle-dir-btn"
      }, /*#__PURE__*/React.createElement("input", {
        type: "checkbox",
        defaultChecked: showOnlyLocalState,
        onChange: function onChange() {
          return jbdUtils.toggleOnlyLocal();
        }
      }), /*#__PURE__*/React.createElement("span", {
        className: "slider"
      }), /*#__PURE__*/React.createElement("span", {
        className: "labels",
        "data-on": JBD.JText._('LNG_SHOW_LOCAL_ON'),
        "data-off": JBD.JText._('LNG_SHOW_LOCAL_OFF')
      })), /*#__PURE__*/React.createElement("i", {
        "class": "local-info icon info-circle",
        "aria-expanded": "false"
      })) : null)));
    }
  }]);

  return SearchFilterHorizontal;
}(React.Component);
