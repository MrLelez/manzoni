<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;
// NON importare Intervention\Image\Facades\Image per evitare conflitto
// Usa il Manager direttamente invece del Facade per evitare conflitti
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // o Imagick\Driver se usi Imagick

class ImageController extends Controller
{
    private ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Serve image by clean name: /img/cestino-roma-blue.jpg
     * NOW WITH INTERVENTION IMAGE PROCESSING INSTEAD OF REDIRECT
     */
    public function serve(Request $request, string $cleanName)
    {
        // Rimuovi estensione dal clean name se presente
        $cleanName = preg_replace('/\.(jpg|jpeg|png|webp|gif)$/i', '', $cleanName);
        
        $image = Image::where('clean_name', $cleanName)->first();
        
        if (!$image) {
            abort(404, 'Image not found');
        }

        // Ottieni parametri per processing (opzionali)
        $width = $request->get('w', null);
        $height = $request->get('h', null);
        $quality = $request->get('q', 90);
        $format = $request->get('f', null);

        try {
            // Scarica l'immagine da AWS S3
            $imageContent = Storage::disk('s3')->get($image->aws_key);
            
            if (!$imageContent) {
                \Log::warning('Image content not found on S3', [
                    'clean_name' => $cleanName,
                    'aws_key' => $image->aws_key
                ]);
                
                $correctUrl = Storage::disk('s3')->url($image->aws_key);
                return redirect($correctUrl, 302);
            }

            // INTERVENTION IMAGE v3 - Usa ImageManager direttamente
            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            
            // Leggi l'immagine con read() invece di make()
            $interventionImage = $manager->read($imageContent);
            
            // Applica trasformazioni se richieste
            if ($width || $height) {
                // v3 syntax: scale() invece di resize()
                if ($width && $height) {
                    $interventionImage = $interventionImage->scale($width, $height);
                } elseif ($width) {
                    $interventionImage = $interventionImage->scaleDown($width);
                } elseif ($height) {
                    $interventionImage = $interventionImage->scaleDown(height: $height);
                }
            }

            // Determina formato output
            $outputFormat = $format ?: 'jpg';
            $mimeType = $this->getMimeType($outputFormat);
            
            // Encode l'immagine - v3 syntax
            $encodedImage = match($outputFormat) {
                'png' => $interventionImage->toPng(),
                'webp' => $interventionImage->toWebp($quality),
                'jpg', 'jpeg' => $interventionImage->toJpeg($quality),
                default => $interventionImage->toJpeg($quality)
            };
            
            \Log::info('Image processed successfully with Intervention v3', [
                'clean_name' => $cleanName,
                'width' => $width,
                'height' => $height,
                'quality' => $quality,
                'format' => $outputFormat,
                'original_size' => strlen($imageContent),
                'processed_size' => strlen($encodedImage)
            ]);
            
            // Restituisci l'immagine processata
            return response($encodedImage)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT')
                ->header('X-Image-Processed', 'intervention-v3')
                ->header('X-Image-Source', 'aws-s3');

        } catch (\Exception $e) {
            \Log::error('Error processing image with Intervention v3', [
                'clean_name' => $cleanName,
                'aws_key' => $image->aws_key ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: redirect ad AWS
            try {
                $correctUrl = Storage::disk('s3')->url($image->aws_key);
                return redirect($correctUrl, 302);
            } catch (\Exception $fallbackError) {
                \Log::error('Fallback redirect failed', [
                    'clean_name' => $cleanName,
                    'fallback_error' => $fallbackError->getMessage()
                ]);
                abort(500, 'Image processing failed');
            }
        }
    }

    /**
     * Helper per determinare MIME type dal formato
     */
    private function getMimeType(string $format): string
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg', 
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
        ];

        return $mimeTypes[strtolower($format)] ?? 'image/jpeg';
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

    // =========================================================================
    // ADMIN METHODS - gallery e pagina immagini con i suoi metodi (14_07_2025)
    // =========================================================================

    /**
     * Admin gallery interface
     */
    public function adminIndex(Request $request)
    {
        // Controlla permission can-edit-images
        if (!auth()->user() || !auth()->user()->can('can-edit-images')) {
            abort(403, 'Non hai i permessi per gestire le immagini');
        }

        // Render Livewire component
        return view('admin.images.index');
    }

    /**
     * Admin image detail
     */
    public function adminShow(Image $image)
    {
        // Controlla permission can-edit-images
        if (!auth()->user() || !auth()->user()->can('can-edit-images')) {
            abort(403, 'Non hai i permessi per gestire le immagini');
        }

        $image->load(['imageable', 'uploader', 'productsAsPrimary', 'productsAsBeauty']);
        
        // Incrementa usage_count
        $image->increment('usage_count');
        $image->update(['last_accessed_at' => now()]);

        // Prodotti disponibili per associazione
        $availableProducts = \App\Models\Product::active()
                                  ->select('id', 'name', 'sku')
                                  ->orderBy('name')
                                  ->get();

        // Immagini simili (stesso tipo, dimensioni simili)
        $similarImages = Image::where('id', '!=', $image->id)
                            ->where('type', $image->type)
                            ->when($image->width, function($q) use ($image) {
                                $q->whereBetween('width', [$image->width * 0.8, $image->width * 1.2]);
                            })
                            ->limit(6)
                            ->get();

        return view('admin.images.show', compact('image', 'availableProducts', 'similarImages'));
    }

    /**
     * Upload multiple images (admin)
     */
    public function adminStore(Request $request)
    {
        // Controlla permission can-edit-images
        if (!auth()->user() || !auth()->user()->can('can-edit-images')) {
            abort(403, 'Non hai i permessi per gestire le immagini');
        }

        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,webp|max:10240',
            'type' => 'required|in:gallery,beauty,product,category,content',
            'product_id' => 'nullable|exists:products,id',
            'beauty_category' => 'nullable|in:main,slideshow,header',
        ]);

