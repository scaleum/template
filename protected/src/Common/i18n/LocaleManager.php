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

use Scaleum\Http\CookieManager;
use Scaleum\i18n\Translator;
use Scaleum\Session\SessionInterface;
use Scaleum\Stdlib\Base\Hydrator;
use Scaleum\Stdlib\Base\InitTrait;
use Scaleum\Stdlib\Exceptions\EInvalidArgumentException;
use Scaleum\Stdlib\Exceptions\ERuntimeError;

/**
 * LocaleManager
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class LocaleManager extends Hydrator {
    use InitTrait;
    protected array $locales = [];

    protected string $defaultLocale      = 'xx_XX';
    protected ?SessionInterface $session = null;
    protected string $storageKey         = 'i18n_locale';
    protected ?Translator $translator    = null;
    protected ?CookieManager $cookies    = null;

    public function ready(): void {
        $this->setLocale($this->getSession()->get($this->storageKey, $this->defaultLocale));
    }

    public function getLocale(): ?LocaleEntity {
        $locale = $this->getSession()->get($this->storageKey, $this->defaultLocale);
        if (! isset($this->locales[$locale])) {
            throw new EInvalidArgumentException(sprintf(
                'Locale "%s" is not defined', $locale
            ));
        }
        return $this->locales[$locale];
    }

    public function setLocale(string $locale): static {
        if (! isset($this->locales[$locale])) {
            throw new EInvalidArgumentException(sprintf(
                'Locale "%s" is not defined', $locale
            ));
        }

        $this->locales[$locale]->setActive(true);
        
        $this->getCookies()->set($this->storageKey, $locale);
        $this->getSession()->set($this->storageKey, $locale);

        $this->getTranslator()->setLocale($locale);

        return $this;
    }
    /**
     * Get the value of locales
     */
    public function getLocales() {

        return $this->locales;
    }

    /**
     * Set the value of locales
     *
     * @return  self
     */
    public function setLocales(array $locales): static {
        foreach ($locales as $iso => $definition) {
            if (! is_array($definition)) {
                throw new \InvalidArgumentException(sprintf(
                    'Locale definition for "%s" must be an array, %s given', $iso, gettype($definition)
                ));
            }
            $this->locales[$iso] = new LocaleEntity($definition);
        }

        return $this;
    }

    /**
     * Get the value of session
     */
    public function getSession() {
        if (! ($session = $this->session) instanceof SessionInterface) {
            throw new ERuntimeError(
                sprintf(
                    "Session service is not defined or is not an instance of `%s`, given `%s`.",
                    SessionInterface::class,
                    is_object($session) ? get_class($session) : gettype($session)
                )
            );
        }
        return $this->session;
    }

    /**
     * Set the value of session
     *
     * @return  self
     */
    public function setSession(SessionInterface $session): static
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get the value of translator
     */
    public function getTranslator() {
        if (! ($translator = $this->translator) instanceof Translator) {
            throw new ERuntimeError(
                sprintf(
                    "Translator service is not defined or is not an instance of `%s`, given `%s`.",
                    Translator::class,
                    is_object($translator) ? get_class($translator) : gettype($translator)
                )
            );
        }
        return $this->translator;
    }

    /**
     * Set the value of translator
     *
     * @return  self
     */
    public function setTranslator(Translator $translator): static
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Get the value of cookies
     */
    public function getCookies() {
        if ($this->cookies === null) {
            $this->cookies = new CookieManager([
                'encode'   => false,
                'expire'   => 3600 * 24, // 1 day
                'salt'     => 'c8b7f832da924e4e90537f071f5e0542',
                'secure'   => false,
                'httpOnly' => false,
            ]);
        }
        return $this->cookies;
    }

    /**
     * Set the value of cookies
     *
     * @return  self
     */
    public function setCookies(CookieManager $cookies) {
        $this->cookies = $cookies;

        return $this;
    }
}
/** End of LocaleManager **/