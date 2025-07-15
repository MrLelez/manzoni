<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use App\Models\Product;

class MaterialsColorsSeeder extends Seeder
{
    public function run()
    {
        // ==========================================
        // MATERIALI MANZONI ARREDO URBANO
        // ==========================================
        
        $materials = [
            [
                'name' => 'Acciaio Inox AISI 316',
                'description' => 'Acciaio inossidabile resistente alla corrosione marina',
                'color' => '#C0C0C0',
                'icon' => '🔧',
                'type' => 'material',
                'sort_order' => 1,
            ],
            [
                'name' => 'Ferro Zincato',
                'description' => 'Ferro trattato con zincatura per resistenza alla corrosione',
                'color' => '#708090',
                'icon' => '⚒️',
                'type' => 'material',
                'sort_order' => 2,
            ],
            [
                'name' => 'Alluminio',
                'description' => 'Lega di alluminio leggera e resistente',
                'color' => '#A8A8A8',
                'icon' => '🪙',
                'type' => 'material',
                'sort_order' => 3,
            ],
            [
                'name' => 'Legno Lamellare',
                'description' => 'Legno lamellare trattato per esterni',
                'color' => '#8B4513',
                'icon' => '🪵',
                'type' => 'material',
                'sort_order' => 4,
            ],
            [
                'name' => 'Granito',
                'description' => 'Pietra naturale di granito levigato',
                'color' => '#2F4F4F',
                'icon' => '🪨',
                'type' => 'material',
                'sort_order' => 5,
            ],
            [
                'name' => 'Plastica Riciclata',
                'description' => 'Materiale plastico riciclato eco-sostenibile',
                'color' => '#32CD32',
                'icon' => '♻️',
                'type' => 'material',
                'sort_order' => 6,
            ],
            [
                'name' => 'Cemento',
                'description' => 'Cemento architettonico levigato',
                'color' => '#696969',
                'icon' => '🏗️',
                'type' => 'material',
                'sort_order' => 7,
            ],
            [
                'name' => 'Ghisa',
                'description' => 'Ghisa decorativa tradizionale',
                'color' => '#36454F',
                'icon' => '⚙️',
                'type' => 'material',
                'sort_order' => 8,
            ],
        ];

        // ==========================================
        // COLORI MANZONI ARREDO URBANO
        // ==========================================
        
        $colors = [
            [
                'name' => 'Bianco',
                'description' => 'Bianco RAL 9016',
                'color' => '#FFFFFF',
                'icon' => '⚪',
                'type' => 'color',
                'sort_order' => 1,
            ],
            [
                'name' => 'Nero',
                'description' => 'Nero RAL 9005',
                'color' => '#000000',
                'icon' => '⚫',
                'type' => 'color',
                'sort_order' => 2,
            ],
            [
                'name' => 'Grigio Antracite',
                'description' => 'Grigio antracite RAL 7016',
                'color' => '#383838',
                'icon' => '🔘',
                'type' => 'color',
                'sort_order' => 3,
            ],
            [
                'name' => 'Grigio Chiaro',
                'description' => 'Grigio chiaro RAL 7035',
                'color' => '#D7D7D7',
                'icon' => '🔘',
                'type' => 'color',
                'sort_order' => 4,
            ],
            [
                'name' => 'Verde Muschio',
                'description' => 'Verde muschio RAL 6005',
                'color' => '#2F4538',
                'icon' => '🟢',
                'type' => 'color',
                'sort_order' => 5,
            ],
            [
                'name' => 'Blu Navy',
                'description' => 'Blu navy RAL 5004',
                'color' => '#1F2A44',
                'icon' => '🔵',
                'type' => 'color',
                'sort_order' => 6,
            ],
            [
                'name' => 'Marrone Terra',
                'description' => 'Marrone terra RAL 8017',
                'color' => '#45322E',
                'icon' => '🤎',
                'type' => 'color',
                'sort_order' => 7,
            ],
            [
                'name' => 'Rosso Mattone',
                'description' => 'Rosso mattone RAL 3009',
                'color' => '#642424',
                'icon' => '🔴',
                'type' => 'color',
                'sort_order' => 8,
            ],
            [
                'name' => 'Giallo Ocra',
                'description' => 'Giallo ocra RAL 1024',
                'color' => '#AEA04B',
                'icon' => '🟡',
                'type' => 'color',
                'sort_order' => 9,
            ],
            [
                'name' => 'Corten',
                'description' => 'Effetto corten naturale',
                'color' => '#B7472A',
                'icon' => '🟤',
                'type' => 'color',
                'sort_order' => 10,
            ],
        ];

        // ==========================================
        // FINITURE MANZONI ARREDO URBANO
        // ==========================================
        
        $finishes = [
            [
                'name' => 'Verniciatura Elettrostatica',
                'description' => 'Verniciatura a polvere elettrostatica anticorrosiva',
                'color' => '#4169E1',
                'icon' => '🎨',
                'type' => 'finish',
                'sort_order' => 1,
            ],
            [
                'name' => 'Zincatura',
                'description' => 'Trattamento di zincatura a caldo',
                'color' => '#C0C0C0',
                'icon' => '⚡',
                'type' => 'finish',
                'sort_order' => 2,
            ],
            [
                'name' => 'Naturale',
                'description' => 'Finitura naturale senza trattamenti',
                'color' => '#D2B48C',
                'icon' => '🌿',
                'type' => 'finish',
                'sort_order' => 3,
            ],
            [
                'name' => 'Levigato',
                'description' => 'Superficie levigata e lucida',
                'color' => '#E6E6FA',
                'icon' => '✨',
                'type' => 'finish',
                'sort_order' => 4,
            ],
            [
                'name' => 'Sabbiato',
                'description' => 'Superficie sabbiata opaca',
                'color' => '#F5DEB3',
                'icon' => '🏜️',
                'type' => 'finish',
                'sort_order' => 5,
            ],
            [
                'name' => 'Impregnato',
                'description' => 'Impregnazione protettiva per legno',
                'color' => '#DEB887',
                'icon' => '🪵',
                'type' => 'finish',
                'sort_order' => 6,
            ],
        ];

        // ==========================================
        // CREAZIONE DEI TAG USANDO TABELLA ESISTENTE
        // ==========================================
        
        $this->command->info('Creazione tag materiali nella tabella tags esistente...');
        foreach ($materials as $material) {
            $slug = \Str::slug($material['name']);
            
            // Controlla se esiste già un tag con questo slug
            $existingTag = Tag::where('slug', $slug)->first();
            
            if ($existingTag) {
                $this->command->warn("Tag '{$material['name']}' già esistente, aggiorno type/category...");
                $existingTag->update([
                    'type' => 'material', // o 'category' => 'material' se usi category
                    'color' => $material['color'],
                    'icon' => $material['icon'],
                    'description' => $material['description'],
                    'sort_order' => $material['sort_order'],
                    'is_active' => true,
                ]);
            } else {
                Tag::create(array_merge($material, [
                    'slug' => $slug,
                    'is_active' => true,
                    'is_featured' => false,
                    'usage_count' => 0,
                ]));
            }
        }

        $this->command->info('Creazione tag colori nella tabella tags esistente...');
        foreach ($colors as $color) {
            $slug = \Str::slug($color['name']);
            
            $existingTag = Tag::where('slug', $slug)->first();
            
            if ($existingTag) {
                $this->command->warn("Tag '{$color['name']}' già esistente, aggiorno type/category...");
                $existingTag->update([
                    'type' => 'color', // o 'category' => 'color' se usi category
                    'color' => $color['color'],
                    'icon' => $color['icon'],
                    'description' => $color['description'],
                    'sort_order' => $color['sort_order'],
                    'is_active' => true,
                ]);
            } else {
                Tag::create(array_merge($color, [
                    'slug' => $slug,
                    'is_active' => true,
                    'is_featured' => false,
                    'usage_count' => 0,
                ]));
            }
        }

        $this->command->info('Creazione tag finiture nella tabella tags esistente...');
        foreach ($finishes as $finish) {
            $slug = \Str::slug($finish['name']);
            
            $existingTag = Tag::where('slug', $slug)->first();
            
            if ($existingTag) {
                $this->command->warn("Tag '{$finish['name']}' già esistente, aggiorno type/category...");
                $existingTag->update([
                    'type' => 'finish', // o 'category' => 'finish' se usi category
                    'color' => $finish['color'],
                    'icon' => $finish['icon'],
                    'description' => $finish['description'],
                    'sort_order' => $finish['sort_order'],
                    'is_active' => true,
                ]);
            } else {
                Tag::create(array_merge($finish, [
                    'slug' => $slug,
                    'is_active' => true,
                    'is_featured' => false,
                    'usage_count' => 0,
                ]));
            }
        }

        // ==========================================
        // ASSEGNAZIONE AI PRODOTTI ESISTENTI
        // ==========================================
        
        $this->command->info('Assegnazione opzioni ai prodotti esistenti...');
        
        $products = Product::all();
        
        foreach ($products as $product) {
            // Assegna opzioni di esempio basate sul tipo di prodotto
            $this->assignOptionsToProduct($product);
        }

        $this->command->info('Seeder completato! Tag materiali e colori creati e assegnati ai prodotti.');
    }

