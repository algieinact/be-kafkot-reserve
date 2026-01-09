<?php

namespace App\Console\Commands;

use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TestCloudinaryUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudinary:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Cloudinary upload functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Cloudinary Configuration...');
        $this->newLine();

        // Check if CLOUDINARY_URL is set
        if (!env('CLOUDINARY_URL')) {
            $this->error('❌ CLOUDINARY_URL is not set in .env file');
            return Command::FAILURE;
        }

        $this->info('✅ CLOUDINARY_URL is configured');
        $this->newLine();

        // Test Cloudinary connection
        try {
            $cloudinary = new CloudinaryService();
            $this->info('✅ CloudinaryService initialized successfully');
            $this->newLine();

            // Show configuration (masked)
            $cloudinaryUrl = env('CLOUDINARY_URL');
            preg_match('/cloudinary:\/\/(\d+):(.+)@(.+)/', $cloudinaryUrl, $matches);

            if (count($matches) === 4) {
                $this->info('Cloudinary Configuration:');
                $this->line('  API Key: ' . $matches[1]);
                $this->line('  API Secret: ' . str_repeat('*', strlen($matches[2]) - 4) . substr($matches[2], -4));
                $this->line('  Cloud Name: ' . $matches[3]);
                $this->newLine();
            }

            $this->info('🎉 Cloudinary is configured correctly!');
            $this->newLine();
            $this->info('To test actual upload, use the API endpoint:');
            $this->line('  POST /api/reservations/{id}/upload-payment-proof');
            $this->line('  with multipart/form-data containing "payment_proof" image file');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Cloudinary initialization failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
