<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Decorator;

use Nette\Forms\Control;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use ReflectionNamedType;
use ReflectionProperty;

final class BuiltInTypeDecorator implements ControlDecoratorInterface
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
		if (!$property instanceof ReflectionNamedType) {
			return;
		}
		if (!$property->isBuiltin()) {
			return;
		}

		match ($property->getName()) {
			'int' => $control->addRule(Form::INTEGER),
			'float' => $control->addRule(Form::FLOAT),
		};
	}

}
