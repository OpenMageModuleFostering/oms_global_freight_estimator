<?php
class OMS_Shippingimport_Model_Methodslist
{
    public function toOptionArray()
    {
        $data=$this->getMethods();
		$methods=array();
		foreach($data as $method)
		{
			$methods[]=array('value' => $method["name"], 'label'=>Mage::helper('adminhtml')->__($method["name"]));
			
		}
		return $methods; 
		
    }
	
	public function getMethods()
	{
		/* $collection=Mage::getModel("shippingimport/import")->getCollection();
		 $data=$collection->getData();
		 return $data;*/
		 
		 $model=Mage::getModel("shippingimport/import");
		 $data= $model->getAllMethods();
		 return $data;
		
	}
}