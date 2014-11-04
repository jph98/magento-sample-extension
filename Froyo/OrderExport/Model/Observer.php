<?php
/**
 * Froyo_OrderExport Observer class
 */
class Froyo_OrderExport_Model_Observer
{
    
    /**
     * Exports an order after it is placed
     * 
     * @param Varien_Event_Observer $observer observer object 
     * 
     * @return boolean
     */
    public function exportOrder(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        
        Mage::getModel('froyo_orderexport/export')
            ->exportOrder($order);
        
        return true;
        
    }
}
