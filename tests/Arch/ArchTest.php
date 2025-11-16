<?php

arch('package source code does not use debugging functions')
    ->expect('Isaac\CrudGenerator')
    ->not->toUse(['dd', 'dump', 'ray', 'var_dump', 'print_r']);

arch('all classes in Commands namespace are console commands')
    ->expect('Isaac\CrudGenerator\Commands')
    ->toExtend('Illuminate\Console\Command');

arch('all classes in Generators namespace end with Generator')
    ->expect('Isaac\CrudGenerator\Generators')
    ->toHaveSuffix('Generator');

arch('parsers use appropriate naming')
    ->expect('Isaac\CrudGenerator\Parsers')
    ->classes()
    ->toHaveSuffix('Parser')
    ->ignoring('Isaac\CrudGenerator\Parsers\ValidationRuleBuilder');

arch('builders use appropriate naming')
    ->expect('Isaac\CrudGenerator\Parsers\ValidationRuleBuilder')
    ->toHaveSuffix('Builder');

arch('all classes use strict types')
    ->expect('Isaac\CrudGenerator')
    ->toUseStrictTypes();
