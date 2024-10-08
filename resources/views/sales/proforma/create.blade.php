@extends('adminlte::page')

@section('title', $proforma ? 'Edit Proforma Transaction' : 'Create Proforma Transaction')

@section('content_header')

@stop

@section('content')
    @include('partials.expiration.expire')
    <div class="container">
        <div class="card p-2">
            <div class="card-header bg-primary text-white">
                {{ $proforma ? 'Edit' : 'Create' }} Proforma Transaction
            </div>
            <form id="proforma_form" method="POST"
                action="{{ $proforma ? route('proforma.update', $proforma->id) : route('proforma.store') }}">
                @csrf
                @if ($proforma)
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="proforma_number">Proforma Number</label>
                            <input type="text" class="form-control" id="proforma_number" name="proforma_number"
                                value="{{ $proforma ? $proforma->proforma_number : old('proforma_number') }}"
                                placeholder="Enter Proforma Number" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="creation_date">Creation Date</label>
                            <input type="text" class="form-control" id="creation_date" name="creation_date"
                                value="{{ $proforma ? $proforma->creation_date : \Carbon\Carbon::now()->toDateString() }}"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="existence">Customer Existence</label>
                            <select class="form-control" id="existence" name="existence" required>
                                <option value="existing" selected>Existing Customer</option>
                                <option value="new">New Customer</option>
                            </select>
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

                <div class="row mb-3">
                    <div class="col-md-4" id="customer_name_div">
                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                value="{{ $proforma ? $proforma->customer->customer_name : old('customer_name') }}"
                                placeholder="Enter Customer Name" required>
                            <input id="customer_id"
                                value="{{ $proforma ? $proforma->customer->customer_id : old('customer_id') }}"
                                type="hidden" class="form-control customer_id" name="customer_id" required>
                            <input type="hidden" value="{{ $proforma ? $proforma->total_amount : old('total_amount') }}"
                                id="total_amount" class="form-control total_amount" name="total_amount" required>

                        </div>
                    </div>
                    <div class="col-md-4" id="customer_vin_div">
                        <div class="form-group">
                            <label for="customer_vin">Customer TIN</label>
                            <input type="text" class="form-control" id="customer_vin" name="customer_vin"
                                value="{{ $proforma ? $proforma->customer->customer_tin : old('customer_vin') }}"
                                placeholder="Enter Customer VIN" required>
                        </div>
                    </div>
                    <div class="col-md-4" id="vrn_number_div">
                        <div class="form-group">
                            <label for="vrn_number">VRN Number</label>
                            <input type="text" class="form-control" id="vrn_number"
                                value="{{ $proforma ? $proforma->customer->customer_vrn : old('customer_vrn') }}"
                                name="vrn_number" placeholder="Enter VRN Number" required>
                        </div>
                    </div>
                </div>

                <!-- Adding Percent Input Here -->
                <div class="row mb-3">
                    <div class="col-md-4" id="percent_div">
                        <div class="form-group">
                            <label for="percent">Percentage</label>
                            <input type="number" class="form-control" id="percent" name="percent"
                                placeholder="Enter Percentage" min="0" max="100" step="0.01">
                        </div>
                    </div>
                </div>

                <div class="text-right mb-3">
                    <button type="button" class="btn btn-primary" id="add_row_btn">Add Row</button>
                </div>

                <table class="table table-bordered" id="proforma_table">
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
                            <th>Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($proforma && $proforma->details->count())
                            @foreach ($proforma->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <input type="text" class="form-control item-name"
                                            value="{{ $detail->item->item_name }}" readonly
                                            name="details[{{ $loop->iteration }}][item_name]" required>
                                        <input type="hidden" class="form-control item-id"
                                            value="{{ $detail->item_id }}" readonly
                                            name="details[{{ $loop->iteration }}][item_id]" required>
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

                                        <input type="hidden" class="form-control unit_id"
                                            value="{{ $detail->unit->id }}"
                                            name="details[{{ $loop->iteration }}][unit_id]" readonly>

                                        <input type="text" class="form-control unit"
                                            value="{{ $detail->unit->unit_name }}"
                                            name="details[{{ $loop->iteration }}][unit]" readonly>
                                    </td>

                                    <td>

                                        <input type="number" class="form-control quantity"
                                            value="{{ $detail->quantity }}" min="1"
                                            name="details[{{ $loop->iteration }}][quantity]" readonly>
                                    </td>
                                    <td>

                                        <input type="number" class="form-control price" value="{{ $detail->price }}"
                                            min="0" step="0.01" name="details[{{ $loop->iteration }}][price]"
                                            readonly>
                                    </td>
                                    <td>

                                        <input type="number" class="form-control total" value="{{ $detail->total }}"
                                            min="0" step="0.01" name="details[{{ $loop->iteration }}][total]"
                                            readonly>
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
                            <th colspan="1" class="text-right">Total Amount:</th>
                            <th>
                                <input type="number" class="form-control" id="total_amount_table"
                                    name="total_amount_table"
                                    value="{{ $proforma ? $proforma->details->sum('total') : 0 }}" disabled>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <div class="row">
                    <div class="col-md-4">


                        <!-- Comment Section -->
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment</label>
                            <textarea name="comment" class="form-control" id="comment" rows="3">{{ $proforma ? $proforma->comment : old('comment') }}</textarea>

                        </div>

                        <!-- Special Relief Number -->
                        <div class="mb-3">
                            <label for="special_releif_number" class="form-label">Special Relief Number</label>
                            <input name="special_releif_number" type="text" class="form-control"
                                id="special_releif_number"
                                value="{{ $proforma ? $proforma->special_releif_number : old('special_releif_number') }}"
                                placeholder="Enter special relief number">
                        </div>
                    </div>

                    <div class="col-md-4">


                        <!-- Discount -->
                        <div class="mb-3">
                            <label for="discount" class="form-label">Discount</label>
                            <input type="number" name="discount" class="form-control" id="discount"
                                placeholder="Enter discount"
                                value="{{ $proforma ? $proforma->discount : old('discount') }}">
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" value="{{ $proforma ? $proforma->status : old('status') }}"
                                name="status" id="status">
                                <option value="Incomplete">Incomplete</option>
                                <option value="Print">Print</option>
                            </select>
                        </div>

                        <!-- LPO # -->
                        <div class="mb-3">
                            <label for="lpoNumber" class="form-label">LPO #</label>
                            <input type="text" value="{{ $proforma ? $proforma->lpo : old('lpo') }}" name="lpo"
                                class="form-control" id="lpoNumber" placeholder="Enter LPO number">
                        </div>

                        <!-- LPO Date -->
                        <div class="mb-3">
                            <label for="lpoDate" class="form-label">LPO Date</label>
                            <input type="date" name="lpo_date" class="form-control" id="lpoDate"
                                value="{{ $proforma ? $proforma->lpo_date : old('lpo_date') }}"
                                max="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Summary Section -->
                        <div class="">
                            <div class="border p-3 bg-light">
                                <p><strong>Subtotal:</strong> <span id="subtotal">0</span></p>
                                <p><strong>Discount:</strong> <span id="summaryDiscount">0</span></p>
                                <p><strong>Total:</strong> <span id="total">0</span></p>
                                <p><strong>VAT:</strong> <span id="vat">0</span></p>
                                <p><strong>Grand Total:</strong> <span id="grandTotal">0</span></p>
                            </div>
                        </div>
                    </div>
                    <div id="alert-container"></div>
                    <div class="text-right mb-3">
                        <a href="{{ route('proforma.index') }}" class="btn btn-danger">Discard</a>
                        <button type="button" class="btn btn-success" id="save_btn">Save</button>
                    </div>
                </div>
            </form>

            <!-- Toasts for Success/Error Messages -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
                <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">Proforma transaction saved successfully!</div>
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
    @if (isset($proforma) && $proforma->details)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let itemId
                @foreach ($proforma->details as $detail)
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
        $(document).ready(function() {
            let rowCount = $('#proforma_table tbody tr').length;
            let rowIndex = {{ $proforma ? $proforma->details->count() + 1 : 1 }};
            let totalQuantity = 0;
            const items = @json($items); // Items fetched from the database
            const customers = @json($customers);
            calculateTotals()
            updateTotalQuantity()
            // Show/hide fields based on the selected existence option
            $('#existence').change(function() {
                if ($(this).val() === 'existing') {
                    // Show customer input fields for existing customers
                    $('#customer_name_div, #customer_vin_div, #vrn_number_div').show();

                    // Implement autocomplete logic for existing customers
                    $('#customer_name').autocomplete({
                        source: customers.map(item => item.customer_name),
                        minLength: 2, // Minimum characters to start search
                        select: function(event, ui) {
                            console.log("event = ", event)
                            // Find the selected customer in the customers array
                            const selectedCustomer = customers.find(customer => customer
                                .customer_name ===
                                ui.item.value);
                            if (selectedCustomer) {
                                // Populate customer VIN and VRN number fields and disable them
                                $('#customer_vin').val(selectedCustomer.customer_tin).prop(
                                    'disabled',
                                    true);
                                console.log("selectedCustomer = ", selectedCustomer)
                                $('#customer_id').val(selectedCustomer.id);
                                $('#vrn_number').val(selectedCustomer.customer_vrn).prop(
                                    'disabled', true);
                            }
                        }
                    });
                } else {
                    // Hide the customer input fields for new customers
                    $('#customer_name_div, #customer_vin_div, #vrn_number_div')
                        .show(); // Keep these visible for new customers
                    $('#customer_name').val(''); // Clear the input
                    $('#customer_vin').val('').prop('disabled', false); // Enable and clear the input
                    $('#vrn_number').val('').prop('disabled', false); // Enable and clear the input
                }
            });
            // Trigger change event on page load to show initial state
            $('#existence').trigger('change');
            // Function to update totals
            function calculateTotals() {
                let totalAmount = 0;
                const percent = parseFloat($('#percent').val()) ||
                    0; // Get percentage value (e.g., discount or markup)
                const discount = parseFloat($('#discount').val()) || 0
                $('#proforma_table tbody tr').each(function() {
                    const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                    const price = parseFloat($(this).find('.price').val()) || 0;
                    const total = quantity * price;
                    $(this).find('.total').val(total.toFixed(2)); // Update total for the row
                    totalAmount += total; // Accumulate total amount
                });

                const percentAmount = totalAmount * (percent / 100);
                totalAmount -= percentAmount; // Subtract the percentage amount from the total

                $('#total_amount').val(totalAmount.toFixed(2));
                $('#subtotal').text(totalAmount.toFixed(2));

                $("#summaryDiscount").text(discount)
                $("#total").text(totalAmount - discount)

                $('#total_amount_table').val((totalAmount - discount).toFixed(2));
                $('#grandTotal').text((totalAmount - discount).toFixed(2));

            }

            function updateTotalQuantity() {
                let totalQuantity = 0; // Reset total quantity
                $('#proforma_table tbody tr').each(function() {
                    const quantity = parseFloat($(this).find('.quantity').val()) || 0;

                    totalQuantity += quantity;

                });

                // Display the total quantity in a designated field (assume you have an element with id="total_quantity_display")
                $('#total_quantity').val(totalQuantity); // Update the text
            }

            // Add a new row
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
                        <input type="text" class="form-control unit" name="details[${rowIndex}][unit_name]" disabled>
                        <input type="hidden" class="form-control unit-id" name="details[${rowIndex}][unit_id]" required>
                    </td>
                <td><input type="number" class="form-control quantity" min="1" placeholder="Quantity" name="details[${rowIndex}][quantity]" required></td>
                <td><input type="number" class="form-control price" min="0" step="0.01" placeholder="Price" name="details[${rowIndex}][price]" required></td>
                <td><input type="number" class="form-control total" name="details[${rowIndex}][total]" required readonly></td>
                <td><button type="button" class="btn btn-danger remove-row-btn">Remove</button></td>
            </tr>
        `;

                $('#proforma_table tbody').append(newRow);
                rowIndex++;
                calculateTotals();

                // Update total quantity after adding a new row
                updateTotalQuantity();
                initializeAutocomplete($('#proforma_table tbody tr:last .item-name'));
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

            // Initialize autocomplete for existing rows
            $(document).ready(function() {
                initializeAutocomplete($('#proforma_table tbody .item-name'));
                $('.toast').toast({
                    autohide: true,
                    delay: 3000
                }); // Initialize toast settings
            });

            // Add a new row when the button is clicked
            $('#add_row_btn').click(function() {
                addRow();
            });



            // Remove Row Button
            $(document).on('click', '.remove-row-btn', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            // Input change events to update totals
            $(document).on('input', '.quantity, .price', function() {
                calculateTotals();
            });

            $(document).on('input', '.quantity', function() {

                updateTotalQuantity()
            });

            $(document).on('input', '#discount', function() {

                calculateTotals()
            });

            // Save Button
            $('#save_btn').click(function() {



                // Loop through each row of the table
                var tableData = [];
                var errorFound = false;

                // Clear any previous alerts
                $('#alert-container').empty();

                // Loop through each row of the table
                $('#proforma_table tr').each(function(index, row) {
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
                    if ($('#proforma_form')[0].checkValidity()) {
                        $.ajax({
                            url: $('#proforma_form').attr('action'),
                            type: $('#proforma_form').attr('method'),
                            data: $('#proforma_form').serialize(),
                            success: function(response) {
                                $('#successToast').toast('show');
                                setTimeout(function() {
                                    window.location.href =
                                        '{{ route('proforma.index') }}';
                                }, 2000);
                            },
                            error: function(xhr) {
                                $('#errorToastMessage').text(xhr.responseText);
                                $('#errorToast').toast('show');
                            }
                        });
                    } else {
                        $('#proforma_form')[0].reportValidity();
                    }
                }


            });

        });
        const getGodwanShopValue = (item_id, type) => {
            const items = @json($items);
            const item = items.find(el => el.item_id === item_id);

            return item[type] !== undefined ? item[type] : 0; // Returns undefined if the item is not found
        };
    </script>
@stop
