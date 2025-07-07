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
namespace Application\Common\Treenodes;

use Application\Base\TransformArray;

class ItemTag extends TransformArray {
    protected $tag        = 'li';
    protected $attributes = [];
    protected static $map = [
        'tag'        => true,
        'attributes' => true,
    ];

    /**
     * @return array
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

    public function getAttributesAsString($exclude = null) {
        $attributes = $this->getAttributes();
        if ($exclude !== null && is_array($exclude)) {
            $attributes = array_diff_key($attributes, array_fill_keys($exclude, 'empty'));
        }

        $result = '';
        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                $value = implode(' ', array_unique($value));
            }
            $result .= sprintf('%s="%s" ', $key, $value);
        }

        return $result;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void {
        $this->attributes = $attributes;
    }

    public function getAttribute($name, $default = null) {
        if ($this->hasAttribute($name)) {
            return $this->attributes[$name];
        }

        return $default;
    }

    public function hasAttribute($name) {
        return array_key_exists($name, $this->attributes);
    }

    public function setAttribute($name, $value) {
        $this->attributes[$name] = strip_tags($value);
    }

    /**
     * @return string
     */
    public function getTag(): string {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag(string $tag): void {
        $this->tag = $tag;
    }
}

/* End of file NodeTag.php */
