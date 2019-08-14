<?php
class OMS_Shippingimport_Model_Alternatemethods
{
    public function toOptionArray()
    {
      
	return	array(
            array('value' => 'tablerates', 'label'=>Mage::helper('adminhtml')->__('Use Failsafe Table Rates')),
            array('value' => 'flatrates', 'label'=>Mage::helper('adminhtml')->__('Use Failsafe Flat Rates'))
          
        );
    }
	
}
	