<?php
class OMS_Shippingimport_Model_Unit
{
    public function toOptionArray()
    {
      
	return	array(
            array('value' => 'US', 'label'=>Mage::helper('adminhtml')->__('US')),
            array('value' => 'Metrics', 'label'=>Mage::helper('adminhtml')->__('Metrics'))
          
        );
    }
	
}
	