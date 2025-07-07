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

class NodeList extends ItemTag {
    protected $tag   = 'ul';
    protected $items = [];

    public static function getMap(): array {
        $map = [
            'items' => ArrayOfNodes::class,
        ];

        return array_merge(parent::getMap(), $map);
    }

    /**
     * @return array
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items): void {
        foreach ($items as $child) {
            if ($child instanceof NodeItem) {
                $child->setOwner($this);
            }
            $this->items[] = $child;
        }
    }

    public function toHtml() {
        $output = sprintf("<%s %s>", $this->getTag(), $this->getAttributesAsString());

        /** @var NodeItem $item */
        foreach ($this->getItems() as $item) {
            $output .= $item->toHtml();
        }

        $output .= sprintf("</%s>", $this->getTag());

        return $output;
    }
}

/* End of file NodeList.php */
