<?php
/**
 * @package     JBD.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 *
 * @var   string   $preview         The preview image relative path
 * @var   integer  $previewHeight   The image preview height
 * @var   integer  $previewWidth    The image preview width
 * @var   string   $asset           The asset text
 * @var   string   $authorField     The label text
 * @var   string   $folder          The folder text
 * @var   string   $link            The link text
 */
extract($displayData);


$attr = '';

$attr .= ' title="' . htmlspecialchars('<span id="TipImgpath"></span>', ENT_COMPAT, 'UTF-8') . '"';

// Initialize some field attributes.
$attr .= !empty($class) ? ' class="input-small field-media-input ' . $class . '"' : ' class="input-small"';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';

// Initialize JavaScript field attributes.
$attr .= !empty($onchange) ? ' onchange="' . $onchange . '"' : '';

list( $path, $url ) =  dirname( __FILE__ );

wp_enqueue_media();

wp_enqueue_style( 'rwmb-media',  WP_BUSINESSDIRECTORY_URL.'includes/mvc/css/image/media.css' );
wp_enqueue_script( 'rwmb-media', WP_BUSINESSDIRECTORY_URL.'includes/mvc/js/image/media.js', array( 'jquery-ui-sortable', 'underscore', 'backbone', 'media-grid' ), "1.0", true );

wp_enqueue_style( 'rwmb-image',  WP_BUSINESSDIRECTORY_URL.'includes/mvc/css/image/image.css');
wp_enqueue_script( 'rwmb-image-advanced',  WP_BUSINESSDIRECTORY_URL.'includes/mvc/js/image/image-advanced.js');

$js_options = array(
    'imageSize' =>'thumbnail',
    'mimeType' => 'image',
    'maxFiles' =>  (int)$maxitems,
    'forceDelete' => false,
    'maxStatus' => false
);

