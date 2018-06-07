<?php
declare(strict_types=1);

namespace Tests\Fixture;

use Metamorph\Resource\AbstractResource;
use Metamorph\TransformerInterface;

class TestObjectToArrayTransformer implements TransformerInterface
{
    public function transform(AbstractResource $resource)
    {
    }
}