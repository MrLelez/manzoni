<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
{
    $query = Product::with(['category']);

    // Filtri corretti usando le colonne reali
    if ($request->filled('search')) {
        $query->where('name', 'like', "%{$request->search}%")
              ->orWhere('sku', 'like', "%{$request->search}%");
    }

    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    if ($request->filled('status')) {
        // Usa 'status' invece di 'is_active'
        $query->where('status', $request->status);
    }

    $products = $query->paginate(20);

    // Stats corrette
    $stats = [
        'total' => Product::count(),
        'active' => Product::where('status', 'active')->count(), // Usa 'status'
        'featured' => Product::where('is_featured', true)->count(),
        'without_images' => 0, // Temporaneamente disabilitato
    ];

    // Categorie (se esiste la tabella)
    $categories = Category::orderBy('name')->get();

    return view('admin.products.index', compact('products', 'categories', 'stats'));
}


    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $tags = Tag::active()->orderBy('name')->get();
        
        return view('admin.products.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        // Mantieni la tua validazione esistente
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'installation_type' => 'nullable|string',
            'warranty_years' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Genera slug automaticamente (come nel tuo controller)
        $validated['slug'] = \Str::slug($validated['name']);

        // Se arriva da Livewire, usa il service, altrimenti usa il metodo standard
        if ($request->wantsJson() || $request->has('livewire')) {
            // Gestito da Livewire
            return response()->json(['success' => true]);
        } else {
            // Metodo tradizionale del tuo controller
            $product = Product::create($validated);
            return redirect()->route('admin.products.index')
                            ->with('success', 'Prodotto creato con successo!');
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'pricing', 'variants', 'relationships.relatedProduct', 'accessories.accessoryProduct', 'tags']);
        
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();
        $tags = Tag::active()->orderBy('name')->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'tags'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'installation_type' => 'nullable|string',
            'warranty_years' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')
                        ->with('success', 'Prodotto aggiornato con successo!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        
        return redirect()->route('admin.products.index')
                        ->with('success', 'Prodotto eliminato con successo!');
    }
}