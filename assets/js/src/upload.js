/**
 * JBD Upload javascript class
 */
class JBDUpload{

    /**
     * Constructor of the class
     *
     * @param params array params for the initialization of the class
     */
    constructor(params){
        this.setIsBack = false;

        this.folderIDs = [];

    	
        if (typeof params !== 'undefined') {
            if (typeof params['maxAttachments'] !== 'undefined') {
                this.maxAttachments = params['maxAttachments'];
            } else {
                this.maxAttachments = jbdUtils.getProperty("maxAttachments");
            }

            if (typeof params['maxPictures'] !== 'undefined') {
                this.maxPictures = params['maxPictures'];
            }

            if (typeof params['maxVideos'] !== 'undefined') {
                this.maxVideos = params['maxVideos'];
            }

            if (typeof params['removePath'] !== 'undefined') {
                this.removePath = params['removePath'];
            }

            if (typeof params['setIsBack'] !== 'undefined') {
                this.setIsBack = params['setIsBack'];
            }

            if (typeof params['picturesFolder'] !== 'undefined') {
                this.picturesFolder = params['picturesFolder'];
            } else {
                this.picturesFolder = jbdUtils.getProperty("imageBaseUrl");
            }
        } else {
            this.picturesFolder = jbdUtils.getProperty("imageBaseUrl");
            this.maxAttachments = jbdUtils.getProperty("maxAttachments");

            console.log('Upload parameters not defined. Initializing with default params.');
        }
    }

    /**
     * Set is backend because the process is done differently
     */
    setIsBackEnd()
    {
        picturesUploaded = jQuery('input[name*="picture_path[]"]').length;
        this.setIsBack = true;
        this.checkNumberOfPictures();
    }

    /**
     * Set Maximum number of allowed items to be uploaded
     *
     * @param maxAllowedNumber int maximum number of items that can be uploaded
     */
    setMaxPictures(maxAllowedNumber)
    {
        picturesUploaded = jQuery('input[name*="picture_path[]"]').length;
        this.maxPictures = maxAllowedNumber;
        this.checkNumberOfPictures();
    }

    /**
     * Check number of Pictures already uploaded and hide the dropzone if the max number is reached otherwise show it
     */
    checkNumberOfPictures()
    {
        //var nrPictures = jQuery('input[name*="picture_path[]"]').length;
        let nrPictures = jQuery('#pictures-list #sortable li').length;

        if (this.maxPictures <= nrPictures) {
            jQuery("#file-upload").hide();
        } else {
            jQuery("#file-upload").show();
        }
    }

    /**
     * Initiate the image uploader
     *
     * @param folderID string name of the folder where the image will be added
     * @param folderIDPath string url path where also are passed the settings for the image upload
     * @param type string type of the image that is being uploaded. Also control the div where the image will be shown on page
     * @param picId int picture ID
     */
    imageUploader(folderID, folderIDPath, type, picId)
    {
        let typeIndex = typeof type === 'undefined' ? 'default' : type;
        this.folderIDs[typeIndex] = {
            'folderID': folderID,
            'folderIDPath': folderIDPath
        };

        let self = this;

        if (type === undefined || type === null)
            type = '';
        if (picId === undefined || picId === null)
            picId = '';
        jQuery("#" + type + "imageUploader" + picId).change(function () {
            jQuery("#remove-image-loading").remove();
            jQuery("#" + type + "picture-preview" + picId).append('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>');
            jQuery("#item-form").validationEngine('detach');
            var fisRe = /^.+\.(jpg|bmp|gif|png|jpeg|PNG|JPG|GIF|JPEG)$/i;
            var path = jQuery(this).val();

            //if empty path stop the upload
            if (!path) {
                return false;
            }

            if (path.search(fisRe) == -1) {
                jQuery("#remove-image-loading").remove();
                alert('JPG, JPEG, BMP, GIF, PNG only!');
                return false;
            }
            jQuery(this).upload(folderIDPath, function (responce) {
                if (responce == '') {
                    jQuery("#remove-image-loading").remove();
                    alert(JBD.JText._('LNG_ERROR_ADDING_FILE'));
                    jQuery(this).val('');
                }
                else {
                    var xml = responce;
                    if (jbdUtils.getProperty("enable_resolution_check")) {
                        var warning = jQuery(xml).find("warning").attr("value");
                        if (typeof warning !== 'undefined') {
                            jQuery("#remove-image-loading").remove();
                            var wHeight = jQuery(xml).find("warning").attr("height");
                            var wWidth = jQuery(xml).find("warning").attr("width");
                            alert(JBD.JText._("LNG_IMAGE_SIZE_WARNING") + " (Width:" + wWidth + ", Height:" + wHeight + ")");
                            return false;
                        }
                    }

                    jQuery(xml).find("picture").each(function () {
                        if (jQuery(this).attr("error") == 0) {
                            self.setUpImage(
                                folderID + jQuery(this).attr("path"),
                                jQuery(this).attr("name"),
                                type,
                                picId
                            );
                            jQuery("#remove-image-loading").remove();

                            if (jbdUtils.getProperty('enable_crop')) {
                                self.showCropper(self.picturesFolder + folderID + jQuery(this).attr("path"), type, picId);
                            }
                        }
                        else if (jQuery(this).attr("error") == 1)
                            alert(JBD.JText._("LNG_FILE_ALLREADY_ADDED"));
                        else if (jQuery(this).attr("error") == 2)
                            alert(JBD.JText._("LNG_ERROR_ADDING_FILE"));
                        else if (jQuery(this).attr("error") == 3)
                            alert(JBD.JText._("LNG_ERROR_GD_LIBRARY"));
                        else if (jQuery(this).attr("error") == 4)
                            alert(JBD.JText._("LNG_ERROR_RESIZING_FILE"));
                    });
                }
            });
            jQuery("#item-form").validationEngine('attach');
        });
    }

