@extends('adminlte::page')

@section('title', $cash ? 'Edit Cash Transaction' : 'Create Cash Transaction')

@section('content_header')

@stop

@section('content')
    <div class="container">
        <div class="card p-2">
            <div class="card-header bg-primary text-white">
                {{ $cash ? 'Edit' : 'Create' }} Cash Transaction
            </div>
            <form id="cash_form" method="POST" action="{{ $cash ? route('cash.update', $cash->id) : route('cash.store') }}">
                @csrf
                @if ($cash)
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cash_number">Cash Number</label>
                            <input type="text" class="form-control" id="cash_number" name="cash_number"
                                value="{{ $cash ? $cash->cash_number : old('cash_number') }}"
                                placeholder="Enter Cash Number" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="creation_date">Creation Date</label>
                            <input type="text" class="form-control" id="creation_date" name="creation_date"
                                value="{{ $cash ? $cash->creation_date : \Carbon\Carbon::now()->toDateString() }}" readonly>
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
                                value="{{ $cash ? $cash->customer->customer_name : old('customer_name') }}"
                                placeholder="Enter Customer Name" required>
                            <input id="customer_id" value="{{ $cash ? $cash->customer->customer_id : old('customer_id') }}"
                                type="hidden" class="form-control customer_id" name="customer_id" required>
                            <input type="hidden" value="{{ $cash ? $cash->total_amount : old('total_amount') }}"
                                id="total_amount" class="form-control total_amount" name="total_amount" required>

                        </div>
                    </div>
                    <div class="col-md-4" id="customer_vin_div">
                        <div class="form-group">
                            <label for="customer_vin">Customer TIN</label>
                            <input type="text" class="form-control" id="customer_vin" name="customer_vin"
                                value="{{ $cash ? $cash->customer->customer_tin : old('customer_vin') }}"
                                placeholder="Enter Customer VIN" required>
                        </div>
                    </div>
                    <div class="col-md-4" id="vrn_number_div">
                        <div class="form-group">
                            <label for="vrn_number">VRN Number</label>
                            <input type="text" class="form-control" id="vrn_number"
                                value="{{ $cash ? $cash->customer->customer_vrn : old('customer_vrn') }}" name="vrn_number"
                                placeholder="Enter VRN Number" required>
                        </div>
                    </div>
                </div>

                <!-- Adding Percent Input Here -->
                <div class="row mb-3">
                    <div class="col-md-4" id="percent_div">
                        <div class="form-group">
                            <label for="percent">Percentage</label>
                            <input type="number" class="form-control" id="percent" name="percent"
                                placeholder="Enter Percentage" min="0" max="100" step="0.01" required>
                        </div>
                    </div>
                </div>

                <div class="text-right mb-3">
                    <button type="button" class="btn btn-primary" id="add_row_btn">Add Row</button>
                </div>

                <table class="table table-bordered" id="cash_table">
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
                        @if ($cash && $cash->details->count())
                            @foreach ($cash->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <input type="text" class="form-control item-name"
                                            value="{{ $detail->item->item_name }}"
                                            name="details[{{ $loop->iteration }}][item_name]" required>
                                        <input type="hidden" class="form-control item-id"
                                            value="{{ $detail->item_id }}"
                                            name="details[{{ $loop->iteration }}][item_id]" required>
                                    </td>
                                    <td>

                                        <input type="hidden" class="form-control unit_id"
                                            value="{{ $detail->unit->id }}"
                                            name="details[{{ $loop->iteration }}][unit_id]" disabled>

                                        <input type="text" class="form-control unit"
                                            value="{{ $detail->unit->unit_name }}"
                                            name="details[{{ $loop->iteration }}][unit]" disabled>
                                    </td>

                                    <td>

                                        <input type="number" class="form-control quantity"
                                            value="{{ $detail->quantity }}" min="1"
                                            name="details[{{ $loop->iteration }}][quantity]" required>
                                    </td>
                                    <td>

                                        <input type="number" class="form-control price" value="{{ $detail->price }}"
                                            min="0" step="0.01" name="details[{{ $loop->iteration }}][price]"
                                            required>
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
                            <th colspan="1" class="text-right">Total Amount:</th>
                            <th>
                                <input type="number" class="form-control" id="total_amount_table"
                                    name="total_amount_table" value="{{ $cash ? $cash->details->sum('total') : 0 }}"
                                    disabled>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <div class="text-right mb-3">
                    <a href="{{ route('cash.index') }}" class="btn btn-danger">Discard</a>
                    <button type="button" class="btn btn-success" id="save_btn">Save</button>
                </div>
            </form>

            <!-- Toasts for Success/Error Messages -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
                <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">Cash transaction saved successfully!</div>
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
    <script>
        let rowIndex = {{ $cash ? $cash->details->count() + 1 : 1 }};
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
                        const selectedCustomer = customers.find(customer => customer.customer_name ===
                            ui.item.value);
                        if (selectedCustomer) {
                            // Populate customer VIN and VRN number fields and disable them
                            $('#customer_vin').val(selectedCustomer.customer_tin).prop('disabled',
                                true);
                            console.log("selectedCustomer = ", selectedCustomer)
                            $('#customer_id').val(selectedCustomer.id);
                            $('#vrn_number').val(selectedCustomer.customer_vrn).prop('disabled', true);
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
        // Function to calculate total amount
        function calculateTotals() {
            let totalAmount = 0;
            const percent = parseFloat($('#percent').val()) || 0; // Get percentage value (e.g., discount or markup)

            $('#cash_table tbody tr').each(function() {
                const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                const price = parseFloat($(this).find('.price').val()) || 0;
                const total = quantity * price;
                $(this).find('.total').val(total.toFixed(2)); // Update total for the row
                totalAmount += total; // Accumulate total amount
            });

            const percentAmount = totalAmount * (percent / 100);
            totalAmount -= percentAmount; // Subtract the percentage amount from the total

            $('#total_amount').val(totalAmount.toFixed(2));
            $('#total_amount_table').val(totalAmount.toFixed(2));

        }

        function updateTotalQuantity() {
            let totalQuantity = 0; // Reset total quantity
            $('#cash_table tbody tr').each(function() {
                const quantity = parseFloat($(this).find('.quantity').val()) || 0;

                totalQuantity += quantity;

            });
            console.log("totalQuantity = ", totalQuantity)
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

            $('#cash_table tbody').append(newRow);
            rowIndex++;
            calculateTotals();

            // Update total quantity after adding a new row
            updateTotalQuantity();
            initializeAutocomplete($('#cash_table tbody tr:last .item-name'));
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
            initializeAutocomplete($('#cash_table tbody .item-name'));
            $('.toast').toast({
                autohide: true,
                delay: 3000
            }); // Initialize toast settings
        });

        // Add a new row when the button is clicked
        $('#add_row_btn').click(function() {
            addRow();
        });

        // Update totals when quantity or price input changes
        $(document).on('input', '.quantity, .price', function() {
            calculateTotals();
        });

        $(document).on('input', '.quantity', function() {

            updateTotalQuantity()
        });

        $(document).on('input', '#percent', function() {

            calculateTotals()
        });


        // Remove row functionality
        $(document).on('click', '.remove-row-btn', function() {
            $(this).closest('tr').remove();
            calculateTotals();
            updateTotalQuantity()
        });

        // Save button functionality to make the API call
        $('#save_btn').click(function() {
            // Remove previous error styles
            $('.form-control').removeClass('is-invalid');

            const formData = $('#cash_form').serialize();
            $.ajax({
                url: $('#cash_form').attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#successToast').toast('show');
                    window.location.href = "{{ route('cash.index') }}"; // Redirect after success
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    for (let field in errors) {
                        errorMessage += errors[field].join(', ') + '\n';
                        // Add red border to fields with errors
                        $(`[name="${field}"]`).addClass('is-invalid');
                    }
                    $('#errorToastMessage').text(errorMessage);
                    $('#errorToast').toast('show');
                }
            });
        });
    </script>
@stop
