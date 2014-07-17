<?php
require_once('modules/CobroPago/Purchase.php');

$options = array();
if (isset($_REQUEST['notify_url'])) {
  $options['notifyUrl'] = $_REQUEST['notify_url'];
}
if (isset($_REQUEST['return_url'])) {
  $options['returnUrl'] = $_REQUEST['return_url'];
}
if (isset($_REQUEST['cancel_url'])) {
  $options['cancelUrl'] = $_REQUEST['cancel_url'];
}

$purchase = new Purchase();

try {
	$response = $purchase->pay($_REQUEST['cpid'], $options);
} catch (Exception $e) {
	if ($e->getMessage() === 'CPID_ERROR') {
		$message = getTranslatedString('Invalid payment.');
	} else {
		throw $e;
	}
}

if ($response !== null) {
	if ($response->isSuccessful()) {
		if (isset($options['returnUrl'])) {
			header('Location: ' . $options['returnUrl']);
			exit;
		}
		$message = getTranslatedString('Payment done.');
	} elseif ($response->isRedirect()) {
		$message = getTranslatedString('Redirecting to payment gateway...');
	} else {
		if (isset($options['cancelUrl'])) {
			header('Location: ' . $options['cancelUrl']);
			exit;
		}
		$message = getTranslatedString('Payment error.');
	}
}

require('modules/CobroPago/Pay.tpl.php');
