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

- specify controllers for all specific requests
- add services
- manage services and controllers from modules
- manage middlewares
- pre/post-process (todo)
- manage application error (todo)


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

For the config, you can use any class implementing \ArrayAccess, instead of the Puppy Config.


## Routes

A route simply matches a request to a controller. When you call this uri, that controller will be called.

### How to add controller?

Puppy\Application has some simple methods to help you to declare your controllers.

```php
$puppy->get($uri, $controller); //filter on GET http method
$puppy->post($uri, $controller); //filter on POST http method
$puppy->json($uri, $controller); //filter on JSON format
$puppy->any($uri, $controller); //filter only on the requested uri
$puppy->filter($filter, $controller); //specific filter as callable
```

### How to define the route pattern?

A pattern of a route is a regex which will match with a specific request uri.

Only one of your controllers will be called when its pattern will match with the request uri. So, depending of the uri, the code of your controller will be executed.

```php
$puppy->get('my/page/(\d)', $controller); 
```

To simplify your live and have more readable uri, you can define some binding. For example:

```php
$puppy->get('my/specific/:uri', $controller)->bind('uri'); 
```

By default, the binding will accept a pattern with string, numeric, '_' and '-'. But you can add a specific regex:

```php
$puppy->get('my/page/:index', $controller)->bind('index', '\d'); 
```

To simplify your life a little bit more, you can use predefined bindings. For example:

```php
$puppy->get(':all', $controller); //every uri
$puppy->get(':home', $controller); //home uri (empty or '/')
$puppy->get(':slug', $controller); //string uri, with numeric, '_' and '-'
$puppy->get(':id', $controller); //any unsigned int, except 0
$puppy->get(':index', $controller); //any unsigned int
$puppy->get(':lang', $controller); //two letters lower case, eventually followed by hyphen and two letters upper case (e.i. fr-FR)
$puppy->get(':datetime', $controller); //datetime with format yyyy-mm-ddThh:mm:ss or yyyy-mm-ddThh:mm:ss+hh:ss
$puppy->get(':date', $controller); //date with format yyyy-mm-dd
$puppy->get(':time', $controller); //time with format hh:mm:ss
```

### How to specify other request constraints?

When you set controllers with the Puppy methods, you can continue to specify some other rules.


```php
$puppy->get($uri, $controller)->content('xml/application');
$puppy->json($uri, $controller)->method('post');
```

All the constraints can be linked together for a same route.

```php
$puppy->any('my/page/:index', $controller)
    ->bind('index', '\d')
    ->method('post')
    ->content('json/application');    
```
You can also restrict your route to a specific path namespace.

```php
$puppy->get('users', $controller)->restrict('admin'); // this is accessible only with the request uri 'admin/users'
```

### How to group routes?

You can also group several of your routes to process to some common actions.

```php
$puppy->group([
             $puppy->get($uri1, $controller1),
             $puppy->get($uri2, $controller2),
        ])
      ->bind('index', '\d')
      ->method('post')
      ->restrict('admin');

```

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

#### Response

But more powerful, this can be also a Response, which will manage also the http header.

```php
$puppy->get('hello', function(){
    return new Response('<h1>Hello world!</h1>');
});
```

To help you to manage some common actions, AppController has some cool methods for you. See AppController section.

### Which arguments will receive the controller?

The controller receive two kinds of arguments, depending on what you want.

#### The pattern matches

If you want to receive the list of matches between pattern and uri, you must specify the param "array $args".

```php
$puppy->get('hello/:all', function(array $args){
    return $args['all']; //will return the value "world" for the uri "/hello/world"
});
```

If you use binding, the key of your matched arg is the alias without ":". For example, binding ":id" can be retrieved with the key "id".

#### The Services

If you want to have the services container, you must specify the param "ArrayAccess $services".

```php
$puppy->get('hello', function(\ArrayAccess $services){
    ...
});
``` 

Of course, you can have the services with the matched args.

```php
$puppy->get('hello', function(array $args, Container $services){
    ...
});
```
The order of params has no importance!

You can also specify which service you want. You just have to name it in the params. (The name of the param must be exactly the name of your service.)

```php
$puppy->get('hello', function(Request $request){
    return 'You ask for the uri "'.htmlentities($request->getRequestUri());
});
```

See services section to know which services are available by default.

### What a controller can do?

A controller manages the HTTP response. So, to help you in common actions, you can use Puppy\Controller\AppController. This is a simple class that contains some utilities methods.

#### Which are the AppController methods?

Methods are for example:

```php
$appController->render($templateFile);
$appController->redirect($url);
$appController->call($uri);
$appController->abort();
$appController->flash()->get($myMessage);
$appController->retrieve($key);
$appController->getService($serviceName);
```

#### How to implement AppController?

There are three ways to use it.

##### As binded class
First, if you simply use a closure as controller, all the methods of AppController will be bound to your closure.

```php
$puppy->get('hello', function(){
    return $this->abort();
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
        return $this->abort();
    }
} 
```

##### As service class

