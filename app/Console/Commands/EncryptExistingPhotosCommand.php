<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use App\Services\SecureMediaService;

class EncryptExistingPhotosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secure:encrypt-existing-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt all existing legacy face and proctor snapshot photos with AES-256-CBC';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting encryption of legacy photos in public storage...');

        $directories = ['faces', 'proctor', 'verification'];
        $count = 0;

        foreach ($directories as $dir) {
            $files = Storage::disk('public')->allFiles($dir);
            foreach ($files as $file) {
                // Check if already in private local storage
                if (Storage::disk('local')->exists($file)) {
                    continue;
                }

                $content = Storage::disk('public')->get($file);
                if (empty($content)) {
                    continue;
                }

                // Check if already encrypted
                $isAlreadyEncrypted = false;
                try {
                    Crypt::decrypt($content, false);
                    $isAlreadyEncrypted = true;
                } catch (\Throwable $e) {
                    $isAlreadyEncrypted = false;
                }

                if (!$isAlreadyEncrypted) {
                    // Encrypt and save to private storage
                    SecureMediaService::storeEncrypted($file, $content, 'local');
                    $this->line("  [Encrypted & Moved] {$file}");
                    // Remove from public storage to eliminate exposure
                    Storage::disk('public')->delete($file);
                    $count++;
                } else {
                    // Just move to private storage
                    Storage::disk('local')->put($file, $content);
                    Storage::disk('public')->delete($file);
                    $count++;
                }
            }
        }

        $this->info("Completed! Encrypted and secured {$count} photos into private storage (storage/app/private/).");
        return Command::SUCCESS;
    }
}
