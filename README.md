# BigCommerce Flickr test project
### Project requirements:

- PHP 5.6
- composer
- apache/nginx/php builtin-server

## Project installation
### Example Nginx configuration

You should point your web root directory to ``public`` subfolder of the project. ``index.php`` file should catch every possible request except for existing files (They should be processed "as is"). In terms of __Nginx__ configuration, that means to use ``try_files`` directive with an ``/index.php`` path as a last resort.

        server {
          listen 80;
          # Server name
          server_name flickr.local;

          index index.php;
          try_files $uri $uri/ /index.php$is_args$args;
          
          # Path to public project subfolder
          root /var/www/flickr/public;

          location ~ [^/]\.php(/|$) {
            fastcgi_split_path_info ^(.+?\.php)(/.*)$;

            if (!-f $document_root$fastcgi_script_name) {
              return 404;
            }

            # Path to PHP socket
            fastcgi_pass unix:/run/php/php-fpm.sock;
            fastcgi_index index.php;

            include /etc/nginx/fastcgi.conf;
          }
        }

### Configuration

Copy ``config.yml.dist`` to ``config.yml`` inside config directory and adjust api_key and database settings as needed.

### Dependencies installation

Run ``composer install`` inside project folder.

### Running the test suite

Just execute ``./vendor/bin/phpunit -c config`` from project root directory and you will see test results.

To generate code coverage use ``./vendor/bin/phpunit -c config --coverage-html=coverage``. This will create ``coverage`` subdirectory. Use ``index.html`` to browse the state.

Due to the time constraint only some ``Infrastructure`` parts are tested. But once you see a tested class - it is 100% covered.

### Database installation

First create a new MySQL database. There are two possible ways to create an SQL schema:
- Run ``./vendor/bin/doctrine orm:schema-tool:create`` to create database structure. This will create structure without support for migration possibilities.
- Run ``./vendor/bin/doctrine-migrations migrations:migrate`` to use migration-based scheme creation. This way it will be possible to execute migrations for on-demand structure modification.

### Libraries and technologies that are used inside the project

- AngularJS for frontend handling
- Twig templating engine
- Yaml parsing library from Symfony for configuration parsing
- Symfony HTTP foundation for request/response handling
- Bootstrap CSS library for responsive layout
- PHPUnit testing framework for unit testing
- Doctrine for ORM, migrations and database abstraction

### Concepts that are used in this project

#### Layers separation

There are currently only two layers: ``Domain`` and ``Infrastructure``. In real world project I would add two more: ``Presentation`` and ``Application``. Currently __Controllers__ from ``Infrastructure`` layers are responsible for the tasks from both, ``Presentation`` and ``Application`` layers.

Additionally, __Entities__ inside of the ``Domain`` layer are not pure nor framework-agnostic. In real project I would abstract them completely from doctrine by moving annotations outside, possibly into config __YAML__ file.

#### Front controller

Located at ``public/index.php`` is a unified application entry point. It is primitive, but sufficient for demonstrational purposes. It configures routing system alongside with some simple error handling logic.

#### Bootstrap

``BigCommerce/Bootstrap.php`` file is responsible for low-level heavy-lifting. It is responsible for resolving paths, loading configuration and creating __Service Registry__. All necessary services are instantiated and populated inside the __Service Registry__. Later on they are used across controllers.

#### Service Registry

Simplified version of container for providing various set of services from unified place. In real world example it could be combined with __Dependency Injection__ pattern to automatically instantiate and register services based on configuration. Implementation is located at ``BigCommerce/Infrastructure/Registry/ServiceRegistry.php``.

#### Routing

Project includes simplistic routing subsystem. It works only with static routes. Configuration is achieved by direct calls to appropriate methods. Router is located in ``BigCommerce/Infrastructure/Routing/Router.php``. At the same directory you can find a base abstract controller class ``Controller.php`` with shortcut for accessing ServiceRegistry from within concrete controllers implementation. Controllers reside inside ``BigCommerce/Infrastructure/Controller/`` directory.

