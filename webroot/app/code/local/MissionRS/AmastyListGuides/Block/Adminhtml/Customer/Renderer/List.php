<?php
 
class MissionRS_AmastyListGuides_Block_Adminhtml_Customer_Renderer_List extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function _getValue(Varien_Object $row)
    {  
        $lists = Mage::registry('cls_all_lists');
        
         $data = parent::_getValue($row);
         
         $data = explode(',', $data);
         
         $titles = array();         
         foreach($data as $list) {             
             $item = $lists->getItemById($list);             
             if($item) {
                 $titles[] = $item->getTitle();
             }
         } 
         return implode(',', $titles);
    }
}