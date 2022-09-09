<?php
namespace Ittower\Simpleprops;

class FormCreator
{

    protected $propertyHandlersObj;

    public function __construct($iblockID)
    {
        $this->propertyHandlersObj = new PropertyHandlers($iblockID);
    }

    public function getFormData()
    {
        return $this->propertyHandlersObj->getFormData();
    }


}