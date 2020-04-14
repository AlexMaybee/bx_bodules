<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option,
    Bitrix\Iblock\ElementTable,
    Bitrix\Main\GroupTablel;

$moduleId = basename( __DIR__ );
$moduleLangPrefix = strtoupper( str_replace( ".", "_", $moduleId ) );
$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
Loc ::loadMessages( __FILE__ );

if ( $APPLICATION -> GetGroupRight( $moduleId ) < "R" )
{
    $APPLICATION -> AuthForm( Loc ::getMessage( "ACCESS_DENIED" ) );
}

Loader ::includeModule( $moduleId );

//список групп
$b24UserGroupList = \Almaybee\Sentnotificationafterdayreportadd\Subfunction::getGroupsArrForOption();
//список групп


$aTabs = [
        [
        'DIV' => 'al-maybee',
        'TAB' => Loc::getMessage('AL_MAYBEE_SLAVES_REPORTS_CONTROL_OPTION_TAB_INFO'),
        'TITLE' => Loc::getMessage("AL_MAYBEE_SLAVES_REPORTS_CONTROL_OPTION_TAB_DESCRIPRION"),
        'OPTIONS' => [
            Loc::getMessage('AL_MAYBEE_SLAVES_REPORTS_CONTROL_OPTION_TAB_TITLE'),
            [
                'AL_MAYBEE_SLAVES_REPORTS_GROUP',
                Loc::getMessage( 'AL_MAYBEE_SLAVES_REPORTS_CONTROL_OPTION_SLAVES_GROUP' ),
                '',
                ['selectbox', $b24UserGroupList]
            ],
            [
                'AL_MAYBEE_OWNERS_REPORTS_GROUP',
                Loc::getMessage( 'AL_MAYBEE_SLAVES_REPORTS_CONTROL_OPTION_OWNERS_GROUP' ),
                '',
                ['selectbox', $b24UserGroupList]
            ],
        ],
    ],
];


if ( $request -> isPost() && check_bitrix_sessid() )
{
    if ( strlen( $request[ 'save' ] ) > 0 )
    {
        foreach ( $aTabs as $arTab )
        {
            if($arTab["TYPE"] != 'rights')
                __AdmSettingsSaveOptions( $moduleId, $arTab['OPTIONS']);
        }
    }
}
$tabControl = new CAdminTabControl( 'tabControl', $aTabs );
$realModuleId = $moduleId;
?>
<form method='post' action='<? echo $APPLICATION -> GetCurPage() ?>?mid=<?= $moduleId ?>&amp;lang=<?= $request[ 'lang' ] ?>'
      name='<?= $moduleId ?>_settings'>
    <? $tabControl -> Begin(); ?>
    <?
    foreach ( $aTabs as $aTab ):
        $tabControl -> BeginNextTab();
        ?>
        <?
        if ( $aTab[ 'OPTIONS' ] ):
            __AdmSettingsDrawList( $moduleId, $aTab[ 'OPTIONS' ] );
        elseif( $aTab["TYPE"] == 'rights' ):
            $table_id = $moduleId ."_". strtolower( $aTab["POSTFIX"] );
            require( __DIR__ . "/table_rights.php" );
            $moduleId = $realModuleId;
        endif;?>

    <?endforeach;
    ?>
    <?= bitrix_sessid_post();
    $tabControl -> Buttons( array( 'btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false, "btnSave" => true ) );
    ?>
    <? $tabControl -> End(); ?>

    <?//need for tab_rights. If in $_REQUEST hasn't Update -> rights do not save?>
    <input type="hidden" name="Update" value="Y" />

</form>