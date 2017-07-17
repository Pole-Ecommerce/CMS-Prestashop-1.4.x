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
 * @author Web In Color (original version, mostly modified)
 * @author Olivier - BM Services (http://www.bm-services.com)
 * @copyright 2012-2015 E-Transactions
 * @license http://opensource.org/licenses/OSL-3.0
 * @link http://www.e-transactions.fr/
 * @since 2
 **/
 
 
if (!defined('_PS_VERSION_')) {
	exit;
}

require_once(dirname(__FILE__) . '/classes/common.inc.php');

class ETransactionsEpayment extends PaymentModule {
	private $_requestId;
	private $_config;
	private $_helper;
	private $_html = '';
	private $_message = '';

	public function __construct() {
		$this->_requestId = time();
		$this->_config = new ETransactionsConfig();
		$this->_helper = new ETransactionsHelper($this);
		$this->name = 'ETransactionsEpayment';
		$this->tab = 'payments_gateways';
		$this->version = '2.0.9.3';
		$this->author = 'E-Transactions';
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = 'E-Transactions - Epayment';
		$this->description = $this->l('In one integration, offer many payment methods, get a customized secure payment page, multi-lingual and multi-currency and offer debit on delivery or in 3 installments without charges for your customers.');
	}

	public function getConfig() {
		return $this->_config;
	}

	
	public function getContent() {
		$html = '';
		require_once(dirname(__FILE__) . '/classes/ETransactionsAdminConfig.php');
		$admin = new ETransactionsAdminConfig($this);
		if (Tools::isSubmit('admin_action')) {
			$html .= $admin->processAction();
		}
		$html .= $admin->getContent();
		return $html;
	}
	public function getContext() {
		return $this->context;
	}


	
	public function getHelper() {
		return $this->_helper;
	}
	
	

	public function getImagePath() {
		return $this->getPath() . 'img/';
	}


	public function getMethodImageUrl($type) {
		$ext = 'png';
		$file = dirname(__FILE__) . '/img/' . $type . '.';
		foreach (array('png', 'gif', 'jpg') as $item) {
			if (is_file($file.$item)) {
				$ext = $item;
			}
		}
	   return $this->getImagePath() . $type . '.' . $ext;
	   }
  
	   
	   
	public function getPath() {
		return $this->_path;
	}

	
	
	public function hookAdminOrder($params) {
		require_once dirname(__FILE__) . '/classes/ETransactionsAdminOrder.php';
		$admin = new ETransactionsAdminOrder($this);
		$w = new ETransactionsHtmlWriter();
		// Process actions if needed
		if (Tools::isSubmit('order_action')) {
			$admin->processAction($w);
		}
		// Generate view
		$admin->getContent($w, $params);
		return (string)$w;
	}

	
	
	public function hookCancelProduct($params) {
		$helper = $this->getHelper();

		// E-Transactions direct must be enabled
		if (!$helper->isDirectEnabled()) {
			return false;
		}

		// Ignore any click to the generate discount button
		if (Tools::isSubmit('generateDiscount')) {
			return false;
		}

		// Payment module must by this one
		if ($params['order']->module != $this->name) {
			return false;
		}

		
		// We need the order, a valid one
		if (empty($params['order']) || !Validate::isLoadedObject($params['order'])) {
			return false;
		}
		$order = $params['order'];
		$orderId = $order->id;

		
		// We need a valid order detail
		$orderDetail = new OrderDetail((int) ($params['id_order_detail']));
		if (!Validate::isLoadedObject($orderDetail)) {
			return false;
		}
		$orderDetailId = (int) ($orderDetail->id_order_detail);
		// Currency informations
		$currency = new Currency(intval($order->id_currency));
		$amountScale = $helper->getCurrencyScale($order);
		$products = $order->getProducts();
		$product = $products[$orderDetailId];
		$cancelQuantity = (int) ($_POST['cancelQuantity'][$orderDetailId]);
		if ($product['product_quantity_discount'] == 0) {
			$amount = $product['product_price_wt'] * $cancelQuantity;
		}
		else {
			$amount = $product['product_quantity_discount'] * $cancelQuantity;
		}
		// Order can be captured?
		if ($helper->canCapture($orderId)) {
			// Update database
			$sql = 'UPDATE `%sETRANS_order` SET `amount` = `amount` - %d'
					. ' WHERE `id_order` = %d';
			$sql = sprintf($sql, _DB_PREFIX_, round($amount * $amountScale), $order->id);
			if (!Db::getInstance()->Execute($sql)) {
				return false;
			}
		}
		// Order can be refunded?
		else if ($order->hasBeenPaid() && $helper->canRefund($orderId)) {
			// Make refund
			$details = $helper->getOrderDetails($orderId);
			$result = $helper->makeRefundAmount($order, $details, $amount);
			switch ($result) {
				case 1:
					$this->context->controller->errors[] = Tools::displayError('Refund request unsuccessful. Please see log message!');
					return false;
				case 2:
					$this->context->controller->errors[] = Tools::displayError('Error when making refund request');
					return false;
				case 3:
					$this->context->controller->errors[] = Tools::displayError('The refund amount is too high.');
					return false;		
			}
		}
	}

	
	
	/**
	 * On payment selection page generation
	 */
	public function hookPayment($params) {
		global $smarty, $cart, $cookie;
		// Load methods
		$methods = $this->getHelper()->getActivePaymentMethods();
		$recurringCards = array();
		$cards = array();
		foreach ($methods as $method) {
			$params = array(
				'a' => 'r',
				'method' => $method['id_card'],
			);
			$params = http_build_query($params);
			$card = array(
				'id' => $method['id_card'],
				'payment' => $method['type_payment'],
				'card' => $method['type_card'],
				'label' => $method['label'],
				'url' => $this->getPath() . '?' . $params,
				'image' => $this->getMethodImageUrl($method['type_card']),
			);
			$cards[] = $card;
			if (in_array($method['type_card'], array('CB', 'VISA', 'EUROCARD_MASTERCARD'))) {
				$recurringCards[] = $card;
			}
			$cardTypes[] = $method['type_card'];
		}
		$smarty->assign('ETransactionsImagePath', $this->getImagePath());
		$smarty->assign('ETransactionsCards', $cards);
		$smarty->assign('ETransactionsProduction', $this->getConfig()->isProduction());
		// Define recurring information
		if ($this->getConfig()->isRecurringEnabled()) {
			if ($cart->getOrderTotal() >= $this->getConfig()->getRecurringMinimalAmount()) {
				$smarty->assign('ETransactionsRecurring', $recurringCards);
			}
		}

		$reason = Tools::getValue('ETransactions');
		if (($reason != 'cancel') && ($reason != 'error')) {
			$reason = null;
		}
		$smarty->assign('ETransactionsReason', $reason);

		if (version_compare(_PS_VERSION_, '1.6', '<')) {
			if (version_compare(_PS_VERSION_, '1.5', '<')) {
				Tools::addCss($this->_path . 'views/css/payment.css', 'all');
			}
			else {
				$this->context->controller->addCSS($this->_path . 'views/css/payment.css', 'all');
			}
			$html = $this->fetchTemplate('payment.tpl');
		}

		else {
			$this->context->controller->addCSS($this->_path . 'views/css/payment-rwd.css', 'all');
			$html = $this->display(__FILE__, 'payment-rwd.tpl');
		}

		return $html;
	}

	
	public function fetchTemplate($name) {
		if (_PS_VERSION_ < '1.4') {
			$this->context->smarty->currentTemplate = $name;
		}

		else if (_PS_VERSION_ < '1.5') {
			$views = 'views/templates/';
			if (@filemtime(dirname(__FILE__) . '/' . $name))
				return $this->display(__FILE__, $name);
			elseif (@filemtime(dirname(__FILE__) . '/' . $views . 'hook/' . $name))
				return $this->display(__FILE__, $views . 'hook/' . $name);
			elseif (@filemtime(dirname(__FILE__) . '/' . $views . 'front/' . $name))
				return $this->display(__FILE__, $views . 'front/' . $name);
			elseif (@filemtime(dirname(__FILE__) . '/' . $views . 'back/' . $name))
				return $this->display(__FILE__, $views . 'back/' . $name);
		}
		return $this->display(__FILE__, $name);
	}

	
	public function hookPaymentReturn($params) {
		global $smarty;
		// Payment method must be enabled
		if (!$this->active) {
			return;
		}
		// Must have an order
		if (empty($params['objOrder'])) {
			return;
		}
		// Must be order paid with ETransactions
		$details = $this->getHelper()->getOrderDetails($params['objOrder']->id);
		if (empty($details)) {
			return;
		}
		$lang = $this->context->language;
		if (!empty($lang) && !empty($lang->iso_code)) {
			$template = $this->getTemplatePath('payment_return.' . $lang->iso_code . '.tpl');
			if (!is_null($template)) {
				return $this->fetchTemplate('payment_return.' . $lang->iso_code . '.tpl');
			}
		}
		return $this->fetchTemplate('payment_return.tpl');
	}

	/**
	 * On order state change, do capture if needed
	 */
	public function hookUpdateOrderStatus($params) {
		$orderId = $params['id_order'];
		// Payment must be E-Transactions "standard"
		$details = $this->getHelper()->getOrderDetails($orderId);
		if (empty($details) || ($details['payment_by'] != 'ETransactionsSystem')) {
			return;
		}
		// Auto capture state must be defined
		$state = $this->getConfig()->getAutoCaptureState();
		if ($state <= -1) {
			return;
		}
		// New state must be the auto capture state
		$orderState = $params['newOrderStatus'];
		if ($state != $orderState->id) {
			return;
		}
		// Capture must be possible
		if (!$this->getHelper()->canCapture($orderId)) {
			return;
		}
		// Load order
		$order = new Order($orderId);
		if (!Validate::isLoadedObject($order)) {
			$w->alertError($this->l('Error when making capture request'));
			return;
		}
		$this->getHelper()->makeCaptureAll($order, $details, false);
	}

	
	
	public function install() {
		if (!parent::install()) {
			return false;
		}
		$installer = new ETransactionsInstaller();
		if (!$installer->install($this)) {
			return false;
		}
		return true;
	}


	/**
	 * On IPN call for a standard payment
	 */
	public function onStandardIPNSuccess(Cart $cart, array $params) {
		$this->logDebug(sprintf('Cart %d: Standard IPN', $cart->id));
		$amount = $params['amount'];
		$amountScale = pow(10, $this->getHelper()->getCurrencyDecimals($cart));
		$amount = floatval($amount) / $amountScale;
		$state = $this->_config->getSuccessState();
		if ($this->_config->getDebitType() == 'receive') {
			$type = 'authorization';
			$message = $this->l('Payment was authorized by E-transactions.');
		} else {
			$type = 'capture';
			$message = $this->l('Payment was authorized and captured by ETransactions.');
		}
		// For 1.4, no shop
		if (version_compare(_PS_VERSION_, '1.5', '<')) {
			$this->logDebug(sprintf('Cart %d: Validating order (PS1.4-)', $cart->id));
			$result = parent::validateOrder($cart->id, $state, $amount, 
				$this->displayName, $message, array('transaction_id' => $params['transaction']), 
				'', '', $cart->secure_key);
		}
		// For 1.5, shop
		else {
			$this->logDebug(sprintf('Cart %d: Validating order (PS1.5+)', $cart->id));
			$shop = new Shop($this->context->cart->id_shop);
			$result = parent::validateOrder($cart->id, $state, $amount, 
				$this->displayName, $message, array('transaction_id' => $params['transaction']), 
				'', '', $cart->secure_key, $shop);
		}
		if (!$result) {
			$this->logFatal(sprintf('Cart %d: Unable to validate order', $cart->id));
			throw new Exception('Unable to validate order');
		}
		$order = new Order($this->currentOrder);
		$this->getHelper()->addOrderPayment($order, $type, $params, 'ETransactionsSystem');
		$this->logDebug(sprintf('Cart %d: Order %d / %s', $cart->id, $order->id, $message));
	}

	/**
	 * On IPN call for a recurring payment
	 */
	public function onThreetimeIPNSuccess(Cart $cart, array $params) {
		$this->logDebug(sprintf('Cart %d: Threetime IPN', $cart->id));

		$orderId = Order::getOrderByCartId($cart->id);
		$orderAmount = $cart->getOrderTotal();

		$ETransactionsAmount = $params['amount'];
		$amountDecimals = $this->getHelper()->getCurrencyDecimals($cart);
		$amountFormat = '%.' . $amountDecimals . 'f';
		$amountScale = pow(10, $amountDecimals);
		$amount = floatval($ETransactionsAmount) / $amountScale;
		$currency = new Currency(intval($cart->id_currency));

		$amounts = $this->getHelper()->computeThreetimePayments($cart->getOrderTotal(), $amountScale);

		// First payment
		if (empty($orderId)) {
			$this->logDebug(sprintf('Cart %d: First payment', $cart->id));
			$state = Configuration::get('ETRANS_MIDDLE_STATE_NX');

			$message = $this->l('Payment was authorized by ETransactions.') . "\r\n";
			$message .= sprintf($this->l('First payment capture of %s %s done.'), sprintf($amountFormat, $amount), $currency->sign) . "\r\n";
			$message .= $this->l('Next payments will be:') . "\r\n";
			$nextAmount = sprintf($amountFormat, floatval($amounts['PBX_2MONT1']) / $amountScale);
			$message .= $amounts['PBX_DATE1'] . ' ' . $nextAmount . ' ' . $currency->sign . "\r\n";
			$nextAmount = sprintf($amountFormat, floatval($amounts['PBX_2MONT2']) / $amountScale);
			$message .= $amounts['PBX_DATE2'] . ' ' . $nextAmount . ' ' . $currency->sign . "\r\n";

			// For 1.4, no shop
			if (version_compare(_PS_VERSION_, '1.5', '<')) {
				$this->logDebug(sprintf('Cart %d: Validating order (PS1.4-)', $cart->id));
				$result = parent::validateOrder($cart->id, $state, $orderAmount, 
					$this->displayName, $message, $params, 
					'', '', $cart->secure_key);
			}

			// For 1.5, shop
			else {
				$this->logDebug(sprintf('Cart %d: Validating order (PS1.5+)', $cart->id));
				$shop = new Shop($this->context->cart->id_shop);
				$result = parent::validateOrder($cart->id, $state, $orderAmount, 
					$this->displayName, $message, $params, 
					'', '', $cart->secure_key, $shop);
			}

			if (!$result) {
				$this->logFatal(sprintf('Cart %d: Unable to validate order', $cart->id));
				throw new Exception('Unable to validate order');
			}

			$order = new Order($this->currentOrder);

			// Save payment information
			$this->getHelper()->addOrderPayment($order, 'capture', $params, 'ETransactionsSystemRecurring');
			$this->getHelper()->addOrderRecurringDetails($order, $ETransactionsAmount);
//			$this->getHelper()->addOrderNote($order, $message);
			$this->logDebug(sprintf('Cart %d: Order %d / %s', $cart->id, $order->id, $message));

			// Send a mail to the customer
			$title = $this->l('Recurring payment is approved');
			$customer = new Customer(intval($cart->id_customer));
			$customerName = $customer->firstname . ' ' . $customer->lastname;
			$varsTpl = array(
				'{lastname}' => $customer->lastname,
				'{firstname}' => $customer->firstname,
				'{id_order}' => $orderId,
				'{message}' => $message
			);

			Mail::Send(intval($order->id_lang), 'payment_recurring', $title, 
				$varsTpl, $customer->email, $customerName, null, null, null, 
				null, dirname(__FILE__) . '/mails/');
		}

		// Other payments
		else {
			$order = new Order($orderId);
			$details = $this->getHelper()->getOrderRecurringDetails($orderId);

			if (empty($details)) {
				// There must be details, this is an unrecovable error
				$message = $this->l('Invalid IPN call for recurring payment');
				$this->getHelper()->addOrderNote($order, $message);
				$this->logFatal(sprintf('Cart %d: Invalid IPN call for recurring payment', $cart->id));
				throw new Exception('Invalid IPN call for recurring payment');
			}

			switch ($details['number_term']) {
				// Second payment
				case 2:
					$this->logDebug(sprintf('Cart %d: Second payment', $cart->id));
					// Update recurring payment details
					$this->getHelper()->updateOrderRecurringDetails($order, 
						$details['amount_paid'] + $ETransactionsAmount, 1);
					// Info
					$message = sprintf($this->l('Second payment capture of %s %s done.'), sprintf($amountFormat, $amount), $currency->sign) . "\r\n";
					$message .= $this->l('Next payment will be:') . "\r\n";
					$nextAmount = sprintf($amountFormat, floatval($amounts['PBX_2MONT2']) / $amountScale);
					$message .= $amounts['PBX_DATE2'] . ' ' . $nextAmount . ' ' . $currency->sign . "\r\n";
					$this->getHelper()->addOrderNote($order, $message);
					$this->logDebug(sprintf('Cart %d: %s', $cart->id, $message));

					// Send a mail to the customer
					$title = $this->l('Recurring payment is approved');
					$customer = new Customer(intval($cart->id_customer));
					$customerName = $customer->firstname . ' ' . $customer->lastname;
					$varsTpl = array(
						'{lastname}' => $customer->lastname,
						'{firstname}' => $customer->firstname,
						'{id_order}' => $orderId,
						'{message}' => $message
					);
					Mail::Send(intval($order->id_lang), 'payment_recurring', $title, 
						$varsTpl, $customer->email, $customerName, null, null, null, 
						null, dirname(__FILE__) . '/mails/');
					break;

				// Third payment
				case 1:
					$this->logDebug(sprintf('Cart %d: Third payment', $cart->id));
					// Update recurring payment details
					$this->getHelper()->updateOrderRecurringDetails($order, 
						$details['amount_paid'] + $ETransactionsAmount, 0);

					// Info
					$message .= sprintf($this->l('Third payment capture of %s %s done.'), sprintf($amountFormat, $amount), $currency->sign) . "\r\n";
					$message .= $this->l('No more capture is pending.') . "\r\n";
					$this->getHelper()->addOrderNote($order, $message);
					$this->logDebug(sprintf('Cart %d: %s', $cart->id, $message));

					// Change status
					$changeHistory = new OrderHistory();
					$changeHistory->id_order = $order->id;
					$changeHistory->changeIdOrderState(Configuration::get('ETRANS_LAST_STATE_NX'), $changeHistory->id_order);
					$changeHistory->addWithemail();
					break;

				default:
					// There must be details, this is an unrecovable error
					$message = $this->l('Invalid IPN call for recurring payment');
					$this->getHelper()->addOrderNote($order, $message);
					$this->logFatal(sprintf('Cart %d: Invalid IPN call for recurring payment', $cart->id));
					throw new Exception('Invalid IPN call for recurring payment');
			}
		}
	}

	/**
	 * On module uninstall
	 */
	public function uninstall() {
		$installer = new ETransactionsInstaller();
		if (!$installer->uninstall($this)) {
			return false;
		}

		return parent::uninstall();
	}

	public function logDebug($message) {
		$this->_log($message, 'DEBUG');
	}

	public function logWarning($message) {
		$this->_log($message, 'WARN');
	}

	public function logError($message) {
		$this->_log($message, 'ERROR');
	}

	public function logFatal($message) {
		$this->_log($message, 'FATAL');
	}

	private function _log($message, $level) {
		$date = date('Y-m-d H:i:s');
		$message = sprintf('%s %010d %5s: %s' . PHP_EOL, $date, $this->_requestId, $level, $message);
		$file = sprintf('%s/logs/log_%s.log', dirname(__FILE__), date('Y-m-d'));
		$dir = dirname($file);
		if (!is_dir($dir)) {
			@mkdir($dir, 0777, true);
		}
		file_put_contents($file, $message, FILE_APPEND);
	}
}
