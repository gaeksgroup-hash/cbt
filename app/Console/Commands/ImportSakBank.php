<?php

namespace App\Console\Commands;

use App\Services\CBT\SakBankImporter;
use Illuminate\Console\Command;
use Throwable;

class ImportSakBank extends Command
{
    protected $signature = 'cbt:import-sak {file} {--dry-run}';

    protected $description = 'Validate or import the private canonical SAK question bank';

    public function handle(SakBankImporter $importer): int
    {
        try {
            $manifest = $importer->read($this->argument('file'));
            if (! $this->option('dry-run')) {
                $importer->import($manifest);
            }
            $this->info(($this->option('dry-run') ? 'PASS dry-run' : 'Imported').': '.count($manifest['questions']).' SAK items.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('FAIL: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
