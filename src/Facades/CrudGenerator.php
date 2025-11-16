<?php

declare(strict_types=1);

namespace Isaac\CrudGenerator\Facades;

use Illuminate\Support\Facades\Facade;
use Isaac\CrudGenerator\Support\CrudGenerationResult;

/**
 * @method static CrudGenerationResult generate(string $resourceName, array $fieldDefinitions, array $options = [])
 *
 * @see \Isaac\CrudGenerator\CrudGenerator
 */
final class CrudGenerator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'crud-generator';
    }
}
