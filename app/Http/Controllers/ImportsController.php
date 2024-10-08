<?php

namespace App\Http\Controllers;

use App\Models\Imports;
use App\Models\ImportDetails;
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

class ImportsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $ImportsRecords = Imports::with([
                'details.item:id,item_name',
                'details.unit:id,unit_name',
                'supplier:id,supplier_name',
                'createdBy:id,name',
                'updatedBy:id,name'
            ])->select(['id', 'import_number', 'created_by', 'updated_by']);

            return DataTables::of($ImportsRecords)
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
                            $q->where('import_number', 'like', "%$searchValue%")
                                ->orWhereHas('supplier', function ($q) use ($searchValue) {
                                    $q->where('supplier_name', 'like', "%$searchValue%");
                                });
                        });
                    }

                    if ($request->filled('import_number')) {
                        $query->where('import_number', 'like', "%" . $request->import_number . "%");
                    }


                    if ($request->filled('created_by')) {
                        $query->where('created_by', $request->created_by);
                    }

                    if ($request->filled('updated_by')) {
                        $query->where('updated_by', $request->updated_by);
                    }
                    $query->where('is_deleted', false);
                })
                ->make(true);
        }

        $users = User::all();
        $suppliers = Suppliers::all();

        return view('purshase.imports.index', [
            'users' => $users,
            'suppliers' => $suppliers,
            'canEditImports' => auth()->user()->can('update-imports'),
            'canDeleteImports' => auth()->user()->can('delete-imports'),
            'canExportImports' => auth()->user()->can('export-details-imports')
        ]);
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
        $Imports = null;

        return view('purshase.imports.create', compact('items', 'currencies','suppliers', 'units', 'Imports'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'import_number' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'details.*.item_id' => 'required|exists:items,id',
            'details.*.unit_id' => 'required|exists:units,id',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.cost' => 'required|numeric|min:0',
            'details.*.total' => 'required|numeric|min:0',

        ]);


        $Imports = new Imports();
        $Imports->import_number = $request->input('import_number');

        $Imports->supplier_id = $request->input('supplier_id');
        $Imports->created_by = auth()->user()->id;
        $Imports->updated_by = auth()->user()->id;


        $Imports->save();
        foreach ($request->details as $detail) {
            $Imports->details()->create($detail);
        }



        // Log success message if the PDF was saved


        if ($request->type == 'Godwan') {
            Godown::addItemsFromTransfert($Imports);
        } elseif ($request->type == 'shop') {
            Shops::addItemsFromTransfert($Imports);
        } elseif ($request->type == 'shop_ashak') {
            ShopAshaks::addItemsFromTransfert($Imports);
        } elseif ($request->type == 'shop_service') {
            ShopService::addItemsFromTransfert($Imports);
        }

        return response()->json(['success' => true]);
    }


    public function show($id)
    {
        $Imports = Imports::with('details.item', 'details.unit')->findOrFail($id);
        return view('Imports.localImports.show', compact('Imports'));
    }

    public function edit($id)
    {

        $Imports = Imports::with(['details', 'supplier', 'details.unit'])->findOrFail($id);
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
        $suppliers = Suppliers::all();
        $currencies = Currency::all();
        return view('purshase.imports.create', compact('Imports', 'items', 'suppliers', 'units','currencies'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'import_number' => 'required|string',
            'details.*.item_id' => 'required|integer',
            'details.*.unit_id' => 'required|integer',
            'details.*.currency_id' => 'required|integer',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.cost' => 'required|numeric|min:0',
            'details.*.total' => 'required|numeric|min:0',
        ]);

        $Imports = Imports::findOrFail($id);
        // Handle profile picture upload

        $Imports->update([
            'import_number' => $validatedData['import_number'],

            'updated_by' => auth()->user()->id,
        ]);

        foreach ($validatedData['details'] as $detail) {
            foreach ($validatedData['details'] as $detail) {
                ImportDetails::updateOrCreate(
                    ['imports_id' => $Imports->id, 'item_id' => $detail['item_id']], // Fix typo here
                    [
                        'quantity' => $detail['quantity'],
                        'cost' => $detail['cost'],
                        'total' => $detail['total'],
                        'unit_id' => $detail['unit_id'],
                        'currency_id' => $detail['currency_id']
                    ]
                );
            }

        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $Imports = Imports::findOrFail($id);
        $Imports->update(['is_deleted' => true]);
        return response()->json(['success' => true]);
    }
    public function details($id)
    {
        $godownshop = Imports::with(['details.item', 'details.unit'])->findOrFail($id);
        return response()->json($godownshop);
    }


    public function exportDetails(Request $request, $id)
{
    // Fetch the Imports with details, item, unit, and currency relationships
    $Imports = Imports::with([
        'details.item:id,item_name',
        'details.unit:id,unit_name',
        'details.currency:id,currencie_name',
        'supplier:id,supplier_name',
        'createdBy:id,name'
    ])->where('id', $id)->first();

    if (!$Imports) {
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

    // Imports Details Header
    $sheet->setCellValue('A' . $row, 'import Number');
    $sheet->setCellValue('B' . $row, $Imports->import_number);

    $sheet->setCellValue('A' . ($row + 1), 'Imports Date');
    $sheet->setCellValue('B' . ($row + 1), $Imports->created_at->format('M d, Y'));
    $sheet->getStyle('A' . $row . ':B' . ($row + 1))->applyFromArray($headerStyle);
    $row += 3;

    // Set column headers for Imports details
    $sheet->setCellValue('A' . $row, 'Created By');
    $sheet->setCellValue('B' . $row, 'Item Name');
    $sheet->setCellValue('C' . $row, 'Unit');
    $sheet->setCellValue('D' . $row, 'Quantity');
    $sheet->setCellValue('E' . $row, 'Cost');
    $sheet->setCellValue('F' . $row, 'Currency');
    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($headerStyle);
    $row++;

    // Iterate over Imports details and populate rows
    foreach ($Imports->details as $detail) {
        $sheet->setCellValue('A' . $row, $Imports->createdBy->name);
        $sheet->setCellValue('B' . $row, $detail->item->item_name);
        $sheet->setCellValue('C' . $row, $detail->unit->unit_name);
        $sheet->setCellValue('D' . $row, $detail->quantity);
        $sheet->setCellValue('E' . $row, $detail->cost);
        $sheet->setCellValue('F' . $row, $detail->currency->currencie_name);
        $row++;
    }

    // Save the file and return it as a download
    $writer = new Xlsx($spreadsheet);
    $filePath = 'Imports_details_' . $Imports->id . '.xlsx';
    $writer->save(storage_path($filePath));

    return response()->download(storage_path($filePath))->deleteFileAfterSend(true);
}
public function export(Request $request)
{
    // Fetch all Importss with relationships to supplier, details, and users
    $Importss = Imports::with([
        'details.item:id,item_name',
        'details.unit:id,unit_name',
        'details.currency:id,currencie_name',
        'supplier:id,supplier_name',
        'createdBy:id,name'
    ])->get();

    // Check if any records exist
    if ($Importss->isEmpty()) {
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
    $sheet->setCellValue('A1', 'Imports Summary');
    $sheet->getStyle('A1')->applyFromArray($titleStyle);
    $sheet->getRowDimension(1)->setRowHeight(30);

    // Column Headers
    $sheet->setCellValue('A2', 'import Number');
    $sheet->setCellValue('B2', 'Imports Date');
    $sheet->setCellValue('C2', 'Supplier');
    $sheet->setCellValue('D2', 'Item Name');
    $sheet->setCellValue('E2', 'Unit');
    $sheet->setCellValue('F2', 'Quantity');
    $sheet->setCellValue('G2', 'Cost');
    $sheet->setCellValue('H2', 'Currency');

    $sheet->getStyle('A2:H2')->applyFromArray($headerStyle);
    $row = 3;

    // Iterate over each Imports and Imports details
    foreach ($Importss as $Imports) {
        foreach ($Imports->details as $detail) {
            $sheet->setCellValue('A' . $row, $Imports->import_number);
            $sheet->setCellValue('B' . $row, $Imports->created_at->format('M d, Y'));
            $sheet->setCellValue('C' . $row, $Imports->supplier->supplier_name);
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
    $filePath = 'Imports_summary_' . now()->timestamp . '.xlsx';
    $writer->save(storage_path($filePath));

    return response()->download(storage_path($filePath))->deleteFileAfterSend(true);
}





}
