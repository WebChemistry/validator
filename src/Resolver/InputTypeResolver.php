<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Resolver;

use ReflectionNamedType;
use ReflectionProperty;
use WebChemistry\Validator\Attribute\InputType;

final class InputTypeResolver
{

	public function getInputType(ReflectionProperty $property): ?string
	{
		$type = $property->getAttributes(InputType::class)[0] ?? null;

		if ($type) {
			return $type->newInstance()->name;
		}

		$type = $property->getType();
		if ($type && $type instanceof ReflectionNamedType && $type->isBuiltin()) {
			return match ($type->getName()) {
				'bool' => 'checkbox',
				default => 'text',
			};
		}

		return 'text';
	}

}
