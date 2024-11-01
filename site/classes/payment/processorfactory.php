<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class ProcessorFactory {
	
	//get processor instance based on the class name
	public function getProcessor($processorType) {
		if ($processorType == "") {
			$processorType = PROCESSOR_CASH;
		}

		if (class_exists($processorType)) {
			$processor = new $processorType();
		} else {
			throw new Exception("Processor $processorType does not exist");
		}

		return $processor;
	}
}
