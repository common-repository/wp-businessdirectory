class SearchFilterVertical extends React.Component {

    constructor(props) {
        super(props);
    }

    getDistanceFilters() {
        const radiuses = [50, 25, 10, 0];
        const distanceUnit = jbdUtils.getProperty('metric') == 1 ? JBD.JText._('LNG_MILES') : JBD.JText._('LNG_KM');

        return (
            <div className="filter-criteria">
                <div className="filter-header">{JBD.JText._('LNG_DISTANCE')}</div>
                <ul>
                    {radiuses.map((radius, index) => {
                        let radiusText = radius + ' ' + distanceUnit;
                        if (radius == 0) {
                            radiusText = JBD.JText._('LNG_ALL');
                        }

                        return (
                            <li key={Math.random() + '-' + index}>
                                {
                                    this.props.radius != radius ?
                                        <a className="cursor-pointer"
                                           onClick={() => jbdListings.setRadius(radius)}>{radiusText}</a> :
                                        <strong>{radiusText}</strong>
                                }
                            </li>
                        )
                    })}
                </ul>
            </div>
        )
    }

    getFilterMonths() {
        const filterMonths = this.props.filterMonths;
        const startDate = this.props.startDate;

        if (filterMonths == null) {
            return null;
        }

        return (
            <div className="filter-criteria">
                <div className="filter-header">{JBD.JText._('LNG_MONTHS')}</div>
                <ul>
                    {filterMonths.map((month, index) => {
                        let liClass = '';
                        let divClass = '';
                        let removeText = '';
                        let action = jbdEvents.setSearchDates;
                        let paramStartDate = month.start_date;
                        let paramEndDate = month.end_date;

                        if (month.start_date == startDate) {
                            action = jbdEvents.setSearchDates;
                            liClass = "selectedlink";
                            divClass = "selected";
                            removeText = <span className="cross">(remove)</span>;
                            paramStartDate = '';
                            paramEndDate = '';
                        }


                        return (
                            <li key={Math.random() + '-' + index} className={liClass}>
                                <div className={divClass}>
                                    <a className="cursor-pointer" onClick={() => action(paramStartDate, paramEndDate)}>
                                        {month.name} {removeText}
                                    </a>
                                </div>
                            </li>
                        )
                    })}
                </ul>
            </div>
        )
    }

    render() {
        const searchFilterClasses = ['search-filter'];
        if (jbdUtils.getProperty('search_filter_view') == 2) {
            searchFilterClasses.push('style-2');
        }

        let distanceFilters = '';
        if (this.props.location != null && this.props.location['latitude'] != null) {
            distanceFilters = this.getDistanceFilters();
        }

        let cityValueField = "city";
        let regionValueField = "region";
        let monthFilters = '';
        let searchFilterItems = jbdUtils.getProperty('search_filter_items');
        let searchType = jbdUtils.getProperty('search_type');
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

        return (
            <div>
                <div id="filter-switch" className="filter-switch"  onClick={() => jbdUtils.toggleFilter()}>
                    {JBD.JText._("LNG_SHOW_FILTER")}
                </div>

                <div id="search-filter" className={searchFilterClasses.join(' ')}>
                    <div className="filter-fav clear" style={{display: 'none'}}>
                        /* TODO is this section needed? */
                    </div>

                    <div className="search-category-box">
                        {distanceFilters}
                        {monthFilters}

                        <div id="filterCategoryItems">

                            {
                                (this.props.searchFilter != null && this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0) ?
                                    <SearchFilterVerticalCategories
                                        categories={this.props.searchFilter['categories']}
                                        category={this.props.category}
                                        selectedCategories={this.props.selectedCategories}
                                        searchFilterItems={searchFilterItems}
                                        searchType={searchType}
                                    /> : null
                            }

                            {
                                (this.props.searchFilter != null && this.props.searchFilter['starRating'] != null && this.props.searchFilter['starRating'].length > 0) ?
                                    <SearchFilterVerticalItems
                                        items={this.props.searchFilter['starRating']}
                                        selectedItems={this.props.selectedParams['starRating']}
                                        title={JBD.JText._('LNG_STAR_RATING')}
                                        type={"starRating"}
                                        valueField={"reviewScore"}
                                        nameField={"reviewScore"}
                                        customText={JBD.JText._('LNG_STARS')}
                                        expandItems={false}
                                        searchFilterItems={searchFilterItems}
                                    /> : null
                            }

                            {
                                (this.props.searchFilter != null && this.props.searchFilter['types'] != null && this.props.searchFilter['types'].length > 0) ?
                                    <SearchFilterVerticalItems
                                        items={this.props.searchFilter['types']}
                                        selectedItems={this.props.selectedParams['type']}
                                        title={JBD.JText._('LNG_TYPES')}
                                        type={"type"}
                                        valueField={"typeId"}
                                        nameField={"typeName"}
                                        expandItems={true}
                                        showMoreId={"extra_types_params"}
                                        showMoreBtn={"showMoreTypes"}
                                        categoryId={this.props.categoryId}
                                        category={this.props.category}
                                        searchFilterItems={searchFilterItems}
                                    /> : null
                            }

                            {
                                (this.props.searchFilter != null && this.props.searchFilter['countries'] != null && this.props.searchFilter['countries'].length > 0) ?
                                    <SearchFilterVerticalItems
                                        items={this.props.searchFilter['countries']}
                                        selectedItems={this.props.selectedParams['country']}
                                        title={JBD.JText._('LNG_COUNTRIES')}
                                        type={"country"}
                                        valueField={"countryId"}
                                        nameField={"countryName"}
                                        expandItems={true}
                                        showMoreId={"extra_countries_params"}
                                        showMoreBtn={"showMoreCountries"}
                                        searchFilterItems={searchFilterItems}
                                    /> : null
                            }

                            {
                                (this.props.searchFilter != null && this.props.searchFilter['regions'] != null && this.props.searchFilter['regions'].length > 0) ?
                                    <SearchFilterVerticalItems
                                        items={this.props.searchFilter['regions']}
                                        selectedItems={this.props.selectedParams['region']}
                                        title={JBD.JText._('LNG_REGIONS')}
                                        type={"region"}
                                        valueField={regionValueField}
                                        nameField={"regionName"}
                                        expandItems={true}
                                        showMoreId={"extra_regions_params"}
                                        showMoreBtn={"showMoreRegions"}
                                        categoryId={this.props.categoryId}
                                        category={this.props.category}
                                        searchFilterItems={searchFilterItems}
                                    /> : null
                            }

                            {
                                (this.props.searchFilter != null && this.props.searchFilter['cities'] != null && this.props.searchFilter['cities'].length > 0) ?
                                    <SearchFilterVerticalItems
                                        items={this.props.searchFilter['cities']}
                                        selectedItems={this.props.selectedParams['city']}
                                        title={JBD.JText._('LNG_CITIES')}
                                        type={"city"}
                                        valueField={cityValueField}
                                        nameField={"cityName"}
                                        expandItems={true}
                                        showMoreId={"extra_cities_params"}
                                        showMoreBtn={"showMoreCities"}
                                        categoryId={this.props.categoryId}
                                        category={this.props.category}
                                        searchFilterItems={searchFilterItems}
                                    /> : null
                            }

                            {
                                (this.props.searchFilter != null && this.props.searchFilter['areas'] != null && this.props.searchFilter['areas'].length > 0) ?
                                    <SearchFilterVerticalItems
                                        items={this.props.searchFilter['areas']}
                                        selectedItems={this.props.selectedParams['area']}
                                        title={JBD.JText._('LNG_AREA')}
                                        type={"area"}
                                        valueField={"areaName"}
                                        nameField={"areaName"}
                                        expandItems={true}
                                        showMoreId={"extra_areas_params"}
                                        showMoreBtn={"showMoreAreas"}
                                        categoryId={this.props.categoryId}
                                        category={this.props.category}
                                        searchFilterItems={searchFilterItems}
                                    /> : null
                            }

                            {
                                (this.props.searchFilter != null && this.props.searchFilter['provinces'] != null && this.props.searchFilter['provinces'].length > 0) ?
                                    <SearchFilterVerticalItems
                                        items={this.props.searchFilter['provinces']}
                                        selectedItems={this.props.selectedParams['province']}
                                        title={JBD.JText._('LNG_PROVINCE')}
                                        type={"province"}
                                        valueField={"provinceName"}
                                        nameField={"provinceName"}
                                        expandItems={true}
                                        showMoreId={"extra_provinces_params"}
                                        showMoreBtn={"showMoreProvinces"}
                                        searchFilterItems={searchFilterItems}
                                    /> : null
                            }
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}