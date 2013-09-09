<?php

defined('C5_EXECUTE') or die('Access Denied.');

Loader::model('attribute/types/default/controller');

class RemoPhasherAttributeTypeController extends DefaultAttributeTypeController {

    protected $searchIndexFieldDefinition = 'X NULL';

    public function getDisplayValue() {
        $phasherInstance = PHasher::Instance();
        return $phasherInstance->HashAsTable($this->getValue(), REMO_PHASER_SIZE, REMO_PHASER_CELLSIZE);
    }

    public function getDisplaySanitizedValue() {
        return $this->getDisplayValue();
    }

}