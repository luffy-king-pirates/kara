<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\Suppliers;
use App\Models\Item;
use App\Models\Units;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Models\Currency;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Style\Alignment; // Import Alignment
use App\Models\ShopService;
use App\Models\Shops;
use App\Models\ShopAshaks;
use App\Models\Godown;
use Illuminate\Support\Facades\Log;

class LocalPurshaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $purchaseRecords = Purchase::with([
                'details.item:id,item_name',
                'details.unit:id,unit_name',
                'supplier:id,supplier_name',
                'createdBy:id,name',
                'updatedBy:id,name'
            ])->select(['id', 'receipt_number', 'created_by', 'updated_by']);

            return DataTables::of($purchaseRecords)
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
                ->addColumn('supplier', function ($row) {

                    return $row->supplier ? $row->supplier->supplier_name : 'Unknown';
                })
                ->addColumn('details', function ($row) {
                    return $row->details->map(function ($detail) {
                        return [
                            'item' => $detail->item ? $detail->item->item_name : 'Unknown',
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
                            $q->where('receipt_number', 'like', "%$searchValue%")
                                ->orWhereHas('supplier', function ($q) use ($searchValue) {
                                    $q->where('supplier_name', 'like', "%$searchValue%");
                                });
                        });
                    }

                    if ($request->filled('receipt_number')) {
                        $query->where('receipt_number', 'like', "%" . $request->receipt_number . "%");
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
        $suppliers = Suppliers::all();

        return view('purshase.localPurshase.index', compact('users', 'suppliers'));
    }

    public function create()
    {
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
        $suppliers = Suppliers::all();
        $units = Units::all();
        $currencies = Currency::all();
        $purchase = null;

        return view('purshase.localPurshase.create', compact('items', 'currencies','suppliers', 'units', 'purchase'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'receipt_number' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'details.*.item_id' => 'required|exists:items,id',
            'details.*.unit_id' => 'required|exists:units,id',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.cost' => 'required|numeric|min:0',
            'details.*.total' => 'required|numeric|min:0',
            'pdf' => 'nullable|mimes:pdf|max:10000',
        ]);


        $purchase = new Purchase();
        $purchase->receipt_number = $request->input('receipt_number');
        $purchase->purchase_date = $request->input('purchase_date');
        $purchase->supplier_id = $request->input('supplier_id');
        $purchase->created_by = auth()->user()->id;
        $purchase->updated_by = auth()->user()->id;

        // Handle PDF file upload
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('purchases', 'public');
            $purchase->pdf = $pdfPath;
        }

        $purchase->save();
        foreach ($request->details as $detail) {
            $purchase->details()->create($detail);
        }



        // Log success message if the PDF was saved


        if ($request->type == 'Godwan') {
            Godown::addItemsFromTransfert($purchase);
        } elseif ($request->type == 'shop') {
            Shops::addItemsFromTransfert($purchase);
        } elseif ($request->type == 'shop_ashak') {
            ShopAshaks::addItemsFromTransfert($purchase);
        } elseif ($request->type == 'shop_service') {
            ShopService::addItemsFromTransfert($purchase);
        }

        return response()->json(['success' => true]);
    }


    public function show($id)
    {
        $purchase = Purchase::with('details.item', 'details.unit')->findOrFail($id);
        return view('purchase.localPurchase.show', compact('purchase'));
    }

    public function edit($id)
    {
        $purchase = Purchase::with(['details', 'supplier', 'details.unit'])->findOrFail($id);
        $items = Item::all();
        $units = Units::all();
        $suppliers = Suppliers::all();

        return view('purshase.localPurshase.create', compact('purchase', 'items', 'suppliers', 'units'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'purchase_number' => 'required|string',
            'total_amount' => 'required|numeric',
            'details.*.item_id' => 'required|integer',
            'details.*.unit_id' => 'required|integer',
            'details.*.currency_id' => 'required|integer',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.cost' => 'required|numeric|min:0',
            'details.*.total' => 'required|numeric|min:0',
        ]);

        $purchase = Purchase::findOrFail($id);
        $purchase->update([
            'purchase_number' => $validatedData['purchase_number'],
            'total_amount' => $validatedData['total_amount'],
            'updated_by' => auth()->user()->id,
        ]);

        foreach ($validatedData['details'] as $detail) {
            foreach ($validatedData['details'] as $detail) {
                PurchaseDetails::updateOrCreate(
                    ['purchase_id' => $purchase->id, 'item_id' => $detail['item_id']], // Fix typo here
                    [
                        'quantity' => $detail['quantity'],
                        'cost' => $detail['cost'],
                        'total' => $detail['total'],
                        'unit_id' => $detail['unit_id'],
                        'currency_id'
                    ]
                );
            }

        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->update(['is_deleted' => true]);
        return response()->json(['success' => true]);
    }
    public function details($id)
    {
        $godownshop = Purchase::with(['details.item', 'details.unit'])->findOrFail($id);
        return response()->json($godownshop);
    }


    public function exportDetails(Request $request, $id)
{
    // Fetch the purchase with details, item, unit, and currency relationships
    $purchase = Purchase::with([
        'details.item:id,item_name',
        'details.unit:id,unit_name',
        'details.currency:id,currencie_name',
        'supplier:id,supplier_name',
        'createdBy:id,name'
    ])->where('id', $id)->first();

    if (!$purchase) {
        return response()->json(['error' => 'Record not found'], 404);
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $row = 1;

    // Header Style
    $headerStyle = [
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']],
        'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
    ];

    // Purchase Details Header
    $sheet->setCellValue('A' . $row, 'Receipt Number');
    $sheet->setCellValue('B' . $row, $purchase->receipt_number);

    $sheet->setCellValue('A' . ($row + 1), 'Purchase Date');
    $sheet->setCellValue('B' . ($row + 1), $purchase->created_at->format('M d, Y'));
    $sheet->getStyle('A' . $row . ':B' . ($row + 1))->applyFromArray($headerStyle);
    $row += 3;

    // Set column headers for purchase details
    $sheet->setCellValue('A' . $row, 'Created By');
    $sheet->setCellValue('B' . $row, 'Item Name');
    $sheet->setCellValue('C' . $row, 'Unit');
    $sheet->setCellValue('D' . $row, 'Quantity');
    $sheet->setCellValue('E' . $row, 'Cost');
    $sheet->setCellValue('F' . $row, 'Currency');
    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($headerStyle);
    $row++;

    // Iterate over purchase details and populate rows
    foreach ($purchase->details as $detail) {
        $sheet->setCellValue('A' . $row, $purchase->createdBy->name);
        $sheet->setCellValue('B' . $row, $detail->item->item_name);
        $sheet->setCellValue('C' . $row, $detail->unit->unit_name);
        $sheet->setCellValue('D' . $row, $detail->quantity);
        $sheet->setCellValue('E' . $row, $detail->cost);
        $sheet->setCellValue('F' . $row, $detail->currency->currencie_name);
        $row++;
    }

    // Save the file and return it as a download
    $writer = new Xlsx($spreadsheet);
    $filePath = 'purchase_details_' . $purchase->id . '.xlsx';
    $writer->save(storage_path($filePath));

    return response()->download(storage_path($filePath))->deleteFileAfterSend(true);
}
public function export(Request $request)
{
    // Fetch all purchases with relationships to supplier, details, and users
    $purchases = Purchase::with([
        'details.item:id,item_name',
        'details.unit:id,unit_name',
        'details.currency:id,currencie_name',
        'supplier:id,supplier_name',
        'createdBy:id,name'
    ])->get();

    // Check if any records exist
    if ($purchases->isEmpty()) {
        return response()->json(['error' => 'No records found'], 404);
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $row = 1;

    // Title and Header Styles
    $titleStyle = [
        'font' => ['bold' => true, 'size' => 16],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ];

    $headerStyle = [
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']],
        'font' => ['bold' => true],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ];

    // Title
    $sheet->mergeCells('A1:F1');
    $sheet->setCellValue('A1', 'Purchase Summary');
    $sheet->getStyle('A1')->applyFromArray($titleStyle);
    $sheet->getRowDimension(1)->setRowHeight(30);

    // Column Headers
    $sheet->setCellValue('A2', 'Receipt Number');
    $sheet->setCellValue('B2', 'Purchase Date');
    $sheet->setCellValue('C2', 'Supplier');
    $sheet->setCellValue('D2', 'Item Name');
    $sheet->setCellValue('E2', 'Unit');
    $sheet->setCellValue('F2', 'Quantity');
    $sheet->setCellValue('G2', 'Cost');
    $sheet->setCellValue('H2', 'Currency');

    $sheet->getStyle('A2:H2')->applyFromArray($headerStyle);
    $row = 3;

    // Iterate over each purchase and purchase details
    foreach ($purchases as $purchase) {
        foreach ($purchase->details as $detail) {
            $sheet->setCellValue('A' . $row, $purchase->receipt_number);
            $sheet->setCellValue('B' . $row, $purchase->created_at->format('M d, Y'));
            $sheet->setCellValue('C' . $row, $purchase->supplier->supplier_name);
            $sheet->setCellValue('D' . $row, $detail->item->item_name);
            $sheet->setCellValue('E' . $row, $detail->unit->unit_name);
            $sheet->setCellValue('F' . $row, $detail->quantity);
            $sheet->setCellValue('G' . $row, $detail->cost);
            $sheet->setCellValue('H' . $row, $detail->currency->currencie_name);
            $row++;
        }
    }

    // Adjust column widths
    foreach (range('A', 'H') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Save the file and return it as a download
    $writer = new Xlsx($spreadsheet);
    $filePath = 'purchases_summary_' . now()->timestamp . '.xlsx';
    $writer->save(storage_path($filePath));

    return response()->download(storage_path($filePath))->deleteFileAfterSend(true);
}


}
