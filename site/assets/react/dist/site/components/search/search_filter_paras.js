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

var SearchFilterParams = /*#__PURE__*/function (_React$Component) {
  _inherits(SearchFilterParams, _React$Component);

  var _super = _createSuper(SearchFilterParams);

  function SearchFilterParams(props) {
    _classCallCheck(this, SearchFilterParams);

    return _super.call(this, props);
  }

  _createClass(SearchFilterParams, [{
    key: "componentDidMount",
    value: function componentDidMount() {}
  }, {
    key: "render",
    value: function render() {
      var _this = this;

      var showClearFilter = false;
      var showOnlyLocal = typeof this.props.selectedParams['city'] !== 'undefined' ? true : false;
      var showOnlyLocalState = this.props.onlyLocal == 1 ? "checked" : "";
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

      if (this.props.searchKeyword != null && this.props.searchKeyword.length > 0 || this.props.selectedParams['category'] != null && this.props.selectedParams['category'].length > 0 && (this.props.categorySearch == 0 || this.props.categorySearch == null || searchType != "dynamic") || this.props.selectedParams['starRating'] != null && this.props.selectedParams['starRating'].length > 0 || this.props.selectedParams['type'] != null && this.props.selectedParams['type'].length > 0 || this.props.zipCode != null && this.props.zipCode.length > 0 || !jQuery.isEmptyObject(this.props.location) || this.props.selectedParams['package'] != null && this.props.selectedParams['package'].length > 0 || this.props.selectedParams['country'] != null && this.props.selectedParams['country'].length > 0 && (this.props.zipCode == null || searchType != "dynamic") || this.props.selectedParams['province'] != null && this.props.selectedParams['province'].length > 0 && (this.props.zipCode == null || searchType != "dynamic") || this.props.selectedParams['region'] != null && this.props.selectedParams['region'].length > 0 && (this.props.zipCode == null || searchType != "dynamic") || this.props.selectedParams['city'] != null && this.props.selectedParams['city'].length > 0 && (this.props.zipCode == null || searchType != "dynamic") || this.props.selectedParams['area'] != null && this.props.selectedParams['area'].length > 0 && (this.props.zipCode == null || searchType != "dynamic") || this.props.selectedParams['membership'] != null && this.props.selectedParams['membership'].length > 0 || this.props.selectedParams['startDate'] != null && this.props.selectedParams['startDate'].length > 0 || this.props.selectedParams['endDate'] != null && this.props.selectedParams['endDate'].length > 0 || this.props.selectedParams['startTime'] != null && this.props.selectedParams['startTime'].length > 0 || this.props.selectedParams['endTime'] != null && this.props.selectedParams['endTime'].length > 0 || this.props.selectedParams['minPrice'] != null && this.props.selectedParams['minPrice'].length > 0 || this.props.selectedParams['maxPrice'] != null && this.props.selectedParams['maxPrice'].length > 0 || this.props.selectedParams['age'] != null && this.props.selectedParams['age'].length > 0 || this.props.selectedParams['custom-attributes'] != null && this.props.selectedParams['custom-attributes'].length > 0 || this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0) {
        showClearFilter = true;
      }

      return /*#__PURE__*/React.createElement("div", {
        id: "search-filter-source"
      }, showClearFilter == true ? /*#__PURE__*/React.createElement("div", {
        id: "search-path",
        className: "search-filter-params"
      }, showClearFilter == true && this.props.filterType == 2 ? /*#__PURE__*/React.createElement("div", {
        "class": "search-filter-header"
      }, /*#__PURE__*/React.createElement("span", {
        "class": "search-filter-title"
      }, JBD.JText._('LNG_APPLIED_FILTERS')), /*#__PURE__*/React.createElement("span", {
        className: "filter-type-elem reset"
      }, /*#__PURE__*/React.createElement("a", {
        href: "javascript:jbdUtils.resetFilters(true, true)"
      }, JBD.JText._('LNG_CLEAR_ALL_FILTERS'), " ", /*#__PURE__*/React.createElement("i", {
        className: "la la-close"
      })))) : null, /*#__PURE__*/React.createElement("ul", {
        id: "selected-criteria",
        className: "selected-criteria"
      }, this.props.searchKeyword != null ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('keyword');
        }
      }, this.props.searchKeyword, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, this.props.category != null && (this.props.categorySearch == 0 || this.props.categorySearch == null || searchType != "dynamic") ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeFilterRuleCategory(_this.props.category.id);
        }
      }, this.props.category.name, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, !jQuery.isEmptyObject(this.props.searchFilter['types']) && this.props.selectedParams['type'] !== undefined && this.props.selectedParams['type'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeFilterRule('type', _this.props.selectedParams['type'][0]);
        }
      }, this.props.searchFilter['types'][this.props.selectedParams['type'][0]].typeName, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, !jQuery.isEmptyObject(this.props.searchFilter['packages']) && this.props.selectedParams['package'] !== undefined && this.props.selectedParams['package'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeFilterRule('package', _this.props.selectedParams['package'][0]);
        }
      }, this.props.searchFilter['packages'][this.props.selectedParams['package'][0]].package_name, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, !jQuery.isEmptyObject(this.props.searchFilter['starRating']) && this.props.selectedParams['starRating'] !== undefined && this.props.selectedParams['starRating'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeFilterRule('starRating', _this.props.selectedParams['starRating'][0]);
        }
      }, this.props.searchFilter['starRating'][this.props.selectedParams['starRating'][0]].reviewScore, /*#__PURE__*/React.createElement("i", {
        "class": "la la-star"
      }), " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, this.props.searchFilter['countries'] != null && this.props.searchFilter['countries'].length > 0 && this.props.selectedParams['country'] !== undefined && this.props.selectedParams['country'].length > 0 && (this.props.zipCode == null || searchType != "dynamic") ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeFilterRule('country', _this.props.selectedParams['country'][0]);
        }
      }, this.props.searchFilter['countries'][this.props.selectedParams['country'][0]].countryName, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, !jQuery.isEmptyObject(this.props.searchFilter['provinces']) && this.props.selectedParams['province'] !== undefined && this.props.selectedParams['province'].length > 0 && (this.props.zipCode == null || searchType != "dynamic") ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeFilterRule('province', _this.props.selectedParams['province'][0]);
        }
      }, this.props.searchFilter['provinces'][this.props.selectedParams['province'][0]].provinceName, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, !jQuery.isEmptyObject(this.props.searchFilter['regions']) && this.props.selectedParams['region'] !== undefined && this.props.selectedParams['region'].length > 0 && (this.props.zipCode == null || searchType != "dynamic") ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeFilterRule('region', _this.props.selectedParams['region'][0]);
        }
      }, this.props.searchFilter['regions'][this.props.selectedParams['region'][0]].regionName, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, !jQuery.isEmptyObject(this.props.searchFilter['cities']) && this.props.selectedParams['city'] !== undefined && this.props.selectedParams['city'].length > 0 && (this.props.zipCode == null || searchType != "dynamic") ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeFilterRule('city', _this.props.selectedParams['city'][0]);
        }
      }, this.props.searchFilter['cities'][this.props.selectedParams['city'][0]].cityName, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, !jQuery.isEmptyObject(this.props.searchFilter['areas']) && this.props.selectedParams['area'] !== undefined && this.props.selectedParams['area'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeFilterRule('area', _this.props.selectedParams['area'][0]);
        }
      }, this.props.searchFilter['areas'][this.props.selectedParams['area'][0]].areaName, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, !jQuery.isEmptyObject(this.props.searchFilter['memberships']) && this.props.selectedParams['membership'] !== undefined && this.props.selectedParams['membership'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeFilterRule('membership', _this.props.selectedParams['membership'][0]);
        }
      }, this.props.searchFilter['memberships'][this.props.selectedParams['membership'][0]].membership_name, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, !jQuery.isEmptyObject(this.props.searchFilter['attributes']) && this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0 ? /*#__PURE__*/React.createElement("ul", {
        "class": "selected-criteria"
      }, this.props.customAttributesValues.map(function (attribute, index) {
        if (attribute != null) {
          return /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
            className: "filter-type-elem",
            onClick: function onClick() {
              return jbdUtils.removeAttrCond(attribute.attribute_id, attribute.id);
            }
          }, attribute.name, " ", /*#__PURE__*/React.createElement("i", {
            "class": "la la-times"
          })));
        } else {
          return null;
        }
      })) : null, !jQuery.isEmptyObject(this.props.selectedParams['custom-attributes']) && this.props.selectedParams['custom-attributes'] != null && this.props.selectedParams['custom-attributes'].length > 0 ? /*#__PURE__*/React.createElement("ul", {
        "class": "selected-criteria"
      }, this.props.selectedParams['custom-attributes'].map(function (attribute, index) {
        if (attribute != null) {
          Object.keys(attribute).map(function (key, index) {
            //console.debug(attribute[key]);
            return /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
              className: "filter-type-elem",
              onClick: function onClick() {
                return jbdUtils.removeAttrCond(key, key);
              }
            }, attribute[key], " ", /*#__PURE__*/React.createElement("i", {
              "class": "la la-times"
            })));
          });
        } else {
          return null;
        }
      })) : null, this.props.zipCode != null && this.props.zipCode.length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('zipcode');
        }
      }, this.props.zipCode, " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, !jQuery.isEmptyObject(this.props.location) ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('location');
        }
      }, Joomla.JText._('LNG_GEO_LOCATION'), " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, this.props.selectedParams['age'] !== undefined && this.props.selectedParams['age'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('age');
        }
      }, JBD.JText._('LNG_AGE'), " ", this.props.selectedParams['age'], " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, this.props.selectedParams['startTime'] !== undefined && this.props.selectedParams['startTime'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('start-time');
        }
      }, JBD.JText._('LNG_START_TIME'), " ", this.props.selectedParams['startTime'], " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, this.props.selectedParams['endTime'] !== undefined && this.props.selectedParams['endTime'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('end-time');
        }
      }, JBD.JText._('LNG_END_TIME'), " ", this.props.selectedParams['endTime'], " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, this.props.selectedParams['startDate'] !== undefined && this.props.selectedParams['startDate'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('startDate');
        }
      }, JBD.JText._('LNG_START'), " ", this.props.selectedParams['startDate'], " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, this.props.selectedParams['endDate'] !== undefined && this.props.selectedParams['endDate'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('endDate');
        }
      }, JBD.JText._('LNG_END'), " ", this.props.selectedParams['endDate'], " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, this.props.selectedParams['minPrice'] !== undefined && this.props.selectedParams['minPrice'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('minprice');
        }
      }, JBD.JText._('LNG_MIN_PRICE'), " ", this.props.selectedParams['minPrice'], " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, this.props.selectedParams['maxPrice'] !== undefined && this.props.selectedParams['maxPrice'].length > 0 ? /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
        "class": "filter-type-elem",
        onClick: function onClick() {
          return jbdUtils.removeSearchRule('maxprice');
        }
      }, JBD.JText._('LNG_MAX_PRICE'), " ", this.props.selectedParams['maxPrice'], " ", /*#__PURE__*/React.createElement("i", {
        "class": "la la-times"
      }))) : null, showClearFilter == true && this.props.filterType != 2 ? /*#__PURE__*/React.createElement("span", {
        className: "filter-type-elem reset"
      }, /*#__PURE__*/React.createElement("a", {
        href: "javascript:jbdUtils.resetFilters(true, true)"
      }, JBD.JText._('LNG_CLEAR_ALL_FILTERS'))) : null)) : null);
    }
  }]);

  return SearchFilterParams;
}(React.Component);
