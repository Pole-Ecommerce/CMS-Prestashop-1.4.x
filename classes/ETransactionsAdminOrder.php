<?php
/**
 * E-Transactions PrestaShop Module
 *
 * Feel free to contact E-Transactions at support@e-transactions.fr for any
 * question.
 *
 * LICENSE: This source file is subject to the version 3.0 of the Open
 * Software License (OSL-3.0) that is available through the world-wide-web
 * at the following URI: http://opensource.org/licenses/OSL-3.0. If
 * you did not receive a copy of the OSL-3.0 license and are unable 
 * to obtain it through the web, please send a note to
 * support@e-transactions.fr so we can mail you a copy immediately.
 *
 * @author Olivier - BM Services (http://www.bm-services.com)
 * @copyright 2012-2015 E-Transactions
 * @license http://opensource.org/licenses/OSL-3.0
 * @link http://www.e-transactions.fr/
 * @since 2
 **/

if (!defined('_PS_VERSION_')) {
  exit;
}

require_once dirname(__FILE__).'/ETransactionsAbstractAdmin.php';

/**
 * Admin order details page helper
 */
class ETransactionsAdminOrder extends ETransactionsAbstractAdmin {
	private function _writeDetails(ETransactionsHtmlWriter $w, array $details) {
		$this->_writeCommonDetails($w, $details);
		$this->_writeEndDetails($w);
	}

	private function _writeCommonDetails(ETransactionsHtmlWriter $w, array $details) {
		$label = $this->l('Payment details');
		$w->blockStart('ETRANS_details', $label, $this->getImagePath().'etransactions-small.png');

		$tpl = '<p><strong>%s</strong> %s</p>';

		$w->html('<div class="row"><div class="col-sm-6">');

		// Transaction id
		$w->html(sprintf(
			$tpl,
			$this->l('Ref. ETransactions:'),
			$w->escape($details['id_transaction'])
		));

		// Payment method
		$img = $this->getModule()->getMethodImageUrl(strtoupper($details['carte']));
		$text = sprintf('<img style="vertical-align:middle;" src="%s" />', $img);
		$w->html(sprintf(
			$tpl,
			$this->l('Payment Method:'),
			$text
		));

		// Card country
		$text = !empty($details['pays']) ? $w->escape($details['pays']) : $this->l('unknown');
		$w->html(sprintf(
			$tpl,
			$this->l('Country card:'),
			$text
		));

		// IP country
		$text = !empty($details['ip']) ? $w->escape($details['ip']) : $this->l('unknown');
		$w->html(sprintf(
			$tpl,
			$this->l('Pays IP:'),
			$text
		));

		// 3-D Secure status
		if (!empty($details['secure'])) {
			$text = $details['secure'] == 'O' ? $this->l('Yes') : $this->l('No');
			$w->html(sprintf(
				$tpl,
				$this->l('Warranty 3D-Secure:'),
				$text
			));
		}

		// Processing date
		$text = $w->escape(preg_replace('/^([0-9]{2})([0-9]{2})([0-9]{4})$/', '$1/$2/$3', $details['date']));
		$w->html(sprintf(
			$tpl,
			$this->l('Processing date:'),
			$text
		));		

		$w->html('</div><div class="col-sm-6">');
	}

