
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
         try
	  {
		  $url=Mage::getStoreConfig('webservice/webservice_group/webservice_url',Mage::app()->getStore());		
		  $username=Mage::getStoreConfig('webservice/webservice_group/webservice_username',Mage::app()->getStore());		
		  $password=Mage::getStoreConfig('webservice/webservice_group/webservice_password',Mage::app()->getStore());
		  $allowedMethods=Mage::getStoreConfig('carriers/customshiping/allowedmethods',Mage::app()->getStore());
		  $warehousename=Mage::getStoreConfig('webservice/webservice_group/webservice_warehousename',Mage::app()->getStore());
		  $OriginCountry =Mage::getStoreConfig('webservice/webservice_group/webservice_originCountry',Mage::app()->getStore());
		  $OriginPostal =Mage::getStoreConfig('webservice/webservice_group/webservice_originPostal',Mage::app()->getStore());
		  $Unit = Mage::getStoreConfig('webservice/webservice_group/webservice_unit',Mage::app()->getStore());
		  $custname = Mage::getStoreConfig('webservice/webservice_group/webservice_custname',Mage::app()->getStore());
		  
		  if($Unit=="")
		  $Unit="US";
		  $InsuranceAmount= Mage::getStoreConfig('webservice/webservice_group/webservice_insuranceamount',Mage::app()->getStore());
		  $AddHandlingFees = Mage::getStoreConfig('webservice/webservice_group/webservice_addhandlingfees',Mage::app()->getStore());
		
		  
		  $HandlingFeePercent =  Mage::getStoreConfig('webservice/webservice_group/webservice_handlingfeepercent',Mage::app()->getStore());
		  $data = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getData();
		  $postcode = $data['postcode'];
		 
		  $country=$data['country_id'];
		  $_iso3countrycode=$this->_getISO3Code($country);
		  
		  $currencycode=Mage::app()->getStore()-> getCurrentCurrencyCode(); 
		  
		  
		  	  
		  $allowedMethodsArr=split(",",$allowedMethods);
		  
		  $importModel= Mage::getModel('shippingimport/import');	
		   $result = Mage::getModel('shipping/rate_result');	
		  
		  $client = new SoapClient($url, array('trace' => TRUE));
		  
		  //$session = $client->__login('testaccount@oms.com.au', 'f7vmz!299pl');
		 $SOAPrequest["strB2BCustomerName"]=$custname;
		  $SOAPrequest["strB2BCustomerEmail"]=$username;
		  $SOAPrequest["strB2BCustomerPassword"]=$password;
		  $SOAPrequest["strCurrencyAbbrev"]=$currencycode;
		  $SOAPrequest["strWarehouseName"]=$warehousename;
		  $SOAPrequest["lngAdjustedWeight"]="";
		  $SOAPrequest["siUnit"]= $Unit;
		  
		   $adminsession = Mage::getSingleton('admin/session')->isLoggedIn();

		  $session = Mage::getSingleton('checkout/session');
		  if (!empty($session) || !empty($adminsession)) 
		  {
            $count=0;
			$object= new FreightLineItem();
			 if(Mage::getSingleton('admin/session')->isLoggedIn())
			 {
    		  $items = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getAllVisibleItems();
  			}
  			else 
			{            
    			$items = Mage::getSingleton('checkout/cart')->getItems();
  			}

			foreach ($items as $item) 
			{
                $_product= Mage::getSingleton('catalog/product')->load($item->getProductId()); 
 
				
				
				
				$object->Quantity +=(int)$item->getQty();
				$length=$_product->getResource()->getAttribute('length');
				
				if(!empty($length))
				{
				$object->Length +=(int)$length->getFrontend()->getValue($_product);
				}
				else
				{
				 $object->Length +=0; 
				}
				
				$width=$_product->getResource()->getAttribute('width');
				if(!empty($width))
				{
		  		$object->Width +=(int)$width->getFrontend()->getValue($_product);
				}
				else
				{
				 $object->Width +=0;	
				}
				$height=$_product->getResource()->getAttribute('height');
				if(!empty($height))
				{
		  		$object->Height +=(int)$height->getFrontend()->getValue($_product);
				}
				else
				{
				 $object->Height +=0; 	
				}
		  		$object->ActualWeight +=(int)$item->getWeight();
				$SOAPrequest["oLineItems"]['FreightLineItem']=$object;
				$count++;
                
            }
          }
		  
		  //print_r($object);
		 // exit;
		  
		  $SOAPrequest["oRestriction"]="NA";
		  $SOAPrequest["strDestinationCountry"]=$_iso3countrycode;
		  $SOAPrequest["strDestinationPostal"]=$OriginPostal;
		  $SOAPrequest["strOriginationCountry"]=$OriginCountry;
		  $SOAPrequest["strOriginationPostal"]=$postcode;
		  $SOAPrequest["lngInsuranceAmount"]=$InsuranceAmount;
		  $SOAPrequest["bAddHandlingFees"]=$AddHandlingFees;
		  $SOAPrequest["lngPctOfHandlingFees"]=$HandlingFeePercent;
		 

		  $result1 = $client->EstimateFreight($SOAPrequest);
		  $method = Mage::getModel('shipping/rate_result_method');
		  $methods=get_class_methods($method);
		 
		 //echo "<pre/>";
		  //print_r($SOAPrequest); 
		  //exit;
		//echo $result1->EstimateFreightResult->WSMessage;exit;
		  try
		  {
		   $rateresult=$result1->EstimateFreightResult->EstimateFreight->RateReplyDetails;
		  }
		  catch(Exception $e)
		  {
			  Mage::getSingleton('core/session')->addError($result1->EstimateFreightResult->WSMessage); 
		  }
		  
		  if(!empty($rateresult))
		  {
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
				
				if(in_array($rate->ServiceType,$allowedMethodsArr))
				{
				 $method = Mage::getModel('shipping/rate_result_method');
				  $method->setCarrier($this->_code);
				  $method->setCarrierTitle($this->_code);
				  $method->setMethod($rate->ServiceType);
				  $method->setMethodTitle($rate->ServiceType);
				  $method->setCost($rate->TotalCharge->Amount);
				  $method->setPrice($rate->TotalCharge->Amount);
				  $result->append($method);
				}
			  }
		  }	  
		  
		  }
 
		  
		  }
		  catch(Exception $e)
		  {
		  echo $e->getMessage();
		  exit;
		  }
	
	   
	    // skip if not enabled
     //   if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active'))
       //     return false;
 
     
 
       
        return $result; 
		
    }
	
	 public function getAllowedMethods()
    {
        return array('customshiping'=>$this->getConfigData('name'));
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
 