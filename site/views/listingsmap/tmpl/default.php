<?php // no direct access
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$config = JBusinessUtil::getSiteConfig();
$appSettings = JBusinessUtil::getApplicationSettings();

$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];

$title = "";
$description = "";
$keywords = "";
$image = "";

$title = JBusinessUtil::getPageTitle($title);
JBusinessUtil::setMetaData($title, $description, $keywords, true);
JBusinessUtil::setFacebookMetaData($title, $description,$image, $url);
?>


<div id="jbd-container" class="jbd-container">
	<div class="listings-map">
		<?php foreach($this->items as $countryId=>$regions){ ?>
			<?php $country = JBusinessUtil::getCountry($countryId); ?>
			<div class="country-item">
				<div class="main-country"> <a href="<?php echo JBusinessUtil::getSearchURL($countryId)?>"><span itemprop="country"><?php echo $country->country_name ?></span> <div class="country-image"><img alt="<?php echo $country->country_name?>" src="<?php echo BD_PICTURES_PATH.$country->logo ?>"></div></a></div>
			
				<?php foreach($regions as $region => $cities){ ?>
					<div class="region-item">
						<div class="main-region"> <a href="<?php echo JBusinessUtil::getSearchURL($countryId, $region)?>"><span itemprop="region"><?php echo $region ?></span></a></div>

						<?php foreach($cities as $item){ ?>
							<div class="main-city-item">
								<div class="main-city"> <a href="<?php echo JBusinessUtil::getSearchURL($item->countryId, $item->county, $item->city)?>"><span itemprop="city"><?php echo $item->city ?></span></a></div>
								<div clas="item-categories">
									<ul>
										<?php if(!empty($item->categories)){?>
											<?php foreach($item->categories as $cat){?>
												<?php if(!empty($cat[1])){ ?>
													<li class="category-city"> <a href="<?php echo JBusinessUtil::getSearchURL($item->countryId, $item->county, $item->city, $cat[0]) ?>"><span itemprop="category"><?php echo $cat[1] ?></span> <?php echo JText::_("LNG_IN") ?> <span itemprop="city"> <?php echo $item->city?><span></a></li>		
												<?php } ?>
											<?php } ?>
										<?php } ?>	
									</ul>
								</div>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</div>

<script>
	window.addEventListener('load', function(){
		
	});
</script>
