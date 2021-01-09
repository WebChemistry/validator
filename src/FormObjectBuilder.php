<?php declare(strict_types = 1);

namespace WebChemistry\Validator;

use JetBrains\PhpStorm\ArrayShape;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;
use ReflectionProperty;
use WebChemistry\Validator\Resolver\CaptionResolver;
use WebChemistry\Validator\Resolver\InputTypeResolver;
use WebChemistry\Validator\Resolver\RulesResolver;

final class FormObjectBuilder
{

	public const REQUIRED_TO_CAPTION = 'requiredToCaption';

	public function __construct(
		private InputTypeResolver $inputTypeResolver,
		private CaptionResolver $captionResolver,
		private RulesResolver $rulesResolver,
	)
	{
	}

	/**
	 * @param mixed[] $options
	 */
	#[ArrayShape([
		'requireToCaption' => 'bool',
	])]
	public function build(Form $form, string $class, array $fields, array $groups = [], array $options = []): void
	{
		$options[self::REQUIRED_TO_CAPTION] ??= false;

		foreach ($fields as $index => $value) {
			if (is_int($index)) {
				$index = $value;
			}

			$reflection = new ReflectionProperty($class, $index);

			$control = $this->createInput($form, $reflection, $value);

			$this->rulesResolver->apply($control, $reflection, $groups);
			$this->applyOptions($control, $options);
		}
	}

	/**
	 * @param mixed[] $options
	 */
	private function createInput(Form $form, ReflectionProperty $reflection, string $name): BaseControl
	{
		if (isset($form[$reflection->getName()])) {
			return $form[$reflection->getName()];
		}

		$type = ucfirst($this->inputTypeResolver->getInputType($reflection));
		$caption = ucfirst($this->captionResolver->getCaption($reflection, $name));

		return $form->{'add' . $type}($name, $caption);
	}

	private function applyOptions(BaseControl $control, array $options): void
	{
		if ($options[self::REQUIRED_TO_CAPTION] === true) {
			if ($control->isRequired()) {
				$control->caption = $control->caption . '*';
			}
		}
	}

}