	private function _writeCapturableDetails(ETransactionsHtmlWriter $w, array $details) {
		$this->_writeCommonDetails($w, $details);

		$w->html(sprintf(
			'<p>%s</p>',
			$this->l('The transaction can be charged only once')
		));
		// Capture all amount
		$w->html('<p class="left">');
		$w->html(sprintf(
			'<form id="ETRANS_capture_all" method="post" action="%s">',
			$w->escape($_SERVER['REQUEST_URI'])
		));
		$w->html(sprintf(
			'<input type="hidden" name="id_order" value="%d" />',
			$details['id_order']
		));
		$w->html('<input type="hidden" name="order_action" value="capture_all" />');
		$w->button($this->l('Capture total transaction'), 'submit');
		$w->html('</form>');
		$w->html('</p>');

		// Capture amount
		$w->html('<p class="left">');
		$w->html('<form id="ETRANS_capture_amount">');
		$w->button($this->l('Capture of an amount'), 'submit');
		$w->html('</form>');
		$w->html('</p>');

		// Capture amount
		$w->html('<p class="left">');
		$w->html(sprintf(
			'<form id="ETRANS_capture_amount_input" method="post" action="%s" style="display: none;">',
			$w->escape($_SERVER['REQUEST_URI'])
		));
		$w->html(sprintf(
			'<input type="hidden" name="id_order" value="%d" />',
			$details['id_order']
		));
		$w->html('<input type="hidden" name="order_action" value="capture_amount" />');
		$w->html('<input type="text" name="amountCapture" value="" /> ');
		$w->button($this->l('Capture this amount'), 'submit');
		$w->html('</form>');
		$w->html('</p>');

		// Cancel item(s)
		if (Configuration::get('PS_ORDER_RETURN')) {
			$w->html(sprintf(
				'<p>%s</p>',
				$this->l('Canceling a product will not capture the transaction.')
			));
			$w->html('<p class="left">');
			$w->html(sprintf(
				'<form id="ETRANS_cancel_item%s">',
				version_compare(_PS_VERSION_,'1.5','<') ? '14' : ''
			));
			$w->button($this->l('Cancel a product'), 'submit');
			$w->html('</form>');
			$w->html('</p>');
		}

		$this->_writeEndDetails($w);

		if (version_compare(_PS_VERSION_,'1.6','<')) {
			$js = <<<EOF
(function($) {
	$(document).ready(function() {
		$('#ETRANS_capture_all, #ETRANS_capture_amount_input').submit(function() {
			return confirm(%s);
		});
		$('#ETRANS_cancel_item14').submit(function() {
			$('html, body').animate({ scrollTop:$('#orderProducts').offset().top }, 'slow');
			return false;
		});
		$('#ETRANS_cancel_item').submit(function() {
			$('#desc-order-standard_refund').click();
			return false;
		});
		$('#ETRANS_capture_amount').submit(function() {
			$('#ETRANS_capture_amount_input').show('normal');
			return false;
		});
	});
})(jQuery);
EOF;
		}
		else {
			$js = <<<EOF
(function($) {
	$(document).ready(function() {
		$('#ETRANS_capture_all, #ETRANS_capture_amount_input').submit(function() {
			return confirm(%s);
		});
		$('#ETRANS_cancel_item14').submit(function() {
			$('html, body').animate({ scrollTop:$('#orderProducts').offset().top }, 'slow');
			return false;
		});
		$('#ETRANS_cancel_item').submit(function() {
			$('#desc-order-standard_refund').click();
			$('body').animate({scrollTop : $('#refundForm').offset().top - $('body').scrollTop() }, 1000, 'swing');
			return false;
		});
		$('#ETRANS_capture_amount').submit(function() {
			$('#ETRANS_capture_amount_input').show('normal');
			return false;
		});
	});
})(jQuery);
EOF;
		}
		$w->js(sprintf($js, json_encode($this->l('Are you sure?'))));
	}

	private function _writeEndDetails(ETransactionsHtmlWriter $w) {
		$w->html('</div></div>');

		$w->blockEnd();	
	}


	private function _writeRefundableDetails(ETransactionsHtmlWriter $w, array $details) {
		$this->_writeCommonDetails($w, $details);

		switch ($details['payment_by']) {
			case 'ETransactionsSystem':
				$this->_writeRefundableStandard($w, $details);
				break;

			case 'ETransactionsSystemRecurring':
				$this->_writeRefundableRecurring($w, $details);
				break;
		}

		$this->_writeEndDetails($w);
	}

	private function _writeRefundableRecurring(ETransactionsHtmlWriter $w, array $details) {
		$partialRefund = 0;
		if (version_compare(_PS_VERSION_,'1.5','>=')) {
			$partialRefund = $this->getHelper()->getAmountPartialRefund($details['id_order']);
		}

		// Refund first payment
		if ($details['refund_amount'] == 0) {
			$w->html('<p class="left">');
			$w->html(sprintf(
				'<form id="ETRANS_refund_first" method="post" action="%s">',
				$w->escape($_SERVER['REQUEST_URI'])
			));
			$w->html(sprintf(
				'<input type="hidden" name="id_order" value="%d" />',
				$details['id_order']
			));
			$w->html('<input type="hidden" name="order_action" value="refund_first" />');
			$w->button($this->l('Refund the first payment'), 'submit');
			$w->html('</form>');
			$w->html('</p>');
		}

		// Cancel recurring
		if (($details['payment_status'] != 'canceled') && ($details['payment_status'] != 'canceled/refundRecurring')) {
			$w->html('<p class="left">');
			$w->html(sprintf(
				'<form id="ETRANS_refund_cancel" method="post" action="%s">',
				$w->escape($_SERVER['REQUEST_URI'])
			));
			$w->html(sprintf(
				'<input type="hidden" name="id_order" value="%d" />',
				$details['id_order']
			));
			$w->html('<input type="hidden" name="order_action" value="cancel_recurring" />');
			$w->button($this->l('Cancel the next recurring payment'), 'submit');
			$w->html('</form>');
			$w->html('</p>');
		}


				$js = <<<EOF
(function($) {
	$(document).ready(function() {
		$('#ETRANS_refund_first, #ETRANS_refund_cancel').submit(function() {
			return confirm(%s);
		});
	});
})(jQuery);
EOF;
		$w->js(sprintf($js, json_encode($this->l('Are you sure?'))));
	}

