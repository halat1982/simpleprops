<?php
$aMenu = array(

'parent_menu' => 'global_menu_content',
'sort' => 150,
'text' => GetMessage('ITTOWER_SIMPLEPROPS_MENU_HEADER_TEXT'),
'title' => GetMessage('ITTOWER_SIMPLEPROPS_HEADER_TITLE'),
'icon' => 'sale_menu_icon_statisti',
'page_icon' => 'sale_menu_icon_statisti',
'items_id' => 'simpleibprops',
'items' => array(
array(
'text' => GetMessage('ITTOWER_SIMPLEPROPS_MENU_TEXT'),
'title' => GetMessage('ITTOWER_SIMPLEPROPS_MENU_TITLE'),
'url' => '/bitrix/admin/ittower.simpleprops_simpleibprops.php?lang='.LANGUAGE_ID,
),
)
);

return (!empty($aMenu) ? $aMenu : false);