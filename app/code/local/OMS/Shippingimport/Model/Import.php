<?php 


class OMS_Shippingimport_Model_Import extends Mage_Core_Model_Abstract
{
    public function _construct()
	{
	   parent::_construct();
	   $this->_init("shippingimport/import");	
		
	}	
	
	
/*	public function checkMethod($name)
	{
	   $collection=Mage::getModel("shippingimport/import")->getCollection()->addFilter("name",array("eq"=>$name));
	   
	   $data=$collection->getData();
	   return $data;	
		
		
	}*/
	
	public function checkMethod($name)
	{
	     //$table=Mage::getSingleton('core/resource')->getTableName('shipping_imported_methods');	
	   $query="select * from shipping_imported_methods where name='".$name."'";
	   
	   $data=Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
	   return $data;
		
		
	}
	
	public function getAllMethods()
	{
	   //$table=Mage::getSingleton('core/resource')->getTableName('shipping_imported_methods');	
	   
	   $query="select * from shipping_imported_methods";
	   
	   $data=Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
	   return $data;
		
	}
	
	public function insertShipping($name)
	{
		$query="insert into shipping_imported_methods(name) values('".$name."')";
	   
	   Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);

	}
	
}