?>
<div id="image-upload-message"></div>
<script type='text/javascript'>
/* <![CDATA[ */
var i18nRwmbMedia = {"add":"+ Add Media","single":" file","multiple":" files","remove":"Remove","edit":"Edit","view":"View","noTitle":"No Title","loadingUrl":"http:\/\/localhost\/ydhr\/wp-admin\/images\/spinner.gif","extensions":{"image\/jpeg":["jpg","jpeg","jpe"],"image":["jpg","jpeg","jpe","gif","png","bmp","tiff","tif","ico"],"image\/*":["jpg","jpeg","jpe","gif","png","bmp","tiff","tif","ico"],"image\/gif":["gif"],"image\/png":["png"],"image\/bmp":["bmp"],"image\/tiff":["tiff","tif"],"image\/x-icon":["ico"],"video\/x-ms-asf":["asf","asx"],"video":["asf","asx","wmv","wmx","wm","avi","divx","flv","mov","qt","mpeg","mpg","mpe","mp4","m4v","ogv","webm","mkv","3gp","3gpp","3g2","3gp2"],"video\/*":["asf","asx","wmv","wmx","wm","avi","divx","flv","mov","qt","mpeg","mpg","mpe","mp4","m4v","ogv","webm","mkv","3gp","3gpp","3g2","3gp2"],"video\/x-ms-wmv":["wmv"],"video\/x-ms-wmx":["wmx"],"video\/x-ms-wm":["wm"],"video\/avi":["avi"],"video\/divx":["divx"],"video\/x-flv":["flv"],"video\/quicktime":["mov","qt"],"video\/mpeg":["mpeg","mpg","mpe"],"video\/mp4":["mp4","m4v"],"video\/ogg":["ogv"],"video\/webm":["webm"],"video\/x-matroska":["mkv"],"video\/3gpp":["3gp","3gpp"],"video\/3gpp2":["3g2","3gp2"],"text\/plain":["txt","asc","c","cc","h","srt"],"text":["txt","asc","c","cc","h","srt","csv","tsv","ics","rtx","css","htm","html","vtt"],"text\/*":["txt","asc","c","cc","h","srt","csv","tsv","ics","rtx","css","htm","html","vtt"],"text\/csv":["csv"],"text\/tab-separated-values":["tsv"],"text\/calendar":["ics"],"text\/richtext":["rtx"],"text\/css":["css"],"text\/html":["htm","html"],"text\/vtt":["vtt"],"application\/ttaf+xml":["dfxp"],"application":["dfxp","rtf","js","pdf","swf","class","tar","zip","gz","gzip","rar","7z","exe","psd","xcf","doc","pot","pps","ppt","wri","xla","xls","xlt","xlw","mdb","mpp","docx","docm","dotx","dotm","xlsx","xlsm","xlsb","xltx","xltm","xlam","pptx","pptm","ppsx","ppsm","potx","potm","ppam","sldx","sldm","onetoc","onetoc2","onetmp","onepkg","oxps","xps","odt","odp","ods","odg","odc","odb","odf","wp","wpd","key","numbers","pages"],"application\/*":["dfxp","rtf","js","pdf","swf","class","tar","zip","gz","gzip","rar","7z","exe","psd","xcf","doc","pot","pps","ppt","wri","xla","xls","xlt","xlw","mdb","mpp","docx","docm","dotx","dotm","xlsx","xlsm","xlsb","xltx","xltm","xlam","pptx","pptm","ppsx","ppsm","potx","potm","ppam","sldx","sldm","onetoc","onetoc2","onetmp","onepkg","oxps","xps","odt","odp","ods","odg","odc","odb","odf","wp","wpd","key","numbers","pages"],"audio\/mpeg":["mp3","m4a","m4b"],"audio":["mp3","m4a","m4b","ra","ram","wav","ogg","oga","flac","mid","midi","wma","wax","mka"],"audio\/*":["mp3","m4a","m4b","ra","ram","wav","ogg","oga","flac","mid","midi","wma","wax","mka"],"audio\/x-realaudio":["ra","ram"],"audio\/wav":["wav"],"audio\/ogg":["ogg","oga"],"audio\/flac":["flac"],"audio\/midi":["mid","midi"],"audio\/x-ms-wma":["wma"],"audio\/x-ms-wax":["wax"],"audio\/x-matroska":["mka"],"application\/rtf":["rtf"],"application\/javascript":["js"],"application\/pdf":["pdf"],"application\/x-shockwave-flash":["swf"],"application\/java":["class"],"application\/x-tar":["tar"],"application\/zip":["zip"],"application\/x-gzip":["gz","gzip"],"application\/rar":["rar"],"application\/x-7z-compressed":["7z"],"application\/x-msdownload":["exe"],"application\/octet-stream":["xcf"],"application\/msword":["doc"],"application\/vnd.ms-powerpoint":["pot","pps","ppt"],"application\/vnd.ms-write":["wri"],"application\/vnd.ms-excel":["xla","xls","xlt","xlw"],"application\/vnd.ms-access":["mdb"],"application\/vnd.ms-project":["mpp"],"application\/vnd.openxmlformats-officedocument.wordprocessingml.document":["docx"],"application\/vnd.ms-word.document.macroEnabled.12":["docm"],"application\/vnd.openxmlformats-officedocument.wordprocessingml.template":["dotx"],"application\/vnd.ms-word.template.macroEnabled.12":["dotm"],"application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet":["xlsx"],"application\/vnd.ms-excel.sheet.macroEnabled.12":["xlsm"],"application\/vnd.ms-excel.sheet.binary.macroEnabled.12":["xlsb"],"application\/vnd.openxmlformats-officedocument.spreadsheetml.template":["xltx"],"application\/vnd.ms-excel.template.macroEnabled.12":["xltm"],"application\/vnd.ms-excel.addin.macroEnabled.12":["xlam"],"application\/vnd.openxmlformats-officedocument.presentationml.presentation":["pptx"],"application\/vnd.ms-powerpoint.presentation.macroEnabled.12":["pptm"],"application\/vnd.openxmlformats-officedocument.presentationml.slideshow":["ppsx"],"application\/vnd.ms-powerpoint.slideshow.macroEnabled.12":["ppsm"],"application\/vnd.openxmlformats-officedocument.presentationml.template":["potx"],"application\/vnd.ms-powerpoint.template.macroEnabled.12":["potm"],"application\/vnd.ms-powerpoint.addin.macroEnabled.12":["ppam"],"application\/vnd.openxmlformats-officedocument.presentationml.slide":["sldx"],"application\/vnd.ms-powerpoint.slide.macroEnabled.12":["sldm"],"application\/onenote":["onetoc","onetoc2","onetmp","onepkg"],"application\/oxps":["oxps"],"application\/vnd.ms-xpsdocument":["xps"],"application\/vnd.oasis.opendocument.text":["odt"],"application\/vnd.oasis.opendocument.presentation":["odp"],"application\/vnd.oasis.opendocument.spreadsheet":["ods"],"application\/vnd.oasis.opendocument.graphics":["odg"],"application\/vnd.oasis.opendocument.chart":["odc"],"application\/vnd.oasis.opendocument.database":["odb"],"application\/vnd.oasis.opendocument.formula":["odf"],"application\/wordperfect":["wp","wpd"],"application\/vnd.apple.keynote":["key"],"application\/vnd.apple.numbers":["numbers"],"application\/vnd.apple.pages":["pages"]},"select":"Select Files","or":"or","uploadInstructions":"Drop files here to upload"};
/* ]]> */
</script>
<input id="rwmb-image_advanced"  class="rwmb-image_advanced" name="<?php echo $name?>" type="hidden" value="<?php echo $value ?>" data-options=<?php echo wp_json_encode($js_options) ?>>


