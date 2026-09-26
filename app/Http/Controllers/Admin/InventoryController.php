<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Condition filter
        if ($request->filled('item_condition')) {
            $query->where('item_condition', $request->item_condition);
        }

        $items = $query->paginate(10)->withQueryString();

        // Statistics
        $stats = [
            'total_items'    => Inventory::count(),
            'total_value'    => Inventory::sum('total_cost'),
            'low_stock'      => Inventory::where('status', 'Low Stock')->orWhereColumn('quantity', '<=', 'min_quantity_alert')->count(),
            'in_use_count'   => Inventory::where('status', 'In Use')->count(),
        ];

        $categories = Inventory::select('category')->distinct()->pluck('category');
        $locations  = Inventory::select('location')->whereNotNull('location')->distinct()->pluck('location');

        return view('pages.admin.inventory.index', compact('items', 'stats', 'categories', 'locations'));
    }

    public function create()
    {
        $categories = ['Electronics & IT', 'Furniture', 'Lab Equipment', 'Stationery', 'Sports Goods', 'Maintenance & Cleaning', 'Library Supplies', 'Other'];
        return view('pages.admin.inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code'          => 'nullable|string|max:100|unique:inventories,item_code',
            'item_name'          => 'required|string|max:255',
            'category'           => 'required|string|max:100',
            'quantity'           => 'required|integer|min:0',
            'min_quantity_alert' => 'nullable|integer|min:0',
            'unit'               => 'required|string|max:50',
            'unit_price'         => 'required|numeric|min:0',
            'location'           => 'nullable|string|max:255',
            'item_condition'     => 'required|string|max:100',
            'status'             => 'required|string|max:100',
            'purchase_date'      => 'nullable|date',
            'supplier_name'      => 'nullable|string|max:255',
            'supplier_contact'   => 'nullable|string|max:255',
            'invoice_no'         => 'nullable|string|max:255',
            'payment_status'     => 'nullable|string|max:50',
            'payment_method'     => 'nullable|string|max:50',
            'image_proof'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_proof_type'   => 'nullable|string|max:100',
            'notes'              => 'nullable|string',
        ]);

        if ($request->hasFile('image_proof')) {
            $path = $request->file('image_proof')->store('inventory_proofs', 'public');
            $validated['image_proof'] = $path;
        }

        $validated['total_cost'] = (float)$validated['quantity'] * (float)$validated['unit_price'];
        $validated['created_by'] = Auth::id();

        Inventory::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item added successfully with image proof!');
    }

    public function show($id)
    {
        $item = Inventory::with('creator')->findOrFail($id);
        return view('pages.admin.inventory.show', compact('item'));
    }

    public function edit($id)
    {
        $item = Inventory::findOrFail($id);
        $categories = ['Electronics & IT', 'Furniture', 'Lab Equipment', 'Stationery', 'Sports Goods', 'Maintenance & Cleaning', 'Library Supplies', 'Other'];
        return view('pages.admin.inventory.edit', compact('item', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $item = Inventory::findOrFail($id);

        $validated = $request->validate([
            'item_code'          => 'nullable|string|max:100|unique:inventories,item_code,' . $id,
            'item_name'          => 'required|string|max:255',
            'category'           => 'required|string|max:100',
            'quantity'           => 'required|integer|min:0',
            'min_quantity_alert' => 'nullable|integer|min:0',
            'unit'               => 'required|string|max:50',
            'unit_price'         => 'required|numeric|min:0',
            'location'           => 'nullable|string|max:255',
            'item_condition'     => 'required|string|max:100',
            'status'             => 'required|string|max:100',
            'purchase_date'      => 'nullable|date',
            'supplier_name'      => 'nullable|string|max:255',
            'supplier_contact'   => 'nullable|string|max:255',
            'invoice_no'         => 'nullable|string|max:255',
            'payment_status'     => 'nullable|string|max:50',
            'payment_method'     => 'nullable|string|max:50',
            'image_proof'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_proof_type'   => 'nullable|string|max:100',
            'notes'              => 'nullable|string',
        ]);

        if ($request->hasFile('image_proof')) {
            // Delete old file if stored locally
            if ($item->image_proof && !str_starts_with($item->image_proof, 'http')) {
                Storage::disk('public')->delete($item->image_proof);
            }
            $path = $request->file('image_proof')->store('inventory_proofs', 'public');
            $validated['image_proof'] = $path;
        }

        $validated['total_cost'] = (float)$validated['quantity'] * (float)$validated['unit_price'];

        $item->update($validated);

        return redirect()->route('inventory.show', $item->id)
            ->with('success', 'Inventory item updated successfully!');
    }

    public function destroy($id)
    {
        $item = Inventory::findOrFail($id);
        $item->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item deleted successfully!');
    }
}
