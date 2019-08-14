<?php
class Discretelogix_Shippingimport_Model_Methodslist
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
		/*array(
            array('value' => 'Courier', 'label'=>Mage::helper('adminhtml')->__('Courier'),'selected'=>true),
            array('value' => 'Courier-Bold', 'label'=>Mage::helper('adminhtml')->__('Courier-Bold')),
            array('value' => 'Courier-Oblique', 'label'=>Mage::helper('adminhtml')->__('Courier-Oblique')),
            array('value' => 'Courier-BoldOblique', 'label'=>Mage::helper('adminhtml')->__('Courier-BoldOblique'))
        );*/
    }
	
	public function getMethods()
	{
		 $collection=Mage::getModel("shippingimport/import")->getCollection();
		 $data=$collection->getData();
		 return $data;
		
	}
}