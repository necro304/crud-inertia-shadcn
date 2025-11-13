<?php

namespace isaac@example.com\crud-generator\Commands;

use Illuminate\Console\Command;

class crud-generatorCommand extends Command
{
    public $signature = 'isaac';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
