<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImageService
{
    private string $disk = 's3';
    private array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    private int $maxSize = 10 * 1024 * 1024; // 10MB

    /**
     * Upload and process image
     */
    public function uploadImage(
        UploadedFile $file,
        string $type = 'product',
        ?string $customName = null,
        ?int $uploadedBy = null,
        $imageable = null
    ): Image {
        // Validazione
        $this->validateFile($file);

        DB::beginTransaction();
        try {
            // Generate clean name
            $cleanName = $this->generateCleanName($customName ?: $file->getClientOriginalName());

            // Generate AWS key
            $awsKey = $this->generateAwsKey($file, $type);

            // Calculate hash
            $hash = md5_file($file->getPathname());

            // Check for duplicates
            $existing = Image::where('hash_md5', $hash)->where('type', $type)->first();
            if ($existing) {
                DB::rollBack();
                return $existing; // Return existing image
            }

            // Upload to S3
            $awsUrl = Storage::disk($this->disk)->putFileAs(
                dirname($awsKey),
                $file,
                basename($awsKey),
                'public'
            );

            if (!$awsUrl) {
                throw new \Exception('Failed to upload to S3');
            }

            // Get image dimensions
            $dimensions = $this->getImageDimensions($file);

            // Create database record
            $image = Image::create([
                'clean_name' => $cleanName,
                'original_filename' => $file->getClientOriginalName(),
                'aws_key' => $awsKey,
                'aws_url' => Storage::disk($this->disk)->url($awsKey),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'width' => $dimensions['width'],
                'height' => $dimensions['height'],
                'hash_md5' => $hash,
                'type' => $type,
                'status' => 'active', // Semplifichiamo per ora
                'uploaded_by' => $uploadedBy,
                'imageable_type' => $imageable ? get_class($imageable) : null,
                'imageable_id' => $imageable?->id,
                'processed_at' => now(),
            ]);

            DB::commit();
            return $image;

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Cleanup S3 if upload was successful but DB failed
            if (isset($awsKey)) {
                Storage::disk($this->disk)->delete($awsKey);
            }
            
            throw $e;
        }
    }

    /**
     * Generate clean name for URL
     */
    private function generateCleanName(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $clean = Str::slug($name);
        
        // Ensure uniqueness
        $counter = 1;
        $originalClean = $clean;
        
        while (Image::where('clean_name', $clean)->exists()) {
            $clean = $originalClean . '-' . $counter;
            $counter++;
        }
        
        return $clean;
    }

    /**
     * Generate AWS S3 key with organized structure
     */
    private function generateAwsKey(UploadedFile $file, string $type): string
    {
        $date = Carbon::now();
        $uuid = Str::uuid();
        $extension = $file->getClientOriginalExtension();
        
        return sprintf(
            '%s/%s/%s/%s.%s',
            $type, // product, category, etc.
            $date->format('Y'),
            $date->format('m'),
            $uuid,
            $extension
        );
    }

    /**
     * Get image dimensions (simplified for now)
     */
    private function getImageDimensions(UploadedFile $file): array
    {
        try {
            $imageInfo = getimagesize($file->getPathname());
            return [
                'width' => $imageInfo[0] ?? null,
                'height' => $imageInfo[1] ?? null
            ];
        } catch (\Exception $e) {
            return ['width' => null, 'height' => null];
        }
    }

    /**
     * Validate uploaded file
     */
    private function validateFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Invalid file upload');
        }

        if (!in_array($file->getMimeType(), $this->allowedMimes)) {
            throw new \InvalidArgumentException('File type not allowed. Only JPEG, PNG, and WebP are supported.');
        }

        if ($file->getSize() > $this->maxSize) {
            throw new \InvalidArgumentException('File size too large. Maximum size is 10MB.');
        }
    }

    /**
     * Get image by clean name (for URL routing)
     */
    public function getImageByCleanName(string $cleanName): ?Image
    {
        return Image::where('clean_name', $cleanName)
                   ->where('status', 'active')
                   ->where('is_public', true)
                   ->first();
    }

    /**
     * Delete image and cleanup S3
     */
    public function deleteImage(Image $image): bool
    {
        DB::beginTransaction();
        try {
            // Delete from S3
            Storage::disk($this->disk)->delete($image->aws_key);

            // Soft delete from database
            $image->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error("Failed to delete image {$image->id}: " . $e->getMessage());
            return false;
        }
    }
}