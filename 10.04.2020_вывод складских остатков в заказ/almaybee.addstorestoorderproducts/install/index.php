<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class almaybee_addstorestoorderproducts extends \CModule
{
//    const MODULE_ID = 'almaybee.addstorestoorderproducts';

    public function __construct(){
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");

        $this->MODULE_ID = 'almaybee.addstorestoorderproducts';
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("AL_MAYBEE_STORES_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("AL_MAYBEE_STORES_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("AL_MAYBEE_STORES_MODULE_PARTNER_NAME");
//        $this->PARTNER_URI = Loc::getMessage("CRM_GENESIS_SLOTS_PARTNER_URI");
    }

    public function InstallFiles($arParams = [])
    {
        $copyRes = CopyDirFiles($this->GetPatch() . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/local/components/", true, true);

//        self::logData($copyRes);

        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx("/local/components/bitrix/catalog.product.search");
        DeleteDirFilesEx("/local/components/bitrix/crm.order.product.list");
    }


    public function DoInstall(){
        global $APPLICATION;
        if($this->isVersionD7())
        {
            $this->InstallFiles();
            ModuleManager::registerModule($this->MODULE_ID);
        }
        else
            $APPLICATION->ThrowException(Loc::getMessage("AL_MAYBEE_STORES_MODULE_VERSION"));
    }

    public function DoUninstall(){
        ModuleManager::unRegisterModule($this->MODULE_ID);
        $this->UnInstallFiles();
    }


    //Из файла Main.php
    public function GetPatch($notDocumentRoot=false)
    {
        if($notDocumentRoot)
            return str_ireplace($_SERVER["DOCUMENT_ROOT"],'',dirname(__DIR__));
        else
            return dirname(__DIR__);
    }

    public function isVersionD7()
    {
        return CheckVersion(SM_VERSION, '14.00.00');
    }

    public function logData($data){
        $file = $_SERVER["DOCUMENT_ROOT"].'/123.log';
        file_put_contents($file, print_r([date('d.m.Y H:i:s'),$data],true), FILE_APPEND | LOCK_EX);
    }

}