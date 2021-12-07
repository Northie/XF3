<?php

namespace Plugins;

class EventFactory {

    public static $plugins = [];

    public static function Build($handler,$obj, $options, $event) {

        if(self::$plugins[$handler]) {
            $plugin  = self::$plugins[$handler];
        } else {
            $plugin = new $handler;
            if($plugin->getPersistence()) {
                self::$plugins[$handler] = $plugin;
            }
        }
        \OS\App::Load()->setTimeLimit(30);
        $proced = $plugin->Initiate($obj, $options, $event);
        \OS\App::Load()->resetTimeLimit();

        return $proced;


    }
}