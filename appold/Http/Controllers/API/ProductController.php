<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class ProductController extends Controller
{
    //


    public function index()
    {
        $products = Product::with(['category','brand'])
            ->latest()
            ->paginate(10);

        return response()->json($products);
    }

    // 🔹 STORE
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'sku' => 'required|string|unique:products,sku',
    //         'category_id' => 'nullable|exists:categories,id',
    //         'brand_id' => 'nullable|exists:brands,id',
    //         'description' => 'nullable|string',

    //         'base_price' => 'required|numeric|min:0',
    //         'interest_rate' => 'nullable|numeric|min:0',
    //         'tax_rate' => 'nullable|numeric|min:0',

    //         'unit' => 'nullable|string|max:50',
    //         'stock_quantity' => 'nullable|integer|min:0',
    //         'min_stock_alert' => 'nullable|integer|min:0',

    //         'is_active' => 'boolean',
    //         'is_featured' => 'boolean',
    //     ]);

    //     // 🔥 Calculate Selling Price
    //     $interest = ($data['base_price'] * ($data['interest_rate'] ?? 0)) / 100;
    //     $tax = ($data['base_price'] * ($data['tax_rate'] ?? 0)) / 100;

    //     $data['selling_price'] = $data['base_price'] + $interest + $tax;

    //     $product = Product::create($data);

    //     return response()->json([
    //         'success' => true,
    //         'data' => $product->load('category','brand')
    //     ], 201);
    // }


    public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'brand_id' => 'nullable|exists:brands,id',
        'description' => 'nullable|string',

        'base_price' => 'required|numeric|min:0',
        'interest_rate' => 'nullable|numeric|min:0',
        'tax_rate' => 'nullable|numeric|min:0',

        'unit' => 'nullable|string|max:50',
        'stock_quantity' => 'nullable|integer|min:0',
        'min_stock_alert' => 'nullable|integer|min:0',

        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ]);

    // 🔥 Auto-generate SKU
    // Format: first 3 letters of brand + first 3 letters of name + random number
    $brandPrefix = $data['brand_id'] ? \App\Models\Brand::find($data['brand_id'])->name : 'GEN';
    $namePrefix = substr($data['name'], 0, 3);
    $randomNum = rand(100, 999);

    $sku = strtoupper($brandPrefix[0] . $brandPrefix[1] . $brandPrefix[2] . '-' . strtoupper($namePrefix) . '-' . $randomNum);

    // Ensure uniqueness
    while (\App\Models\Product::where('sku', $sku)->exists()) {
        $randomNum = rand(100, 999);
        $sku = strtoupper($brandPrefix[0] . $brandPrefix[1] . $brandPrefix[2] . '-' . strtoupper($namePrefix) . '-' . $randomNum);
    }

    $data['sku'] = $sku;

    // 🔥 Calculate Selling Price
    $interest = ($data['base_price'] * ($data['interest_rate'] ?? 0)) / 100;
    $tax = ($data['base_price'] * ($data['tax_rate'] ?? 0)) / 100;

    $data['selling_price'] = $data['base_price'] + $interest + $tax;

    $product = Product::create($data);

    return response()->json([
        'success' => true,
        'data' => $product->load('category','brand')
    ], 201);
}




public function bulkUpload(Request $request)
{
    // Validate CSV file
    $request->validate([
        'file' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('file');

    // Read CSV using League\Csv (install via composer: composer require league/csv)
    $csv = Reader::createFromPath($file->getRealPath(), 'r');
    $csv->setHeaderOffset(0); // First row as header
    $records = $csv->getRecords(); // Returns iterator

    $productsCreated = [];
    $errors = [];

    foreach ($records as $index => $row) {
        $validator = Validator::make($row, [
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_alert' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            $errors[$index + 1] = $validator->errors()->all();
            continue;
        }

        $data = $validator->validated();

        // 🔥 Auto-generate SKU
        $brandPrefix = $data['brand_id'] ? Brand::find($data['brand_id'])->name : 'GEN';
        $namePrefix = substr($data['name'], 0, 3);
        $randomNum = rand(100, 999);
        $sku = strtoupper(substr($brandPrefix,0,3) . '-' . strtoupper($namePrefix) . '-' . $randomNum);

        while (Product::where('sku', $sku)->exists()) {
            $randomNum = rand(100, 999);
            $sku = strtoupper(substr($brandPrefix,0,3) . '-' . strtoupper($namePrefix) . '-' . $randomNum);
        }

        $data['sku'] = $sku;

        // 🔥 Calculate Selling Price
        $interest = ($data['base_price'] * ($data['interest_rate'] ?? 0)) / 100;
        $tax = ($data['base_price'] * ($data['tax_rate'] ?? 0)) / 100;
        $data['selling_price'] = $data['base_price'] + $interest + $tax;

        $product = Product::create($data);
        $productsCreated[] = $product;
    }

    return response()->json([
        'success' => true,
        'created' => count($productsCreated),
        'errors' => $errors,
        'products' => $productsCreated
    ]);
}
    // 🔹 SHOW
    public function show(Product $product)
    {
        return response()->json(
            $product->load('category','brand')
        );
    }

    // 🔹 UPDATE
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',

            'base_price' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',

            'unit' => 'nullable|string|max:50',
            'stock_quantity' => 'nullable|integer|min:0',

            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // 🔥 Recalculate price
        $interest = ($data['base_price'] * ($data['interest_rate'] ?? 0)) / 100;
        $tax = ($data['base_price'] * ($data['tax_rate'] ?? 0)) / 100;

        $data['selling_price'] = $data['base_price'] + $interest + $tax;

        $product->update($data);

        return response()->json([
            'success' => true,
            'data' => $product->fresh()->load('category','brand')
        ]);
    }

    // 🔹 DELETE
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