	private function _writeRefundableStandard(ETransactionsHtmlWriter $w, array $details) {
		$partialRefund = 0;			
		if (version_compare(_PS_VERSION_,'1.5','>=')) {
			$partialRefund = $this->getHelper()->getAmountPartialRefund($details['id_order']);
		}

		$text = $this->l('The transaction can be refund many times');
		$w->html(sprintf('<p>%s</p>', $text));

		$order = new Order(intval($details['id_order']));
		if (Validate::isLoadedObject($order)) {
			$currency = new Currency(intval($order->id_currency));
			$amountScale = $this->getHelper()->getCurrencyScale($order);

			$possibleRefund = (float)($details['amount'] - $details['refund_amount']) / $amountScale;
			if ($possibleRefund - $partialRefund > 0) {
				if ($possibleRefund > 0) {
					$tpl = '<p>%s %s %s</p>';
					$w->html(sprintf(
						$tpl,
						$this->l('It is possible to repay'),
						(string)($possibleRefund - $partialRefund),
						$currency->sign
					));
				}


				// Refund all amount
				$w->html('<p class="left">');
				$w->html(sprintf(
					'<form id="ETRANS_refund_all" method="post" action="%s">',
					$w->escape($_SERVER['REQUEST_URI'])
				));
				$w->html(sprintf(
					'<input type="hidden" name="id_order" value="%d" />',
					$details['id_order']
				));
				$w->html('<input type="hidden" name="order_action" value="refund_all" />');
				$w->button($this->l('Refund total transaction'), 'submit');
				$w->html('</form>');
				$w->html('</p>');

				// Refund item(s)
				if (Configuration::get('PS_ORDER_RETURN')) {
					$w->html('<p class="left">');
					$w->html(sprintf(
						'<form id="ETRANS_refund_item%s">',
						version_compare(_PS_VERSION_,'1.5','<') ? '14' : ''
					));
					$w->button($this->l('Refund an item'), 'submit');
					$w->html('</form>');
					$w->html('</p>');
				}

				// Refund amount
				$w->html('<p class="left">');
				$w->html('<form id="ETRANS_refund_amount">');
				$w->button($this->l('Refund of an amount'), 'submit');
				$w->html('</form>');
				$w->html('</p>');

				// Refund amount form
				$w->html('<p class="left">');
				$w->html(sprintf(
					'<form id="ETRANS_refund_amount_input" method="post" action="%s" style="display:none;">',
					$w->escape($_SERVER['REQUEST_URI'])
				));
				$w->html(sprintf(
					'<input type="hidden" name="id_order" value="%d" />',
					$details['id_order']
				));
				$w->html('<input type="hidden" name="order_action" value="refund_amount" />');
				$w->html('<input type="text" name="amountRefund" value="" /> ');
				$w->button($this->l('Refund this amount'), 'submit');
				$w->html('</form>');
				$w->html('</p>');

				$js = <<<EOF
(function($) {
	$(document).ready(function() {
		$('#ETRANS_refund_all, #ETRANS_refund_amount_input').submit(function() {
			return confirm(%s);
		});
		$('#ETRANS_refund_item14').submit(function() {
			$('html, body').animate({ scrollTop:$('#orderProducts').offset().top }, 'slow');
			return false;
		});
EOF;
				if (Configuration::get('PS_ORDER_RETURN')) {
				$js .= <<<EOF
		$('#ETRANS_refund_item').submit(function() {
			$('#desc-order-standard_refund').click();
			return false;
		});
EOF;
				}
				$js .= <<<EOF
		$('#ETRANS_refund_amount').submit(function() {
			$('#ETRANS_refund_amount_input').show('normal');
			return false;
		});
	});
})(jQuery);
EOF;
				$w->js(sprintf($js, json_encode($this->l('Are you sure?'))));
			}
		}
	}

	public function getContent(ETransactionsHtmlWriter $w, array $params) {
		$orderId = $params['id_order'];
		$details = $this->getHelper()->getOrderDetails($orderId);

		// Not handled by ETransactions
		if (empty($details)) {
			return null;
		}
		
		// Can be refunded?
		else if ($this->getHelper()->canRefund($orderId)) {
			$this->_writeRefundableDetails($w, $details);
		}

		// Waiting for capture?
		else if ($this->getHelper()->canCapture($orderId)) {
			$this->_writeCapturableDetails($w, $details);
		}

		// All other cases
		else {
			$this->_writeDetails($w, $details);
		}
	}

