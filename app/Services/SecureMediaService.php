<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Encryption\DecryptException;

class SecureMediaService
{
    /**
     * Store raw binary data encrypted with AES-256-CBC in private storage.
     *
     * @param string $relativePath
     * @param string $binary
     * @param string $disk
     * @return string
     */
    public static function storeEncrypted(string $relativePath, string $binary, string $disk = 'local'): string
    {
        $encrypted = Crypt::encrypt($binary, false);

        $dir = dirname($relativePath);
        if ($dir && $dir !== '.') {
            Storage::disk($disk)->makeDirectory($dir);
        }

        Storage::disk($disk)->put($relativePath, $encrypted);

        return $relativePath;
    }

    /**
     * Read and decrypt image data into memory.
     * Fallback to unencrypted data if legacy file.
     *
     * @param string|null $relativePath
     * @return string|null
     */
    public static function getDecrypted(?string $relativePath): ?string
    {
        if (empty($relativePath)) {
            return null;
        }

        // 1. Check local private disk first
        $disk = 'local';
        if (!Storage::disk('local')->exists($relativePath)) {
            // 2. Fallback to public disk for legacy uploads
            if (Storage::disk('public')->exists($relativePath)) {
                $disk = 'public';
            } else {
                return null;
            }
        }

        $rawContent = Storage::disk($disk)->get($relativePath);
        if ($rawContent === null || $rawContent === '') {
            return null;
        }

        // Try decrypting with AES-256
        try {
            return Crypt::decrypt($rawContent, false);
        } catch (DecryptException $e) {
            // If DecryptException occurs, the file is an unencrypted legacy JPEG
            return $rawContent;
        } catch (\Throwable $e) {
            return $rawContent;
        }
    }

    /**
     * Delete file from both private and public storage disks.
     *
     * @param string|null $relativePath
     * @return bool
     */
    public static function delete(?string $relativePath): bool
    {
        if (empty($relativePath)) {
            return false;
        }

        $deleted = false;
        if (Storage::disk('local')->exists($relativePath)) {
            $deleted = Storage::disk('local')->delete($relativePath) || $deleted;
        }
        if (Storage::disk('public')->exists($relativePath)) {
            $deleted = Storage::disk('public')->delete($relativePath) || $deleted;
        }

        return $deleted;
    }
}
