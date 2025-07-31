<?php

namespace App\Console\Commands\Imports;

use App\Imports\Excel\BuyerImport;
use App\Models\ImportedFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportBuyersFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-buyers-from-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all buyers.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = array_filter(
            Storage::disk('imports')->allFiles('buyers'),
            fn($file) => str_ends_with(strtolower($file), '.xlsx')
        );

        if(empty($files))
        {
            $this->error('No Files found in storage/app/imports/buyers');
            return Command::FAILURE;
        }

        foreach($files as $file)
        {
            if (ImportedFile::where('file_name', $file)->exists())
            {
                $this->info('Skipping already imported suppliers: ' . $file);
                continue;
            }

            $filePath = Storage::disk('imports')->path($file);
            (new BuyerImport())->withOutput($this->output)->import($filePath);

            ImportedFile::create([
                'file_name' => $file,
                'file_hash' => hash('md5', $file),
                'imported_at' => now(),
            ]);
        }

        $this->output->success('Import completed successfully');
        return Command::SUCCESS;
    }
}
