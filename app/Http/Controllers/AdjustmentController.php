<?php

namespace App\Http\Controllers;

use App\Models\Adjustment;
use App\Models\AdjustmentDetail;
use App\Models\Item;
use App\Models\StockTypes;
use App\Models\Units;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Carbon\Carbon;

class AdjustmentController extends Controller
{
    public function index(Request $request)
    {
        // Fetch necessary data for the view (e.g., users, stock types, items, units)
        $users = User::all();
        $stockTypes = StockTypes::all();
        $items = Item::all();
        $units = Units::all();

        if ($request->ajax()) {
            // Fetch adjustments with related details for DataTables
            $adjustments = Adjustment::with([
                'details.item:id,item_name',
                'details.stockType:id,stock_type_name',
                'details.unit:id,unit_name',
                'createdByUser:id,name',
                'updatedByUser:id,name'
            ])
            ->select(['id', 'adjustment_number', 'adjustment_date', 'created_at', 'updated_at', 'created_by', 'updated_by'])
            ->where('is_deleted', false); // Adjust based on your deletion logic

            return DataTables::of($adjustments)
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('M d, Y h:i A');
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at ? Carbon::parse($row->updated_at)->format('M d, Y h:i A') : 'Not updated';
                })
                ->addColumn('created_by', function ($row) {
                    return $row->createdByUser ? $row->createdByUser->name : 'Unknown';
                })
                ->addColumn('updated_by', function ($row) {
                    return $row->updatedByUser ? $row->updatedByUser->name : 'Not updated';
                })
                ->addColumn('details', function ($row) {
                    return $row->details->map(function($detail) {
                        return [
                            'item' => $detail->item ? $detail->item->item_name : 'Unknown',
                            'stock_type' => $detail->stockType ? $detail->stockType->stock_type_name : 'Unknown',
                            'unit' => $detail->unit ? $detail->unit->unit_name : 'Unknown',
                            'quantity' => $detail->quantity
                        ];
                    });
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function($q) use ($searchValue) {
                            $q->where('adjustment_number', 'like', "%$searchValue%")
                              ->orWhereHas('createdByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              })
                              ->orWhereHas('updatedByUser', function($q) use ($searchValue) {
                                  $q->where('name', 'like', "%$searchValue%");
                              });
                        });
                    }

                    if ($request->has('adjustment_number') && $request->adjustment_number != '') {
                        $query->where('adjustment_number', 'like', "%" . $request->adjustment_number . "%");
                    }

                    if ($request->has('adjustment_date') && $request->adjustment_date != '') {
                        $query->whereDate('adjustment_date', $request->adjustment_date);
                    }

                    if ($request->has('created_at') && $request->created_at != '') {
                        $query->whereDate('created_at', $request->created_at);
                    }

                    if ($request->has('updated_at') && $request->updated_at != '') {
                        $query->whereDate('updated_at', $request->updated_at);
                    }

                    if ($request->filled('created_by')) {
                        $query->where('created_by', $request->created_by);
                    }

                    if ($request->filled('updated_by')) {
                        $query->where('updated_by', $request->updated_by);
                    }
                })
                ->make(true);
        }

        // Pass necessary data to the view
        return view('adjustment.index', [
            'users' => $users,
            'stockTypes' => $stockTypes,
            'items' => $items,
            'units' => $units
        ]);
    }
    public function create()
    {
        $adjustment = null;
        $stockTypes = StockTypes::all();
        $units = Units::all();
        $result = Item::with('unit')->get(['id', 'item_name', 'item_unit']);

        // Transform the result to return only the needed fields
        $items = $result->map(function ($item) {
            return [
                'item_name' => $item->item_name,
                'unit_name' => $item->unit ? $item->unit->unit_name : null, // Get the unit name
                'item_id' => $item->id, // Now this will return the item ID
                'unit_id' => $item->unit ? $item->unit->id : null, // Unit ID
            ];
        });

        return view('adjustment.create', compact('items', 'stockTypes', 'units','adjustment'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'adjustment_number' => 'required|string|max:255',
            'adjustment_date' => 'required|date',
            'details.*.item_id' => 'required|exists:items,id',
            'details.*.stock_type_id' => 'required|exists:stock_types,id',
            'details.*.godown' => 'integer',
            'details.*.shop' => 'integer',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.unit_id' => 'required|exists:units,id',
        ]);

        $adjustment = Adjustment::create([
            'adjustment_number' => $request->adjustment_number,
            'adjustment_date' => $request->adjustment_date,
            'created_by' => auth()->user()->id,  // Add created_by field
            'updated_by' => auth()->user()->id   // Add updated_by field initially as the same user

        ]);

        foreach ($request->details as $detail) {
            $adjustment->details()->create($detail);
        }
        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $adjustment = Adjustment::with('details.item', 'details.stockType', 'details.unit')->findOrFail($id);
        return view('adjustments.show', compact('adjustment'));
    }


      // Method to get adjustment details for a specific adjustment
      public function details($id)
      {
          $adjustment = Adjustment::with([
              'details.item',      // Load item details
              'details.stockType', // Load stock type details
              'details.unit',      // Load unit details
          ])->findOrFail($id);

          return response()->json($adjustment); // Return adjustment with details
      }

    public function edit($id)
    {
        $adjustment = Adjustment::with('details')->findOrFail($id);
        $items = Item::all();
        $stockTypes = StockTypes::all();

        return view('adjustment.create', compact('adjustment', 'items', 'stockTypes'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'adjustment_number' => 'required|string',
            'details.*.item_id' => 'required|integer',
            'details.*.stock_type_id' => 'required|integer',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.unit_id' => 'required|integer',
            'details.*.godown' => 'nullable|integer', // Allow null or integer
            'details.*.shop' => 'nullable|integer',   // Allow null or integer
        ]);

        // Find the existing adjustment record
        $adjustment = Adjustment::findOrFail($id);
        $adjustment->update([
            'adjustment_number' => $validatedData['adjustment_number'],
            'updated_by' => auth()->user()->id, // Make sure to use id() instead of just id
        ]);

        // Update or create adjustment details
        foreach ($validatedData['details'] as $detail) {
            AdjustmentDetail::updateOrCreate(
                ['adjustment_id' => $adjustment->id, 'item_id' => $detail['item_id']],
                [
                    'stock_type_id' => $detail['stock_type_id'],
                    'quantity' => $detail['quantity'],
                    'unit_id' => $detail['unit_id'],
                    'godown' => !empty($detail['godown']) ? $detail['godown'] : null, // Set to null if empty
                    'shop' => !empty($detail['shop']) ? $detail['shop'] : null,       // Set to null if empty
                ]
            );
        }

        return response()->json(['success' => true]);
    }

}
