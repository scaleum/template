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

namespace Application\Commands;

use Scaleum\Console\CommandAbstract;
use Scaleum\Console\Contracts\CommandInterface;
use Scaleum\Console\Contracts\ConsoleRequestInterface;
use Scaleum\Console\Contracts\ConsoleResponseInterface;
use Scaleum\Console\Response;

/**
 * PasswordHashCommand
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class PasswordHashCommand extends CommandAbstract implements CommandInterface {
    public function execute(ConsoleRequestInterface $request): ConsoleResponseInterface {
        $this->getOptions()
            ->setOpts(["p:"])
            ->setOptsLong(["password:"])
            ->setArgs($request->getRawArguments())
            ->parse();

        $password = $this->getOptions()->get('password') ?? $this->getOptions()->get('p');

        if (empty($password)) {
            $response = new Response();
            $response->setStatusCode(ConsoleResponseInterface::STATUS_INVALID_PARAMS);
            $response->setContent("Password is required.\n");
            return $response;
        }

        $response = new Response();
        $response->setStatusCode(ConsoleResponseInterface::STATUS_SUCCESS);
        $response->setContent("Password hash: " . password_hash($password, PASSWORD_DEFAULT) . "\n");
        return $response;
    }
}
/** End of PasswordHashCommand **/