<?php

namespace Almaybee\Sentnotificationafterdayreportadd;

use \Bitrix\Main\Localization\Loc,
    \Almaybee\Sentnotificationafterdayreportadd\Subfunction;

\Bitrix\Main\Loader::includeModule("timeman");
\Bitrix\Main\Loader::includeModule("socialnetwork");
\Bitrix\Main\Loader::includeModule("im");

class Event
{
    const MODULE_ID = 'almaybee.sentnotificationafterdayreportadd';

    public function catchDailyReportAdd(&$arFields)
    {
        if($arFields)
        {
            $slavesGroupId = Subfunction::getOptionValue(self::MODULE_ID,'AL_MAYBEE_SLAVES_REPORTS_GROUP');
            $ownersGroupId = Subfunction::getOptionValue(self::MODULE_ID,'AL_MAYBEE_OWNERS_REPORTS_GROUP');

            $reportArr = Subfunction::getReportDataById($arFields);

            //Запуск уведомлдений только если обе группы выбраны в options.php!
            if($reportArr && $slavesGroupId && $ownersGroupId)
            {
                //если чувак в группе Рабов
                $slavesGroupArr = Subfunction::getUsersFromGroup($slavesGroupId);
                if($slavesGroupArr && in_array($reportArr['USER_ID'],$slavesGroupArr))
                {
                    //получаем массив Хозяев
                    $ownersGroupArr = Subfunction::getUsersFromGroup($ownersGroupId);
                    if($ownersGroupArr)
                    {
                        //для каждого хозяина формируем сообщение
                        foreach ($ownersGroupArr as $owner) {

                            $slaveGenderArr = Subfunction::getUserDataById($reportArr['USER_ID']);

                            switch ($slaveGenderArr["PERSONAL_GENDER"])
                            {
                                case "M":
                                    $gender_suffix = "_M";
                                    break;
                                case "F":
                                    $gender_suffix = "_F";
                                    break;
                                default:
                                    $gender_suffix = "";
                            }

                            $arEntry["DATE_TEXT"] = FormatDate("j F", MakeTimeStamp($reportArr["TIMESTAMP_X"], FORMAT_DATETIME));

                            $reports_page = \COption::GetOptionString("timeman", "TIMEMAN_REPORT_PATH", "/timeman/timeman.php");
                            $arTmp = \CSocNetLogTools::ProcessPath(array("REPORTS_PAGE" => $reports_page), $owner);

                            $messageArr = [
                                'TO_USER_ID' => $owner,
                                "MESSAGE_TYPE" => IM_MESSAGE_SYSTEM,
                                "FROM_USER_ID" => $reportArr["USER_ID"],
                                "NOTIFY_TYPE" => IM_NOTIFY_FROM,
//                                "NOTIFY_MODULE" => "timeman",
                                "NOTIFY_MODULE" => self::MODULE_ID,
                                "NOTIFY_EVENT" => "entry",
//                                "LOG_ID" => $arEntry["LOG_ID"],
                                "NOTIFY_TAG" => "TIMEMAN|ENTRY|" . $reportArr["ENTRY_ID"],
                            ];

                            $messageArr["NOTIFY_MESSAGE"] = GetMessage("AL_MAYBEE_SLAVES_REPORTS_CONTROL_REPORT_FULL_IM_ADD".$gender_suffix, Array(
                                "#period#" => "<a href=\"".$arTmp["URLS"]["REPORTS_PAGE"]."\" class=\"bx-notifier-item-action\">".htmlspecialcharsbx($arEntry["DATE_TEXT"])."</a>",
                            ));


                            $messageArr["NOTIFY_MESSAGE_OUT"] = GetMessage("AL_MAYBEE_SLAVES_REPORTS_CONTROL_REPORT_FULL_IM_ADD".$gender_suffix, Array(
                                    "#period#" => htmlspecialcharsbx($arEntry["DATE_TEXT"]),
                                ))." (".$arTmp["SERVER_NAME"].$arTmp["URLS"]["REPORTS_PAGE"].")";


                            \CIMNotify::Add($messageArr);

                            $hh[] = $messageArr;
                        }

                        $hh[] = ['slaves' => $slavesGroupId,'owners' => $ownersGroupId];

                    }
                }
            }
        }

        Subfunction::logData($hh);
    }
    
    
}
