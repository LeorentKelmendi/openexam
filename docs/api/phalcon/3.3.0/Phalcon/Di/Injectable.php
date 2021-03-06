<?php

namespace OpenExam\Library\DI {

  class Logger {

    /**
     * The system log.
     * @var \Phalcon\Logger\AdapterInterface
     */
    public $system;
    /**
     * The authentication log.
     * @var \Phalcon\Logger\AdapterInterface
     */
    public $auth;
    /**
     * The object access log.
     * @var \Phalcon\Logger\AdapterInterface
     */
    public $access;
    /**
     * The debug log.
     * @var \Phalcon\Logger\AdapterInterface
     */
    public $debug;
    /**
     * The debug log.
     * @var \Phalcon\Logger\AdapterInterface
     */
    public $access;
    /**
     * The cache log.
     * @var \Phalcon\Logger\AdapterInterface
     */
    public $cache;

  }

}

namespace Phalcon\Di {

  /**
   * Phalcon\Di\Injectable
   *
   * This class allows to access services in the services container by just only accessing a public property
   * with the same name of a registered service
   *
   * @property \Phalcon\Mvc\Dispatcher|\Phalcon\Mvc\DispatcherInterface $dispatcher
   * @property \Phalcon\Mvc\Router|\Phalcon\Mvc\RouterInterface $router
   * @property \Phalcon\Mvc\Url|\Phalcon\Mvc\UrlInterface $url
   * @property \Phalcon\Http\Request|\Phalcon\Http\RequestInterface $request
   * @property \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface $response
   * @property \Phalcon\Http\Response\Cookies|\Phalcon\Http\Response\CookiesInterface $cookies
   * @property \Phalcon\Filter|\Phalcon\FilterInterface $filter
   * @property \Phalcon\Flash\Direct $flash
   * @property \Phalcon\Flash\Session $flashSession
   * @property \Phalcon\Session\Adapter\Files|\Phalcon\Session\Adapter|\Phalcon\Session\AdapterInterface $session
   * @property \Phalcon\Events\Manager|\Phalcon\Events\ManagerInterface $eventsManager
   * @property \Phalcon\Db\AdapterInterface $db
   * @property \Phalcon\Security $security
   * @property \Phalcon\Crypt|\Phalcon\CryptInterface $crypt
   * @property \Phalcon\Tag $tag
   * @property \Phalcon\Escaper|\Phalcon\EscaperInterface $escaper
   * @property \Phalcon\Annotations\Adapter\Memory|\Phalcon\Annotations\Adapter $annotations
   * @property \Phalcon\Mvc\Model\Manager|\Phalcon\Mvc\Model\ManagerInterface $modelsManager
   * @property \Phalcon\Mvc\Model\MetaData\Memory|\Phalcon\Mvc\Model\MetadataInterface $modelsMetadata
   * @property \Phalcon\Mvc\Model\Transaction\Manager|\Phalcon\Mvc\Model\Transaction\ManagerInterface $transactionManager
   * @property \Phalcon\Assets\Manager $assets
   * @property \Phalcon\Di|\Phalcon\DiInterface $di
   * @property \Phalcon\Session\Bag|\Phalcon\Session\BagInterface $persistent
   * @property \Phalcon\Mvc\View|\Phalcon\Mvc\ViewInterface $view
   *
   * //
   * // OpenExam specific services:
   * //
   * @property \OpenExam\Library\Security\Acl $acl The access control (ACL) service.
   * @property \OpenExam\Library\Security\Authenticati $auth The user authentication service.
   * @property \OpenExam\Library\Core\Cache $cache The multi-level general purpose caching service.
   * @property \OpenExam\Library\Security\Capabilities $capabilities The capabilities service.
   * @property \OpenExam\Library\Catalog\DirectoryManager $catalog The catalog (directory information) service.
   * @property \Phalcon\Db\Adapter\Pdo $dbread Database connection object (for read only).
   * @property \Phalcon\Db\Adapter\Pdo $dbwrite Database connection object (for read/write).
   * @property \OpenExam\Library\Globalization\Locale\Locale $locale The locale service.
   * @property \OpenExam\Library\Core\Location $location The location and access information service.
   * @property \OpenExam\Library\DI\Logger $logger The logging service.
   * @property \OpenExam\Library\Render\RenderService $render The render service.
   * @property \OpenExam\Library\Globalization\Translate\Translate $tr The translation service.
   * @property \OpenExam\Library\Security\User $user The authenticated user.
   * @property \OpenExam\Library\Model\Audit\Service $audit The model audit service.
   * @property \OpenExam\Library\Monitor\Performance\Profiler $profiler System performance profiler.
   */
  abstract class Injectable implements \Phalcon\Di\InjectionAwareInterface, \Phalcon\Events\EventsAwareInterface {

    protected $_dependencyInjector;
    protected $_eventsManager;

    /**
     * Sets the dependency injector
     */
    public function setDI(\Phalcon\DiInterface $dependencyInjector) {

    }

    /**
     * Returns the internal dependency injector
     */
    public function getDI() {

    }

    /**
     * Sets the event manager
     */
    public function setEventsManager(\Phalcon\Events\ManagerInterface $eventsManager) {

    }

    /**
     * Returns the internal event manager
     */
    public function getEventsManager() {

    }

    /**
     * Magic method __get
     */
    public function __get($propertyName) {

    }

  }

}
