<?php
declare(strict_types=1);
/**
 * Width class
 *
 * @package   YetiForcePDF\Style\Normalizer
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Normalizer;

/**
 * Class Width
 */
class Width extends Normalizer
{
	public function normalize($ruleValue): array
	{
		$matches = [];
		preg_match_all('/([0-9]+)([a-z]+)/', $ruleValue, $matches);
		$originalSize = (float)$matches[1][0];
		$originalUnit = $matches[2][0];
		$normalized = ['width' => $this->document->convertUnits($originalUnit, $originalSize)];
		return $normalized;
	}
}
