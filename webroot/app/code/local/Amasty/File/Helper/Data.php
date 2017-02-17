<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_File
 */
class Amasty_File_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getUploadDir()
    {
         return Mage::getBaseDir('media') . DS . 'amfile' . DS ;
    }

    public function getUploadErrorMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $this->__($message);
    }

	public function getFtpImportDir()
	{
		$dir = Mage::getStoreConfig('amfile/import/ftp_dir');
		if(substr($dir,0,-1) != "/") {
			$dir .= "/";
		}
		//var_dump(substr($dir,0,1));
		if(substr($dir,0,1) != "/") {
			$dir = "/".$dir;
		}
		$dir = Mage::getBaseDir().$dir;

		return $dir;
	}

    public function getSetCustomerGroups()
    {
        $groups = Mage::getStoreConfig('amfile/block/customer_group');
        return $groups !== null ? explode(',',$groups) : array();
    }
}
