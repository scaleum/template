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


/**
 * ContextAwareTrait
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
trait ContextAwareTrait
{
    protected ?object $context = null;

    public function setContext(object $context): static
    {
        $this->context = $context;
        return $this;
    }

    public function getContext(): ?object
    {
        return $this->context;
    }
}
/** End of ContextAwareTrait **/