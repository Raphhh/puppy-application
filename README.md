# Puppy Application
HTTP package for Puppy framework

[![Latest Stable Version](https://poser.pugx.org/raphhh/puppy/v/stable.svg)](https://packagist.org/packages/raphhh/puppy-application)
[![Build Status](https://travis-ci.org/Raphhh/puppy-application.png)](https://travis-ci.org/Raphhh/puppy-application)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Raphhh/puppy-application/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Raphhh/puppy-application/)
[![Code Coverage](https://scrutinizer-ci.com/g/Raphhh/puppy-application/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Raphhh/puppy-application/)
[![Dependency Status](https://www.versioneye.com/user/projects/54062eb9c4c187ff6100006f/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54062eb9c4c187ff6100006f)
[![Total Downloads](https://poser.pugx.org/raphhh/puppy-application/downloads.svg)](https://packagist.org/packages/raphhh/puppy-application)
[![Reference Status](https://www.versioneye.com/php/raphhh:puppy-application/reference_badge.svg?style=flat)](https://www.versioneye.com/php/raphhh:puppy-application/references)
[![License](https://poser.pugx.org/raphhh/puppy-application/license.svg)](https://packagist.org/packages/raphhh/puppy-application)

Puppy Application is like an HTTP controller. It parses the current request and calls a matched controller.

Application basic logic:

- add services
- specify controllers for all specific request
- manage services and controllers from modules

## Installation

```
$ composer require raphhh/puppy-application
```

## Basic usage

Puppy needs a config and a request to run. Then you can add a controller.

```php
use Puppy\Application;
use Puppy\Config\Config;
use Symfony\Component\HttpFoundation\Request;

$puppy = new Application(new Config(), Request::createFromGlobals());
$puppy->get('hello', function(){ 
  return 'Hello world!'; 
});
$puppy->run(); //good dog! :)
```

## Routes

A route simply matches a request to a controller. When you call this uri, that controller will be called.

### How to add controller?

Puppy\Application has some simple methods to help you to declare your controllers.

```php
  $puppy->get($uri, $controller); //filter on GET http method
  $puppy->post($uri, $controller); //filter on POST http method
  $puppy->json($uri, $controller); //filter on JSON format
  $puppy->any($uri, $controller); //filter only on the requested uri
  $puppy->filter($filter, $controller); //specific filter as callable (todo)
```

### How to define the route pattern?

A pattern of a route is a regex which will match with a specific request uri.

Only one of your controllers will be called when its pattern will match with the request uri. So, depending of the uri, the code of your controller will be executed.

```php
  $puppy->get('my/page/(.*)', $controller); 
```

To simplify your life, you can use predefined alias. For example:

```php
  $puppy->get('my/page/:id', $controller);
  $puppy->get('my/page/:all', $controller);
  $puppy->get('my/page/:lang', $controller);
  $puppy->get('my/page/:datetime', $controller);
  $puppy->get('my/page/:date', $controller);
  $puppy->get('my/page/%time%', $controller); 
```

You can add your own alias with the help of the config. (todo)

## Controllers

### What is a controller?

A controller is any callable.

For example, a controller can be a closure:

```php
$puppy->get('hello', function(){
        ...
    });
```

or it can be a class method:

```php
$puppy->get('hello', array($controller, 'method'));
```

or what you want that is callable...

### What will a controller return?

#### String

Your controller will return the response to send to the client. This can be a simple string. 

```php
$puppy->get('hello', function(){
         return '<h1>Hello world!</h1>';
    });
```

#### IResponse
But more powerful, this can be also a IResponse, which will manage also the http header.

```php
$puppy->get('hello', function(){
         return new Response('<h1>Hello world!</h1>');
    });
```

To help you to manage some common actions, AppController has some cool methods for you. See AppController section.

### What arguments will receive the controller?

The controller receive two kinds of arguments, depending of what you want.

#### The matches patterns

If you want to receive the list of matches between pattern and uri, you must specify the param "array $matches".

```php
$puppy->get('hello/:all', function(array $matches){
        return $matches[1]; //will return the value "world" for the uri "/hello/world"
    });
```
#### The Services

If you want to have the services container, you must specify the param "ArrayAccess $services".

```php
$puppy->get('hello', function(\ArrayAccess $services){
        ...
    });
```

Of course, you can have the services with the matches.

```php
$puppy->get('hello', function(array $matches, Container $services){
        ...
    });
```
The order of params has no importance!

You can also specify which service you want. You just have to name it in the params. (The name of the param must be the exactly the name of your service.)

```php
$puppy->get(':all', function(Request $request){
        return '<h1>Hello world!</h1> <p>You ask for the uri "'.htmlentities($request->getRequestUri()).'"</p>';
    });
```

See services section to know which services are available by default.


### What a controller can do?

A controller manages the http response. So, to help you in common action, you can use Puppy\Controller\AppController. This is a simple class that contains some utilities methods.

#### Which are the AppController methods?

Methods are for example:

```php
  $appController->error404();
  $appController->render($templateFile);
  $appController->redirect($url);
  $appController->flash()->get($myMessage);
  $appController->getService($serviceName);
```

#### How to implement AppController?

There are three ways to use it.

##### As binded class
First, if you simply use a closure as controller, all the methods of AppController will be bind to your closure.

```php
$puppy->get('hello', function(){
        return $this->error404();
    });
```

##### As parent class
Second, you can create your Controller class which extends AppController.

```php
use Puppy\Controller\AppController;

class MyController extends AppController 
{    
    public function myAction()
    {
        return $this->error404();
    }
} 
```

##### As service class
Third, you can ask for AppController as a service in the params.

```php
$puppy->get('hello', function(AppController $appController){
        return $appController->error404();
    });
```
See services section for more information.

## Services

### What is a service?

A service is a class which will be present in all your controllers.

By default, Puppy adds some services:
 * config (an object with the config according to your env)
 * request (an object with all the context of the client request and the session)
 * router (an object which can analyse all the defined routes and controllers of your app)
 * frontController (instance of the class Puppy\Controller\AppController)
 * appController (instance of the class Puppy\Controller\AppController)

You can add any service you want, like for example a templating library, an ORM, ...

By default, service must be added from a callable.

```php
$puppy->addService('serviceName', function(\ArrayAccess $services){
        return new MyService();
    });
```

### How to retrieve any services?

#### From Application

If you work with the Application objet.

```php
$puppy->getService('myService');
```

#### From AppController

If you work with the AppController objet.

```php
$appController->getService('myService');
```
See AppController section for more information about this class.

#### From any controller

The more powerful way is to retrieve dynamically your service in the params of the controller. You just have to specify a param with the same name as your service.

```php

//you want the request?
$puppy->get('hello', function(Request $request){
        ...
    });
    
//you want the request and the config?
$puppy->get('hello', function(Request $request, IConfig $config){
        ...
    });
    
//you want the router and the appController?
$puppy->get('hello', function(Router $router, AppController $appController){
        ...
    });
```

The order of the params does not matter.

## Modules

### What is a module?
A module is a class that wraps a specific list of services an controllers. The module receives the Application in argument. So, your module class can add to the Application any services or controllers that are in your package.


```php
//your module class
class MyModule implements \Puppy\Module\IModule{

    function init(\Puppy\Application $puppy){
        $puppy->get('my-module/:all', function(){
            return 'This is my module';
        });
    }

}

//add the module to the Application
$puppy->addModule(new MyModule());
```

### How to load dynamically modules?

You can load dynamically all the modules of your project. You just have to create classes with two specifications:
 - The name of the class has to end with 'Module'.
 - The class must extends Puppy\Module\IModule.

Application::initModules(new ModulesLoader()) will load for you the modules of your project (by default modules in "src" and "vendor" dir). You can use a cache loader with ModulesLoaderProxy(). The search in the project will done only on the first call and be cached into the filesystem.

## Config options

 - 'route.pattern.alias' => add specific alias for the pattern of the route. (used by RoutePattern) (todo)
 - 'module.directories' => define the directories where to find dynamically modules. (used by ModuleFactory)
 - 'module.cache.enable' => active the file cache of modules loader. (used by ModuleFactory)
 - 'module.cache.path' => set the path to save the cached files of the modules loader. (used by ModuleFactory)
