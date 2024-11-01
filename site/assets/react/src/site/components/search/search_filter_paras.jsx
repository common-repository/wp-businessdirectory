class SearchFilterParams extends React.Component {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
       
    }

    render() {

        let showClearFilter = false;
        let showOnlyLocal = typeof this.props.selectedParams['city'] !== 'undefined' ? true: false;
        let showOnlyLocalState = this.props.onlyLocal == 1 ? "checked" : "";

        let selectedCategory = null;
        let selectedCategoryName = null;
        if (this.props.category != null) {
            selectedCategory = this.props.category.id;
            selectedCategoryName = this.props.category.name;
        }

        //disable selection
        selectedCategory = null;

        let cityValueField = "city";
        let regionValueField = "region";

        //when the search type is dynamic it will not show the filters for the searched parameters
        // e.g. Searching for category will disable the category filter
        let searchType = "dynamic";
        //let searchType = "dynamic";

        if (
            (this.props.searchKeyword != null && this.props.searchKeyword.length > 0) ||
            (this.props.selectedParams['category'] != null && this.props.selectedParams['category'].length > 0 && (this.props.categorySearch == 0 || this.props.categorySearch == null || searchType!="dynamic") ) ||
            (this.props.selectedParams['starRating'] != null && this.props.selectedParams['starRating'].length > 0) ||
            (this.props.selectedParams['type'] != null && this.props.selectedParams['type'].length > 0) ||
            (this.props.zipCode != null && this.props.zipCode.length > 0) ||
            (!jQuery.isEmptyObject(this.props.location)) ||
            (this.props.selectedParams['package'] != null && this.props.selectedParams['package'].length > 0) ||
            (this.props.selectedParams['country'] != null && this.props.selectedParams['country'].length > 0 && (this.props.zipCode == null || searchType!="dynamic")) ||
            (this.props.selectedParams['province'] != null && this.props.selectedParams['province'].length > 0 && (this.props.zipCode == null || searchType!="dynamic")) ||
            (this.props.selectedParams['region'] != null && this.props.selectedParams['region'].length > 0 && (this.props.zipCode == null || searchType!="dynamic")) ||
            (this.props.selectedParams['city'] != null && this.props.selectedParams['city'].length > 0 && (this.props.zipCode == null || searchType!="dynamic")) ||
            (this.props.selectedParams['area'] != null && this.props.selectedParams['area'].length > 0 && (this.props.zipCode == null || searchType!="dynamic")) ||
            (this.props.selectedParams['membership'] != null && this.props.selectedParams['membership'].length > 0) ||
            (this.props.selectedParams['startDate'] != null && this.props.selectedParams['startDate'].length > 0) ||
            (this.props.selectedParams['endDate'] != null && this.props.selectedParams['endDate'].length > 0) ||
            (this.props.selectedParams['startTime'] != null && this.props.selectedParams['startTime'].length > 0) ||
            (this.props.selectedParams['endTime'] != null && this.props.selectedParams['endTime'].length > 0) ||
            (this.props.selectedParams['minPrice'] != null && this.props.selectedParams['minPrice'].length > 0) ||
            (this.props.selectedParams['maxPrice'] != null && this.props.selectedParams['maxPrice'].length > 0) ||
            (this.props.selectedParams['age'] != null && this.props.selectedParams['age'].length > 0) ||
            (this.props.selectedParams['custom-attributes'] != null && this.props.selectedParams['custom-attributes'].length > 0) ||
            (this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0) 
        ) {
            showClearFilter = true;
        }

        return (
            <div id="search-filter-source">
                {
                    showClearFilter == true ?
                        <div id="search-path" className="search-filter-params">
                            {
                                (showClearFilter == true && this.props.filterType == 2)?
                                    <div class="search-filter-header">
                                        <span class="search-filter-title">{JBD.JText._('LNG_APPLIED_FILTERS')}</span>
                                        <span className="filter-type-elem reset"><a href="javascript:jbdUtils.resetFilters(true, true)">{JBD.JText._('LNG_CLEAR_ALL_FILTERS')} <i className="la la-close"></i></a></span>
                                    </div>
                                : null
                            }
                            <ul id="selected-criteria" className="selected-criteria">
                                {
                                    this.props.searchKeyword != null ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeSearchRule('keyword')}>{this.props.searchKeyword} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (this.props.category != null && (this.props.categorySearch == 0 || this.props.categorySearch == null || searchType!="dynamic"))?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeFilterRuleCategory(this.props.category.id)}>{this.props.category.name} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (!jQuery.isEmptyObject(this.props.searchFilter['types']) && this.props.selectedParams['type'] !== undefined && this.props.selectedParams['type'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeFilterRule('type',this.props.selectedParams['type'][0])}>{this.props.searchFilter['types'][this.props.selectedParams['type'][0]].typeName} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (!jQuery.isEmptyObject(this.props.searchFilter['packages']) && this.props.selectedParams['package'] !== undefined && this.props.selectedParams['package'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeFilterRule('package',this.props.selectedParams['package'][0])}>{this.props.searchFilter['packages'][this.props.selectedParams['package'][0]].package_name} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (!jQuery.isEmptyObject(this.props.searchFilter['starRating']) && this.props.selectedParams['starRating'] !== undefined && this.props.selectedParams['starRating'].length > 0) ?
                                            <li>
                                                <a class="filter-type-elem"
                                                onClick={() => jbdUtils.removeFilterRule('starRating',this.props.selectedParams['starRating'][0])}>{this.props.searchFilter['starRating'][this.props.selectedParams['starRating'][0]].reviewScore}<i class="la la-star"></i> <i class="la la-times"></i></a>
                                            </li>
                                        : null
                                }

                                {
                                    (this.props.searchFilter['countries'] != null && this.props.searchFilter['countries'].length > 0 && this.props.selectedParams['country'] !== undefined  && this.props.selectedParams['country'] .length > 0
                                        && (this.props.zipCode == null || searchType!="dynamic")) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeFilterRule('country',this.props.selectedParams['country'][0])}>{this.props.searchFilter['countries'][this.props.selectedParams['country'][0]].countryName} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (!jQuery.isEmptyObject(this.props.searchFilter['provinces']) && this.props.selectedParams['province'] !== undefined && this.props.selectedParams['province'].length > 0
                                        && (this.props.zipCode == null || searchType!="dynamic")) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeFilterRule('province',this.props.selectedParams['province'][0])}>{this.props.searchFilter['provinces'][this.props.selectedParams['province'][0]].provinceName} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (!jQuery.isEmptyObject(this.props.searchFilter['regions']) && this.props.selectedParams['region'] !== undefined && this.props.selectedParams['region'].length > 0
                                        && (this.props.zipCode == null || searchType!="dynamic")) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeFilterRule('region',this.props.selectedParams['region'][0])}>{this.props.searchFilter['regions'][this.props.selectedParams['region'][0]].regionName} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (!jQuery.isEmptyObject(this.props.searchFilter['cities']) && this.props.selectedParams['city'] !== undefined && this.props.selectedParams['city'].length > 0
                                        && (this.props.zipCode == null || searchType!="dynamic")) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeFilterRule('city',this.props.selectedParams['city'][0])}>{this.props.searchFilter['cities'][this.props.selectedParams['city'][0]].cityName} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (!jQuery.isEmptyObject(this.props.searchFilter['areas']) && this.props.selectedParams['area'] !== undefined && this.props.selectedParams['area'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeFilterRule('area',this.props.selectedParams['area'][0])}>{this.props.searchFilter['areas'][this.props.selectedParams['area'][0]].areaName} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (!jQuery.isEmptyObject(this.props.searchFilter['memberships']) && this.props.selectedParams['membership'] !== undefined && this.props.selectedParams['membership'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeFilterRule('membership',this.props.selectedParams['membership'][0])}>{this.props.searchFilter['memberships'][this.props.selectedParams['membership'][0]].membership_name} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }
                            
                                {
                                    (!jQuery.isEmptyObject(this.props.searchFilter['attributes']) && this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0) ?
                                        <ul class="selected-criteria">
                                            {this.props.customAttributesValues.map((attribute, index) => {
                                                if (attribute != null) {
                                                    return (
                                                        <li>
                                                            <a className="filter-type-elem"
                                                            onClick={() => jbdUtils.removeAttrCond(attribute.attribute_id, attribute.id)}>{attribute.name} <i class="la la-times"></i></a>
                                                        </li>
                                                    )
                                                } else {
                                                    return null;
                                                }
                                            })}
                                        </ul> 
                                    : null
                                }       
                                {
                                    (!jQuery.isEmptyObject(this.props.selectedParams['custom-attributes']) && this.props.selectedParams['custom-attributes'] != null && this.props.selectedParams['custom-attributes'].length > 0) ?
                                        <ul class="selected-criteria">
                                            {this.props.selectedParams['custom-attributes'].map((attribute, index) => {
                                                if (attribute != null) {
                                                    Object.keys(attribute).map((key,index) => {
                                                        //console.debug(attribute[key]);
                                                        return (
                                                            <li>
                                                                <a className="filter-type-elem"
                                                                onClick={() => jbdUtils.removeAttrCond(key, key)}>{attribute[key]} <i class="la la-times"></i></a>
                                                            </li>
                                                        )
                                                    });
                                                } else {
                                                    return null;
                                                }
                                            })}
                                        </ul> 
                                    : null
                                }

                                {
                                    (this.props.zipCode != null && this.props.zipCode.length > 0 ) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeSearchRule('zipcode')}>{this.props.zipCode} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (!jQuery.isEmptyObject(this.props.location)) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeSearchRule('location')}>{Joomla.JText._('LNG_GEO_LOCATION')} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }
                       
                                {
                                    (this.props.selectedParams['age'] !== undefined && this.props.selectedParams['age'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeSearchRule('age')}>{JBD.JText._('LNG_AGE')} {this.props.selectedParams['age']} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (this.props.selectedParams['startTime'] !== undefined && this.props.selectedParams['startTime'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeSearchRule('start-time')}>{JBD.JText._('LNG_START_TIME')} {this.props.selectedParams['startTime']} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (this.props.selectedParams['endTime'] !== undefined && this.props.selectedParams['endTime'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeSearchRule('end-time')}>{JBD.JText._('LNG_END_TIME')} {this.props.selectedParams['endTime']} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (this.props.selectedParams['startDate'] !== undefined && this.props.selectedParams['startDate'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeSearchRule('startDate')}>{JBD.JText._('LNG_START')} {this.props.selectedParams['startDate']} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (this.props.selectedParams['endDate'] !== undefined && this.props.selectedParams['endDate'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeSearchRule('endDate')}>{JBD.JText._('LNG_END')} {this.props.selectedParams['endDate']} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (this.props.selectedParams['minPrice'] !== undefined && this.props.selectedParams['minPrice'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeSearchRule('minprice')}>{JBD.JText._('LNG_MIN_PRICE')} {this.props.selectedParams['minPrice']} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (this.props.selectedParams['maxPrice'] !== undefined && this.props.selectedParams['maxPrice'].length > 0) ?
                                        <li>
                                            <a class="filter-type-elem"
                                            onClick={() => jbdUtils.removeSearchRule('maxprice')}>{JBD.JText._('LNG_MAX_PRICE')} {this.props.selectedParams['maxPrice']} <i class="la la-times"></i></a>
                                        </li>
                                    : null
                                }

                                {
                                    (showClearFilter == true && this.props.filterType != 2)?
                                        <span className="filter-type-elem reset"><a href="javascript:jbdUtils.resetFilters(true, true)">{JBD.JText._('LNG_CLEAR_ALL_FILTERS')}</a></span>
                                    : null
                                }
                            </ul>
                        </div>
                    : null
                }
            </div>
        )
    }
}