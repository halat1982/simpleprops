<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

$module_id = 'ittower.simpleprops'; //обязательно, иначе права доступа не работают!

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight($module_id)<"S")
{
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

//\Bitrix\Main\Loader::includeModule($module_id);


$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

// description

$aTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS"),       
    )
);
// save options

if ($request->isPost() && $request['Update'] && check_bitrix_sessid())
{
    if(count($aTabs) > 0){
        foreach ($aTabs as $aTab)
        {

            foreach ($aTab['OPTIONS'] as $arOption)
            {
                if (!is_array($arOption))
                    continue;

                if ($arOption['note'])
                    continue;



                $optionName = $arOption[0];

                $optionValue = $request->getPost($optionName);

                Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue):$optionValue);
            }
        }
    }

}

//show tabs

$tabControl = new CAdminTabControl('tabControl', $aTabs);

?>
<? $tabControl->Begin(); ?>
<form method='post' action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request['mid'])?>&amp;lang=<?=$request['lang']?>' name='local_lib_settings'>

    <? foreach ($aTabs as $aTab):
            if($aTab['OPTIONS']):?>
        <? $tabControl->BeginNextTab(); ?>
        <? __AdmSettingsDrawList($module_id, $aTab['OPTIONS']); ?>

    <?      endif;
        endforeach; ?>

    <?
    $tabControl->BeginNextTab();
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");

    $tabControl->Buttons(); ?>

    <input type="submit" name="Update" value="<?echo GetMessage('MAIN_SAVE')?>">
    <input type="reset" name="reset" value="<?echo GetMessage('MAIN_RESET')?>">
    <?=bitrix_sessid_post();?>
</form>
<? $tabControl->End(); ?>

 