    /**
     * Sets the image on the image placeholder on the page
     *
     * @param path string image path
     * @param name string image name
     * @param type string image type which is also used to get the id of the section where the image is set
     * @param picId string image id which also is used to get the id of the section where the image is set
     */
    setUpImage(path, name, type, picId)
    {
        console.debug(path);
        console.debug("#"+type+"imageLocation"+picId);
        jQuery("#"+type+"imageLocation"+picId).val(path);
        let img_new	= document.createElement('img');
        img_new.setAttribute('src', this.picturesFolder + path );
        img_new.setAttribute('id', 'itemImg');
        img_new.setAttribute('class', 'item-image');
        console.debug("#"+type+"picture-preview"+picId);
        jQuery("#"+type+"picture-preview"+picId).empty();
        jQuery("#"+type+"picture-preview"+picId).append(img_new);
        if (path == '/no_image.jpg'){
            //Reload the page and ignore the browser cache.
            window.location.reload(true);
        }
    }

    /**
     * Initiate the marker upload
     *
     * @param folderID string name of the folder where the image will be added
     * @param folderIDPath string url path where also are passed the settings for the image upload
     */
    markerUploader(folderID, folderIDPath)
    {
        let self = this;

        jQuery("#markerfile").change(function() {
            jQuery("#remove-image-loading").remove();
            jQuery("#marker-preview").append('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span></p>');
            jQuery("#item-form").validationEngine('detach');
            var fisRe 	= /^.+\.(jpg|bmp|gif|png)$/i;
            var path = jQuery(this).val();
            if (path.search(fisRe) == -1) {
                jQuery("#remove-image-loading").remove();
                alert(' JPG, BMP, GIF, PNG only!');
                return false;
            }
            jQuery(this).upload(folderIDPath, function(responce) {
                if( responce == '' ) {
                    jQuery("#remove-image-loading").remove();
                    alert(JBD.JText._('LNG_ERROR_ADDING_FILE'));
                    jQuery(this).val('');
                }
                else {
                    var xml = responce;
                    jQuery(xml).find("picture").each(function() {
                        if(jQuery(this).attr("error") == 0 ) {
                            self.setUpMarker(
                                folderID + jQuery(this).attr("path"),
                                jQuery(this).attr("name")
                            );
                            jQuery("#remove-image-loading").remove();
                        }
                        else if( jQuery(this).attr("error") == 1 )
                            alert(JBD.JText._('LNG_FILE_ALLREADY_ADDED'));
                        else if( jQuery(this).attr("error") == 2 )
                            alert(JBD.JText._('LNG_ERROR_ADDING_FILE'));
                        else if( jQuery(this).attr("error") == 3 )
                            alert(JBD.JText._('LNG_ERROR_GD_LIBRARY'));
                        else if( jQuery(this).attr("error") == 4 )
                            alert(JBD.JText._('LNG_ERROR_RESIZING_FILE'));
                    });
                }
            });
            jQuery("#item-form").validationEngine('attach');
        });
    }

    /**
     * Sets the image on the image placeholder on the page
     *
     * @param path string image path
     * @param name string image name
     */
    setUpMarker(path, name)
    {
        jQuery("#markerLocation").val(path);
        var img_new	= document.createElement('img');
        img_new.setAttribute('src', this.picturesFolder + path );
        img_new.setAttribute('id', 'markerImg');
        img_new.setAttribute('class', 'marker-image');
        jQuery("#marker-preview").empty();
        jQuery("#marker-preview").append(img_new);
    }

