Changelog
=========

0.7
---

* Updated dependencies
* Removed BundleInterface in lieu of MattFerris\Provider\ProviderInterface
* Fixed action type-hint for PathRoute::__construct

0.6
---

* Actions can be defined as callable type or string (class:method)
* Added test coverage and refactored event logger helpers

0.5
---

* Dropped native Request/Response implementation, opting to support any PSR-7 compatible implementation instead
* Changed namespace to MattFerris\Http\Routing
* Added support for named/reverse routes
* RegexRoute now supports all three regex named-subpattern syntax variants
* Added support for optional route parameters

0.4.1 (0.4)
-----------

Really version 0.4, but tagged before TravisCI testing passed

* refactored routes as objects implementing RouteInterface
* Dispatcher::addRoute() and Dispatcher::addRoutes() dropped (see below)
* new helper methods on Dispatcher: get(), post(), put(), delete(), etc...
* new route methods on Dispatcher: add() and insert()
* friendly named parameter support using curly braces /users/{username}
* bug fixes
* expanded test coverage