#### No comments

Comments are hard to maintain. Comments are lying. If you have to write a comment to some piece of code - this is clear indicator of problems in your code. Code must be self-explanatory. Annotations are useful for IDE completion though.

#### TDD

Most interesting and challenging part of this app (API communication) was written using TDD. Pay attention to ``CurlProxy`` class to see how one can manage to abstract non-OOP PHP calls with side-effects. This abstraction gives an opportunity to create unit tests for classes like ``Curl``. This is minimalistic but really useful example of how to handle tests.

#### Twig extension

There is one small Twig extension that is used to display copyright years in the footer. It's implementation is located in ``BigCommerce/Infrastructure/Twig/CopyrightExtension.php``.

#### Domain logic and interfaces

Although there is no time/reason to use __DDD__ for such minimalistic project, layering still is very useful. ``Gallery`` and ``Image`` value object and ``User`` and ``SearchHistory`` entities, represents some core concepts from image gallery domain and search tracking domin of the application. There is an ``ApiRepositoryInterface`` interface inside ``Contract`` directory that specifies how ``Gallery`` will be created/loaded. That promotes flexibility in implementation. One can provide multiple implementations of the interfaces, even connected to non-Flickr API.

#### Flickr API Service

API communication is done via ``FlickrRestService``. It is slightly generalized - it uses ``FlickrRequestInterface`` that implemented only once for __search__ requests. Search request can be easily extended to include more filtering criterias, such as dates/tags/geo. Additionaly one can simply create another implementation for different API interactions. All this is possible without touching service itself.

#### Image preview

Angular-powered preview is used instead of simply opening a new window with bigger size image. Works well on mobile platform too.

#### Many-to-many User to History relations

I've made history items unqiue and immutable for the sake of normalization. Only intermediate mapping table is modifiable. History record once created can not be removed or modified. This gives a possibility to use same search item across multiple users.

#### Reliable password encryption

Latest standard additions (PHP5.5+) gives us a native reliable password encryption and constant-time hash verification using __bcrypt__ which I'm more than a happy to use!

#### Fluent interfaces

You will find many places where setters or void operations returns original objects. I do like to have a fluent interface while respecting __Law of Demeter__.

#### Typed lists

In many cases there are plain old arrays are used around to store a list of the objects of the same type. In real world scenario most of them would be replaced with __typed lists__.

#### Private setters

I like the power of setters, as they help to increase incapsulation. Therefore I'm often use private setters to handle validation and integrity checks. Value objects are primary targets for this technique.

### Some bonuses

#### History management

There is a possibility to remove search history items. Frontend part is based on __AngularJS__ and uses directives to do a heavy-lifting. __DELETE__ HTTP method is used to remove items from search history, although it is only for demo purposes and not even close to __REST__.

#### CSRF protection

There is built-in CSRF form protection mechanism. It is used only on login / registration pages.

#### Autologin after registration

User is automatically logged in right after successful registration. Additionally, it is not possible to access registration page for authenticated user.

### Shortcuts / limitations

#### Few assumptions

- Username must be between 3 to 10 characters long
- Password must be at least 6 characters long
- Search request must be at least 3 and up to 100 characters long

#### TDD

Due to the nature of this project only some interesting core parts are unit tested. In real sized project TDD is my only way to go. Everything would be covered with tests, except outermost side-effect functionilaty.

This is also the case for frontend counterpart: __AngularJS__ promotes testability and makes it easy. And it is a bit sed not to use such a great opportunity.

#### Error handling

There is no or very limited error handling around the project. It is not the way to go for production. Proper error handling and reporting is invaluable for application stability in long-term run. __Fail Fast__ is a way to go.

#### Frontend

This is makes no sense to use __SMACSS__ inside such a small project. But for projects of mid and bigger size it is the proper way. __SCSS__ preprocessor ensures reusability and manageability. __Gulp__ would be used in the project of the real size to assist in assets management and build automation.

