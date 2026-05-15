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

class SignatureBuilder {
    public const T_INT      = '<int>';
    public const T_FLOAT    = '<float>';
    public const T_RATIO    = '<int>/<int>';
    public const T_UUID     = '<uuid>';
    public const T_URL      = '<url>';
    public const T_EMAIL    = '<email>';
    public const T_STR      = '<str>';
    public const T_DATE     = '<date>';
    public const T_TIME     = '<time>';
    public const T_DATETIME = '<datetime>';

    /** Возвращает: [ $strictSig, $values, $spans, $source ] */
    public function buildStrict(string $source): array {
        [$strictSig, $values, $spans] = $this->scanAndBuild($source);
        return [$strictSig, $values, $spans, $source];
    }

    /**
     * Строит один «релакс»-кандидат: каждый литеральный промежуток между
     * динамикой превращается в: LEFT_ANCHOR + (space?) + <str> + (space?) + RIGHT_ANCHOR,
     * при этом сохраняются пробелы, которые были СНАРУЖИ правого/левого якоря (важно для "at <time>").
     * Возвращает список: [[ $signature, $values ]]
     */
    public function buildRelaxedCandidates(array $spans, string $source, array $strictValues): array {
        if (! $spans) {
            return [[self::T_STR, [1 => $source]]];
        }

        // Сформировать последовательность: lit/dyn/lit/dyn/.../lit
        $chunks = [];
        $pos    = 0;
        foreach ($spans as $s) {
            $lit      = substr($source, $pos, $s['start'] - $pos);
            $chunks[] = ['type' => 'lit', 'text' => $lit];
            $chunks[] = ['type' => 'dyn', 'tok' => $s];
            $pos      = $s['end'] + 1;
        }
        $chunks[] = ['type' => 'lit', 'text' => substr($source, $pos)];

        $sigParts = [];
        $vals     = [];
        $vi       = 1;

        foreach ($chunks as $idx => $ch) {
            if ($ch['type'] === 'lit') {
                $lit = $ch['text'];
                if ($lit === '') {
                    continue;
                }

                // Наружные пробелы литерала (голова/хвост)
                preg_match('/^(\s*)/u', $lit, $m1);
                $headWs = $m1[1] ?? '';
                preg_match('/(\s*)$/u', $lit, $m2);
                $tailWs = $m2[1] ?? '';

                $core = substr($lit, strlen($headWs), strlen($lit) - strlen($headWs) - strlen($tailWs));

                $left  = $this->firstToken($core);
                $right = $this->lastToken($core);

                // если нет «ядра» — весь литерал -> <str> (с вынесением крайних пробелов наружу)
                if ($core === '' || $left === '' || $right === '') {
                    $this->emitOuterSpaces($sigParts, $headWs);
                    $sigParts[] = self::T_STR;
                    $this->emitOuterSpaces($sigParts, $tailWs);
                    $vals[$vi++] = $core === '' ? '' : $core;
                    continue;
                }

                // середина между якорями
                $leftPos  = strpos($core, $left);
                $rightPos = strrpos($core, $right);
                $mid      = ($rightPos > $leftPos + strlen($left))
                    ? substr($core, $leftPos + strlen($left), $rightPos - ($leftPos + strlen($left)))
                    : '';

                // Пробелы вокруг mid внутри core
                $midLeftSpace  = preg_match('/^\s+/u', $mid) ? ' ' : '';
                $midRightSpace = preg_match('/\s+$/u', $mid) ? ' ' : '';
                $midTrim       = trim($mid);

                // ВЫВОД: headWs + left + (space?) + <str> + (space?) + right + tailWs
                $this->emitOuterSpaces($sigParts, $headWs);
                $sigParts[] = $left;
                if ($midTrim !== '') {
                    if ($midLeftSpace !== '') {
                        $sigParts[] = ' ';
                    }
                    $sigParts[] = self::T_STR;
                    if ($midRightSpace !== '') {
                        $sigParts[] = ' ';
                    }
                    $vals[$vi++] = $midTrim;
                }
                if ($right !== $left) {
                    $sigParts[] = $right;
                }

                // ВАЖНО: если после правого якоря в исходнике были пробелы (как в "at HH:MM"),
                // добавим их наружу — это вернёт пробел перед следующим токеном (<time>)
                $this->emitOuterSpaces($sigParts, $tailWs);
            } else {
                // динамический токен
                $t = $ch['tok']['type'];
                $v = $ch['tok']['value'];
                switch ($t) {
                case 'datetime':
                    $sigParts[]  = self::T_DATETIME;
                    $vals[$vi++] = $v;
                    break;
                case 'time':
                    $sigParts[]  = self::T_TIME;
                    $vals[$vi++] = $v;
                    break;
                case 'ratio':
                    $sigParts[]  = self::T_RATIO;
                    [$a, $b]     = explode('/', $v, 2);
                    $vals[$vi++] = $a;
                    $vals[$vi++] = $b;
                    break;
                case 'uuid':
                    $sigParts[]  = self::T_UUID;
                    $vals[$vi++] = $v;
                    break;
                case 'url':
                    $sigParts[]  = self::T_URL;
                    $vals[$vi++] = $v;
                    break;
                case 'email':
                    $sigParts[]  = self::T_EMAIL;
                    $vals[$vi++] = $v;
                    break;
                case 'float':
                    $sigParts[]  = self::T_FLOAT;
                    $vals[$vi++] = $v;
                    break;
                case 'int':
                    $sigParts[]  = self::T_INT;
                    $vals[$vi++] = $v;
                    break;
                }
            }
        }

        $signature = $this->normalize(implode('', $sigParts));
        return [[$signature, $vals]];
    }

