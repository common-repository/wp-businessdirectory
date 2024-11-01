class Loading extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const loaderUrl = jbdUtils.getProperty('assetsUrl')+'images/loading-search.gif';

        return (
            <div class="search-loading">
                <img src={loaderUrl} alt="loading..." />
            </div>
        )
    }
}