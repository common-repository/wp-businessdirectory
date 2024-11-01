class SearchFilterHorizontal extends React.Component {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        //console.debug("render horizontal mount");
        jQuery(".chosen-react").on('change', function(e) {
            let type = jQuery(this).attr('name');
            let val = jQuery(this).chosen().val();

            // console.debug(type);
            // console.debug(val);
            if(val){
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
        });

        // tippy('.local-info', {
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

    render() {

        let showClearFilter = false;
        let showOnlyLocal = typeof this.props.selectedParams['city'] !== 'undefined' && this.props.selectedParams['city'].length > 0 ? true: false;
        // console.debug(this.props.onlyLocal);
        let showOnlyLocalState = this.props.onlyLocal == 1 ? "checked" : "";
        // console.debug(showOnlyLocalState);
        // console.debug("render horizontal");

        showOnlyLocal = false;

        if (
            this.props.searchKeyword != null ||
            this.props.zipCode != null ||
            (this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0) ||
            (this.props.searchFilter['starRating'] != null && this.props.searchFilter['starRating'].length > 0) ||
            (this.props.searchFilter['types'] != null && this.props.searchFilter['types'].length > 0) ||
            (this.props.searchFilter['countries'] != null && this.props.searchFilter['countries'].length > 0) ||
            (this.props.searchFilter['provinces'] != null && this.props.searchFilter['provinces'].length > 0) ||
            (this.props.searchFilter['regions'] != null && this.props.searchFilter['regions'].length > 0) ||
            (this.props.searchFilter['cities'] != null && this.props.searchFilter['cities'].length > 0) ||
            (this.props.searchFilter['areas'] != null && this.props.searchFilter['areas'].length > 0) ||
            (this.props.searchFilter['companies'] != null && this.props.searchFilter['companies'].length > 0) ||
            (this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0) ||
            (this.props.location != null && this.props.location['latitude'] != null)
        ) {
            showClearFilter = false;
        }

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


        //console.debug("zipcode: " + this.props.zipCode);
        // console.debug(this.props.searchFilter['provinces']);
        // console.debug(this.props.searchFilter['provinces'] != null && this.props.searchFilter['provinces'].length > 0);
        return (
            <div>
                <div id="filter-switch-horizontal" className="filter-switch"  onClick={() => jbdUtils.toggleHorizontalFilter()}>
                    {JBD.JText._("LNG_SHOW_FILTER")}
                </div>
                <div id="search-filter-horizontal" className="search-filter-horizontal">
                    <div class="search-filter-label">
                        <i class="icon filters"></i> {JBD.JText._('LNG_FILTERS')}
                    </div>
                    <div class="search-filter-fields">
                        {
                            this.props.searchKeyword != undefined ?
                                <div className="search-options-item">
                                    <div className="jbd-input-box">
                                        <i className="icon pencil"></i>
                                        <a onClick={() => jbdUtils.removeSearchRule('keyword')}>{this.props.searchKeyword} x</a>
                                    </div>
                                </div> : null
                        }

                        {
                            (this.props.searchFilter['categories'] != undefined && (this.props.categorySearch == 0 || this.props.categorySearch == null || searchType!="dynamic")) ?
                                <div className="search-options-item">
                                    <div className="jbd-select-box">
                                        <i className="la la-list"></i>
                                        <select name="categories" className="chosen-react" value={selectedCategory}
                                                onChange={(e) => jbdUtils.chooseCategory(e.target.value)}>
                                            {selectedCategory != null ? (
                                                <>
                                                    <option value="">{JBD.JText._("LNG_CATEGORY")}</option>
                                                    <option value={selectedCategory}>{selectedCategoryName}</option>
                                                </>
                                                ) : (
                                                    <option value="">{JBD.JText._("LNG_CATEGORY")}</option>
                                            )}
                                            {
                                                this.props.searchFilter['categories'].map((filterCriteria) => {
                                                    if (filterCriteria[1] > 0 && filterCriteria[0][0].id != selectedCategory) {
                                                        return (
                                                            <option
                                                                value={filterCriteria[0][0].id}>{filterCriteria[0][0].name}</option>
                                                        )
                                                    } else {
                                                        return null;
                                                    }
                                                })
                                            }
                                        </select>
                                    </div>
                                </div>
                                : null
                        }

                        {
                            (this.props.searchFilter['starRating'] !== undefined) ?
                                <SearchFilterHorizontalItems
                                    items={this.props.searchFilter['starRating']}
                                    selectedItems={this.props.selectedParams['starRating']}
                                    title={JBD.JText._('LNG_SELECT_RATING')}
                                    type={"starRating"}
                                    valueField={"reviewScore"}
                                    nameField={"reviewScore"}
                                />
                                : null
                        }

                        {
                            (this.props.searchFilter['types'] !== undefined) ?
                                <SearchFilterHorizontalItems
                                    fetchData={this.props.fetchData}
                                    items={this.props.searchFilter['types']}
                                    selectedItems={this.props.selectedParams['type']}
                                    title={JBD.JText._('LNG_SELECT_TYPE')}
                                    type={"type"}
                                    valueField={"typeId"}
                                    nameField={"typeName"}
                                />
                                : null
                        }

                        {
                            (this.props.searchFilter['packages'] !== undefined) ?
                                <SearchFilterHorizontalItems
                                    fetchData={this.props.fetchData}
                                    items={this.props.searchFilter['packages']}
                                    selectedItems={this.props.selectedParams['package']}
                                    title={Joomla.JText._('LNG_PACKAGE')}
                                    type={"package"}
                                    valueField={"package_id"}
                                    nameField={"package_name"}
                                />
                                : null
                        }

                        {
                            (this.props.searchFilter['countries'] !== undefined && (this.props.zipCode == null || searchType!="dynamic")) ?
                                <SearchFilterHorizontalItems
                                    items={this.props.searchFilter['countries']}
                                    selectedItems={this.props.selectedParams['country']}
                                    title={JBD.JText._('LNG_SELECT_COUNTRY')}
                                    type={"country"}
                                    valueField={"countryId"}
                                    nameField={"countryName"}
                                />
                                : null
                        }

                        {
                            (this.props.searchFilter['provinces'] !== undefined && (this.props.zipCode == null || searchType!="dynamic")) ?
                                <SearchFilterHorizontalItems
                                    items={this.props.searchFilter['provinces']}
                                    selectedItems={this.props.selectedParams['province']}
                                    title={JBD.JText._('LNG_PROVINCE')}
                                    type={"province"}
                                    valueField={"provinceName"}
                                    nameField={"provinceName"}
                                />
                                : null
                        }

                        {
                            (this.props.searchFilter['regions'] !== undefined && (this.props.zipCode == null || searchType!="dynamic")) ?
                                <SearchFilterHorizontalItems
                                    items={this.props.searchFilter['regions']}
                                    selectedItems={this.props.selectedParams['region']}
                                    title={JBD.JText._('LNG_SELECT_REGION')}
                                    type={"region"}
                                    valueField={regionValueField}
                                    nameField={"regionName"}
                                />
                                : null
                        }

                        {
                            (this.props.searchFilter['cities'] !== undefined && (this.props.zipCode == null || searchType!="dynamic")) ?
                                <SearchFilterHorizontalItems
                                    items={this.props.searchFilter['cities']}
                                    selectedItems={this.props.selectedParams['city']}
                                    title={JBD.JText._('LNG_SELECT_CITY')}
                                    type={"city"}
                                    valueField={cityValueField}
                                    nameField={"cityName"}
                                />
                                : null
                        }

                        {
                            (this.props.searchFilter['areas'] !== undefined) ?
                                <SearchFilterHorizontalItems
                                    items={this.props.searchFilter['areas']}
                                    selectedItems={this.props.selectedParams['area']}
                                    title={JBD.JText._('LNG_SELECT_AREA')}
                                    type={"area"}
                                    valueField={"areaName"}
                                    nameField={"areaName"}
                                />
                                : null
                        }

                        {
                            (this.props.searchFilter['memberships'] !== undefined) ?
                                <SearchFilterHorizontalItems
                                    items={this.props.searchFilter['memberships']}
                                    selectedItems={this.props.selectedParams['membership']}
                                    title={JBD.JText._('LNG_SELECT_MEMBERSHIP')}
                                    type={"membership"}
                                    valueField={"membership_id"}
                                    nameField={"membership_name"}
                                />
                                : null
                        }

                        {
                            (this.props.searchFilter['attributes'] !=undefined) ?
                                this.props.searchFilter['attributes'].map((items) => {
                                    let item = Object.values(items)[0];
                                    let nameField = "value";
                                    //console.debug(item["optionName"]);
                                    if(item["optionName"] != null){
                                        nameField = "optionName";
                                    }
                                    let type = "attribute_" + item["id"];

                                    //console.debug(type);
                                    //console.debug(nameField);
                                    return (
                                        <SearchFilterHorizontalItems
                                            items={items}
                                            selectedItems={this.props.selectedParams[type]}
                                            title={item["name"]}
                                            type={type}
                                            valueField={"value"}
                                            nameField={nameField}
                                        />
                                    )
                                })
                            : null
                        }

                        {
                            (this.props.searchFilter['companies'] !== undefined) ?
                                <SearchFilterHorizontalItems
                                    items={this.props.searchFilter['companies']}
                                    selectedItems={this.props.selectedParams['company']}
                                    title={JBD.JText._('LNG_SELECT_COMPANY')}
                                    type={"company"}
                                    valueField={"companyId"}
                                    nameField={"companyName"}
                                />
                                : null
                        }

                        {
                            this.props.searchFilter['showDates'] != null && this.props.itemType == JBDConstants.ITEM_TYPE_REQUEST_QUOTE ?
                                <div className="search-options-item">
                                    <div className="jbd-date-box">
                                        <input type="date" value={this.props.startDate} onChange={(e) => jbdUtils.setFilterDates('startDate', e.target.value)}/>
                                    </div>
                                </div> 
                            : null
                        }

                        {
                            this.props.searchFilter['showDates'] != null && this.props.itemType == JBDConstants.ITEM_TYPE_REQUEST_QUOTE ?
                                <div className="search-options-item">
                                    <div className="jbd-date-box">
                                        <input type="date" value={this.props.endDate} onChange={(e) => jbdUtils.setFilterDates('endDate', e.target.value)}/>
                                    </div>
                                </div> 
                            : null
                        }

                        {/* {
                            (this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0) ?
                                <div className="custom-attributes-filter">
                                    {this.props.customAttributesValues.map((attribute, index) => {
                                        if (attribute != null) {
                                            return (
                                                <div className="search-options-item" key={index}>
                                                    <div className="jbd-input-box">
                                                        <a className="filter-type-elem"
                                                        onClick={() => jbdUtils.removeAttrCond(attribute.attribute_id)}>{attribute.name} x</a>
                                                    </div>
                                                </div>
                                            )
                                        } else {
                                            return null;
                                        }
                                    })}
                                </div> : null
                        } */}

                        {
                        /*
                            this.props.zipCode != null ?
                                <div className="search-options-item">
                                    <div className="jbd-input-box">
                                        <i className="icon map-marker"></i>
                                        <a className="filter-type-elem"
                                        onClick={() => jbdUtils.removeSearchRule('zipcode')}>{this.props.zipCode} x</a>
                                    </div>
                                </div> : null
                        */
                        }

                        {
                            (this.props.location != undefined && this.props.location['latitude'] != undefined) ?
                                <div className="search-options-item radius">
                                    <div className="jbd-select-box">
                                        <i className="la la-list"></i>
                                        <select name="distance" className="chosen-react"
                                                onChange={(e) => jbdListings.setRadius(e.target.value)}>
                                            <option value="0">{JBD.JText._('LNG_RADIUS')}</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div> : null
                        }

                        {
                            showClearFilter ?
                                <div className="search-options-item">
                                    <a className="clear-search cursor-pointer" onClick={() => jbdUtils.resetFilters(true, true)}
                                    style={{textDecoration: "none"}}>{JBD.JText._('LNG_CLEAR')}</a>
                                </div> : null
                        }

                        {
                            showOnlyLocal?
                                <div id="map-location" className="search-options-item">
                                </div>
                            : null
                        }

                        {
                            showOnlyLocal ?
                                <div className="search-options-item show-local">
                                    <label className="toggle-dir-btn">
                                        <input type="checkbox" defaultChecked={showOnlyLocalState} onChange={() => jbdUtils.toggleOnlyLocal()}/>
                                        <span className="slider"></span>
                                        <span className="labels" data-on={JBD.JText._('LNG_SHOW_LOCAL_ON')} data-off={JBD.JText._('LNG_SHOW_LOCAL_OFF')}></span>
                                    </label>
                                    <i class="local-info icon info-circle" aria-expanded="false"></i>
                                </div>
                            : null
                        }
                    </div>
                </div>
            </div>
        )
    }
}