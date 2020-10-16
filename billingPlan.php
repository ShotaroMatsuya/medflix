<?php
require_once("includes/paypalConfig.php");

use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;

// Create a new billing plan
$plan = new Plan();
$plan->setName('Reeceflix monthly subscription')
    ->setDescription('Gets you all the features of our site.')
    ->setType('INFINITE'); //cancelするまでずっと請求

// Set billing plan definitions
$paymentDefinition = new PaymentDefinition();
$paymentDefinition->setName('Regular Payments')
    ->setType('REGULAR')
    ->setFrequency('Month')
    ->setFrequencyInterval('1') //毎月請求
    ->setAmount(new Currency(array('value' => 9.99, 'currency' => 'GBP'))); //金額と通貨を設定

// Set charge models
// $chargeModel = new ChargeModel();
// $chargeModel->setType('SHIPPING')
//     ->setAmount(new Currency(array('value' => 10, 'currency' => 'USD')));
// $paymentDefinition->setChargeModels(array($chargeModel));

$currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$returnUrl = str_replace("billing.php", "profile.php", $currentUrl); //billing→profileへreplace
// Set merchant preferences
$merchantPreferences = new MerchantPreferences();
$merchantPreferences->setReturnUrl($returnUrl . "?success=true") //成功したときのpage
    ->setCancelUrl($returnUrl . "?success=false") //失敗したときのpage
    ->setAutoBillAmount('yes')
    ->setInitialFailAmountAction('CONTINUE')
    ->setMaxFailAttempts('0')
    ->setSetupFee(new Currency(array('value' => 9.99, 'currency' => 'GBP')));

$plan->setPaymentDefinitions(array($paymentDefinition));
$plan->setMerchantPreferences($merchantPreferences);

//create plan
try {
    $createdPlan = $plan->create($apiContext);

    try {
        $patch = new Patch();
        $value = new PayPalModel('{"state":"ACTIVE"}');
        $patch->setOp('replace')
            ->setPath('/')
            ->setValue($value);
        $patchRequest = new PatchRequest();
        $patchRequest->addPatch($patch);
        $createdPlan->update($patchRequest, $apiContext);
        $plan = Plan::get($createdPlan->getId(), $apiContext);

        // Output plan id
        echo $plan->getId();
    } catch (PayPal\Exception\PayPalConnectionException $ex) {
        echo $ex->getCode();
        echo $ex->getData();
        die($ex);
    } catch (Exception $ex) {
        die($ex);
    }
} catch (PayPal\Exception\PayPalConnectionException $ex) {
    echo $ex->getCode();
    echo $ex->getData();
    die($ex);
} catch (Exception $ex) {
    die($ex);
}
