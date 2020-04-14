<?php

namespace Almaybee\Sentnotificationafterdayreportadd;

use \Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule("timeman");
//\Bitrix\Main\Loader::includeModule("socialnetwork");
//\Bitrix\Main\Loader::includeModule("im");

class Subfunction
{
    public function getReportDataById($reprotId)
    {
        $result = [];
        $resObj = \CTimeManReport::GetList(["ID"=>"DESC"],['ID'=> $reprotId]);
        if($ob = $resObj->fetch())
            $result = $ob;
        return $result;
    }

    public function getUsersFromGroup($groupID)
    {
        $result = [];
        $groupUsers = \Bitrix\Main\UserGroupTable::getList([
            'filter' => ['GROUP_ID' => $groupID,'USER.ACTIVE' => 'Y'],
            'select' => ['USER_ID','NAME' => 'USER.NAME','LAST_NAME' => 'USER.LAST_NAME'], // выбираем идентификатор п-ля, имя и фамилию
            'order' => ['USER.ID' => 'DESC'], // сортируем по идентификатору пользователя
        ]);
        while($ob = $groupUsers->fetch()) $result[] = $ob['USER_ID'];
        return $result;
    }

    public function getUserDataById($userId)
    {
        $result = [];
        $slaveGenderArr = \Bitrix\Main\UserTable::getRow([
            'select' => ['PERSONAL_GENDER'],
            'filter' => ['ID' => $userId],
        ]);
        if($slaveGenderArr) $result = $slaveGenderArr;
        return $result;
    }

    public function logData($data){
        $file = $_SERVER["DOCUMENT_ROOT"].'/bbb.log';
        file_put_contents($file, print_r([date('d.m.Y H:i:s'),$data],true), FILE_APPEND | LOCK_EX);
    }

}
