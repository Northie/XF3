<?php

namespace libs\forms;

interface iFormHandler {
	public function Execute();
	public function isSubmitted();
	public function DrawForm();
	public function CheckIn();
	public function getFormDefinition();
	public function ProcessForm();
	public function HTMLise($form);
}