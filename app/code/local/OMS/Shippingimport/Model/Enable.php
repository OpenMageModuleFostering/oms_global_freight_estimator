<?php
class OMS_Shippingimport_Model_Enable
{
    public function toOptionArray()
    {
      
	return	array(
            array('value' => 'yes', 'label'=>Mage::helper('adminhtml')->__('Yes')),
            array('value' => 'no', 'label'=>Mage::helper('adminhtml')->__('No'))
          
        );
    }
	
}
	