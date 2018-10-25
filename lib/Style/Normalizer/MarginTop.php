<?php
declare(strict_types=1);
/**
 * MarginTop class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class MarginTop
 */
class MarginTop extends Normalizer
{
    public function normalize($ruleValue): array
    {
        if (is_string($ruleValue)) {
            return ['margin-top' => $this->getNumberValues($ruleValue)[0]];
        }
        return ['margin-top' => $ruleValue];
    }
}
