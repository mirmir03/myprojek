<?php

define('IS_SANDBOX' , FALSE);
define('URL_DEV' , 'https://pay.umt.edu.my/billplz_dev/create_bill');

// define('URL_DEV' , 'https://pelajar.umt.edu.my/pay/billplz_dev/create_bill');
define('URL_PROD', 'https://pay.umt.edu.my/billplz_prod/create_bill');

class Bill 
{
	protected $isSandbox=false;
	protected $isSuccess=false;
	protected $idFpx=false;
	protected $idSys=false;
	protected $error_message=false;
	
	protected $params=[];
	protected $fields=[];
	
	public function setStaging()
	{
		$this->isSandbox = true;
	}
	
	public function create_bill($id_collection="", $fields1="")
	{
		$url = ($this->isSandbox) ? URL_DEV : URL_PROD ;
		
		
		// $key = "qazwsxedc142536";
		$key = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkudW10LmVkeS5teVwvZnVzaW9cL3B1YmxpYyIsInN1YiI6IjY2MGY3NDgyLTMzNmMtNTE4MC1iZmZlLWU2NGMyNzY5ZDEzMyIsImlhdCI6MTU5MTc0NzkwMywiZXhwIjo0NzE1Nzk5MTAzLCJuYW1lIjoiYWRtaW4ifQ.XhGqO40NiRtvVDEcWxgPL1IdrZyw8SOcUlRr9TmhGzA";
		
		$fields['key'] = $key;
		// $fields['appid'] = $id_collection;
		$fields['appid'] = $this->idSys;
		
		$fields = $fields + $this->fields;
		
		$fields_string="";
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);

		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//execute post
		$result = curl_exec($ch); 
		
		

		if (curl_errno($ch)) {
			$error_msg = curl_error($ch);
			
			$return = array('status' => false, 'message' => $error_msg);	
			$this->error_message = $error_msg;
			$this->isSuccess = false;
		}
		else 
		{ 
			$respon = json_decode($result); 
		

			if ($respon->status == 1) {		
				$return = array('id' => $respon->id, 'status' => true, 'bill_url' => $respon->url);
				$this->params = $return;
				$this->isSuccess = true;
				//header('Location: ' . $respon->url);
			}
			else
			{
				$return = array('status' => false, 'message' => "error insert db");
				$this->isSuccess = false;
				$this->error_message = "Wrong Collection ID or X-Signature or API Key";
			}
			
			
		}
		
			
		//close connection
		curl_close($ch);
		
		return $return;
	}
	
	public function open_connection()
	{
		$url = $this->params["bill_url"];
		header('Location: ' . $url);
	}
	
	public function is_connected()
	{
		return $this->isSuccess;
	}
	
	public function get_bill_id()
	{
		return $this->idFpx;
	}
	
	public function get_error_message()
	{
		return $this->error_message;
	}
	
	public function set_amount($value="")
	{
		$this->fields["amount"] = $value;
	}
	
	public function set_email($value="")
	{
		$this->fields["email"] = $value;
	}
	
	public function set_name($value="")
	{
		$this->fields["name"] = $value;
	}
	
	public function set_reference($value="", $label="Rujukan")
	{
		$this->fields["reference_1_label"] = $label;
		$this->fields["reference_1"] = $value;
	}
	
	public function set_reference_2($label="Rujukan", $value="")
	{
		$this->fields["reference_2_label"] = $label;
		$this->fields["reference_2"] = $value;
	}
	
	public function set_id_collection($id)
	{
		$this->idSys = $id;
	}
	
	public function get_response()
	{
		return $_REQUEST;
	}
	
	public function is_paid()
	{
		return ($_POST["paid"] == "1") ? true : false;
	}
	
	public function get_paid_amount()
	{
		return $_POST["amount"] / 100;
	}
	
	public function get_reference()
	{
		return $_POST["reference_1"];
	}
	public function get_reference2()
	{
		return $_POST["reference_2"];
	}
	
	public function get_transaction_id()
	{
		return $_POST["id"];
	}
}	