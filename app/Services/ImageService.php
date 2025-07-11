<?php
// app/Services/ImageService.php - FIXED VERSION

namespace App\Services;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as InterventionImage;

class ImageService
{
    protected $disk = 's3';
    protected $baseUrl;
    
    public function __construct()
    {
        $this->baseUrl = config('filesystems.disks.s3.url', config('app.url'));
    }

    /**
     * ✨ FIXED: Upload image with proper parameter handling
     */
    public function uploadImage(
        UploadedFile $file, 
        $relatedModel = null, 
        string $type = 'general',
        $uploadedBy = null
    ): Image {
        // ✨ FIX: Handle both User object and user ID
        if (is_object($uploadedBy)) {
            $userId = $uploadedBy->id ?? null;
        } else {
            $userId = $uploadedBy; // Already an ID
        }

        // Validate file
        $this->validateFile($file);
        
        // Generate paths and names
        $cleanName = $this->generateCleanName($file, $type);
        $awsKey = $this->generateAwsKey($file, $type);
        
        // Check for duplicates
        $hash = md5_file($file->getPathname());
        $existingImage = Image::where('hash_md5', $hash)->first();
        
        if ($existingImage) {
            // Return existing image if duplicate
            return $existingImage;
        }
        
        // Upload to S3
        $path = Storage::disk($this->disk)->putFileAs(
            dirname($awsKey),
            $file,
            basename($awsKey),
            'public'
        );
        
        if (!$path) {
            throw new \Exception('Failed to upload file to S3');
        }
        
        // Get image dimensions
        $dimensions = $this->getImageDimensions($file);
        
        // Generate AWS URL
        $awsUrl = Storage::disk($this->disk)->url($path);
        
        // Create database record
        $image = Image::create([
            'clean_name' => $cleanName,
            'original_filename' => $file->getClientOriginalName(),
            'aws_key' => $path,
            'aws_url' => $awsUrl,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'width' => $dimensions['width'] ?? null,
            'height' => $dimensions['height'] ?? null,
            'hash_md5' => $hash,
            'type' => $type,
            'status' => 'active',
            'is_public' => true,
            'processing_status' => 'completed',
            'uploaded_by' => $userId, // ✨ FIXED: Use processed user ID
            'sort_order' => $this->getNextSortOrder($relatedModel),
            'imageable_type' => $relatedModel ? get_class($relatedModel) : null,
            'imageable_id' => $relatedModel ? $relatedModel->id : null,
        ]);
        
        return $image;
    }

    /**
     * Get next sort order for related model
     */
    private function getNextSortOrder($relatedModel): int
    {
        if (!$relatedModel) {
            return 1;
        }
        
        $maxOrder = Image::where('imageable_type', get_class($relatedModel))
                         ->where('imageable_id', $relatedModel->id)
                         ->max('sort_order');
        
        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Validate uploaded file
     */
    private function validateFile(UploadedFile $file): void
    {
        // Check file size (10MB max)
        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new \Exception('File size too large. Maximum 10MB allowed.');
        }
        
        // Check mime type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Invalid file type. Only JPEG, PNG, WebP, and GIF are allowed.');
        }
        
        // Check if file is actually an image
        try {
            $imageInfo = getimagesize($file->getPathname());
            if (!$imageInfo) {
                throw new \Exception('File is not a valid image.');
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to process image: ' . $e->getMessage());
        }
    }

    /**
     * Generate clean name for URL
     */
    private function generateCleanName(UploadedFile $file, string $type): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        // Clean the name
        $cleanName = Str::slug($name);
        
        // Add type prefix if not general
        if ($type !== 'general') {
            $cleanName = $type . '-' . $cleanName;
        }
        
        // Add timestamp to avoid conflicts
        $cleanName .= '-' . time();
        
        // Ensure uniqueness
        $counter = 1;
        $originalCleanName = $cleanName;
        while (Image::where('clean_name', $cleanName)->exists()) {
            $cleanName = $originalCleanName . '-' . $counter;
            $counter++;
        }
        
