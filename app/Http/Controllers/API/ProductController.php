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


    // public function index()
    // {
    //     $products = Product::with(['category','brand'])
    //         ->latest()
    //         ->paginate(10);

    //     return response()->json($products);
    // }
//     public function index(Request $request)
// {
//     $search = $request->search;

//     $search = strtolower(trim($request->search));

//     $products = Product::with(['category:id,name', 'brand:id,name'])
//         ->when($search, function ($query) use ($search) {

//             $query->where(function ($q) use ($search) {

//                 $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
//                   ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"])

//                   ->orWhereHas('category', function ($category) use ($search) {
//                       $category->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
//                   })

//                   ->orWhereHas('brand', function ($brand) use ($search) {
//                       $brand->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
//                   });

//             });

//         })
//         ->select([
//             'id',
//             'category_id',
//             'brand_id',
//             'name',
//             'description',
//             'base_price',
//             'labour_cost',
//         ])
//         ->latest()
//         ->paginate(20);

//     return response()->json($products);
// }

public function index(Request $request)
{
    $search = strtolower(trim($request->search));
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    // Dynamic Pagination: फ्रंटएंड से per_page न मिलने पर डिफ़ॉल्ट 20 रिकॉर्ड्स
    $perPage = $request->query('per_page', 20);

    $products = Product::with(['category:id,name', 'brand:id,name'])
        // 1. Search Logic (Product Name, Description, Category, Brand)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('category', function ($category) use ($search) {
                      $category->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                  })
                  ->orWhereHas('brand', function ($brand) use ($search) {
                      $brand->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                  });
            });
        })
        // 2. Start Date Filter (created_at)
        ->when($startDate, function ($query) use ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        })
        // 3. End Date Filter (created_at)
        ->when($endDate, function ($query) use ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        })
        ->select([
            'id',
            'category_id',
            'brand_id',
            'name',
            'description',
            'base_price',
            'labour_cost',
            'created_at', // Date filter के लिए ज़रूरी
        ])
        ->latest()
        ->paginate($perPage);

    return response()->json($products);
}


    public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
        'brand_id' => 'nullable|exists:brands,id',
        'description' => 'nullable|string',

        'base_price' => 'required|numeric|min:0',
        'labour_cost'=>'nullable',
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
            'labour_cost' => 'nullable|numeric|min:0',
            // 'unit' => 'nullable|string|max:50',
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
// $data['is_active']=1;
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
