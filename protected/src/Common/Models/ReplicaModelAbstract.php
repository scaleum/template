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
namespace Application\Common\Models;

use Scaleum\Stdlib\Helpers\ArrayHelper;
use Scaleum\Storages\PDO\ModelAbstract;
use Scaleum\Storages\PDO\ModelData;

/**
 * ReplicaModelAbstract
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
abstract class ReplicaModelAbstract extends ModelAbstract {
    public function replica(bool $asVirtual = true, array $stripKeys = []): static
    {
        /** @var static $clone */
        $clone       = clone $this;
        $clone->data = new ModelData($this->cloneData($this, $asVirtual, $stripKeys));
        return $clone;
    }

    protected function cloneData(self $model, bool $asVirtual = true, array $stripKeys = []): array {
        $attributes = $model->data->getAttributes();
        if ($asVirtual === true) {
            unset($attributes[$model->primaryKey]);
        }

        if (! empty($stripKeys)) {
            $attributes = ArrayHelper::filter($stripKeys, $attributes);
        }

        return $this->cloneAttributes($attributes, $asVirtual, $stripKeys);
    }

    protected function cloneAttributes(array $haystack, bool $asVirtual = true, array $stripKeys = []): array {
        foreach ($haystack as $key => $value) {
            if ($value instanceof self) {
                /** @var static $clone */
                $clone       = clone $value;
                $clone->data = new ModelData($this->cloneData($value, $asVirtual, $stripKeys));

                $haystack[$key] = $clone;
            } elseif (is_array($value)) {
                $haystack[$key] = $this->cloneAttributes($value, $asVirtual, $stripKeys);
            }
        }
        return $haystack;
    }
}
/** End of ReplicaModelAbstract **/