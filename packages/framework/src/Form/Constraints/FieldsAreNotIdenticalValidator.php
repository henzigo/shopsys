<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FieldsAreNotIdenticalValidator extends ConstraintValidator
{
    /**
     * @param array $values
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate(mixed $values, Constraint $constraint): void
    {
        if (!$constraint instanceof FieldsAreNotIdentical) {
            throw new UnexpectedTypeException($constraint, FieldsAreNotIdentical::class);
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $value1 = $propertyAccessor->getValue($values, $constraint->field1);
        $value2 = $propertyAccessor->getValue($values, $constraint->field2);

        if ($value1 === $value2) {
            $this->context->buildViolation($constraint->message)
                ->atPath($constraint->errorPath)
                ->addViolation();
        }
    }
}
