<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

\Bitrix\Main\Loader::includeModule('ittower.simpleprops');
use Bitrix\Main\Context,
    Ittower\Simpleprops\FormCreator,
    Ittower\Simpleprops\Common;

$request = Context::getCurrent()->getRequest();
$requestFieldsArray = $request->getPostList()->toArray();


if($requestFieldsArray["ib_id"]){
    $_SESSION["propsIbId"] = $requestFieldsArray["ib_id"];
}


require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
if($request->isPost()){
    global $APPLICATION;
    $APPLICATION->RestartBuffer();
}

$ibID = Common::$iblockId ?: $_SESSION["propsIbId"];

$form = new FormCreator($ibID);
$fd = $form->getFormData();

if(!empty($fd)){?>

<?$i=0;?>
<?foreach($fd as $k => $data):?>
    <div class="form-group">
        <div class="ibname"><?=++$i?>. <label> <b><?=$data["NAME"]?> (id <?=$k?>)</b></label></div>
        <label class="checkbox-inline">
            <input type="checkbox" <?=$data["DETAIL_PAGE_SHOW"] === 'Y'? 'checked':''?> name="detail-<?=$k?>" value="Y"> <?=GetMessage("ITTOWER_SIMPLEPROPS_SHOW_DETAIL")?>
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" <?=$data["LIST_PAGE_SHOW"] === 'Y'? 'checked':''?>  name="list-<?=$k?>" value="Y"> <?=GetMessage("ITTOWER_SIMPLEPROPS_SHOW_LIST")?>
        </label>
        <?if(array_key_exists("SMART_FILTER", $data)):?>
            <label class="checkbox-inline">
                <?$filterV = $data["SMART_FILTER"]?>
                <input type="checkbox"  name="filter-<?=$k?>" <?=$filterV === 'Y'?'checked': ''?> value="Y"> <?=GetMessage("ITTOWER_SIMPLEPROPS_SHOW_FILTER")?>
            </label>
        <?endif;?>
    </div>
<?endforeach;?>
<script>
    var propValues = []; //dinamic set values
    var issetCheckboxValues = $('#prop_data').serializeArray();
    $('#prop_data input').on('click', function(e){
        let pos;
        if($(e.target).prop('checked')){
            pos = propValues.indexOf($(e.target).attr('name')+'-N');
            if(pos !== -1){
                propValues.splice(pos, 1);
            }
            propValues.push($(e.target).attr('name')+'-Y');
        } else {
            pos = propValues.indexOf($(e.target).attr('name')+'-Y');
            if(pos !== -1){
                propValues.splice(pos, 1);
            }
            propValues.push($(e.target).attr('name')+'-N');
        }
    });
    $(document).ready(function(){
        $(".save_props").show();
    });
</script>

<?} else {?>
<div class="adm-info-message-wrap">
    <div class="adm-info-message">
        <?=GetMessage("ITTOWER_SIMPLEPROPS_NO_PROPERTY")?>
    </div>

</div>
<script>
    $(document).ready(function(){
        $(".save_props").hide();
    });
</script>
<?}?>
