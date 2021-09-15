<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Factory;

use Nette\Forms\Control;
use Nette\Forms\Form;
use ReflectionProperty;

interface InputFactoryInterface
{

	public function create(Form $form, string $name, ReflectionProperty $property): Control;

}
