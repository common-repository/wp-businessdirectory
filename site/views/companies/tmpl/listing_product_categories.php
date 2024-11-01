<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php JBusinessUtil::includeValidation(); ?>

<?php
if(isset($this->productCategories) && count($this->productCategories)) { ?>
    <div id="search-path">
        <ul>
            <li><?php echo JText::_("LNG_YOU_ARE_HERE")?>:</li>
            <li id="all-categories">
                <a href="javascript:void(0);" id="all-categories-path" onclick="jbdListings.goBack()"><?php echo JText::_("LNG_ALL_CATEGORIES"); ?></a>
                &raquo;
            </li>
            <li id="sub-categories">
            </li>
            <li id="category-products">
            </li>
            <li id="product-details">
            </li>
        </ul>
    </div>
    <div class="clear"></div>
    <div id="grid-content" class='categories-level-1  product-categories'>
        <?php $i = 0; ?>
        <?php foreach ($this->productCategories[1] as $category) {
            ?>
            <?php if($i%3==0) { ?>
                <div class="row-fluid">
            <?php } ?>
                    <div id="post-<?php echo $category->id ?>" class="col-md-4 product-image">
                        <div>
                            <figure class="post-image">
                                <a href="javascript:void(0)" onclick="jbdListings.showProductCategories(<?php echo $category->id ?>,<?php echo $this->company->id ?>)">
                                    <?php if(!empty($category->imageLocation) ){?>
                                        <img title="<?php echo $category->name?>" alt="<?php echo $category->name?>" src="<?php echo BD_PICTURES_PATH.$category->imageLocation ?>">
                                    <?php }else{ ?>
                                        <img title="<?php echo $category->name?>" alt="<?php echo $category->name?>" src="<?php echo BD_PICTURES_PATH.'/no_image.jpg' ?>">
                                    <?php } ?>
                                </a>
                            </figure>

                            <div class="post-content" id="parent-category-<?php echo $category->id ?>" style="text-align:center;">
                                <a style="color:black;" href="javascript:void(0)" onclick="jbdListings.showProductCategories(<?php echo $category->id ?>)">
                                    <strong class="post-title">
                                        <span><?php echo $category->name ?></span>
                                    </strong>
                                    <p class="offer-dates">
                                        <?php echo JBusinessUtil::truncate($category->description, 100); ?>
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>
            <?php
            $i++;
            if($i%3==0) { ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php if($i%3!=0) { ?>
            </div>
        <?php } ?>
    </div>

    <?php foreach($this->productCategories[2] as $key=>$categories) { ?>
    <div id="grid-content" class='subcategory grid4 categories-level-<?php echo $key ?> product-categories' style="display:none;">
        <?php $i = 0; ?>

        <?php foreach($categories->categories as $category) { ?>
            <?php if($i%4==0) { ?>
                <div class="row-fluid">
            <?php } ?>
            <div id="subcategory-<?php echo $category->id ?>" class="span3 product-image-small">
                <div>
                    <div class="post-content">
                        <a style="color:black;" href="javascript:void(0);" onclick="jbdListings.showProducts(<?php echo $category->id.', '.$this->company->id; ?>)">
                            <h2 class="post-title">
                                <span  id="product-category-<?php echo $category->id ?>"><?php echo $category->name ?></span>
                            </h2>
                            <p class="offer-dates">
                                <?php
                                echo JBusinessUtil::truncate($category->description, 100);
                                ?>
                            </p>
                        </a>
                    </div>
                </div>
            </div>
            <?php
            $i++;
            if($i%4==0) { ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php if($i%4!=0) { ?>
            </div>
        <?php } ?>
        </div>
    <?php } ?>
<?php } else {
    echo JText::_("LNG_NO_PRODUCT_CATEGORIES");
}
?>
<span id="product-list-content"></span>
<span id="product-details-content"></span>
<span id="product-quote-request" style="display: none">
    <a href="javascript:jbdListings.showQuoteCompanyProduct(<?php echo $this->company->id; ?>)" class="btn btn-primary" style="float: right">
        <?php echo JText::_('LNG_QUOTE') ?>
    </a>
</span>
<div class="clear"></div>

<div id="company-quote-response" class="jbd-container" style="display:none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title" id="text"></p>
        </div>
    </div>
</div>

<div id="company-quote-product" class="jbd-container" style="display:none">
    <?php
    $user = JBusinessUtil::getUser();
    if ($user->ID > 0) {
	    $userNameDetails = explode(' ', $user->name);
	    $firstName = $userNameDetails[0];
	    $lastName = (count($userNameDetails) > 1) ? $userNameDetails[1] : '';
    }
    ?>
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_QUOTE_PRODUCT') ?></p>
        </div>
        <form id="quoteCompanyProductFrm" name="quoteCompanyProductFrm" action="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory') ?>" method="post">

            <div class="jmodal-body">
                <p><?php echo JText::_('LNG_PRODUCT_QUTE_TEXT') ?></p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="jinput-outline jinput-hover">
                            <input class="validate[required]" id="firstName-quote" name="firstName" type="text" value="<?php echo $user->ID>0?$firstName:""?>" required="" >
                            <label for="firstName-quote"><?php echo JText::_('LNG_FIRST_NAME') ?></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="jinput-outline jinput-hover">
                            <input class="validate[required]" id="lastName-quote" type="text" name="lastName" value="<?php echo $user->ID>0?$lastName:""?>" required="">
                            <label for="lastName-quote"><?php echo JText::_('LNG_LAST_NAME') ?></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline jinput-hover">
                            <input type="text" name="email" id="email-quote" class="validate[required,custom[email]]" value="<?php echo $user->ID>0?$user->email:""?>" required="">
                            <label for="email-quote"><?php echo JText::_('LNG_EMAIL_ADDRESS') ?></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline jinput-hover">
                            <textarea rows="5" name="description" id="description-quote" class="form-control validate[required]" required=""></textarea>
                            <label for="description-quote"><?php echo JText::_('LNG_CONTACT_TEXT')?>:</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <?php echo JBusinessUtil::renderTermsAndConditions('contact'); ?>
                    </div>
                </div>

                <?php if($this->appSettings->captcha){?>
                    <div class="form-item">
                        <?php
                        $namespace="jbusinessdirectory.contact";
                        $class=" required";

                        $captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

                        if(!empty($captcha)){
                            echo $captcha->display("captcha", "captcha-div-contact", $class);
                        }
                        ?>
                    </div>
                <?php } ?>
            </div>

        </form>
        <div class="jmodal-footer">
            <div class="btn-group" role="group" aria-label="">
                <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                <button type="button" class="jmodal-btn" onclick="jbdListings.requestQuoteCompanyProduct('<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&task=companies.requestQuoteCompanyProductAjax', false); ?>')"><?php echo JText::_("LNG_REQUEST_QUOTE")?></button>
            </div>
        </div>
    </div>

	<?php echo JHTML::_( 'form.token' ); ?>
    <input type='hidden' name='task' id="task" value='companies.contactCompany'/>
    <input type='hidden' name='userId' value=''/>
    <input type="hidden" id="path" name="path" value="" />
    <input type="hidden" id="companyId" name="companyId" value="" />
    <input type="hidden" id="productId" name="productId" value="" />
    <input type="hidden" id="productAlias" name="productAlias" value="" />
    <input type="hidden" id="productSubject" name="productSubject" value="" />
</div>