    /**
     * Assegna opzioni di materiali e colori ai prodotti basandosi sul nome
     */
    private function assignOptionsToProduct(Product $product)
    {
        $productName = strtolower($product->name);
        
        // Tutti i prodotti Manzoni hanno materiali e colori
        $product->update([
            'has_material_options' => true,
            'has_color_options' => true,
        ]);

        // Assegna tag specifici basandosi sul tipo di prodotto
        if (str_contains($productName, 'panchina')) {
            $this->assignPanchinaOptions($product);
        } elseif (str_contains($productName, 'cestino')) {
            $this->assignCestinoOptions($product);
        } elseif (str_contains($productName, 'fontana')) {
            $this->assignFontanaOptions($product);
        } elseif (str_contains($productName, 'griglia')) {
            $this->assignGrigliaOptions($product);
        } else {
            $this->assignDefaultOptions($product);
        }
    }

    private function assignPanchinaOptions(Product $product)
    {
        // Materiali tipici per panchine
        $materials = Tag::whereIn('name', ['Legno Lamellare', 'Ferro Zincato', 'Alluminio'])->get();
        $colors = Tag::whereIn('name', ['Bianco', 'Grigio Antracite', 'Verde Muschio', 'Marrone Terra'])->get();

        $product->tags()->sync(array_merge(
            $materials->pluck('id')->toArray(),
            $colors->pluck('id')->toArray()
        ));

        // Imposta default
        $product->update([
            'default_material_tag_id' => $materials->first()?->id,
            'default_color_tag_id' => $colors->first()?->id,
        ]);
    }

