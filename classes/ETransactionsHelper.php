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

/**
 * E-Transactions generic code
 */
class ETransactionsHelper extends ETransactionsAbstract {
	private $_currencyDecimals = array(
		'008' => 2,
		'012' => 2,
		'032' => 2,
		'036' => 2,
		'044' => 2,
		'048' => 3,
		'050' => 2,
		'051' => 2,
		'052' => 2,
		'060' => 2,
		'064' => 2,
		'068' => 2,
		'072' => 2,
		'084' => 2,
		'090' => 2,
		'096' => 2,
		'104' => 2,
		'108' => 0,
		'116' => 2,
		'124' => 2,
		'132' => 2,
		'136' => 2,
		'144' => 2,
		'152' => 0,
		'156' => 2,
		'170' => 2,
		'174' => 0,
		'188' => 2,
		'191' => 2,
		'192' => 2,
		'203' => 2,
		'208' => 2,
		'214' => 2,
		'222' => 2,
		'230' => 2,
		'232' => 2,
		'238' => 2,
		'242' => 2,
		'262' => 0,
		'270' => 2,
		'292' => 2,
		'320' => 2,
		'324' => 0,
		'328' => 2,
		'332' => 2,
		'340' => 2,
		'344' => 2,
		'348' => 2,
		'352' => 0,
		'356' => 2,
		'360' => 2,
		'364' => 2,
		'368' => 3,
		'376' => 2,
		'388' => 2,
		'392' => 0,
		'398' => 2,
		'400' => 3,
		'404' => 2,
		'408' => 2,
		'410' => 0,
		'414' => 3,
		'417' => 2,
		'418' => 2,
		'422' => 2,
		'426' => 2,
		'428' => 2,
		'430' => 2,
		'434' => 3,
		'440' => 2,
		'446' => 2,
		'454' => 2,
		'458' => 2,
		'462' => 2,
		'478' => 2,
		'480' => 2,
		'484' => 2,
		'496' => 2,
		'498' => 2,
		'504' => 2,
		'504' => 2,
		'512' => 3,
		'516' => 2,
		'524' => 2,
		'532' => 2,
		'532' => 2,
		'533' => 2,
		'548' => 0,
		'554' => 2,
		'558' => 2,
		'566' => 2,
		'578' => 2,
		'586' => 2,
		'590' => 2,
		'598' => 2,
		'600' => 0,
		'604' => 2,
		'608' => 2,
		'634' => 2,
		'643' => 2,
		'646' => 0,
		'654' => 2,
		'678' => 2,
		'682' => 2,
		'690' => 2,
		'694' => 2,
		'702' => 2,
		'704' => 0,
		'706' => 2,
		'710' => 2,
		'728' => 2,
		'748' => 2,
		'752' => 2,
		'756' => 2,
		'760' => 2,
		'764' => 2,
		'776' => 2,
		'780' => 2,
		'784' => 2,
		'788' => 3,
		'800' => 2,
		'807' => 2,
		'818' => 2,
		'826' => 2,
		'834' => 2,
		'840' => 2,
		'858' => 2,
		'860' => 2,
		'882' => 2,
		'886' => 2,
		'901' => 2,
		'931' => 2,
		'932' => 2,
		'934' => 2,
		'936' => 2,
		'937' => 2,
		'938' => 2,
		'940' => 0,
		'941' => 2,
		'943' => 2,
		'944' => 2,
		'946' => 2,
		'947' => 2,
		'948' => 2,
		'949' => 2,
		'950' => 0,
		'951' => 2,
		'952' => 0,
		'953' => 0,
		'967' => 2,
		'968' => 2,
		'969' => 2,
		'970' => 2,
		'971' => 2,
		'972' => 2,
		'973' => 2,
		'974' => 0,
		'975' => 2,
		'976' => 2,
		'977' => 2,
		'978' => 2,
		'979' => 2,
		'980' => 2,
		'981' => 2,
		'984' => 2,
		'985' => 2,
		'986' => 2,
		'990' => 0,
		'997' => 2,
		'998' => 2,
	);

	private $_errorCode = array(
		'00000' => 'Successful operation',
		'00001' => 'Payment system not available',
		'00003' => 'E-Transactions error',
		'00004' => 'Card number or invalid cryptogram',
		'00006' => 'Access denied or invalid identification',
		'00008' => 'Invalid validity date',
		'00009' => 'Subscription creation failed',
		'00010' => 'Unknown currency',
		'00011' => 'Invalid amount',
		'00015' => 'Payment already done',
		'00016' => 'Existing subscriber',
		'00021' => 'Unauthorized card',
		'00029' => 'Invalid card',
		'00030' => 'Timeout',
		'00033' => 'Unauthorized IP country',
		'00040' => 'No 3D Secure',
	);

	private $_resultMapping = array(
		'M' => 'amount',
		'R' => 'reference',
		'T' => 'call',
		'A' => 'authorization',
		'B' => 'subscription',
		'C' => 'cardType',
		'D' => 'validity',
		'E' => 'error',
		'F' => '3ds',
		'G' => '3dsWarranty',
		'H' => 'imprint',
		'I' => 'ip',
		'J' => 'lastNumbers',
		'K' => 'sign',
		'N' => 'firstNumbers',
		'O' => '3dsInlistment',
		'o' => 'celetemType',
		'P' => 'paymentType',
		'Q' => 'time',
		'S' => 'transaction',
		'U' => 'subscriptionData',
		'W' => 'date',
		'Y' => 'country',
		'Z' => 'paymentIndex',
	);

