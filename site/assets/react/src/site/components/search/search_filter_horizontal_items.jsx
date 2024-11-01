class SearchFilterHorizontalItems extends React.Component {

    constructor(props) {
        super(props);

        this.changeHandler = this.changeHandler.bind(this);
    }

    changeHandler(e) {
        console.debug("Change performed");
        jbdUtils.addFilterRule(this.props.type, e.target.value, e.target.options[e.target.selectedIndex].text);
        //this.props.fetchData();
    }

    render() {
        const nameField = this.props.nameField;
        const valueField = this.props.valueField;
        const selectedItems = typeof this.props.selectedItems !== "undefined" ? this.props.selectedItems : null;
        const type = this.props.type;
        const title = this.props.title;

        // console.debug(nameField);
        // console.debug(this.props.items);

        let itemDisabled = false;

        if(jQuery.isEmptyObject(this.props.items)){
            itemDisabled = true;
        }

        const items = Object.values(this.props.items);

        let selectedItem = null;
        if (selectedItems != null) {
            //selectedItem = selectedItems[0];
        }

        return (
            <div className="search-options-item">
                <div className="jbd-select-box">
                    <i className="la la-list"></i>
                    <select name={type} className="chosen-react" value={selectedItem} key={'horizontal-' +type} disabled={itemDisabled}
                            onChange={(e) => this.changeHandler(e)}>
                        <option value="">{title}</option>
                        {
                            items.map((item) => {
                                return (
                                    <option className={type+"-"+item[valueField]} value={item[valueField]}>{item[nameField]}</option>
                                )
                            })
                        }
                    </select>
                </div>
            </div>
        )
    }

}