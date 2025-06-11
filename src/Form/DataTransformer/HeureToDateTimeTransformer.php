<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class HeureToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($value): ?string
    {
        return $value instanceof \DateTime ? $value->format('H:i') : null;
    }

    public function reverseTransform($value): ?\DateTime
    {
        return \DateTime::createFromFormat('H:i', $value) ?: null;
    }
}
