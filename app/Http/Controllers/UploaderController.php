<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Units;
use Exception;

class UploaderController extends Controller
{
    public function upload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        // Get the uploaded file
        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $successes = [];
        $errors = [];

        try {
            // Load the spreadsheet
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Loop through rows and columns
            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                // Skip header row if any
                if ($rowIndex === 1) {
                    continue;
                }

                try {
                    $rowData = [];
                    foreach ($row->getCellIterator() as $cell) {
                        $rowData[] = $cell->getValue();
                    }

                    // Assume rowData indexes correspond to your model fields
                    Brand::create([
                        'brand_name' => $rowData[0],
                         'created_by' => auth()->user()->id
                        // Add more columns as needed
                    ]);

                    $successes[] = "Row $rowIndex was successfully saved.";
                } catch (Exception $e) {
                    // Catch errors for specific rows and continue
                    $errors[] = "Error on row $rowIndex: " . $e->getMessage();
                }
            }
        } catch (Exception $e) {
            // Handle file loading errors
            return redirect()->back()->with('error', 'Error loading the file: ' . $e->getMessage());
        }

        // Redirect back with success and error messages
        return redirect()->back()->with([
            'successes' => $successes,
            'errors'    => $errors
        ]);
    }
}
