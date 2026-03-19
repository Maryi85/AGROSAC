<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait UploadsImages
{
    /**
     * Upload an image to the specified folder in public storage.
     *
     * @param UploadedFile $file The file to upload
     * @param string $folder The folder name within 'app/public/photos/'
     * @return string|false The path to the stored file or false on failure
     */
    protected function uploadImage(UploadedFile $file, string $folder)
    {
        try {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $photoName = time() . '_' . $safeName . '.' . $extension;

            // Ensure directory exists
            $directory = storage_path("app/public/photos/{$folder}");
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Store file
            $path = Storage::disk('public')->putFileAs("photos/{$folder}", $file, $photoName);
            
            return $path;
        } catch (\Exception $e) {
            Log::error("Error uploading image to {$folder}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete an image from storage if it exists.
     *
     * @param string|null $path The path to the file
     * @return void
     */
    protected function deleteImage(?string $path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
