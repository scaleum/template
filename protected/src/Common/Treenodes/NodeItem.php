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

use Application\Common\Helpers\UUIDHelper;

class NodeItem extends NodeList {
    protected $tag = 'li';
    protected $text;

    /** @var NodeList */
    protected $owner;

    /** @var NodeItem */
    protected $parent;

    public static function getMap(): array {
        $map = [
            'text' => ItemCaption::class,
        ];

        return array_merge(parent::getMap(), $map);
    }

    /**
     * @return mixed
     */
    public function getText() {
        if (! $this->text instanceof ItemCaption) {
            $this->text = ItemCaption::fromInput([]);
        }
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void {
        $this->text = $text;
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
                $child->setTag($this->getTag());
                $child->setParent($this);
            }
            $this->items[] = $child;
        }
    }

    public function hasItems() {
        return count($this->items) ? true : false;
    }

    /**
     * @return NodeItem
     */
    public function getParent(): NodeItem {
        return $this->parent;
    }

    /**
     * @param NodeItem $parent
     */
    public function setParent(NodeItem $parent): void {
        $this->parent = $parent;
    }

    public function toHtml() {
        $id = $this->getAttribute('id', UUIDHelper::rand(8));

        $output = sprintf("<%s %s>", $this->getTag(), $this->getAttributesAsString());
        if ($this->hasItems()) {
            $output .= sprintf("<input type='checkbox' id='c-%s'>", $id);
            $output .= sprintf("<label for='c-%s' class='node-label'>%s</label>", $id, $this->getText()->toHtml());

            $output .= sprintf("<%s>", $this->getOwner()->getTag());
            /** @var NodeItem $item */
            foreach ($this->getItems() as $item) {
                $output .= $item->toHtml();
            }
            $output .= sprintf("</%s>", $this->getOwner()->getTag());
        } else {
            $output .= sprintf("<div id='c-%s' class='node-label'>%s</div>", $id, $this->getText()->toHtml());
        }
        $output .= sprintf("</%s>", $this->getTag());

        return $output;
    }

    /**
     * @return NodeList
     */
    public function getOwner(): NodeList {
        return $this->owner;
    }

    /**
     * @param NodeList $owner
     */
    public function setOwner(NodeList $owner): void {
        $this->owner = $owner;

        /** @var NodeItem $item */
        foreach ($this->getItems() as $item) {
            $item->setOwner($owner);
        }
    }
}

/* End of file NodeItem.php */
