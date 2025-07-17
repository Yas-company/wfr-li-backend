<?php

namespace App\Console\Commands\Imports;

use App\Models\User;
use App\Models\ImportedFile;
use App\Imports\SupplierImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportSuppliersFromExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-suppliers-from-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all .xlsx files from storage/app/imports/users into the users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = 'suppliers/';

        $files = array_filter(
            Storage::disk('imports')->allFiles($directory),
            fn($file) => str_ends_with(strtolower($file), '.xlsx')
        );

        if (empty($files)) {
            $this->error('No .xlsx files found in storage/app/imports/suppliers');
            return 1;
        }

        $this->info('Found ' . count($files) . ' .xlsx file(s) to import');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->info('Suppliers table truncated');

        $this->output->title('Starting import - Suppliers');

        foreach ($files as $file) {
            if (ImportedFile::where('file_name', $file)->exists()) {
                $this->info('Skipping already imported file: ' . $file);
                continue;
            }
            $fullPath = Storage::disk('imports')->path($file);
            $this->info('Importing file: ' . $file);

            try {
                (new SupplierImport)->withOutput($this->output)->import($fullPath);
                ImportedFile::create([
                    'file_name' => $file,
                    'file_hash' => hash_file('md5', $fullPath),
                    'imported_at' => now(),
                ]);
                $this->info('Successfully imported: ' . $file);
            } catch (\Exception $e) {
                $this->error('Failed to import ' . $file . ': ' . $e->getMessage());
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->output->success('Import completed successfully');
        return 0;
    }
}
