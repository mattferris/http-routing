HttpRouting
===========

[![Build Status](https://travis-ci.org/mattferris/http-routing.svg?branch=master)](https://travis-ci.org/mattferris/http-routing)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2996f3e8-b7a9-424c-a656-939e98d07916/mini.png)](https://insight.sensiolabs.com/projects/2996f3e8-b7a9-424c-a656-939e98d07916)

An HTTP routing library for PHP

Installable via composer:

    composer require mattferris/http-routing

Dispatcher
----------

The dispatcher resolves requests by comparing the passed `Request` object against it's list of routes.

    use MattFerris\HttpRouting\Dispatcher;

    $dispatcher = new Dispatcher();

Calling `dispatch()` routes the request to an action. The action is responsible for generating and returning a response. This response is then returned by `dispatch()`. The response is an instance of `ResponseInterface`. `dispatch()` will generate a request object from the environment if it isn't passed a request. To send the response to the client, call `send()` on the returned response.

    // let the dispatcher generate the request
    $response = $dispatcher->dispatch();

    // dispatch a custom request
    $response = $dispatcher->dispatch($customRequest);

    // send the response
    $response->send();

Or, if you prefer a one-liner:

    $dispatcher->dispatch()->send();

Routing
-------

Routes define the criteria that a request must match in order for a given action to process the request. Actions must be `callable`. Routes are evaluated in the order they are added. Multiple actions can process a single request, with processing continuing until an action returns a response. The collection of routes is referred to as a *route stack*.

Routes can be added a number of ways. A simple method is by using the helper methods, named after the HTTP methods you want to match (`get()`, `post()`, `put()`, `delete()`, `head()`, `options()` and `trace()`).

    // handle requests for GET /foo with a closure
    $dispatcher->get('/foo', function () {
        return new Response('response to GET /foo');
    });

    // handle requests for POST /foo with the fooAction() method on the Controller class
    $dispatcher->post('/foo', 'Controller::fooAction');

You can also match any HTTP method using `any()`.

    // handle requests for /foo with fooAction() method on $object
    $dispatcher->any('/foo', array($object, 'fooAction'));

All helper methods also support a third parameter for matching HTTP headers. The headers must be passed an array with the array keys as the header name. Header names are not case-sensitive.

    $dispatcher->get('/foo', 'Controller::fooAction', array(
        'Host' => 'example.com'
    ));

### Capturing Parameters

You can capture parts of the URI using named parameters.For example, to capture usernames in the URI `/users/joe`, where `joe` can be any username, you could do the following.

    $dispatcher->get('/users/{username}', 'Controller::fooAction');

The value captured by the parameter is passed to the action as an argument.

    class Controller
    {
        public function fooAction($username)
        {
            ...
        }
    }

Multiple parameters can be captured and will be passed to the action in the same way.

    $dispatcher->get('/users/{username}/{option}', function ($username, $option) {
        ...
    });

### Error 404 Handling

You can define a 404 handler easily enough by defining the last route in the *route stack* with generic criteria, and setting the action to a piece of code that can generate an approriate response.

    $dispatcher->any('/', function () {
        return new Response('not found', 404);
    });

Actions
-------

An action defines the code that actually processes the request and generates a response. The only requirement of an action is that it return an instance of `ResponseInterface`, an instance of `RequestInterface`, or nothing at all.

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

            return new Response(
                '{"bar":"'.$bar.'"}', 200, 'application/json'
            );
        }

        public function postFooAction()
        {
            ...

            return new Response(
                '{"status": "success"}', 200, 'application/json'
            );
        }
    }

### Internal Redirects

You can redirect a client with an HTTP 301 response (for example), which the browser then interprets and issues a new request to the specified URL. In some cases, you may want to simply re-evaluate a new request without returning anything to the client. This is possible by returning an instance of `RequestInterface` from the action.

    public function someAction()
    {
        return new Request();
    }

When `Dispatcher` identifies the return value from the action as a new request, it calls `dispatch()` again and passes the new request as an argument. The new request is processed exactly the same as the original.

### Fall-Through Routes

