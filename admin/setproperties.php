<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
\Bitrix\Main\Loader::includeModule('ittower.simpleprops');

use Bitrix\Main\Context,
    Ittower\Simpleprops\Common,
    Ittower\Simpleprops\PropertyHandlers;

$requestFieldsArray = Context::getCurrent()->getRequest()->getPostList()->toArray();


$requestProps = $requestFieldsArray["properties"]?:Array();
$oldProps = $requestFieldsArray["issetValues"]?:Array();

$propsData = Common::prepareArrayProperties($requestProps, $oldProps);

$iblockID = intval($requestFieldsArray["ib_id"]);

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

if($iblockID > 0 && !is_array($requestFieldsArray["ib_id"])){ // non-empty arrays return 1 from intval

    $propHandlers = new PropertyHandlers($iblockID);
    if(!empty($propsData)){
        $propHandlers->setIBlockProperties($propsData);
    }
}
?>