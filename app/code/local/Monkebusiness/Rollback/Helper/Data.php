<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 12/3/13
 * Time: 9:09 PM
 */ 
class Monkebusiness_Rollback_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Convert string with underscores to camel case
     * @param $str
     * @param bool $capitalise_first_char
     * @return mixed
     */
    public function toCamelCase($str, $capitalise_first_char = true) {
        if($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }


    public function getStockAttrOption($code,$value)
    {
        $type = '';
        if(stristr($code,'use_config')){
            $type = 'yesno';
        }

        if (in_array($code, array('is_qty_decimal','backorders','manage_stock','enable_qty_increments','is_decimal_divided'))) {
            $type = 'yesno';
        }

        if($code == 'is_in_stock'){
            $options = Mage::getModel('cataloginventory/source_stock')->toOptionArray();
            foreach($options as $v){
                if($v['value'] == $value){
                    return $v['label'];
                }
            }
        }

        if($code == 'backorders'){
            $options = Mage::getModel('cataloginventory/source_backorders')->toOptionArray();
            foreach($options as $v){
                if($v['value'] == $value){
                    return $v['label'];
                }
            }
        }

        if ($type == 'yesno') {
            if ($value == 0) {
                return 'No';
            }
            return 'Yes';
        }

        return $value;
    }

    /**
     * Get admin user names
     *
     * @return array
     */
    public function getAdminUsers()
    {
        $users = Mage::getModel('admin/user')->getCollection();
        $apiUsers = Mage::getModel('api/user')->getCollection();
        $options = array();
        foreach ($users as $user) {
            $options[$user->getId() . '_0'] = $user->getFirstname() . ' ' . $user->getLastname();
        }

        if ($apiUsers->getSize() > 0) {
            foreach ($apiUsers as $apiUser) {
                $options[$apiUser->getId() . '_1'] = 'API User: ' . $apiUser->getFirstname() . ' ' . $apiUser->getLastname();
            }
        }

        return $options;
    }
}