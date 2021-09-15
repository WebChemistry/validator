<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Factory;

use LogicException;
use Nette\Forms\Control;
use Nette\Forms\Form;
use Nette\Utils\Arrays;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use WebChemistry\Validator\Attribute\Input;

final class InputFactory implements InputFactoryInterface
{

	public function create(Form $form, string $name, ReflectionProperty $property): Control
	{
		/** @var Input|null $attribute */
		$attribute = ($property->getAttributes(Input::class)[0] ?? null)?->newInstance();

		if ($attribute) {
			$method = $this->toMethod($form, $attribute->name);

			return $form->$method($name, ...$attribute->arguments);
		} else if ($type = $property->getType()) {
			return $this->propertyTypeToControl($form, $name, $type);
		}

		return $form->addText($name);
	}

	private function propertyTypeToControl(Form $form, string $name, ReflectionNamedType $type): Control
	{
		if (!$type->isBuiltin()) {
			throw new LogicException(
				sprintf('%s only supports builtin types, %s given.', self::class, $type->getName())
			);
		}

		return match ($type->getName()) {
			'bool' => $form->addCheckbox($name),
			'int' => $form->addText($name)->setHtmlType('number'),
			'string' => $form->addText($name),
			'float' => $form->addText($name)->setHtmlType('number'),
			default => throw new LogicException('%s does not support type %s', self::class, $type->getName())
 		};
	}

	private function toMethod(Form $form, string $type): string
	{
		$method = 'add' . ucfirst($type);
		if (!method_exists($form, $method)) {
			throw new LogicException(sprintf('Method %s not exists in %s.', $method, get_debug_type($form)));
		}

		return $method;
	}

}