    /**
     * Initiate multi image uploader
     *
     * @param folder string name of the folder where the images will be added
     * @param folderPath string url path where also are passed the settings for the images upload
     */
    multiImageUploader(folder, folderPath)
    {
        let self = this;

        jQuery("#multiImageUploader").change(function() {
            jQuery("#remove-image-loading").remove();
            jQuery("#table_pictures").append('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span>Loading...</p>');
            jQuery("#item-form").validationEngine('detach');
            var fisRe = /^.+\.(jpg|bmp|gif|png|jpeg|PNG|JPG|GIF|JPEG)$/i;
            var path = jQuery(this).val();

            if (path.search(fisRe) == -1) {
                jQuery("#remove-image-loading").remove();
                alert(' JPG, JPEG, BMP, GIF, PNG only!');
                return false;
            }
            jQuery(this).upload(folderPath, function(responce) {
                if( responce =='' ) {
                    jQuery("#remove-image-loading").remove();
                    alert(JBD.JText._('LNG_ERROR_ADDING_FILE'));
                    jQuery(this).val('');
                }
                else {
                    var xml = responce;
                    if(jbdUtils.getProperty("enable_resolution_check")) {
                        var warning = jQuery(xml).find("warning").attr("value");
                        if (typeof warning !== 'undefined') {
                            jQuery("#remove-image-loading").remove();
                            var wHeight = jQuery(xml).find("warning").attr("height");
                            var wWidth = jQuery(xml).find("warning").attr("width");
                            alert(JBD.JText._("LNG_IMAGE_SIZE_WARNING")+" (Width:" + wWidth + ", Height:" + wHeight + ")");
                            return false;
                        }
                    }
                    jQuery(xml).find("picture").each(function() {
                        if(jQuery(this).attr("error") == 0 ) {
                            self.addPicture(
                                folder + jQuery(this).attr("path"),
                                jQuery(this).attr("name")
                            );
                            jQuery("#remove-image-loading").remove();
                        }
                        else if( jQuery(this).attr("error") == 1 )
                            alert(JBD.JText._('LNG_FILE_ALLREADY_ADDED'));
                        else if( jQuery(this).attr("error") == 2 )
                            alert(JBD.JText._('LNG_ERROR_ADDING_FILE'));
                        else if( jQuery(this).attr("error") == 3 )
                            alert(JBD.JText._('LNG_ERROR_GD_LIBRARY'));
                        else if( jQuery(this).attr("error") == 4 )
                            alert(JBD.JText._('LNG_ERROR_RESIZING_FILE'));
                    });
                    jQuery(this).val('');
                }
            }, 'html');
            jQuery("#item-form").validationEngine('attach');
        });
    }

    /**
     * Removes the image from the list by its position
     *
     * @param pos int position of the image where to find it on the list
     */
    removePicture(pos)
    {
        let lis = document.querySelectorAll('#sortable li');

        if (lis==null) {
            alert('Undefined List, contact administrator !');
        }

        if(pos >= lis.length)
            pos = lis.length-1;

        //lis[pos].parentNode.removeChild(lis[pos]);

        this.checkNumberOfPictures();
    }

    /**
     * Get the list where the images are saved and removes them all
     */
    removeAllPicture()
    {
        let lis=document.querySelectorAll('#sortable li');

        if (lis==null) {
            alert('Undefined List, contact administrator !');
        }

        let maxImages = lis.length;

        for (let i = 0; i < maxImages; i++) {
            let pos = i;

            if (pos >= lis.length)
                pos = lis.length-1;

            lis[pos].parentNode.removeChild(lis[pos]);
        }

        this.checkNumberOfPictures();
    }

    /**
     * Initialize a click event for the button. When it is clicked removes the file from the path it is saved and
     * empties all fields of form related with it
     */
    btn_removefile()
    {
        let self = this;

        jQuery('#btn_removefile').click(function() {
            jQuery("#item-form").validationEngine('detach');
            let pos = jQuery('#crt_pos').val();
            let path = jQuery('#crt_path').val();
            jQuery(this).upload(this.removePath + path + '&_pos='+pos, function(responce) {
                if( responce =='' ) {
                    alert(JBD.JText._('LNG_ERROR_REMOVING_FILE'));
                    jQuery(this).val('');
                }
                else {
                    var xml = responce;
                    jQuery(xml).find("picture").each(function() {
                        if(jQuery(this).attr("error") == 0 ) {
                            self.removePicture( jQuery(this).attr("pos") );
                        }
                        else if( jQuery(this).attr("error") == 2 ) {
                            self.removePicture(pos);
                        }
                        else if( jQuery(this).attr("error") == 3 )
                            alert(JBD.JText._('LNG_FILE_DOESNT_EXIST'));
                    });
                    jQuery('#crt_pos').val('');
                    jQuery('#crt_path').val('');
                }
            }, 'html');
            jQuery("#item-form").validationEngine('detach');
        });
    }

