<?php
declare(strict_types=1);
/**
 * FontSize class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class FontSize
 */
class FontSize extends Normalizer
{
    public function normalize($ruleValue): array
    {
        if (is_string($ruleValue)) {
            return ['font-size' => $this->getNumberValues($ruleValue)[0]];
        }
        // value is already parsed
        return ['font-size' => $ruleValue];
    }
}