	public function _processCaptureAll(ETransactionsHtmlWriter $w, array $details) {
		$orderId = $details['id_order'];

		// Load order
		$order = new Order($orderId);
		if (!Validate::isLoadedObject($order)) {
			$w->alertError($this->l('Error when making capture request'));
			return;
		}

		$result = $this->getHelper()->makeCaptureAll($order, $details);

		switch ($result) {
			case 0:
				$w->alertConf($this->l('Funds have been captured.'));
				break;
			case 1:
				$w->alertError($this->l('Capture of funds unsuccessful. Please see log message!'));
				break;
			case 2:
				$w->alertError($this->l('Error when making capture'));
				break;
		}
	}

	public function _processCaptureAmount(ETransactionsHtmlWriter $w, array $details) {
		$orderId = $details['id_order'];

		// Load order
		$order = new Order($orderId);
		if (!Validate::isLoadedObject($order)) {
			$w->alertError($this->l('Error when making capture request'));
			return;
		}

		$result = $this->getHelper()->makeCaptureAmount($order, $details, Tools::getValue('amountCapture'));

		switch ($result) {
			case 0:
				$w->alertConf($this->l('Funds have been captured.'));
				break;
			case 1:
				$w->alertError($this->l('Capture of funds unsuccessful. Please see log message!'));
				break;
			case 2:
				$w->alertError($this->l('Error when making capture'));
				break;
			case 3:
				$w->alertError($this->l('The capture amount is too high.'));
				break;
		}
	}

	public function _processCancelRecurring(ETransactionsHtmlWriter $w, array $details) {
		$orderId = $details['id_order'];

		// Load order
		$order = new Order($orderId);
		if (!Validate::isLoadedObject($order)) {
			$w->alertError($this->l('Error when canceling recurring payment.'));
			return false;
		}

		$result = $this->getHelper()->deleteRecurringPayment($order, $details);

		if ($result === false) {
			$message = $this->l('Unable to cancel recurring payment.')."\r\n";
			$message .= $this->l('For more information logon to the E-Transactions Back-Office.');
			$w->alertError($message);
		}

		else {
			$message = $this->l('Recurring payment canceled.');
			$w->alertConf($message);
		}
	}

	public function _processRefundAmount(ETransactionsHtmlWriter $w, array $details) {
		$orderId = $details['id_order'];

		// Load order
		$order = new Order($orderId);
		if (!Validate::isLoadedObject($order)) {
			$w->alertError($this->l('Error when making refund request'));
			return false;
		}

		$result = $this->getHelper()->makeRefundAmount($order, $details, Tools::getValue('amountRefund'));

		switch ($result) {
			case 0:
				$w->alertConf($this->l('Refund has been made.'));
				break;
			case 1:
				$w->alertError($this->l('Refund request unsuccessful. Please see log message!'));
				break;
			case 2:
				$w->alertError($this->l('Error when making refund request'));
				break;
			case 3:
				$w->alertError($this->l('The refund amount is too high.'));
				break;

		}
	}

	public function _processRefundAll(ETransactionsHtmlWriter $w, array $details) {
		$orderId = $details['id_order'];

		// Load order
		$order = new Order($orderId);
		if (!Validate::isLoadedObject($order)) {
			$w->alertError($this->l('Error when making refund request'));
			return false;
		}

		$result = $this->getHelper()->makeRefundAll($order, $details);

		switch ($result) {
			case 0:
				$w->alertConf($this->l('Refund has been made.'));
				break;
			case 1:
				$w->alertError($this->l('Refund request unsuccessful. Please see log message!'));
				break;
			case 2:
				$w->alertError($this->l('Error when making refund request'));
				break;
		}
	}

	public function _processRefundFirst(ETransactionsHtmlWriter $w, array $details) {
		$orderId = $details['id_order'];

		// Load order
		$order = new Order($orderId);
		if (!Validate::isLoadedObject($order)) {
			$w->alertError($this->l('Error when making refund request'));
			return false;
		}

		$result = $this->getHelper()->makeRefundAll($order, $details);

		switch ($result) {
			case 0:
				$w->alertConf($this->l('Refund has been made.'));
				break;
			case 1:
				$w->alertError($this->l('Refund request unsuccessful. Please see log message!'));
				break;
			case 2:
				$w->alertError($this->l('Error when making refund request'));
				break;
		}
	}

	public function processAction(ETransactionsHtmlWriter $w) {
		$details = $this->getHelper()->getOrderDetails(Tools::getValue('id_order'));

		// If handled by ETransactions
		if (!empty($details)) {
			switch (Tools::getValue('order_action')) {
				case 'capture_amount':
					$this->_processCaptureAmount($w, $details);
					break;
				case 'capture_all':
					$this->_processCaptureAll($w, $details);
					break;
				case 'cancel_recurring':
					$this->_processCancelRecurring($w, $details);
					break;
				case 'refund_amount':
					$this->_processRefundAmount($w, $details);
					break;
				case 'refund_all':
					$this->_processRefundAll($w, $details);
					break;
				case 'refund_first':
					$this->_processRefundFirst($w, $details);
					break;
			}
		}
	}
}