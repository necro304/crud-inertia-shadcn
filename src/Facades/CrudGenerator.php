<?php

declare(strict_types=1);

namespace Necro304\CrudInertiaShadcn\Facades;

use Illuminate\Support\Facades\Facade;
use Necro304\CrudInertiaShadcn\Support\CrudGenerationResult;

/**
 * @method static CrudGenerationResult generate(string $resourceName, array $fieldDefinitions, array $options = [])
 *
 * @see \Necro304\CrudInertiaShadcn\CrudGenerator
 */
final class CrudGenerator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'crud-generator';
    }
}
