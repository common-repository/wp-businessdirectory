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

<style>
.marvel-device .screen {
    background-color: <?php echo $this->item->backgroundColor?> !important;
}
.bottom-nav-container {
    background: <?php echo $this->item->backgroundColor?> !important;
}
.bottom-nav .la {
    color: <?php echo $this->item->iconColor?> !important;
}
.home-text {
    color: <?php echo $this->item->primaryColor?> !important;
}
.bottom-nav .la-home {
    color: <?php echo $this->item->primaryColor?> !important;
}
.home-segment {
    background: <?php echo $this->item->primaryColor?>2e !important;
}
.buttons-text {
    color: <?php echo $this->item->textPrimary?> !important;
}
.viewall {
    color: <?php echo $this->item->primaryColor?> !important;
}
.phone-button-primary{
    background: <?php echo $this->item->primaryColor?> !important;
}
.phone-button-secondary{
    border: 1.4px solid <?php echo $this->item->primaryColor?> !important;
}
.phone-button-grey .la-cog{
    color: <?php echo $this->item->iconColor?> !important;
}
.signup{
    color: <?php echo $this->item->primaryColor?> !important;
}
.settings{
    color: <?php echo $this->item->iconColor?> !important;
}
.featured-tag{
    background:<?php echo $this->item->primaryColor?>2e !important;
}
.text-featured{
    color: <?php echo $this->item->primaryColor?> !important;
}
.button-stack .listing-wishlist .la-heart-o{
    color: <?php echo $this->item->primaryColor?> !important;
}
.category-text{
    color: <?php echo $this->item->primaryColor?> !important;
}
</style>
<div class="marvel-device iphone-x">
    <div class="notch">
        <div class="camera"></div>
        <div class="speaker"></div>
    </div>
    <div class="top-bar"></div>
    <div class="sleep"></div>
    <div class="bottom-bar"></div>
    <div class="volume"></div>
    <div class="overflow">
        <div class="shadow shadow--tr"></div>
        <div class="shadow shadow--tl"></div>
        <div class="shadow shadow--br"></div>
        <div class="shadow shadow--bl"></div>
    </div>
    <div class="inner-shadow"></div>
    <div class="screen">
        <div class="app-header">
            <img class="header-image" src="https://www.theladders.com/wp-content/uploads/friends-happy-190821.jpg"/>

            <div class="companies-search">
                <img style="max-width:75px;max-height:75px;" src="<?php echo BD_MOBILE_APP_BUILD_UPLOAD_ACCESS_PATH.DS.$this->item->logo_android_nb?>"/>

                <p class="search-text">Good morning, John.</p>
                <div class="search-box has-jicon-left">
                    <input class="search-input " type="text" placeholder="What are you looking for?" />
                    <i class="la la-search"></i>
                </div>
            </div>
        </div>
        <div class="app-body">
            <div class="buttons-header">
                <p class="buttons-text">Buttons</p>
                <a class="viewall" href="#">View All</a>
            </div>
            <div class="buttons-div">
                <button class="phone-button-primary" type="button"><p class="signin">Sign In</p></button>
                <button class="phone-button-secondary" type="button"><p class="signup">Sign Up</p></button>
                <button class="phone-button-grey" type="button">
                    <div class="settings-div">
                        <i class="la la-cog"></i><p class="settings">Settings</p>
                    </div>
                    <i class="la la-arrow-right"></i>
                </button>
            </div>
            <div class="listing-card">
                <img class="listing-image" src="https://d2ub1k1pknil0e.cloudfront.net/media/images/camera-photography.width-1200.jpg"/>
                <div class="featured-tag">
                    <p class="text-featured">Featured</p>
                </div>
                <div class="button-stack">
                    <div class="listing-rating">
                        <i class="la la-star"></i><span class="rating-text">5,0</span>
                    </div>
                    <div class="listing-wishlist">
                        <i class="la la-heart-o"></i>
                    </div>
                </div>
                <div class="listing-content">
                    <div class="category-text">PHOTOGRAPHY</div>
                    <p class="business-name-text">Vintage Photography</p>
                    <p class="location-text">Sunnyvale, California</p>
                </div>
            </div>
            <div class="spacer"></div>
            <div class="bottom-nav-container">
                <nav class="bottom-nav">
                    <ul class="nav-items">
                        <div class="nav-item-div home-div">
                            <li class="segment home-segment">
                            <a class="home-text" href="#"><i class="la la-home"></i>Home</a>
                            </li>
                        </div>
                        <div class="nav-item-div search-div">
                            <li class="segment search-segment">
                                <a href="#"><i class="la la-search"></i></a>
                            </li>
                        </div>
                        <div class="nav-item-div messages-div">
                            <li class="segment messages-segment">
                                <a href="#"><i class="la la-inbox"></i></a>
                            </li>
                        </div>
                        <div class="nav-item-div profile-div">
                            <li class="segment profile-segment">
                                <a href="#"><i class="la la-user"></i></a>
                            </li>
                        </div>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>	