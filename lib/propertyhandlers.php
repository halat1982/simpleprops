<?php
namespace Ittower\Simpleprops;

class PropertyHandlers extends \Bitrix\Iblock\Model\PropertyFeature
{

    protected $ids = Array();
    protected $props = Array();
    protected $formData = Array();


    public function __construct($iblockID){
        $this->props = Common::getAllProperties($iblockID);
        $this->formData = $this->props;
        $this->setPropertiesIDs($this->props);
    }

    public function setIBlockProperties(Array $propsValues)
    {
        foreach($propsValues as $propID => $values){
            foreach($values as $type => $value){
                switch ($type) {
                    case 'feature':
                        \Bitrix\Iblock\Model\PropertyFeature::setFeatures(
                            $propID, $value
                        );
                        break;
                    case 'filter':
                        $this->setFilterValues($propID, $value);
                        break;
                }
            }
        }
    }

    protected function setFilterValuesToFormResult(Array $ids)
    {
        $fValues = $this->getSmartFilterValues($ids);

        if(!empty($this->formData) && !empty($fValues)){
            foreach($fValues as $k => $value){
                /* if($this->formData["PROPERTY_TYPE"] == "S" && $this->formData["USER_TYPE"] == "HTML")
                     continue;*/
                if($this->formData[$k]["PROPERTY_TYPE"] == "F")
                    continue;
                $this->formData[$k]["SMART_FILTER"] = $value;
            }
        }
    }

    protected function setFeatureValuesToFormResult(Array $ids)
    {
        $featureV = $this->getFeatureValues($ids);
        if(!empty($featureV)){
            foreach($featureV as $val){
                $this->formData[$val["PROPERTY_ID"]][$val["FEATURE_ID"]] = $val["IS_ENABLED"];
            }
        }
    }

    protected function getFeatureValues(Array $ids): Array
    {
        $features = Array();
        $iterator = \Bitrix\Iblock\PropertyFeatureTable::getList([
            'select' => ['*'],
            'filter' => ['=PROPERTY_ID' => $ids]
        ]);
        while ($row = $iterator->fetch())
        {
            $features[$row['ID']] = $row;
        }
        unset($iterator);

        return $features;

    }

    protected function getSmartFilterValues(Array $ids): Array
    {
        global $DB;

        $arrSP = Array();
        $in = Common::getStringToIN($ids);
        $SECTION_ID = intval(0);
        if($in !== '()'){
            $rs = $DB->Query("
                        SELECT *
                        FROM b_iblock_section_property
                        WHERE SECTION_ID = ".$SECTION_ID." AND PROPERTY_ID IN ".$in." 
                    ");

            while($sectionProperty = $rs->Fetch()){
                $arrSP[$sectionProperty["PROPERTY_ID"]] = $sectionProperty["SMART_FILTER"];
            }
        }

        return $arrSP;
    }

    protected function setPropertiesIDs(array $properties)
    {
        foreach ($properties as $id => $prop){
            $this->ids[$prop["ID"]] = $prop["ID"];
        }
    }

    protected function setFilterValues($propertyID, $value = "Y", $sectionID = 0)
    {
        \CIBlockSectionPropertyLink::Set($sectionID, $propertyID, $arLink = Array(
            'SMART_FILTER'  => $value
        ));
    }

    protected function setShowDetailPagePropertyValue($propertyID, $isEnabled)
    {
        \Bitrix\Iblock\Model\PropertyFeature::setFeatures(
            $propertyID,[[
                "MODULE_ID"=>"iblock",
                "IS_ENABLED"=>$isEnabled, //Y
                "FEATURE_ID" => "DETAIL_PAGE_SHOW"
            ]]
        );
    }

    protected function setShowInListPropertyValue($propertyID, $isEnabled)
    {
        //var_dump($propertyID);
        //var_dump($isEnabled);
        \Bitrix\Iblock\Model\PropertyFeature::setFeatures(
            $propertyID,[[
                "MODULE_ID"=>"iblock",
                "IS_ENABLED"=>$isEnabled,
                "FEATURE_ID" => "LIST_PAGE_SHOW"
            ]]
        );
    }

    public function getFormData()
    {
        $this->setFilterValuesToFormResult($this->ids);
        $this->setFeatureValuesToFormResult($this->ids);

        return $this->formData;
    }
}