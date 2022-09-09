<?php

namespace Ittower\Simpleprops;

class Common
{
    public static $iblockId = "";

    public static function getIblocks(): Array
    {
        $arIBlocks=array();
        $db_iblock = \CIBlock::GetList(array("SORT"=>"ASC")); //, array("TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:""))
        while($arRes = $db_iblock->Fetch()){
            $arIBlocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];
        }
        return $arIBlocks;
    }

    public static function getAllProperties($iblockID): Array
    {
        $props = Array();
        $properties = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>intval($iblockID)));
        while ($prop_fields = $properties->GetNext())
        {
            $props[$prop_fields["ID"]]["NAME"] = $prop_fields["NAME"];
            $props[$prop_fields["ID"]]["ID"] = $prop_fields["ID"];
            $props[$prop_fields["ID"]]["PROPERTY_TYPE"] = $prop_fields["PROPERTY_TYPE"];
            $props[$prop_fields["ID"]]["USER_TYPE"] = $prop_fields["USER_TYPE"];

        }
        return $props;
    }

    public static function getStringToIN(array $ids) : string
    {
        $str = "(";
        $str .= implode(",", $ids);
        $str .= ")";

        return $str;
    }


    public static function prepareArrayProperties( array $requestData, array $oldProps): array
    {
        $propsValues = Array();
        foreach ($requestData as $prop){
            $row = explode('-', $prop);
            if($row[0] == 'detail' || $row[0] == 'list'){
                $propsValues[$row[1]]['feature'][] = Array(
                    "MODULE_ID"=>"iblock",
                    "IS_ENABLED"=>$row[2], //Y
                    "FEATURE_ID" => $row[0] == 'detail' ? "DETAIL_PAGE_SHOW" : "LIST_PAGE_SHOW"
                );
            } else {
                $propsValues[$row[1]][$row[0]] = $row[2];
            }

        }

        foreach($oldProps as $prop){
            $row = explode('-', $prop["name"]);
            if($row[0] == 'detail'){
                $oldValues[$row[1]]["DETAIL_PAGE_SHOW"] = $prop["value"];
            } else if($row[0] == 'list'){
                $oldValues[$row[1]]["LIST_PAGE_SHOW"] = $prop["value"];
            }
        }

        //compose 2 arrays in 1
        foreach($oldValues as $propID => $arValues)
        {
            if(isset($propsValues[$propID]['feature']) && is_array($propsValues[$propID]['feature'])){
                foreach($propsValues[$propID]['feature'] as $prop){
                    $existFeatureID = $prop["FEATURE_ID"];
                    if($arValues[$prop["FEATURE_ID"]]){
                        continue;
                    } else if(count($propsValues[$propID]['feature']) < 2) {
                        array_push($propsValues[$propID]['feature'], Array(
                            "MODULE_ID"=> "iblock",
                            "IS_ENABLED"=> "Y",
                            "FEATURE_ID"=>  $existFeatureID == "LIST_PAGE_SHOW" ? 'DETAIL_PAGE_SHOW':'LIST_PAGE_SHOW'
                        ));
                    }
                }
            }
        }
        return $propsValues;
    }

}