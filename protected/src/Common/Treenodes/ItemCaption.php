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

class ItemCaption extends ItemTag {
    protected $tag = 'span';
    protected $text;
    protected $description;
    protected $link;

    public static function getMap(): array {
        $map = [
            'link'        => true,
            'text'        => true,
            'description' => true,
        ];

        return array_merge(parent::getMap(), $map);
    }

    /**
     * @return mixed
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link): void {
        $this->link = $link;
    }

    public function toHtml() {
        $output = "";
        if (! empty($this->getLink())) {
            $output .= sprintf("<a href='%s' %s>%s</a>", $this->getLink(), $this->getAttributesAsString(), $this->getText());
        } else {
            $output .= sprintf("<div %s>%s</div>", $this->getAttributesAsString(), $this->getText());
        }

        if (! empty($this->getDescription())) {
            $output .= sprintf("<span>%s</span>", $this->getDescription());
        }

        return $output;
    }
}

/* End of file NodeCaption.php */
