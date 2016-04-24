<?php namespace BigCommerce;

use \BigCommerce\Domain\Entity\User;
use \BigCommerce\Infrastructure\Authentication\Authentication;
use \BigCommerce\Infrastructure\Authentication\AuthenticationService;
use \BigCommerce\Infrastructure\Authentication\PasswordHasher;
use \BigCommerce\Infrastructure\Configuration\Configuration;
use \BigCommerce\Infrastructure\Flickr\FlickrApiRepository;
use \BigCommerce\Infrastructure\Flickr\FlickrRestService;
use \BigCommerce\Infrastructure\Form\CsrfTokenManager;
use \BigCommerce\Infrastructure\Php\Curl;
use \BigCommerce\Infrastructure\Php\CurlProxy;
use \BigCommerce\Infrastructure\Registry\ServiceRegistry;
use \BigCommerce\Infrastructure\Routing\Router;
use \Doctrine\DBAL\Migrations\Configuration\Configuration as MigrationConfiguration;
use \Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Tools\Console\ConsoleRunner;
use \Doctrine\ORM\Tools\Setup;
use \Exception;
use \Symfony\Component\Console\Helper\HelperSet;
use \Symfony\Component\Yaml\Yaml;
use \Twig_Environment;
use \Twig_Loader_Filesystem;

class Bootstrap
{

    /**
     * @throws Exception
     * @return ServiceRegistry
     */
    public static function serviceRegistry()
    {
        $configuration = new Configuration(
            Yaml::parse(file_get_contents(
                static::pathTo('config', 'config.yml')
            ))
        );

        $entityManager = EntityManager::create(
            $configuration->databaseConnection(),
            Setup::createAnnotationMetadataConfiguration([
                static::pathTo('BigCommerce', 'Domain', 'Entity')
            ], false, null, null, false)
        );

        $passwordHasher = new PasswordHasher();

        return new ServiceRegistry([
            'flickr.repository' => new FlickrApiRepository(
                new FlickrRestService(
                    $configuration,
                    new Curl(
                        new CurlProxy()
                    )
                )
            ),
            'twig' => new Twig_Environment(
                new Twig_Loader_Filesystem([
                    static::pathTo('template')
                ])
            ),
            'router' => new Router(),
            'csrf' => new CsrfTokenManager(),
            'password' => $passwordHasher,
            'doctrine' => $entityManager,
            'user' => $entityManager->getRepository(User::class),
            'auth' => new AuthenticationService(
                $entityManager->getRepository(Authentication::class),
                $passwordHasher
            )
        ]);
    }

    /** @return HelperSet */
    public static function doctrineHelserSet()
    {
        $doctrineEntityManager = static::serviceRegistry()->service('doctrine'); /* @var $doctrineEntityManager EntityManager */
        $helperSet = ConsoleRunner::createHelperSet($doctrineEntityManager);

        $doctrineConnection = $doctrineEntityManager->getConnection();

        $migrationsConfiguration = new MigrationConfiguration($doctrineConnection);
        $migrationsPath = static::pathTo('migrations');
        $migrationsConfiguration->setMigrationsNamespace('Bigcommerce\Migrations');
        $migrationsConfiguration->setMigrationsDirectory($migrationsPath);
        $migrationsConfiguration->registerMigrationsFromDirectory($migrationsPath);

        $helperSet->set(
            new ConfigurationHelper($doctrineConnection, $migrationsConfiguration),
            'configuration'
        );

        return $helperSet;
    }

    /** @return string */
    public static function projectRootPath()
    {
        return dirname(__DIR__);
    }

    /**
     * @param string ...
     * @return string
     */
    public static function pathTo()
    {
        $pathParts = func_get_args();
        array_unshift($pathParts, static::projectRootPath());
        return implode(DIRECTORY_SEPARATOR, $pathParts);
    }
}
