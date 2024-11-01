class SearchFilterVertical extends React.Component {

    constructor(props) {
        super(props);
    }

    getDistanceFilters() {
        const radiuses = [50, 25, 10, 0];
        const distanceUnit = jbdUtils.getProperty('metric') == 1 ? JBD.JText._('LNG_MILES') : JBD.JText._('LNG_KM');

        return (
            <div className="filter-criteria">
                <div key={Math.random()} className="filter-header">{JBD.JText._('LNG_DISTANCE')}</div>
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
                <div key={Math.random()} className="filter-header">{JBD.JText._('LNG_MONTHS')}</div>
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
                            removeText = <span className="cross"></span>;
                            paramStartDate = '';
                            paramEndDate = '';
                        }

                        return (
                            <li key={Math.random() + '-' + index} className={liClass}>
                                <div key={Math.random() + '-' + index} className={divClass}>
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

                    <div key={Math.random()} className="search-category-box">
                        {distanceFilters}
                        {
                        (!jQuery.isEmptyObject(this.props.searchFilter['months'])) ? monthFilters : null
                        }

                        <div id="filterCategoryItems" key={Math.random()}>
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
                                (!jQuery.isEmptyObject(this.props.searchFilter['starRating'])) ?
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
                                (!jQuery.isEmptyObject(this.props.searchFilter['types'])) ?
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
                                (!jQuery.isEmptyObject(this.props.searchFilter['memberships'])) ?
                                    <SearchFilterVerticalItems
                                        items={this.props.searchFilter['memberships']}
                                        selectedItems={this.props.selectedParams['membership']}
                                        title={JBD.JText._('LNG_SELECT_MEMBERSHIP')}
                                        type={"membership"}
                                        valueField={"membership_id"}
                                        nameField={"membership_name"}
                                        expandItems={true}
                                        showMoreId={"extra_memberships_params"}
                                        showMoreBtn={"showMoreMemberships"}
                                        searchFilterItems={searchFilterItems}
                                    /> : null
                            }

                            {
                                (!jQuery.isEmptyObject(this.props.searchFilter['packages'])) ?
                                    <SearchFilterVerticalItems
                                        items={this.props.searchFilter['packages']}
                                        selectedItems={this.props.selectedParams['package']}
                                        title={Joomla.JText._('LNG_PACKAGE')}
                                        type={"package"}
                                        valueField={"package_id"}
                                        nameField={"package_name"}
                                        expandItems={true}
                                        showMoreId={"extra_package_params"}
                                        showMoreBtn={"showMorePackages"}
                                        searchFilterItems={searchFilterItems}
                                    /> : null
                            }

                            {
                                (!jQuery.isEmptyObject(this.props.searchFilter['countries'])) ?
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
                                (!jQuery.isEmptyObject(this.props.searchFilter['provinces'])) ?
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

                            {
                                (!jQuery.isEmptyObject(this.props.searchFilter['regions'])) ?
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
                                (!jQuery.isEmptyObject(this.props.searchFilter['cities'])) ?
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
                                (!jQuery.isEmptyObject(this.props.searchFilter['areas'])) ?
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
                                (!jQuery.isEmptyObject(this.props.searchFilter['attributes'])) ?
                                    this.props.searchFilter['attributes'].map((items) => {
                                        let item = Object.values(items)[0];
                                        let nameField = "value";
                                        //console.debug(item["optionName"]);
                                        if(item["optionName"] != null){
                                            nameField = "optionName";
                                        }
                                        let type = "attribute_" + item["id"];
                                        let extraAttribute = "extra_attributes_params_"+ item["id"];
                                        let showMore = "showMoreAttributes_"+ item["id"];

                                        //console.debug(extraAttribute);
                                        //console.debug(showMore);
                                        return (
                                            <SearchFilterVerticalItems
                                                items={items}
                                                selectedItems={this.props.selectedParams[type]}
                                                title={item["name"]}
                                                type={type}
                                                valueField={"value"}
                                                nameField={nameField}
                                                expandItems={true}
                                                showMoreId={extraAttribute}
                                                showMoreBtn={showMore}
                                                categoryId={this.props.categoryId}
                                                category={this.props.category}
                                                searchFilterItems={searchFilterItems}
                                            />
                                        )
                                    })
                                : null
                            }
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}