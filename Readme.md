HttpRouting
===========

[![Build Status](https://travis-ci.org/mattferris/http-routing.svg?branch=master)](https://travis-ci.org/mattferris/http-routing)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2996f3e8-b7a9-424c-a656-939e98d07916/mini.png)](https://insight.sensiolabs.com/projects/2996f3e8-b7a9-424c-a656-939e98d07916)

An HTTP routing library for PHP

Dispatcher
----------

The dispatcher resolves requests by comparing the URI of the passed `Request` object against it's list of routes.

    use MattFerris\HttpRouting\Dispatcher;

    $dispatcher = new Dispatcher();

Calling `dispatch()` routes the request to an action, which, in turn, returns a response. This response is then returned by `dispatch()`. The response is an instance of `Response`. Unless you want to modify the current request, you can call `dispatch()` without arguments, however you can pass a custom request for processing. To send the response to the client, call `send()` on the returned response.

    $response = $dispatcher->dispatch();
    $response->send();

Or, if you prefer a one-liner:

    $dispatcher->dispatch()->send();

Routing
-------

Routes define the criteria that a request must match in order for a given action to process the request. Routes are evaluated in the order they are added. Multiple actions can process a single request, with processing continuing until an action returns a response. The collection of routes is referred to as a *route stack*.

### Route Criteria

Routes contain at least two pieces of information: a `uri`, and an `action`. The `uri` is the regular expression (without start and end delimiters) used to match the requested URI. The `action` is code that the dispatcher will hand the request off to. Optionally, you can also match based on request method and HTTP headers.

Routes can be defined individually using `addRoute()`, or as a collection using `addRoutes()`. In each case, both `uri` and `action` must be specified.

    $dispatcher->addRoute($uri, $action);

    $dispatcher->addRoutes([
        [ 'uri' => $fooUri, 'action' => $fooAction ],
        [ 'uri' => $barUri, 'action' => $barAction ],
        ...
    ]);

Request methods can be matched as well.

    $method = 'GET';
    $dispatcher->addRoute($uri, $action, $method);

    $dispatcher->addRoutes([
        [ 'method' => $method, 'uri' => $uri, 'action' => $action ],
        ...
    ]);

As well as one or more HTTP headers using regular expressions.

    $dispatcher->addRoute($uri $action, $method, array(
        'Host' => $host,
        'User-Agent' => $userAgent
    ));

    $dispatcher->addRoutes([
        [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'headers' => [
                'Host' => $host,
                'User-Agent' => $userAgent
            ]
        ],
        ...
    )];

### Capturing Parameters

Regex patterns can be used to capture named parameters. For example, to capture usernames in the URI `/users/joe`, where `joe` can be any username, you could add a route with `uri` set to `#^/users/(?P<username>[a-zA-Z0-9-_])$#`. `?P<username>` defines a named substring match in the regular expression, which is then made available to the action as an argument.

    $dispatcher->addRoute('^/users/(?P<username>[a-zA-Z0-9-_])$', function ($username) {
        // if the requested URI is /users/joe, then $username will contain 'joe'
    });

One thing to keep in mind is that the name of the argument in the action must match the name of the parameter in the pattern. In the above example, the parameter is `username` so the argument in the action must be `$username`. Multiple named parameters can be defined using the same method. This can also be done with matched headers as well.

    $action = function ($host) {
        // $host will contain the matched Host header
    };

    $dispatcher->addRoute($uri, $action, $method, array(
        'Host' => '^(?P<host>example.com)$'
    ));

Actions can be a method on an object or a closure, and can defined like so:

    // define a method action
    $dispatcher->addRoute($uri, 'ClassName:methodName');

    // define a closure action
    $dispatcher->addRoute($uri, function () { ... });

### Error 404 Handling

You can define a 404 handler easily enough by defining the last route in the *route stack* with generic criteria, and setting the action to a piece of code that can generate an approriate response.

    $error404Action = function (Request $request) {
        return new Response('...');
    };

    $dispatcher->addRoutes([
        ...
        [ 'uri' => '^.*$', 'action' => $error404Action ]
    ]);

Actions
-------

An action defines the code that actually processes the request and generates a response. The only requirement of an action is that it return an instance of `ResponseInterface`, an instance of `RequestInterface`, or nothing at all.

    // given these routes...
    $routes = [
        [
            'method' => 'GET',
            'uri' => '^/foo/(?P<bar>.*)$',
            'action' => 'MyController:getFooAction'
        ],
        [
            'method' => 'POST',
            'uri' => '^/foo',
            'action' => 'MyController:postFooAction'
        ]
    ];

    // your controller might look like...
    class MyController
    {
        public function getFooAction($bar)
        {
            ...

            return new \MattFerris\HttpRouting\Response(
                '{"bar":"'.$bar.'"}', 200, 'application/json'
            );
        }

        public function postFooAction()
        {
            ...

            return new \MattFerris\HttpRouting\Response(
                '{"status": "success"}', 200, 'application/json'
            );
        }
    }

### Interal Redirects

Using `$this->redirect()` in the example above produces an HTTP 301 response, which the browser then interprets and issues a new request to the specified URL. In some cases, you may want to simply re-evaluate a new request without returning anything to the client. This is possible by returning an instance of `RequestInterface` from the action.

    public function someAction()
    {
        return new Request();
    }

When `Dispatcher` identifies the return value from the action as a new request, it calls `dispatch()` again and passes the new request as an argument. The new request is processed exactly the same as the original.

### Fall-Through Routes

*Fall-through routes* are routes which don't return a response, and therefore allow further matching to continue. They can be useful for executing code without terminating the routing process. For example, you could use a *fall-through route* to add request logging.

    $dispatcher->addRoute($uri, function (Request $request) {
        error_log('received request: '.$request->getUri());
    });


### Argument Injection

The routing section touched on how named parameters can be accessed via the arguments of your action, i.e. a pattern named `username` can be access via an argument name `$username`. This is done via injection, where the dependency injector matches the argument name to the parameter. In addition to parameters, your actions can access additional information via arguments.

As discussed above, you can access the current `Request` via the dependency injector made available via `AbstractController`. This can also be accomplished via injection by adding an argument called `$request` to your action.

    public function getFooAction(Request $request)
    {
        $uri = $request->getUri();

        ...
    }

The dependency injector automagically injects the `Request` object into the action when it sees an argument named `$request`. This allows you to write controllers that don't extend `AbstractController` and therefore are more decoupled.

For more on dependency injection, checkout [mattferris/di](http://bueller.ca/di).

Bundles
-------

Within your application, you can define 'bundles', which are a collection of routes that parts of your application can handle. Bundles can be registered with a dispatcher via `register()`. Bundles are just a plain class implementing `BundleInterface`, and must define a single method, `provides()`, which returns an array of the supported routes.

    class MyAppBundle implements \MattFerris\HttpRouting\BundleInterface
    {
        public function provides()
        {
            return [
                // routes defined here
            ];
        }
    }

    $dispatcher->register(new MyAppBundle());

Bundles offer a convenient way of allowing parts of your application to manage their own routing.
