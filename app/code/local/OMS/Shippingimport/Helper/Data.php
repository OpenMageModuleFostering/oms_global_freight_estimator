<?php 

class OMS_Shippingimport_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function sendEmail($data)
	{
		
		 
		 $email_arr = array();
		 $email_arr['host'] = 
		 Mage::getStoreConfig('webservice/email_settings/hostname',Mage::app()->getStore());
		 
		 $email_arr['username'] = 
		 Mage::getStoreConfig('webservice/email_settings/username',Mage::app()->getStore());
		 
		 $email_arr['password'] = 
		 Mage::getStoreConfig('webservice/email_settings/password',Mage::app()->getStore());
		 
		 $email_arr['port'] = 
		 Mage::getStoreConfig('webservice/email_settings/port',Mage::app()->getStore());
		 
		 $senders =array(); 
		 $send_to= Mage::getStoreConfig('webservice/email_settings/send_to',Mage::app()->getStore());
		 
		 $senders=explode(',',$send_to);
		
		  $email_arr['store_name'] = 
		 Mage::getStoreConfig('webservice/email_settings/from_name',Mage::app()->getStore());
		 
		  $email_arr['store_email'] = 
		 Mage::getStoreConfig('webservice/email_settings/from_email',Mage::app()->getStore());
		
		
		 
		 /*Send email*/
		 
		
		

		
		  $settings = array(
                    'port' => $email_arr['port'],
                    'auth' => 'login',
                    'username' => $email_arr['username'],
                    'password' =>  $email_arr['password']
                );
		
              
		$transport = new Zend_Mail_Transport_Smtp($email_arr['host'], $settings);
                $email_from =  $email_arr['store_email'];
                $name_from =   $email_arr['store_name'];
                $email_to = "muhammad.mubashir@OMS.com";
                $name_to = "Mubashir Qayyum";
 		$Body = '<p>Dear OMS Support Team,</p><p>We have recieved a new exception:</p><p>Exception Details:<br/><b>'.$data['exception'].'<b></p>';
                $mail = new Zend_Mail ();
		
                $mail->setReplyTo($email_from, $name_from);
                $mail->setFrom ($email_from, $name_from);
				foreach($senders as $send)
				{
                $mail->addTo ($send, '');
				}
                $mail->setSubject ('OMS Global Freight Estimator -'.$email_arr['storename']."- Error");
                $mail->setBodyHtml($Body);
		
                $mail->send($transport);
		

	}
	
	
}
	