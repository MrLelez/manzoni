<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        // Remove .jpg extension if present
        $cleanName = preg_replace('/\.(jpg|jpeg|png|webp|gif)$/i', '', $cleanName);
        
        $image = $this->imageService->getImageByCleanName($cleanName);
        
        if (!$image) {
            abort(404, 'Image not found');
        }

        // Check if AWS URL is accessible
        if (!Storage::disk('s3')->exists($image->aws_key)) {
            abort(404, 'Image file not found');
        }

        // Redirect to AWS URL with cache headers
        return redirect($image->aws_url)
            ->header('Cache-Control', 'public, max-age=31536000'); // 1 year cache
    }

    /**
     * Upload new image via API
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,webp|max:10240', // 10MB
            'type' => 'required|in:product,category,content',
            'custom_name' => 'nullable|string|max:100',
            'alt_text' => 'nullable|string|max:255',
        ]);

        try {
            $image = $this->imageService->uploadImage(
                $request->file('image'),
                $request->input('type'),
                $request->input('custom_name'),
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
                    'clean_url' => $image->clean_url,
                    'aws_url' => $image->aws_url,
                    'dimensions' => $image->dimensions,
                    'file_size' => $image->formatted_size,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}