    /**
     * Initialize a javascript event for the multi File Uploader, so when its value is changed(files has been added to be uploaded),
     * it uploads the file and appends it to the list
     *
     * @param folderID string folder name where this file will be added
     * @param folderIDPath string path to where this file will be founded
     */
    multiFileUploader(folderID, folderIDPath)
    {
        let self = this;

        jQuery("#multiFileUploader").change(function() {
            jQuery("#remove-file-loading").remove();
            jQuery("#attachment-list").find('.jbd-item-list').append('<p id="remove-file-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>');
            jQuery("#item-form").validationEngine('detach');
            var path = jQuery(this).val();
            jQuery(this).upload(folderIDPath, function(responce) {
                if( responce =='' ) {
                    jQuery("#remove-file-loading").remove();
                    alert(JBD.JText._('LNG_ERROR_ADDING_FILE'));
                    jQuery(this).val('');
                }
                else {
                    var xml = responce;
                    jQuery("#remove-file-loading").remove();
                    jQuery(xml).find("attachment").each(function() {
                        if(jQuery(this).attr("name").length > jbdUtils.getProperty("maxFilenameLength")) {
                            alert(JBD.JText._('LNG_FILENAME_TOO_LONG'));
                        }
                    else if(jQuery(this).attr("error") == 0 ) {
                            if(jQuery("#attachment-list #sortable-attachment li").length < self.maxAttachments) {
                                self.addAttachment(
                                    folderID + jQuery(this).attr("path"),
                                    jQuery(this).attr("name")
                                );
                                jQuery("#multiFileUploader").val("");
                            } else {
                                alert(JBD.JText._('LNG_MAX_ATTACHMENTS_ALLOWED')+self.maxAttachments);
                            }
                        }
                        else if( jQuery(this).attr("info"))
                            alert(jQuery(this).attr("info"));
                        else {
                            alert(JBD.JText._('LNG_ERROR_ADDING_FILE'));
                        }
                    });
                }
            }, 'html');
            jQuery("#item-form").validationEngine('attach');
        });
    }

    /**
     * After the attach has been uploaded, display it and append it to the list
     *
     * @param path string path where the attach has been added
     * @param name string attach name
     */
    addAttachment(path, name)
    {
        let self = this;

        var attachTemplate = jQuery('#attachment-item-template').html();

        var newId = Math.random().toString(36).substring(7);
        var status = 1;

        attachTemplate = attachTemplate.replaceAll('{attachment_id}', newId);
        attachTemplate = attachTemplate.replaceAll('{attachment_name}', name);
        attachTemplate = attachTemplate.replaceAll('{attachment_path}', jbdUtils.basename(path));
        attachTemplate = attachTemplate.replaceAll('{attachment_full_path}', path);
        attachTemplate = attachTemplate.replaceAll('{attachment_status}', status);

        jQuery('#attachment-list').find('.jbd-item-list').append(attachTemplate);

        jQuery('#attachment-list').jbdList({
            statusCallback: self.changeAttachmentStatus,
            deleteCallback: self.deleteAttachment,
            statusSelector: 'attachment_status_',
            deleteMsg: JBD.JText._('LNG_CONFIRM_DELETE_ATTACHMENT')
        });
    }

    /**
     * After the image has been uploaded, display it and append it to the list
     *
     * @param path string path where the image has been added
     * @param name string image name
     */
    addPicture(path, name)
    {
        let self = this;

        var pictureTemplate = jQuery('#picture-item-template').html();

        var newId = Math.random().toString(36).substring(7);
        var status = 1;

        pictureTemplate = pictureTemplate.replaceAll('{picture_id}', newId);
        pictureTemplate = pictureTemplate.replaceAll('{picture_title}', '');
        pictureTemplate = pictureTemplate.replaceAll('{picture_info}', '');//before was replaced by name
        pictureTemplate = pictureTemplate.replaceAll('{picture_path}', jbdUtils.basename(path));
        pictureTemplate = pictureTemplate.replaceAll('{picture_full_path}', path);
        pictureTemplate = pictureTemplate.replaceAll('{picture_enable}', status);
        var link = this.picturesFolder+path;
        var link = '<img src="'+link+'">';
        pictureTemplate = pictureTemplate.replaceAll('{picture_link}', link);

        jQuery('#pictures-list').find('.jbd-item-list').append(pictureTemplate);

        jQuery('#pictures-list').jbdList({
            statusCallback: self.changePictureStatus,
            deleteCallback: self.deletePicture,
            statusSelector: 'picture_enable_',
            deleteMsg: JBD.JText._('LNG_CONFIRM_DELETE_PICTURE')
        });

        this.checkNumberOfPictures();
    }

