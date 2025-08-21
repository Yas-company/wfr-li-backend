<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\File;
use Maantje\ReactEmail\Renderer;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('telescope:prune --hours=48')->daily();


// Build React Email TSX templates into Blade without sample data
Artisan::command('react-email:build', function () {
    $configured = config('react-email.template_directory', 'resources/emails/');
    // If config holds a file:/// URL, convert to local filesystem path for scanning
    if (is_string($configured) && str_starts_with($configured, 'file:///')) {
        $emailsTsxDir = str_replace('/', DIRECTORY_SEPARATOR, substr($configured, 8));
    } else {
        $emailsTsxDir = base_path($configured);
    }
    $bladeOutDir  = resource_path('views/emails');

    if (! File::exists($emailsTsxDir)) {
        $this->error("TSX directory not found: {$emailsTsxDir}");
        return 1;
    }

    File::ensureDirectoryExists($bladeOutDir);

    $files = collect(File::files($emailsTsxDir))
        ->filter(fn ($f) => str_ends_with($f->getFilename(), '.tsx'))
        ->values();

    if ($files->isEmpty()) {
        $this->warn('No TSX email templates found.');
        return 0;
    }

    $written = 0;
    foreach ($files as $file) {
        $view = pathinfo($file->getFilename(), PATHINFO_FILENAME);

        try {
            // Render without props; templates should use sensible defaults
            $result = Renderer::render($view, []);
        } catch (\Throwable $e) {
            $this->error("Failed rendering {$view}: " . $e->getMessage());
            continue;
        }

        $bladePath = $bladeOutDir . DIRECTORY_SEPARATOR . $view . '.blade.php';
        File::put($bladePath, $result['html'] ?? '');
        $written++;
        $this->info("Wrote {$bladePath}");
    }

    $this->info("Build complete. Files written: {$written}");
    return 0;
})->purpose('Compile TSX emails to Blade without loading sample data');

