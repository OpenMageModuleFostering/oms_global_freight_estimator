<?php
 class  FreightLineItem
 {
    public $Quantity;
	public	$Length;
	public $Width;
	public $Height;
	public $ActualWeight;
	 
}
class OMS_Shippingimport_Model_Carrier_Localdelivery extends Mage_Shipping_Model_Carrier_Abstract
 implements Mage_Shipping_Model_Carrier_Interface
{
    /* Use group alias */
    protected $_code = 'customshiping';
    	
 
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    { 
	
       //  skip if not enabled
        if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active'))
            return false;
	   
	     try
	  { 
		$enable=Mage::getStoreConfig('webservice/webservice_group/webservice_enable',Mage::app()->getStore());	
		
		if($enable=="no")
		{
		  return false;	
			
		}
		
	
		
		 $url=Mage::getStoreConfig('webservice/webservice_group/webservice_url',Mage::app()->getStore());
		 $pos=strpos($url,"?WSDL");
		 if($pos === false) 
		 {
    		$url=$url."?WSDL";
			
		 }		
		  $username=Mage::getStoreConfig('webservice/webservice_group/webservice_username',Mage::app()->getStore());		
		  $password=Mage::getStoreConfig('webservice/webservice_group/webservice_password',Mage::app()->getStore());
		  $allowedMethods=Mage::getStoreConfig('carriers/customshiping/allowedmethods',Mage::app()->getStore());
		  $shipping_title = Mage::getStoreConfig('carriers/customshiping/title',Mage::app()->getStore());
		  $allMethods=Mage::getModel("shippingimport/import")->getAllMethods();
		  $methods=array();
		foreach($allMethods as $method)
		{
			$methods[]=$method["name"];
			
		}
		  $warehousename=Mage::getStoreConfig('webservice/webservice_group/webservice_warehousename',Mage::app()->getStore());
		  $OriginCountry =Mage::getStoreConfig('webservice/webservice_group/webservice_originCountry',Mage::app()->getStore());
		  $OriginPostal =Mage::getStoreConfig('webservice/webservice_group/webservice_originPostal',Mage::app()->getStore());
		  $Unit = Mage::getStoreConfig('webservice/webservice_group/webservice_unit',Mage::app()->getStore());
		  $custname = Mage::getStoreConfig('webservice/webservice_group/webservice_custname',Mage::app()->getStore());
		  
		  if($Unit=="")
		  $Unit="US";
		  $InsuranceAmount= Mage::getStoreConfig('webservice/webservice_group/webservice_insuranceamount',Mage::app()->getStore());
		  $AddHandlingFees = Mage::getStoreConfig('webservice/webservice_group/webservice_addhandlingfees',Mage::app()->getStore());
		  
		  if($AddHandlingFees=="true")
		  {
			  
			$AddHandlingFees==1;  
		  }
		  else
		  {
			  
			$AddHandlingFees=0;  
		   }
		
		  
		  $HandlingFeePercent =  Mage::getStoreConfig('webservice/webservice_group/webservice_handlingfeepercent',Mage::app()->getStore());
		  $debug=Mage::getStoreConfig('webservice/webservice_group/webservice_debug',Mage::app()->getStore());
		  
		 
		  $currencycode =  Mage::getStoreConfig('webservice/webservice_group/webservice_CurrencyAbbrev',Mage::app()->getStore());
		  
		  if($currencycode=="")
		  {
		  $currencycode=Mage::app()->getStore()-> getCurrentCurrencyCode(); 
		  }
		  
		  
		  	  
		  $allowedMethodsArr=split(",",$allowedMethods);
		    $adminsession = Mage::getSingleton('admin/session')->isLoggedIn();
          
		  $session = Mage::getSingleton('checkout/session');
	
		  $object= new FreightLineItem();
		  if (!empty($session) || !empty($adminsession)) 
		  {
		
            $count=0;
			
			 if(Mage::getSingleton('admin/session')->isLoggedIn())
			 {
    		  $items = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getAllVisibleItems();
			  $adminData = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getShippingAddress()->getData();
			  if(empty($postcode))
			  {
				
				$postcode=$adminData['postcode'] ; 
				$country=$adminData['country_id'];
				$_iso3countrycode=$this->_getISO3Code($country); 
				      
			  }
			 
  			}
  			else 
			{   
				
				$data = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getData();
				$postcode = $data['postcode'];
				$country=$data['country_id'];
				
				$_iso3countrycode=$this->_getISO3Code($country); 
				
				       
				$items = Mage::getSingleton('checkout/cart')->getItems();
  			}
		  $importModel= Mage::getModel('shippingimport/import');	
		   $result = Mage::getModel('shipping/rate_result');
		   $result->reset();
		  /* $methods = get_class_methods($result);
		   echo "<pre/>";
		   print_r($methods);
		   exit;*/ 	
		  
		  $client = new SoapClient($url, array('trace' => TRUE));
		  
		  //$session = $client->__login('testaccount@oms.com.au', 'f7vmz!299pl');
		 $SOAPrequest["strB2BCustomerName"]=$custname;
		  $SOAPrequest["strB2BCustomerEmail"]=$username;
		  $SOAPrequest["strB2BCustomerPassword"]=$password;
		  $SOAPrequest["strCurrencyAbbrev"]=$currencycode;
		  $SOAPrequest["strWarehouseName"]=$warehousename;
		  $SOAPrequest["lngAdjustedWeight"]="";
		  $SOAPrequest["siUnit"]= $Unit;
		  
		 
			$itemlength=0;
			$itemWidth=0;
			$itemHeight=0;
			$itemWeight=0;
			$itemQuantity=0;
			
			$count=0;
			$freightArray[]="";
			/*echo "<pre/>";
			print_r($items->getData());*/
			
			$parentid[]="";
			$real_qty=0;
			foreach ($items as $item) 
			{
               $object= new FreightLineItem();
				$_product= Mage::getSingleton('catalog/product')->load($item->getProductId()); 
				
 				$itemQuantity =(int)$item->getQty();
				$length=$_product->getResource()->getAttribute('length');
				
				$id=$item->getId();
				//$parentid[]=$id;
				$parent_item_id=$item->getParentItemId();
				if($parent_item_id=="")
				{
				  $real_qty=$itemQuantity;	
				}
				if($parent_item_id!="" || !($_product->isConfigurable()))
				{
				
				if(!empty($length))
				{
				$itemlength =(int)$length->getFrontend()->getValue($_product);
				
				}
				else
				{ 
				 $itemlength =0; 
				}
				
				$width=$_product->getResource()->getAttribute('width');
				if(!empty($width))
				{
		  		$itemWidth =(int)$width->getFrontend()->getValue($_product);
				}
				else
				{
				 $itemWidth =0;	
				}
				$height=$_product->getResource()->getAttribute('height');
				if(!empty($height))
				{
		  		$itemHeight =(int)$height->getFrontend()->getValue($_product);
				}
				else
				{
				 $itemHeight =0; 	
				}
		  		$itemWeight =(float)$item->getWeight();
				
				 $object->Quantity =$real_qty;
		  
		  $object->Length = $itemlength;
		  
		  $object->Weight =$itemWeight;
		  $object->Width = $itemWidth;
		  $object->Height = $itemHeight;
		  $object->ActualWeight=$itemWeight;
		  
		  $freightArray[$count]=$object;
		  $count++;
           $_product=null;
		   $object=null;
            }
          }
		  }
		/* echo "<pre/>";
		  print_r($freightArray);
		  exit;*/
		  
		  $SOAPrequest["oLineItems"]['FreightLineItem']=$freightArray;
		  $SOAPrequest["oRestriction"]="NA";
		  $SOAPrequest["strDestinationCountry"]=$_iso3countrycode;
		  $SOAPrequest["strDestinationPostal"]=$postcode;
		  $SOAPrequest["strOriginationCountry"]=$OriginCountry;
		  $SOAPrequest["strOriginationPostal"]=$OriginPostal;
		  $SOAPrequest["lngInsuranceAmount"]=$InsuranceAmount;
		  $SOAPrequest["bAddHandlingFees"]=$AddHandlingFees;
		  $SOAPrequest["lngPctOfHandlingFees"]=$HandlingFeePercent;
		 
/*echo "<pre/>";
		  print_r($SOAPrequest); 
		  exit;*/
		  if($debug=="true")
		  {
			  Mage::log("SOAP request:", null, 'OMS_Shipping.log');
			Mage::log($SOAPrequest, null, 'OMS_Shipping.log');  
		  }
		  $result1 = $client->EstimateFreight($SOAPrequest);
		  
		   if($debug=="true")
		  {
			Mage::log("SOAP response:", null, 'OMS_Shipping.log');
			Mage::log($result1, null, 'OMS_Shipping.log');  
		  }
		  $method = Mage::getModel('shipping/rate_result_method');
		 // $methods=get_class_methods($method);
		 
		
		//echo $result1->EstimateFreightResult->WSMessage;exit;
		  try
		  {
		   $rateresult=$result1->EstimateFreightResult->EstimateFreight->RateReplyDetails;
			
		  }
		  catch(Exception $e)
		  {
			  Mage::getSingleton('core/session')->setData('Webservice',0); 
			  if($debug=="true")
		  		{
			  		Mage::log("SOAP Error:", null, 'OMS_Shipping.log');
					Mage::log($result1->EstimateFreightResult->WSMessage, null, 'OMS_Shipping.log');  
		  		}
			$data['exception'] = $result1->EstimateFreightResult->WSMessage;	
			Mage::helper('Shippingimport')->sendEmail($data);	
		  }
/*echo "<pre/>";
print_r($methods);
echo "<br/>";
print_r($rateresult);
exit;*/
	  if(!empty($rateresult))
		  { 
			  $config_methods="";
			 Mage::getSingleton('core/session')->setData('Webservice',1);
		  foreach($rateresult as $rate)
		  {
			 // echo $rate->ServiceType;  
			  if(!empty($rate->ServiceType))
			  {
			  	$data=$importModel->checkMethod(trim($rate->ServiceType));
				if(empty($data))
				{
				  // $data["name"]=$rate->ServiceType;
				   try
				   {
				   //$importModel->setData($data)->save();
				   if($rate->ServiceType!="")
				    $importModel->insertShipping($rate->ServiceType);
				   }
				   catch(Exception $e)
				   {
					// echo $e->getMessage();   
					 if($debug=="true")
		  				{
			  				Mage::log("Code Error:", null, 'OMS_Shipping.log');
							Mage::log($e->getMessage(), null, 'OMS_Shipping.log');  
		  				}
						
					$data['exception'] = $e->getMessage();	
					Mage::helper('Shippingimport')->sendEmail($data);
				   }
				}
				
				if(in_array($rate->ServiceType,$allowedMethodsArr) || (!in_array($rate->ServiceType,$methods)))
				{
				 $method = Mage::getModel('shipping/rate_result_method');
				  $method->setCarrier($this->_code);
				  $method->setCarrierTitle($shipping_title);
				  $method->setMethod($rate->ServiceType);
				  $method->setMethodTitle($rate->ServiceType);
				  $method->setCost($rate->TotalCharge->Amount);
				  $method->setPrice($rate->TotalCharge->Amount);
				  $result->append($method);
				  
				  if(!in_array($rate->ServiceType,$methods))
				  {
					$configModel = new Mage_Core_Model_Config();
					
				
					if($config_methods!="")
					{
					$config_methods =$config_methods.",".trim($rate->ServiceType);
					}
					else
					{
						$config_methods=$allowedMethods;
					$config_methods .=$config_methods. trim($rate->ServiceType);
					}
						
					//$configModel ->saveConfig('design/head/demonotice', "1", 'default', 0);
					$configModel ->saveConfig('carriers/customshiping/allowedmethods', $config_methods);
				  }
				}
			  }
		
	     }	
		  } 
		  else /*Enhancement: Add the Failsafe rates. Updated on 11/10/2012*/
		 {
			  Mage::getSingleton('core/session')->setData('Webservice',0); 
			  $result = $this->failSafeRates($_iso3countrycode,$OriginCountry);
			
		 } 
		 
		  
		 
 
		
		  }
		  catch(Exception $e)
		  {
		  //echo $e->getMessage();
		  //exit;
		   Mage::getSingleton('core/session')->setData('Webservice',0); 
		 $result = $this->failSafeRates($_iso3countrycode,$OriginCountry);
		  if($debug=="true")
			{
				Mage::log("Code Error:", null, 'OMS_Shipping.log');
				Mage::log($e->getMessage(), null, 'OMS_Shipping.log');  
			}
			
			$data['exception'] = $e->getMessage();	
			Mage::helper('Shippingimport')->sendEmail($data);
		  }
	  
	   
	   
 
     
 
       
        return $result; 
		
    }
	
	 public function getAllowedMethods()
    {
        return array('customshiping'=>$this->getConfigData('name'));
    }
	
	public function failSafeRates($destination_country,$origin_country)
	{
	 
	    $result = Mage::getModel('shipping/rate_result');
		//$result->reset();
		
	   $alternatemethod = 
	   Mage::getStoreConfig('webservice/webservice_group/webservice_alternate',Mage::app()->getStore());
	  
	   $method = Mage::getModel('shipping/rate_result_method');
	   
	   $method->setCarrier($this->_code);
	   
	   $method->setCarrierTitle($this->_code);
				  
	   if($alternatemethod=="flatrates")
		 {
			 $domestic_rate = 
			 Mage::getStoreConfig('carriers/customshiping/domestic_fail_safe_rate',Mage::app()->getStore());
			
			 
			 $international_rate = 
			 Mage::getStoreConfig('carriers/customshiping/international_fail_safe_rate',Mage::app()->getStore());
			if($origin_country==$destination_country)
			{
				
				$method->setMethod("Domestic Rate");
				$method->setMethodTitle("Domestic Rate");
				$method->setCost($domestic_rate);
				$method->setPrice($domestic_rate);
				$result->append($method);
				
			}
			else
			{
			  	
				$method->setMethod("International Rate");
				$method->setMethodTitle("International Rate");
				$method->setCost($international_rate);
				$method->setPrice($international_rate);
				$result->append($method);
			}	 
				
			} 
			
		return $result; 
	}
	
	public function _getISO3Code($szISO2Code)
	{
	$boFound = false;
	$nCount = 1;
	
	$collection = Mage::getModel('directory/country_api')->items();
	
	while ($boFound == false &&
	$nCount < count($collection))
	{
	$item = $collection[$nCount];
	if($item['iso2_code'] == $szISO2Code)
	{
	$boFound = true;
	$szISO3Code = $item['iso3_code'];
	}
	$nCount++;
	}
	
	return $szISO3Code;
	} 
}
 