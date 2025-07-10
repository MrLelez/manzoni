<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Tag;
use App\Models\Locale;
use App\Models\Translation;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Avvio seeding prodotti Manzoni...');
        
        // 1. Crea locales (semplificato)
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
     * Crea i locales essenziali
     */
    private function createLocales(): void
    {
        $this->command->info('🌍 Creazione locales...');
        
        $locales = [
            [
                'code' => 'it',
                'name' => 'Italiano',
                'native_name' => 'Italiano',
                'flag' => '🇮🇹',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'datetime_format' => 'd/m/Y H:i',
                'currency_code' => 'EUR',
                'currency_symbol' => '€',
                'currency_position' => 'before',
                'decimal_separator' => ',',
                'thousands_separator' => '.',
                'decimal_places' => 2,
                'direction' => 'ltr',
                'charset' => 'UTF-8'
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => '🇬🇧',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'datetime_format' => 'd/m/Y H:i',
                'currency_code' => 'GBP',
                'currency_symbol' => '£',
                'currency_position' => 'before',
                'decimal_separator' => '.',
                'thousands_separator' => ',',
                'decimal_places' => 2,
                'direction' => 'ltr',
                'charset' => 'UTF-8'
            ],
            [
                'code' => 'fr',
                'name' => 'Français',
                'native_name' => 'Français',
                'flag' => '🇫🇷',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'datetime_format' => 'd/m/Y H:i',
                'currency_code' => 'EUR',
                'currency_symbol' => '€',
                'currency_position' => 'before',
                'decimal_separator' => ',',
                'thousands_separator' => ' ',
                'decimal_places' => 2,
                'direction' => 'ltr',
                'charset' => 'UTF-8'
            ]
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
                        'sort_order' => 1
                    ],
                    [
                        'name' => 'Panchine in Legno',
                        'slug' => 'panchine-legno',
                        'description' => 'Panchine in legno naturale e trattato',
                        'sort_order' => 2
                    ],
                    [
                        'name' => 'Panchine Moderne',
                        'slug' => 'panchine-moderne',
                        'description' => 'Design contemporaneo per spazi urbani',
                        'sort_order' => 3
                    ]
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
                        'sort_order' => 1
                    ],
                    [
                        'name' => 'Fontane Pubbliche',
                        'slug' => 'fontane-pubbliche',
                        'description' => 'Fontane per parchi e spazi pubblici',
                        'sort_order' => 2
                    ]
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
                        'sort_order' => 1
                    ],
                    [
                        'name' => 'Griglie Rotonde',
                        'slug' => 'griglie-rotonde',
                        'description' => 'Griglie circolari per alberi',
                        'sort_order' => 2
                    ]
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
                        'sort_order' => 1
                    ],
                    [
                        'name' => 'Cestini Design',
                        'slug' => 'cestini-design',
                        'description' => 'Cestini con design moderno',
                        'sort_order' => 2
                    ]
                ]
            ],
            [
                'name' => 'Soluzioni su Misura',
                'slug' => 'soluzioni-su-misura',
                'description' => 'Progetti personalizzati per ogni esigenza',
                'icon' => '⚙️',
                'color' => '#8B5CF6',
                'is_featured' => false,
                'sort_order' => 5
            ]
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
                        'is_active' => true
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
            ['name' => 'Acciaio Inox', 'category' => 'material', 'color' => '#64748B', 'icon' => '🔧'],
            ['name' => 'Ferro Battuto', 'category' => 'material', 'color' => '#374151', 'icon' => '⚒️'],
            ['name' => 'Legno Teak', 'category' => 'material', 'color' => '#92400E', 'icon' => '🌳'],
            ['name' => 'Alluminio', 'category' => 'material', 'color' => '#6B7280', 'icon' => '⚡'],
            ['name' => 'Ghisa', 'category' => 'material', 'color' => '#1F2937', 'icon' => '🏭'],
            
            // Colori
            ['name' => 'Antracite', 'category' => 'color', 'color' => '#374151', 'icon' => '🎨'],
            ['name' => 'Verde RAL 6005', 'category' => 'color', 'color' => '#0F4C3A', 'icon' => '🎨'],
            ['name' => 'Blu RAL 5017', 'category' => 'color', 'color' => '#063971', 'icon' => '🎨'],
            ['name' => 'Bianco RAL 9016', 'category' => 'color', 'color' => '#F1F5F9', 'icon' => '🎨'],
            ['name' => 'Corten', 'category' => 'color', 'color' => '#B45309', 'icon' => '🎨'],
            
            // Stili
            ['name' => 'Classico', 'category' => 'style', 'color' => '#7C2D12', 'icon' => '🏛️'],
            ['name' => 'Moderno', 'category' => 'style', 'color' => '#1E40AF', 'icon' => '🏢'],
            ['name' => 'Minimalista', 'category' => 'style', 'color' => '#6B7280', 'icon' => '◻️'],
            ['name' => 'Vintage', 'category' => 'style', 'color' => '#92400E', 'icon' => '🕰️'],
            
            // Caratteristiche
            ['name' => 'Resistente UV', 'category' => 'feature', 'color' => '#F59E0B', 'icon' => '☀️'],
            ['name' => 'Antivandalico', 'category' => 'feature', 'color' => '#DC2626', 'icon' => '🛡️'],
            ['name' => 'Riciclabile', 'category' => 'feature', 'color' => '#10B981', 'icon' => '♻️'],
            ['name' => 'Manutenzione Zero', 'category' => 'feature', 'color' => '#06B6D4', 'icon' => '🔧'],
            ['name' => 'Installazione Facile', 'category' => 'feature', 'color' => '#8B5CF6', 'icon' => '⚡'],
            
            // Certificazioni
            ['name' => 'CE', 'category' => 'certification', 'color' => '#1E40AF', 'icon' => '✅'],
            ['name' => 'ISO 9001', 'category' => 'certification', 'color' => '#059669', 'icon' => '🏆'],
            ['name' => 'Made in Italy', 'category' => 'certification', 'color' => '#DC2626', 'icon' => '🇮🇹']
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate(
                ['name' => $tagData['name']],
                array_merge($tagData, [
                    'slug' => \Str::slug($tagData['name']),
                    'is_active' => true,
                    'is_featured' => in_array($tagData['name'], ['Acciaio Inox', 'Resistente UV', 'Made in Italy'])
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
            [
                'name' => 'Panchina Roma Classic',
                'sku' => 'ROMA-001',
                'model' => 'roma-classic',
                'short_description' => 'Panchina classica in acciaio e teak',
                'description' => 'Panchina elegante in acciaio inox e legno teak, perfetta per parchi e giardini pubblici. Design classico italiano che unisce funzionalità e bellezza.',
                'type' => 'variable',
                'category' => 'panchine-metallo',
                'base_price' => 1250.00,
                'currency' => 'EUR',
                'status' => 'active',
                'is_featured' => true,
                'weight' => 85.5,
                'length' => 180,
                'width' => 60,
                'height' => 80,
                'material' => 'Acciaio Inox + Legno Teak',
                'finish' => 'Verniciatura elettrostatica',
                'color' => 'Antracite',
                'lead_time_days' => 15,
                'tags' => ['Acciaio Inox', 'Legno Teak', 'Classico', 'Resistente UV', 'Made in Italy']
            ],
            [
                'name' => 'Panchina Milano Modern',
                'sku' => 'MILANO-001',
                'model' => 'milano-modern',
                'short_description' => 'Panchina moderna in alluminio',
                'description' => 'Panchina dal design contemporaneo, ideale per centri urbani moderni. Struttura minimalista in alluminio con seduta in materiale composito.',
                'highlights' => ['Design minimalista', 'Alluminio resistente', 'Manutenzione zero'],
                'type' => 'variable',
                'category' => 'panchine-moderne',
                'base_price' => 890.00,
                'currency' => 'EUR',
                'status' => 'active',
                'is_featured' => true,
                'weight' => 42.0,
                'length' => 160,
                'width' => 55,
                'height' => 75,
                'material' => 'Alluminio + Composito',
                'finish' => 'Anodizzato',
                'color' => 'Bianco RAL 9016',
                'specifications' => [
                    'Struttura' => 'Alluminio anodizzato',
                    'Seduta' => 'Materiale composito',
                    'Installazione' => 'Ancoraggio diretto'
                ],
                'certifications' => ['CE'],
                'features' => ['Manutenzione Zero', 'Leggero', 'Moderno'],
                'lead_time_days' => 10,
                'tags' => ['Alluminio', 'Moderno', 'Minimalista', 'Manutenzione Zero']
            ],
            [
                'name' => 'Fontana Trevi Mini',
                'sku' => 'TREVI-001',
                'model' => 'trevi-mini',
                'short_description' => 'Fontana decorativa in pietra',
                'description' => 'Fontana decorativa ispirata ai classici romani, realizzata in pietra naturale con sistema di ricircolo integrato.',
                'highlights' => ['Pietra naturale', 'Design classico', 'Sistema ricircolo'],
                'type' => 'variable',
                'category' => 'fontane-artistiche',
                'base_price' => 3200.00,
                'currency' => 'EUR',
                'status' => 'active',
                'is_featured' => true,
                'weight' => 450.0,
                'length' => 120,
                'width' => 80,
                'height' => 90,
                'material' => 'Pietra Naturale',
                'finish' => 'Levigato',
                'color' => 'Pietra Naturale',
                'specifications' => [
                    'Materiale' => 'Pietra naturale italiana',
                    'Sistema' => 'Ricircolo integrato',
                    'Installazione' => 'Basamento in cemento'
                ],
                'certifications' => ['CE'],
                'features' => ['Artigianale', 'Durevole', 'Made in Italy'],
                'lead_time_days' => 30,
                'tags' => ['Pietra Naturale', 'Classico', 'Made in Italy']
            ],
            [
                'name' => 'Griglia Quadrata Pro',
                'sku' => 'GRIG-001',
                'model' => 'quadrata-pro',
                'short_description' => 'Griglia protettiva modulare',
                'description' => 'Griglia di protezione per alberi urbani, design modulare in ghisa con sistema di espansione.',
                'highlights' => ['Ghisa resistente', 'Design modulare', 'Sistema espansione'],
                'type' => 'variable',
                'category' => 'griglie-quadrate',
                'base_price' => 320.00,
                'currency' => 'EUR',
                'status' => 'active',
                'is_featured' => false,
                'weight' => 28.0,
                'length' => 100,
                'width' => 100,
                'height' => 5,
                'material' => 'Ghisa',
                'finish' => 'Verniciato',
                'color' => 'Verde RAL 6005',
                'specifications' => [
                    'Materiale' => 'Ghisa di prima qualità',
                    'Sistema' => 'Modulare espandibile',
                    'Installazione' => 'Appoggio diretto'
                ],
                'certifications' => ['CE'],
                'features' => ['Antivandalico', 'Modulare', 'Riciclabile'],
                'lead_time_days' => 7,
                'tags' => ['Ghisa', 'Antivandalico', 'Riciclabile']
            ],
            [
                'name' => 'Cestino Eco Smart',
                'sku' => 'CEST-001',
                'model' => 'eco-smart',
                'short_description' => 'Cestino eco-sostenibile',
                'description' => 'Cestino portarifiuti con scomparto per raccolta differenziata, realizzato in materiali riciclati.',
                'highlights' => ['Materiali riciclati', 'Raccolta differenziata', 'Eco-sostenibile'],
                'type' => 'variable',
                'category' => 'cestini-differenziata',
                'base_price' => 450.00,
                'currency' => 'EUR',
                'status' => 'active',
                'is_featured' => true,
                'weight' => 18.5,
                'length' => 40,
                'width' => 40,
                'height' => 85,
                'material' => 'Materiale Riciclato',
                'finish' => 'Satinato',
                'color' => 'Verde RAL 6005',
                'specifications' => [
                    'Materiale' => 'Plastica riciclata',
                    'Scomparti' => 'Differenziata doppia',
                    'Installazione' => 'Ancoraggio a terra'
                ],
                'certifications' => ['CE'],
                'features' => ['Eco-friendly', 'Riciclabile', 'Differenziata'],
                'lead_time_days' => 5,
                'tags' => ['Riciclabile', 'Moderno']
            ]
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
            $tags = $productData['tags'] ?? [];
            unset($productData['tags'], $productData['category']);

            $product = Product::create(array_merge($productData, [
                'category_id' => $category->id,
                'slug' => \Str::slug($productData['name'])
            ]));

            // Associa tag
            /* 
            foreach ($tags as $tagName) {
                $tag = Tag::where('name', $tagName)->first();
                if ($tag) {
                    $product->tags()->attach($tag->id);
                    $tag->incrementUsage();
                }
            }
            */

            // Crea traduzioni base inglese
            /*
            if (Locale::where('code', 'en')->exists()) {
                $this->createTranslations($product, 'en');
            }
            */
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
            'short_description' => $this->translateProductDescription($product->short_description)
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
                'version' => 1
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
            'Cestino Eco Smart' => 'Eco Smart Waste Bin'
        ];

        return $translations[$name] ?? $name;
    }

    /**
     * Traduzioni descrizioni per demo
     */
    private function translateProductDescription(string $description): string
    {
        $translations = [
            'Panchina elegante' => 'Elegant bench',
            'design contemporaneo' => 'contemporary design',
            'Fontana decorativa' => 'Decorative fountain',
            'Griglia di protezione' => 'Protective grid',
            'Cestino portarifiuti' => 'Waste bin',
            'realizzata in' => 'made of',
            'perfetta per' => 'perfect for',
            'ideale per' => 'ideal for'
        ];

        $translated = $description;
        foreach ($translations as $italian => $english) {
            $translated = str_ireplace($italian, $english, $translated);
        }

        return $translated;
    }
}