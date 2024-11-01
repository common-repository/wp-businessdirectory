class SearchFilterVerticalCategories extends React.Component {

    constructor(props) {
        super(props);
    }

    getRegularFilters(categories) {
        let counterCategories = 0;

        let categoryFilters = [];
        let moreCategoryFilters = [];

        for (let i = 0; i < categories.length; i++) {
            let filterCriteria = categories[i];
            if (counterCategories < this.props.searchFilterItems) {
                if (filterCriteria[1] > 0) {
                    categoryFilters.push(<li key={Math.random() + '-' + i}>
                        {
                            (this.props.category != null && filterCriteria[0][0].id == this.props.category.id) ?
                                <strong>{filterCriteria[0][0].name}</strong> :
                                <a className="cursor-pointer"
                                   onClick={() => jbdUtils.chooseCategory(filterCriteria[0][0].id)}>{filterCriteria[0][0].name}</a>
                        }
                    </li>);

                }
                counterCategories++;
            } else {
                categoryFilters.push(
                    <a id="showMoreCategories" className="filterExpand cursor-pointer"
                       onClick={() => jbdUtils.showMoreParams('extra_categories_params', 'showMoreCategories')}>
                        {JBD.JText._('LNG_MORE')} (+)
                    </a>);

                break;
            }
        }

        for (let i = 0; i < categories.length; i++) {
            let filterCriteria = categories[i];
            counterCategories--;

            if (counterCategories < 0) {
                if (filterCriteria[1] > 0) {
                    moreCategoryFilters.push(<li key={Math.random() + '-' + i}>
                        {
                            (this.props.category != null && filterCriteria[0][0] == this.props.category.id) ?
                                <strong>{filterCriteria[0][0].name}</strong> :
                                <a className="cursor-pointer"
                                   onClick={() => jbdUtils.chooseCategory(filterCriteria[0][0].id)}>{filterCriteria[0][0].name}</a>
                        }
                    </li>);
                }
            }
        }

        return (
            <ul>
                {categoryFilters}

                <div style={{display: "none"}} id="extra_categories_params">
                    {moreCategoryFilters}

                    <a id="showLessCategories" className="filterExpand cursor-pointer"
                       onClick={() => jbdUtils.showLessParams('extra_categories_params', 'showMoreCategories')}>
                        {JBD.JText._('LNG_LESS')} (-)
                    </a>
                </div>
            </ul>
        )
    }

    getFacetedFilters(categories) {
        let counterCategories = 0;

        let categoryFilters = [];
        let moreCategoryFilters = [];

        for (let i = 0; i < categories.length; i++) {
            let filterCriteria = categories[i];

            filterCriteria[0]["subCategories"] = Object.values(filterCriteria[0]["subCategories"]);

            if (counterCategories < this.props.searchFilterItems) {
                let liClass = '';
                let divClass = '';
                let action = jbdUtils.addFilterRuleCategory;
                let removeText = '';

                if (this.props.selectedCategories.some(cat => cat == filterCriteria[0][0].id)) {
                    liClass = "selectedlink";
                    divClass = "selected";
                    action = jbdUtils.removeFilterRuleCategory;
                    removeText = <span className="cross">(remove)</span>;
                }

                let subCategoriesFilters = [];
                if (filterCriteria[0]["subCategories"] != null) {
                    for (let j = 0; j < filterCriteria[0]["subCategories"].length; j++) {
                        let subCategory = filterCriteria[0]["subCategories"][j];

                        let liClassSub = '';
                        let divClassSub = '';
                        let actionSub = jbdUtils.addFilterRuleCategory;
                        let removeTextSub = '';

                        if (this.props.selectedCategories.some(cat => cat == subCategory[0].id)) {
                            liClassSub = "selectedlink";
                            divClassSub = "selected";
                            actionSub = jbdUtils.removeFilterRuleCategory;
                            removeTextSub = <span className="cross">(remove)</span>;
                        }

                        subCategoriesFilters.push(
                            <li className={liClassSub}>
                                <div className={divClassSub}>
                                    <a className="cursor-pointer" onClick={() => actionSub(subCategory[0].id)}>
                                        {subCategory[0].name} {removeText}
                                    </a>
                                </div>
                            </li>
                        );
                    }
                }

                categoryFilters.push(
                    <li key={Math.random() + '-' + i} className={liClass}>
                        <div className={divClass}>
                            <a className="filter-main-cat cursor-pointer" onClick={() => action(filterCriteria[0][0].id)}>
                                {filterCriteria[0][0].name} {removeText}
                            </a>
                        </div>

                        {subCategoriesFilters}
                    </li>
                );

                counterCategories++;
            } else {
                categoryFilters.push(
                    <a id="showMoreCategories1" className="filterExpand cursor-pointer"
                       onClick={() => jbdUtils.showMoreParams('extra_categories_params1', 'showMoreCategories1')}>
                        {JBD.JText._('LNG_MORE')} (+)
                    </a>
                );

                break;
            }
        }

        for (let i = 0; i < categories.length; i++) {
            let filterCriteria = categories[i];
            counterCategories--;

            filterCriteria[0]["subCategories"] = Object.values(filterCriteria[0]["subCategories"]);

            if (counterCategories < 0) {
                if (filterCriteria[1] > 0) {
                    let liClass = '';
                    let divClass = '';
                    let action = jbdUtils.addFilterRuleCategory;
                    let removeText = '';

                    if (this.props.selectedCategories.some(cat => cat == filterCriteria[0][0].id)) {
                        liClass = "selectedlink";
                        divClass = "selected";
                        action = jbdUtils.removeFilterRuleCategory;
                        removeText = <span className="cross">(remove)</span>;
                    }

                    let subCategoriesFilters = [];
                    if (filterCriteria[0]["subCategories"] != null) {
                        for (let j = 0; j < filterCriteria[0]["subCategories"].length; j++) {
                            let subCategory = filterCriteria[0]["subCategories"][j];

                            let liClassSub = '';
                            let divClassSub = '';
                            let actionSub = jbdUtils.addFilterRuleCategory;
                            let removeTextSub = '';

                            if (this.props.selectedCategories.some(cat => cat == subCategory[0].id)) {
                                liClassSub = "selectedlink";
                                divClassSub = "selected";
                                actionSub = jbdUtils.removeFilterRuleCategory;
                                removeTextSub = <span className="cross">(remove)</span>;
                            }

                            subCategoriesFilters.push(
                                <li key={Math.random() + '-' + i} className={liClassSub}>
                                    <div className={divClassSub}>
                                        <a className="cursor-pointer" onClick={() => action(subCategory[0].id)}>
                                            {subCategory[0].name} {removeText}
                                        </a>
                                    </div>
                                </li>
                            );
                        }
                    }

                    moreCategoryFilters.push(
                        <li key={Math.random() + '-' + i} className={liClass}>
                            <div className={divClass}>
                                <a className="filter-main-cat cursor-pointer" onClick={() => action(filterCriteria[0][0].id)}>
                                    {filterCriteria[0][0].name} {removeText}
                                </a>
                            </div>
                            {subCategoriesFilters}

                        </li>
                    )
                }
            }
        }

        return (
            <ul className="filter-categories">
                {categoryFilters}

                <div style={{display: "none"}} id="extra_categories_params1">
                    {moreCategoryFilters}

                    <a id="showLessCategories1" className="filterExpand cursor-pointer"
                       onClick={() => jbdUtils.showLessParams('extra_categories_params1', 'showMoreCategories1')}>
                        {JBD.JText._('LNG_LESS')} (-)
                    </a>
                </div>

            </ul>
        )
    }

    render() {
        const categories = this.props.categories;

        let categoryFilters = '';
        if (this.props.searchType == 0) {
            categoryFilters = this.getRegularFilters(categories);
        } else {
            categoryFilters = this.getFacetedFilters(categories);
        }

        return (
            <div className="filter-criteria">
                <div className="filter-header">{JBD.JText._('LNG_CATEGORIES')}</div>
                {categoryFilters}
                <div className="clear"></div>
            </div>
        );
    }
}