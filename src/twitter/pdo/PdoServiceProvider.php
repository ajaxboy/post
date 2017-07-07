<?php

namespace Twitter\Pdo;

use PDO;
use Twitter\Pdo as PdoLog;
use Silex\Application;
use Pimple\ServiceProviderInterface;
use Pimple\Container;
/**
 * A simple PDO service provider.
 * */
class PdoServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    // @codeCoverageIgnoreStart
    public function boot(Application $app)
    {
    }
    // @codeCoverageIgnoreEnd
    /**
     * {@inheritDoc}
     */
    public function register(Container $app)
    {
        $app['pdo.factory'] = $app->protect(
            function (
                $dsn,
                $username = null,
                $password = null,
                array $options = array()
            ) use ($app) {
                if ($app['debug'] && isset($app['monolog'])) {
                    $pdo = new PdoLog($dsn, $username, $password, $options);
                    $pdo->onLog(
                        function (array $entry) use ($app) {
                            $app['monolog']->addDebug(
                                sprintf(
                                    'PDO query: %s, values :%s',
                                    $entry['query'],
                                    var_export($entry['values'], true)
                                )
                            );
                        }
                    );
                    return $pdo;
                }
                $pdo = new PDO($dsn, $username, $password, $options);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $pdo;
            }
        );
        $app['pdo'] = function (Application $app) {
                foreach ($app['pdo.defaults'] as $name => $value) {
                    if (!isset($app[$name])) {
                        $app[$name] = $value;
                    }
                }
                return $app['pdo.factory'](
                    $app['pdo.dsn'],
                    $app['pdo.username'],
                    $app['pdo.password'],
                    $app['pdo.options']
                );
            };
        $app['pdo.defaults'] = array(
            'pdo.username' => null,
            'pdo.password' => null,
            'pdo.options' => array()
        );
    }
}