        $uploadedImages = [];
        $errors = [];

        foreach ($request->file('images') as $file) {
            try {
                $imageable = null;
                if ($request->filled('product_id')) {
                    $imageable = \App\Models\Product::find($request->product_id);
                }

                $image = $this->imageService->uploadImage(
                    $file,
                    $imageable,
                    $request->type,
                    auth()->id()
                );

                // Imposta beauty category se specificata
                if ($request->filled('beauty_category') && $request->type === 'beauty') {
                    $image->update(['beauty_category' => $request->beauty_category]);
                }

                $uploadedImages[] = $image;

            } catch (\Exception $e) {
                $errors[] = "Errore upload {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        if (count($uploadedImages) > 0) {
            session()->flash('success', 'Caricate ' . count($uploadedImages) . ' immagini');
        }

        if (count($errors) > 0) {
            session()->flash('errors', $errors);
        }

        return redirect()->route('admin.images.index');
    }

    /**
     * Associa immagine a prodotto
     */
    public function associateToProduct(Request $request, Image $image)
    {
        if (!auth()->user() || !auth()->user()->can('can-edit-images')) {
            abort(403, 'Non hai i permessi per gestire le immagini');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:gallery,beauty',
            'beauty_category' => 'nullable|in:main,slideshow,header',
            'set_as_primary' => 'boolean'
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);

        // Aggiorna associazione
        $image->update([
            'imageable_type' => \App\Models\Product::class,
            'imageable_id' => $product->id,
            'type' => $request->type,
            'beauty_category' => $request->beauty_category,
            'usage_count' => $image->usage_count + 1
        ]);

        // Imposta come primary se richiesto
        if ($request->boolean('set_as_primary')) {
            $product->setPrimaryImage($image);
        }

        return response()->json([
            'success' => true,
            'message' => "Immagine associata al prodotto {$product->name}"
        ]);
    }

    /**
     * Disassocia immagine da prodotto
     */
    public function dissociateFromProduct(Image $image)
    {
        if (!auth()->user() || !auth()->user()->can('can-edit-images')) {
            abort(403, 'Non hai i permessi per gestire le immagini');
        }

        $oldProduct = $image->imageable;
        
        $image->update([
            'imageable_type' => null,
            'imageable_id' => null,
            'type' => 'temp',
            'beauty_category' => null
        ]);

        // Se era primary, resetta
        if ($oldProduct && $oldProduct instanceof \App\Models\Product && $oldProduct->primary_image_id === $image->id) {
            $oldProduct->clearPrimaryImage();
        }

        return response()->json([
            'success' => true,
            'message' => 'Immagine disassociata dal prodotto'
        ]);
    }

