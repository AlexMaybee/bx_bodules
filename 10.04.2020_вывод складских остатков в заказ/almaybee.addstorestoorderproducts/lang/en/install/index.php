<?php
$MESS["AL_MAYBEE_STORES_MODULE_NAME"] = "CRM: Модуль отображения количества товаров на складах";
$MESS["AL_MAYBEE_STORES_MODULE_DESCRIPTION"] = "Добавляет на страницу обработки заказов количество товара на складах в просмотре заказа и при добавлении товара в заказ.";
$MESS["AL_MAYBEE_STORES_MODULE_PARTNER_NAME"] = "AlMeybee";
//$MESS["AL_MAYBEE_STORES_MODULE_PARTNER_URI"] = "https://crmgenesis.com/";
$MESS["AL_MAYBEE_STORES_MODULE_TITLE"] = "Установка модуля";
$MESS["AL_MAYBEE_STORES_MODULE_VERSION"] = "Версия главного модуля ниже 14. Не поддерживается технология D7, необходимая модулю. Пожалуйста обновите систему.";

/*

  public function InstallFiles($arParams = [])
    {
//        CopyDirFiles($this->GetPatch() . "/install/components/short", $_SERVER["DOCUMENT_ROOT"] . "/local/templates/.default/components", true, true);
        CopyDirFiles($this->GetPatch() . "/install/components/bitrix", $_SERVER["DOCUMENT_ROOT"] . "/local/components/bitrix", true, true);
        return true;
    }

    public function UnInstallFiles()
    {
//        DeleteDirFilesEx("/local/templates/.default/components/bitrix/catalog.product.search");
//        DeleteDirFilesEx("/local/templates/.default/components/bitrix/crm.order.product.list");
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
 */