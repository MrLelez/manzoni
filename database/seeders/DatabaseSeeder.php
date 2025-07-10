<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Tag;
use App\Models\Locale;
use App\Models\Translation;
use App\Models\Image;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('it_IT');
        
        $this->command->info('🌱 Avvio seeding prodotti Manzoni...');
        
        // 1. Crea locales
        $this->createLocales();
        
        // 2. Crea categorie
        $this->createCategories();
        
        // 3. Crea tag
        $this->createTags();
        
        // 4. Crea prodotti
        $this->createProducts();
        
        $this->command->info('✅ Seeding completato con successo!');
    }

    /**
     * Crea i locales per il sistema multilingua
     */
    private function createLocales(): void
{
    $this->command->info('🌍 Creazione locales...');
    
    $locales = [
        ['code' => 'it', 'name' => 'Italiano', 'native_name' => 'Italiano', 'flag' => '🇮🇹', 'is_active' => true, 'is_default' => true],
        ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'flag' => '🇬🇧', 'is_active' => true, 'is_default' => false],
        ['code' => 'fr', 'name' => 'Français', 'native_name' => 'Français', 'flag' => '🇫🇷', 'is_active' => true, 'is_default' => false],
        ['code' => 'de', 'name' => 'Deutsch', 'native_name' => 'Deutsch', 'flag' => '🇩🇪', 'is_active' => true, 'is_default' => false],
        ['code' => 'es', 'name' => 'Español', 'native_name' => 'Español', 'flag' => '🇪🇸', 'is_active' => true, 'is_default' => false],
        ['code' => 'en-us', 'name' => 'English (US)', 'native_name' => 'English (US)', 'flag' => '🇺🇸', 'is_active' => true, 'is_default' => false],
    ];

    foreach ($locales as $localeData) {
        Locale::firstOrCreate(
            ['code' => $localeData['code']],
            $localeData
        );
    }
}

    /**
     * Crea le categorie Manzoni
     */
    private function createCategories(): void
    {
        $this->command->info('📂 Creazione categorie...');
        
        $categories = [
            // Categorie principali
            [
                'name' => 'Panchine',
                'slug' => 'panchine',
                'description' => 'Panchine per esterni in diversi materiali e stili',
                'icon' => '🪑',
                'color' => '#3B82F6',
                'is_featured' => true,
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Panchine in Metallo',
                        'slug' => 'panchine-metallo',
                        'description' => 'Panchine realizzate in acciaio e ferro',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Panchine in Legno',
                        'slug' => 'panchine-legno',
                        'description' => 'Panchine in legno naturale e trattato',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Panchine Moderne',
                        'slug' => 'panchine-moderne',
                        'description' => 'Design contemporaneo per spazi urbani',
                        'sort_order' => 3,
                    ],
                ]
            ],
            [
                'name' => 'Fontane',
                'slug' => 'fontane',
                'description' => 'Fontane decorative e funzionali per spazi pubblici',
                'icon' => '⛲',
                'color' => '#06B6D4',
                'is_featured' => true,
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'Fontane Artistiche',
                        'slug' => 'fontane-artistiche',
                        'description' => 'Fontane con design artistico e decorativo',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Fontane Pubbliche',
                        'slug' => 'fontane-pubbliche',
                        'description' => 'Fontane per parchi e spazi pubblici',
                        'sort_order' => 2,
                    ],
                ]
            ],
            [
                'name' => 'Griglie Albero',
                'slug' => 'griglie-albero',
                'description' => 'Griglie di protezione per alberi urbani',
                'icon' => '🌳',
                'color' => '#10B981',
                'is_featured' => true,
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Griglie Quadrate',
                        'slug' => 'griglie-quadrate',
                        'description' => 'Griglie con design geometrico quadrato',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Griglie Rotonde',
                        'slug' => 'griglie-rotonde',
                        'description' => 'Griglie circolari per alberi',
                        'sort_order' => 2,
                    ],
                ]
            ],
            [
                'name' => 'Cestini',
                'slug' => 'cestini',
                'description' => 'Cestini portarifiuti per esterni',
                'icon' => '🗑️',
                'color' => '#F59E0B',
                'is_featured' => true,
                'sort_order' => 4,
                'children' => [
                    [
                        'name' => 'Cestini Differenziata',
                        'slug' => 'cestini-differenziata',
                        'description' => 'Cestini per raccolta differenziata',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Cestini Design',
                        'slug' => 'cestini-design',
                        'description' => 'Cestini con design moderno',
                        'sort_order' => 2,
                    ],
                ]
            ],
            [
                'name' => 'Soluzioni su Misura',
                'slug' => 'soluzioni-su-misura',
                'description' => 'Progetti personalizzati per ogni esigenza',
                'icon' => '⚙️',
                'color' => '#8B5CF6',
                'is_featured' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $category = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                array_merge($categoryData, ['is_active' => true])
            );

            // Crea sottocategorie
            foreach ($children as $childData) {
                Category::firstOrCreate(
                    ['slug' => $childData['slug']],
                    array_merge($childData, [
                        'parent_id' => $category->id,
                        'is_active' => true,
                    ])
                );
            }
        }
    }

    /**
     * Crea i tag
     */
    private function createTags(): void
    {
        $this->command->info('🏷️ Creazione tag...');
        
        $tags = [
            // Materiali
            ['name' => 'Acciaio Inox', 'type' => 'material', 'color' => '#64748B', 'icon' => '🔧'],
            ['name' => 'Ferro Battuto', 'type' => 'material', 'color' => '#374151', 'icon' => '⚒️'],
            ['name' => 'Legno Teak', 'type' => 'material', 'color' => '#92400E', 'icon' => '🌳'],
            ['name' => 'Alluminio', 'type' => 'material', 'color' => '#6B7280', 'icon' => '⚡'],
            ['name' => 'Ghisa', 'type' => 'material', 'color' => '#1F2937', 'icon' => '🏭'],
            
            // Colori
            ['name' => 'Antracite', 'type' => 'color', 'color' => '#374151', 'icon' => '🎨'],
            ['name' => 'Verde RAL 6005', 'type' => 'color', 'color' => '#0F4C3A', 'icon' => '🎨'],
            ['name' => 'Blu RAL 5017', 'type' => 'color', 'color' => '#063971', 'icon' => '🎨'],
            ['name' => 'Bianco RAL 9016', 'type' => 'color', 'color' => '#F1F5F9', 'icon' => '🎨'],
            ['name' => 'Corten', 'type' => 'color', 'color' => '#B45309', 'icon' => '🎨'],
            
            // Stili
            ['name' => 'Classico', 'type' => 'style', 'color' => '#7C2D12', 'icon' => '🏛️'],
            ['name' => 'Moderno', 'type' => 'style', 'color' => '#1E40AF', 'icon' => '🏢'],
            ['name' => 'Minimalista', 'type' => 'style', 'color' => '#6B7280', 'icon' => '◻️'],
            ['name' => 'Vintage', 'type' => 'style', 'color' => '#92400E', 'icon' => '🕰️'],
            
            // Caratteristiche
            ['name' => 'Resistente UV', 'type' => 'feature', 'color' => '#F59E0B', 'icon' => '☀️'],
            ['name' => 'Antivandalico', 'type' => 'feature', 'color' => '#DC2626', 'icon' => '🛡️'],
            ['name' => 'Riciclabile', 'type' => 'feature', 'color' => '#10B981', 'icon' => '♻️'],
            ['name' => 'Manutenzione Zero', 'type' => 'feature', 'color' => '#06B6D4', 'icon' => '🔧'],
            ['name' => 'Installazione Facile', 'type' => 'feature', 'color' => '#8B5CF6', 'icon' => '⚡'],
            
            // Certificazioni
            ['name' => 'CE', 'type' => 'certification', 'color' => '#1E40AF', 'icon' => '✅'],
            ['name' => 'ISO 9001', 'type' => 'certification', 'color' => '#059669', 'icon' => '🏆'],
            ['name' => 'Made in Italy', 'type' => 'certification', 'color' => '#DC2626', 'icon' => '🇮🇹'],
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate(
                ['name' => $tagData['name']],
                array_merge($tagData, [
                    'slug' => \Str::slug($tagData['name']),
                    'is_active' => true,
                    'is_featured' => in_array($tagData['name'], ['Acciaio Inox', 'Resistente UV', 'Made in Italy']),
                ])
            );
        }
    }

    /**
     * Crea i prodotti Manzoni
     */
    private function createProducts(): void
    {
        $this->command->info('🛍️ Creazione prodotti...');
        
        $products = [
            // PANCHINE
            [
                'name' => 'Panchina Roma Classic',
                'sku' => 'ROMA-001',
                'description' => 'Panchina elegante in acciaio inox e legno teak, perfetta per parchi e giardini pubblici. Design classico italiano che unisce funzionalità e bellezza.',
                'short_description' => 'Panchina classica in acciaio e teak',
                'category' => 'panchine-metallo',
                'base_price' => 1250.00,
                'weight' => 85.5,
                'dimensions' => ['length' => 180, 'width' => 60, 'height' => 80],
                'materials' => ['Acciaio Inox AISI 316', 'Legno Teak'],
                'colors' => ['Antracite', 'Verde RAL 6005'],
                'installation_type' => 'Tasselli chimici',
                'warranty_years' => 10,
                'technical_specs' => [
                    'Struttura' => 'Acciaio inox AISI 316 spessore 8mm',
                    'Seduta' => 'Legno teak certificato FSC',
                    'Finitura' => 'Verniciatura elettrostatica',
                    'Carico' => 'Fino a 300kg',
                    'Normative' => 'UNI EN 1176, CE',
                ],
                'tags' => ['Acciaio Inox', 'Legno Teak', 'Classico', 'Resistente UV', 'Made in Italy'],
                'is_featured' => true,
                'variants' => [
                    [
                        'name' => 'Standard',
                        'description' => 'Versione base senza schienale',
                        'sku_suffix' => 'STD',
                        'price_adjustment' => 0,
                        'is_default' => true,
                        'is_removable' => false,
                    ],
                    [
                        'name' => 'Con Schienale',
                        'description' => 'Versione con schienale ergonomico',
                        'sku_suffix' => 'SCH',
                        'price_adjustment' => 280,
                        'price_adjustment_type' => 'fixed',
                        'is_removable' => false,
                    ],
                    [
                        'name' => 'Con Braccioli',
                        'description' => 'Versione con braccioli laterali',
                        'sku_suffix' => 'BRC',
                        'price_adjustment' => 15,
                        'price_adjustment_type' => 'percentage',
                        'is_removable' => true,
                    ],
                ]
            ],
            [
                'name' => 'Panchina Milano Modern',
                'sku' => 'MILANO-001',
                'description' => 'Panchina dal design contemporaneo, ideale per centri urbani moderni. Struttura minimalista in alluminio con seduta in materiale composito.',
                'short_description' => 'Panchina moderna in alluminio',
                'category' => 'panchine-moderne',
                'base_price' => 890.00,
                'weight' => 42.0,
                'dimensions' => ['length' => 160, 'width' => 55, 'height' => 75],
                'materials' => ['Alluminio', 'Composito'],
                'colors' => ['Bianco RAL 9016', 'Antracite'],
                'installation_type' => 'Ancoraggio diretto',
                'warranty_years' => 5,
                'tags' => ['Alluminio', 'Moderno', 'Minimalista', 'Manutenzione Zero'],
                'is_featured' => true,
                'variants' => [
                    [
                        'name' => 'Standard 160cm',
                        'description' => 'Lunghezza standard',
                        'sku_suffix' => '160',
                        'is_default' => true,
                        'is_removable' => false,
                    ],
                    [
                        'name' => 'Estesa 200cm',
                        'description' => 'Versione più lunga',
                        'sku_suffix' => '200',
                        'price_adjustment' => 180,
                        'price_adjustment_type' => 'fixed',
                        'dimensions_adjustment' => ['length' => 40, 'width' => 0, 'height' => 0],
                        'is_removable' => false,
                    ],
                ]
            ],

            // FONTANE
            [
                'name' => 'Fontana Trevi Mini',
                'sku' => 'TREVI-001',
                'description' => 'Fontana decorativa ispirata ai classici romani, realizzata in pietra naturale con sistema di ricircolo integrato.',
                'short_description' => 'Fontana decorativa in pietra',
                'category' => 'fontane-artistiche',
                'base_price' => 3200.00,
                'weight' => 450.0,
                'dimensions' => ['length' => 120, 'width' => 80, 'height' => 90],
                'materials' => ['Pietra Naturale', 'Acciaio Inox'],
                'colors' => ['Pietra Naturale'],
                'installation_type' => 'Basamento in cemento',
                'warranty_years' => 15,
                'tags' => ['Pietra Naturale', 'Classico', 'Artistico', 'Made in Italy'],
                'is_featured' => true,
                'variants' => [
                    [
                        'name' => 'Base',
                        'description' => 'Versione senza illuminazione',
                        'sku_suffix' => 'BASE',
                        'is_default' => true,
                        'is_removable' => false,
                    ],
                    [
                        'name' => 'LED',
                        'description' => 'Con illuminazione LED integrata',
                        'sku_suffix' => 'LED',
                        'price_adjustment' => 580,
                        'price_adjustment_type' => 'fixed',
                        'is_removable' => true,
                    ],
                ]
            ],

            // GRIGLIE ALBERO
            [
                'name' => 'Griglia Quadrata Pro',
                'sku' => 'GRIG-001',
                'description' => 'Griglia di protezione per alberi urbani, design modulare in ghisa con sistema di espansione.',
                'short_description' => 'Griglia protettiva modulare',
                'category' => 'griglie-quadrate',
                'base_price' => 320.00,
                'weight' => 28.0,
                'dimensions' => ['length' => 100, 'width' => 100, 'height' => 5],
                'materials' => ['Ghisa'],
                'colors' => ['Verde RAL 6005', 'Antracite'],
                'installation_type' => 'Appoggio diretto',
                'warranty_years' => 8,
                'tags' => ['Ghisa', 'Modulare', 'Antivandalico', 'Riciclabile'],
                'variants' => [
                    [
                        'name' => '100x100',
                        'description' => 'Dimensione standard',
                        'sku_suffix' => '100',
                        'is_default' => true,
                        'is_removable' => false,
                    ],
                    [
                        'name' => '120x120',
                        'description' => 'Dimensione maggiorata',
                        'sku_suffix' => '120',
                        'price_adjustment' => 80,
                        'price_adjustment_type' => 'fixed',
                        'dimensions_adjustment' => ['length' => 20, 'width' => 20, 'height' => 0],
                        'is_removable' => false,
                    ],
                ]
            ],

            // CESTINI
            [
                'name' => 'Cestino Eco Smart',
                'sku' => 'CEST-001',
                'description' => 'Cestino portarifiuti con scomparto per raccolta differenziata, realizzato in materiali riciclati.',
                'short_description' => 'Cestino eco-sostenibile',
                'category' => 'cestini-differenziata',
                'base_price' => 450.00,
                'weight' => 18.5,
                'dimensions' => ['length' => 40, 'width' => 40, 'height' => 85],
                'materials' => ['Materiale Riciclato', 'Acciaio Inox'],
                'colors' => ['Verde RAL 6005', 'Blu RAL 5017'],
                'installation_type' => 'Ancoraggio a terra',
                'warranty_years' => 5,
                'tags' => ['Riciclabile', 'Eco-friendly', 'Differenziata', 'Moderno'],
                'is_featured' => true,
                'variants' => [
                    [
                        'name' => 'Doppio Scomparto',
                        'description' => 'Per differenziata base',
                        'sku_suffix' => 'DUO',
                        'is_default' => true,
                        'is_removable' => false,
                    ],
                    [
                        'name' => 'Triplo Scomparto',
                        'description' => 'Per differenziata completa',
                        'sku_suffix' => 'TRI',
                        'price_adjustment' => 120,
                        'price_adjustment_type' => 'fixed',
                        'is_removable' => false,
                    ],
                ]
            ],
        ];

        foreach ($products as $productData) {
            $this->command->info("  📦 Creazione prodotto: {$productData['name']}");
            
            // Trova categoria
            $category = Category::where('slug', $productData['category'])->first();
            if (!$category) {
                $this->command->warn("  ⚠️ Categoria non trovata: {$productData['category']}");
                continue;
            }

            // Crea il prodotto
            $variants = $productData['variants'] ?? [];
            $tags = $productData['tags'] ?? [];
            unset($productData['variants'], $productData['tags'], $productData['category']);

            $product = Product::create(array_merge($productData, [
                'category_id' => $category->id,
                'slug' => \Str::slug($productData['name']),
                'is_active' => true,
                'sort_order' => 1,
            ]));

            // Associa tag
            foreach ($tags as $tagName) {
                $tag = Tag::where('name', $tagName)->first();
                if ($tag) {
                    $product->tags()->attach($tag->id);
                    $tag->incrementUsage();
                }
            }

            // Crea varianti
            foreach ($variants as $index => $variantData) {
                ProductVariant::create(array_merge($variantData, [
                    'product_id' => $product->id,
                    'variant_type' => 'configuration',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'stock_quantity' => rand(5, 50),
                    'availability_status' => 'available',
                ]));
            }

            // Crea traduzioni base (solo inglese per ora)
            if (Locale::where('code', 'en')->exists()) {
                $this->createTranslations($product, 'en');
            }
        }
    }

    /**
     * Crea traduzioni per un prodotto
     */
    private function createTranslations(Product $product, string $locale): void
    {
        $translations = [
            'name' => $this->translateProductName($product->name),
            'description' => $this->translateProductDescription($product->description),
            'short_description' => $this->translateProductDescription($product->short_description),
        ];

        foreach ($translations as $field => $value) {
            Translation::create([
                'translatable_type' => Product::class,
                'translatable_id' => $product->id,
                'field' => $field,
                'value' => $value,
                'locale_code' => $locale,
                'is_active' => true,
                'is_published' => true,
                'is_auto_translated' => true,
                'translation_status' => 'published',
                'version' => 1,
            ]);
        }
    }

    /**
     * Traduzioni semplici per demo
     */
    private function translateProductName(string $name): string
    {
        $translations = [
            'Panchina Roma Classic' => 'Rome Classic Bench',
            'Panchina Milano Modern' => 'Milan Modern Bench',
            'Fontana Trevi Mini' => 'Trevi Mini Fountain',
            'Griglia Quadrata Pro' => 'Pro Square Tree Guard',
            'Cestino Eco Smart' => 'Eco Smart Waste Bin',
        ];

        return $translations[$name] ?? $name;
    }

    /**
     * Traduzioni descrizioni per demo
     */
    private function translateProductDescription(string $description): string
    {
        // Traduzioni semplici per demo
        $translations = [
            'Panchina elegante' => 'Elegant bench',
            'design contemporaneo' => 'contemporary design',
            'Fontana decorativa' => 'Decorative fountain',
            'Griglia di protezione' => 'Protective grid',
            'Cestino portarifiuti' => 'Waste bin',
            'realizzata in' => 'made of',
            'perfetta per' => 'perfect for',
            'ideale per' => 'ideal for',
        ];

        $translated = $description;
        foreach ($translations as $italian => $english) {
            $translated = str_ireplace($italian, $english, $translated);
        }

        return $translated;
    }
}