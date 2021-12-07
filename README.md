# ReadMe

* This  is a PHP application Framework
* This does not use composer
* This is light-wight
* This is platform and location agnostic
* This is NOT MVC

### If This Toolkit is not MVC, then what is it?

The paradigm for This Toolkit is not not to be all things to all people, nor does the framework insist that you work in a certain way, nor does it assume that is the only framework in play.

Instead, This Toolkit allows for a light weight way to route a request to an endpoint and respond; "Route-Endpoint-Respond"

If you apprach This Toolkit from an MVC background or point of view then the "Endpoints" with This Toolkit map nicely to the controllers in MVC. In This Toolkit Views and Models are optional, treated almost like libraries you would implement yourself.

### No Models? No Views?

Basic database adapters to MySQL, Filesystem, APC and couchbase are provided

A simple dispatch loop (more on that later) provides events to which a view layer could be bound

## Autoloading

The autoloader works by scanning all files in the This Toolkit framework folder and the application folder implemeting This Toolkit. From these PHP source files a manifest is created mapping fully qualified class names to full filepaths.

If running in "DEV" mode the autoloader will generate this manifest whenever a class is requested but does not yet exist in the manifest. If the mode is not "DEV" then this manifest generation does not execute; assuming that the minifest already exists.

A build script could trigger this manifest generation

## Modes

Development / Production / Environment modes can be set by the implementing application.

"DEV" is set by default, no others are assumed

The farmework carries out other tasks when in "DEV" mode

Set the mode in bootstrap.php with

    \settings\general::Load()->set(['This Toolkit','ENV'], 'DEV');
	
## General Concepts

### Contexts and Endpoints

An endpoint is ultimately the class that will be instantiated for given request.

The method signature of an endpoint has a constructor and an Execute method. The endpoint class is always instantiated so the constructor is always called.

The Execute method is called by the Action Filter (see Dispatch Loop and Filters below)

Contexts allow grouping and routing rules to be applied to a set of Endpoints

All endpoints must be declared inside a Context

The application must have at least one context

When using more than one context, one of the contexts must be defined as the default context

Examples of different contexts may include

* web (default) - Where all front end web traffic for general browsing is routed too
* admin - where your build your application's back end
* api - somewhere to expose your ReSTful API

Set the contexts in bootstrap.php

Context routing may be folder based, eg

* www.mywebsite.com/
* www.mywebsite.com/admin/
* www.mywebsite.com/api/

or subdomain based, eg

* www.mywebsite.com/
* admin.mywebsite.com/
* api.mywebsite.com/

### Dispatch Loop and Filters

The "controller" aspect from MVC is distributed across many layers in This Toolkit; the front controller, filters and the endpoint

An instance of the Request object and the Front Controller are two of the first classes to be intantiated, logic within the front controller identifies the context, module and endpoint for the given request and instantiates the endpoint.

There is one main dispatch loop in This Toolkit consisting of a doubly linked list of "Filters". Eaxch Filter has an "In" method and an "Out" method. The default filter list, has just two filters: "dispatch" and "action". Logic in an Endpoint's  constructor can add and remove more filters to this list with before/after type methods

The linked list executes all the In methods in turn, then all the out methods in reverse order. The final filter, "Action" calles the Execute method on the endpoint within the "In" method and the getData method of the endpoint on the "out" method.

It is the responsibility of the filters to call Fwd (forward) for Rwd (reverse/rewind) which will pick the next filter's in/out method respectivly.

Logic within a filter may decide not to proceed with the loop and directly call the out method of its self from the in method. This can be useful for form validation, implementing a cache layer or as part of security measures.

An out method could call the in method of its self again, but without care this could lead to an infinate loop.

### View Filter ###

It is in the filter list where a view layer can be added. A view filter's In method wll have access to the request object and the Out method will have access to all the data from the endpoint. The implementation of how the data is rendered is up to the developer - it could be anything from json encoding the data as part of an api response, through including a basic php+html template right through to initialising a full 3rd party templating library

### Events and Plugins

An event handler is provided whereby at any point in the execution of a request an event can be triggered. In practice the event name is a string, passed to the event handling function. The event handler will match the given string to any plugins registered to that event name.

To use plugins, write a plugin class in the plugins folder of the application, or {modulename}/plugins with a plublic static method "RegisterMe". Bind the plugin class to the event names here

### Modules

Modules can be used to separate concerns. The directory and class structure inside a module follows the same structure as the main application with contexts, endpoints, plugins etc.

# Setup