<?php
/**
 * @package    WPBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2021 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JFactory::getApplication()->input->get('option').DS.'tables');
require_once(BD_HELPERS_PATH.'/category_lib.php');

class JBusinessDirectoryModelVideo extends JModelItem {
	public $video = null;

	public function __construct() {
		parent::__construct();

		$this->context="com_jbusinessdirectory.video.details";

		$this->appSettings = JBusinessUtil::getApplicationSettings();
		$this->videoId = JFactory::getApplication()->input->get('videoId');
		$this->videoId = intval($this->videoId);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'Videos', $prefix = 'Table', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}


	/**
	 * Method to get a cache id based on the listing id.
	 *
	 * @param unknown $params
	 * @param string $id
	 * @return string
	 */
	protected function getCacheId($id) {
		return md5($this->context . ':' . $id);
	}

	/**
	 * Get video based on video id from cache or from database
	 *
	 * @return offer data
	 */
	public function getVideo() {
		if (empty($this->videoId)) {
			return;
		}

		$videoData = null;
		$cacheIdentifier = $this->getCacheId($this->videoId);
		try {
			if ($this->appSettings->enable_cache) {
				$cache = JCache::getInstance();
				$videoData = $cache->get($cacheIdentifier);
				if (empty($videoData)) {
					$videoData = $this->getVideoData();
					$cache->store($videoData, $cacheIdentifier);
				}
			} else {
				$videoData = $this->getVideoData();
			}
		} catch (RuntimeException $e) {
			$this->setError($e->getMessage());
			return null;
		}

		return $videoData;
	}


	/**
	 * Get item function
	 *
	 * @return void
	 */
	public function getItem($pk = NULL){
		return $this->getVideo();
	}


	public function getVideoData() {
		$videosTable = JTable::getInstance("Videos", "Table");
		$video = $videosTable->getVideo($this->videoId);
		if (empty($video)) {
			return $video;
		}

		$this->video = $video;
		$userId = JBusinessUtil::getUser()->ID;

		if (!empty($video->categories)) {
			$video->categories = explode('#|', $video->categories);
			foreach ($video->categories as $k=>&$category) {
				$category = explode("|", $category);
			}
		}

		$maxCategories = !empty($video->categories)?count($video->categories):0;
		if (!empty($this->appSettings->max_categories)) {
			$maxCategories = $this->appSettings->max_categories;
		}

		if (!empty($video->categories)) {
			$video->categories = array_slice($video->categories, 0, $maxCategories);
		}

		//dispatch load offer
//		JFactory::getApplication()->triggerEvent('onAfterJBDLoadOffer', array($offer)); //TODO is this needed for video?

		return $video;
	}

	/**
	 * Get the video based on ID
	 * @param $videoId
	 * @return mixed
	 */
	public function getPlainVideo($videoId) {
		$videosTable = $this->getTable("Videos", "Table");
		$video = $videosTable->getVideo($videoId);

		return $video;
	}

	/**
	 * Get the releated videos
	 *
	 * @return void
	 */
	public function getRelatedVideos(){
		$videosTable = JTable::getInstance("Videos", "Table");
		$searchDetails = array();
		$videos =  $videosTable->getVideosByCategories($searchDetails);

		return $videos;
	}
}
?>

