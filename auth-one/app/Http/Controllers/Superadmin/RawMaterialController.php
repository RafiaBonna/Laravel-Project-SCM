<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RawMaterialController extends Controller
{
    // 1. To show Raw Material List (Index)
    public function index()
    {
        $rawMaterials = RawMaterial::orderBy('id', 'desc')->paginate(15);
        return view('superadmin.raw_materials.index', compact('rawMaterials'));
    }

    // 2. To show the form for adding new Material (Create Form)
    public function create()
    {
        return view('superadmin.raw_materials.create');
    }

    // 3. To store new Material in the database (Store)
    public function store(Request $request)
    {
        // Validation with custom error message
        $request->validate([
            'name' => 'required|string|max:255|unique:raw_materials,name',
            'unit_of_measure' => 'required|string|max:50',
            'description' => 'nullable|string',
        ], [
            'name.unique' => 'This raw material has already been added.',
            'name.required' => 'The raw material name is required.',
        ]);

        RawMaterial::create($request->all());

        return redirect()->route('superadmin.raw-materials.index')
                            ->with('success', 'Raw material added successfully.');
    }

    // 4. To show the form for editing Material (Edit Form)
    public function edit(RawMaterial $rawMaterial)
    {
        return view('superadmin.raw_materials.edit', compact('rawMaterial'));
    }

    // 5. To update the Material (Update)
    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate([
            // Check unique name ignoring the current ID during update
            'name' => ['required', 'string', 'max:255', Rule::unique('raw_materials')->ignore($rawMaterial->id)],
            'unit_of_measure' => 'required|string|max:50',
            'description' => 'nullable|string',
        ], [
            'name.unique' => 'This raw material has already been added.',
            'name.required' => 'The raw material name is required.',
        ]);

        $rawMaterial->update($request->all());

        return redirect()->route('superadmin.raw-materials.index')
                            ->with('success', 'Raw material updated successfully.');
    }

    // 6. To delete the Material (Delete)
    public function destroy(RawMaterial $rawMaterial)
    {
        try {
            $rawMaterial->delete();
            return redirect()->route('superadmin.raw-materials.index')
                             ->with('success', 'Raw material deleted successfully.');
        } catch (\Exception $e) {
            // If Foreign Key Constraint Violation occurs
            return back()->with('error', 'This raw material cannot be deleted as it is used in purchase or stock out records.');
        }
    }
    
    // ✅ FIX 7: To show Raw Material Stock Report (Stock Index)
    public function stockIndex()
    {
        // View Path Updated: Stock Report-এর জন্য একটি আলাদা ভিউ ব্যবহার করা হলো।
        // পরে আপনাকে resources/views/superadmin/raw_material_stock_out/stock_report.blade.php ফাইলটি তৈরি করতে হবে।
        
        // $stocks = \App\Models\RawMaterialStock::with('rawMaterial')->where('stock_quantity', '>', 0)->paginate(15);
        return view('superadmin.raw_material_stock_out.stock_report'); 
    }
}