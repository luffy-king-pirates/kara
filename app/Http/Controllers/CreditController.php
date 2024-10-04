<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\CreditDetails;
use App\Models\Customers;
use App\Models\StockTypes;
use App\Models\Item;
use App\Models\Units;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $creditRecords = Credit::with([
                'details.item:id,item_name',
                'details.stockType:id,stock_type_name',
                'details.unit:id,unit_name',
                'customer:id,customer_name',
                'createdByUser:id,name',
                'updatedByUser:id,name'
            ])->select(['id', 'credit_number', 'creation_date', 'total_amount', 'created_by', 'updated_by']);

            return DataTables::of($creditRecords)
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
                ->addColumn('customer', function ($row) {
                    return $row->customer ? $row->customer->customer_name : 'Unknown';
                })
                ->addColumn('details', function ($row) {
                    return $row->details->map(function ($detail) {
                        return [
                            'item' => $detail->item ? $detail->item->item_name : 'Unknown',
                            'stock_type' => $detail->stockType ? $detail->stockType->stock_type_name : 'Unknown',
                            'unit' => $detail->unit ? $detail->unit->unit_name : 'Unknown',
                            'quantity' => $detail->quantity,
                            'price' => $detail->price,
                            'total' => $detail->total
                        ];
                    });
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];
                        $query->where(function ($q) use ($searchValue) {
                            $q->where('credit_number', 'like', "%$searchValue%")
                                ->orWhereHas('customer', function ($q) use ($searchValue) {
                                    $q->where('customer_name', 'like', "%$searchValue%");
                                });
                        });
                    }

                    if ($request->filled('credit_number')) {
                        $query->where('credit_number', 'like', "%" . $request->credit_number . "%");
                    }

                    if ($request->filled('creation_date')) {
                        $query->whereDate('creation_date', $request->creation_date);
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

        $users = User::all();
        $customers = Customers::all();

        return view('sales.credit.index', compact('users', 'customers'));
    }

    public function create()
    {
        $stockTypes = StockTypes::all();
        $result = Item::with(['unit', 'godown','shops','shopAshaks','shopService'])->get(['id', 'item_name', 'item_unit']);

        $items = $result->map(function ($item) {
            return [
                'item_name' => $item->item_name,
                'unit_name' => $item->unit ? $item->unit->unit_name : null,
                'item_id' => $item->id,
                'unit_id' => $item->unit ? $item->unit->id : null,
                'godown_quantity' => $item->godown ? $item->godown->quantity : 0,
                'shop_quantity' => $item->shops ? $item->shops->quantity : 0,
                'shop_ashaks_quantity' => $item->shopAshaks ? $item->shopAshaks->quantity : 0,
                'shop_service' => $item->shopService ? $item->shopService->quantity : 0,

            ];
        });
        $units = Units::all();
        $credit = null;
        $customers = Customers::all();
        return view('sales.credit.create', compact('stockTypes', 'items', 'units', 'customers', 'credit'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'credit_number' => 'required|string|max:255',
            'creation_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'details.*.item_id' => 'required|exists:items,id',
            'details.*.unit_id' => 'required|exists:units,id',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.price' => 'required|numeric|min:0',
            'details.*.total' => 'required|numeric|min:0',
        ]);

        $credit = Credit::create([
            'credit_number' => $request->credit_number,
            'creation_date' => $request->creation_date,
            'type' => $request->type,
            'total_amount' => number_format((float) $request->total_amount, 2, '.', ''),
            'customer_id' => $request->customer_id,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
        ]);

        foreach ($request->details as $detail) {
            $credit->details()->create($detail);
        }

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $credit = Credit::with('details.item', 'details.stockType', 'details.unit')->findOrFail($id);
        return view('credit.show', compact('credit'));
    }

    public function edit($id)
    {
        $credit = Credit::with(['details', 'customer', 'details.unit'])->findOrFail($id);
        $items = Item::all();
        $units = Units::all();
        $customers = Customers::all();

        return view('sales.credit.create', compact('credit', 'items', 'customers', 'units'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'credit_number' => 'required|string',
            'total_amount' => 'required|numeric',
            'details.*.item_id' => 'required|integer',
            'details.*.unit_id' => 'required|integer',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.price' => 'required|numeric|min:0',
            'details.*.total' => 'required|numeric|min:0',
        ]);

        $credit = Credit::findOrFail($id);
        $credit->update([
            'credit_number' => $validatedData['credit_number'],
            'total_amount' => $validatedData['total_amount'],
            'updated_by' => auth()->user()->id,
        ]);

        foreach ($validatedData['details'] as $detail) {
            CreditDetails::updateOrCreate(
                ['credit_id' => $credit->id, 'item_id' => $detail['item_id']],
                [
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                    'total' => $detail['total'],
                    'unit_id' => $detail['unit_id'],
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $credit = Credit::findOrFail($id);
        $credit->update(['is_deleted' => true]);
        return response()->json(['success' => true]);
    }


    public function details($id)
{
    $cash = Credit::with([
        'details.item',      // Load item details
        'details.unit',      // Load unit details
    ])->findOrFail($id);

    return response()->json($cash); // Return cash transaction with details
}
public function exportDetails(Request $request, $id)
{
    // Query credit by the specific ID
    $credit = Credit::query()
        ->with([
            'details.item:id,item_name',
            'details.unit:id,unit_name',
            'customer:id,customer_name',
            'createdByUser:id,name',
            'updatedByUser:id,name'
        ]) // Eager load relationships
        ->where('id', $id) // Filter by the given credit ID
        ->first();

    if (!$credit) {
        return response()->json(['error' => 'Credit record not found'], 404);
    }

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Initialize row counter
    $row = 1;

    // Define header styles (background color, font style, borders)
    $headerStyle = [
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFCCCCCC'], // Light gray background
        ],
        'font' => [
            'bold' => true,
            'color' => ['argb' => 'FF000000'] // Black font
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'], // Black border
            ],
        ],
    ];

    // Define alternate row styles
    $alternateRowStyle = [
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFE6F7FF'], // Light blue background
        ]
    ];

    // Set header for credit number and creation date
    $sheet->setCellValue('A' . $row, 'Credit Number');
    $sheet->setCellValue('B' . $row, $credit->credit_number);

    $sheet->setCellValue('A' . ($row + 1), 'Creation Date');
    $sheet->setCellValue('B' . ($row + 1), Carbon::parse($credit->creation_date)->format('M d, Y'));

    // Apply header style to these two rows
    $sheet->getStyle('A' . $row . ':B' . ($row + 1))->applyFromArray($headerStyle);

    // Skip to the next row for the table of details
    $row += 3;

    // Set headers for the details table
    $sheet->setCellValue('A' . $row, 'Customer Name');
    $sheet->setCellValue('B' . $row, 'Created By');
    $sheet->setCellValue('C' . $row, 'Updated By');
    $sheet->setCellValue('D' . $row, 'Item Name');
    $sheet->setCellValue('E' . $row, 'Unit');
    $sheet->setCellValue('F' . $row, 'Quantity');
    $sheet->setCellValue('G' . $row, 'Price');
    $sheet->setCellValue('H' . $row, 'Total');

    // Apply header style to details table header
    $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($headerStyle);

    $row++; // Move to the next row for detail entries

    // Initialize total amount variable
    $totalAmount = 0;

    // Check if there are details and loop through each detail in the credit record
    $isAlternateRow = false;
    if ($credit->details->isEmpty()) {
        // If no details are found, indicate in the spreadsheet
        $sheet->setCellValue('A' . $row, 'No details available for this credit record.');
    } else {
        foreach ($credit->details as $detail) {
            // Calculate total for each detail
            $lineTotal = $detail->quantity * $detail->price;
            $totalAmount += $lineTotal; // Add to the total amount

            // Insert details data
            $sheet->setCellValue('A' . $row, $credit->customer ? $credit->customer->customer_name : 'Unknown');
            $sheet->setCellValue('B' . $row, $credit->createdByUser ? $credit->createdByUser->name : 'Unknown');
            $sheet->setCellValue('C' . $row, $credit->updatedByUser ? $credit->updatedByUser->name : 'Unknown');
            $sheet->setCellValue('D' . $row, $detail->item ? $detail->item->item_name : 'Unknown');
            $sheet->setCellValue('E' . $row, $detail->unit ? $detail->unit->unit_name : 'Unknown');
            $sheet->setCellValue('F' . $row, $detail->quantity);
            $sheet->setCellValue('G' . $row, $detail->price);
            $sheet->setCellValue('H' . $row, $lineTotal);

            // Apply alternate row color for readability
            if ($isAlternateRow) {
                $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($alternateRowStyle);
            }

            $isAlternateRow = !$isAlternateRow; // Toggle row color
            $row++; // Move to the next row for the next detail
        }
    }

    // Add calculated total amount at the end
    $sheet->setCellValue('G' . $row, 'Total Amount');
    $sheet->setCellValue('H' . $row, $totalAmount);

    // Apply header style to the total row
    $sheet->getStyle('G' . $row . ':H' . $row)->applyFromArray($headerStyle);

    // Write the spreadsheet to a file (in memory)
    $writer = new Xlsx($spreadsheet);
    $fileName = 'credit_export_' . $id . '.xlsx';

    // Prepare the response for download
    return response()->stream(function() use ($writer) {
        // Disable output buffering
        ob_end_clean();
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        'Cache-Control' => 'max-age=0',
    ]);
}


public function generatePdf($id,$headers)
{
    // Fetch the Cash entry and its details
    $credit = Credit::with('details', 'createdByUser', 'customer')->findOrFail($id);

    // Pass the data to the PDF view
    $pdf = Pdf::loadView('pdf.credit', compact(['credit','headers']))->setOption('isRemoteEnabled', true); // Allow external resources;

    // Download or stream the PDF
    return $pdf->download('credit_transaction_' . $credit->id . '.pdf');
}

}
