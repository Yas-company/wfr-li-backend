<?php

namespace App\Console\Commands\Imports;

use App\Models\User;
use App\Models\ImportedFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Imports\Excel\ProductImport;
use App\Imports\Excel\CategoryImport;
use App\Imports\Excel\SupplierImport;
use Illuminate\Support\Facades\Storage;

class ImportSuppliersFromExcel extends Command
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
    protected $description = 'Import all suppliers.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dirs = Storage::disk('imports')->directories('suppliers');

        if (empty($dirs)) {
            $this->error('No directories found in storage/app/imports/');
            return Command::FAILURE;
        }

        try {
            DB::beginTransaction();

            foreach ($dirs as $dir)
            {
                if (ImportedFile::where('file_name', $dir)->exists())
                {
                    $this->info('Skipping already imported suppliers: ' . $dir);
                    continue;
                }

                $this->output->title('Starting import - ' . $dir);

                $files = array_filter(
                    Storage::disk('imports')->allFiles($dir),
                    fn($file) => str_ends_with(strtolower($file), '.xlsx')
                );

                if (empty($files))
                {
                    $this->error('No .xlsx files found in storage/app/imports/suppliers');
                    return Command::FAILURE;
                }

                $files = collect($files);

                $supplierFile = $files->first(fn ($item) => str_ends_with($item, 'supplier.xlsx'));

                if(!$supplierFile)
                {
                    $this->error('No supplier file found in storage/app/imports/suppliers');
                    return Command::FAILURE;
                }

                $supplierFilePath = Storage::disk('imports')->path($supplierFile);
                $createdSupplier = $this->importSuppliers($supplierFilePath );

                $categoriesFile = $files->first(fn ($item) => str_ends_with($item, 'categories.xlsx'));
                $categoriesFilePath = Storage::disk('imports')->path($categoriesFile);
                $this->importCategories($categoriesFilePath, $createdSupplier->id);

                $productsFile = $files->first(fn ($item) => str_ends_with($item, 'products.xlsx'));
                $productsFilePath = Storage::disk('imports')->path($productsFile);
                $this->importProducts($productsFilePath, $createdSupplier->id, $dir);

                ImportedFile::create([
                    'file_name' => $dir,
                    'file_hash' => hash('md5', $dir),
                    'imported_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to import ' . $dir . ': ' . $e->getMessage());
        }

        $this->output->success('Import completed successfully');
        return Command::SUCCESS;
    }

    protected function importSuppliers(string $supplierFilePath): User
    {
        $supplierImport = new SupplierImport();
        $supplierImport->withOutput($this->output)->import($supplierFilePath);

        return $supplierImport->createdSupplier();
    }

    protected function importCategories(string $categoriesFilePath, int $supplierId): void
    {
        (new CategoryImport($supplierId))->withOutput($this->output)->import($categoriesFilePath);
    }

    protected function importProducts(string $productsFilePath, int $supplierId, string $imagesPath): void
    {
        (new ProductImport($supplierId, $imagesPath))->withOutput($this->output)->import($productsFilePath);
    }
}
