<?php
declare (strict_types = 1);
/**
 * This file is part of Scaleum Application.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Common\i18n;

use Scaleum\Core\Contracts\HandlerInterface;
use Scaleum\Core\DependencyInjection\Framework;
use Scaleum\Events\Event;
use Scaleum\Events\EventManagerInterface;
use Scaleum\Http\CookieManager;
use Scaleum\i18n\Translator;
use Scaleum\Services\ServiceLocator;
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
    protected ?EventManagerInterface $eventManager;
    protected string $defaultLocale   = 'xx_XX';
    protected string $storageKey      = 'i18n_locale';
    protected ?Translator $translator = null;
    protected ?CookieManager $cookies = null;

    public function ready(): void {
        // On each request(after loading session service), set locale from session
        $this->getEventManager()->on(HandlerInterface::EVENT_GET_REQUEST, function (Event $event) {
            $locale = $this->getLocale()?->getIso() ?? $this->defaultLocale;
            $this->setLocale($locale);
        }, 0);
    }

    public function getLocale(?string $iso = null): ?LocaleEntity {
        $iso ??= $this->getCookies()->get($this->storageKey, $this->defaultLocale);
        if (isset($this->locales[$iso])) {
            return $this->locales[$iso];
        }
        return null;
    }

    public function setLocale(string $locale): static {
        if (! isset($this->locales[$locale])) {
            throw new EInvalidArgumentException(sprintf(
                'Locale "%s" is not defined', $locale
            ));
        }

        $this->locales[$locale]->setActive(true);

        // Store locale in session and cookies(for UI)
        $this->getCookies()->set($this->storageKey, $locale);

        // Set locale in translator
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
                'httponly' => false,
                'samesite' => 'Lax',
            ]);
        }
        return $this->cookies;
    }

    /**
     * Set the value of cookies
     *
     * @return  self
     */
    public function setCookies(array | CookieManager $cookies): static {
        if (is_array($cookies)) {
            $cookies = self::createInstance(['class' => CookieManager::class, ...$cookies]);
        }
        $this->cookies = $cookies;
        return $this;
    }

    public function getEventManager() {
        if ($this->eventManager === null) {
            if (! ($events = ServiceLocator::get(Framework::SVC_EVENTS, null)) instanceof EventManagerInterface) {
                throw new ERuntimeError(
                    sprintf(
                        "Events service `%s` is not found or is not an instance of `%a`, given `%s`.",
                        Framework::SVC_EVENTS,
                        EventManagerInterface::class,
                        is_object($events) ? get_class($events) : gettype($events)
                    )
                );
            }
            $this->eventManager = $events;
        }

        return $this->eventManager;
    }

    public function setEventManager(EventManagerInterface $events): static {
        $this->eventManager = $events;
        return $this;
    }
}
/** End of LocaleManager **/