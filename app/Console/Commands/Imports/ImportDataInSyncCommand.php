<?php

namespace App\Console\Commands\Imports;

use Illuminate\Console\Command;

class ImportDataInSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imports:data-in-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if(app()->isProduction()) {
            $this->error('This command is not allowed in production');
            return;
        }

        $this->call(ImportFieldsFromExcelCommand::class);
        $this->call(ImportCategoriesFromExcelCommand::class);
        $this->call(ImportSuppliersFromExcelCommand::class);
    }
}
