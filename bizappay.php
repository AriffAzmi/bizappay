<?php

/**
 * 
 */
class Bizappay
{
	protected $apiKey;
	protected $sandbox;

	function __construct($apiKey,$sandbox=false)
	{
		$this->apiKey = $apiKey;
		$this->sandbox = $sandbox;
	}

	public function createBill()
	{
		try {
			
			$obj = (object) get_object_vars($this);

			$params = [
				'apiKey' => (isset($obj->apiKey) ? $obj->apiKey : die("Api key not provided.")),
				'categoryCode' => (isset($obj->categoryCode) ? $obj->categoryCode : die("Category code not provided.")),
				'billName' => (isset($obj->billName) ? $obj->billName : die("Bill name not provided.")),
				'billDescription' => $obj->billDescription,
				'billAmount' => (isset($obj->billAmount) ? $obj->billAmount : die("Bill amount not provided.")),
				'billTo' => (isset($obj->billTo) ? $obj->billTo : die("Customer name not provided.")),
				'billEmail' =>  (isset($obj->billEmail) ? $obj->billEmail : die("Customer email not provided.")),
				'billPhone' =>  (isset($obj->billPhone) ? $obj->billPhone : die("Customer phone not provided.")),
				'billReturnUrl' => $obj->billReturnUrl,
				'billCallbackUrl' => $obj->billCallbackUrl,
				'billExternalReferenceNo' => $obj->billExternalReferenceNo
			];

			$this->clearParams();

			$url = $this->getUrl("createBill");

			return $this->run($url,$params);

		} catch (\Exception $e) {
			
			die($e->getMessage());
		}
	}

	public function createNewCategory()
	{
		try {
			
			$obj = (object) get_object_vars($this);
			$params = [
				'apiKey' => (isset($obj->apiKey) ? $obj->apiKey : die("Api key not provided.")),
				'categoryName' => (isset($obj->categoryName) ? $obj->categoryName : die("Category name not provided.")),
				'categoryDescription' => (isset($obj->categoryDescription) ? $obj->categoryDescription : die("Category description not provided."))
			];

			$this->clearParams();

			$url = $this->getUrl("createNewCategory");

			return $this->run($url,$params);

		} catch (\Exception $e) {
			
			die($e->getMessage());
		}
	}

	public function run($url,$params)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($curl);
		curl_close($curl);

		return json_decode($result);
	}

	public function getUrl($process)
	{
		if ($process=="createBill") {
			
			if ($this->sandbox) {
				
				return "https://bizappay.com/staging/api/createNewBill";
			}
			
			return "https://bizappay.com/api/createNewBill";
		}
		elseif ($process=="createNewCategory") {
			
			if ($this->sandbox) {
				
				return "https://bizappay.com/staging/api/createNewCategory";
			}
			
			return "https://bizappay.com/api/createNewCategory";
		}
		else {

			die("Process not found");
		}
	}

	public function clearParams()
	{
		$obj = get_object_vars($this);
		foreach ($obj as $key => $value) {
			
			unset($obj[$key]);
		}
	}
}

// CREATE CATEGORY IF YOU DONT HAVE YET
$category = new Bizappay("your-api-key",true);
$category->categoryName = "Kategori Cubaan.";
$category->categoryDescription = "Kategori cubaan pembayaran.";
$category = $category->createNewCategory();


// CREATE A PAYMENT LINK
$payment = new Bizappay("your-api-key",true);
$payment->categoryCode = $category->categoryCode;
$payment->billName = "Cubaan Pembayaran";
$payment->billDescription = "Bayaran percubaan";
$payment->billAmount = "1.00";
$payment->billTo = "Ahmad Albab";
$payment->billEmail = "ahmad.albab@gmail.com";
$payment->billPhone = "0123456789";

$pay = $payment->createBill();

if ($pay->status=="ok") {
	
	header("Location: ".$pay->paymentUrl);
}
else {

	echo $pay->msg;
}