    /**
     * Enable or Disable the attach status
     *
     * @param id int id of the attach. It is used to find the attach location
     * @param oldVal int old Value that was the attach status and change it to the new one
     */
    changeAttachmentStatus(id, oldVal)
    {
        var newVal = (oldVal == 0) ? 1 : 0;

        jQuery('#attachment_status_'+id).val(newVal);
    }

    /**
     * Enable or Disable the image status
     *
     * @param id int id of the image. It is used to find the image location
     * @param oldVal int old Value that was the image status and change it to the new one
     */
    changePictureStatus(id, oldVal)
    {
        var newVal = (oldVal == 0) ? 1 : 0;

        jQuery('#picture_enable_'+id).val(newVal);
    }

    /**
     * Empty the attachments path from form and clicks the button
     */
    deleteAttachment(id)
    {
        jQuery('#crt_path_a').val(jQuery('#attachment_path_'+id));
        jQuery('#btn_removefile_at').click();
    }

    /**
     * Empty the picture path from form and clicks the button
     */
    deletePicture(id)
    {
        jQuery('#crt_path').val(jQuery('#picture_path_'+id));
        jQuery('#btn_removefile').click();
    }

    /**
     * Initialize a click event for the button. When it is clicked removes the file from the path it is saved and
     * empties all fields of form related with it
     *
     * @param removePath_at string path where to find the file
     */
    btn_removefile_at(removePath_at)
    {
        if (typeof removePath_at === "undefined") {
            removePath_at = this.removePath;
        }

        jQuery('#btn_removefile_at').click(function() {
            jQuery("#item-form").validationEngine('detach');
            pos = jQuery('#crt_pos_a').val();
            path = jQuery('#crt_path_a').val();
            jQuery(this).upload(removePath_at + path + '&_pos='+pos, function(responce) {
                if (responce == '') {
                    alert(JBD.JText._('LNG_ERROR_REMOVING_FILE'));
                    jQuery(this).val('');
                }
                else {
                    var xml = responce;
                    jQuery(xml).find("picture").each(function() {
                        if(jQuery(this).attr("error") == 0 ) {
                            // removeAttachment( jQuery(this).attr("pos") );
                        }
                        else if( jQuery(this).attr("error") == 2 )
                            alert(JBD.JText._('LNG_ERROR_REMOVING_FILE'));
                        else if( jQuery(this).attr("error") == 3 )
                            alert(JBD.JText._('LNG_FILE_DOESNT_EXIST'));
                    });
                    jQuery('#crt_pos_a').val('');
                    jQuery('#crt_path_a').val('');
                }
            }, 'html');
            jQuery("#item-form").validationEngine('detach');
        });
    }

    /**
     * Removes image and empty all fields related with it
     */
    removeCoverImage()
    {
        jQuery("#cover-imageLocation").val("");
        jQuery("#cover-picture-preview").html("<i class='la la-image'></i>");
        jQuery("#cover-imageUploader").val("");
    }

    /**
     * Removes image and empty all fields related with it
     */
    removeLogo() {
        jQuery("#imageLocation").val("");
        jQuery("#picture-preview").html("<i class='la la-image'></i>");
        jQuery("#imageUploader").val("");
    }

    /**
     * Removes image and empty all fields related with it
     */
    removeAd()
    {
        jQuery("#ad-imageLocation").val("");
        jQuery("#ad-picture-preview").html("<i class='la la-image'></i>");
        jQuery("#ad-imageUploader").val("");
    }

    /**
     * Removes image and empty all fields related with it
     */
    removeCompanyLogo()
    {
        jQuery("#company-imageLocation").val("");
        jQuery("#company-picture-preview").html("<i class='la la-image'></i>");
        jQuery("#company-imageUploader").val("");
    }
    /* Company & Conference & SessionLocation & Speaker */

    /**
     * Removes image and empty all fields related with it
     */
    removeMarker() {
        jQuery("#markerLocation").val("");
        jQuery("#marker-preview").html("");
        jQuery("#markerfile").val("");
    }

