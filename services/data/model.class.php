<?php

namespace models\data;

class Testmodel {
	
	private $model = false;
	private $adapter = false;
	private $flat = [];
	private $joins = [];

	public function __construct(array $model) {
		$this->model = $model;
	
	foreach($this->model as $child => $childData) {
		$this->recurse($child, $childData);
	}
	}
	
	public function toForm() {
		if(!$this->model) {
			throw new Exception("No Model Set");
		}
	}
	
	public function setServiceDataAdapter(\services\data\adapter $adapter) {
		$this->adapter = $adapter;
	}

	private function recurse($key,$data) {

		if($data['fields']) {
			foreach($data['fields'] as $field) {
				$this->flat[] = implode(".",[$key,$field]);
			}
		}
		if($data['join']) {
			foreach($data['join'] as $resource => $field) {
				$this->joins[$key][$resource] = $field;
			}
		}
		
		if($data['children']) {
			foreach($data['children'] as $child => $childData) {
				$this->recurse($child, $childData);
			}
		}
	}
	
	public function getFlat() {
		return $this->flat;
	}
	
	public function getJoin() {
		return $this->joins;
	}

}

//rows/records should be returned similar to

/**
 * user.id
 * user.name
 * user.email
 * email_communication.id
 * email_communication.to
 * email_communication.from
 * email_communication.subject
 * contact.name
 * contact.email
 * company.id
 * company.name
 */
/*
$m = new \services\data\model([
	'user' => [ //user is a resource
		'fields' => [
			'id',
			'name',
			'email'
		],
		'children' => [
			'email_communication' => [ //email communication is a resource
				'fields' => [
					'id',
					'to',
					'from',
					'subject'
				],
				'join' => [
					'user' => 'email',
					'email_communication' => 'from'
				],
				'children' => [
					'contact' => [ //contact is a resource
						'fields' => [
							'name',
							'email'
						],
						'join' => [
							'contact' => 'email',
							'email_communication' => 'to'
						],
						'children' => [
							'company' => [ //company is a resource
								'fields' => [
									'id',
									'name'
								],
								'join' => [
									'company' => 'id',
									'contact' => 'company_id'
								]
							]
						]
					]
				]
			]
		]
	]
]);

print_r($m->getFlat());
print_r($m->getJoin());
 * 
 */