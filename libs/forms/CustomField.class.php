<?php

namespace libs\forms;

class CustomField extends manager {
	//*
	public function __construct() {
		parent::__construct(__CLASS__);
		//$this->caller = $caller;
	}
	//*/
	public function getFormDefinition() {
	
		$this->form_name = 'Custom Field';
		
		$form[] = array(
			"label"=>"Field Name",
			"name"=>"name",
			"required"=>1,
			"input_type"=>"text",
			"data_type"=>"text"
		);

		$form[] = array(
			"label"=>"Input Type",
			"name"=>"type",
			"required"=>1,
			"input_type"=>"select",
			"data_type"=>"text",
			"option_data"=>array(
				array(
					"display"=>"Text - Single Line",
					"post"=>"text"
				),
				array(
					"display"=>"Text - Multiline",
					"post"=>"textarea"
				),
				array(
					"display"=>"Single Select - Drop Down",
					"post"=>"select"
				),
				array(
					"display"=>"Single Select - Radio Buttons",
					"post"=>"radio"
				),
				array(
					"display"=>"Multi Select - Checkboxes",
					"post"=>"checkbox"
				)
			)
		);

		$form[] = array(
			"label"=>"Options",
			"name"=>"options",
			"input_type"=>"textarea",
			"data_type"=>"text"
		);
		
		return $form;
	}
}