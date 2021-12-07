<?php

namespace flow\filters;

trait filter {

    use \Plugins\helper;

    private $currentNode;
    private $list;
    private $request;
    private $response;
    protected $options = [];

    public function __construct($list, $request, $response) {
        $this->list = $list;
        $this->request = $request;
        $this->response = $response;
    }

    public function init() {
        //as this is called just after the filter has been stacked, the list object has this object as the last node
        $this->currentNode = $this->list->getLastNode();
        $this->request->getEndpoint()->filteredBy($this);
    }

    private function getNext() {
        if ($this->currentNode->next->label) {
            return $this->list->getNodeValue($this->currentNode->next->label);
        }
        return false;
    }

    private function getPrev() {
        if ($this->currentNode->previous->label) {
            return $this->list->getNodeValue($this->currentNode->previous->label);
        }
        return false;
    }

    public function FFW() {
        $filter = $this->getNext();

        if ($filter) {
            $r = new \ReflectionObject($filter);
            $filterName = ucfirst($r->getName());
            if (\Plugins\EventManager::Load()->ObserveEvent("onAround" . $filterName . "In", $filter)) {
                if (\Plugins\EventManager::Load()->ObserveEvent("onBefore" . $filterName . "In", $filter)) {
                    \settings\registry::Load()->set('ActiveFilter', $filter);
                    $filter->in();
                    \Plugins\EventManager::Load()->ObserveEvent("onAfter" . $filterName . "In", $filter);
                }
            }
        } else {
            $r = new \ReflectionObject($this);
            $filterName = ucfirst($r->getName());
            if (\Plugins\EventManager::Load()->ObserveEvent("onAround" . $filterName . "Out", $this)) {
                if (\Plugins\EventManager::Load()->ObserveEvent("onBefore" . $filterName . "Out", $this)) {
                    \settings\registry::Load()->set('ActiveFilter', $this);
                    $this->out();
                    \Plugins\EventManager::Load()->ObserveEvent("onAfter" . $filterName . "Out", $this);
                }
            }
        }
    }

    public function RWD() {
        $filter = $this->getPrev();
        if ($filter) {
            $r = new \ReflectionObject($filter);
            $filterName = ucfirst($r->getName());
            if (\Plugins\EventManager::Load()->ObserveEvent("onAround" . $filterName . "Out", $filter)) {
                if (\Plugins\EventManager::Load()->ObserveEvent("onBefore" . $filterName . "Out", $filter)) {
                    \settings\registry::Load()->set('ActiveFilter', $filter);
                    $filter->out();
                    \Plugins\EventManager::Load()->ObserveEvent("onAfter" . $filterName . "Out", $filter);
                }
            }
        }
    }

    public function setOptions($options) {
        $this->options = $options;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getResponse() {
        return $this->response;
    }

}
