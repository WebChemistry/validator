<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Decorator;

use Nette\Forms\Control;
use Nette\Forms\Controls\BaseControl;
use ReflectionProperty;
use WebChemistry\Validator\Attribute\Caption;

final class CaptionDecorator implements ControlDecoratorInterface
{

	public function decorate(
		Control $control,
		ReflectionProperty $property,
		array $groups = [],
		array $options = []
	): void
	{
		if (!$control instanceof BaseControl) {
			return;
		}
		if ($control->caption) {
			return;
		}

		/** @var Caption|null $attribute */
		$attribute = ($property->getAttributes(Caption::class)[0] ?? null)?->newInstance();

		if ($attribute) {
			$control->caption = $attribute->name;
		} else {
			$control->caption = ucfirst($property->getName());
		}
	}

}
