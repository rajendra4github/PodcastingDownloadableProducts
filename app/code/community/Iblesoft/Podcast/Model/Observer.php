<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable product type model
 *
 * @category    Mage
 * @package     Fono_PodCast
 * @author      Rajendra <rajendra.korukonda@iblesoft.com>
 */
class Iblesoft_Podcast_Model_Observer extends Varien_Event_Observer
{
   public function createItune($observer)
   {
		$object = $observer->getEvent()->getObject();
		$shortdescription = "";
		$baseuri = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		if (($object instanceof Mage_Downloadable_Model_Link)) {
		
		$link =	$object->getData();
		//echo "<pre>";print_r($link);exit;
		$product = Mage::getModel('catalog/product')->load($link['product_id']);
		$productname = $product->getName();
		$shortdescription = $product->getShortDescription();
		$imageurl = $product->getImageUrl();
		
		if($link['link_type'] == "file")
		{
		  $link_file = $link['link_file'];
		  $dirarray = explode("/",$link_file);
		  $file_name = basename($link_file);
		  $trackurl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."downloadable/files/links/".$link_file;
		}
		if($link['link_type'] == "url")
		{
		  	
		  $link_url = $link['link_url'];
		  //echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."<br />";
		  $link_url = str_replace(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA),'',$link_url);
		  $link_url = str_replace($baseuri."media/",'',$link_url);
		  //$link_url = str_replace($baseuri."https://www.fonolibro.com/media/",'',$link_url);
		  
		  //echo "asdf".$link_url."<br />";
		  $dirarray = explode("/",$link_url);
		  $file_name = basename($link_url);
		  $trackurl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$link_url;
		}
		//echo $trackurl;exit;
		$appRoot= Mage::getRoot();
        $root   = dirname($appRoot);
		
		
		$iTunesurl = $root."/media/downloadable/itunes/";
		$c = 1;
		foreach($dirarray as $dirname)
		{
			
		  if($dirname!="")
		   {
			    //echo $iTunesurl.$dirname."<br />";
				
				if(!file_exists($iTunesurl.$dirname) && $c!=sizeof($dirarray))
				{
				  mkdir($iTunesurl.$dirname,0777);	
				}
				if($c!=sizeof($dirarray))
				$iTunesurl .= $dirname."/";
		   } 
		   $c++;	
		}
		//echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."feed.xml";
		$xml = file_get_contents(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."feed.xml");
		
		$xml = str_replace("{title}",$link['title'],$xml);
		$xml = str_replace("{link}",$link['title'],$xml);
		$xml = str_replace("{track_title}",$link['title'],$xml);
		$xml = str_replace("{itune_image}","Test Image",$xml);
		$xml = str_replace("{subtitle}",$productname,$xml);
		$xml = str_replace("{build_date}",date('d M Y'),$xml);
		$xml = str_replace("{podcast_summary}",$shortdescription,$xml);
		$xml = str_replace("{track_link}",Mage::getBaseUrl()."?name=".$file_name,$xml);
		$xml = str_replace("{track_url}",$trackurl,$xml);
		$xml = str_replace("{guid}",Mage::getBaseUrl()."?name=".$file_name,$xml);
		$xml = str_replace("{duration}","",$xml);
		$xml = str_replace("{podcast_title}",$link['chapter_name'],$xml);
		$xml = str_replace("{chapter}",$link['chapter_name'],$xml);
		$xml = str_replace("{product_description}",$shortdescription,$xml);
		$xml = str_replace("{publishdate}",date('d M Y'),$xml);
		
		 //echo "<pre>";print_r($link);
		 //echo "asdfds".$xml;exit;
		 
		 $xmlPath = $iTunesurl.str_replace(".mp3",".xml",$file_name);
		 $xmlPath = str_replace(".ogg",".xml",$xmlPath);
		 
		 //echo $xmlPath."<br />";
		 
		 //$xmlPath = Mage::getBaseDir().DS.'test.xml';
		 $xmlObj = new Varien_Simplexml_Config($xmlPath);
		 file_put_contents($xmlPath,$xml);

/*			if ($object->getLinkType()) {
				Mage::helper('solvingmagento_downloadablesize')
					->setFileSize($object, 'link_type', 'link_file', 'link_url', 'filesize');
			}
*/	 
		}
    return;
 }
 public function createItuneAlbum($observer)
 {
    
 	$product = $observer->getProduct();
    $baseuri = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
    
    $dir = Mage::getBaseDir('media')."/downloadable/itunes/products/".$product->getId()."/";
    
    if(!file_exists($dir))
	  mkdir($dir,0777);
	
	header('Content-type: text/xml');
	//CONSTRUCT RSS FEED HEADERS
	
    $xml = '<rss version="2.0">';
    $xml .= '<channel>';
    $xml .= '<title>'.$product->getName().'</title>';
    $xml .= '<description>'.$product->getShortDescription().'</description>';
    $xml .= '<link>'.Mage::getBaseUrl().'</link>';
    $xml .= '<copyright>Fonolibro</copyright>';
	
    if($product->getTypeId()=="downloadable")
	{
	  $links=Mage::getModel('downloadable/link')
                      ->getCollection()
                      ->addFieldToFilter('product_id',array('eq'=>$product->getId()))
					  ->addTitleToResult();
 	
	  foreach($links as $link)
	  {
		  if($link->getLinkType() == "file"){
				$flink = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'downloadable/files/links'.$link->getLinkFile();				  
			  }
		  else if($link->getLinkType() == "url"){
			  $flink = $link->getLinkUrl();
			  }
		 
		  
	      $xml .= '<item>';
		  $xml .= '<title>'.$link->getTitle().'</title>';
		  $xml .= '<description>'.$product->getShortDescription().'</description>';
		  $xml .= '<link>'.Mage::getBaseUrl().'</link>';
		  $xml .= '<enclosure url="'.$flink.'" length="6768" type="audio/mpeg"/>'; //URL: FULL FILE PATH   LENGTH: IN BYTES
		  $xml .= '<pubDate>'.date('M d Y').'</pubDate>';
		  $xml .= '</item> ';  
	  }
	  //echo "<pre>"; print_r($links->getData()); exit;
	}
	//echo "<pre>"; print_r($product->getData()); exit;
 
    //CLOSE RSS FEED
   $xml .= '</channel>';
   $xml .= '</rss>';

	file_put_contents($dir.$product->getId()."_feed.xml",$xml);
    //SEND COMPLETE RSS FEED TO BROWSER
	return;
 }
 
}
?>