        return $cleanName;
    }

    /**
     * Generate AWS key (path in S3)
     */
    private function generateAwsKey(UploadedFile $file, string $type): string
    {
        $extension = $file->getClientOriginalExtension();
        $year = date('Y');
        $month = date('m');
        $uuid = Str::uuid();
        
        return "{$type}/{$year}/{$month}/{$uuid}.{$extension}";
    }

    /**
     * Get image dimensions
     */
    private function getImageDimensions(UploadedFile $file): array
    {
        try {
            $imageInfo = getimagesize($file->getPathname());
            return [
                'width' => $imageInfo[0] ?? null,
                'height' => $imageInfo[1] ?? null,
            ];
        } catch (\Exception $e) {
            return ['width' => null, 'height' => null];
        }
    }

    /**
     * Get image by clean name
     */
    public function getImageByCleanName(string $cleanName): ?Image
    {
        return Image::where('clean_name', $cleanName)
                   ->where('status', 'active')
                   ->first();
    }

    /**
     * Delete image
     */
    public function deleteImage(Image $image): bool
    {
        try {
            // Delete from S3
            if ($image->aws_key && Storage::disk($this->disk)->exists($image->aws_key)) {
                Storage::disk($this->disk)->delete($image->aws_key);
            }
            
            // Soft delete from database
            $image->delete();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to delete image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ✨ NEW: Batch reorder images
     */
    public function reorderImages(array $imageIds): bool
    {
        try {
            foreach ($imageIds as $index => $imageId) {
                Image::where('id', $imageId)->update([
                    'sort_order' => $index + 1
                ]);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to reorder images: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ✨ NEW: Set primary image
     */
    public function setPrimaryImage(Image $image, $relatedModel): bool
    {
        try {
            // Remove primary from all images of this model
            Image::where('imageable_type', get_class($relatedModel))
                 ->where('imageable_id', $relatedModel->id)
                 ->update(['is_primary' => false]);
            
            // Set this image as primary
            $image->update(['is_primary' => true]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to set primary image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ✨ NEW: Generate responsive URLs
     */
    public function getResponsiveUrls(Image $image): array
    {
        $baseUrl = $image->aws_url;
        
        return [
            'original' => $baseUrl,
            'large' => $this->addImageParams($baseUrl, ['w' => 1200, 'q' => 85]),
            'medium' => $this->addImageParams($baseUrl, ['w' => 800, 'q' => 80]),
            'small' => $this->addImageParams($baseUrl, ['w' => 400, 'q' => 75]),
            'thumbnail' => $this->addImageParams($baseUrl, ['w' => 200, 'h' => 200, 'c' => 'fill', 'q' => 70]),
        ];
    }

    /**
     * Add image transformation parameters to URL
     */
    private function addImageParams(string $url, array $params): string
    {
        // This assumes you're using a service like Cloudinary or similar
        // Modify according to your image processing service
        return $url . '?' . http_build_query($params);
    }

    /**
     * ✨ NEW: Generate optimized alt text
     */
    public function generateAltText(Image $image, $relatedModel = null): string
    {
        if ($image->alt_text) {
            return $image->alt_text;
        }

        $altText = '';
        
        if ($relatedModel) {
            $modelName = class_basename(get_class($relatedModel));
            $identifier = $relatedModel->name ?? $relatedModel->title ?? $relatedModel->id;
            $altText = "{$modelName}: {$identifier}";
        } else {
            $altText = $image->clean_name ? str_replace('-', ' ', $image->clean_name) : 'Image';
        }
        
        return ucfirst($altText);
    }

    /**
     * ✨ NEW: Batch update alt texts
     */
    public function generateAltTextsForModel($relatedModel): int
    {
        $images = Image::where('imageable_type', get_class($relatedModel))
                      ->where('imageable_id', $relatedModel->id)
                      ->where(function($query) {
                          $query->whereNull('alt_text')
                                ->orWhere('alt_text', '');
                      })
                      ->get();

        $updated = 0;
        foreach ($images as $image) {
            $altText = $this->generateAltText($image, $relatedModel);
            $image->update(['alt_text' => $altText]);
            $updated++;
        }

        return $updated;
    }
}