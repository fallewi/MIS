<?php
/* Russell Mantilla - rmantilla@levementum.com - 11/12/13
* column renderer to get product thumbnail
*/

class Levementum_AdminOrders_Block_Adminhtml_Sales_Order_Create_Search_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {
        if($row->getThumbnail()){
            if($row->getThumbnail() != 'no_selection'){
                $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$row->getThumbnail();
            } else{
                $imageUrl = Mage::getModel('catalog/product')->getSmallImageUrl(70);
            }
            $imageHtml = '<img src="'.$imageUrl.'" class="" width="70" />';

            return $imageHtml;

        }
            return false;
    }
}

?>