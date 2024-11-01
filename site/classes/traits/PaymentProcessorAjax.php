<?php
/**
 * @package    J-BusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com/
 * @copyright  Copyright (C) 2007 - 2022 CMSJunkie. All rights reserved.
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */
defined('_JEXEC') or die('Restricted access');

trait PaymentProcessorAjax {
	public static $responseSuccess = 1;
	public static $responseError = 0;

	/**
	 * Renders the payment fields HTML based on field configuration
	 *
	 * @param $fields array of payment processor fields
	 *
	 * @return string
	 *
	 * @since 5.4.0
	 */
	public function getPaymentFieldsHTML($fields) {
		$html = "";

		// live fields
		foreach ($fields as $key => $val) {
			$html .= "
			           <div class='row processor-fields mb-1 base-fields' id='processor_field_0'>
			                <div class='col-md-4'>
			                    <label class='hasTooltip' for='column_name[]'>
			                        " . JText::_('LNG_' . strtoupper($key), true) . "
								</label>
			                    <span id='column_name'>
			                        <input type='hidden' name='column_name[]' id='column_name[]' value='$key' />
								</span>
							</div>
							
							<div class='col-md-4'>
								<span id='column_value'>
									<input type='text' name='column_value[]' id='column_value[]'
										   placeholder='$val' size='32' maxlength='255' />
								</span>
								<div class='clear'></div>
							</div>  
							
							<div class='col-md-3 d-flex align-items-center'>
							    <select id='mode' name='column_mode[]' class='input_sel form-control' >
                                    <option value = '0' selected>".JText::_('LNG_TEST')."</option>
                                    <option value = '1'>".JText::_('LNG_LIVE')."</option>
                                </select>
							</div>

			           </div>
					";
		}

		// test fields
		foreach ($fields as $key => $val) {
			$html .= "
			           <div class='row processor-fields mb-1 base-fields' id='processor_field_0'>
			                <div class='col-md-4'>
			                    <label class='hasTooltip' for='column_name[]'>
			                        " . JText::_('LNG_' . strtoupper($key), true) . "
								</label>
			                    <span id='column_name'>
			                        <input type='hidden' name='column_name[]' id='column_name[]' value='$key' />
								</span>
							</div>
							
							<div class='col-md-4'>
								<span id='column_value'>
									<input type='text' name='column_value[]' id='column_value[]'
										   placeholder='$val' size='32' maxlength='255' />
								</span>
								<div class='clear'></div>
							</div>  
							
							<div class='col-md-3 d-flex align-items-center'>
							    <select id='mode' name='column_mode[]' class='input_sel form-control' >
                                    <option value = '0'>".JText::_('LNG_TEST')."</option>
                                    <option value = '1' selected>".JText::_('LNG_LIVE')."</option>
                                </select>
							</div>

			           </div>
					";
		}

		return $html;
	}

	/**
	 * Ajax endpoint, will return processor fields HTML based on type
	 */
    public function getPaymentProcessorFields() {
        $type        = JFactory::getApplication()->input->getString('processor_type');
        $response          = new stdClass();
        $response->data    = null;
        $response->status  = self::$responseSuccess;
        $response->message = null;

        $fieldConfig = JBusinessUtil::getPaymentProcessorFields();
        if (!isset($fieldConfig[$type])) {
            $response->status = self::$responseError;
            $response->data   = JText::_('LNG_PAYMENT_PROCESSOR_TYPE_NOT_FOUND');

            JBusinessUtil::sendJsonResponse($response->data, $response->status, $response->message);
        }

        $html           = $this->getPaymentFieldsHTML($fieldConfig[$type]['fields']);
        $response->data = $html;

        JBusinessUtil::sendJsonResponse($response->data, $response->status, $response->message);
    }

}