<!-- templates for renderingt the image upload. -->
<script id="tmpl-rwmb-media-item" type="text/html">
	<input type="hidden" name="{{{ data.controller.fieldName }}}" value="{{{ data.id }}}" class="rwmb-media-input">
	<div class="rwmb-media-preview attachment-preview">
		<div class="rwmb-media-content thumbnail">
			<div class="centered">
				<# if ( 'image' === data.type && data.sizes ) { #>
					<# if ( data.sizes.thumbnail ) { #>
						<img src="{{{ data.sizes.thumbnail.url }}}">
					<# } else { #>
						<img src="{{{ data.sizes.full.url }}}">
					<# } #>
				<# } else { #>
					<# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
						<img src="{{ data.image.src }}" />
					<# } else { #>
						<img src="{{ data.icon }}" />
					<# } #>
				<# } #>
			</div>
		</div>
	</div>
	<div class="rwmb-media-info">
		<a href="{{{ data.url }}}" class="rwmb-media-title" target="_blank">
			<# if( data.title ) { #>
				{{{ data.title }}}
			<# } else { #>
				{{{ i18nRwmbMedia.noTitle }}}
			<# } #>
		</a>
		<p class="rwmb-media-name">{{{ data.filename }}}</p>
		<p class="rwmb-media-actions">
			<a class="rwmb-edit-media" title="{{{ i18nRwmbMedia.edit }}}" href="{{{ data.editLink }}}" target="_blank">
				<span class="dashicons dashicons-edit"></span>{{{ i18nRwmbMedia.edit }}}
			</a>
			<a href="#" class="rwmb-remove-media" title="{{{ i18nRwmbMedia.remove }}}">
				<span class="dashicons dashicons-no-alt"></span>{{{ i18nRwmbMedia.remove }}}
			</a>
		</p>
	</div>
</script>

<script id="tmpl-rwmb-media-status" type="text/html">
	<# if ( data.maxFiles > 0 ) { #>
		{{{ data.length }}}/{{{ data.maxFiles }}}
		<# if ( 1 < data.maxFiles ) { #>{{{ i18nRwmbMedia.multiple }}}<# } else {#>{{{ i18nRwmbMedia.single }}}<# } #>
	<# } #>
</script>

<script id="tmpl-rwmb-media-button" type="text/html">
	<a class="button">{{{ data.text }}}</a>
</script>


<script id="tmpl-rwmb-image-item" type="text/html">
	<input type="hidden" name="{{{ data.controller.fieldName }}}" value="{{{ data.id }}}" class="rwmb-media-input">
	<div class="attachment-preview">
		<div class="thumbnail">
			<div class="centered">
				<# if ( 'image' === data.type && data.sizes ) { #>
					<# if ( data.sizes[data.controller.imageSize] ) { #>
						<img src="{{{ data.sizes[data.controller.imageSize].url }}}">
					<# } else { #>
						<img src="{{{ data.sizes.full.url }}}">
					<# } #>
				<# } else { #>
					<# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
						<img src="{{ data.image.src }}" />
					<# } else { #>
						<img src="{{ data.icon }}" />
					<# } #>
				<# } #>
			</div>
		</div>
	</div>
	<div class="rwmb-image-overlay"></div>
	<div class="rwmb-image-actions">
		<a class="rwmb-image-edit rwmb-edit-media" title="{{{ i18nRwmbMedia.edit }}}" href="{{{ data.editLink }}}" target="_blank">
			<span class="dashicons dashicons-edit"></span>
		</a>
		<a href="#" class="rwmb-image-delete rwmb-remove-media" title="{{{ i18nRwmbMedia.remove }}}">
			<span class="dashicons dashicons-no-alt"></span>
		</a>
	</div>
</script>


