<?php

namespace models\data;

abstract class model {

    public $id = '';
    //public static $type = '';
    public $structure = [];
    public $idParam = 'id';
    protected $store;

    public function __construct() {


        $r = new \ReflectionObject($this);
        $this->getExtensions($r);
        /*
          $type = trim(str_replace($r->getNamespaceName(), "", $r->getName()), "\\/");

          if(strpos($r->getNamespaceName(),'modules') > -1) {
          $ns = explode('\\',$r->getNamespaceName());
          $module = \array_pop($ns);
          $type = $module."_".$type;
          }

          self::$type = $type;
          // */

        //$this->getExtensions($r);
    }

    public function getType() {
        $cls = get_called_class();
        $parts = explode("\\", $cls);

        $type = array_pop($parts);

        if ($parts[1] == 'modules') {
            $type = $parts[2] . "_" . $type;
        }

        return $type;
    }

    /**
     * 
     * @param \services\data\store $store
     * @return $this
     */
    public function setStore(\services\data\store $store) {
        $this->store = $store;
        return $this;
    }

    /**
     * 
     * @return \services\data\store
     */
    public function getStore() {
        return $this->store;
    }

    public final function AddSubType($model, $required, $multiple, $fields = []) {

        $modelString = "\\" . __NAMESPACE__ . "\\" . $model;

        $oModel = new $modelString;

        $structure = $oModel->getStructure();

        if ($fields) {
            if (!is_array($fields)) {
                $fields = explode(",", $fields);
            }
            $incStructure = [];
            foreach ($fields as $field) {
                $incStructure[$field] = $structure[$field];
            }
            $structure = $incStructure;
        }

        $this->structure[$model] = [
            'required' => $required,
            'multiple' => $multiple,
            'structure' => $structure
        ];
    }

    public function getStructure() {
        return $this->structure;
    }

    public function setStructure($structure) {
        $this->structure = $structure;
    }

    public final function getExtensions($r) {

        $hierarchy = [\get_called_class()];

        while ($parent = $r->getParentClass()) {
            $hierarchy[] = $parent->getName();
            $r = $parent;
        }

        foreach ($hierarchy as $level) {
            $extensions = \models\extensionManager::Load($level)->getExtensions();
            if ($extensions) {
                foreach ($extensions as $extension) {
                    $this->setStructure(array_merge(
                                    $this->getStructure(), $extension
                    ));
                }
            }
        }
    }

    public function toForm() {

        $defintion = [];

        foreach ($this->getStructure() as $key => $val) {

            $optionData = [];

            if (is_array($val['options'])) {
                foreach ($val['options'] as $option) {
                    $optionData[] = ['display' => $option, 'post' => $option];
                }
            }

            $defintion[] = [
                "label" => \utils\Tools::camel_to_title($key),
                "name" => $key,
                "required" => $val[0],
                "input_type" => $val[1],
                "data_type" => $val[1],
                'option_data' => $optionData
            ];
        }
        $form = new class(get_called_class(), $defintion) extends \libs\forms\manager {};

        return $form;
    }

}