Third, you can ask for AppController as a service in the params.

```php
$puppy->get('hello', function(AppController $appController){
    return $appController->abort();
});
```
See services section for more information.

#### Is there no dependencies?

Be careful, if you use AppController::flash(), you will need a service 'session'. And if your use AppController::rend(), you will need a service 'template'.

To simplify your life, you have two solutions.

##### Work with Puppy

Directly work with [raphhh/puppy](https://github.com/Raphhh/puppy).

Include everything you need.

##### Work with Puppy/Service

You can work directly with Puppy\Service\Session and Puppy\Service\Template. These two services fit perfectly with the AppController.

First, you need to include their [package](https://github.com/Raphhh/puppy-service) to your project. Then, you just need to add these two services with Puppy\Application::addService(). See services section for more information.


## Middlewares

### What is a middleware?

A middleware is just a code executed before the controller. The middleware will trigger the call of its associated controller.

For example, imagine you want to call a controller only for users with admin rights. Then, your middleware can control this for you by filtering only accessible controllers.


### How to implement a middleware?

Just by linking a callable to a controller.

```php
$puppy->get($uri, $controller)->filter(function(){
    return true;
});
```

A middleware works like a controller: it can be any callable. The only difference is that a middleware must return a boolean indicating if we can call the controller.

You can also add any middleware you want. They will be executed in the same order. But, the chain will stop when a middleware returns false.

```php
$puppy->get($uri, $controller)
      ->filter($middleware1)
      ->filter($middleware2)
      ->filter($middleware3);
```

Like a controller, a middleware works with the same system of dynamic params. You can retrieve any service you want. You just have to specify it in the params.

```php
$puppy->get($uri, $controller)->filter(function(Request $request){
    ...
});
```

## Pre and post processing (todo)

You can easily process on the HTTP request and response before and after the routing.
 
The method 'before' is called before the routing and receive the HTTP request.

```php
$puppy->before(function(Request $request){
    ...
});
```

The method 'after' is called after the routing and receive the HTTP response.

```php
$puppy->after(function(Response $response){
    ...
});
```

You can add as many processing as you want.

```php
$puppy->before($callback1)
      ->before($callback2)
      ->after($callback3)
      ->after($callback4);
```

## Mirrors

You can require that some uri be analysed like there were another ones. These uri will be like a mirror that points to a specific predefined route.

For example, you want your request uri "mail" points to "contact". "contact" is a real route, and "mail" must do exactly the same. So, if we the request uri is "mail", the route "contact" will be called.

```php
$puppy->mirror('mail', 'contact'); //request uri "mail" will point to "contact"
```

Mirrors accept also dynamic params.

```php
$puppy->mirror('mail/:id', 'contact/{id}');
```

## Services

### What is a service?

A service is a class which will be present in all your controllers.

By default, Puppy adds some services:
 * config (an object with the config according to your env)
 * request (an object with all the context of the current request. Just be aware that current request could be not the master request.)
 * requestStack (an object with all the requests used during the process. you can retrieve the current and the master request.)
 * router (an object which can analyse all the defined routes and controllers of your app)
 * frontController (instance of the class Puppy\Controller\AppController)
 * appController (instance of the class Puppy\Controller\AppController)
 * retriever (instance of the class Puppy\Helper\Retriever)

You can add any services you want, like for example a templating library, an ORM, ...

### How to add a service?

Because Puppy uses [Pimple](https://github.com/silexphp/Pimple) as services container, a service must be added from a callable.

```php
$puppy->addService('serviceName', function(Container $services){
    return new MyService();
});
```

### How to retrieve any services?

#### From Application

If you work with the Application object.

```php
$puppy->getService('myService');
```

#### From AppController

If you work with the AppController object.

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
$puppy->get('hello', function(Request $request, \ArrayAccess $config){
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
A module is a class that wraps a specific list of services an controllers. The module receives the Application in argument. So, your module class can add any services or controllers that are in your package.


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
 - The class must extend Puppy\Module\IModule.

Application::initModules(new ModulesLoader()) will load for you the modules of your project (by default modules in "src" and "vendor" dir). You can use a cache loader with ModulesLoaderProxy(). The search in the project will be done only on the first call and be cached into the filesystem.


## Application error (todo)

You can add an error/exception handler which will be called for every error (event fatal error) and not caught exception.

```php
$puppy->error(function(\Exception $exception){
    ...
});
```

If the script is interrupted because of a fatal error, you can specify a controller to send a correct HTTP header.

```php
$puppy->die($controller);
```

It is recommended to display the error in the dev env only (display_error), but to intercept always the error (error_reporting). See the [PHP doc](http://php.net/manual/en/errorfunc.configuration.php) for more information about the error.


## Config options

 - 'module.directories' => define the directories where to find dynamically modules. (used by ModuleFactory)
 - 'module.cache.enable' => active the file cache of modules loader. (used by ModuleFactory)
 - 'module.cache.path' => set the path to save the cached files of the modules loader. (used by ModuleFactory)
