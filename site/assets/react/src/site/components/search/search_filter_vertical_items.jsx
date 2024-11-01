class SearchFilterVerticalItems extends React.Component {

    constructor(props) {
        super(props);
    }

    getFilters(items) {
        const nameField = this.props.nameField;
        const valueField = this.props.valueField;
        const selectedItems = this.props.selectedItems;
        const customText = this.props.customText;
        const type = this.props.type;

        items =  Object.values(items);

        const setCategory = (typeof this.props.category !== 'undefined' && this.props.category != null) ? 1 : 0;
        const categId = (typeof this.props.categoryId !== 'undefined' && this.props.categoryId != null) ? this.props.categoryId : 0;

        const addFilterAction = (typeof this.props.addFilterAction !== 'undefined') ? this.props.addFilterAction : jbdUtils.addFilterRule;
        const removeFilterAction = (typeof this.props.removeFilterAction !== 'undefined') ? this.props.removeFilterAction : jbdUtils.removeFilterRule;

        return (
            <span>
                {
                    items.map((item, index) => {
                        //console.debug(index);
                        if (item[valueField] != null) {
                            let liClass = '';
                            let divClass = '';
                            let action = addFilterAction;
                            let removeText = '';

                            if (selectedItems!= null && selectedItems.some(selectedItem => selectedItem == item[valueField])) {
                                liClass = "selectedlink";
                                divClass = "selected";
                                action = removeFilterAction;
                                removeText = <span className="cross"></span>;
                            }

                            return (
                                <li key={Math.random()*10*index} className={liClass}>
                                    <div key={Math.random()*10} className={divClass}>
                                        <a className="cursor-pointer" onClick={() => action(type, item[valueField], setCategory, categId)}>
                                            {item[nameField]} {customText} {removeText}
                                        </a>
                                    </div>
                                </li>
                            )
                        }
                    })
                }
            </span>
        )
    }

    getExpandedFilters() {
        let items = this.props.items;
        const showMoreBtn = this.props.showMoreBtn;
        const showMoreId = this.props.showMoreId;

        items = Object.values(items);

        let result = [];
        let filters = '';
        let moreFilters = '';

        let counterItems = 0;

        let visibleItems = [];
        let hiddenItems = [];
        for (let i = 0; i < items.length; i++) {
            let item = items[i];

            if (counterItems < this.props.searchFilterItems) {
                visibleItems.push(item);
            } else {
                hiddenItems.push(item);
            }

            counterItems++;
        }

        filters = this.getFilters(visibleItems);

        result.push(filters);

        if (hiddenItems.length > 0) {
            moreFilters = this.getFilters(hiddenItems);

            result.push(
                <a id={showMoreBtn} className="filterExpand cursor-pointer"
                   onClick={() => jbdUtils.showMoreParams(showMoreId, showMoreBtn)}>
                    {JBD.JText._('LNG_MORE')} (+)
                </a>
            );
            result.push(
                <div style={{display: "none"}} id={showMoreId}>
                    {moreFilters}

                    <a id={showMoreBtn} className="filterExpand cursor-pointer"
                       onClick={() => jbdUtils.showLessParams(showMoreId, showMoreBtn)}>
                        {JBD.JText._('LNG_LESS')} (-)
                    </a>
                </div>
            );
        }

        return result;
    }

    render() {
        let items = this.props.items;
        const title = this.props.title;
        const expandItems = this.props.expandItems;

        let filters = '';
        if (expandItems) {
            filters = this.getExpandedFilters(items);
        } else {
            filters = this.getFilters(items);
        }

        //console.debug(items);
        //console.debug(filters);
        return (
            <div key={Math.random()*10} className="filter-criteria">
                <div key={Math.random()*10} className="filter-header">{title}</div>
                <ul key={Math.random()*10}>
                    {filters}
                </ul>
                <div key={Math.random()*10} className="clear"></div>
            </div>
        );
    }

}