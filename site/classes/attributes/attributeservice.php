<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
 
defined('_JEXEC') or die('Restricted access');

class AttributeService {
	public static function renderAttributes($attributes, $enablePackages, $packageFeatures) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$renderedContent="";
		$db =JFactory::getDBO();

		if (count($attributes)>0) {
			foreach ($attributes as $attribute) {
				$class = "";

				if ($attribute->is_mandatory == 1) {
					$class = "validate[required]";
				}

				if (!isset($attribute->attributeValue)) {
					$attribute->attributeValue ="";
				}
				$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);
					
				$hideClass= "";
				$app = JFactory::getApplication();
				if (!$app->isClient('administrator') && $attribute->only_for_admin) {
					continue;
				}
				
				if (isset($packageFeatures) && in_array($attribute->code, $packageFeatures) || !$enablePackages) {
					if (isset($attribute->options)) {
						$attributeOptions = explode("|#", $attribute->options);
					}
					if ($appSettings->enable_multilingual && isset($attribute->options)) {
						foreach ($attributeOptions as $key => $option) {
							$attributeOptions[$key] = JBusinessDirectoryTranslations::getTranslatedItemName($option);
						}
					}
					switch ($attribute->attributeTypeCode) {
						case "header":
							$renderedContent .= '<div class="form-group">';
							$renderedContent .= '<h3 title="' . $attribute->name . '">' . $attribute->name . '</h3>';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';
							break;
						case "input":
							$inputValue = $attribute->attributeValue;

							if ($attribute->is_mandatory == 1) {
								$class = "validate[required] text-input";
							}

							$renderedContent .= '<div class="form-group '.$hideClass.'">';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . JBusinessUtil::showMandatory($attribute->is_mandatory) . '</label>';
							$renderedContent .= '<input type="text" maxLength="150" size="50" name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id . '" value="' . $inputValue . '"  class="form-control ' . $class . '"/>';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';

							break;
						case "link":
							$inputValue = $attribute->attributeValue;

							if ($attribute->is_mandatory == 1) {
								$class = "validate[required] text-input";
							}
							$renderedContent .= '<div class="form-group '.$hideClass.'">';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . JBusinessUtil::showMandatory($attribute->is_mandatory) . '</label>';
							$renderedContent .= '<input type="text" maxLength="150" size="50" name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id . '" value="' . $inputValue . '"  class="form-control ' . $class . '"/>';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';

							break;
						case "textarea":
							$inputValue = $attribute->attributeValue;
							if ($attribute->is_mandatory == 1) {
								$class = "validate[required] text-input";
							}

							$renderedContent .= '<div class="form-group '.$hideClass.'">';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . JBusinessUtil::showMandatory($attribute->is_mandatory) . '</label>';
							$renderedContent .= '<textarea cols="10" maxLength="250" name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id . '" class="form-control ' . $class . '">' . $inputValue . '</textarea>';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';

							break;
						case "select_box":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							if ($attribute->is_mandatory == 1) {
								$class = "validate[required] select";
							}

							$renderedContent .= '<div class="form-group '.$hideClass.'">';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . JBusinessUtil::showMandatory($attribute->is_mandatory) . '</label>';
							$renderedContent .= '<select name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id . '" class="input_sel ' . $class . '">';
							$renderedContent .= '<option data-icon="false" value="" selected="selected">' . JText::_("LNG_SELECT") . '</option>';
							foreach ($attributeOptions as $key => $option) {
								if (isset($attribute->attributeValue) && $attributeOptionsIDS[$key] == $attribute->attributeValue) {
									$renderedContent .= '<option value="' . $attributeOptionsIDS[$key] . '" selected="selected">' . $option . '</option>';
								} else {
									$renderedContent .= '<option value="' . $attributeOptionsIDS[$key] . '">' . $option . '</option>';
								}
							}
							$renderedContent .= '</select>';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';
							break;
						case "checkbox":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeValues     = explode(",", $attribute->attributeValue);

							if ($attribute->is_mandatory == 1) {
								$class = "validate[minCheckbox[1]] checkbox";
							}

							$renderedContent .= '<div class="form-group '.$hideClass.'">';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . JBusinessUtil::showMandatory($attribute->is_mandatory) . '</label>';
							$renderedContent .= '<div class="d-flex flex-wrap">';
							foreach ($attributeOptions as $key => $option) {
								$renderedContent .= "<div class='jbd-checkbox pr-2'>";
								$renderedContent .= "<label for='attribute_". $attribute->id.'_'.$option."'>" . $option . "</label>";
								if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
									$renderedContent .= '<input type="checkbox" name="attribute_' . $attribute->id . '[]" id="attribute_' . $attribute->id .'_'.$option.'" value="' . $attributeOptionsIDS[$key] . '"  class="' . $class . '" checked="true"/>';
								} else {
									$renderedContent .= '<input type="checkbox" name="attribute_' . $attribute->id . '[]" id="attribute_' . $attribute->id .'_'.$option.'" value="' . $attributeOptionsIDS[$key] . '"  class="' . $class . '"/>';
								}
								
								$renderedContent .= "</div>";
							}
							$renderedContent .= '<input type="hidden" name="delete_attribute_' . $attribute->id . '" id="delete_attribute_' . $attribute->id . '" value="1" />';
							$renderedContent .= '</div>';
							$renderedContent .= '</div>';
							break;
						case "radio":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							
							if ($attribute->is_mandatory == 1) {
								$class = "validate[required] radio";
							}

							$renderedContent .= '<div class="form-group '.$hideClass.'">';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . JBusinessUtil::showMandatory($attribute->is_mandatory) . '</label>';
							$renderedContent .= '<div class="d-flex flex-wrap">';
							foreach ($attributeOptions as $key => $option) {
								$renderedContent .= "<div class='jbd-checkbox pr-2'>";
								$renderedContent .= "<label for='attribute_". $attribute->id.'_'.$option."'>" . $option . "</label>";
								if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
									$renderedContent .= '<input type="radio" name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id.'_'.$option . '" value="' . $attributeOptionsIDS[$key] . '"  class="' . $class . '" checked="true"/>';
								} else {
									$renderedContent .= '<input type="radio" name="attribute_' . $attribute->id . '" id="attribute_' . $attribute->id.'_'.$option . '" value="' . $attributeOptionsIDS[$key] . '"  class="' . $class . '"/>';
								}
								
								$renderedContent .= "</div>";
							}
							$renderedContent .= '<input type="hidden" name="delete_attribute_' . $attribute->id . '" id="delete_attribute_' . $attribute->id . '" value="1" />';
							$renderedContent .= '</div>';
							$renderedContent .= '</div>';
							break;
						case "multiselect":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeValues     = explode(",", $attribute->attributeValue);

							if ($attribute->is_mandatory == 1) {
								$class = "validate[required]";
							}

							$renderedContent .= '<div class="form-group">';
							$renderedContent .= '<label id="details-lbl" for="attribute_' . $attribute->id . '" class="hasTip" title="' . $attribute->name . '">' . $attribute->name . JBusinessUtil::showMandatory($attribute->is_mandatory) . '</label>';
							$renderedContent .= '<select multiple="multiple" id="attribute_' . $attribute->id . '" class="inputbox input-medium chosen-select ' . $class . '" name="attribute_' . $attribute->id . '[]">';
							foreach ($attributeOptions as $key => $option) {
								if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
									$renderedContent .= '<option value="' . $attributeOptionsIDS[$key] . '" selected>' . $option . '</option>';
								} else {
									$renderedContent .= '<option value="' . $attributeOptionsIDS[$key] . '">' . $option . '</option>';
								}
							}
							$renderedContent .= "</select>";
							$renderedContent .= '<input type="hidden" name="delete_attribute_' . $attribute->id . '" id="delete_attribute_' . $attribute->id . '" value="1" />';
							$renderedContent .= '<div class="clear"></div>';
							$renderedContent .= '</div>';
							break;
						default:
							echo "";
					}
				}
			}
		}
		return $renderedContent;
	}

	public static function renderAttributesSearch($attributes, $enablePackages, $packageFeatures) {
		$appSettings = JBusinessUtil::getApplicationSettings();

		$renderedContent="";
		if (!empty($attributes)) {
			if ($appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateAttributesTranslation($attributes);
			}
				
			foreach ($attributes as $attribute) {
				$class = "";
				
				if (!isset($attribute->attributeValue)) {
					$attribute->attributeValue ="";
				}
				$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);

				if ($attribute->is_mandatory==1) {
					$class = "validate[required]";
				}

				if (isset($packageFeatures) && in_array($attribute->code, $packageFeatures) || !$enablePackages) {
					$attributeOptions = explode("|#", $attribute->options);
					if ($appSettings->enable_multilingual && isset($attribute->options)) {
						foreach ($attributeOptions as $key => $option) {
							$attributeOptions[$key] = JBusinessDirectoryTranslations::getTranslatedItemName($option);
						}
					}
					switch ($attribute->attributeTypeCode) {
						case "header":
							break;
						case "input":
							$inputValue= $attribute->attributeValue;
							$renderedContent.= '<div class="form-field">';
							$renderedContent.= '<input type="text" placeholder="'.$attribute->name.'" size="50" name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'" value="'.$inputValue.'"  class="input_txt '.$class.'"/>';
							$renderedContent.= '</div>';
							break;
						case "textarea":
							$inputValue= $attribute->attributeValue;
							$renderedContent.= '<div class="form-field">';
							$renderedContent.= '<input type="text" placeholder="'.$attribute->name.'" size="50" name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'" value="'.$inputValue.'"  class="input_txt '.$class.'"/>';
							$renderedContent.= '</div>';
							break;
						case "select_box":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$renderedContent.= '<div class="form-field">';
							$renderedContent.= '<select name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'" class="chosen-select input_sel '.$class.'">';
							$renderedContent.= '<option value="" selected="selected">'.JText::_("LNG_SELECT").' '.$attribute->name.'</option>';
							foreach ($attributeOptions as $key=>$option) {
								if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
									$renderedContent.='<option value="'.$attributeOptionsIDS[$key].'" selected="selected">'.$option.'</option>';
								} else {
									$renderedContent.='<option value="'.$attributeOptionsIDS[$key].'">'.$option.'</option>';
								}
							}
							$renderedContent.= '</select>';
							$renderedContent.= '</div>';
							break;
						case "checkbox":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeValues = explode(",", $attribute->attributeValue);
							if ($attribute->is_mandatory==1) {
								$class = "validate[minCheckbox[1]] checkbox";
							}

							$renderedContent.= '<div class="form-field custom">';
							$renderedContent.= '<label id="details-lbl" for="attribute_'.$attribute->id.'" class="hasTip" title="'.$attribute->name.'">'.$attribute->name.'</label>';
							foreach ($attributeOptions as $key=>$option) {
								$renderedContent.="<div class='custom-div'>";
								$option = "<span class='dir-check-lbl'>".$option."</span>";
								if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
									$renderedContent.= '<input type="checkbox" name="attribute_'.$attribute->id.'[]" id="attribute_'.$attribute->id.'_'.$option.'" value="'.$attributeOptionsIDS[$key].'"  class="'.$class.'" checked="true"/>'.$option;
								} else {
									$renderedContent.= '<input type="checkbox" name="attribute_'.$attribute->id.'[]" id="attribute_'.$attribute->id.'_'.$option.'" value="'.$attributeOptionsIDS[$key].'"  class="'.$class.'"/>'.$option;
								}
								$renderedContent.="</div>";
							}
							$renderedContent.= '</div>';
							break;
						case "radio":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							if ($attribute->is_mandatory==1) {
								$class = "validate[required] radio";
							}

							$renderedContent.= '<div class="form-field custom">';
							$renderedContent.= '<label id="details-lbl" for="attribute_'.$attribute->id.'" class="hasTip" title="'.$attribute->name.'">'.$attribute->name.'</label>';
							foreach ($attributeOptions as $key=>$option) {
								$option = "<span class='dir-check-lbl'>".$option."</span>";
								if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
									$renderedContent.= '&nbsp;<input type="radio" name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'_'.$option.'" value="'.$attributeOptionsIDS[$key].'"  class="'.$class.'" checked="true"/>&nbsp;&nbsp;'.$option;
								} else {
									$renderedContent.= '&nbsp;<input type="radio" name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'_'.$option.'" value="'.$attributeOptionsIDS[$key].'"  class="'.$class.'"/>&nbsp;&nbsp;'.$option;
								}
							}
							$renderedContent.= '</div>';
							break;
						case "multiselect":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$renderedContent.= '<div class="form-field custom">';
							$renderedContent.= '<select name="attribute_'.$attribute->id.'" id="attribute_'.$attribute->id.'" class="input_sel '.$class.'">';
							$renderedContent.= '<option value="" selected="selected">'.JText::_("LNG_SELECT").' '.$attribute->name.'</option>';
							foreach ($attributeOptions as $key=>$option) {
								if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
									$renderedContent.='<option value="'.$attributeOptionsIDS[$key].'" selected="selected">'.$option.'</option>';
								} else {
									$renderedContent.='<option value="'.$attributeOptionsIDS[$key].'">'.$option.'</option>';
								}
							}
							$renderedContent.= '</select>';
							$renderedContent.= '</div>';
							break;
						default:
							echo "";
					}
				}
			}
		}
		return $renderedContent;
	}

	public static function renderAttributesFront($attributes, $enablePackages, $packageFeatures, $noSpace = false) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db =JFactory::getDBO();
		
		$renderedContent="";
		if (!empty($attributes)) {
			//update the translations
			if ($appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateAttributesTranslation($attributes);
			}
			
			foreach ($attributes as $attribute) {
				if ($attribute->show_in_front != 1) {
					continue;
				}
				
				if (!isset($attribute->attributeValue)) {
					$attribute->attributeValue ="";
				}
				$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);
				$attribute->name = htmlspecialchars($attribute->name, ENT_QUOTES);
				
				if (!isset($attribute->attributeValue)) {
					$attribute->attributeValue ="";
				}
				$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);
				
				if (isset($packageFeatures) && in_array($attribute->code, $packageFeatures) || !$enablePackages) {
					$attributeOptions = explode("|#", $attribute->options);
					if ($appSettings->enable_multilingual && isset($attribute->options)) {
						foreach ($attributeOptions as $key => $option) {
							$attributeOptions[$key] = JBusinessDirectoryTranslations::getTranslatedItemName($option);
						}
					}
					
					$span = $attribute->show_name?"":"col-md-3 col-sm-4";
					if (!$attribute->show_icon) {
						$span="attr-space";
					}

					$rowspan = $attribute->show_name?"attribute-with-name":"row";
					
					if($noSpace){
						$span="";
						$rowspan = "";
					}

					switch ($attribute->attributeTypeCode) {
						case "header":
							$renderedContent.="<div class='attribute-header attribute-name'>".$attribute->name."</div>";
							break;
						case "input":
							$inputValue= $attribute->attributeValue;
							if (!empty($inputValue)) {
								$renderedContent.='<div class="listing-attributes  attribute-'.$attribute->id.'">';
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':"";
								$renderedContent.= '<div class="attribute-item">'.html_entity_decode($inputValue).'</div>';
								$renderedContent.= '</div>';
							}
							break;
						case "link":
							$inputValue= $attribute->attributeValue;
							
							if (!empty($inputValue)) {
								if (!preg_match("~^(?:f|ht)tps?://~i", $inputValue)) {
									$inputValue = "http://" . $inputValue;
								}
								
								$renderedContent.='<div class="listing-attributes  attribute-'.$attribute->id.'">';
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':"";
								$renderedContent.= '<div class="attribute-item"> <a target="_blank" href="'.html_entity_decode($inputValue).'">'.html_entity_decode($inputValue).'</a></div>';
								$renderedContent.= '</div>';
							}
							break;
						case "textarea":
							$inputValue= $attribute->attributeValue;
							$inputValue = html_entity_decode($inputValue);
							
							if (!empty($inputValue)) {
								$renderedContent.='<div class="listing-attributes  attribute-'.$attribute->id.'">';
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.html_entity_decode($attribute->name).': </div>':"";
								$renderedContent.= '<div class="attribute-item">'.html_entity_decode($inputValue).'</div>';
								$renderedContent.= '</div>';
							}
							break;
						case "select_box":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeIcons = explode("|#", $attribute->optionsIcons);
							$inputValue="";
							foreach ($attributeOptions as $key=>$option) {
								if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
									$inputValue = $option;
									if (!empty($attributeIcons) && isset($attributeIcons[$key])) {
										$icon = $attributeIcons[$key];
									}
									break;
								}
							}
							if (!empty($inputValue)) {
								$renderedContent.='<div class="listing-attributes  attribute-'.$attribute->id.'">';
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':"";
								$color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
								$renderedContent.= '<div class="attribute-item">';
								if ($attribute->show_icon) {
									$renderedContent.= '<i class="'.$icon.'" '.$color.'></i>';
								}
								$renderedContent.=$inputValue.'</div>';
								$renderedContent.='</div>';
							}
							break;
						case "checkbox":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeValues = explode(",", $attribute->attributeValue);
							$attributeIcons = explode("|#", $attribute->optionsIcons);
							if ($attributeValues[0]=="") {
								break;
							}
								
								$color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
								$renderedContent.="<div class=\"listing-attributes  attribute-$attribute->id $rowspan\">";
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':"";
								foreach ($attributeOptions as $key=>$option) {
									if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
										$renderedContent.= "<div class=\"$span attribute-item\">";
										if ($attribute->show_icon && !empty($attributeIcons) && isset($attributeIcons[$key])) {
											$renderedContent.= '<i class="'.$attributeIcons[$key].'" '.$color.'></i>&nbsp;';
										}
										$renderedContent.= $option;
										$renderedContent.='</div>';
									}
								}
								
								$renderedContent.='</div>';
								break;
						case "radio":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeIcons = explode("|#", $attribute->optionsIcons);
							$inputValue="";
							foreach ($attributeOptions as $key=>$option) {
								if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
									$inputValue = $option;
									if (!empty($attributeIcons) && isset($attributeIcons[$key])) {
										$icon = $attributeIcons[$key];
									}
									break;
								}
							}
							if (!empty($inputValue)) {
								$renderedContent.='<div class="listing-attributes  attribute-'.$attribute->id.'">';
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':"";
								$color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
								
								$renderedContent.='<div class="attribute-item">';
								if ($attribute->show_icon) {
									$renderedContent.= '<i class="'.$icon.' attribute-value" '.$color.'></i>&nbsp;';
								}
								$renderedContent.= $inputValue;
								$renderedContent.='</div>';
							
								$renderedContent.='</div>';
							}
							
							break;
						case "multiselect":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeValues = explode(",", $attribute->attributeValue);
							$attributeIcons = explode("|#", $attribute->optionsIcons);
							if ($attributeValues[0]=="") {
								break;
							}
								
							$color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
							$renderedContent.="<div class=\"listing-attributes  attribute-$attribute->id $rowspan\">";
							$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':'';
							foreach ($attributeOptions as $key=>$option) {
								if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
									$renderedContent.= "<div class=\"$span attribute-item\">";
									if ($attribute->show_icon && !empty($attributeIcons) && isset($attributeIcons[$key])) {
										$renderedContent.= '<i class="'.$attributeIcons[$key].' attribute-value"'.$color.'></i>&nbsp;';
									}
									$renderedContent.= $option;
									$renderedContent.="</div>";
								}
							}
							
							$renderedContent.='</div>';
							break;
						default:
							echo "";
					}
				}
			}
		}
		return $renderedContent;
	}


	public static function renderAttributesSearchResults($attributes, $enablePackages, $packageFeatures, $noSpace = false) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db =JFactory::getDBO();
		
		$renderedContent="";
		if (!empty($attributes)) {
			//update the translations
			if ($appSettings->enable_multilingual) {
				JBusinessDirectoryTranslations::updateAttributesTranslation($attributes);
			}
			
			foreach ($attributes as $attribute) {
				if ($attribute->show_in_front != 1) {
					continue;
				}
				
				if (!isset($attribute->attributeValue)) {
					$attribute->attributeValue ="";
				}
				$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);
				$attribute->name = htmlspecialchars($attribute->name, ENT_QUOTES);
				
				if (!isset($attribute->attributeValue)) {
					$attribute->attributeValue ="";
				}
				$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);
				
				if (isset($packageFeatures) && in_array($attribute->code, $packageFeatures) || !$enablePackages) {
					$attributeOptions = explode("|#", $attribute->options);
					if ($appSettings->enable_multilingual && isset($attribute->options)) {
						foreach ($attributeOptions as $key => $option) {
							$attributeOptions[$key] = JBusinessDirectoryTranslations::getTranslatedItemName($option);
						}
					}
					
					$span = "";
					$rowspan = "";
					

					switch ($attribute->attributeTypeCode) {
						case "header":
							$renderedContent.="<div class='attribute-header attribute-name'>".$attribute->name."</div>";
							break;
						case "input":
							$inputValue= $attribute->attributeValue;
							if (!empty($inputValue)) {
								$renderedContent.='<div class="item-option  attribute-'.$attribute->id.'">';
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':"";
								$renderedContent.= '<div class="attribute-item">'.html_entity_decode($inputValue).'</div>';
								$renderedContent.= '</div>';
							}
							break;
						case "link":
							$inputValue= $attribute->attributeValue;
							
							if (!empty($inputValue)) {
								if (!preg_match("~^(?:f|ht)tps?://~i", $inputValue)) {
									$inputValue = "http://" . $inputValue;
								}
								
								$renderedContent.='<div class="item-option  attribute-'.$attribute->id.'">';
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':"";
								$renderedContent.= '<div class="attribute-item"> <a target="_blank" href="'.html_entity_decode($inputValue).'">'.html_entity_decode($inputValue).'</a></div>';
								$renderedContent.= '</div>';
							}
							break;
						case "textarea":
							$inputValue= $attribute->attributeValue;
							$inputValue = html_entity_decode($inputValue);
							
							if (!empty($inputValue)) {
								$renderedContent.='<div class="item-option  attribute-'.$attribute->id.'">';
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.html_entity_decode($attribute->name).': </div>':"";
								$renderedContent.= '<div class="attribute-item">'.html_entity_decode($inputValue).'</div>';
								$renderedContent.= '</div>';
							}
							break;
						case "select_box":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeIcons = explode("|#", $attribute->optionsIcons);
							$inputValue="";
							foreach ($attributeOptions as $key=>$option) {
								if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
									$inputValue = $option;
									if (!empty($attributeIcons) && isset($attributeIcons[$key])) {
										$icon = $attributeIcons[$key];
									}
									break;
								}
							}
							if (!empty($inputValue)) {
								$renderedContent.='<div class="item-option  attribute-'.$attribute->id.'">';
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':"";
								$color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
								$renderedContent.= '<div class="attribute-item">';
								if ($attribute->show_icon && !empty($icon)) {
									$renderedContent.= '<i class="'.$icon.'" '.$color.'></i>';
								}
								$renderedContent.=$inputValue.'</div>';
								$renderedContent.='</div>';
							}
							break;
						case "checkbox":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeValues = explode(",", $attribute->attributeValue);
							$attributeIcons = explode("|#", $attribute->optionsIcons);
							if ($attributeValues[0]=="") {
								break;
							}
								
								$color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
								$renderedContent.="<div class=\"item-option  attribute-$attribute->id $rowspan\">";
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':"";
								foreach ($attributeOptions as $key=>$option) {
									if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
										$renderedContent.= "<div class=\"$span attribute-item\">";
										if ($attribute->show_icon && !empty($attributeIcons) && !empty($attributeIcons[$key])) {
											$renderedContent.= '<i class="'.$attributeIcons[$key].'" '.$color.'></i>&nbsp;';
										}
										$renderedContent.= $option;
										$renderedContent.='</div>';
									}
								}
								
								$renderedContent.='</div>';
								break;
						case "radio":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeIcons = explode("|#", $attribute->optionsIcons);
							$inputValue="";
							foreach ($attributeOptions as $key=>$option) {
								if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
									$inputValue = $option;
									if (!empty($attributeIcons) && isset($attributeIcons[$key])) {
										$icon = $attributeIcons[$key];
									}
									break;
								}
							}
							if (!empty($inputValue)) {
								$renderedContent.='<div class="item-option  attribute-'.$attribute->id.'">';
								$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':"";
								$color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
								
								$renderedContent.='<div class="attribute-item">';
								if ($attribute->show_icon && !empty($icon)) {
									$renderedContent.= '<i class="'.$icon.' attribute-value" '.$color.'></i>&nbsp;';
								}
								$renderedContent.= $inputValue;
								$renderedContent.='</div>';
							
								$renderedContent.='</div>';
							}
							
							break;
						case "multiselect":
							$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
							$attributeValues = explode(",", $attribute->attributeValue);
							$attributeIcons = explode("|#", $attribute->optionsIcons);
							if ($attributeValues[0]=="") {
								break;
							}
								
							$color = !empty($attribute->color)?'style="color:'.$attribute->color.';"':'';
							$renderedContent.="<div class=\"item-option  attribute-$attribute->id $rowspan\">";
							$renderedContent.= $attribute->show_name?'<div class="attribute-name">'.$attribute->name.': </div>':'';
							foreach ($attributeOptions as $key=>$option) {
								if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
									$renderedContent.= "<div class=\"$span attribute-item\">";
									if ($attribute->show_icon && !empty($attributeIcons) && !empty($attributeIcons[$key])) {
										$renderedContent.= '<i class="'.$attributeIcons[$key].' attribute-value"'.$color.'></i>&nbsp;';
									}
									$renderedContent.= $option;
									$renderedContent.="</div>";
								}
							}
							
							$renderedContent.='</div>';
							break;
						default:
							echo "";
					}
				}
			}
		}
		return $renderedContent;
	}


	/**
	 * Parse attributes and get the values for selected attribute
	 * @param Object $attributes
	 * @param string $name
	 */
	public static function getAttributeAsString($attributes, $name) {
		foreach ($attributes as $attribute) {
			if ($attribute->name == $name) {
				return self::getAttributeValues($attribute);
			}
		}
		return "";
	}


	/**
	 * Render a attribute
	 *
	 * @param unknown_type $attribute
	 */
	public static function getAttributeValues($attribute) {
		$appSettings = JBusinessUtil::getApplicationSettings();
		$db =JFactory::getDBO();
		if (isset($attribute->options)) {
			$attributeOptions = explode("|#", $attribute->options);
		}
		if ($appSettings->enable_multilingual && isset($attribute->options)) {
			foreach ($attributeOptions as $key => $option) {
				$attributeOptions[$key] = JBusinessDirectoryTranslations::getTranslatedItemName($option);
			}
		}
		
		if (!isset($attribute->attributeValue)) {
			$attribute->attributeValue ="";
		}
		$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);
		
		switch ($attribute->attributeTypeCode) {
			case "header":
				return "";
			case "input":
				$inputValue= $attribute->attributeValue;
				if (!empty($inputValue)) {
					return $inputValue;
				} else {
					return "";
				}
				break;
			case "link":
				$inputValue= $attribute->attributeValue;
				if (!empty($inputValue)) {
					return $inputValue;
				} else {
					return "";
				}
				break;
			case "textarea":
				$inputValue= $attribute->attributeValue;
				if (!empty($inputValue)) {
					return $inputValue;
				} else {
					return "";
				}
				break;
			case "select_box":
				$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
				$inputValue="";
				foreach ($attributeOptions as $key=>$option) {
					if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
						$inputValue = $option;
						break;
					}
				}
				if (!empty($inputValue)) {
					return $inputValue;
				} else {
					return "";
				}
				break;
			case "checkbox":
				$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
				$attributeValues = explode(",", $attribute->attributeValue);
				if ($attributeValues[0]=="") {
					break;
				}
				$renderedContent="";
				foreach ($attributeOptions as $key=>$option) {
					if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
						$renderedContent.= $option.',';
					}
				}
				$inputValue= rtrim($renderedContent, ',');
				if (!empty($inputValue)) {
					return $inputValue;
				} else {
					return "";
				}
				break;
			case "radio":
				$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
				$inputValue="";
				foreach ($attributeOptions as $key=>$option) {
					if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
						$inputValue = $option;
						break;
					}
				}
				if (!empty($inputValue)) {
					return $inputValue;
				} else {
					return "";
				}
				break;
			case "multiselect":
				$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
				$attributeValues = explode(",", $attribute->attributeValue);
				if ($attributeValues[0]=="") {
					break;
				}
				$renderedContent="";
				foreach ($attributeOptions as $key=>$option) {
					if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
						$renderedContent.= $option.',';
					}
				}
				$inputValue= rtrim($renderedContent, ',');
				if (!empty($inputValue)) {
					return $inputValue;
				} else {
					return "";
				}
				break;
			default:
				echo "";
		}
	}

	/**
	 * Assembles all icon classes of a certain attributes into an array. Returns false
	 * if show_icon is set to no. Also checks if attribute is contained in the package when
	 * the enablePackages is set to true. If not, it returns false.
	 *
	 * @param $attribute object containing attribute and it's selected values
	 * @param $enablePackages boolean true if packages are enabled, false otherwise
	 * @param $packageFeatures array array containing package features
	 *
	 * @return array|bool|string
	 *
	 * @since 4.9.0
	 */
	public static function getAttributeIcons($attribute, $enablePackages, $packageFeatures) {
		if (isset($attribute->optionsIcons)) {
			$attributeIcons = explode("|#", $attribute->optionsIcons);
		}
		if (!$attribute->show_icon || $attribute->show_in_front != 1) {
			return false;
		}

		if (isset($packageFeatures) && in_array($attribute->code, $packageFeatures) || !$enablePackages) {
			switch ($attribute->attributeTypeCode) {
				case "select_box":
					$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
					$icons = array();
					foreach ($attributeIcons as $key => $val) {
						if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
							$icons[] = $val;
							break;
						}
					}
					if (!empty($icons)) {
						return $icons;
					} else {
						return "";
					}
					break;
				case "checkbox":
					$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
					$attributeValues = explode(",", $attribute->attributeValue);
					if ($attributeValues[0] == "") {
						break;
					}
					$icons = array();
					foreach ($attributeIcons as $key => $val) {
						if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
							$icons[] = $val;
						}
					}
					if (!empty($icons)) {
						return $icons;
					} else {
						return "";
					}
					break;
				case "radio":
					$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
					$icons = array();
					foreach ($attributeIcons as $key => $val) {
						if ($attributeOptionsIDS[$key] == $attribute->attributeValue) {
							$icons[] = $val;
							break;
						}
					}
					if (!empty($icons)) {
						return $icons;
					} else {
						return "";
					}
					break;
				case "multiselect":
					$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
					if(empty($attribute->attributeValue)){
						break;
					}
					$attributeValues = explode(",", $attribute->attributeValue);
					if ($attributeValues[0] == "") {
						break;
					}
					$icons = array();
					foreach ($attributeIcons as $key => $val) {
						if (in_array($attributeOptionsIDS[$key], $attributeValues)) {
							$icons[] = $val;
						}
					}
					if (!empty($icons)) {
						return $icons;
					} else {
						return "";
					}
					break;
				default:
					echo "";
			}
		} else {
			return false;
		}
	}
	
	/**
	* Display icons of a specific attribute
	*
	*/
	public static function displayAttributeIcons($attributes, $attributeCode, $enablePackages, $packageFeatures) {
		$attribute = null;
		foreach ($attributes as $attr) {
			if ($attr->code == $attributeCode) {
				$attribute = $attr;
				break;
			}
		}
		$icons = self::getAttributeIcons($attribute, $enablePackages, $packageFeatures);
		$color = !empty($attribute->color)?$attribute->color:'';
		if (!empty($icons)) {
			foreach ($icons as $icon) {
				echo '<i class="'.$icon.' attribute-icon" style="color:'.$color.';"></i>';
			}
		}
		
		return true;
	}


	//--- Selling Attributes Rendering Start ---//

	/**
	 * Render the offer stocks based on the configuration passed as attributes
	 *
	 * @param $attributes array containing all the attributes for each combinations and one cell of attributes is stock
	 * config which is removed and used to define the quantity
	 * @param $enablePackages boolean true or false if the packages are enabled
	 * @param $packageFeatures array package features
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function renderOfferStocks($attributes, $enablePackages, $packageFeatures){
		$renderedContent = '';

		if (count($attributes) > 0) {
			$counter = 0;
			foreach ($attributes as $key => $attribute) {

				//extract stock data
				$stockData = $attribute['stock'];
				unset($attribute['stock']);

				$attrOptionsHTML = self::createStockOptions($attribute, $enablePackages, $packageFeatures);

				//if no html is provided for attribute combinations then return empty html
				if (empty($attrOptionsHTML)){
					return $renderedContent;
				}

				if ($counter == 0) {
					$renderedContent .= '<label>' . JText::_('LNG_STOCK_CONFIGURATION') . '</label>
									 	<div id="add_stock_option_field" class="">
					                       <a href="javascript:void(0)" class="btn btn-xs btn-success btn-panel" onclick="jbdUtils.addOfferSellingOption()">
					                           <i class="la la-plus"></i>
					                       </a>
					                    </div>
					            ';
				}
				$renderedContent .= '<div class="selling-option-fields options-' . $counter . '" id="options-' . $counter . '">';

				$renderedContent .= '<div class="row">';

				$renderedContent .= '<div class="col-md-6">';
				$renderedContent .= '<div class="row">';
				$renderedContent .= $attrOptionsHTML;
				$renderedContent .= '</div>';
				$renderedContent .= '</div>';

				$renderedContent .= '<div class="col-md-6">';
				$renderedContent .= '<div class="row">';
				$renderedContent .= '<div class="col-md">';
				$renderedContent .= '<div class="form-group ">';
				$renderedContent .= '<label for="quantities' . $counter . '">'.JText::_('LNG_QUANTITY').'</label>';
				$renderedContent .= '<input type="text" maxLength="150" size="50" name="quantities[]" id="quantities' . $counter . '" value="'.$stockData->qty.'" placeholder="' . JText::_('LNG_QUANTITY') . '"  class="form-control validate[required]"/>';
				$renderedContent .= '<div class="clear"></div>';
				$renderedContent .= '</div>';
				$renderedContent .= '</div>';

				$renderedContent .= '<div class="col-md">';
				$renderedContent .= '<div class="form-group ">';
				$renderedContent .= '<label for="notify_stock_qty' . $counter . '">'.JText::_('LNG_NOTIFY_AT_STOCK_QTY').'</label>';
				$renderedContent .= '<input type="text" maxLength="150" size="50" name="notify_stock_qty[]" id="notify_stock_qty' . $counter . '" value="'.$stockData->notify_stock_qty.'" placeholder="' . JText::_('LNG_NOTIFY_AT_STOCK_QTY') . '"  class="form-control validate[required]"/>';
				$renderedContent .= '<div class="clear"></div>';
				$renderedContent .= '</div>';
				$renderedContent .= '</div>';

                if(!isset($stockData->price)){
                    $stockData->price = 0;
                }

                $renderedContent .= '<div class="col-md stock-price">';
                $renderedContent .= '<div class="form-group ">';
                $renderedContent .= '<label for="notify_stock_qty' . $counter . '">'.JText::_('LNG_PRICE').'</label>';
                $renderedContent .= '<input type="text" maxLength="150" size="50" name="stock_price[]" id="stock_price' . $counter . '" value="'.$stockData->price.'" placeholder="' . JText::_('LNG_PRICE') . '"  class="form-control validate[required]"/>';
                $renderedContent .= '<div class="clear"></div>';
                $renderedContent .= '</div>';
                $renderedContent .= '</div>';

				$renderedContent .= '<div class="col-md-2 deleteButton">';
				$renderedContent .= '<div id="delete_offer_selling_option">
		                                    <a href="javascript:void(0)" class="btn btn-xs btn-danger btn-panel" onclick="jbdUtils.deleteOfferSellingOption(\'options-' . $counter . '\')">
		                                        <i class="la la-trash"></i>
		                                    </a>
		                                </div>';
				$renderedContent .= '</div>';

				$renderedContent .= '</div>';
				$renderedContent .= '</div>';
				$renderedContent .= '<hr/>';

				$renderedContent .= '</div>';

				$counter ++;
			}
		}
		return $renderedContent;
	}

	/**
	 * Create select boxes for each attribute passed to function
	 *
	 * @param $attributes array contain the attributes
	 * @param $enablePackages boolean true or false if packages are enabled
	 * @param $packageFeatures array containing package features
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function createStockOptions($attributes, $enablePackages, $packageFeatures){
		$renderedContent = '';
		foreach ($attributes as $attribute) {
			$class = "validate[required]";
			$attribute->attributeValue = htmlspecialchars($attribute->attributeValue, ENT_QUOTES);

			$app = JFactory::getApplication();
			if (!$app->isClient('administrator') && $attribute->only_for_admin) {
				continue;
			}

			if (!isset($attribute->addColDivs)){
				$attribute->addColDivs = true;
			}

			$onChangeAction = '';
			if (isset($attribute->onChangeAction)){
				$onChangeAction = 'onchange="'.$attribute->onChangeAction.'"';
			}

			if (isset($packageFeatures) && in_array($attribute->code, $packageFeatures) || !$enablePackages) {
				$renderedContent .= '<div class="jbtn-order-item">';
			
				$attributeOptions = explode("|#", $attribute->options);
				$attributeOptionsIDS = explode("|#", $attribute->optionsIDS);
				if ($attribute->is_mandatory == 1) {
					$class = "validate[required] select";
				}

				if ($attribute->addColDivs) {
					$renderedContent .= '<div class="col-12 col-md">';
					$renderedContent .= '<div class="form-group">';
				}
				$renderedContent .= '<label for="attribute_' . $attribute->id.'">'.$attribute->name.'</label>';
				$renderedContent .= '<select name="selling-attribute_' . $attribute->id . '[]" id="attribute_' . $attribute->id . '" class="input_sel ' . $class . '"'.$onChangeAction.'>';
				$renderedContent .= '<option value="" selected="selected">' . JText::_("LNG_SELECT") . '</option>';
				foreach ($attributeOptions as $key => $option) {
					if (empty($option)) {
						continue;
					}
					if (isset($attribute->attributeValue) && $attributeOptionsIDS[$key] == $attribute->attributeValue) {
						$renderedContent .= '<option value="' . $attributeOptionsIDS[$key] . '" selected="selected">' . $option . '</option>';
					} else {
						$renderedContent .= '<option value="' . $attributeOptionsIDS[$key] . '">' . $option . '</option>';
					}
				}
				$renderedContent .= '</select>';
				if ($attribute->addColDivs) {
					$renderedContent .= '<div class="clear"></div>';
					$renderedContent .= '</div>';
					$renderedContent .= '</div>';
				}
				
				$renderedContent .= '</div>';
			}

		}
		return $renderedContent;
	}

	/**
	 * Create the structure for the front end. Create the attributes structure and assign in the end also the quantity select
	 *
	 * @param $sellingOptions array attributes that need to be rendered for the front end
	 * @param $offerId int offer id
	 * @param $mainSubCategory  int main subcategory of offer
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @since version
	 */
	public static function renderSellingAttributesFront($sellingOptions,$offerId,$mainSubCategory){
		$renderedContent = '';
		if (!empty($sellingOptions)) {
			foreach ($sellingOptions as $key => $item) {
				$item->is_mandatory = "1";
				$item->addColDivs = false;
				$item->attributeValue = "";
				$item->only_for_admin = false;
				$item->onChangeAction = 'jbdOffers.updateQuantity(this.value,' . $offerId . ','.$mainSubCategory.')';

				$itemOtions1 = explode('|#', $item->optionsIDS);
				foreach ($itemOtions1 as &$opt1) {
					$opt1 = $item->id . '_' . $opt1;
				}
				$item->optionsIDS = implode('|#', $itemOtions1);
			}
			$renderedContent = self::createStockOptions($sellingOptions, false, array());
		}
		
		$renderedContent .= '<div class="jbtn-order-item">';
		$renderedContent .= '<label for="quantity">'.JText::_("LNG_QUANTITY").'</label>';
		$renderedContent .= '<select onchange="jbdOffers.checkAddToCartStatus()" name="quantity" id="quantity" class="validate[required] select"><option value="0">0</option></select>';
		$renderedContent .= '</div>';
		
		return $renderedContent;
	}

	//--- Selling Attributes Rendering End ---//
}
