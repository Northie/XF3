<?php

namespace models\data;

class resource extends model {

    public $structure = [
        'status' => [true, "enum('draft','published','deleted')"],
        'published' => [false, 'int', 'time'],
        'editions' => [
            'required' => false,
            'multiple' => true,
            'structure' => [
                'timestamp' => [true, 'int', 'time'],
                'note' => [false, 'string']
            ]
        ],
    ];
    
    public function __construct($structure=false) {
        parent::__construct();
        $base = new base;
        $this->structure = array_merge(
                $base->getStructure(),
                $structure ? $structure : $this->getStructure()
        );
    }

}