	private function _curl($url, $fields) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
		
		curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/cacert.pem');
		
		$result = curl_exec($ch);

		// TODO: Bad error management
		if (curl_errno($ch)) {
			die(curl_error($ch));
		}

		curl_close($ch);

		$result = preg_split('/(\r\r|\n\n|\r\n\r\n)/', $result, 2);
		$data = count($result) == 2 ? $result[1] : null;
		$headers = explode("\r\n", $result[0]);
		if (preg_match('#^HTTP/[0-9.]+ ([0-9]+)(.*)$#', array_shift($headers), $matches)) {
			$code = intval($matches[1]);
			$status = trim($matches[2]);
		}
		else {
			$code = 999;
			$status = 'Error';
		}

		return array(
			'code' => $code,
			'status' => $status,
			'headers' => $headers,
			'data' => $data,
		);
	}

	private function _callDirect(Order $order, $type, $trxId, $callId, $amount, $cardType) {
		$customer = new Customer($order->id_customer);
		$currency = $this->getCurrency($order);

		$amountScale = $this->getCurrencyScale($order);
		$amount = floatval(str_replace(',', '.', $amount));
		$amount = round($amount * $amountScale);

		$now = new DateTime('now', new DateTimeZone('Europe/Paris'));
		$fields = array(
			'ACTIVITE' => '024',
			'VERSION' => '00104',
			'CLE' => $this->getConfig()->getPassword(),
			'DATEQ' => $now->format('dmYHis'),
			'DEVISE' => sprintf('%03d', $currency),
			'IDENTIFIANT' => $this->getConfig()->getIdentifier(),
			'MONTANT' => sprintf('%010d', $amount),
			'NUMAPPEL' => sprintf('%010d', $callId),
			'NUMQUESTION' => sprintf('%010d', time()),
			'NUMTRANS' => sprintf('%010d', $trxId),
			'RANG' => sprintf('%02d', $this->getConfig()->getRank()),
			'REFERENCE' => $order->id_cart.' - '.$this->getBillingName($customer),
			'SITE' => sprintf('%07d', $this->getConfig()->getSite()),
			'TYPE' => $type,
		);

		// Add ACQUEREUR for some cards
		switch ($cardType) {
			case 'PAYPAL':
				$fields['ACQUEREUR'] = 'PAYPAL';
				break;
		}

		$urls = $this->getConfig()->getDirectUrls();
		$url = $this->checkUrls($urls);

		$message = 'Order %d: call E-Transactions direct - DEVISE=%s MONTANT=%s NUMAPPEL=%s NUMTRANS=%s TYPE=%s';
		$message = sprintf($message, $order->id, $fields['DEVISE'], $fields['MONTANT'], $fields['NUMAPPEL'], $fields['NUMTRANS'], $fields['TYPE']);
		$this->logDebug($message);
		$result = $this->_curl($url, $fields);

		$this->logDebug(sprintf('Order %d: E-Transactions Direct HTTP return code %d', $order->id, $result['code']));
		$this->logDebug(sprintf('Order %d: E-Transactions Direct data %s', $order->id, $result['data']));
		$data = array();
		parse_str($result['data'], $data);

		return $data;
	}

	/**
	 * Make a capture using E-Transactions Direct
	 */
	private function _makeCapture(Order $order, $trxId, $callId, $amount, $cardType = null) {
		return $this->_callDirect($order, '00002', $trxId, $callId, $amount, $cardType);
	}

	/**
	 * Make a new authorization using E-Transactions Direct
	 */
	// TODO ??? Same as _makeCapture ???
	private function _makeReauthorization(Order $order, $trxId, $callId, $amount, $cardType = null) {
		return $this->_callDirect($order, '00002', $trxId, $callId, $amount, $cardType);
	}

	/**
	 * Make a refund using E-Transactions Direct
	 */
	private function _makeRefund(Order $order, $trxId, $callId, $amount, $cardType = null) {
		return $this->_callDirect($order, '00014', $trxId, $callId, $amount, $cardType);
	}

	/**
	 * Add a private message to the order $order
	 */
	public function addOrderNote(Order $order, $message) {
		$message = strip_tags($message, '<br>');
		if (!Validate::isCleanHtml($message)) {
			$message = $this->l('Payment message is not valid, please check your module.');
		}

		$msg = new Message();
		$msg->id_order = $order->id;
		$msg->message = $message;
		$msg->private = 1;
		$msg->add();
	}

	public function addOrderPayment(Order $order, $type, $params, $method) {
		if (method_exists('Cache', 'clean')) {
			Cache::clean('Cart::orderExists_'.$order->id_cart);
		}

		$cart = new Cart($order->id_cart);

		if (Validate::isLoadedObject($cart)) {
			$currency = new Currency(intval($cart->id_currency));

			$data = array(
				'id_order' => $order->id,
				'id_transaction' => $params['transaction'],
				'num_appel' => $params['call'],
				'ref_abo' => null,
				'payment_status' => $type,
				'amount' => $params['amount'],
				'currency' => $currency->iso_code_num,
				'payment_by' => $method,
				'carte' => $params['cardType'],
				'pays' => isset($params['country']) ? $params['country'] : '',
				'ip' => $params['ip'],
				'secure' => $params['3dsWarranty'],
				'date' => $params['date']
			);
			$db = new ETransactionsDb();
			$db->insert('ETRANS_order', $data);
		}
	}

	public function addOrderRecurringDetails(Order $order, $amountPaid) {
		$data = array(
			'id_order' => $order->id,
			'status' => 'in progress',
			'number_term' => 2,
			'amount_paid' => $amountPaid,
		);
		$db = new ETransactionsDb();
		$db->insert('ETRANS_recurring', $data);
	}

	/**
	 * @param Order $order PrestaShop Order object
	 * @param array $card Card information
	 * @param string $type one of standard ou threetime
	 * @param array $additionalParams
	 * @return array
	 */
	public function buildSystemParams(Cart $cart, array $card, $type, array $additionalParams = array()) {
		$customer = new Customer($cart->id_customer);
		$reference = $cart->id.' - '.$this->getBillingName($customer);

		// Parameters
		$base = Tools::getHttpHost(true, false).__PS_BASE_URI__;
		$base .= 'modules/ETransactionsEpayment/index.php?t=';
		if ($type == 'threetime' && $this->getConfig()->getSubscription()) {
			$base .= '3';
		}
		else {
			$base .= 's';
		}
		$values = array(
			'PBX_ANNULE' => $base.'&a=c',
			'PBX_EFFECTUE' => $base.'&a=s',
			'PBX_REFUSE' => $base.'&a=f',
			'PBX_REPONDRE_A' => $base.'&a=i',
		);

		// Merchant information
		$values['PBX_SITE'] = $this->getConfig()->getSite();
		$values['PBX_RANG'] = substr(sprintf('%02d', $this->getConfig()->getRank()), -2);
		$values['PBX_IDENTIFIANT'] = $this->getConfig()->getIdentifier();

		// Card information
		$values['PBX_TYPEPAIEMENT'] = $card['type_payment'];
		$values['PBX_TYPECARTE'] = $card['type_card'];

		// Order information
		$values['PBX_PORTEUR'] = $this->getBillingEmail($customer);
		$values['PBX_DEVISE'] = $this->getCurrency($cart);
		$values['PBX_CMD'] = $reference;

		$orderAmount = floatval($cart->getOrderTotal());
		$amountScale = $this->_currencyDecimals[$values['PBX_DEVISE']];
		$amountScale = pow(10, $amountScale);
		if ($type == 'threetime' && $this->getConfig()->getSubscription()) {
			$amounts = $this->computeThreetimePayments($orderAmount, $amountScale);
			foreach ($amounts as $k => $v) {
				$values[$k] = $v;
			}
		}
		else {
			$values['PBX_TOTAL'] = sprintf('%03d', round($orderAmount * $amountScale));
			switch ($this->getConfig()->getDebitType()) {
				case 'delayed':
					if ($card['debit_differe']) {
						$delay = $this->getConfig()->getDelay();
						if ($delay < 1) {
							$delay = 1;
						}
						else if ($delay > 7) {
							$delay = 7;
						}
						$values['PBX_DIFF'] = sprintf('%02d', $delay);
					}
					break;
				case 'receive':
					if ($card['debit_expedition']) {
						$values['PBX_AUTOSEULE'] = 'O';
					}
					break;
			}
		}

		// 3-D Secure
		$enable3ds = true;
		$tds = (isset($card['3ds']) && $this->getConfig()->getSubscription()) ? $card['3ds'] : null;		
		switch ($tds) {
			case 1:
				if ($this->getConfig()->get3DSEnabled()) {			 
					$tdsAmount = ($this->getConfig()->getSubscription() ? $this->getConfig()->get3DSAmount() : 0);
					$enable3ds = empty($tdsAmount) || ($orderAmount >= $tdsAmount);
				}else{
					$enable3ds = false;
				}
				break;

			case 2:
				$enable3ds = true;
				break;

			case 0:
				$enable3ds = false;
				break;
			default:
		}
		// Enable is the default behaviour
		if ($this->getConfig()->getSubscription() && !$enable3ds) {
			$values['PBX_3DS'] = 'N';
		}

		// E-Transactions => PrestaShop
		$values['PBX_RETOUR'] = 'M:M;R:R;T:T;A:A;B:B;C:C;D:D;E:E;F:F;G:G;H:H;I:I;J:J;N:N;O:O;P:P;Q:Q;S:S;W:W;Y:Y;K:K';
		$values['PBX_RUF1'] = 'POST';

		// Choose correct language
		$values['PBX_LANGUE'] = $this->getLanguage($cart);

		// Choose page format depending on browser/devise
		if ($this->isMobile()) {
			$values['PBX_SOURCE'] = 'XHTML';
		}

		// Misc.
		$values['PBX_TIME'] = date('c');
		$values['PBX_HASH'] = strtoupper($this->getConfig()->getHmacAlgo());

		// Card specific workaround
		if (($card['type_payment'] == 'LEETCHI') && ($card['type_card'] == 'LEETCHI')) {
			$values['PBX_EFFECTUE'] .= '&R='.urlencode($reference);
			$values['PBX_REFUSE'] .= '&R='.urlencode($reference);
		}
		else if (($card['type_payment'] == 'PREPAYEE') && ($card['type_card'] == 'IDEAL')) {
			$s =  '&C=IDEAL&P=PREPAYEE';
			$values['PBX_ANNULE'] .= $s;
			$values['PBX_EFFECTUE'] .= $s;
			$values['PBX_REFUSE'] .= $s;
			$values['PBX_REPONDRE_A'] .= $s;
		}

		// Adding additionnal informations
		$values = array_merge($values, $additionalParams);

		// Sort parameters for simpler debug
		ksort($values);

		// Sign values
		$sign = $this->signValues($values);

		// Hash HMAC
		$values['PBX_HMAC'] = $sign;

		return $values;
	}

	/**
	 * Can order with id $orderId can be captured? It checks if E-Transactions Direct
	 * is enabled, if the order is handled by E-Transactions and if the payment method
	 * support the "paid on shipping" feature.
	 * @param int $orderId
	 * @return boolean
	 */
	public function canCapture($orderId) {
		if (!$this->isDirectEnabled()) {
			return false;
		}

		$details = $this->getOrderDetails($orderId);
		if (empty($details)) {
			return false;
		}

		if ($details['payment_status'] != 'authorization') {
			return false;
		}

		$method = $this->getPaymentMethod($details['carte']);
		if (empty($method)) {
			return false;
		}

		return $method['debit_expedition'] == 1;
	}

	/**
	 * Can order with id $orderId can be refunded? It checks if E-Transactions Direct
	 * is enabled, if the order is handled by E-Transactions and if the payment method
	 * support the "refund" feature.
	 * @param int $orderId
	 * @return boolean
	 */
	public function canRefund($orderId) {
		if (!$this->isDirectEnabled()) {
			return false;
		}

		$details = $this->getOrderDetails($orderId);
		if (empty($details)) {
			return false;
		}

		// TODO: check this condition
		if (($details['payment_status'] != 'capture') && ($details['payment_status'] != 'canceled')) {
			return false;
		}

		$method = $this->getPaymentMethod($details['carte']);
		if (empty($method)) {
			return false;
		}

		return $method['remboursement'] == 1;
	}

	public function checkUrls(array $urls) {
		// look for valid peer
		$client = new ETransactionsCurl();
		$client->setTimeout(5);
		$client->setUserAgent('PrestaShop E-Transactions module');
		$client->setFollowRedirect(false);
		$error = null;
		$this->logDebug(sprintf('URL to check %s', count($urls)));
		foreach ($urls as $url) {
			$testUrl = preg_replace('#^([a-zA-Z0-9]+://[^/]+)(/.*)?$#', '\1/load.html', $url);
			$this->logDebug(sprintf('Checking E-Transactions URL %s', $testUrl));

			try {
				$response = $client->get($testUrl);
				if (!is_array($response)) {
					$this->logDebug(sprintf('  Invalid response type %s', gettype($response)));
				}
				else if ($response['code'] != 200) {
					$this->logDebug(sprintf('  Invalid response code %s', $response['code']));
				}
				else {
					$this->logDebug(sprintf('  Valid url found: %s', $url));
					return $url;
				}
			}
			catch (Exception $e) {
				$this->logDebug(sprintf('  Exception %s: %s', get_class($e), $e->getMessage()));
				$error = $e;
			}
		}

		// Here, there's a problem
		$this->logDebug('No valid URL found!');
		throw new Exception($this->l('E-Transactions not available. Please try again later.'));
	}

	public function computeThreetimePayments($orderAmount, $amountScale) {
		$values = array();
		// Compute each payment amount
		$step = round($orderAmount * $amountScale / 3);
		$firstStep = ($orderAmount * $amountScale) - 2 * $step;
		$values['PBX_TOTAL'] = sprintf('%03d', $firstStep);
		$values['PBX_2MONT1'] = sprintf('%03d', $step);
		$values['PBX_2MONT2'] = sprintf('%03d', $step);

		// Payment dates
		$now = new DateTime();
		$now->modify('1 month');
		$values['PBX_DATE1'] = $now->format('d/m/Y');
		$now->modify('1 month');
		$values['PBX_DATE2'] = $now->format('d/m/Y');


		// Force validity date of card
		$values['PBX_DATEVALMAX'] = $now->format('ym');
		return $values;
	}

	public function convertParams(array $params) {
		$result = array();
		foreach ($this->_resultMapping as $param => $key) {
			if (isset($params[$param]))
				$result[$key] = utf8_encode($params[$param]);
		}

		if($result[$this->_resultMapping["C"]] == "MasterCard"){
			$result[$this->_resultMapping["C"]] = "EUROCARD_MASTERCARD";
		}
		
		return $result;
	}

	public function deleteRecurringPayment(Order $order, array $details) {
		// Find URL
		$urls = $this->getConfig()->getResAboUrls();
		$url = $this->checkUrls($urls);

		// Call parameters
		$customer = new Customer($order->id_customer);
		$fields = array(
			'IDENTIFIANT' => $this->getConfig()->getIdentifier(),
			'MACH' => sprintf('%03d', $this->getConfig()->getRank()),
			'REFERENCE' => $order->id_cart.' - '.$this->getBillingName($customer),
			'SITE' => $this->getConfig()->getSite(),
			'TYPE' => '001',
			'VERSION' => '001',
		);

		// Call
		$result = $this->_curl($url, $fields);

		// Check answer
		if (($result['code'] != 200) || !preg_match('#^(.*&)?ACQ=OK(&.*)$#', $result['data'])) {
			$message = $this->l('Unable to cancel recurring payment.')."\r\n";
			$message .= $this->l('For more information logon to the E-Transactions Back-Office.');
			$this->addOrderNote($order, $message);
			return false;
		}

		// Update details
		$sql = 'UPDATE `%sETRANS_order` SET `payment_status` = "canceled"'
			.' WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, $order->id);
		Db::getInstance()->Execute($sql);

		// Add a note
		$message = $this->l('Recurring payment canceled.');
		$this->addOrderNote($order, $message);

		// Change state if needed
		if ($details['payment_status'] == 'capture') {
			$history = new OrderHistory();
			$history->id_order = (int)($order->id);
			$history->changeIdOrderState(Configuration::get('ETRANS_STATE_MIN_CAPTURE'), $history->id_order);
			$history->addWithemail();
		}
		if ($details['payment_status'] == 'refundRecurring') {
			$history = new OrderHistory();
			$history->id_order = (int)($order->id);
			$history->changeIdOrderState(_PS_OS_REFUND_, $history->id_order);
			$history->addWithemail();
		}

		return true;
	}

	public function getAllPaymentMethods() {
		$sql = 'SELECT * FROM `%sETRANS_card`';
		$sql = sprintf($sql, _DB_PREFIX_);
		return Db::getInstance()->ExecuteS($sql);
	}

	public function getActivePaymentMethods() {
		$allMethods = $this->getAllPaymentMethods();
		$methods = array();
		foreach ($allMethods as $method) {
			$id = $method['id_card'];
			$active = Configuration::get('ETRANS_CARD_ENABLED_'.$id);
			$label = Configuration::get('ETRANS_CARD_LABEL_'.$id);
			if ($active !== false) {
				$method['active'] = $active;
			}
			if ($label !== false) {
				$method['label'] = $label;
			}
			if ($method['active'] == 1) {
				$methods[] = $method;
			}
		}
		return $methods;
	}

	public function getAmountPartialRefund($orderId) {
		$orderId = intval($orderId);
		if (empty($orderId)) {
			return false;
		}

		$sql = 'SELECT SUM(`amount` + `shipping_cost_amount`) FROM `%sorder_slip` WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, $orderId);
		return Db::getInstance()->getValue($sql);
	}

	public function getBillingEmail(Customer $customer) {
		return $customer->email;
	}

	public function getBillingName(Customer $customer) {
		$name = $customer->firstname.' '.$customer->lastname;
		$name = Tools::replaceAccentedChars($name);
		$name = trim(preg_replace('/[^-. a-zA-Z0-9]/', '', $name));
		return $name;
	}

	public function getClientIp() {
		return Tools::getRemoteAddr();
	}

	public function getCurrency($cartOrOrder) {
		$currency = new Currency($cartOrOrder->id_currency);
		return $currency->iso_code_num;
	}

	public function getCurrencyDecimals($cartOrOrder) {
		return $this->_currencyDecimals[$this->getCurrency($cartOrOrder)];
	}

	public function getCurrencyScale($cartOrOrder) {
		return pow(10, $this->getCurrencyDecimals($cartOrOrder));
	}

	public function getLanguage(Cart $cart) {
		$lang = Language::getIsoById($cart->id_lang);
		$languages = $this->getLanguages();
		if (!array_key_exists($lang, $languages)) {
			$lang = 'default';
		}
		return $languages[$lang];
	}

	public function getLanguages() {
		return array(
			'fr' => 'FRA',
			'es' => 'ESP',
			'it' => 'ITA',
			'de' => 'DEU',
			'nl' => 'NLD',
			'sv' => 'SWE',
			'pt' => 'PRT',
			'default' => 'FRA',
		);
	}

	public function getOrderDetails($orderId) {
		$orderId = intval($orderId);
		if (empty($orderId)) {
			return false;
		}

		$sql = 'SELECT * FROM `%sETRANS_order` WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, $orderId);
		return Db::getInstance()->getRow($sql);
	}

	public function getOrderPaymentBy($orderId) {
		$orderId = intval($orderId);
		if (empty($orderId)) {
			return false;
		}

		$sql = 'SELECT `payment_by` FROM `%sETRANS_order` WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, $orderId);
		return Db::getInstance()->getValue($sql);
	}

	public function getOrderRecurringDetails($orderId) {
		$orderId = intval($orderId);
		if (empty($orderId)) {
			return false;
		}

		$sql = 'SELECT * FROM `%sETRANS_recurring` WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, $orderId);
		return Db::getInstance()->getRow($sql);
	}

	public function getOrderTransactionId($orderId) {
		$orderId = intval($orderId);
		if (empty($orderId)) {
			return false;
		}

		$sql = 'SELECT `id_transaction` FROM `%sETRANS_order` WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, $orderId);
		return Db::getInstance()->getValue($sql);
	}

	public function getParams($logParams = false, $checkSign = true) {
		// Retrieves data
		$data = file_get_contents('php://input');
		if (empty($data)) {
			$data = $_SERVER['QUERY_STRING'];
		}
		if (empty($data)) {
			$message = 'An unexpected error in E-Transactions call has occured: no parameters.';
			throw new Exception($this->l($message));
		}

		// Log params if needed
		if ($logParams) {
			$this->logDebug(sprintf('Call params: %s', $data));
		}

		// Check signature if needed
		if ($checkSign) {
			// Extract signature
			$matches = array();
			if (!preg_match('#^(.*)&K=(.*)$#', $data, $matches)) {
				$message = 'An unexpected error in E-Transactions call has occured: missing signature.';
				throw new Exception($this->l($message));
			}

			// Check sign
			$signature = base64_decode(urldecode($matches[2]));
			$pubkey = file_get_contents(dirname(__FILE__).'/pubkey.pem');
			$res = (boolean) openssl_verify($matches[1], $signature, $pubkey);

			if (!$res) {
				if (preg_match('#^t=[s3]&a=[cfrsij]&(.*)&K=(.*)$#', $data, $matches)) {
					$signature = base64_decode(urldecode($matches[2]));
					$res = (boolean) openssl_verify($matches[1], $signature, $pubkey);
				}

				if (preg_match('#^t=[s3]&a=[cfrsij]&C=IDEAL&P=PREPAYEE&(.*)&K=(.*)$#', $data, $matches)) {
					$signature = base64_decode(urldecode($matches[2]));
					$res = (boolean) openssl_verify($matches[1], $signature, $pubkey);
				}

				if (!$res) {
					$message = 'An unexpected error in E-Transactions call has occured: invalid signature.';
					throw new Exception($this->l($message));
				}
			}
		}

		$rawParams = array();
		parse_str($data, $rawParams);

		// Decrypt params
		$params = $this->convertParams($rawParams);
		if (empty($params)) {
			$message = 'An unexpected error in E-Transactions call has occured: no parameters.';
			throw new Exception($this->l($message));
		}

		return $params;
	}

	public function getPaymentMethod($cardType) {
		$db = Db::getInstance();
		$sql = 'SELECT * FROM `%sETRANS_card` WHERE `type_card` = "%s"';
		if (version_compare(_PS_VERSION_, '1.5', '<')) {
			$sql = sprintf($sql, _DB_PREFIX_, Tools::htmlentitiesUTF8($cardType));
		} else {
			$sql = sprintf($sql, _DB_PREFIX_, $db->escape($cardType));
		}
		$result = $db->ExecuteS($sql);
		if (!empty($result)) {
			$result = $result[0];
		}
		return $result;
	}

	public function getPaymentMethodById($id) {
		$db = Db::getInstance();
		$sql = 'SELECT * FROM `%sETRANS_card` WHERE `id_card` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, intval($id));
		$result = $db->ExecuteS($sql);
		if (!empty($result)) {
			$result = $result[0];
		}
		return $result;
	}

	public function isDirectEnabled() {
		return Configuration::get('ETRANS_WEB_CASH_DIRECT') == 1;
	}

	public function isMobile() {
		// From http://detectmobilebrowsers.com/, regexp of 09/09/2013
		global $_SERVER;
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $userAgent)) {
			return true;
		}
		if (preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4))) {
			return true;
		}
		return false;
	}

	public function logDebug($message) {
		$this->getModule()->logDebug($message);
	}

	public function logWarning($message) {
		$this->getModule()->logDebug($message);
	}

	public function logError($message) {
		$this->getModule()->logDebug($message);
	}

	public function logFatal($message) {
		$this->getModule()->logDebug($message);
	}

	/**
	 * @return 2 if call error, 1 if ETransactions error, 0 if success
	 */
	public function makeCaptureAll(Order $order, array $details, $changestate = true) {
		$this->logDebug(sprintf('Order %d: capture all', $order->id));

		// Transaction informations
		$trxId = $details['id_transaction'];
		$callId = $details['num_appel'];

		// Refund partial (?)
		$partialRefund = 0;
		if (version_compare(_PS_VERSION_, '1.5', '>=')) {
			$partialRefund = $this->getAmountPartialRefund($order->id);
			$this->logDebug(sprintf('Order %d: with partial refund of %f', $order->id, $partialRefund));
		}

		// Amount to capture
		$cart = new Cart($order->id_cart);
		$amount = $cart->getOrderTotal();
		$amountScale = $this->getCurrencyScale($order);

		// Capture there are more than 7 days?
		if (strtotime($order->date_add) <= mktime(23, 59, 59, date('m'), date('d') - 7, date('Y'))) {
			$this->logDebug(sprintf('Order %d: reauthorization required', $order->id));
			$response = $this->_makeReauthorization($order, $trxId, $callId, $amount - $partialRefund, $details['carte']);
		}
		else {
			$response = $this->_makeCapture($order, $trxId, $callId, $amount - $partialRefund, $details['carte']);
		}

		$currency = new Currency(intval($order->id_currency));

		// Call error
		if (empty($response)) {
			$this->logError(sprintf('Order %d: E-Transactions Direct call error', $order->id));
			return 2;
		}

		// E-Transactions error
		else if ($response['CODEREPONSE'] != '00000') {
			$this->logError(sprintf('Order %d: E-Transactions Direct returned an error of %s', $order->id, $response['CODEREPONSE']));
			$message = $this->l('Capture operation:').chr(10).chr(13);
			$message .= $this->l('Return code: error').chr(10).chr(13);
			$message .= $this->l('Capture amount:').' '.($amount - $partialRefund).' '.$currency->sign.chr(10).chr(13);
			$message .= $this->l('For more information logon to the back office ETransactions');
			$this->addOrderNote($order, $message);

			return 1;
		}

		// Success
		$this->logError(sprintf('Order %d: Success of E-Transactions Direct call', $order->id));
		$message = $this->l('Capture operation:').chr(10).chr(13);
		$message .= $this->l('Return code: ok').chr(10).chr(13);
		$message .= $this->l('Ref. ETransactions:').$response['NUMTRANS'].chr(10).chr(13);
		$message .= $this->l('Capture amount:').' '.($amount - $partialRefund).' '.$currency->sign.chr(10).chr(13);
		$this->addOrderNote($order, $message);

		// Update database
		$sql = 'UPDATE `%sETRANS_order` SET `payment_status` = "capture",'
			.' `id_transaction` = %d, `amount` = %d'
			.' WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, intval($response['NUMTRANS']),
			intval($amount * $amountScale), $order->id);
		if (!Db::getInstance()->Execute($sql)) {
			die(Tools::displayError('Error when updating E-Transactions database'));
		}

		// Change order state if needed
		if ($partialRefund == 0) {
			$stateId = $this->getConfig()->getAutoCaptureState();
		}
		else {
			$stateId = Configuration::get('ETRANS_STATE_MIN_CAPTURE');
		}

		if ($changestate) {
			if ($stateId != $order->current_state) {
				$history = new OrderHistory();
				$history->id_order = (int)($order->id);
				$history->changeIdOrderState($stateId, $history->id_order);
				$history->addWithemail();
			}
		}

		return 0;
	}

	/**
	 * @return 3 if amount too high, 2 if call error, 1 if ETransactions error, 0 if success
	 */
	public function makeCaptureAmount(Order $order, array $details, $amount) {
		$this->logDebug(sprintf('Order %d: capture amount %s', $order->id, $amount));
		// Refund partial (?)
		$partialRefund = 0;
		if (version_compare(_PS_VERSION_, '1.5', '>=')) {
			$this->logDebug(sprintf('Order %d: with partial refund of %f', $order->id, $partialRefund));
			$partialRefund = $this->getAmountPartialRefund($order->id);
		}
		$currency = new Currency(intval($order->id_currency));

		// Amount to refund
		$amountScale = $this->getCurrencyScale($order);
		$amount = floatval(str_replace(',', '.', $amount));

		// Check if the amount is too high
		$check = $details['amount'] - $details['refund_amount'];
		$check -= round($amount * $amountScale);
		$check -= round($partialRefund * $amountScale);
		if ($check < 0) {
			$this->logDebug(sprintf('Order %d: amount too high', $order->id));
			return 3;
		}

		// Transaction informations
		$trxId = $details['id_transaction'];
		$callId = $details['num_appel'];

		$response = $this->_makeCapture($order, $trxId, $callId, $amount, $details['carte']);

		// Call error
		if (empty($response)) {
			$this->logError(sprintf('Order %d: E-Transactions Direct call error', $order->id));
			return 2;
		}

		// E-Transactions error
		else if ($response['CODEREPONSE'] != '00000') {
			$this->logError(sprintf('Order %d: E-Transactions Direct returned an error of %s', $order->id, $response['CODEREPONSE']));
			$message = $this->l('E-Transactions capture amount:').chr(10).chr(13);
			$message .= $this->l('Return code: error').chr(10).chr(13);
			$message .= $this->l('Capture amount:').' '.$amount.' '.$currency->sign.chr(10).chr(13);
			$message .= $this->l('For more information logon to the back office E-Transactions');
			$this->addOrderNote($order, $message);

			return 1;
		}

		$this->logError(sprintf('Order %d: Success of E-Transactions Direct call', $order->id));
		$message = $this->l('E-Transactions capture amount:').chr(10).chr(13);
		$message .= $this->l('Return code: ok').chr(10).chr(13);
		$message .= $this->l('Ref. ETransactions:').$response['NUMTRANS'].chr(10).chr(13);
		$message .= $this->l('Capture amount:').' '.$amount.' '.$currency->sign.chr(10).chr(13);
		$this->addOrderNote($order, $message);

		// Update database
		$sql = 'UPDATE `%sETRANS_order` SET `payment_status` = "capture",'
			.' `id_transaction` = %d, `amount` = %d'
			.' WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, intval($response['NUMTRANS']),
			round($amount * $amountScale), $order->id);
		if (!Db::getInstance()->Execute($sql)) {
			die(Tools::displayError('Error when updating E-Transactions database'));
		}

		// Change order state
		if ($details['amount'] - round($amount * $amountScale) == 0) {
			$stateId = $this->getConfig()->getAutoCaptureState();
		}
		else {
			$stateId = Configuration::get('ETRANS_STATE_MIN_CAPTURE');
		}
		$history = new OrderHistory();
		$history->id_order = (int)($order->id);
		$history->changeIdOrderState($stateId, $history->id_order);
		$history->addWithemail();

		return 0;
	}

	/**
	 * @return 2 if call error, 1 if ETransactions error, 0 if success
	 */
	public function makeRefundAll(Order $order, array $details) {
		// Transaction informations
		$trxId = $details['id_transaction'];
		$callId = $details['num_appel'];

		// Refund partial (?)
		$partialRefund = 0;
		if (version_compare(_PS_VERSION_, '1.5', '>=')) {
			$partialRefund = $this->getAmountPartialRefund($order->id);
		}

		// Amount to refund
		$amountScale = $this->getCurrencyScale($order);
		$amount = floatval($details['amount'] - $details['refund_amount']) / $amountScale;
		$amount -= $partialRefund;

		$response = $this->_makeRefund($order, $trxId, $callId, $amount, $details['carte']);

		// If it's the full paid amount
		if ($order->total_paid_real == $amount) {
			$message = $this->l('E-Transactions Refund total:').chr(10).chr(13);
		}
		else {
			$message = $this->l('E-Transactions Refund partial:').chr(10).chr(13);
		}

		$currency = new Currency(intval($order->id_currency));

		// Call error
		if (empty($response)) {
			return 2;
		}

		// E-Transactions error
		else if ($response['CODEREPONSE'] != '00000') {
			$message .= $this->l('Return code: error').chr(10).chr(13);
			$message .= $this->l('Refund amount:').' '.$amount.' '.$currency->sign.chr(10).chr(13);
			$message .= $this->l('For more information logon to the back office ETransactions');
			$this->addOrderNote($order, $message);

			return 1;
		}

		// Success
		$message .= $this->l('Return code: ok').chr(10).chr(13);
		$message .= $this->l('Ref. ETransactions:').$response['NUMTRANS'].chr(10).chr(13);
		$message .= $this->l('Refund amount:').' '.$amount.' '.$currency->sign.chr(10).chr(13);
		$this->addOrderNote($order, $message);

		$changeOrderState = false;
		if ($details['payment_by'] == 'ETransactionsSystemRecurring') {
			if ($details['payment_status'] == 'canceled') {
				$status = 'canceled/refundRecurring';
				$changeOrderState = true;
			}
			else {
				$status = 'refundRecurring';
				$changeOrderState = false;
			}
		}
		else {
			$status = 'Refunded';
			$changeOrderState = true;
		}

		// Update database
		$sql = 'UPDATE `%sETRANS_order` SET `payment_status` = "%s",'
			.' `id_transaction` = %d, `refund_amount` = refund_amount + %d'
			.' WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, $status, intval($response['NUMTRANS']),
			round($amount * $amountScale), $order->id);
		if (!Db::getInstance()->Execute($sql)) {
			die(Tools::displayError('Error when updating E-Transactions database'));
		}

		// Change state of order if needed
		if ($changeOrderState) {
			$history = new OrderHistory();
			$history->id_order = (int)($order->id);
			$history->changeIdOrderState(_PS_OS_REFUND_, $history->id_order);
			$history->addWithemail();
		}

		return 0;
	}

	/**
	 * @return 3 if amount too high, 2 if call error, 1 if ETransactions error, 0 if success
	 */
	public function makeRefundAmount(Order $order, array $details, $amount) {
		// Refund partial (?)
		$partialRefund = 0;
		if (version_compare(_PS_VERSION_, '1.5', '>=')) {
			$partialRefund = $this->getAmountPartialRefund($order->id);
		}

		// Amount to refund
		$amountScale = $this->getCurrencyScale($order);
		$amount = floatval(str_replace(',', '.', $amount));

		// Check if the refund amount is too high
		$check = $details['amount'] - $details['refund_amount'];
		$check -= round($amount * $amountScale);
		$check -= round($partialRefund * $amountScale);
		if ($check < 0) {
			return 3;
		}

		// Transaction informations
		$trxId = $details['id_transaction'];
		$callId = $details['num_appel'];

		$response = $this->_makeRefund($order, $trxId, $callId, $amount, $details['carte']);

		$currency = new Currency(intval($order->id_currency));

		// Call error
		if (empty($response)) {
			return 2;
		}

		// E-Transactions error
		else if ($response['CODEREPONSE'] != '00000') {
			$message = $this->l('E-Transactions refund amount:').chr(10).chr(13);
			$message .= $this->l('Return code: error').chr(10).chr(13);
			$message .= $this->l('Refund amount:').' '.$amount.' '.$currency->sign.chr(10).chr(13);
			$message .= $this->l('For more information logon to the back office ETransactions');
			$this->addOrderNote($order, $message);

			return 1;
		}

		// Success
		$message = $this->l('E-Transactions refund amount:').chr(10).chr(13);
		$message .= $this->l('Return code: ok').chr(10).chr(13);
		$message .= $this->l('Ref. ETransactions:').$response['NUMTRANS'].chr(10).chr(13);
		$message .= $this->l('Refund amount:').' '.$amount.' '.$currency->sign.chr(10).chr(13);
		$this->addOrderNote($order, $message);

		// Update database
		$sql = 'UPDATE `%sETRANS_order` SET `refund_amount` = refund_amount + %d'
			.' WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, round($amount * $amountScale), $order->id);
		if (!Db::getInstance()->Execute($sql)) {
			die(Tools::displayError('Error when updating E-Transactions database'));
		}

		return 0;
	}

	/**
	 * Sign E-Transactions System message
	 */
	public function signValues(array $values) {
		// Serialize values
		$params = array();
		foreach ($values as $name => $value) {
			$params[] = $name.'='.$value;
		}
		$query = implode('&', $params);

		// Prepare key
		$key = pack('H*', $this->getConfig()->getHmacKey());

		// Sign values
		$sign = hash_hmac($this->getConfig()->getHmacAlgo(), $query, $key);
		if ($sign === false) {
			$errorMsg = 'Unable to create hmac signature. Maybe a wrong configuration.';
			throw new Exception($this->l($errorMsg));
		}

		return strtoupper($sign);
	}

	public function toErrorMessage($code) {
		if (isset($this->_errorCode[$code])) {
			return $this->_errorCode[$code];
		}

		return 'Unknown error '.$code;
	}

	/**
	 * Load order from the $token
	 * @param string $token Token (@see tokenizeOrder)
	 * @return Cart
	 */
	public function untokenizeCart($token) {
		$parts = explode(' - ', $token, 2);
		if (count($parts) < 2) {
			$message = 'Invalid decrypted token "%s"';
			throw new Exception(sprintf($this->l($message), $token));
		}

		// Retrieves order
		$cart = new Cart($parts[0]);
		if (!Validate::isLoadedObject($cart)) {
			$message = 'Not existing order id from decrypted token "%s"';
			throw new Exception(sprintf($this->l($message), $token));
		}

		$customer = new Customer($cart->id_customer);
		$name = $this->getBillingName($customer);
		if (($name != utf8_decode($parts[1])) && ($name != $parts[1])) {
			$message = 'Consistency error on descrypted token "%s"';
			throw new Exception(sprintf($this->l($message), $token));
		}

		return $cart;
	}

	public function updateOrderRecurringDetails(Order $order, $amountPaid, $numberTerm) {
		$db = Db::getInstance();
		$sql = 'UPDATE `%sETRANS_recurring` SET `number_term` = %d, `amount_paid` = %d WHERE `id_order` = %d';
		$sql = sprintf($sql, _DB_PREFIX_, $numberTerm, $amountPaid, $order->id);
		$db->Execute($sql);
		return true;
	}

}
