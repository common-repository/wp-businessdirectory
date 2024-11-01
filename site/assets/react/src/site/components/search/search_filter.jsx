class SearchFilter extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            radius: null,
            location: null,
            searchFilter: [],
            category: null,
            categoryId: null,
            categorySearch: null,
            selectedCategories: [],
            selectedParams: [],
            filterMonths: null,
            startDate: null,
            endDate: null,
            searchKeyword: null,
            customAttributesValues: null,
            zipCode: null,
            err: null,
            searchFilterType: null,
            showSearchFilterParams: null,
            isLoading: false,
            onlyLocal: null
        }

        this.fetchData = this.fetchData.bind(this);
    }

    fetchData() {
        this.setState({isLoading: true});

        let url = jbdUtils.getAjaxUrl('getSearchFilter', 'search');
        if (this.props.itemType == JBDConstants.ITEM_TYPE_EVENT) {
            url = jbdUtils.getAjaxUrl('getSearchFilter', 'events');
        } else if (this.props.itemType == JBDConstants.ITEM_TYPE_OFFER) {
            url = jbdUtils.getAjaxUrl('getSearchFilter', 'offers');
        } else if (this.props.itemType == JBDConstants.ITEM_TYPE_REQUEST_QUOTE) {
            url = jbdUtils.getAjaxUrl('getSearchFilter', 'requestquotes');
        }

        url = url + "&_c=" + Math.random() * 10 + "&reload=1";

        fetch(url, {
            headers: {
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache',
            'Expires': 0
            }
        }).then(res => {
            
            if (res.status >= 400) {
                throw new Error("Server responded with error!");
            }

            return res.json();
        }).then(response => {
                this.setFilterData(response);
        },
        err => {
            this.setState({
                err,
                isLoading: false
            })
        });
    }

    componentDidMount() {
        this.fetchData();
    }

    setFilterData(response){
        let searchFilter = null;
        if (response.data.searchFilter != null) {
            searchFilter = [];
            for (let key in response.data.searchFilter) {
                if (response.data.searchFilter.hasOwnProperty(key)) {
                    let row = [];
                    for (let keyj in response.data.searchFilter[key]){
                        row[keyj] = response.data.searchFilter[key][keyj];
                    }
                    searchFilter[key] = row;
                }
            }
        }

        this.setState({
            radius: response.data.radius,
            location: response.data.location,
            searchFilter: searchFilter,
            category: response.data.category,
            categoryId: typeof response.data.categoryId !== 'undefined' ? response.data.categoryId : null,
            categorySearch: typeof response.data.categorySearch !== 'undefined' ? response.data.categorySearch : null,
            selectedCategories: response.data.selectedCategories,
            selectedParams: response.data.selectedParams,
            filterMonths: typeof response.data.filterMonths !== 'undefined' ? response.data.filterMonths : null,
            startDate: typeof response.data.startDate !== 'undefined' ? response.data.startDate : null,
            endDate: typeof response.data.endDate !== 'undefined' ? response.data.endDate : null,
            onlyLocal: typeof response.data.onlyLocal !== 'undefined' ? response.data.onlyLocal : null,
            searchKeyword: typeof response.data.searchKeyword !== 'undefined' ? response.data.searchKeyword : null,
            customAttributesValues: typeof response.data.customAttributesValues !== 'undefined' ? response.data.customAttributesValues : null,
            zipCode: typeof response.data.zipCode !== 'undefined' ? response.data.zipCode : null,
            isLoading: false
        })

        jbdUtils.moveMap();

        //move vertical search filter
        if(jbdUtils.getProperty('move-search-filter')){
            //jQuery("#search-filters-react-container").html(jQuery("#search-filter-source").html());

            //jQuery("#search-filter-source").detach().appendTo("#search-filters-react-container");
            //jQuery("#search-filter-source").html("");
        }
    }

    render() {
        //console.debug(this.props.searchFilterType);
        if (this.state.isLoading) {
            return (
                <Loading/>
            )
        } else {
            // console.debug(this.props.showSearchFilterParams);
            return (
                <div>
                    {
                        (this.props.searchFilterType == 1) ?
                            <SearchFilterHorizontal
                                fetchData={this.fetchData}
                                searchKeyword={this.state.searchKeyword}
                                radius={this.state.radius}
                                location={this.state.location}
                                searchFilter={this.state.searchFilter}
                                category={this.state.category}
                                categorySearch={this.state.categorySearch}
                                categoryId={this.state.categoryId}
                                selectedCategories={this.state.selectedCategories}
                                selectedParams={this.state.selectedParams}
                                customAttributesValues={this.state.customAttributesValues}
                                zipCode={this.state.zipCode}
                                itemType={this.props.itemType}
                                startDate={this.state.startDate}
                                endDate={this.state.endDate}
                                onlyLocal={this.state.onlyLocal}
                            />
                            :null
                    }

                    {
                        this.props.searchFilterType == 3 && this.props.itemType == 1 &&
                            <SearchFilterHorizontalCat
                                searchKeyword={this.state.searchKeyword}
                                radius={this.state.radius}
                                location={this.state.location}
                                searchFilter={this.state.searchFilter}
                                category={this.state.category}
                                categorySearch={this.state.categorySearch}
                                categoryId={this.state.categoryId}
                                selectedCategories={this.state.selectedCategories}
                                selectedParams={this.state.selectedParams}
                                customAttributesValues={this.state.customAttributesValues}
                                zipCode={this.state.zipCode}
                                itemType={this.props.itemType} />
                    }

                    {
                        (this.props.showSearchFilterParams == true) ?
                            <SearchFilterParams
                                searchKeyword={this.state.searchKeyword}
                                radius={this.state.radius}
                                location={this.state.location}
                                searchFilter={this.state.searchFilter}
                                filterType={this.props.searchFilterType}
                                category={this.state.category}
                                categorySearch={this.state.categorySearch}
                                categoryId={this.state.categoryId}
                                selectedCategories={this.state.selectedCategories}
                                selectedParams={this.state.selectedParams}
                                customAttributesValues={this.state.customAttributesValues}
                                zipCode={this.state.zipCode}
                                itemType={this.props.itemType}
                                startDate={this.state.startDate}
                                endDate={this.state.endDate}
                                onlyLocal={this.state.onlyLocal}
                            />
                            :null
                    }

                    {
                        (this.props.searchFilterType == 2) ?
                            <SearchFilterVertical
                                filterType={this.props.searchFilterType}
                                radius={this.state.radius}
                                location={this.state.location}
                                searchFilter={this.state.searchFilter}
                                category={this.state.category}
                                categorySearch={this.state.categorySearch}
                                categoryId={this.state.categoryId}
                                selectedCategories={this.state.selectedCategories}
                                selectedParams={this.state.selectedParams}
                                filterMonths={this.state.filterMonths}
                                startDate={this.state.startDate}
                                itemType={this.props.itemType}
                            />
                            :null
                    }
                </div>
            );
        }
    }
}