    /**
     * Removes image and empty all fields related with it
     */
    removeMapMarker()
    {
        jQuery("#mapimageLocation").val("");
        jQuery("#mappicture-preview").html("");
        jQuery("#mapimageUploader").val("");
    }

    /**
     * Removes image and empty all fields related with it
     */
    removeFeatureMapMarker()
    {
        jQuery("#fMarkerimageLocation").val("");
        jQuery("#fMarkerpicture-preview").html("");
        jQuery("#fMarkerimageUploader").val("");
    }
    /* Category */

    /**
     * Removes videos or sounds based on the id of the div that contain the item
     * @param id
     */
    removeRow(id)
    {
        jQuery('#'+id).remove();
        self.checkNumberOfVideos();
        self.checkNumberOfSounds();
    }

    /**
     * Remove Service logo and empty all fields related with it on the form
     *
     * @param id int logo id, used also to get the fields that are used to display the image
     */
    removeServiceLogo(id)
    {
        jQuery('#service-imageLocation' + id).val("");
        jQuery('#service-picture-preview' + id).html("<i class='la la-image'></i>");
        jQuery('#service-imageUploader' + id).val("");
    }

    /**
     * Show the cropper modal with the image that will be cropped inside
     *
     * @param dataUri string image URI
     * @param type string image type
     * @param picId int image id
     */
    showCropper(dataUri, type, picId)
    {
        let self = this;

        if (typeof this.cropper !== 'undefined')
            this.cropper.destroy();
        let cropped = false;

        if (picId === undefined || picId === null)
            picId = '';

        jQuery('#cropper-modal').jbdModal();

        jQuery('#cropper-image').attr('src', '');
        jQuery('#cropper-image').attr('src', dataUri);
        jQuery('#save-cropped').unbind('click');
        jQuery('#save-cropped').on("click", function (event) {
            self.saveCropped(type, picId);
        });

        var width;
        var height;
        if (type.length == 0) {
            this.removeLogo();
            width = jbdUtils.getProperty("logo_width");
            height = jbdUtils.getProperty("logo_height");
        }
        else if (type === 'cover-') {
            width = jbdUtils.getProperty("cover_width");
            height = jbdUtils.getProperty("cover_height");
            this.removeCoverImage();
        }
        else if (type === 'service-') {
            width = jbdUtils.getProperty("gallery_width");
            height = jbdUtils.getProperty("gallery_height");
            this.removeServiceLogo(picId);
        }
        else {
            width = jbdUtils.getProperty("gallery_width");
            height = jbdUtils.getProperty("gallery_height");
        }

        width = parseInt(width);
        height = parseInt(height);

        let left = 0;
        if (width < 490) {
            left = (490-width) / 2;
        }

        var image = document.getElementById('cropper-image');
        this.cropper = new Cropper(image, {
            aspectRatio: width / height,
            cropBoxResizable: false,
            dragMode: 'move',
            scalable: true,
            data: {
                width: width,
                height: height,
                left: left,
                top: 0
            },
            crop: function (e) {
            }
        });
    }

    /**
     * Save cropped image
     *
     * @param type string image type
     * @param picId int ID that will be used where to set the image
     */
    saveCropped(type, picId)
    {
        let self = this;

        this.cropper.getCroppedCanvas({
            fillColor: '#fff',
        }).toBlob(function (blob) {
            var formData = new FormData();
            blob['name'] = 'cropped.' + blob['type'].substr(blob['type'].indexOf('/') + 1, blob.type.length);
            formData.append('croppedimage', blob);

            let folderID = '';
            let submitPath = '';

            if (type.length == 0) {
                folderID = self.folderIDs['default'].folderID;
                submitPath = self.folderIDs['default'].folderIDPath;
            } else {
                folderID = self.folderIDs[type].folderID;
                submitPath = self.folderIDs[type].folderIDPath;
            }

            submitPath += '&crop=1';
            jQuery.ajax(submitPath, {
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (xml) {
                    jQuery(xml).find("picture").each(function () {
                        if (jQuery(this).attr("error") == 0) {
                            self.setUpImage(
                                folderID + jQuery(this).attr("path"),
                                jQuery(this).attr("name"),
                                type,
                                picId
                            );
                            jQuery("#remove-image-loading").remove();
                        }
                        else if (jQuery(this).attr("error") == 1)
                            alert(JBD.JText._('LNG_FILE_ALLREADY_ADDED'));
                        else if (jQuery(this).attr("error") == 2)
                            alert(JBD.JText._('LNG_ERROR_ADDING_FILE'));
                        else if (jQuery(this).attr("error") == 3)
                            alert(JBD.JText._('LNG_ERROR_GD_LIBRARY'));
                        else if (jQuery(this).attr("error") == 4)
                            alert(JBD.JText._('LNG_ERROR_RESIZING_FILE'));
                    });

                    jQuery.jbdModal.close();
                    self.cropper.destroy();
                },
                error: function () {
                    console.log('Upload error');
                }
            });
        },"image/jpeg", 0.8, );
    }