    /**
     * Ottimizza immagine
     */
    public function optimize(Request $request)
    {
        if (!auth()->user() || !auth()->user()->can('can-edit-images')) {
            abort(403, 'Non hai i permessi per gestire le immagini');
        }

        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:images,id',
            'quality' => 'integer|between:60,100',
            'convert_to_webp' => 'boolean'
        ]);

        $optimized = 0;
        $totalSavings = 0;

        foreach ($request->image_ids as $imageId) {
            $image = Image::find($imageId);
            
            try {
                $result = $this->imageService->optimizeImage(
                    $image, 
                    $request->get('quality', 85),
                    $request->boolean('convert_to_webp')
                );
                
                if ($result['optimized']) {
                    $optimized++;
                    $totalSavings += $result['savings'];
                }
                
            } catch (\Exception $e) {
                \Log::error("Optimization failed for image {$imageId}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Ottimizzate {$optimized} immagini. Risparmiati " . $this->formatBytes($totalSavings),
            'optimized_count' => $optimized,
            'total_savings' => $totalSavings
        ]);
    }

    /**
     * Elimina immagini (bulk)
     */
    public function bulkDelete(Request $request)
    {
        if (!auth()->user() || !auth()->user()->can('can-edit-images')) {
            abort(403, 'Non hai i permessi per gestire le immagini');
        }

        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:images,id'
        ]);

        $deleted = 0;
        foreach ($request->image_ids as $imageId) {
            $image = Image::find($imageId);
            if ($image) {
                $image->delete(); // Soft delete
                $deleted++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Eliminate {$deleted} immagini"
        ]);
    }

    /**
     * Aggiorna dettagli immagine
     */
    public function updateImage(Request $request, Image $image)
    {
        if (!auth()->user() || !auth()->user()->can('can-edit-images')) {
            abort(403, 'Non hai i permessi per gestire le immagini');
        }

        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'beauty_category' => 'nullable|in:main,slideshow,header'
        ]);

        $image->update($request->only([
            'alt_text', 
            'caption', 
            'tags', 
            'beauty_category'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Dettagli immagine aggiornati'
        ]);
    }

    /**
     * Sostituisci immagine (reupload)
     */
    public function replace(Request $request, Image $image)
    {
        if (!auth()->user() || !auth()->user()->can('can-edit-images')) {
            abort(403, 'Non hai i permessi per gestire le immagini');
        }

        $request->validate([
            'new_image' => 'required|image|mimes:jpeg,png,webp|max:10240'
        ]);

        try {
            // Backup old data
            $oldData = $image->only(['alt_text', 'caption', 'tags', 'type', 'beauty_category']);
            $associations = [
                'imageable_type' => $image->imageable_type,
                'imageable_id' => $image->imageable_id
            ];

            // Upload new image
            $newImage = $this->imageService->uploadImage(
                $request->file('new_image'),
                $image->imageable,
                $image->type,
                auth()->id()
            );

            // Transfer old data to new image
            $newImage->update(array_merge($oldData, $associations));

            // If old image was primary, make new image primary
            if ($image->isPrimaryForProduct()) {
                $product = \App\Models\Product::where('primary_image_id', $image->id)->first();
                if ($product) {
                    $product->setPrimaryImage($newImage);
                }
            }

            // Delete old image
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Immagine sostituita con successo',
                'new_image_id' => $newImage->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante sostituzione: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Statistiche per dashboard admin
     */
    private function getGalleryStats(): array
    {
        return [
            'total_images' => Image::count(),
            'gallery_images' => Image::where('type', 'gallery')->count(),
            'beauty_images' => Image::where('type', 'beauty')->count(),
            'orphan_images' => Image::whereNull('imageable_id')->count(),
            'unoptimized_images' => Image::where('is_optimized', false)->count(),
            'total_size_mb' => round(Image::sum('file_size') / 1024 / 1024, 2),
            'average_size_kb' => round(Image::avg('file_size') / 1024, 1)
        ];
    }

    /**
     * Formatta bytes in formato leggibile
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}