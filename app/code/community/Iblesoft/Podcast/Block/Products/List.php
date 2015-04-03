<?php 
class Iblesoft_Podcast_Block_Products_List extends Mage_Downloadable_Block_Customer_Products_List
{
  //Write you own functions
  public function _toHtml()
  {
    $this->setTemplate('podcast/products/list.phtml');
  
    return parent::_toHtml();
  } 
}