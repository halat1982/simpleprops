<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use \Bitrix\Main\Page\Asset,
    \Bitrix\Main\Loader,
    Ittower\Simpleprops\Common,
    Ittower\Simpleprops\PropertyHandlers;


Loader::includeModule('ittower.simpleprops');

CJSCore::Init(array("jquery"));



if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

IncludeModuleLangFile(__FILE__);

$APPLICATION->SetTitle(GetMessage("ITTOWER_SIMPLEPROPS_TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$usePropertyFeatures = PropertyHandlers::isEnabledFeatures(); //use prop params in comp and forms

$arIBlocks = Common::getIblocks();

if(isset($_SESSION["propsIbId"]) && $_SESSION["propsIbId"] > 0){
    Common::$iblockId = $_SESSION["propsIbId"];
} else {
    Common::$iblockId = array_key_first($arIBlocks);
}
?>
<?if(!$usePropertyFeatures):?>
    <div class="adm-info-message-wrap" style="position: relative; top: -15px;">
        <div class="adm-info-message">
            <?=GetMessage("ITTOWER_SIMPLEPROPS_DESCRIPTION")?>
        </div>
    </div>
<?endif;?>
    <main class="main">
        <div class="pre_catalog item_cont catalog_cont _cont">
            <div class="wrapper">
                <div class="main_cont">
                    <form name="iblock" >
                        <div class="form-group">
                            <label for="exampleFormControlSelect1"><?=GetMessage("ITTOWER_SIMPLEPROPS_CHOOSE_IB")?></label>
                            <select name="iblock_id" class="form-control" id="iblock_id">
                                <?foreach($arIBlocks as $ibId => $ibVal):?>
                                    <option <?=Common::$iblockId == $ibId ? 'selected':''?> value="<?=$ibId?>"><?=$ibVal?></option>
                                <?endforeach;?>
                            </select>
                        </div>

                    </form>
                    <hr>

                    <div id="main_content">
                        <form name="prop_data" id="prop_data">
                            <input type="submit" class="save_props adm-btn-save" name="save" style="margin-bottom: 10px;" value="<?=GetMessage("ITTOWER_SIMPLEPROPS_SAVE_BTN")?>">
                            <br>
                            <div id="props_list">
                                <?
                                require_once( "getform.php" );?>
                            </div>
                            <br>
                            <input type="submit" class="save_props adm-btn-save" name="save" value="<?=GetMessage("ITTOWER_SIMPLEPROPS_SAVE_BTN")?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <style>
        input[type="checkbox"] {
            margin-top: 0px;
        }

        .form-group {
            margin-top:3px;
        }

        .ibname {
            margin-bottom:3px;
        }
    </style>

    <script>
        $('#iblock_id').on('change', function(e){
            let ibID = $(e.target).val();

            $.ajax({
                type: 'post',
                url: 'ittower.simpleprops_getform.php',
                data: { ib_id: ibID},
                success: function(html) {
                    $('#props_list').html(html);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        });
        $('.save_props').on('click', function(e){
            e.preventDefault();
            let ibID = $('#iblock_id').val();
            BX.showWait(this, 'Сохранение');
            $.ajax({
                type: 'post',
                url: 'ittower.simpleprops_setproperties.php',
                data: { ib_id: ibID, properties: propValues, issetValues: issetCheckboxValues},
                success: function(html) {
                    window.location.reload();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert("!!!ERROR!!!");
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });
        });
    </script>

<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>