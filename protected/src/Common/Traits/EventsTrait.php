<?php
declare(strict_types=1);
/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Common\Traits;

use Scaleum\Core\DependencyInjection\Framework;
use Scaleum\Events\EventManager;
use Scaleum\Services\ServiceLocator;
use Scaleum\Stdlib\Exceptions\ERuntimeError;

/**
 * EventsTrait
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
trait EventsTrait
{
    public function getEvents(): EventManager {
        $result = ServiceLocator::get(Framework::SVC_EVENTS);
        if (! $result instanceof EventManager) {
            throw new ERuntimeError('EventManager service is not set or invalid');
        }
        return $result;
    }
}
/** End of EventsTrait **/