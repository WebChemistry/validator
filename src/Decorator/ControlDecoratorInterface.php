<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Decorator;

use Nette\Forms\Control;
use ReflectionProperty;

interface ControlDecoratorInterface
{

	public function decorate(Control $control, ReflectionProperty $property, array $groups = [], array $options = []): void;

}
