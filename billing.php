<?php
require_once("includes/paypalConfig.php");
require_once("billingPlan.php"); //このファイルから発行されたplanIdを取得する

$id = $plan->getId();



use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

// Create new agreement
$agreement = new Agreement();
$agreement->setName('Medflixを定期購読する(試験運用版)')
    ->setDescription('加入後1ヶ月後に請求が開始され、その後1ヶ月毎に自動更新されます。')
    ->setStartDate(date("Y-m-d\TH:i:s\Z", strtotime("+1 month", time())));
//gmdate関数はdate() 関数と同じですが、返される時刻が グリニッジ標準時 (GMT) であるところが異なります。

// Set plan id
$plan = new Plan();
$plan->setId($id);
$agreement->setPlan($plan);

// Add payer type
$payer = new Payer();
$payer->setPaymentMethod('paypal');
$agreement->setPayer($payer);

// Adding shipping details
// $shippingAddress = new ShippingAddress();
// $shippingAddress->setLine1('111 First Street')
//     ->setCity('Saratoga')
//     ->setState('CA')
//     ->setPostalCode('95070')
//     ->setCountryCode('US');
// $agreement->setShippingAddress($shippingAddress);
try {
    // Create agreement
    $agreement = $agreement->create($apiContext);

    // Extract approval URL to redirect user
    $approvalUrl = $agreement->getApprovalLink();
    echo $approvalUrl;
    header("Location: $approvalUrl");
} catch (PayPal\Exception\PayPalConnectionException $ex) {
    echo $ex->getCode();
    echo $ex->getData();
    die($ex);
} catch (Exception $ex) {
    die($ex);
}