*Fall-through routes* are routes which don't return a response, and therefore allow further matching to continue. They can be useful for executing code without terminating the routing process. For example, you could use a *fall-through route* to add request logging.

    $dispatcher->any('/', function (Request $request) {
        error_log('received request: '.$request->getUri());
    });

### Request Attributes

When using internal redirects or *fall-through routes*, it can be useful to pass along information from one action to another. This can be done by setting attributes on the request object using `setAttribute()`.

    public function getFooAction(RequestInterface $request)
    {
        $request->setAttribute('bar', 'baz');
    }

Other actions can then access this information via `getAttribute()`.

    public function anotherAction(RequestInterface $request)
    {
        $bar = $request->getAttribute('bar');
    }

### Argument Injection

The routing section touched on how named parameters can be accessed via the arguments of your action, i.e. a pattern named `username` can be access via an argument name `$username`. This is done via injection, where the dependency injector matches the argument name to the parameter. In addition to parameters, your actions can access additional information via arguments, such as the current request object.

    public function someAction(RequestInterface $request)
    {
        ...
    }

For more on dependency injection, checkout [mattferris/di](http://bueller.ca/di).

Bundles
-------

Within your application, you can define 'bundles', which are a collection of routes that parts of your application can handle. Bundles can be registered with a dispatcher via `register()`. Bundles are just a plain class implementing `BundleInterface`, and must define a single method, `provides()`, which accepts an instance of `Dispatcher` as it's only argument.

    class MyAppBundle implements \MattFerris\HttpRouting\BundleInterface
    {
        public function provides(ConsumerInterface $dispatcher)
        {
            $dispatcher->get('/users/{username}', 'Controller::someAction', ['Host => 'example.com']);
        }
    }

    $dispatcher->register(new MyAppBundle());

Bundles offer a convenient way of allowing parts of your application to manage their own routing.

Advanced Routing
----------------

### Additional Route Types

Internally, routes are represented as objects implementing `RouteInterface`. When adding routes using the helper methods, the `Dispatcher` creates route objects. By default, these route objects are all `PathRoute` instances, as `PathRoute` is the default type. This can be changed by calling `Dispatcher::setDefaultRouteType()`. Two other route types are included: `SimpleRoute` and `RegexRoute`.

    $dispatcher->setDefaultRouteType('\MattFerris\HttpRouting\RegexRoute');

After setting the new default route type, all helper methods will then create instances of the new route type. This can be used to implement your own route type.

You can also add route objects directly using `Dispatcher::add()` and `Dispatcher::insert()`. `add()` adds the route to the end of the route stack, while `insert()` allows you to insert the route into any position in the route stack. This can be useful for adding early routes to capture requests for middleware to process. `add()` and `insert()` accept instances of `RouteInterface`.

    $dispatcher
        ->add(new SimpleRoute('/foo', 'Controller::someAction'))
        ->insert(new RegexRoute('^/foo/(bar|baz)', 'Controller::anotherAction'));

Route constructors accept 4 arguments.

    new SimpleRoute($uri, $action, $method, $headers);

`$method` and `$headers` are optional.

`RegexRoute` allows you to use regular expressions to match URIs, methods and headers. While flexible, the syntax for capturing parameters can be a little unweildly (`^/users/(?P<username>[a-zA-Z_]+?)/`). `PathRoute` extends `RegexRoute` to provide friendly parameter matching for URIs, but can still employ full regex functionality for URIs, methods and headers as well.

`SimpleRoute` truly is simple. Not pattern matching. URIs are matched on prefix, so `/foo` will match `/foo/bar` and `/foo/baz`. It's sole purpose is for efficiency.

Route types can be used together within the same route stack to acheive effeciency or flexibility where it's needed most.

    // capture requests for logging middleware 
    $dispatcher->add(new SimpleRoute('/', 'LoggerController::logRequest'));

    // process user requests
    $dispatcher->add(new PathRoute('/users/{username}', 'UsersController::getUser', 'GET');

    // capture similar requests
    $dispatcher->add(new RegexRoute('^/(help|support)', 'HelpController::getHelp', 'GET');

    // error 404
    $dispatcher->add(new SimpleRoute('/', 'ErrorController::error404');
