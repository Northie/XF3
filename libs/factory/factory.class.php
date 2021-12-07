<?php

namespace libs\factory;

abstract class factory {

	protected $deferred = false;
	protected $queue = false;
	private $notifications = [];
	private $currentNotification = false;
	protected $reference = false;

	public function Build() {
		ignore_user_abort(true);
		$this->reference = \utils\Tools::UUID();
		$steps = $this->processList->exportForward();
		$start = $this->processList->getFirstNode(true);
		
		\state\manager::Load(\get_called_class(),$this->reference);
		
		$start->start();
	}

	public function Defer() {
		$this->deferred = true;
		$this->reference = \utils\Tools::UUID();
		$steps = $this->processList->exportForward();

		var_dump($steps);
		//so far so good
		//get list of steps
		//get queue
		//get queue reference
		//queue items with reference to session and queue ref
		//return reference
	}

	public function getFeedback($reference) {
		//use reference and queue to get
	}

	public function isDeferred() {
		return $this->deferred;
	}

	public function success($step = false) {
		if ($this->isDeferred()) {
			$this->queue->log(get_class($step) . ' Complete');
		}
	}

	public function failed($step = false) {
		if ($this->isDeferred()) {
			$this->queue->log(get_class($step) . ' Failed');
		}
	}

	public function notify($notification) {
		$notification = $this->currentNotification = $notification;
		$this->notifications[] = $notification;

		$label = get_class($this->processList->getFirstNode(true)->getParent());

		if ($this->isDeferred()) {
			//save to queue
			$this->queue->log($this->reference, $notification);
		} else {
			$session = new \utils\XSession('PROCESSES');
			$session->set($this->reference, [$label => $this->notifications]);
		}
	}

	public function getNotifications() {
		return $this->notifications;
	}

	public function getCurrentNotification() {
		return $this->currentNotification;
	}

	public function getReference() {
		return $this->reference;
	}

}
