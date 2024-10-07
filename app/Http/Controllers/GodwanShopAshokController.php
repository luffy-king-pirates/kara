<?php

namespace App\Http\Controllers;

use App\Models\Transfert;
use App\Models\TransfertDetails;
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
use App\Models\Godown;
use App\Models\ShopAshaks;
class GodwanShopAshokController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $records = Transfert::with([
                'details.item:id,item_name',
                'details.unit:id,unit_name',
                'createdByUser:id,name',
                'updatedByUser:id,name'
            ])->select(['id', 'transfert_number', 'transfert_date', 'created_by', 'updated_by'])
            ->where([
                'transfert_from' => 'godown',
                'transfert_to' => 'shop'
            ]);
            return DataTables::of($records)
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
                            $q->where('transfert_number', 'like', "%$searchValue%")
                                ->orWhereHas('createdByUser', function ($q) use ($searchValue) {
                                    $q->where('name', 'like', "%$searchValue%");
                                });
                        });
                    }

                    if ($request->filled('transfert_number')) {
                        $query->where('transfert_number', 'like', "%" . $request->transfert_number . "%");
                    }
                    $query->where('is_deleted', false);
                })
                ->make(true);
        }

        $users = User::all();

        return view('stock-transfert.godown-to-shop-ashok.index', compact('users'));
    }

    public function create()
    {
        $units = Item::with('unit')->get(['id', 'item_name', 'item_unit']);
        $result = Item::with(['unit', 'godown','shopAshaks'])->get(['id', 'item_name', 'item_unit']);

$items = $result->map(function ($item) {
    return [
        'item_name' => $item->item_name,
        'unit_name' => $item->unit ? $item->unit->unit_name : null,
        'item_id' => $item->id,
        'unit_id' => $item->unit ? $item->unit->id : null,
        'godown_quantity' => $item->godown ? $item->godown->quantity : 0,
        'shop_quantity' => $item->shopAshaks ? $item->shopAshaks->quantity : 0
    ];
});


        $godownshop = null;
        $units = Units::all();
        return view('stock-transfert.godown-to-shop-ashok.create', compact('items', 'units','godownshop'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'transfert_number' => 'required|string|max:255',
            'transfert_date' => 'required|date',
            'details.*.item_id' => 'required|exists:items,id',
            'details.*.unit_id' => 'required|exists:units,id',
            'details.*.quantity' => 'required|numeric|min:1',
        ]);

        $godownshop = Transfert::create([
            'transfert_number' => $request->transfert_number,
            'transfert_date' => $request->transfert_date,
            'transfert_from' =>'godown',
            'transfert_to' => 'shop_ashok',
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
        ]);

        foreach ($request->details as $detail) {
            $godownshop->details()->create($detail);
        }
  // Check if transfert_to is a godown
  if ($godownshop->transfert_to == 'godown') {
    // Add items to godown
    Godown::addItemsFromTransfert($godownshop);
}

// Check if transfert_from is a godown
if ($godownshop->transfert_from == 'shop_ashok') {
    // Remove items from godown
    ShopAshaks::removeItemsFromTransfert($godownshop);
}



        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $godownshop = Transfert::with('details.item', 'details.unit')->findOrFail($id);
        return view('stock-transfert.godown-to-shop-ashok.show', compact('godownshop'));
    }

    public function edit($id)
    {
        $godownshop = Transfert::with(['details'])->findOrFail($id);
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

        return view('stock-transfert.godown-to-shop-ashok.create', compact('godownshop', 'items', 'units'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'transfert_number' => 'required|string|max:255',
            'details.*.item_id' => 'required|integer',
            'details.*.unit_id' => 'required|integer',
            'details.*.quantity' => 'required|numeric|min:1',

        ]);

        $godownshop = Transfert::findOrFail($id);
        $godownshop->update([
            'transfert_number' => $validatedData['transfert_number'],
            'updated_by' => auth()->user()->id,
        ]);

        foreach ($validatedData['details'] as $detail) {
            TransfertDetails::updateOrCreate(
                ['transfert_id' => $godownshop->id, 'item_id' => $detail['item_id']],
                [
                    'quantity' => $detail['quantity'],

                    'unit_id' => $detail['unit_id'],
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $godownshop = Transfert::findOrFail($id);
        $godownshop->update(['is_deleted' => true]);
        return response()->json(['success' => true]);
    }

    public function details($id)
    {
        $godownshop = Transfert::with(['details.item', 'details.unit'])->findOrFail($id);
        return response()->json($godownshop);
    }

    public function exportDetails(Request $request, $id)
    {
        $godwanShop = Transfert::with([
            'details.item:id,item_name',
            'details.unit:id,unit_name',
            'createdByUser:id,name',
            'updatedByUser:id,name'
        ])->where('id', $id)->first();

        if (!$godwanShop) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;

        $headerStyle = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']],
            'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
        ];

        $sheet->setCellValue('A' . $row, 'Transfert Number');
        $sheet->setCellValue('B' . $row, $godwanShop->transfert_number);

        $sheet->setCellValue('A' . ($row + 1), 'Transfert Date');
        $sheet->setCellValue('B' . ($row + 1), Carbon::parse($godwanShop->transfert_date)->format('M d, Y'));
        $sheet->getStyle('A' . $row . ':B' . ($row + 1))->applyFromArray($headerStyle);
        $row += 3;

        $sheet->setCellValue('A' . $row, 'Created By');
        $sheet->setCellValue('B' . $row, 'Item Name');
        $sheet->setCellValue('C' . $row, 'Unit');
        $sheet->setCellValue('D' . $row, 'Quantity');

        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($headerStyle);
        $row++;

        foreach ($godwanShop->details as $detail) {
            $sheet->setCellValue('A' . $row, $godwanShop->createdByUser->name);
            $sheet->setCellValue('B' . $row, $detail->item->item_name);
            $sheet->setCellValue('C' . $row, $detail->unit->unit_name);
            $sheet->setCellValue('D' . $row, $detail->quantity);

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filePath = 'godown_to_shop_ashok' . $godwanShop->id . '.xlsx';
        $writer->save(storage_path($filePath));

        return response()->download(storage_path($filePath))->deleteFileAfterSend(true);
    }

    public function generatePdf($id,$headers)
{
    // Fetch the Cash entry and its details
    $godownshop = Transfert::with('details', 'createdByUser')->findOrFail($id);

    // Pass the data to the PDF view
    $pdf = Pdf::loadView('pdf.godownToshopAshok', compact(['godownshop','headers']))->setOption('isRemoteEnabled', true); // Allow external resources;

    // Download or stream the PDF
    return $pdf->download('godown_to_shop_ashok_transaction' . $godownshop->id . '.pdf');
}

}
