<?php

declare(strict_types=1);

namespace jdavidbakr\UlidModelRoutes\Console\Commands;

use Illuminate\Console\Command;

class UlidModelRoutesCommand extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'ulidmodelroutes:placeholder';

    /**
     * The command description.
     */
    protected $description = 'Placeholder Artisan command shipped by the package ulidmodelroutes.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('UlidModelRoutes placeholder command executed.');

        return self::SUCCESS;
    }
}
