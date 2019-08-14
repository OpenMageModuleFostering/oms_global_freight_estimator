<?php

class OMS_Shippingimport_IndexController extends Mage_Core_Controller_Front_Action
{
  
  public function indexAction()
  {
	  try
	  {
		  $url=Mage::getStoreConfig('webservice/webservice_group/webservice_url',Mage::app()->getStore());		
		  $username=Mage::getStoreConfig('webservice/webservice_group/webservice_username',Mage::app()->getStore());		
		  $password=Mage::getStoreConfig('webservice/webservice_group/webservice_password',Mage::app()->getStore());
		  $importModel= Mage::getModel('shippingimport/import');		
		  
		  $client = new SoapClient($url);
		  
		  //$session = $client->__login('testaccount@oms.com.au', 'f7vmz!299pl');
		  $request["strB2BCustomerName"]="Park City Apparel";
		  $request["strB2BCustomerEmail"]=$username;
		  $request["strB2BCustomerPassword"]=$password;
		  $request["strCurrencyAbbrev"]="AUD";
		  $request["strWarehouseName"]="";
		  $request["lngAdjustedWeight"]="";
		  $request["siUnit"]="US";
		  
		  
		  $innerArray["Quantity"]=60;
		  $innerArray["Length"]=10;
		  $innerArray["Width"]=30;
		  $innerArray["Height"]=40;
		  $innerArray["ActualWeight"]=10;
		  $request["oLineItems"]["FreightLineItem"]=$innerArray;
		  
		  $request["oRestriction"]="NA";
		  $request["strDestinationCountry"]="AUS";
		  $request["strDestinationPostal"]="2001";
		  $request["strOriginationCountry"]="AUS";
		  $request["strOriginationPostal"]="2170";
		  $request["lngInsuranceAmount"]="0";
		  $request["bAddHandlingFees"]=false;
		  $request["lngPctOfHandlingFees"]=0;
		  $result = $client->EstimateFreight($request);
		  $rateresult=$result->EstimateFreightResult->EstimateFreight->RateReplyDetails;
		  echo "<pre/>";
		  print_r($rateresult);
		  
		  foreach($rateresult as $rate)
		  {
			 // echo $rate->ServiceType;  
			  if(!empty($rate->ServiceType))
			  {
			  	$data=$importModel->checkMethod(trim($rate->ServiceType));
				if(empty($data))
				{
				   $data["name"]=$rate->ServiceType;
				   try
				   {
				   $importModel->setData($data)->save();
				   }
				   catch(Exception $e)
				   {
					 echo $e->getMessage();   
				   }
				   	
					
				}
			  }
		  }	  
		  
	echo "Data Saved";
 
		  
		  }
		  catch(Exception $e)
		  {
		  echo $e->getMessage();  
		  }
	  
	 
  }
  	
}