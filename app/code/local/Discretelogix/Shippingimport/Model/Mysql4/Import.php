<?php 


class Discretelogix_Shippingimport_Model_Mysql4_Import extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
	{
	   $this->_init("shippingimport/import","id");	
		
	}	
	
}