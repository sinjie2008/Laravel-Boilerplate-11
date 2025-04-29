<?php

namespace Modules\ExcelManager\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excelmanager:publish-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the ExcelManager module assets to the public directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Publishing ExcelManager assets...');

        // Create the destination directory if it doesn't exist
        $publicPath = public_path('modules/excelmanager/css');
        
        if (!File::isDirectory($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        // Copy the CSS file to public directory
        $cssFile = module_path('ExcelManager', 'resources/css/excel-manager.css');
        $destinationFile = $publicPath . '/excel-manager.css';

        if (File::exists($cssFile)) {
            File::copy($cssFile, $destinationFile);
            $this->info('CSS file copied successfully.');
        } else {
            $this->error('CSS file not found: ' . $cssFile);
        }

        $this->info('ExcelManager assets published successfully!');
        
        return 0;
    }
} 