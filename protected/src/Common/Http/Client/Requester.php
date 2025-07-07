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
namespace Application\Common\Http\Client;

use Scaleum\Http\Client\RequesterAbstract;
use Scaleum\Http\InboundResponse;
use Scaleum\Http\OutboundRequest;

/**
 * Requester
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class Requester extends RequesterAbstract
{
    public function send(OutboundRequest $request): InboundResponse {
        $client = $this->transport ?? $this->getDefaultClient();
        return $client->send($request);
    }
}
/** End of Requester **/