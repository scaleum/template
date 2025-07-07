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

use Scaleum\Config\Config;
use Scaleum\Config\LoaderResolver;
use Scaleum\Core\DependencyInjection\Framework;
use Scaleum\Core\KernelEvents;
use Scaleum\Core\KernelProviderAbstract;
use Scaleum\Events\Event;
use Scaleum\Events\EventHandlerInterface;
use Scaleum\Events\EventManagerInterface;
use Scaleum\Services\ServiceLocator;

/**
 * Services
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class Services extends KernelProviderAbstract implements EventHandlerInterface {
    public function register(EventManagerInterface $events): void {
        $events->on(KernelEvents::BOOTSTRAP, [$this, 'onBoot'], 10);
    }

    public function onBoot(Event $event): void {
        // Main config
        ServiceLocator::set('config', $config = new Config([], '.', $this->getKernel()->getContainer()->get(LoaderResolver::class)));

        $configDir = $this->getKernel()->getConfigDir();
        
        // Выполняем загрузку конфигураций приложения(если нужно)
        // $files = [$configDir . '/simple.ini', $configDir . '/simple.xml'];
        // $config = ServiceLocator::get('config');
        // if ($config instanceof Config) {
        //     $config->fromFiles($files);
        // }

        // Выполняем загрузку сервисов
        // Важно! Сервисы загружаются в "ленивом" режиме, т.е. они не инициализируются до тех пор, пока не будет вызван первый раз метод ServiceLocator::get($key)
        // Таким образом, если сервис использует системные сообщения (т.к. KernelEvents::START, ControllerResolver::CONTROLLER_RESOLVED и т.д.), они будут проигнорированы.
        // Выход из ситуации - используй 'eager' => true в конфигурации сервиса, либо передавай в $definnition уже инициализированный объект
        $config->fromFile("{$configDir}/services.php", "services");
        foreach ($config->get('services') as $key => $definition) {
            ServiceLocator::set($key, $definition, true);
        }
    }
}
/** End of Services **/