<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Controllers\api\CurlController;

class Home extends BaseController
{
    protected $curlController;

    public function index()
    {
        $this->curlController = new CurlController();
        if (array_key_exists('date', $_GET)){
            $data['products'] = json_decode($this->curlController->getProducts("frontCall", $_GET['date']));
            $dateFilter = strtotime($_GET['date']);
            $data['filter'] = date('M', $dateFilter) .' ' . date('Y', $dateFilter);
        }
        else {
            $data['products'] = json_decode($this->curlController->getProducts("frontCall"));
            $data['filter'] = 'General';
        }
        $productsArray = $data['products'];
        $chartData = array();
        $totalProducts = 0;
        foreach($productsArray as $product) {
            $totalProducts = $totalProducts + $product->sale_count;
        }
        for($index = 0; $index < count($productsArray); $index++) {
            array_push($chartData, array("label" => $productsArray[$index]->title, "y" => (($productsArray[$index]->sale_count/$totalProducts)*100)));
        }
        $data['chartData'] = $chartData;
        return view('Front/home', $data);
    }
}
