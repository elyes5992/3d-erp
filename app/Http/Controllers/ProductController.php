<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Import Str for string manipulation

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        $categories = Category::all();
        return view('erp.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'pinterest_url' => 'nullable|url',
            'description' => 'nullable',
            'remarks' => 'nullable',
            'cost' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
        ]);

        $validated['status'] = 'Idea';

        if ($request->hasFile('image')) {
            try {
                // 1. Upload the file
                // We force the filename to be unique to ensure a clean path
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Store in 'products' folder
                $path = $file->storeAs('products', $filename, 's3');

                // 2. Set Visibility to Public (Crucial for Backblaze)
                Storage::disk('s3')->setVisibility($path, 'public');
                
                // 3. Generate the URL using the 'AWS_URL' from .env
                $url = Storage::disk('s3')->url($path);
                
                $validated['image_path'] = $url;

            } catch (\Exception $e) {
                // If it fails, go back with the error message
                return back()->withErrors(['image' => 'Upload failed: ' . $e->getMessage()]);
            }
        }

        Product::create($validated);

        return back()->with('success', 'Product Added!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'pinterest_url' => 'nullable|url',
            'description' => 'nullable',
            'remarks' => 'nullable',
            'cost' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
        ]);

        if ($request->hasFile('image')) {
            // --- DELETE OLD IMAGE FROM BACKBLAZE ---
            if ($product->image_path) {
                // We must strip the Domain URL to get the relative path (e.g., "products/image.jpg")
                // This assumes your .env AWS_URL is set correctly
                $baseUrl = config('filesystems.disks.s3.url'); 
                $relativePath = Str::after($product->image_path, $baseUrl . '/');
                
                // Check if file exists before trying to delete to avoid errors
                // if(Storage::disk('s3')->exists($relativePath)) {
                    Storage::disk('s3')->delete($relativePath);
                
            }

            // 1. Upload new file
            $path = $request->file('image')->store('products', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            
            $validated['image_path'] = Storage::disk('s3')->url($path);
        }

        $product->update($validated);

        return back()->with('success', 'Product Updated!');
    }

   public function destroy($id)
{
    $product = Product::findOrFail($id);
    
    // --- DELETE IMAGE FROM BACKBLAZE ---
    if ($product->image_path) {
        try {
            // 1. Get the path from the URL (removes the domain name)
            $path = parse_url($product->image_path, PHP_URL_PATH);
            
            // 2. Clean up the path (remove leading slashes)
            $relativePath = ltrim($path, '/');
            
            // 3. (Optional but recommended) Handle bucket name appearing in path
            $bucketName = config('filesystems.disks.s3.bucket');
            if (str_starts_with($relativePath, $bucketName)) {
                $relativePath = ltrim(substr($relativePath, strlen($bucketName)), '/');
            }

            // 4. DIRECT DELETE - No "exists" check needed
            Storage::disk('s3')->delete($relativePath);

        } catch (\Exception $e) {
            // If the delete fails (e.g., connection error), we catch it here 
            // so the code continues and deletes the product from the Database anyway.
        }
    }

    $product->delete();
    return back()->with('success', 'Product Deleted!');
}
}