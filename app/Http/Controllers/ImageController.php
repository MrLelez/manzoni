<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;

class ImageController extends Controller
{
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Serve image by clean name: /img/cestino-roma-blue.jpg
     */
    public function serve(string $cleanName)
{
    $cleanName = preg_replace('/\.(jpg|jpeg|png|webp|gif)$/i', '', $cleanName);
    
    $image = Image::where('clean_name', $cleanName)->first();
    
    if (!$image) {
        abort(404, 'Image not found');
    }

    // FIX: NON usare $image->aws_url, genera l'URL corretto
    $correctUrl = Storage::disk('s3')->url($image->aws_key);
    
    \Log::info('S3 redirect', [
        'clean_name' => $cleanName,
        'aws_key' => $image->aws_key,
        'old_aws_url' => $image->aws_url,
        'new_correct_url' => $correctUrl
    ]);

    return redirect($correctUrl);
}

    /**
     * Genera l'URL S3 corretto basato sulla configurazione attuale
     */
    private function getCorrectS3Url(Image $image): string
    {
        // Usa il disk S3 configurato per generare l'URL corretto
        try {
            return Storage::disk('s3')->url($image->aws_key);
        } catch (\Exception $e) {
            \Log::error('Error generating S3 URL', [
                'aws_key' => $image->aws_key,
                'error' => $e->getMessage()
            ]);
            
            // Fallback: costruisci manualmente l'URL
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');
            
            return "https://{$bucket}.s3.{$region}.amazonaws.com/{$image->aws_key}";
        }
    }

    /**
     * Upload new image via API
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,webp|max:10240',
            'type' => 'required|in:product,category,content',
            'custom_name' => 'nullable|string|max:100',
            'alt_text' => 'nullable|string|max:255',
        ]);

        try {
            $image = $this->imageService->uploadImage(
                $request->file('image'),
                null, // imageable (se serve)
                $request->input('type'),
                auth()->id()
            );

            if ($request->has('alt_text')) {
                $image->update(['alt_text' => $request->input('alt_text')]);
            }

            return response()->json([
                'success' => true,
                'image' => [
                    'id' => $image->id,
                    'clean_name' => $image->clean_name,
                    'url' => $image->url,
                    'aws_url' => $image->aws_url,
                    'dimensions' => $image->formatted_dimensions,
                    'file_size' => $image->formatted_size,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Comando per aggiornare tutti gli URL S3 nel database
     */
    public function fixAllS3Urls()
    {
        if (!app()->environment('local')) {
            abort(403, 'Only available in local environment');
        }

        $images = Image::whereNotNull('aws_key')->get();
        $updated = 0;

        foreach ($images as $image) {
            $correctUrl = $this->getCorrectS3Url($image);
            
            if ($image->aws_url !== $correctUrl) {
                $image->update(['aws_url' => $correctUrl]);
                $updated++;
            }
        }

        return response()->json([
            'message' => "Updated {$updated} image URLs",
            'total_checked' => $images->count()
        ]);
    }
}