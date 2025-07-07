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

namespace Application\Base;

use Application\Modules\Security\Authentication\Services\AuthManagerTrait;
use Application\Modules\Security\Authentication\Traits\UserTrait;
use Application\Modules\Security\Entities\UserEntry;
use Scaleum\Core\Contracts\HandlerInterface;
use Scaleum\Events\Event;
use Scaleum\Http\InboundRequest;
use Scaleum\Http\MethodDispatcherTrait;
use Scaleum\Http\OutboundResponse;

/**
 * RestfulController
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 * @version 1.2.0 (without authorization)
 */
abstract class RestfulController extends ControllerAbstract {
    use MethodDispatcherTrait;

    protected function getResponse(mixed $result, int $statusCode = 200, string $statusMessage = 'OK'): OutboundResponse {
        $body = [
            'result'         => $result,
            'status'         => $statusCode,
            'status_message' => $statusMessage,
        ];

        return new OutboundResponse(
            $statusCode,
            [],
            $body,
            '1.1'
        );
    }

    protected function getErrorResponse(int $statusCode = 500, string $statusMessage, mixed $result = null): OutboundResponse {
        return $this->getResponse($result, $statusCode, $statusMessage);
    }
}
/** End of RestfulController **/