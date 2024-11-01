class SearchFilterHorizontalItems extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {
        const nameField = this.props.nameField;
        const valueField = this.props.valueField;
        const selectedItems = typeof this.props.selectedItems !== "undefined" ? this.props.selectedItems : null;
        const type = this.props.type;
        const title = this.props.title;

        const items = Object.values(this.props.items);

        let selectedItem = null;
        if (selectedItems != null) {
            selectedItem = selectedItems[0];
        }

        return (
            <div className="search-options-item">
                <div className="jbd-select-box">
                    <i className="la la-list"></i>
                    <select name={type} className="chosen-react" value={selectedItem}
                            onChange={() => jbdUtils.addFilterRule(type, this.value)}>
                        <option value="">{title}</option>
                        {
                            items.map((item) => {
                                return (
                                    <option value={item[valueField]}>{item[nameField]}</option>
                                )
                            })
                        }
                    </select>
                </div>
            </div>
        )
    }

}