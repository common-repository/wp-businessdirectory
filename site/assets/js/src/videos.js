/**
 * JBD Videos javascript class
 */
class JBDVideos {
    

    /**
     * Do not create recurring events and hide the options for recurring events
     */
     loadMore() {
        
        let moreVideosUrl = jbdUtils.getAjaxUrl('getMoreVideosAjax', 'video');
        let videoId = jQuery('#video-id').val();
        let categoryId = jQuery('#main-catetegory').val();
        let start = jQuery('#start').val();
        start = parseInt(start)

        jQuery.ajax({
            type: "GET",
            url: moreVideosUrl,
            data: {videoId: videoId, categoryId: categoryId, start: start},
            dataType: 'json',
            cache:false,
            success: function (data) {
                jQuery("#related-videos").append(data.data.videos);
                jQuery('#start').val(start + data.data.videosCount)
                if(!data.data.show_more){
                    jQuery("#load-more-btn").hide();
                }
            }
        });
    }
}

let jbdVideos = new JBDVideos();