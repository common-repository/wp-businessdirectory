Dropzone.autoDiscover = false;

function imageUploaderDropzone(dropZoneDiv,url,clickableButtons,MultiLanguageMessage,ImagePath,paralelUploadNumber,pictureAdder) {
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
                    if((file.height >= jbdUtils.gallery_height && file.width >= jbdUtils.gallery_width) || !jbdUtils.enable_resolution_check)
                        addPicture(ImagePath + name, name);
                    else
                        alert("["+name+"] "+JBD.JText._("LNG_IMAGE_SIZE_WARNING")+" (Width:"+jbdUtils.gallery_width+", Height:"+jbdUtils.gallery_height+")");
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

function photosNameFormater(imageName){
    var NameLength = imageName.length;
    if(NameLength > 14){
        return  imageName.substring(imageName.length - 14);
    }else{
        return imageName;
    }
}