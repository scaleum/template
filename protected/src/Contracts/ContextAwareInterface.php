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

namespace Application\Contracts;


/**
 * ContextAwareInterface
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
interface ContextAwareInterface
{
    public function setContext(object $context): static;
    public function getContext(): ?object;
}
/** End of ContextAwareInterface **/