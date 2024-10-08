@extends('adminlte::page')

@section('title', $Imports ? 'Edit Imports Transaction' : 'Create Imports Transaction')

@section('content_header')

@stop
@include('partials.expiration.expire')
@section('content')

    <div class="container">
        <div class="card p-2">
            <div class="card-header bg-primary text-white">
                {{ $Imports ? 'Edit' : 'Create' }} Imports Transaction
            </div>
            <form id="purchase_form" method="POST"
                action="{{ $Imports ? route('imports.update', $Imports->id) : route('imports.store') }}">
                @csrf
                @if ($Imports)
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="import_number">import Number</label>
                            <input type="text" class="form-control" id="import_number" name="import_number"
                                value="{{ $Imports ? $Imports->import_number : old('import_number') }}"
                                placeholder="Enter import Number" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="purchase_date">Purchase Date</label>
                            <input type="text" class="form-control" id="purchase_date" name="purchase_date"
                                value="{{ $Imports ? $Imports->purchase_date : \Carbon\Carbon::now()->toDateString() }}">
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supplier_name">Supplier</label>
                            <input type="text" class="form-control" id="supplier_name" name="supplier_name"
                                value="{{ $Imports ? $Imports->supplier->supplier_name : old('supplier_name') }}"
                                placeholder="Enter Supplier Name" required>
                            <input id="supplier_id"
                                value="{{ $Imports ? $Imports->supplier->supplier_id : old('supplier_id') }}" type="hidden"
                                class="form-control supplier_id" name="supplier_id" required>
                        </div>
                    </div>
                    <div class="col-md-6">
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

                <table class="table table-bordered" id="purchase_table">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Item Name</th>
                            <th>Godwan</th>
                            <th>Shop</th>
                            <th>Shop Ashak</th>
                            <th>Shop Services</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Cost</th>
                            <th>Currency</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($Imports && $Imports->details->count())
                            @foreach ($Imports->details as $detail)
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
                                        <input readonly type="hidden" class="form-control unit_id"
                                            value="{{ $detail->unit->id }}"
                                            name="details[{{ $loop->iteration }}][unit_id]" required>
                                        <input readonly type="text" class="form-control unit"
                                            value="{{ $detail->unit->unit_name }}"
                                            name="details[{{ $loop->iteration }}][unit]" disabled>
                                    </td>
                                    <td>
                                        <input readonly type="number" class="form-control quantity"
                                            value="{{ $detail->quantity }}" min="1"
                                            name="details[{{ $loop->iteration }}][quantity]" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control cost" value="{{ $detail->cost }}"
                                            min="0" step="0.01" name="details[{{ $loop->iteration }}][cost]"
                                            required readonly>
                                    </td>
                                    <td>


                                        @php
                                            $currencyName = $currencies->firstWhere('id', $detail->currency_id)
                                                ?->currencie_name;
                                        @endphp
                                        <input type="text" class="form-control " value="{{ $currencyName }}" readonly>
                                        <input readonly type="hidden" class="form-control currency_id"
                                            value="{{ $detail->currency_id }}"
                                            name="details[{{ $loop->iteration }}][currency_id]" required>


                                    </td>
                                    <td>
                                        <input type="number" class="form-control total" value="{{ $detail->total }}"
                                            min="0" step="0.01" name="details[{{ $loop->iteration }}][total]"
                                            required readonly>
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
                                    value="0" disabled>
                            </th>
                            <th colspan="2" class="text-right">Total Amount:</th>
                            <th>
                                <input type="number" class="form-control" id="total_amount_table"
                                    name="total_amount_table"
                                    value="{{ $Imports ? $Imports->details->sum('total') : 0 }}" disabled>
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <div id="alert-container"></div>
                <div class="text-right mb-3">
                    <a href="{{ route('imports.index') }}" class="btn btn-danger">Discard</a>
                    <button type="submit" class="btn btn-success" id="save_btn">Save</button>
                </div>
            </form>

            <!-- Toasts for Success/Error Messages -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
                <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">Import transaction saved successfully!</div>
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
    @if (isset($Imports) && $Imports->details)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let itemId
                @foreach ($Imports->details as $detail)
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
        let rowIndex = {{ $Imports ? $Imports->details->count() + 1 : 1 }};
        let totalQuantity = 0;
        const items = @json($items); // Items fetched from the database
        const suppliers = @json($suppliers); // Supplier list
        const currencies = @json($currencies); // Currency list

        calculateTotals();
        updateTotalQuantity();

        // Supplier Autocomplete
        $("#supplier_name").autocomplete({
            source: suppliers.map(supplier => ({
                label: supplier.supplier_name + " - " + supplier.supplier_location,
                value: supplier.supplier_name,
                id: supplier.id
            })),
            minLength: 2,
            select: function(event, ui) {
                $('#supplier_id').val(ui.item.id);
            }
        });

        // Add Row button functionality
        $("#add_row_btn").click(function() {

            const newRow = `
                <tr>
                    <td>${rowIndex}</td>
                    <td>
                        <input type="text" class="form-control item-name" name="details[${rowIndex}][item_name]" required>
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
                        <input type="hidden" class="form-control unit-id" name="details[${rowIndex}][unit_id]" required>
                        <input type="text" class="form-control unit" name="details[${rowIndex}][unit]" disabled>
                    </td>
                    <td>
                        <input type="number" class="form-control quantity" name="details[${rowIndex}][quantity]" min="1" required>
                    </td>
                    <td>
                        <input type="number" class="form-control cost" name="details[${rowIndex}][cost]" min="0" step="0.01" required>
                    </td>
                    <td>
                        <select class="form-control currency" name="details[${rowIndex}][currency_id]" required>
                            ${currencies.map(currency => `<option value="${currency.id}">${currency.currencie_name}</option>`).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control total" name="details[${rowIndex}][total]" min="0" step="0.01" required readonly>
                    </td>
                    <td><button type="button" class="btn btn-danger remove-row-btn">Remove</button></td>
                </tr>
            `;
            $("#purchase_table tbody").append(newRow);
            rowIndex++;
            calculateTotals();

            // Update total quantity after adding a new row
            updateTotalQuantity();
            initializeAutocomplete($('#purchase_table tbody tr:last .item-name'));
        });

        // Remove Row functionality
        $(document).on('click', '.remove-row-btn', function() {
            $(this).closest('tr').remove();
            calculateTotals();
            updateTotalQuantity();
        });

        // Function to initialize autocomplete for each item name field
        function initializeAutocomplete(element) {
            $(element).autocomplete({
                source: items.map(item => item.item_name), // Autocomplete based on item names
                select: function(event, ui) {
                    const selectedItem = items.find(item => item.item_name === ui.item.value);
                    if (selectedItem) {
                        const row = $(this).closest('tr');
                        console.log("selectedItem = ", selectedItem)
                        // Populate hidden fields and others based on the selected item
                        row.find('.item-id').val(selectedItem.item_id);
                        row.find('.unit').val(selectedItem.unit_name);
                        row.find('.unit-id').val(selectedItem.unit_id);
                        row.find('.godown_quantity').val(selectedItem.godown_quantity ||
                            '0'); // Assuming you have godown info
                        row.find('.shop_quantity').val(selectedItem.shop_quantity ||
                            '0'); // Assuming you have shop info
                        row.find('.shop_ashak').val(selectedItem.shop_ashaks_quantity ||
                            '0'); // Assuming you have godown info
                        row.find('.shop_service').val(selectedItem.shop_service ||
                            '0'); // Assuming you have shop info

                    }
                }
            });
        }
        $(document).ready(function() {
            initializeAutocomplete($('#purchase_table tbody .item-name'));
            $('.toast').toast({
                autohide: true,
                delay: 3000
            }); // Initialize toast settings
        });

        // Calculate totals when quantity or cost changes
        $(document).on('input', '.quantity, .cost', function() {
            const row = $(this).closest('tr');
            const quantity = parseFloat(row.find('.quantity').val()) || 0;
            const cost = parseFloat(row.find('.cost').val()) || 0;
            const total = (quantity * cost).toFixed(2);
            row.find('.total').val(total);
            calculateTotals();
            updateTotalQuantity();
        });

        // Calculate Total Quantity
        function updateTotalQuantity() {
            totalQuantity = 0;
            $('.quantity').each(function() {
                totalQuantity += parseFloat($(this).val()) || 0;
            });
            $('#total_quantity').val(totalQuantity);
        }

        // Calculate Total Amount
        function calculateTotals() {
            let totalAmount = 0;
            $('.total').each(function() {
                totalAmount += parseFloat($(this).val()) || 0;
            });
            $('#total_amount_table').val(totalAmount.toFixed(2));
        }



        $('#save_btn').click(function(event) {







            event.preventDefault(); // Prevent the default form submission behavior

            // Remove previous error styles
            $('.form-control').removeClass('is-invalid');

            var tableData = [];
            var errorFound = false;

            // Clear any previous alerts
            $('#alert-container').empty();



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
                const form = $('#purchase_form')[0];

                // Create a FormData object
                const formData = new FormData(form); // This will include all form fields and the file input

                $.ajax({
                    url: $('#purchase_form').attr('action'), // The action URL of the form
                    method: 'POST', // The HTTP method to use
                    data: formData, // The serialized form data
                    processData: false, // Prevent jQuery from processing the data (required for FormData)
                    contentType: false, // Prevent jQuery from setting the content type (required for FormData)
                    success: function(response) {
                        // Show success toast
                        $('#successToast').toast('show');
                        window.location.href =
                            "{{ route('imports.index') }}"; // Redirect after success

                        // Optionally, clear form fields or reset the form (if needed)
                        $('#purchase_form')[0].reset();
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = '';

                        // Loop through errors and apply styles to invalid fields
                        for (let field in errors) {
                            errorMessage += errors[field].join(', ') + '\n';
                            $(`[name="${field}"]`).addClass(
                                'is-invalid'); // Add red border to the invalid fields
                        }

                        // Display the error message in the error toast
                        $('#errorToastMessage').text(errorMessage);
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
