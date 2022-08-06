<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Controllers\api\CurlController;

class Orders extends BaseController
{
  protected $curlController;

  public function index()
  {
    $amountMXN = 0;
    $amountEUR = 0;
    $amountUSD = 0;
    $amountBOB = 0;
    $amountTotal = 0;
    $amountDiscounted = 0;
    $this->curlController = new CurlController();
    if (array_key_exists('date', $_GET)){
      $data['orders'] = json_decode($this->curlController->getOrders($_GET['date']));
    }
    else {
      $data['orders'] = json_decode($this->curlController->getOrders());
    }

    foreach($data['orders'] as $order) {
      $amountMXN = $amountMXN + $order->total;
      $amountDiscounted = $amountDiscounted + $order->discount;
      $amountTotal = $amountTotal + $order->subtotal;
    }

    $amountEUR = json_decode($this->curlController->convertMoney('EUR', $amountMXN));
    usleep(1000000);
    $amountUSD = json_decode($this->curlController->convertMoney('USD', $amountMXN));
    usleep(1000000);
    $amountBOB = json_decode($this->curlController->convertMoney('BOB', $amountMXN));
    usleep(1000000);

    $data['totals'] = array('MXN' => $amountMXN, 'EUR' => $amountEUR->rates->EUR->rate_for_amount, 'USD' => $amountUSD->rates->USD->rate_for_amount, 'BOB' => $amountBOB->rates->BOB->rate_for_amount);

    $chartData = array();
    array_push($chartData, array("label" => "Ganancias", "y" => (($amountMXN * 100)/$amountTotal)));
    array_push($chartData, array("label" => "Descuentos", "y" => (($amountDiscounted * 100)/$amountTotal)));
    
    $data['chartData'] = $chartData;
    return view('Front/Orders', $data);
  }
}