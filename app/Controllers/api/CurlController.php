<?php

namespace APP\Controllers\api;

use App\Controllers\BaseController;

class CurlController extends  BaseController{

  var $url = 'https://sandbox.ixaya.net/api/';

  public function getProducts($apiRequest = null, $dateSelected = null) {
  
    // Get all products
    $ch = curl_init($this->url . "products");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'x-api-key: owkcsc0ks8k0cocs4gscw8kw40gss00448so0gco'
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result);

    $responseData = $data->response;

    $products = array();
    $productsOrders = array();
    foreach($responseData as $product) {
      $products[$product->id] = 0;
      $productsOrders[$product->title] = array();
    }

    // Get all orders

    $ch = curl_init($this->url . "orders/list_record");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'x-api-key: owkcsc0ks8k0cocs4gscw8kw40gss00448so0gco'
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result);

    
    $ordersResponse = $data->response;
    
    // Apply filter & get total of date
    for($order = 0; $order < count($ordersResponse); $order++) {
      $textFilter = $ordersResponse[$order]->last_update;
      $orderDate = strtotime($textFilter);
      if ($dateSelected != null) {
        $dateFilter = strtotime($dateSelected);
        if(date('M/Y', $dateFilter) == date('M/Y', $orderDate)) {
          for($productOrder = 0; $productOrder < count($ordersResponse[$order]->products); $productOrder++) {
            $products[$ordersResponse[$order]->products[$productOrder]->id] = $products[$ordersResponse[$order]->products[$productOrder]->id] + (int)$ordersResponse[$order]->products[$productOrder]->qty;
            if(array_key_exists($ordersResponse[$order]->order_code, $productsOrders[$ordersResponse[$order]->products[$productOrder]->title])) {
              $productsOrders[$ordersResponse[$order]->products[$productOrder]->title][$ordersResponse[$order]->order_code] = $productsOrders[$ordersResponse[$order]->products[$productOrder]->title][$ordersResponse[$order]->order_code] + (int)$ordersResponse[$order]->products[$productOrder]->qty;
            }
            else {
              $title = $ordersResponse[$order]->products[$productOrder]->title;
              array_push($productsOrders[$title], array($ordersResponse[$order]->order_code => (int)$ordersResponse[$order]->products[$productOrder]->qty));
            }
          }
        }
      }
      else {
        for($productOrder = 0; $productOrder < count($ordersResponse[$order]->products); $productOrder++) {
          $products[$ordersResponse[$order]->products[$productOrder]->id] = $products[$ordersResponse[$order]->products[$productOrder]->id] + (int)$ordersResponse[$order]->products[$productOrder]->qty;
          if(array_key_exists($ordersResponse[$order]->order_code, $productsOrders[$ordersResponse[$order]->products[$productOrder]->title])) {
            $productsOrders[$ordersResponse[$order]->products[$productOrder]->title][$ordersResponse[$order]->order_code] = $productsOrders[$ordersResponse[$order]->products[$productOrder]->title][$ordersResponse[$order]->order_code] + (int)$ordersResponse[$order]->products[$productOrder]->qty;
          }
          else {
            $title = $ordersResponse[$order]->products[$productOrder]->title;
            array_push($productsOrders[$title], array($ordersResponse[$order]->order_code => (int)$ordersResponse[$order]->products[$productOrder]->qty));
          }
        }
      }
    }

    $newProperty = 'orders';
    $converter = 'amountConverter';
    $amountUSD = json_decode($this->convertMoney('USD', 100));
    usleep(1000000);
    $amountBOB = json_decode($this->convertMoney('BOB', 100));
    usleep(1000000);
    $amountEUR = json_decode($this->convertMoney('EUR', 100));
    usleep(1000000);
    
    foreach($responseData as $product) {
      $product->sale_count = $products[$product->id];
      $product->{$newProperty} = $productsOrders[$product->title];
      $product->{$converter} = $amountesConverted[$product->title] = array('USD'=> $amountUSD->rates->USD->rate, 'BOB'=> $amountBOB->rates->BOB->rate, 'EUR'=> $amountEUR->rates->EUR->rate);
    }

    usort($responseData, function($a, $b) {
      if ($a->sale_count==$b->sale_count) return 0;
      return ($a->sale_count>$b->sale_count)?-1:1;
    });

    header('Content-Type: application/json');
    if ($apiRequest != null) {
      return json_encode($responseData);
    }
    else {
      return $this->response->setJson($responseData);
    }
  }

  public function convertMoney($to='USD', $amount=1){
    //$curl = curl_init('https://currency-converter5.p.rapidapi.com/currency/convert?&amp;from=MXN&amp;to='.$to'&amp;amount='.$amount'&amp;format=json');
    $curl = curl_init('https://currency-converter5.p.rapidapi.com/currency/convert?format=json&from=MXN&to='.$to .'&amount='.$amount);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'X-RapidAPI-Key: ef67070cddmshc377e90448a83e1p15d0b1jsn0c0fad1abe77'
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
      //echo "cURL Error #:" . $err;
    } else {
      return $response;
    }
  }

  public function getOrders($dateSelected = null){
    $ordersFiltered = array();
    
    $ch = curl_init($this->url . "orders/list_record");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'x-api-key: owkcsc0ks8k0cocs4gscw8kw40gss00448so0gco'
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result);

    foreach($data->response as $order){
      if ($dateSelected != null) {
        $dateFilter = $order->last_update;
        $orderDate = strtotime($dateFilter);
        $dateFilter = strtotime($dateSelected);
        if(date('M/Y', $dateFilter) == date('M/Y', $orderDate)) {
          array_push($ordersFiltered, $order);    
        }
      }
      else {
        array_push($ordersFiltered, $order);
      }
    }

    return json_encode($ordersFiltered);
  }

}
