<?php 


class Discretelogix_Shippingimport_Model_Import extends Mage_Core_Model_Abstract
{
    public function _construct()
	{
	   parent::_construct();
	   $this->_init("shippingimport/import");	
		
	}	
	
	
	public function checkMethod($name)
	{
	   $collection=Mage::getModel("shippingimport/import")->getCollection()->addFilter("name",array("eq"=>$name));
	   
	   $data=$collection->getData();
	   return $data;	
		
		
	}
	
}