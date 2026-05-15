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
use Scaleum\Stdlib\SAPI\Explorer;
use Scaleum\Stdlib\SAPI\SapiMode;

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
        $container = $this->getKernel()->getContainer();
        // Формируем объект конфигурации времени выполнения приложения
        // Это обеспечит доступ к основным директориям приложения и окружению сервисам без необходимости
        // передавать их в конструктор каждого сервиса(доступ через трейт конфига).
        $runtime = [
            'configDir'   => $this->getKernel()->getConfigDir(),
            'appDir'      => $this->getKernel()->getApplicationDir(),
            'environment' => $this->getKernel()->getEnvironment(),
        ];

        // Базовые сервисы
        ServiceLocator::set('config', $config = new Config(['runtime' => $runtime], '.', $container->get(LoaderResolver::class)));

        // Сервисы, которые зависят от SAPI режима
        if (Explorer::getTypeFamily() !== SapiMode::CONSOLE) {
            ServiceLocator::set('router', $container->get('router'));
        }

        $configDir = $config->get('runtime.configDir');
        // Можно выполнить загрузку конфигураций приложения(если нужно)
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