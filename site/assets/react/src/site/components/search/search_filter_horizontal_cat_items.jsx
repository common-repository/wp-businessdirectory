class SearchFilterHorizontalCatItems extends React.Component {

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

        const liClassSub = "";
        // console.debug("selected items " + selectedItems);
        let values = [];
        if(selectedItems){
            if(selectedItems.toString().indexOf(",") != -1){
                values = selectedItems.toString().split(",").map(Number);
            }else{
                values = [parseInt(selectedItems)];
            }
        }

        const addFilterAction = jbdUtils.addFilterRule;
        const removeFilterAction = jbdUtils.removeFilterRule;

        return (
            <li key={Math.random()}>
                <div className="main-cat-container">
                    <div>
                        <div className="filter-main-cat cursor-pointer">
                            {title}
                        </div>
                    </div>
                    <i className="icon"></i>
                </div>
                <ul className="submenu" key={'horizontal-' +type}>
                    {
                        items.map((item) => {

                            if (item[valueField] != null) {

                                let action = addFilterAction;
                                let itemValue = parseInt(item[valueField]);

                                if (values.includes(itemValue)) {
                                    action = removeFilterAction;
                                }

                                return (
                                    <li key={Math.random()} className={liClassSub}>
                                        <div>
                                            <input className="cursor-pointer" name="cat" type="checkbox"  checked={values.includes(itemValue)}  onChange={() => action(type, item[valueField], true)} /> &nbsp;
                                            <a className="cursor-pointer" onClick={() => action(type, item[valueField], true)}>
                                                {item[nameField]}
                                            </a>
                                        </div>
                                    </li>
                                )
                            }
                        })
                    }
                </ul>
            </li>
        )
    }
}