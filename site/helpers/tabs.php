<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class JBDTabs
 */
class JBDTabs {
	private $joomlaVersion;
	private $options;
	private $tabType;

	private $tabSetStart;
	private $tabAdd;
	private $tabEnd;
	private $tabSetEnd;

	private $useDefault;

	private $unsupported = array(
		'uitab'
	);

	/**
	 * JBDTabs constructor.
	 *
	 * @param null $tabType string type of the tab
	 */
	public function __construct($tabType = null) {
		if (defined('JVERSION')) {
			$this->joomlaVersion = (int) JVERSION;
		} else {
			$j                   = new JVersion();
			$this->joomlaVersion = (int) $j->getShortVersion();
		}

		$this->tabType = $tabType;
		if (empty($tabType)) {
			$this->tabType = 'uitab';
		}

		$this->tabSetStart = $this->tabType . '.startTabSet';
		$this->tabAdd      = $this->tabType . '.addTab';
		$this->tabEnd      = $this->tabType . '.endTab';
		$this->tabSetEnd   = $this->tabType . '.endTabSet';

		if ($this->joomlaVersion == 3) {
			$this->tabType = $tabType;
			if (empty($tabType) || in_array($tabType, $this->unsupported)) {
				$this->tabType     = 'tabs';
				$this->tabSetStart = 'tabs.start';
				$this->tabAdd      = 'tabs.panel';
				$this->tabEnd      = '';
				$this->tabSetEnd   = 'tabs.end';

				$this->useDefault = true;
			}
		}
	}

	/**
	 * Starts the tab group/set
	 *
	 * @param $tabGroupName string Name of the tab group
	 *
	 * @return string HTML
	 */
	public function startTabSet($tabGroupName) {
		return JHtml::_($this->tabSetStart, $tabGroupName, $this->options);
	}

	/**
	 * Adds a single tab
	 *
	 * @param $tabGroupName string name of the tab group where the tab belongs to
	 * @param $tabId        string ID of the tab
	 * @param $tabName      string name of the tab
	 *
	 * @return mixed
	 */
	public function addTab($tabGroupName, $tabId, $tabName) {
		if ($this->useDefault && $this->joomlaVersion == 3) {
			$output = JHtml::_($this->tabAdd, $tabName, $tabId);
		} else {
			$output = JHtml::_($this->tabAdd, $tabGroupName, $tabId, $tabName);
		}

		return $output;
	}

	/**
	 * Ends a tab
	 *
	 * @return string
	 */
	public function endTab() {
		if ($this->useDefault && $this->joomlaVersion == 3) {
			$output = "";
		} else {
			$output = JHtml::_($this->tabEnd);
		}

		return $output;
	}

	/**
	 * Ends the tab group/set
	 *
	 * @return mixed
	 */
	public function endTabSet() {
		return JHtml::_($this->tabSetEnd);
	}

	/**
	 * @return array of options
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * @param mixed $options array
	 */
	public function setOptions($options) {
		$this->options = $options;
	}

	/**
	 * @return string
	 */
	public function getTabType() {
		return $this->tabType;
	}

	/**
	 * @param string $tabType
	 */
	public function setTabType($tabType) {
		$this->tabType = $tabType;
	}
}