    private function assignCestinoOptions(Product $product)
    {
        $materials = Tag::whereIn('name', ['Acciaio Inox AISI 316', 'Ferro Zincato', 'Plastica Riciclata'])->get();
        $colors = Tag::whereIn('name', ['Grigio Antracite', 'Verde Muschio', 'Nero', 'Bianco'])->get();

        $product->tags()->sync(array_merge(
            $materials->pluck('id')->toArray(),
            $colors->pluck('id')->toArray()
        ));

        $product->update([
            'default_material_tag_id' => $materials->first()?->id,
            'default_color_tag_id' => $colors->first()?->id,
        ]);
    }

    private function assignFontanaOptions(Product $product)
    {
        $materials = Tag::whereIn('name', ['Granito', 'Cemento', 'Acciaio Inox AISI 316'])->get();
        $colors = Tag::whereIn('name', ['Grigio Chiaro', 'Grigio Antracite', 'Bianco'])->get();

        $product->tags()->sync(array_merge(
            $materials->pluck('id')->toArray(),
            $colors->pluck('id')->toArray()
        ));

        $product->update([
            'default_material_tag_id' => $materials->first()?->id,
            'default_color_tag_id' => $colors->first()?->id,
        ]);
    }

    private function assignGrigliaOptions(Product $product)
    {
        $materials = Tag::whereIn('name', ['Ferro Zincato', 'Acciaio Inox AISI 316', 'Ghisa'])->get();
        $colors = Tag::whereIn('name', ['Nero', 'Grigio Antracite', 'Corten'])->get();

        $product->tags()->sync(array_merge(
            $materials->pluck('id')->toArray(),
            $colors->pluck('id')->toArray()
        ));

        $product->update([
            'default_material_tag_id' => $materials->first()?->id,
            'default_color_tag_id' => $colors->first()?->id,
        ]);
    }

    private function assignDefaultOptions(Product $product)
    {
        // Opzioni generiche per prodotti non specificati
        $materials = Tag::whereIn('name', ['Acciaio Inox AISI 316', 'Ferro Zincato', 'Alluminio'])->get();
        $colors = Tag::whereIn('name', ['Grigio Antracite', 'Bianco', 'Nero'])->get();

        $product->tags()->sync(array_merge(
            $materials->pluck('id')->toArray(),
            $colors->pluck('id')->toArray()
        ));

        $product->update([
            'default_material_tag_id' => $materials->first()?->id,
            'default_color_tag_id' => $colors->first()?->id,
        ]);
    }
}