<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Resolver;

use ReflectionProperty;
use WebChemistry\Validator\Attribute\Caption;

final class CaptionResolver
{

	public function getCaption(ReflectionProperty $property, string $name): string
	{
		$caption = $property->getAttributes(Caption::class)[0] ?? null;

		return $caption?->newInstance()->name ?? $name;
	}

}
