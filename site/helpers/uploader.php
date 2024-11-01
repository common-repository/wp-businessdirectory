<?php
/**
 * @package    WBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2019 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

$app = JFactory::getApplication();
$input = $app->input;
$view = $input->get('view');
?>

<div id="attachment-item-template" style="display:none;">
    <li class="jbd-item" id="jbd-item-{attachment_id}">
        <div class="jupload-files">
            <div class="jupload-files-img">
                <i class="la la-file-text"></i>
            </div>
            <div class="jupload-files-info">
                <div class="jupload-filename">
                    <p>{attachment_path}</p>
                    <input id="jupload-filename-{attachment_id}" type="text"
                           name="attachment_name[]" value="{attachment_name}">
                </div>
                <div class="jupload-actions jbd-item-actions">
                    <label for="jupload-filename-{attachment_id}">
                        <i class="la la-pencil"></i>
                    </label>

                    <input type="hidden" name="attachment_status[]" id="attachment_status_{attachment_id}" value="{attachment_status}" />
                    <input type='hidden' name='attachment_path[]' id='attachment_path_{attachment_id}' value='{attachment_full_path}' />
                </div>
            </div>
        </div>
    </li>
</div>

<div id="picture-item-template" style="display:none;">
    <li class="jbd-item" id="jbd-item-{picture_id}">
        <div class="jupload-files">
            <div class="jupload-files-img">
                {picture_link}
            </div>
            <div class="jupload-files-info">
                <div class="jupload-filename">
                    <p>{picture_path}</p>
                    <input id="jupload-filename-{picture_id}" type="text"
                           name="picture_info[]" value="{picture_info}">
                </div>
                <div class="jupload-actions jbd-item-actions">
                    <label for="jupload-filename-{picture_id}">
                        <i class="la la-pencil"></i>
                    </label>

                    <input type="hidden" name="picture_enable[]" id="picture_enable_{picture_id}" value="{picture_enable}" />
                    <input type='hidden' name='picture_path[]' id='picture_path_{picture_id}' value='{picture_full_path}' />
                </div>
            </div>
        </div>
    </li>
</div>

<script type="text/javascript">
var view = '<?php echo $view; ?>';
var maxAttachments = '<?php echo (isset($this->item->package) &&  isset($this->item->package->max_attachments))?$this->item->package->max_attachments : $this->appSettings->max_attachments ?>';
var maxPictures;
var setIsBack = false;
var picturesUploaded = 0;

var picturesFolder = '<?php echo BD_PICTURES_PATH ?>';

function setIsBackEnd(){
    picturesUploaded = jQuery('input[name*="picture_path[]"]').length;
    setIsBack = true;
	checkNumberOfPictures();
}

function setMaxPictures(maxAllowedNumber){
    picturesUploaded = jQuery('input[name*="picture_path[]"]').length;
    maxPictures = maxAllowedNumber;
    checkNumberOfPictures();
}

function getMaxAllowedNumber(){
    return maxPictures;
}

function imageUploader(folderID, folderIDPath, type, picId) {
	if(type === undefined || type === null)
		type= '';
	if(picId === undefined || picId === null)
	    picId= '';
	jQuery("#"+type+"imageUploader"+picId).change(function()  {
		jQuery("#remove-image-loading").remove();
		jQuery("#"+type+"picture-preview"+picId).append('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>');
		jQuery("#item-form").validationEngine('detach');
		var fisRe = /^.+\.(jpg|bmp|gif|png|jpeg|PNG|JPG|GIF|JPEG)$/i;
		var path = jQuery(this).val();

		//if empty path stop the upload
		if(!path){
			return false;
		}
		
		if (path.search(fisRe) == -1) {
			jQuery("#remove-image-loading").remove();
			alert('JPG, JPEG, BMP, GIF, PNG only!');
			return false;
		}
		jQuery(this).upload(folderIDPath, function(responce)  {
			if( responce == '' ) {
				jQuery("#remove-image-loading").remove();
				alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
                if(jbdUtils.enable_resolution_check) {
                    var warning = jQuery(xml).find("warning").attr("value");
                    if (typeof warning !== 'undefined') {
                        jQuery("#remove-image-loading").remove();
                        var wHeight = jQuery(xml).find("warning").attr("height");
                        var wWidth = jQuery(xml).find("warning").attr("width");
                        alert("<?php echo JText::_("LNG_IMAGE_SIZE_WARNING",true) ?> (Width:" + wWidth + ", Height:" + wHeight + ")");
                        return false;
                    }
                }

				jQuery(xml).find("picture").each(function() {
					if(jQuery(this).attr("error") == 0 ) {
						setUpImage(
							folderID + jQuery(this).attr("path"),
							jQuery(this).attr("name"),
							type,
                            picId
						);
						jQuery("#remove-image-loading").remove();

						if(jbdUtils.enable_crop) {
                            showCropper(picturesFolder + folderID + jQuery(this).attr("path"), type, picId);
                        }
					}
					else if( jQuery(this).attr("error") == 1 )
						alert("<?php echo JText::_("LNG_FILE_ALLREADY_ADDED",true)?>");
					else if( jQuery(this).attr("error") == 2 )
						alert("<?php echo JText::_("LNG_ERROR_ADDING_FILE",true)?>");
					else if( jQuery(this).attr("error") == 3 )
						alert("<?php echo JText::_("LNG_ERROR_GD_LIBRARY",true)?>");
					else if( jQuery(this).attr("error") == 4 )
						alert("<?php echo JText::_("LNG_ERROR_RESIZING_FILE",true)?>");
				});
			}
		});
		jQuery("#item-form").validationEngine('attach');
	});
}

function setUpImage(path, name, type, picId) {
	jQuery("#"+type+"imageLocation"+picId).val(path);
	var img_new	= document.createElement('img');
	img_new.setAttribute('src', picturesFolder + path );
	img_new.setAttribute('id', 'itemImg');
	img_new.setAttribute('class', 'item-image');
	jQuery("#"+type+"picture-preview"+picId).empty();
	jQuery("#"+type+"picture-preview"+picId).append(img_new);
}

function markerUploader(folderID, folderIDPath) {
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
				alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
				jQuery(xml).find("picture").each(function() {
					if(jQuery(this).attr("error") == 0 ) {
						setUpMarker(
							folderID + jQuery(this).attr("path"),
							jQuery(this).attr("name")
						);
						jQuery("#remove-image-loading").remove();
					}
					else if( jQuery(this).attr("error") == 1 )
						alert("<?php echo JText::_('LNG_FILE_ALLREADY_ADDED',true)?>");
					else if( jQuery(this).attr("error") == 2 )
						alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
					else if( jQuery(this).attr("error") == 3 )
						alert("<?php echo JText::_('LNG_ERROR_GD_LIBRARY',true)?>");
					else if( jQuery(this).attr("error") == 4 )
						alert("<?php echo JText::_('LNG_ERROR_RESIZING_FILE',true)?>");
				});
			}
		});
		jQuery("#item-form").validationEngine('attach');
	});
}

function setUpMarker(path, name) {
	jQuery("#markerLocation").val(path);
	var img_new	= document.createElement('img');
	img_new.setAttribute('src', picturesFolder + path );
	img_new.setAttribute('id', 'markerImg');
	img_new.setAttribute('class', 'marker-image');
	jQuery("#marker-preview").empty();
	jQuery("#marker-preview").append(img_new);
}

function multiImageUploader(folder, folderPath) {
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
				alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
				if(jbdUtils.enable_resolution_check) {
                    var warning = jQuery(xml).find("warning").attr("value");
                    if (typeof warning !== 'undefined') {
                        jQuery("#remove-image-loading").remove();
                        var wHeight = jQuery(xml).find("warning").attr("height");
                        var wWidth = jQuery(xml).find("warning").attr("width");
                        alert("<?php echo JText::_("LNG_IMAGE_SIZE_WARNING",true) ?> (Width:" + wWidth + ", Height:" + wHeight + ")");
                        return false;
                    }
                }
				jQuery(xml).find("picture").each(function() {
					if(jQuery(this).attr("error") == 0 ) {
						addPicture(
							folder + jQuery(this).attr("path"),
							jQuery(this).attr("name")
						);
						jQuery("#remove-image-loading").remove();
					}
					else if( jQuery(this).attr("error") == 1 )
						alert("<?php echo JText::_('LNG_FILE_ALLREADY_ADDED',true)?>");
					else if( jQuery(this).attr("error") == 2 )
						alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
					else if( jQuery(this).attr("error") == 3 )
						alert("<?php echo JText::_('LNG_ERROR_GD_LIBRARY',true)?>");
					else if( jQuery(this).attr("error") == 4 )
						alert("<?php echo JText::_('LNG_ERROR_RESIZING_FILE',true)?>");
				});
				jQuery(this).val('');
			}
		}, 'html');
		jQuery("#item-form").validationEngine('attach');
	});
}

function removePicture(pos) {
	var lis=document.querySelectorAll('#sortable li');

	if(lis==null) {
		alert('Undefined List, contact administrator !');
	}

	if(pos >= lis.length)
		pos = lis.length-1;

	//lis[pos].parentNode.removeChild(lis[pos]);

    checkNumberOfPictures();
}

function removeAllPicture() {
    var lis=document.querySelectorAll('#sortable li');

    if(lis==null) {
        alert('Undefined List, contact administrator !');
    }

    var maxImages = lis.length;

    for (var i = 0; i < maxImages; i++) {
        var pos = i;

        if(pos >= lis.length)
            pos = lis.length-1;

        lis[pos].parentNode.removeChild(lis[pos]);
    }

    checkNumberOfPictures();
}

function btn_removefile(removePath) {
	jQuery('#btn_removefile').click(function() {
        jQuery("#item-form").validationEngine('detach');
		pos = jQuery('#crt_pos').val();
		path = jQuery('#crt_path').val();
		jQuery( this ).upload(removePath + path + '&_pos='+pos, function(responce) {
			if( responce =='' ) {
				alert("<?php echo JText::_('LNG_ERROR_REMOVING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
				jQuery(xml).find("picture").each(function() {
					if(jQuery(this).attr("error") == 0 ) {
						removePicture( jQuery(this).attr("pos") );
					}
					else if( jQuery(this).attr("error") == 2 ) {
						removePicture(pos);
					}
					else if( jQuery(this).attr("error") == 3 )
						alert("<?php echo JText::_('LNG_FILE_DOESNT_EXIST',true)?>");
				});
				jQuery('#crt_pos').val('');
				jQuery('#crt_path').val('');
			}
		}, 'html');
		jQuery("#item-form").validationEngine('detach');
	});
}

function multiFileUploader(folderID, folderIDPath) {
	jQuery("#multiFileUploader").change(function() {
		jQuery("#remove-file-loading").remove();
		jQuery("#attachment-list").find('.jbd-item-list').append('<p id="remove-file-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>');
		jQuery("#item-form").validationEngine('detach');
		var path = jQuery(this).val();
		jQuery(this).upload(folderIDPath, function(responce) {
			if( responce =='' ) {
				jQuery("#remove-file-loading").remove();
				alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
				jQuery("#remove-file-loading").remove();
				jQuery(xml).find("attachment").each(function() {
				    if(jQuery(this).attr("name").length > <?php echo MAX_FILENAME_LENGTH ?>) {
                        alert("<?php echo JText::_('LNG_FILENAME_TOO_LONG',true)?>");
                    }
					else if(jQuery(this).attr("error") == 0 ) {
						if(jQuery("#attachment-list #sortable-attachment li").length < maxAttachments) {
							addAttachment(
								folderID + jQuery(this).attr("path"),
								jQuery(this).attr("name")
							);
						jQuery("#multiFileUploader").val("");
						} else {
							alert("<?php echo JText::_('LNG_MAX_ATTACHMENTS_ALLOWED',true)?>"+maxAttachments);
						}
					}
					else if( jQuery(this).attr("info"))
						alert(jQuery(this).attr("info"));
					else {
						alert("<?php echo JText::_('LNG_ERROR_ADDING_FILE',true)?>");
					}
				});
			}
		}, 'html');
		jQuery("#item-form").validationEngine('attach');
	});
}

function addAttachment(path, name) {
    var attachTemplate = jQuery('#attachment-item-template').html();

    var newId = Math.random().toString(36).substring(7);
    var status = 1;

    attachTemplate = attachTemplate.replaceAll('{attachment_id}', newId);
    attachTemplate = attachTemplate.replaceAll('{attachment_name}', name);
    attachTemplate = attachTemplate.replaceAll('{attachment_path}', basename(path));
    attachTemplate = attachTemplate.replaceAll('{attachment_full_path}', path);
    attachTemplate = attachTemplate.replaceAll('{attachment_status}', status);

    jQuery('#attachment-list').find('.jbd-item-list').append(attachTemplate);

    jQuery('#attachment-list').jbdList({
        statusCallback: changeAttachmentStatus,
        deleteCallback: deleteAttachment,
        statusSelector: 'attachment_status_',
        deleteMsg: "<?php echo JText::_('LNG_CONFIRM_DELETE_ATTACHMENT') ?>"
    });
}

function addPicture(path, name) {
    var pictureTemplate = jQuery('#picture-item-template').html();

    var newId = Math.random().toString(36).substring(7);
    var status = 1;

    pictureTemplate = pictureTemplate.replaceAll('{picture_id}', newId);
    pictureTemplate = pictureTemplate.replaceAll('{picture_info}', '');//before was replaced by name
    pictureTemplate = pictureTemplate.replaceAll('{picture_path}', basename(path));
    pictureTemplate = pictureTemplate.replaceAll('{picture_full_path}', path);
    pictureTemplate = pictureTemplate.replaceAll('{picture_enable}', status);
    var link = "<?php echo BD_PICTURES_PATH; ?>"+path;
    var link = '<img src = \"'+link+'\">';
    pictureTemplate = pictureTemplate.replaceAll('{picture_link}', link);

    jQuery('#pictures-list').find('.jbd-item-list').append(pictureTemplate);

    jQuery('#pictures-list').jbdList({
        statusCallback: changePictureStatus,
        deleteCallback: deletePicture,
        statusSelector: 'picture_enable_',
        deleteMsg: "<?php echo JText::_('LNG_CONFIRM_DELETE_PICTURE') ?>"
    });

    checkNumberOfPictures();
}

function changeAttachmentStatus(id, oldVal) {
    var newVal = (oldVal == 0) ? 1 : 0;

    jQuery('#attachment_status_'+id).val(newVal);
}

function changePictureStatus(id, oldVal) {
    var newVal = (oldVal == 0) ? 1 : 0;

    jQuery('#picture_enable_'+id).val(newVal);
}

function deleteAttachment(id) {
    jQuery('#crt_path_a').val(jQuery('#attachment_path_'+id));
    jQuery('#btn_removefile_at').click();
}

function deletePicture(id) {
    jQuery('#crt_path').val(jQuery('#picture_path_'+id));
    jQuery('#btn_removefile').click();
}

function btn_removefile_at(removePath_at) {
	jQuery('#btn_removefile_at').click(function() {
		jQuery("#item-form").validationEngine('detach');
		pos = jQuery('#crt_pos_a').val();
		path = jQuery('#crt_path_a').val();
		jQuery(this).upload(removePath_at + path + '&_pos='+pos, function(responce) {
			if( responce =='' ) {
				alert("<?php echo JText::_('LNG_ERROR_REMOVING_FILE',true)?>");
				jQuery(this).val('');
			}
			else {
				var xml = responce;
				jQuery(xml).find("picture").each(function() {
					if(jQuery(this).attr("error") == 0 ) {
						// removeAttachment( jQuery(this).attr("pos") );
					}
					else if( jQuery(this).attr("error") == 2 )
						alert("<?php echo JText::_('LNG_ERROR_REMOVING_FILE',true)?>");
					else if( jQuery(this).attr("error") == 3 )
						alert("<?php echo JText::_('LNG_FILE_DOESNT_EXIST',true)?>");
				});
				jQuery('#crt_pos_a').val('');
				jQuery('#crt_path_a').val('');
			}
		}, 'html');
		jQuery("#item-form").validationEngine('detach');
	});
}

function removeCoverImage() {
	jQuery("#cover-imageLocation").val("");
	jQuery("#cover-picture-preview").html("<i class='la la-image'></i>");
	jQuery("#cover-imageUploader").val("");
}

function removeLogo() {
	jQuery("#imageLocation").val("");
	jQuery("#picture-preview").html("<i class='la la-image'></i>");
	jQuery("#imageUploader").val("");
}

function removeAd() {
    jQuery("#ad-imageLocation").val("");
    jQuery("#ad-picture-preview").html("<i class='la la-image'></i>");
    jQuery("#ad-imageUploader").val("");
}

function removeCompanyLogo() {
    jQuery("#company-imageLocation").val("");
    jQuery("#company-picture-preview").html("<i class='la la-image'></i>");
    jQuery("#company-imageUploader").val("");
}
/* Company & Conference & SessionLocation & Speaker */


function removeMarker() {
	jQuery("#markerLocation").val("");
	jQuery("#marker-preview").html("");
	jQuery("#markerfile").val("");
} 

function removeMapMarker() {
	jQuery("#mapimageLocation").val("");
	jQuery("#mappicture-preview").html("");
	jQuery("#mapimageUploader").val("");
}

function removeFeatureMapMarker() {
    jQuery("#fMarkerimageLocation").val("");
    jQuery("#fMarkerpicture-preview").html("");
    jQuery("#fMarkerimageUploader").val("");
}
/* Category */


function removeRow(id) {
	jQuery('#'+id).remove();
    checkNumberOfVideos();
    checkNumberOfSounds();
}

function checkNumberOfPictures() {
    //var nrPictures = jQuery('input[name*="picture_path[]"]').length;
    var nrPictures = jQuery('#pictures-list #sortable li').length;

    if (maxPictures <= nrPictures){
        jQuery("#file-upload").hide();
    }else{
        jQuery("#file-upload").show();

    }
}

</script>