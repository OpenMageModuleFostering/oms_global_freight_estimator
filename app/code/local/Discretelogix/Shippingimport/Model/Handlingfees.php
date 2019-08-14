<?php
class Discretelogix_Shippingimport_Model_Handlingfees
{
    public function toOptionArray()
    {
      
	return	array(
            array('value' => 'true', 'label'=>Mage::helper('adminhtml')->__('True')),
            array('value' => 'false', 'label'=>Mage::helper('adminhtml')->__('False'))
          
        );
    }
	
}
	