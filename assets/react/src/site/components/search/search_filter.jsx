class SearchFilter extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
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
        }
    }

    componentDidMount() {
        this.setState({isLoading: true});

        let url = jbdUtils.getAjaxUrl('getSearchFilter', 'search');
        if (this.props.itemType == JBDConstants.ITEM_TYPE_EVENT) {
            url = jbdUtils.getAjaxUrl('getSearchFilter', 'events');
        } else if (this.props.itemType == JBDConstants.ITEM_TYPE_OFFER) {
            url = jbdUtils.getAjaxUrl('getSearchFilter', 'offers');
        }

        fetch(url).then(res => {
            if (res.status >= 400) {
                throw new Error("Server responded with error!");
            }

            return res.json();
        }).then(response => {
                let searchFilter = null;
                if (response.data.searchFilter != null) {
                    searchFilter = [];
                    for (let key in response.data.searchFilter) {
                        if (response.data.searchFilter.hasOwnProperty(key)) {
                            searchFilter[key] = Object.values(response.data.searchFilter[key]);
                        }
                    }
                }

                this.setState({
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
                })
            },
            err => {
                this.setState({
                    err,
                    isLoading: false
                })
            });
    }

    render() {

        if (this.state.isLoading) {
            return (
                <Loading/>
            )
        } else {
            return (
                <div>
                    {
                        jbdUtils.getProperty('search_filter_type') == 2 || this.props.itemType != 1 ?
                            <SearchFilterVertical
                                radius={this.state.radius}
                                location={this.state.location}
                                searchFilter={this.state.searchFilter}
                                category={this.state.category}
                                categoryId={this.state.categoryId}
                                selectedCategories={this.state.selectedCategories}
                                selectedParams={this.state.selectedParams}
                                filterMonths={this.state.filterMonths}
                                startDate={this.state.startDate}
                                itemType={this.props.itemType}
                            />
                            :
                            <SearchFilterHorizontal
                                searchKeyword={this.state.searchKeyword}
                                radius={this.state.radius}
                                location={this.state.location}
                                searchFilter={this.state.searchFilter}
                                category={this.state.category}
                                categoryId={this.state.categoryId}
                                selectedCategories={this.state.selectedCategories}
                                selectedParams={this.state.selectedParams}
                                customAttributesValues={this.state.customAttributesValues}
                                zipCode={this.state.zipCode}
                                itemType={this.props.itemType}
                            />
                    }
                </div>
            );
        }
    }
}