    private function scanAndBuild(string $source): array {
        $len   = strlen($source);
        $i     = 0;
        $spans = [];

        while ($i < $len) {
            $substr = substr($source, $i);
            $prev   = ($i > 0) ? $source[$i - 1] : "\0";

            // datetime
            if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}/', $substr, $m)) {
                $next = $source[$i + strlen($m[0])] ?? "\0";
                if (! ctype_digit($prev) && ! ctype_digit($next)) {
                    $spans[] = ['type' => 'datetime', 'value' => $m[0], 'start' => $i, 'end' => $i + strlen($m[0]) - 1];
                    $i += strlen($m[0]);
                    continue;
                }
            }

            // time
            if (preg_match('/^\d{1,2}:\d{2}(?::\d{2})?/', $substr, $m)) {
                $next = $source[$i + strlen($m[0])] ?? "\0";
                if (! ctype_digit($prev) && ! ctype_digit($next)) {
                    $spans[] = ['type' => 'time', 'value' => $m[0], 'start' => $i, 'end' => $i + strlen($m[0]) - 1];
                    $i += strlen($m[0]);
                    continue;
                }
            }

            // ratio
            if (preg_match('/^\d+\/\d+/', $substr, $m)) {
                $next = $source[$i + strlen($m[0])] ?? "\0";
                if (! preg_match('/[A-Za-z_.-]/', $prev) && ! preg_match('/[A-Za-z_.-]/', $next)) {
                    $spans[] = ['type' => 'ratio', 'value' => $m[0], 'start' => $i, 'end' => $i + strlen($m[0]) - 1];
                    $i += strlen($m[0]);
                    continue;
                }
            }

            // uuid
            if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}/i', $substr, $m)) {
                $spans[] = ['type' => 'uuid', 'value' => $m[0], 'start' => $i, 'end' => $i + strlen($m[0]) - 1];
                $i += strlen($m[0]);
                continue;
            }

            // url
            if (preg_match('#^(https?://\S+|www\.\S+)#i', $substr, $m)) {
                $spans[] = ['type' => 'url', 'value' => $m[0], 'start' => $i, 'end' => $i + strlen($m[0]) - 1];
                $i += strlen($m[0]);
                continue;
            }

            // email
            if (preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+/', $substr, $m)) {
                $spans[] = ['type' => 'email', 'value' => $m[0], 'start' => $i, 'end' => $i + strlen($m[0]) - 1];
                $i += strlen($m[0]);
                continue;
            }

            // float (не часть имени файла)
            if (preg_match('/^\d+\.\d+/', $substr, $m)) {
                $next = $source[$i + strlen($m[0])] ?? "\0";
                if (! preg_match('/[A-Za-z_.-]/', $prev) && ! preg_match('/[A-Za-z_.-]/', $next)) {
                    $spans[] = ['type' => 'float', 'value' => $m[0], 'start' => $i, 'end' => $i + strlen($m[0]) - 1];
                    $i += strlen($m[0]);
                    continue;
                }
            }

            // int (не внутри слова/файла)
            if (preg_match('/^\d+/', $substr, $m)) {
                $next = $source[$i + strlen($m[0])] ?? "\0";
                if (! preg_match('/[A-Za-z_.-]/', $prev) && ! preg_match('/[A-Za-z_.-]/', $next)) {
                    $spans[] = ['type' => 'int', 'value' => $m[0], 'start' => $i, 'end' => $i + strlen($m[0]) - 1];
                    $i += strlen($m[0]);
                    continue;
                }
            }

            $i++;
        }

        // строгая сигнатура
        $sig    = '';
        $values = [];
        $vi     = 1;
        $cursor = 0;

        foreach ($spans as $dm) {
            if ($dm['start'] > $cursor) {
                $sig .= substr($source, $cursor, $dm['start'] - $cursor);
            }

            switch ($dm['type']) {
            case 'datetime':
                $sig .= self::T_DATETIME;
                $values[$vi++] = $dm['value'];
                break;
            case 'time':
                $sig .= self::T_TIME;
                $values[$vi++] = $dm['value'];
                break;
            case 'ratio':
                $sig .= self::T_RATIO;
                [$a, $b]       = explode('/', $dm['value'], 2);
                $values[$vi++] = $a;
                $values[$vi++] = $b;
                break;
            case 'uuid':
                $sig .= self::T_UUID;
                $values[$vi++] = $dm['value'];
                break;
            case 'url':
                $sig .= self::T_URL;
                $values[$vi++] = $dm['value'];
                break;
            case 'email':
                $sig .= self::T_EMAIL;
                $values[$vi++] = $dm['value'];
                break;
            case 'float':
                $sig .= self::T_FLOAT;
                $values[$vi++] = $dm['value'];
                break;
            case 'int':
                $sig .= self::T_INT;
                $values[$vi++] = $dm['value'];
                break;
            }
            $cursor = $dm['end'] + 1;
        }
        if ($cursor < strlen($source)) {
            $sig .= substr($source, $cursor);
        }

        return [$this->normalize($sig), $values, $spans];
    }

    private function firstToken(string $s): string {
        return preg_match('/^\s*(\S+)/u', $s, $m) ? $m[1] : '';
    }

    private function lastToken(string $s): string {
        return preg_match('/(\S+)\s*$/u', $s, $m) ? $m[1] : '';
    }

    private function emitOuterSpaces(array &$sigParts, string $ws): void {
        if ($ws === '') {
            return;
        }

        $sigParts[] = ' ';
    }

    private function normalize(string $s): string {
        $s = preg_replace('/\s+([,.;:!?])/', '$1', $s);
        $s = preg_replace('/\(\s+/', '(', $s);
        $s = preg_replace('/\s+\)/', ')', $s);
        $s = preg_replace('/\s{2,}/', ' ', $s);
        return trim($s);
    }
}
