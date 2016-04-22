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

Copy ``config.yml.dist`` to ``config.yml`` inside config directory and adjust api_key as needed.

### Dependencies installation

Run ``composer install`` inside project folder.

### Libraries and technologies that are used inside the project

- AngularJS for frontend handling
- Twig templating engine
- Yaml parsing library from Symfony for configuration parsing
- Symfony HTTP foundation for request/response handling
- Bootstrap CSS library for responsive layout
- PHPUnit testing framework for unit testing

### Concepts that are used in this project

#### Front controller

Located at ``public/index.php`` is a unified application entry point. It is primitive, but sufficient for demonstrational purposes. It set-ups __Service Registry__, instantiates necessary dependencies, reads configuration, prepares __Twig__ environment. Routing system is also configured inside of the front controller. Some simple error handling logic is also located here.

#### Service Registry

Simplified version of container for providing various set of services from unified place. In real world example it could be combined with __Dependency Injection__ pattern to automatically instantiate and register services based on configuration. Implementation is located at ``BigCommerce/Infrastructure/Registry/ServiceRegistry.php``.

#### Routing

Project includes simplistic routing subsystem. It works only with static routes. Configuration is achieved by direct calls to appropriate methods. Router is located in ``BigCommerce/Infrastructure/Routing/Router.php``. At the same directory you can find a base abstract controller class ``Controller.php`` with shortcut for accessing ServiceRegistry from within concrete controllers implementation. Controllers reside inside ``BigCommerce/Infrastructure/Controller/`` directory.

#### No comments

Comments are hard to maintain. Comments are lying. If you have to write a comment to some piece of code - this is clear indicator of problems in your code. Code must be self-explanatory. Annotations are useful for IDE completion.

#### TDD

Most interesting and challenging part of this app (API communication) was written using TDD. Pay attention to ``CurlProxy`` class to see how one can manage to abstract non-OOP PHP calls with side-effects. This abstraction gives an opportunity to create unit tests for classes like ``Curl``. This is minimalistic but really useful example of how to handle tests.

#### Twig extension

There is one small Twig extension that is used to display copyright years in the footer. It's implementation is located in ``BigCommerce/Infrastructure/Twig/CopyrightExtension.php``.

#### Domain logic and interfaces

Although there is no time/reason to use __DDD__ for such minimalistic project, layering still is very useful. ``Gallery`` and ``Image`` value object represents some core concepts from image gallery domain of the application. There is an ``ApiRepositoryInterface`` interface inside ``Contract`` directory that specifies how ``Gallery`` will be created/loaded. That promotes flexibility in implementation. One can provide multiple implementations of the interfaces, even connected to non-Flickr API.

#### Flickr API Service

API communication is done via ``FlickrRestService``. It is slightly generalized - it uses ``FlickrRequestInterface`` that implemented only once for __search__ requests. Search request can be easily extended to include more filtering criterias, such as dates/tags/geo. Additionaly one can simply create another implementation for different API interactions. All this is possible without touching service itself.

#### Image preview

Angular-powered preview is used instead of simply opening a new window with bigger size image. Works well on mobile platform too.

### Shortcuts / limitations

#### TDD

Due to the nature of this project only some interesting core parts are unit tested. In real sized project TDD is my only way to go. Everything would be covered with tests, except outermost side-effect functionilaty.

This is also the case for frontend counterpart: __AngularJS__ promotes testability and makes it easy. And it is a bit sed not to use such a great opportunity.

#### Error handling

There is no or very limited error handling around the project. It is not the way to go for production. Proper error handling and reporting is invaluable for application stability in long-term run. __Fail Fast__ is a way to go.

#### Frontend

This is makes no sense to use __SMACSS__ inside such a small project. But for projects of mid and bigger size it is the proper way. __SCSS__ preprocessor ensures reusability and manageability. __Gulp__ would be used in the project of the real size to assist in assets management and build automation.

Since this is very simple one pade app I do not see reasons to use __Angular Routing (ngRoute)__ module. For the project with more than three simple views I would prefer to go in this direction.

Proper use of __HTML5 Polyglot__ markup is my preffered way of presenting documents. This means strict XML validations, closing tags, empty attributes, proper content-type (``application/xhtml+xml``) and no ``document.write()`` and others ugly DOM handling ways.

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

#### Configuration class

I decided to go wigh uniform Configuration container for this project. That means, every service that requires some degree of configuration - should expose an interface. All these interfaces then will be implemented by single uniform Configuration class. As the number of interfaces grows - aggregation of multiple configuration value objects that conforms to separated interfaces and proxied via uniformed class can be applied to reduce complexity.

#### Nice to have

- Twig caching
- AJAX queue management, centralized loaders and handlers
- Sorting, images per page, various filters
