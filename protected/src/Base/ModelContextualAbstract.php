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

use Application\Contracts\ContextAwareInterface;
use Application\Common\Traits\ContextAwareTrait;
use Scaleum\Storages\PDO\ModelAbstract;

/**
 * ModelContextualAbstract
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
abstract class ModelContextualAbstract extends ModelAbstract implements ContextAwareInterface {
    use ContextAwareTrait;
    protected function createModelInstance(string $class): ModelAbstract {
        $model = new $class($this->getDatabase(), $this);
        if ($model instanceof ContextAwareInterface) {
            $model->setContext($this->getContext());
        }

        return $model;
    }
}
/** End of ModelContextualAbstract **/