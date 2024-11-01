<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath('/components/'.JFactory::getApplication()->input->get('option').'/tables');

class JBusinessDirectorCategoryLib {
	/**
	 * JBusinessDirectorCategoryLib constructor.
	 */
	public function __construct() {
	}

	/**
	 * Get categories based on the ids or type if none is set then all will be retrieved
	 *
	 * @param $type int Category type, by default it is null
	 * @param $categoriesIds array category ids that needs to be retrieved, by default it is set to null
	 * @return array|mixed categories
	 */
	public function getCategories($type=null, $categoriesIds=null) {
		$categories = $this->getAllCategories($type, $categoriesIds);
		$categories = $this->processCategories($categories);

		$startingLevel = 1;
		$maxLevel=0;
		$path=array();
		$categories["maxLevel"] = $this->setCategoryLevel($categories, $startingLevel, $maxLevel, $path);

		return $categories;
	}

	/**
	 * Get all categories based on type are if needed also the ids (the default for ids is an empty array)
	 *
	 * @param $type int category type
	 * @param array $categoriesIds category Ids
	 * @return mixed categories
	 */
	public function getAllCategories($type, $categoriesIds=array()) {
		$where = "where 1 and published=1 and id!=1 ";
		if (isset($type)) {
			$where .= "and type='$type' ";
		}
		if (!empty($categoriesIds)) {
			$where .= "and id IN(".$categoriesIds.") ";
		}
		$db = JFactory::getDBO();
		$query = "select * 
					from #__jbusinessdirectory_categories 
					$where
					order by lft,parent_id,name";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get conference categories and all of their details
	 *
	 * @return array|mixed conference Categories
	 */
	public function getConferenceCategories() {
		$categories = $this->getAllConferenceCategories();
		$categories = $this->processCategories($categories);

		$startingLevel = 1;
		$maxLevel=0;
		$path=array();
		$categories["maxLevel"] = $this->setCategoryLevel($categories, $startingLevel, $maxLevel, $path);

		return $categories;
	}

	/**
	 *  Get all conference categories from the database
	 *
	 * @return mixed return object with all categories
	 */
	public function getAllConferenceCategories() {
		$db =JFactory::getDBO();
		$query = "select * from #__jbusinessdirectory_categories c
				 inner join #__jbusinessdirectory_conference_session_categories cs on c.id = cs.categoryId
				 group by c.id
		         order by lft,parent_id,name";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get all category data info based on its type and id
	 *
	 * @param $categoryId int category Id from which we need the information
	 * @param $type int category type
	 * @return array|null array with all the information of category or null if category is not found
	 */
	public function getCompleteCategoryById($categoryId, $type) {
		$categoryTable = JTable::getInstance("Category", "JBusinessTable");

		$category = null;
		if (!empty($categoryId)) {
			$category = $categoryTable->getCategoryById($categoryId);
			$category = array($category);
		}

		$categories = $categoryTable->getAllCategories($type);
		$categories = $this->processCategories($categories);

		$startingLevel = 1;
		$maxLevel=0;
		$path=array();
		//set the level by reference
		$this->setCategoryLevel($categories, $startingLevel, $maxLevel, $path);
		//set the category by reference
		$this->findCategory($categories, $category);
		return $category;
	}

	/**
	 * Process categories, Get parent and subcategories of them
	 *
	 * @param $categories array categories that need to be processed
	 * @return array
	 */
	public function processCategories($categories) {
		$newCategories = array();
		//if(empty($categories))
		//	return $newCategories;
		foreach ($categories as $category) {
			if ($category->parent_id != 1) {
				$parentCategory = $this->getParent($newCategories, $category);
				//$newCategories[$category->id] = array($category,"subCategories"=>array());
			} else {
				$newCategories[$category->id] = array($category,"subCategories"=>array());
			}
		}
		return $newCategories;
	}

	/**
	 * Process categories by name, Get parent and subcategories of them
	 *
	 * @param $categories array categories that need to be processed
	 * @return array
	 */
	public function processCategoriesByName($categories) {
		$newCategories = array();
		//if(empty($categories))
		//	return $newCategories;
		foreach ($categories as $category) {
			if ($category->parent_id!=1) {
				$parentCategory = $this->getParentByName($newCategories, $category);
			} else {
				$newCategories[$category->name] = array($category,"subCategories"=>array());
			}
		}
		return $newCategories;
	}

	/**
	 * Set category maximum level
	 *
	 * @param $categories array categories
	 * @param $level int level
	 * @param int $maxLevel max level
	 * @param $path string path of categories
	 * @return int returns max level
	 */
	public function setCategoryLevel(&$categories, $level, &$maxLevel, &$path) {
		foreach ($categories as &$cat) {
			//dump($cat[0]->name);
			if ($maxLevel < $level) {
				$maxLevel = $level;
			}
			$cat["level"]= $level;

			$cat["path"]=$path;
			//dump($cat);
			if (is_array($cat["subCategories"]) && count($cat["subCategories"])>0) {
				$path[$level] =array($cat[0]->id,$cat[0]->name,$cat[0]->alias,$cat[0]->icon,$cat[0]->color);
				$this->setCategoryLevel($cat["subCategories"], $level+1, $maxLevel, $path);
			}
		}
		// 		echo "------start unset ".(count($path));
		//  		dump($path);
		unset($path[count($path)]);
		// 		dump($path);
		// 		echo "-----end unset -------------";
		//dump((count($path)-1));
		//unset($path[count($path)-1]);
		return $maxLevel;
	}

	/**
	 * Get category parent from the categories passed as parameters
	 *
	 * @param $categories array subcategories
	 * @param $category object category
	 * @return mixed
	 */
	public function getParent(&$categories, $category) {
		foreach ($categories as &$cat) {
			if ($category->parent_id==$cat[0]->id) {
				$cat["subCategories"][$category->id] = array($category,"subCategories"=>array());
				return $cat;
			} elseif (isset($cat["subCategories"])) {
				$this->getParent($cat["subCategories"], $category);
			}
		}
	}

	/**
	 * Get parent category by name
	 *
	 * @param $categories array subcategories
	 * @param $category object category
	 * @return mixed
	 */
	public function getParentByName(&$categories, $category) {
		foreach ($categories as &$cat) {
			if ($category->parent_id==$cat[0]->id) {
				$cat["subCategories"][$category->name] = array($category,"subCategories"=>array());
				return $cat;
			} elseif (isset($cat["subCategories"])) {
				$this->getParent($cat["subCategories"], $category);
			}
		}
	}

	/**
	 * Find category in the categories set
	 *
	 * @param $categories array categories to search in
	 * @param $category object category searching for
	 * @return mixed|null null if no category is set or category if one is found
	 */
	public function findCategory($categories, &$category) {
		if (!isset($category)) {
			return null;
		}
		foreach ($categories as $cat) {
			if (isset($category[0]) && $category[0]->id==$cat[0]->id) {
				$category=$cat;
				return $cat;
			} elseif (isset($cat["subCategories"])) {
				$this->findCategory($cat["subCategories"], $category);
			}
		}
	}

	/**
	 * Find category by ID
	 *
	 * @param $categories array categories
	 * @param $category object category
	 * @param $id int id
	 * @return mixed
	 */
	public function findCategoryById($categories, &$category, $id) {
		foreach ($categories as $cat) {
			//dump( $cat[0]->id);
			if ($id == $cat[0]->id) {
				$category=$cat;
				return $cat;
			} elseif (isset($cat["subCategories"])) {
				$this->findCategoryById($cat["subCategories"], $category, $id);
			}
		}
	}

	/**
	 * Find category by name
	 *
	 * @param $categories array categories
	 * @param $category object category
	 * @param $categoryName string category name
	 * @return mixed
	 */
	public function findCategoryByName($categories, &$category, $categoryName) {
		foreach ($categories as $cat) {
			if (strcmp($cat[0]->name, $categoryName)==0) {
				$category = $cat;
				return $cat;
			} elseif (isset($cat["subCategories"])) {
				$this->findCategoryByName($cat["subCategories"], $category, $categoryName);
			}
		}
		return $category;
	}

	/**
	 * Get category leafs for the category
	 *
	 * @param $categoryId int category Id
	 * @param $type int category type
	 * @return array
	 */
	public function getCategoryLeafs($categoryId, $type) {
		$category = $this->getCompleteCategoryById($categoryId, $type);
		$leafsIds = array();

		if (isset($category["subCategories"]) && is_array($category["subCategories"]) && count($category["subCategories"])>0) {
			$leafsIds = $this->getAllLeafs($category["subCategories"], $leafsIds);
		//dump($leafsIds);
		} else {
			if (isset($categoryId) && isset($category)) {
				$leafsIds[] = $category[0]->id;
			}
		}
		//dump($leafsIds);
		return $leafsIds;
	}

	/**
	 * Get category childs
	 *
	 * @param $category object category
	 * @return array|null
	 */
	public function getCategoryChilds($category) {
		if (!isset($category)) {
			return null;
		}
		$leafsIds = array();
		if (is_array($category["subCategories"]) && count($category["subCategories"])>0) {
			$leafsIds = $this->getAllLeafs($category["subCategories"], $leafsIds);
		//dump($leafsIds);
		} else {
			//dump($category);
			$leafsIds[] = $category[0]->id;
		}

		return $leafsIds;
	}

	/**
	 * Get all leafs
	 *
	 * @param $categories array categories
	 * @param $leafIds array leafs ids
	 * @return array
	 */
	public function getAllLeafs($categories, &$leafIds) {
		foreach ($categories as &$cat) {
			//if(count($cat["subCategories"])==0) {
			//dump($cat);
			$leafIds[]=$cat[0]->id;
			//dump($leafIds);
			//}
			if (is_array($cat["subCategories"])) {
				$this->getAllLeafs($cat["subCategories"], $leafIds);
			}
		}
		return $leafIds;
	}

	/**
	 * Convert categories levels
	 *
	 * @param $categories array categories
	 * @param $result array converted categories
	 */
	public function convertCategories($categories, &$result) {
		foreach ($categories as $cat) {
			if (!empty($cat["level"])) {
				$cat[0]->level = $cat["level"]-1;
			}
			if(isset($cat[0])){
				$result[] = $cat[0];
			}else{
				$result[] = $cat;
			}

			if (isset($cat["subCategories"])) {
				$this->convertCategories($cat["subCategories"], $result);
			}
		}
	}

	/**
	 * Create root category element if none exists
	 */
	public function createRootElement() {
		$db =JFactory::getDBO();

		$query = "select * from #__jbusinessdirectory_categories where id =1 ";
		$db->setQuery($query);
		$rootElement = $db->loadObject();

		if (!empty($rootElement) && $rootElement->name != "Root" && $rootElement->name != "root") {
			//get max number
			$query = "select max(id) as max_id from #__jbusinessdirectory_categories";
			$db->setQuery($query);
			$max = $db->loadObject()->max_id;
			$max = $max + 1;

			$query = "update #__jbusinessdirectory_categories set id = $max where id =1";
			$db->setQuery($query);
			$db->execute();

			$query = "update #__jbusinessdirectory_categories set parent_id = $max where parent_id = 1";
			$db->setQuery($query);
			$db->execute();
		//change first id
		} elseif (empty($rootElement)) {
			$query = "insert into #__jbusinessdirectory_categories(id,parent_id,name,alias) values(1,0,'Root','root')";
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Update category structure, parent, levels and ordering
	 */
	public function updateCategoryStructure() {
		$db =JFactory::getDBO();

		$query = "select max(lft) as max_left from #__jbusinessdirectory_categories ";
		$db->setQuery($query);
		$maxLft = $db->loadObject()->max_left;

		$this->createRootElement();

		$query = "select * from #__jbusinessdirectory_categories where id =1 ";
		$db->setQuery($query);
		$rootElement = $db->loadObject();

		if (empty($rootElement)) {
			//insert Root category
			$query = "INSERT INTO #__jbusinessdirectory_categories(`id`,`parent_id`,`lft`,`rgt`,`level`,`name`,`alias`,`description`,`published`,`imageLocation`,`markerLocation`,`path`) VALUES ('1', '0', '0', '286', '0', 'Root', 'root', '', '1', '', '', NULL)	";
			$db->setQuery($query);
			$db->execute();
		}

		$query = "update #__jbusinessdirectory_categories set parent_id = 1 where parent_id = 0 and id>1";
		$db->setQuery($query);
		$db->execute();
		//change first id

		$categories = $this->getCategories();
		//dump($categories);
		$result = array();
		$this->convertCategories($categories, $result);
		$categories = $result;
		$lft = 1;
		$rgt = 2;

		foreach ($categories as $category) {
			if (!empty($category) && !empty($category->id)) {
				$id = $category->id;
				if ($id > 1) {
					$category->lft = $lft;
					$category->rgt = $rgt;
					//dump($category);
					$query = "update #__jbusinessdirectory_categories set lft = $category->lft, rgt=$category->rgt, level = ($category->level+1)  where id = $category->id";
					$db->setQuery($query);
					$db->execute();

					$lft = $lft+2;
					$rgt = $rgt+2;
				}
			}
		}

		$query = "update #__jbusinessdirectory_categories set rgt = $rgt where id = 1";
		$db->setQuery($query);
		$db->execute();
		//exit;
	}

	/**
	 * Function that order categories on module, and menu items, based on their structure.
	 *
	 * @param $categories array with all the structure of categories and their subcategories
	 * @param bool $subcategoriesSort true or false if we are sorting subcategories
	 * @param bool $onModuleSort true or false if we are sorting categories on module
	 * @param bool $changeStructure true or false if we are sorting inside the first lvl of subcategories.. since the
	 *                              first lvl of subcategories will have a different structure from the other level inside it
	 * @param bool $firstLvl true or false if we are still on the lvl of the categories(which is the first lvl also for
	 *                         subcategories), right after we go on and sort them.
	 * @return mixed
	 *
	 * @since 4.9.0
	 */
	public function sortCategories($categories, $subcategoriesSort = false, $onModuleSort=false, $changeStructure = false, $firstLvl = false) {
		foreach ($categories as $key => &$category) {
			if ($onModuleSort) {
				if (isset($category->subcategories)) {
					if (!empty($category->subcategories)) {
						$category->subcategories = $this->sortCategories($category->subcategories, true, true, false);
					}
				}
			} else {
				if ($changeStructure) {
					$cat = $category['subCategories'];
				} else {
					$cat = $category[0]['subCategories'];
				}
				if (isset($cat)) {
					if (!empty($cat)) {
						if ($changeStructure) {
							$category['subCategories'] = $this->sortCategories($cat, true, false, true, false);
						} else {
							$category[0]['subCategories'] = $this->sortCategories($cat, true, false, true, true);
						}
					}
				}
			}
		}

		usort($categories, function ($a, $b) use ($onModuleSort,$firstLvl) {
			if ($onModuleSort) {
				$first = $a;
				$second = $b;
			} else {
				if ($firstLvl) {
					$first = $a[0];
					$second = $b[0];
				} elseif (!$firstLvl) {
					if (!isset($a[0]->name)) {
						$first = $a[0][0];
						$second = $b[0][0];
					} else {
						$first = $a[0];
						$second = $b[0];
					}
				}
			}


			$firstItem = strtoupper($first->name);
			$secondItem = strtoupper($second->name);
			return strcasecmp($firstItem, $secondItem);
		});

		return $categories;
	}

	/**
	 * Function that order categories by their names.
	 *
	 * @param $categories array categories
	 * @return mixed
	 */
	public function sortCategoryView($categories) {
		foreach ($categories as $key => &$category) {
			//check if is object because we have this case when menu item is only one category
			if (!is_object($category)) {
				if (!empty($category['subCategories'])) {
					$category['subCategories'] = $this->sortCategoryView($category['subCategories']);
				}
			}
		}

		uasort($categories, function ($a, $b) {
			if (is_object($a) & is_object($b)) {
				if (isset($a->name)) {
					$first = $a->name;
				} else {
					$first = "";
				}

				if (isset($b->name)) {
					$second = $b->name;
				} else {
					$second = "";
				}
			} else {
				if (isset($a[0]->name)) {
					$first = $a[0]->name;
				} else {
					$first = "";
				}

				if (isset($b[0]->name)) {
					$second = $b[0]->name;
				} else {
					$second = "";
				}
			}
			$firstItem = strtoupper($first);
			$secondItem = strtoupper($second);
			return strcasecmp($firstItem, $secondItem);
		});

		return $categories;
	}
}
