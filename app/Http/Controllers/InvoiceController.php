<?php

namespace App\Http\Controllers;

use App\Models\Proforma;
use App\Models\ProformaDetails;
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
use setasign\Fpdi\Fpdi;
class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        return view("pdf.cash");
    }
}
