@extends('adminlte::page')

@section('title', 'Godown to Shop Ashok')

@section('content_header')
    <h1>Godown to Shop Ashok</h1>
@stop

@section('content')
    <div class="container">
        <form>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="transfer_number">Transfer Number</label>
                        <input type="text" class="form-control" id="transfer_number" placeholder="Enter Transfer Number">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="creation_date">Creation Date</label>
                        <input type="text" class="form-control" id="creation_date" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="transfer_from">Transfer From</label>
                        <input type="text" class="form-control" id="transfer_from" value="Godown" disabled>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="transfer_to">Transfer To</label>
                        <input type="text" class="form-control" id="transfer_to" value="Shop Ashok" disabled>
                    </div>
                </div>
            </div>
        </form>

        <!-- Add Row Button positioned at the top right after the form -->
        <div class="text-right mb-3">
            <button class="btn btn-primary" id="add_row_btn">Add Row</button>
        </div>

        <!-- Table with Add Row functionality -->
        <table class="table" id="transfer_table">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Item Name</th>
                    <th>Godown</th>
                    <th>Shop Ashok</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be dynamically added here -->
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Total Quantity:</th>
                    <th>
                        <input type="number" class="form-control" id="total_quantity" value="0" disabled>
                    </th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
@stop

@section('css')
    {{-- Add jQuery UI CSS --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@stop

@section('js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        let rowIndex = 1;

        // Sample data for autocomplete (replace this with an AJAX call in production)
        const itemNames = [
            "Item 1",
            "Item 2",
            "Item 3",
            "Item 4",
            "Item 5"
            // Add more items as needed
        ];

        // Function to calculate total quantity
        function calculateTotalQuantity() {
            let total = 0;
            $('.quantity').each(function() {
                const value = parseInt($(this).val()) || 0; // Ensure it's a number
                total += value;
            });
            $('#total_quantity').val(total); // Update total quantity input
        }

        // Function to add a new row
        function addRow() {
            let newRow = `
                <tr>
                    <td>${rowIndex}</td>
                    <td><input type="text" class="form-control item-name" placeholder="Item Name"></td>
                    <td><input type="text" class="form-control" value="Godown" disabled></td>
                    <td><input type="text" class="form-control" value="Shop Ashok" disabled></td>
                    <td><input type="number" class="form-control quantity" min="1" placeholder="Quantity"></td>
                    <td><input type="text" class="form-control unit" disabled></td>
                    <td><button class="btn btn-danger remove-row-btn">Remove</button></td>
                </tr>
            `;
            $('#transfer_table tbody').append(newRow);
            rowIndex++;
            calculateTotalQuantity(); // Update total after adding a row

            // Initialize autocomplete for the new row
            initializeAutocomplete($('#transfer_table tbody tr:last .item-name'));
        }

        // Function to initialize autocomplete
        function initializeAutocomplete(element) {
            $(element).autocomplete({
                source: itemNames,
                select: function(event, ui) {
                    // Fetch Godown, Shop Ashok, and Unit values based on selected item
                    const selectedItem = ui.item.value;

                    // Replace with actual logic to fetch item details
                    const godownValue = `Godown for ${selectedItem}`;
                    const shopValue = `Shop Ashok for ${selectedItem}`;
                    const unitValue = `Unit for ${selectedItem}`;

                    $(this).closest('tr').find('input[disabled]').eq(0).val(godownValue);
                    $(this).closest('tr').find('input[disabled]').eq(1).val(shopValue);
                    $(this).closest('tr').find('.unit').val(unitValue);
                }
            });
        }

        // Add autocomplete to the first row
        $(document).ready(function() {
            initializeAutocomplete($('#transfer_table tbody .item-name'));
        });

        // Update total quantity when the quantity input changes
        $(document).on('input', '.quantity', function() {
            calculateTotalQuantity(); // Update total whenever a quantity is changed
        });

        // Add a new row when the button is clicked
        $('#add_row_btn').click(function() {
            addRow();
        });

        // Remove row functionality
        $(document).on('click', '.remove-row-btn', function() {
            $(this).closest('tr').remove();
            calculateTotalQuantity(); // Update total after removing a row
        });

        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
