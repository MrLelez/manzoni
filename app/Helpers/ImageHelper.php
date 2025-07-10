<?php

namespace App\Helpers;

use App\Models\Image;

class ImageHelper
{
    /**
     * Generate image tag with clean URL
     */
    public static function img(string $cleanName, string $alt = '', array $attributes = []): string
    {
        $url = url("/img/{$cleanName}.jpg");
        
        $attrs = [];
        foreach ($attributes as $key => $value) {
            $attrs[] = $key . '="' . htmlspecialchars($value) . '"';
        }
        
        $attrString = !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
        
        return '<img src="' . $url . '" alt="' . htmlspecialchars($alt) . '"' . $attrString . '>';
    }

    /**
     * Generate responsive image with variants
     */
    public static function responsiveImg(string $cleanName, string $alt = '', array $attributes = []): string
    {
        $baseUrl = url("/img/{$cleanName}");
        
        $attrs = [
            'src' => $baseUrl . '.jpg',
            'alt' => $alt,
            'loading' => 'lazy'
        ];
        
        // Merge custom attributes
        $attrs = array_merge($attrs, $attributes);
        
        $attrString = '';
        foreach ($attrs as $key => $value) {
            $attrString .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<img' . $attrString . '>';
    }

    /**
     * Get image URL by clean name
     */
    public static function url(string $cleanName): string
    {
        return url("/img/{$cleanName}.jpg");
    }

    /**
     * Check if image exists by clean name
     */
    public static function exists(string $cleanName): bool
    {
        return Image::where('clean_name', $cleanName)
                   ->where('status', 'active')
                   ->where('is_public', true)
                   ->exists();
    }
}