<?php declare(strict_types = 1);

namespace WebChemistry\Validator\Constraint;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEntityValidator extends ConstraintValidator
{

	public function __construct(
		private EntityManagerInterface $em
	)
	{
	}

	public function validate($value, Constraint $constraint)
	{
		if (!$constraint instanceof UniqueEntity) {
			throw new UnexpectedTypeException($constraint, UniqueEntity::class);
		}

		if ($value === null || $value === '') {
			return;
		}

		$property = $this->context->getPropertyName();
		if (!$property) {
			throw new LogicException(sprintf('Property name is not given for constraint %s', $constraint::class));
		}

		$object = $this->context->getObject();
		if (!$object) {
			throw new LogicException(sprintf('Object is not given for constraint %s', $constraint::class));
		}

		$found = $this->em->getRepository($object::class)->findOneBy([
			$property => $value,
		]);

		if ($found && $object !== $found) {
			$this->context->buildViolation($constraint->message)
				->addViolation();
		}
	}

}
