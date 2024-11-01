class SearchFilterHorizontalCat extends React.Component {

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
                default:
                    jbdUtils.addFilterRule(type, val);
            }
        });

        jQuery(".filter-categories i.icon").click(function(e) {
            $hasOpenClass = jQuery(this).parent().parent().hasClass('open');
            jQuery(".filter-categories li").removeClass('open');
            
            if(!$hasOpenClass){
                jQuery(this).parent().parent().toggleClass("open");
            }

            e.stopPropagation();
        });

        jQuery(".filter-main-cat").click(function(e) {
            $hasOpenClass = jQuery(this).parent().parent().parent().hasClass('open');
            jQuery(".filter-categories li").removeClass('open');
            
            if(!$hasOpenClass){
                jQuery(this).parent().parent().parent().toggleClass("open");
            }

            e.stopPropagation();
         });

        jQuery("body").click(function(e) {
            jQuery(".filter-categories li").removeClass('open');
        });
    }


    getCategoryFilters(categories) {
        let counterCategories = 0;

        let categoryFilters = [];
        
        for (let i = 0; i < categories.length; i++) {
            let filterCriteria = categories[i];

            filterCriteria[0]["subCategories"] = Object.values(filterCriteria[0]["subCategories"]);

            if (counterCategories < 100) {
                let liClass = '';
                let divClass = '';
                let action = jbdUtils.addFilterRuleCategory;
                let removeText = '';
                let checkedMain = false;

                if (this.props.selectedCategories.some(cat => cat == filterCriteria[0][0].id)) {
                    liClass = "selectedlink";
                    divClass = "selected";
                    action = jbdUtils.removeFilterRuleCategory;
                    removeText = <span className="cross"></span>;
                    checkedMain = true;
                }

                let subCategoriesFilters = [];
                if (filterCriteria[0]["subCategories"] != null) {
                    for (let j = 0; j < filterCriteria[0]["subCategories"].length; j++) {
                        let subCategory = filterCriteria[0]["subCategories"][j];

                        let liClassSub = '';
                        let divClassSub = '';
                        let actionSub = jbdUtils.addFilterRuleCategory;
                        let removeTextSub = '';
                        let checked = false;

                        if (this.props.selectedCategories.some(cat => cat == subCategory[0].id)) {
                            liClassSub = "selectedlink";
                            divClassSub = "selected";
                            actionSub = jbdUtils.removeFilterRuleCategory;
                            removeTextSub = <span className="cross"></span>;
                            checked = true;
                        }

                        subCategoriesFilters.push(
                            <li key={Math.random() + '-' + i} className={liClassSub}>
                                <div>
                                    <input className="cursor-pointer" name="cat" type="checkbox"  checked={checked}  onChange={() => actionSub(subCategory[0].id)} /> &nbsp;
                                    <a className="cursor-pointer" onClick={() => actionSub(subCategory[0].id)}>
                                        {subCategory[0].name} {removeTextSub}
                                    </a>
                                </div>
                            </li>
                        );
                    }
                }

                categoryFilters.push(
                    <li key={Math.random() + '-' + i} className="multi-column">
                        <div className="main-cat-container">
                            <div>
                                <div className="filter-main-cat cursor-pointer">
                                    {filterCriteria[0][0].name}
                                </div>
                            
                            </div>
                            <i className="icon"></i>
                        </div>

                        <ul className="submenu">
                            <li key={Math.random() + '-' + i}>
                                <div>
                                    <input className="cursor-pointer" name="cat" type="checkbox"  checked={checkedMain}  onChange={() => action(filterCriteria[0][0].id)} /> &nbsp;
                                    <a className="cursor-pointer" onClick={() => action(filterCriteria[0][0].id)}>
                                        {filterCriteria[0][0].name}
                                    </a>
                                </div>
                            </li>
                            {subCategoriesFilters}
                        </ul>
                    </li>
                );

                counterCategories++;
            }
        }

        return (
            categoryFilters
        )
    }

    render() {
        let showClearFilter = false;

        let categoriesFilter = "";
        if(this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0){
            categoriesFilter = this.getCategoryFilters(this.props.searchFilter['categories']);
        }
        
        return (
            <div id="category-filter-horizontal" className="category-filter-horizontal">
                <ul key={Math.random()*100} className="filter-categories">
                    {
                        (this.props.searchFilter['categories'] != null && this.props.searchFilter['categories'].length > 0) ?

                        this.getCategoryFilters(this.props.searchFilter['categories']) : null
                    }

                    {
                        (this.props.searchFilter['memberships'] != null && this.props.searchFilter['memberships'].length > 0) ?
                            <SearchFilterHorizontalCatItems
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
                        (this.props.searchFilter != null && this.props.searchFilter['attributes'] != null && this.props.searchFilter['attributes'].length > 0) ?
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
                                    <SearchFilterHorizontalCatItems
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

                </ul>
                
                {
                    showClearFilter ?
                        <div className="search-options-item">
                            <a className="clear-search cursor-pointer" onClick={() => jbdUtils.resetFilters(true, true)}
                            style={{textDecoration: "none"}}>{JBD.JText._('LNG_CLEAR')}</a>
                        </div> : null
                }
              
            </div>
        );

       
    }
}