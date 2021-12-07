<?php

namespace libs\factory;

abstract class processStepUI extends processStep implements iProcessStepUI {

	protected $form = false;

	public final function start() {

		if (!$this->form) {
			throw new BuildException('User Interface/Input Step form not set');
		}

		$this->form->Execute();

		if ($this->form->isValid()) {
			$this->Build();
		} else {
			$this->getUI();
		}
	}

	public function setForm($form) {
		$this->form = $form;
	}

	public function getForm() {
		return $this->form;
	}

	public function getUI() {
		//get response
		//if view filter applied, get view filter and set form
		//else???
		//how to deal with ext/sencha ui?
	}

}
