<?php
/**
 * Froyo_OrderExport Export class
 */
class Froyo_OrderExport_Model_Export
{
    
    /**
     * Generates an XML file from the order data and places it into
     * the var/export directory
     * 
     * @param Mage_Sales_Model_Order $order order object
     * 
     * @return boolean
     */
    public function exportOrder($order) 
    {
        $dirPath = Mage::getBaseDir('var') . DS . 'export';
        
        //if the export directory does not exist, create it
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        
        $data = $order->getData();
        $xml = new SimpleXMLElement('<root/>');
        $callback =
            function ($value, $key) use (&$xml, &$callback) {
                if ($value instanceof Varien_Object && is_array($value->getData())) {
                    $value = $value->getData();
                }
                if (is_array($value)) {
                    array_walk_recursive($value, $callback);
                }
                $xml->addChild($key, serialize($value));
            };

        array_walk_recursive($data, $callback);
        
        file_put_contents(
            $dirPath. DS .$order->getIncrementId().'.xml', 
            $xml->asXML()
        );

	// Send request to request bin - http://requestb.in/xnaknexn

	// Create map with request parameters
	$endpoint = Mage::getStoreConfig('orderexport_options/froyo_group/froyo_input');
	$storename = Mage::app()->getStore()->getName();
	$params = array ('endpoint' => $endpoint, 'storename' => $storename, 'order' => $xml->asXML());
 
	// Build Http query using params
	$query = http_build_query ($params);
 
	// Create Http context details
	$contextData = array ( 
                'method' => 'POST',
                'header' => "Connection: close\r\n".
                            "Content-Length: ".strlen($query)."\r\n",
                'content'=> $query );
 
	// Create context resource for our request
	$context = stream_context_create (array ( 'http' => $contextData ));
 
	// Read page rendered as result of your POST request
	$result =  file_get_contents (
                  $endpoint,  // page url
                  false,
                  $context);
        
        return true;
    }
}
