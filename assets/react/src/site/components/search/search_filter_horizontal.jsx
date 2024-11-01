class SearchFilterHorizontal extends React.Component {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        jQuery(".chosen-react").on('change', function(e) {
            let type = jQuery(this).attr('name');
            let val = jQuery(this).chosen().val();

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

    render() {

        let showClearFilter = false;

        if (
            this.props.searchKeyword != null ||
            this.props.zipCode != null ||
            (this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0) ||
            (this.props.searchFilter['starRating'] != null && this.props.searchFilter['starRating'].length > 0) ||
            (this.props.searchFilter['types'] != null && this.props.searchFilter['types'].length > 0) ||
            (this.props.searchFilter['countries'] != null && this.props.searchFilter['countries'].length > 0) ||
            (this.props.searchFilter['regions'] != null && this.props.searchFilter['regions'].length > 0) ||
            (this.props.searchFilter['cities'] != null && this.props.searchFilter['cities'].length > 0) ||
            (this.props.searchFilter['areas'] != null && this.props.searchFilter['areas'].length > 0) ||
            (this.props.searchFilter['provinces'] != null && this.props.searchFilter['provinces'].length > 0) ||
            (this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0) ||
            (this.props.location != null && this.props.location['latitude'] != null)
        ) {
            showClearFilter = true;
        }

        let selectedCategory = null;
        if (this.props.category != null) {
            selectedCategory = this.props.category.id;
        }

        let cityValueField = "city";
        let regionValueField = "region";

        return (
            <div id="search-filter-horizontal" className="search-filter-horizontal">
                {
                    this.props.searchKeyword != null ?
                        <div className="search-options-item">
                            <div className="jbd-input-box">
                                <i className="la la-pencil"></i>
                                <a onClick={() => jbdUtils.removeSearchRule('keyword')}>{this.props.searchKeyword} x</a>
                            </div>
                        </div> : null
                }

                {
                    (this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0) ?
                        <div className="search-options-item">
                            <div className="jbd-select-box">
                                <i className="la la-list"></i>
                                <select name="categories" className="chosen-react" value={selectedCategory}
                                        onChange={() => jbdUtils.chooseCategory(this.value)}>
                                    <option value="">{JBD.JText._('LNG_CATEGORY')}</option>
                                    {
                                        this.props.searchFilter['categories'].map((filterCriteria) => {
                                            if (filterCriteria[1] > 0) {
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
                    (this.props.searchFilter['starRating'] != null && this.props.searchFilter['starRating'].length > 0) ?
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
                    (this.props.searchFilter['types'] != null && this.props.searchFilter['types'].length > 0) ?
                        <SearchFilterHorizontalItems
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
                    (this.props.searchFilter['countries'] != null && this.props.searchFilter['countries'].length > 0) ?
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
                    (this.props.searchFilter['regions'] != null && this.props.searchFilter['regions'].length > 0) ?
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
                    (this.props.searchFilter['cities'] != null && this.props.searchFilter['cities'].length > 0) ?
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
                    (this.props.searchFilter['areas'] != null && this.props.searchFilter['areas'].length > 0) ?
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
                    (this.props.searchFilter['provinces'] != null && this.props.searchFilter['provinces'].length > 0) ?
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
                    (this.props.customAttributesValues != null && this.props.customAttributesValues.length > 0) ?
                        <span>
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
                        </span> : null
                }

                {
                    this.props.zipCode != null ?
                        <div className="search-options-item">
                            <div className="jbd-input-box">
                                <i className="la la-map-marker"></i>
                                <a className="filter-type-elem"
                                   onClick={() => jbdUtils.removeSearchRule('zipcode')}>{this.props.zipCode} x</a>
                            </div>
                        </div> : null
                }

                {
                    (this.props.location != null && this.props.location['latitude'] != null) ?
                        <div className="search-options-item">
                            <div className="jbd-select-box">
                                <i className="la la-list"></i>
                                <select name="distance" className="chosen-react"
                                        onChange={() => jbdListings.setRadius(this.value)}>
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
                               style={{textDecoration: "none"}}><i className="la la-close"></i></a>
                        </div> : null
                }
            </div>
        )
    }
}