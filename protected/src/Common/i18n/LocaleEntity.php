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
namespace Application\Common\i18n;

use Scaleum\Stdlib\Base\Hydrator;

/**
 * LocaleEntity
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class LocaleEntity extends Hydrator {
    protected string $iso       = 'xx_XX';
    protected array $isoAliases = [];
    protected string $language  = '';
    protected string $idiom     = '';
    protected bool $isActive    = false;

    /**
     * Get the value of isoAliases
     */
    public function getIsoAliases():array {
        $result = [ ...$this->isoAliases, $this->iso];
        $result = array_unique($result, SORT_STRING);
        return $result;
    }

    public function setIsoAliases(array | string $aliases): self {
        if (is_string($aliases)) {
            $aliases = explode(',', $aliases);
        }
        $this->isoAliases = array_map('trim', $aliases);

        return $this;
    }

    public function getCountry() {
        return explode('_', $this->iso)[1] ?? 'XX';
    }

    public function toArray(): array {        
        return [
            'iso'         => $this->iso,
            'language'    => $this->language,
            // 'idiom'       => $this->idiom,
            // 'iso_aliases' => $this->isoAliases,
            'is_active'   => $this->isActive,
            'country'     => $this->getCountry(),
        ];
    }

    /**
     * Get the value of isActive
     */
    public function getActive(): bool {
        return $this->isActive;
    }

    /**
     * Set the value of isActive
     *
     * @return  self
     */
    public function setActive(bool $value) {
        $this->isActive = $value;

        return $this;
    }

    

    /**
     * Get the value of iso
     */ 
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * Set the value of iso
     *
     * @return  self
     */ 
    public function setIso(string $iso)
    {
        $this->iso = $iso;

        return $this;
    }
}
/** End of LocaleEntity **/