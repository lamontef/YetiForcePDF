<?php
declare(strict_types=1);
/**
 * FontStyle class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class FontStyle
 */
class FontStyle extends Normalizer
{
	public function normalize($ruleValue): array
	{
		return ['font-style' => strtolower($ruleValue)];
	}
}