<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class HeureToDateTimeTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        return $value instanceof \DateTimeInterface ? $value->format('H:i') : '';
    }

    public function reverseTransform(mixed $value): mixed
    {
        return $value ? \DateTime::createFromFormat('H:i', $value) : null;
    }
}
