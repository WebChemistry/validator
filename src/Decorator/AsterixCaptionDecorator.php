<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Decorator;

use Nette\Forms\Control;
use Nette\Forms\Controls\BaseControl;
use ReflectionProperty;

final class AsterixCaptionDecorator implements ControlDecoratorInterface
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
		if (!$control->isRequired()) {
			return;
		}

		$control->caption .= ' *';
	}

}
