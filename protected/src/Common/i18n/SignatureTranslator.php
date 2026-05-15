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

use Scaleum\i18n\Translator;

class SignatureTranslator extends Translator {
    protected SignatureBuilder $signatureBuilder;
    protected array $cache = [];

    public function __construct(array $config = []) {
        parent::__construct($config);
        $this->signatureBuilder = new SignatureBuilder();
    }

    protected function getTranslation(string $message, string $textDomain, string $locale): mixed {
        // 1) прямой ключ
        if (($tpl = parent::getTranslation($message, $textDomain, $locale)) !== null) {
            return $tpl;
        }

        if (isset($this->cache[$textDomain][$locale][$message])) {
            return $this->cache[$textDomain][$locale][$message];
        }
                
        // 2) строгая сигнатура
        [$strictSig, $strictVals, $spans, $src] = $this->signatureBuilder->buildStrict($message);
        if ($strictSig !== '' && ($tpl = parent::getTranslation($strictSig, $textDomain, $locale)) !== null) {
            return $this->cache[$textDomain][$locale][$message] = $this->interpolate($tpl, $strictVals);
        }

        // 3) «релакс»-кандидат (с правильными пробелами)
        foreach ($this->signatureBuilder->buildRelaxedCandidates($spans, $src, $strictVals) as [$sig, $vals]) {
            if (($tpl = parent::getTranslation($sig, $textDomain, $locale)) !== null) {
                return $this->cache[$textDomain][$locale][$message] = $this->interpolate($tpl, $vals);
            }
        }

        return null;
    }

    protected function interpolate(string $template, array $values): string
    {
        return preg_replace_callback('/\{\{\s*(\d+)\s*}}/u', static function ($m) use ($values) {
            $i = (int)$m[1];
            return (string)($values[$i] ?? '');
        }, $template);
    }
}