    /**
     * Initiate image uploader dropzone
     *
     * @param dropZoneDiv string id of the div of the dropzone
     * @param url string url with the path and settings passed to it for the image upload
     * @param clickableButtons string button that will be clicked
     * @param MultiLanguageMessage string Text to upload
     * @param ImagePath string folder where the image will be saved
     * @param paralelUploadNumber int number of how many can be uploaded at the same time
     * @param pictureAdder javascript function to call for image adder
     */
    imageUploaderDropzone(dropZoneDiv,url,clickableButtons,MultiLanguageMessage,ImagePath,paralelUploadNumber,pictureAdder)
    {
        let self = this;
        Dropzone.autoDiscover = false;

        jQuery(dropZoneDiv).dropzone({
            url: url,
            addRemoveLinks: true,
            acceptedFiles:'image/gif,.jpg,.jpeg,.png',
            maxFilesize: 10, // MB
            enqueueForUpload: true,
            dictRemoveFile: "Remove Preview",
            autoProcessQueue: true,
            parallelUploads: paralelUploadNumber,
            dictDefaultMessage: MultiLanguageMessage,
            clickable: clickableButtons,

            // The setting up of the dropzone
            init: function () {
                var myDropzone = this;
                jQuery("#submitAll").click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // var images = myDropzone.getQueuedFiles();
                    //console.log(images);

                    myDropzone.processQueue();
                    jQuery('button').each(function () {
                        jQuery(this).remove('#add');
                    });
                });
                /* this.on("addedfile", function (file) {
                    var addButton = Dropzone.createElement("<button id='add' class='btn btn-primary start'>Upload</button>");
                    addButton.addEventListener("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        myDropzone.processFile(file);
                        file.previewElement.classList.add("dz-success");
                        jQuery(this).remove();
                    });
                    file.previewElement.appendChild(addButton);
                }); */
                // this.on("thumbnail", function(file, dataUri) {
                //     var cropButton = Dropzone.createElement("<button id='add' class='btn btn-primary start'>Crop</button>");
                //
                //     if(file.width > 500 ||  file.height > 500) {
                //         cropButton.addEventListener("click", function (e) {
                //             e.preventDefault();
                //             e.stopPropagation();
                //             showImage(file.width, file.height, dataUri);
                //         });
                //         file.previewElement.appendChild(cropButton);
                //     }
                // });
            },
            success: function (file, response) {
                var xml = response;
                var name;
                name = file.name.replace(/[^0-9a-zA-Z.]/g, '_');
                file.previewElement.classList.add("dz-success");
                switch (pictureAdder){
                    case "addPicture":
                        if((file.height >= jbdUtils.getProperty('gallery_height') && file.width >= jbdUtils.getProperty('gallery_width')) || !jbdUtils.getProperty("enable_resolution_check"))
                            self.addPicture(ImagePath + name, name);
                        else
                            alert("["+name+"] "+JBD.JText._("LNG_IMAGE_SIZE_WARNING")+" (Width:"+jbdUtils.getProperty('gallery_width')+", Height:"+jbdUtils.getProperty('gallery_height')+")");
                        break;
                    case "setUpLogo":
                        setUpLogo(name);
                        break;
                    case "setUpLogoExtraOptions":
                        setUpLogoExtraOptions(ImagePath + name,name);
                        break;
                    default :
                        alert("Error! no image creation function defined for this view");
                        console.log("no image creation function defined");
                        break;
                }
            },
            error: function (file, response) {
                file.previewElement.classList.add("dz-error");
                console.log(response);
            }
        });
    }

    /**
     * Get partly the name up to 14 chars
     *
     * @param imageName string image name
     * @returns {string|*}
     */
    photosNameFormater(imageName)
    {
        var NameLength = imageName.length;
        if (NameLength > 14) {
            return  imageName.substring(imageName.length - 14);
        } else {
            return imageName;
        }
    }

    /**
     * Add a new video section to upload the video
     */
    addVideo()
    {
        var count = jQuery("#video-container").children().length + 1;
        let id = 0;
        var outerDiv = document.createElement('div');
        outerDiv.setAttribute('class', 'video-item');
        outerDiv.setAttribute('id', 'detailBox' + count);

        var newLabel = document.createElement('label');
        newLabel.setAttribute("for", id);
        newLabel.innerHTML = JBD.JText._('LNG_VIDEO');

        var cointainerDiv = document.createElement('div');
        cointainerDiv.setAttribute('class', 'input-group');

        var newInput = document.createElement('input');
        newInput.setAttribute('name', 'videos[]');
        newInput.setAttribute('type', 'text');
        newInput.setAttribute('id', id);
        newInput.setAttribute('class', 'form-control');

        var appendDiv = document.createElement('div');
        appendDiv.setAttribute('class', 'input-group-append');

        var newButton = document.createElement('button');
        newButton.setAttribute('class', 'btn btn-secondary');
        newButton.setAttribute('onclick', 'uploadInstance.removeRow("detailBox' + count + '");checkNumberOfVideos();');

        var newIcon = document.createElement('i');

        newIcon.setAttribute('class', 'la la-lg la-remove');

        newButton.appendChild(newIcon);
        appendDiv.appendChild(newButton);

        cointainerDiv.appendChild(newInput);
        cointainerDiv.appendChild(appendDiv);

        outerDiv.appendChild(newLabel);
        outerDiv.appendChild(cointainerDiv);

        var facilityContainer = jQuery("#video-container");
        facilityContainer.append(outerDiv);

        this.checkNumberOfVideos();
    }

    /**
     * Check maximum number of videos uploaded. If maximum number is reached then hide the uploader
     */
    checkNumberOfVideos()
    {
        var nrVideos = jQuery('input[name*="videos[]"]').length;

        if (nrVideos < this.maxVideos) {
            jQuery("#add-video").show();
        }
        else {
            jQuery("#add-video").hide();
        }
    }

    /**
     * Add a sound div next to the last one
     */
    addSound()
    {
        var count = jQuery("#sound-container").children().length + 1;
        let id = 0;
        var outerDiv = document.createElement('div');
        outerDiv.setAttribute('class', 'detail_box');
        outerDiv.setAttribute('id', 'soundDetailBox' + count);

        var newLabel = document.createElement('label');
        newLabel.setAttribute("for", id);
        newLabel.innerHTML = JBD.JText._('LNG_SOUND');

        var newInput = document.createElement('textarea');
        newInput.setAttribute('name', 'sounds[]');
        newInput.setAttribute('id', id);
        newInput.setAttribute('class', 'input_txt');
        newInput.setAttribute('rows', '3');

        var img_del = document.createElement('img');
        img_del.setAttribute('src', jbdUtils.getProperty("imageRepo") + "/assets/images/del_icon.png");
        img_del.setAttribute('alt', 'Delete option');
        img_del.setAttribute('height', '12px');
        img_del.setAttribute('width', '12px');
        img_del.setAttribute('align', 'left');
        img_del.setAttribute('onclick', 'uploadInstance.removeRow("soundDetailBox' + count + '")');
        img_del.setAttribute('style', "cursor: pointer; margin:3px;");

        var clearDiv = document.createElement('div');
        clearDiv.setAttribute('class', 'clear');

        outerDiv.appendChild(newLabel);
        outerDiv.appendChild(newInput);
        outerDiv.appendChild(img_del);
        outerDiv.appendChild(clearDiv);

        var facilityContainer = jQuery("#sound-container");
        facilityContainer.append(outerDiv);

        this.checkNumberOfSounds();
    }

    /**
     * Check number of uploaded sounds. If maximum number of sounds is reached then hide the uploader
     */
    checkNumberOfSounds()
    {
        var nrSounds = jQuery('textarea[name*="sounds[]"]').length;
        if (nrSounds < 15) {
            jQuery("#add-sound").show();
        }
        else {
            jQuery("#add-sound").hide();
        }
    }

    /**
     * Remove specific item by catching its row id and removing it
     *
     * @param id int Row ID
     */
    removeRow(id)
    {
        jQuery('#' + id).remove();
        this.checkNumberOfVideos();
        this.checkNumberOfSounds();
    }
}

class JBDUploadHelper
{
    static getUploadInstance(params)
    {
        if (typeof params !== 'undefined') {
            if (typeof params['maxPictures'] !== 'undefined') {
                JBDUploadHelper.maxPictures = params['maxPictures'];
            }
        }

        return new JBDUpload(params);
    }

    static getMaxAllowedNumber()
    {
        return JBDUploadHelper.maxPictures;
    }
}