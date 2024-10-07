@extends('adminlte::page')

@section('title', $adjustment ? 'Edit Stock Adjustment' : 'Create Stock Adjustment')

@section('content_header')

@stop

@section('content')
    @include('partials.expiration.expire')
    <div class="card p-2">
        <div class="card-header bg-primary text-white">
            {{ $adjustment ? 'Edit' : 'Create' }} Stock Adjustment
        </div>
        <div class="container">
            <form id="adjustment_form" method="POST"
                action="{{ $adjustment ? route('adjustments.update', $adjustment->id) : route('adjustments.store') }}">
                @csrf
                @if ($adjustment)
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="adjustment_number">Adjustment Number</label>
                            <input type="text" class="form-control" id="adjustment_number" name="adjustment_number"
                                value="{{ $adjustment ? $adjustment->adjustment_number : old('adjustment_number') }}"
                                placeholder="Enter Adjustment Number" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="adjustment_date">Adjustment Date</label>
                            <input type="text" class="form-control" id="adjustment_date" name="adjustment_date"
                                value="{{ $adjustment ? $adjustment->adjustment_date : \Carbon\Carbon::now()->toDateString() }}"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="shop" selected>Shop</option>
                                <option value="Godwan">Godwan</option>
                                <option value="shop_ashak">Shop (Ashak)</option>
                                <option value="shop_service">Shop (Service)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="text-right mb-3">
                    <button type="button" class="btn btn-primary" id="add_row_btn">Add Row</button>
                </div>

                <table class="table table-bordered" id="transfer_table">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Item Name</th>
                            <th>Godwan</th>
                            <th>Shop</th>
                            <th>Shop Ashak</th>
                            <th>Shop Services</th>
                            <th>Stock Type</th>

                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($adjustment && $adjustment->details->count())
                            @foreach ($adjustment->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <input type="text" class="form-control item-name"
                                            value="{{ $detail->item->item_name }}"
                                            name="details[{{ $loop->iteration }}][item_name]" readonly required>
                                        <input type="hidden" class="form-control item-id" value="{{ $detail->item_id }}"
                                            name="details[{{ $loop->iteration }}][item_id]" readonly required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control godown-quantity"
                                            id="godown-quantity-{{ $loop->iteration }}" readonly required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control shop-quantity"
                                            id="shop-quantity-{{ $loop->iteration }}" readonly required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control shop-ashak"
                                            id="shop-ashak-{{ $loop->iteration }}" readonly required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control shop-service"
                                            id="shop-service-{{ $loop->iteration }}" readonly required>
                                    </td>

                                    <td>
                                        <input type="text" class="form-control godown-quantity"
                                            id="godown-quantity-{{ $loop->iteration }}" readonly required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control shop-quantity"
                                            id="shop-quantity-{{ $loop->iteration }}" readonly required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control shop-ashak"
                                            id="shop-ashak-{{ $loop->iteration }}" readonly required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control shop-service"
                                            id="shop-service-{{ $loop->iteration }}" readonly required>
                                    </td>
                                    <td>
                                        <select class="form-control stock-type"
                                            name="details[{{ $loop->iteration }}][stock_type_id]" readonly required>
                                            @foreach ($stockTypes as $stockType)
                                                <option value="{{ $stockType->id }}"
                                                    {{ $detail->stock_type_id == $stockType->id ? 'selected' : '' }}>
                                                    {{ $stockType->stock_type_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>

                                        <input type="number" class="form-control quantity" value="{{ $detail->quantity }}"
                                            min="1" name="details[{{ $loop->iteration }}][quantity]" readonly
                                            required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control unit"
                                            value="{{ $detail->unit->unit_name }}"
                                            name="details[{{ $loop->iteration }}][unit_name]" readonly>
                                        <input type="hidden" class="form-control unit-id" value="{{ $detail->unit_id }}"
                                            name="details[{{ $loop->iteration }}][unit_id]" readonly required>
                                    </td>
                                    <td><button type="button" class="btn btn-danger remove-row-btn">Remove</button></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-right">Total Quantity:</th>
                            <th>
                                <input type="number" class="form-control" id="total_quantity" name="total_quantity"
                                    value="{{ $adjustment ? $adjustment->details->sum('quantity') : 0 }}" disabled>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
                <div id="alert-container"></div>
                <div class="text-right mb-3">
                    <a href="{{ route('adjustments.index') }}" class="btn btn-danger">Discard</a>
                    <button type="button" class="btn btn-success" id="save_btn">Save</button>
                </div>
            </form>

            <!-- Toasts for Success/Error Messages -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
                <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">Adjustment saved successfully!</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>

                <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" id="errorToastMessage">An error occurred!</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .form-control[disabled] {
            background-color: #e9ecef;
        }

        .is-invalid {
            border-color: red;
        }
    </style>
@stop

@section('js')
    @include('partials.import-cdn')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    @if (isset($adjustment) && $adjustment->details)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let itemId
                @foreach ($adjustment->details as $detail)
                    itemId = {{ $detail->item_id }};
                    // Update the input values using the JavaScript function
                    document.getElementById(`godown-quantity-{{ $loop->iteration }}`).value = getGodwanShopValue(
                        itemId, 'godown_quantity') || 0;
                    document.getElementById(`shop-quantity-{{ $loop->iteration }}`).value = getGodwanShopValue(
                        itemId, 'shop_quantity') || 0;
                    document.getElementById(`shop-ashak-{{ $loop->iteration }}`).value = getGodwanShopValue(
                        itemId, 'shop_ashak') || 0;
                    document.getElementById(`shop-service-{{ $loop->iteration }}`).value = getGodwanShopValue(
                        itemId, 'shop_service') || 0;
                @endforeach
            });
        </script>
    @endif
    <script>
        let rowIndex = {{ $adjustment ? $adjustment->details->count() + 1 : 1 }};

        // Data passed from the controller
        const items = @json($items); // Items fetched from the database
        const stockTypes = @json($stockTypes);

        // Function to calculate total quantity
        function calculateTotals() {
            let totalQuantity = 0;
            $('#transfer_table tbody tr').each(function() {
                const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                totalQuantity += quantity;
            });
            $('#total_quantity').val(totalQuantity.toFixed(2));
        }

        // Function to initialize autocomplete for each item name field
        function initializeAutocomplete(element) {
            $(element).autocomplete({
                source: items.map(item => item.item_name), // Autocomplete based on item names
                select: function(event, ui) {
                    const selectedItem = items.find(item => item.item_name === ui.item.value);
                    if (selectedItem) {
                        const row = $(this).closest('tr');
                        // Populate hidden fields and others based on the selected item
                        row.find('.item-id').val(selectedItem.item_id);
                        row.find('.godown_quantity').val(selectedItem.godown_quantity ||
                            '0'); // Assuming you have godown info
                        row.find('.shop_quantity').val(selectedItem.shop_quantity ||
                            '0'); // Assuming you have shop info
                        row.find('.shop_ashak').val(selectedItem.shop_ashaks_quantity ||
                            '0'); // Assuming you have godown info
                        row.find('.shop_service').val(selectedItem.shop_service ||
                            '0'); // Assuming you have shop info

                        row.find('.unit').val(selectedItem.unit_name);
                        row.find('.unit-id').val(selectedItem.unit_id);
                    }
                }
            });
        }

        // Function to add a new row
        function addRow() {
            let newRow = `
                <tr>
                    <td>${rowIndex}</td>
                    <td>
                        <input type="text" class="form-control item-name" placeholder="Item Name" name="details[${rowIndex}][item_name]" required>
                        <input type="hidden" class="form-control item-id" name="details[${rowIndex}][item_id]" required>
                    </td>
                     <td>
                                               <input  class="form-control godown_quantity"  disabled>


                    </td>

 <td>
                                               <input  class="form-control shop_quantity"  disabled>


                    </td>


                     <td>
                                               <input  class="form-control shop_ashak"  disabled>


                    </td>
                     <td>
                                               <input  class="form-control shop_service"  disabled>


                    </td>
                    <td>
                        <select class="form-control stock-type" name="details[${rowIndex}][stock_type_id]" required>

                            ${stockTypes.map(stockType => `<option value="${stockType.id}">${stockType.stock_type_name}</option>`).join('')}
                        </select>
                    </td>
                    <td><input type="number" class="form-control quantity" min="1" placeholder="Quantity" name="details[${rowIndex}][quantity]" required></td>
                    <td>
                        <input type="text" class="form-control unit" name="details[${rowIndex}][unit_name]" disabled>
                        <input type="hidden" class="form-control unit-id" name="details[${rowIndex}][unit_id]" required>
                    </td>
                    <td><button type="button" class="btn btn-danger remove-row-btn">Remove</button></td>
                </tr>
            `;
            $('#transfer_table tbody').append(newRow);
            rowIndex++;
            calculateTotals();

            // Initialize autocomplete for the new row
            initializeAutocomplete($('#transfer_table tbody tr:last .item-name'));
        }

        // Add a new row when the button is clicked
        $('#add_row_btn').click(function() {
            addRow();
        });

        // Update totals when the quantity input changes
        $(document).on('input', '.quantity', function() {
            calculateTotals();
        });

        // Remove row functionality
        $(document).on('click', '.remove-row-btn', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        // Initialize autocomplete for existing rows
        $(document).ready(function() {
            initializeAutocomplete($('#transfer_table tbody .item-name'));
            $('.toast').toast({
                autohide: true,
                delay: 3000
            }); // Initialize toast settings
        });

        // Save button functionality to make the API call
        $('#save_btn').click(function() {
            // Remove previous error styles
            $('.form-control').removeClass('is-invalid');

            var tableData = [];
            var errorFound = false;

            // Clear any previous alerts
            $('#alert-container').empty();

            // Loop through each row of the table
            $('#transfer_table tr').each(function(index, row) {
                var rowData = {};

                // Get quantity input value
                var quantity = $(row).find('.quantity').val();

                // Get godown quantity value (even though it's disabled)
                let classToUse = ""
                switch ($("#type").val()) {
                    case "Godwan":
                        classToUse = ".godown_quantity"
                        break;
                    case "shop":
                        classToUse = ".shop_quantity"
                        break;
                    case "shop_ashak":
                        classToUse = ".shop_ashak"
                        break;
                    case "shop_service":
                        classToUse = ".shop_service"
                        break;
                }

                var godownQuantity = $(row).find(classToUse).val();
                // Ensure there's valid data
                if (quantity && godownQuantity) {

                    // Check if     the quantity exceeds godown_quantity
                    if (parseInt(quantity) > parseInt(godownQuantity)) {
                        errorFound = true;

                        // Display error alert if quantity is more than godown quantity
                        $('#alert-container').append(`
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Error!</strong> Quantity (${quantity}) exceeds available ${$("#type").val()} quantity (${godownQuantity}) in row ${index }.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          `);
                    }

                    // Add this row's data to tableData array
                    tableData.push(rowData);
                }
            });


            if ($('#total_amount_table').val() <= 0) {
                errorFound = true;

                // Display error alert if quantity is more than godown quantity
                $('#alert-container').append(`
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Error!</strong> Total amount  must be greater then 0.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          `);
            }
            if (!errorFound) {
                const formData = $('#adjustment_form').serialize();
                $.ajax({
                    url: $('#adjustment_form').attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#successToast').toast('show');
                        window.location.href =
                            "{{ route('adjustments.index') }}"; // Redirect after success
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        for (let field in errors) {
                            errorMessage += errors[field].join(', ') + '\n';
                            // Add red border to fields with errors
                            $(`[name="${field}"]`).addClass('is-invalid');
                        }
                        $('#errorToastMessage').text('Error saving the adjustment: ' + errorMessage);
                        $('#errorToast').toast('show');
                    }
                });
            }

        });
        const getGodwanShopValue = (item_id, type) => {
            const items = @json($items);
            const item = items.find(el => el.item_id === item_id);

            return item[type] !== undefined ? item[type] : 0; // Returns undefined if the item is not found
        };
    </script>
@stop
