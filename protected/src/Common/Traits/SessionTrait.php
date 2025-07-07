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

use Scaleum\Services\ServiceLocator;
use Scaleum\Session\SessionInterface;
use Scaleum\Stdlib\Exceptions\ERuntimeError;
/**
 * SessionTrait
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
trait SessionTrait
{
    public function getSession(): SessionInterface {
        $result = ServiceLocator::get('session');
        if (! $result instanceof SessionInterface) {
            throw new ERuntimeError('Session service is not set or invalid');
        }
        return $result;
    }
}
/** End of SessionTrait **/