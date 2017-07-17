<?php

$dir = dirname(dirname(dirname(__FILE__)));

require_once($dir.'/config/config.inc.php');
require_once($dir.'/init.php');
require_once(dirname(__FILE__).'/ETransactionsEpayment.php');

$action = isset($_GET['a']) ? $_GET['a'] : null;
$c = new ETransactionsController();
try {
	switch ($action) {
		// Cancel
		case 'c':
			$c->cancelAction();
			break;

		// Failure
		case 'f':
			$c->failureAction();
			break;

		// Redirect
		case 'r':
			$c->redirectAction();
			break;

		// Success
		case 's':
			$c->successAction();
			break;

		// IPN
		case 'i':
			//file_put_contents('debug.log', file_get_contents('php://input'));die();
			$c->ipnAction();
			break;
		case 'j':
			$c->ipnAction();
			break;
		
		default:
			$c->defaultAction();
	}
}
catch (Exception $e) {
	header('Status: 500 Error', true, 500);
	echo $e->getMessage();
}
