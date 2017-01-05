HTTP Routing
============

[![Build Status](https://travis-ci.org/mattferris/http-routing.svg?branch=master)](https://travis-ci.org/mattferris/http-routing)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2996f3e8-b7a9-424c-a656-939e98d07916/mini.png)](https://insight.sensiolabs.com/projects/2996f3e8-b7a9-424c-a656-939e98d07916)

A PSR-7 compliant HTTP routing library for PHP

Installable via composer:

```
composer require mattferris/http-routing
```

To use this library, you'll need to also install a PSR-7 compliant HTTP
messaging library like 
[zendframework/zend-diactoros](https://github.com/zendframework/zend-diactoros).

Dispatcher
----------

The dispatcher resolves requests by comparing the passed instance
`Psr\Http\Message\ServerRequestInterface` against it's list of routes.

```php
use MattFerris\Http\Routing\Dispatcher;

$dispatcher = new Dispatcher($request);
```

Calling `dispatch()` routes the request to an action. The action is responsible
for generating and returning a response. This response is then returned by
`dispatch()`. The response is an instance of
`Psr\Http\Message\ResponseInterface`. 

```php
// get a response
$response = $dispatcher->dispatch($request);
```

Routing
-------

Routes define the criteria that a request must match in order for a given action
to process the request. Actions must be `callable` or a string with a class name
and a method name separated by a colon (:). Using the `ClassName:methodName`
syntax has the advantage of letting the controller worry about instantiating the
controller (allowing the controller to have dependecies injected).

Routes are evaluated in the order they are added. Multiple actions can process a
single request, with processing continuing until an action returns a response.
The collection of routes is referred to as a *route stack*.

Routes can be added a number of ways. A simple method is by using the helper
methods, named after the HTTP methods you want to match (`get()`, `post()`,
`put()`, `delete()`, `head()`, `options()` and `trace()`).

```php
// handle requests for GET /foo with a closure
$dispatcher->get('/foo', function () {
    return $response;
});

// handle requests for POST /foo with the fooAction() method on the Controller class
$dispatcher->post('/foo', 'Controller::fooAction');
```

You can also match any HTTP method using `any()`.

```php
// handle requests for /foo with fooAction() method on $object
$dispatcher->any('/foo', array($object, 'fooAction'));
```

All helper methods also support a third parameter for matching HTTP headers. The
headers must be passed an array with the array keys as the header name. Header
names are not case-sensitive.

```php
$dispatcher->get('/foo', 'Controller::fooAction', array(
    'Host' => 'example.com'
));
```

### Capturing Parameters

You can capture parts of the URI using named parameters. For example, to capture
usernames in the URI `/users/joe`, where `joe` can be any username, you could do
the following.

```php
$dispatcher->get('/users/{username}', 'Controller::fooAction');
```

The value captured by the parameter is passed to the action as an argument.

```php
class Controller
{
    public function fooAction($username)
    {
        ...
    }
}
```

Multiple parameters can be captured and will be passed to the action in the same
way.

```
$dispatcher->get('/users/{username}/{option}', function ($username, $option) {
    // ...
});
```

Parameter names must start with letters or an underscore and contain letters,
numbers and underscores (as a regex, this would look like
`[a-zA-Z_][a-zA-Z0-9_]+`). This follows PHPs allowed characters for variable
names.

By default, all parameters are required for a URI to match the route. When a
route is created, you can define default values for it's parameters. Any
parameter that has a default value is considered to be optional. Parameters are
passed as the fourth argument.

```php
$dispatcher->get('/users/{username}/{option}', $action, $headers, ['option' => 'update']);
```

This route will now match `/users/joe/preferences` as well as `/users/joe/`.
In the case of `/users/joe/`, because `{option}` isn't specified it will default
to `update`.

Once a parameter has been defined as optional, all parameters that follow must
also be optional. A `BadLogicException` will be thrown if a required parameter
proceeds an optional parameter.

```php
// {details} must be optional because {option} is optional
$dispatcher->get('/users/{username}/{option}/{details}', $action, $headers, ['option' => 'update', 'details' => '']);

// {details} is required (no default value), which will throw an exception
$dispatcher->get('/users/{username}/{option}/{details}', $action $headers, ['option' => 'update']);
```

### Error 404 Handling

You can define a 404 handler easily enough by defining the last route in the
*route stack* with generic criteria, and setting the action to a piece of code
that can generate an approriate response.

```php
$dispatcher->any('/', function () {
    return $error404Response;
});
```

Actions
-------

An action defines the code that actually processes the request and generates a
response. The only requirement of an action is that it return an instance of
`Psr\Http\Message\ResponseInterface`, an instance of
`Psr\Http\Message\ServerRequestInterface`, or nothing at all.

```php
// given these routes...
$dispatcher
    ->get('/foo/{bar}', 'MyController::getFooAction')
    ->post('/foo', 'MyController::postFooAction');

// your controller might look like...
class MyController
{
    public function getFooAction($bar)
    {
        ...

        return $response;
    }

    public function postFooAction()
    {
        ...

        return $response;
    }
}
```

### Internal Redirects

You can redirect a client with an HTTP 301 response (for example), which the
browser then interprets and issues a new request to the specified URL. In some
cases, you may want to simply re-evaluate a new request without returning
anything to the client. This is possible by returning an instance of
`Psr\Http\Message\ServerRequestInterface` from the action.

```php
public function someAction()
{
    return $request;
}
```

When `Dispatcher` identifies the return value from the action as a new request,
it calls `dispatch()` again and passes the new request as an argument. The new
request is processed exactly the same as the original.

### Fall-Through Routes

*Fall-through routes* are routes which don't return a response, and therefore
allow further matching to continue. They can be useful for executing code
without terminating the routing process. For example, you could use a
*fall-through route* to add request logging.

```php
$dispatcher->any('/', function (Request $request) {
    error_log('received request: '.$request->getUri());
});
```

### Argument Injection

The routing section touched on how named parameters can be accessed via the
arguments of your action, i.e. a pattern named `username` can be access via an
argument name `$username`. This is done via injection, where the dependency
injector matches the argument name to the parameter. In addition to parameters,
your actions can access additional information via arguments, such as the
current request object.

```php
public function someAction(\Psr\Http\Message\ServerRequestInterface $request)
{
    // ...
}
```

For more on dependency injection, checkout [mattferris/di](http://bueller.ca/di).

Named Routes and Reverse Routing
--------------------------------

You can optionally specify names for routes. Named routes can then be used to
generate URIs that will match the given route using `generate($name)`. Use this
method of URI generation to bind your application to routes instead of URIs.

```php
// specify a route called auth_login
$dispatcher->post('/login', 'AuthController::login', [], 'auth_login');

// now you can generate URIs in your actions based on the route
public function someAction()
{
    $uri = $dispatcher->generate('auth_login');
    // $uri contains '/login'
}

// if you need to change the route, the action will automatically
// generate the correct URI
$dispatcher->post('/auth/login', 'AuthController::login', [], 'auth_login');
```

### Parameters

Routes containing parameters must be passed an array of parameter values to use.

```php
$dispatcher->get('/users/{user}', 'UsersController::getUser', [], 'get_user');

// pass the username to use for the {user} parameter
$uri = $dispatcher->generate('get_user', ['user' => 'joe']);
echo $uri; // outputs '/users/joe'
```

You can pass extra parameters that aren't defined in the route. Extra parameters
are used to generate a query string.

```php
$uri = $dispatcher->generate('get_user', ['user' => 'joe', 'foo' => 'bar']);
echo $uri; // outputs '/users/joe?foo=bar'
```

Route Providers
---------------

Within your application, you can define *providers*, which provide a collection
of routes that parts of your application can handle. *Providers* can be
registered with a dispatcher via `register()`. *Providers* are just a plain
class implementing `MattFerris\Provider\ProviderInterface`, and must define a
single method, `provides()`, which accepts an instance of `Dispatcher` as it's
only argument.

```php
class RoutingProvider implements \MattFerris\Provider\ProviderInterface
{
    public function provides($consumer)
    {
        $dispatcher->get('/users/{username}', 'Controller::someAction', ['Host => 'example.com']);
    }
}

$dispatcher->register(new RoutingProvider());
```

Providers offer a convenient way of allowing parts of your application to manage
their own routing.

Advanced Routing
----------------

### Additional Route Types

Internally, routes are represented as objects implementing `RouteInterface`.
When adding routes using the helper methods, the `Dispatcher` creates route
objects. By default, these route objects are all `PathRoute` instances, as
`PathRoute` is the default type. This can be changed by calling
`Dispatcher::setDefaultRouteType()`. Two other route types are included:
`SimpleRoute` and `RegexRoute`.

```php
$dispatcher->setDefaultRouteType('\MattFerris\Http\Routing\RegexRoute');
```

After setting the new default route type, all helper methods will then create
instances of the new route type. This can be used to implement your own route
type.

You can also add route objects directly using `Dispatcher::add()` and
`Dispatcher::insert()`. `add()` adds the route to the end of the route stack,
while `insert()` allows you to insert the route into any position in the route
stack. This can be useful for adding early routes to capture requests for
middleware to process. `add()` and `insert()` accept instances of
`RouteInterface`.

```php
$dispatcher
    ->add(new SimpleRoute('/foo', 'Controller::someAction'))
    ->insert(new RegexRoute('/foo/(bar|baz)', 'Controller::anotherAction'));
```

Route constructors accept 4 arguments.

```php
new SimpleRoute($uri, $action, $method, $headers);
```

`$method` and `$headers` are optional.

`RegexRoute` allows you to use regular expressions to match URIs, methods and
headers. While flexible, the syntax for capturing parameters can be a little
unweildly (`/users/(?P<username>[a-zA-Z_]+?)/`). `PathRoute` extends
`RegexRoute` to provide friendly parameter matching for URIs, but can still
employ full regex functionality for URIs, methods and headers as well.

`SimpleRoute` truly is simple. Not pattern matching. URIs are matched on prefix,
so `/foo` will match `/foo/bar` and `/foo/baz`. It's sole purpose is for
efficiency.

Route types can be used together within the same route stack to acheive
effeciency or flexibility where it's needed most.

```php
// capture requests for logging middleware 
$dispatcher->add(new SimpleRoute('/', 'LoggerController::logRequest'));

// process user requests
$dispatcher->add(new PathRoute('/users/{username}', 'UsersController::getUser', 'GET');

// capture similar requests
$dispatcher->add(new RegexRoute('/(help|support)', 'HelpController::getHelp', 'GET');

// error 404
$dispatcher->add(new SimpleRoute('/', 'ErrorController::error404');
```

### RegexRoute and Optional Parameters

When specifying default parameters in `RegexRoute` routes, the optional
parameter sub-patterns must allow for an empty match.

```php
new RegexRoute('/users/(?<username>[a-zA-Z_]+?|)', $action, $method, $headers, ['username' => '']);
```

