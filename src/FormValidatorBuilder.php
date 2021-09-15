<?php declare(strict_types = 1);

namespace WebChemistry\Validator;

use Nette\Application\UI\Form;
use Nette\ComponentModel\Component;
use Nette\Forms\Control;
use ReflectionClass;
use ReflectionProperty;
use WebChemistry\Validator\Decorator\ControlDecoratorInterface;
use WebChemistry\Validator\Factory\InputFactoryInterface;

final class FormValidatorBuilder
{

	/**
	 * @param ControlDecoratorInterface[] $decorators
	 */
	public function __construct(
		private InputFactoryInterface $inputFactory,
		private array $decorators,
	)
	{
	}

	public function withDecorator(ControlDecoratorInterface ... $decorators): self
	{
		return new self($this->inputFactory, array_merge($this->decorators, $decorators));
	}

	/**
	 * @param string[]|mixed[] $fields
	 * @param mixed[] $options
	 */
	public function build(Form $form, string $class, array $fields = [], array $groups = [], array $options = []): void
	{
		if (!$fields) {
			$reflection = new ReflectionClass($class);
			$fields = array_map(
				fn (ReflectionProperty $property) => $property->getName(),
				$reflection->getProperties()
			);
		}

		foreach ($fields as $property => $name) {
			if (is_int($property)) {
				$property = $name;
			}

			$reflection = new ReflectionProperty($class, $property);
			$control = $form[$name] ?? $this->inputFactory->create($form, $name, $reflection);

			foreach ($this->decorators as $decorator) {
				$decorator->decorate($control, $reflection, $groups, $options);
			}
		}
	}

	/**
	 * @param string[]|mixed[] $fields
	 * @param mixed[] $options
	 */
	public function apply(Form $form, string $class, array $fields = [], array $groups = [], array $options = []): void
	{
		if (!$fields) {
			$fields = array_map(
				fn (Component $component) => $component->getName(),
				iterator_to_array($form->getComponents())
			);
		}

		foreach ($fields as $property => $name) {
			if (is_int($property)) {
				$property = $name;
			}

			$control = $form[$name] ?? null;
			if (!$control) {
				continue;
			}

			$reflection = new ReflectionProperty($class, $property);

			foreach ($this->decorators as $decorator) {
				$decorator->decorate($control, $reflection, $groups, $options);
			}
		}
	}

}
