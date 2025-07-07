<?php
declare (strict_types = 1);
/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Behaviors;

use Application\Services\ChronologAdapter;
use Chronolog\AutoInitialized;
use Chronolog\LogBook;
use Scaleum\Config\Config;
use Scaleum\Config\LoaderResolver;
use Scaleum\Core\DependencyInjection\Framework;
use Scaleum\Core\KernelEvents;
use Scaleum\Core\KernelProviderAbstract;
use Scaleum\Events\Event;
use Scaleum\Events\EventHandlerInterface;
use Scaleum\Events\EventManagerInterface;
use Scaleum\Logger\LoggerProviderInterface;
use Scaleum\Services\ServiceLocator;
use Scaleum\Stdlib\Exceptions\ERuntimeError;

/**
 * LoggerAdapter
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class LoggerAdapter extends KernelProviderAbstract implements EventHandlerInterface {
    public function register(EventManagerInterface $events): void {
        $events->on(KernelEvents::BOOTSTRAP, [$this, 'onBoot'], 1);
    }

    public function onBoot(Event $event): void {
        // LoggerManager
        if (! ($provider = $this->getKernel()->getContainer()->get(Framework::SVC_LOGGERS)) instanceof LoggerProviderInterface) {
            throw new ERuntimeError('Logger manager must implement LoggerProviderInterface');
        }

        $configDir = $this->getKernel()->getConfigDir();
        $filename = $configDir . '/log.php';
        if (file_exists($filename)) {
            $channels = ($this->getKernel()->getContainer()->get(LoaderResolver::class))->fromFile($filename);
            foreach ($channels as $key => $value) {
                if (is_array($value)) {
                    if (($book = AutoInitialized::turnInto($value)) instanceof LogBook) {
                        $provider->setLogger($key, new ChronologAdapter($book));
                    }
                }
            }
        }
    }
}

/** End of LoggerAdapter **/