Since this is very simple one pade app I do not see reasons to use __Angular Routing (ngRoute)__ module. For the project with more than three simple views I would prefer to go in this direction.

Proper use of __HTML5 Polyglot__ markup is my preffered way of presenting documents. This means strict XML validations, closing tags, empty attributes, proper content-type (``application/xhtml+xml``) and no ``document.write()`` and others ugly DOM handling ways.

__Search controller__ should also be refactored. At least __preview__ and __pagination__ subcontrollers can be factored out and separated from search controller, possibly with more generalization (if needed).

#### Naming and Annotations

I would also go in the direction of brainstorming better names for some methods and classes to make code even more clearer. Although I prefer using __type hints__ with meaningful class and variables names, annotation are very useful in terms of exceptions, return types and POD specifications. Looking forward for PHP7 in terms of return type hints specs to reduce unnecessary info even more.

Frontend namings are far away from ideal. Given more time they would go through few refactoring sessions.

#### Serialization

``Gallery`` and ``Image`` are two classes that are serialized and transferred to frontend via AJAX. Due to time limitation a shortcut was made by simply implementing ``JsonSerializable`` interface directly inside of these classes. This is absolutely not my preffered approach. Serialization is another way of presenting the same thing and it would be great to offload this job to application layer with the help of infrastructure of course. Especially this is very important if different parts of an objects are required in different places, since you can have a much better control of this process.

#### Rest API service improvements

There is an idea of REST request object implemented inside of this project. Having response representation object absolutely makes sense to avoid passing untyped (or weakly typed) direct response as an array.

API service is hardcoded to use only PHP serialized data response format. Providing support for more formats is easily achievable and could be used in terms of generalization.

Checking for HTTP response inside the service is also ommited at the current stage.

#### Page overflow feature

Page overflow is not possible in both - zero/negative and too high directions. Both, frontend and backend are responsible for taking care of preventing such a problems.

#### Validations

There must be provided way much more validators around the project. Some helper services and also PHP7 could drastically improve the situation, rising quality and stability of the system.

Additional attention is required to configuration parser. There is primitive and non-efficient validation and it should ideally be replaced with much more robust solution.

Please, take a look on how setters are used inside entities to prevent modification of already persisted entries. I like concept of encapsulating such things into entities, but it very much depends on the usage context.

#### Flickr API is a bit unreliable

During late stages of extensive testing, I recognized that pagination in Flickr API is not really reliable. Total number of pages is varying from request to request and often times there is no images returned from their side. As far as I researched across Internet this is known problem, but no solution exists yet.

#### Configuration loading

Configuration loading is currently done via unchecked direct file_get_contents() call. In real world scenario, a proxy would have been created in a similar way to ``CurlProxy``. This is necessary to promote testability and reliability and gives a better control of error handling (exceptions could be thrown instead of issuing a warning). At this stage I'm really looking forward for PHP7, as plain errors will be ``Throwable`` and __catchable__.

#### Configuration class

I decided to go wigh uniform Configuration container for this project. That means, every service that requires some degree of configuration - should expose an interface. All these interfaces then will be implemented by single uniform Configuration class. As the number of interfaces grows - aggregation of multiple configuration value objects that conforms to separated interfaces and proxied via uniformed class can be applied to reduce complexity.

#### UserRepository

User repository is completely useles as one can simply directly use __EntityManager__. Yet it serves a demonstrational purpose of usage for custom __Doctrine__ repositories.

#### Nice to have

- Twig caching
- AJAX queue management, centralized loaders and handlers
- Sorting, images per page, various filters
- Blocking users on many unsuccessful login attempts
- Good alternative to previous point would be using captcha
- At the moment, if user comes to some specific URL and is unauthorized - she gets redirected to the home page. Would be cool to redirect to original request URL.
- Doctrine caching
