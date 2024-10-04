@extends('adminlte::page')

@section('title', $purchase ? 'Edit Purchase Transaction' : 'Create Purchase Transaction')

@section('content_header')
    <h1>{{ $purchase ? 'Edit' : 'Create' }} Purchase Transaction</h1>
@stop

@section('content')
    <div class="container">
        <form id="purchase_form" method="POST"
            action="{{ $purchase ? route('purchase.update', $purchase->id) : route('purchase.store') }}">
            @csrf
            @if ($purchase)
                @method('PUT')
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="receipt_number">Purchase Number</label>
                        <input type="text" class="form-control" id="receipt_number" name="receipt_number"
                            value="{{ $purchase ? $purchase->receipt_number : old('receipt_number') }}"
                            placeholder="Enter Receip Number" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="purchase_date">Purchase Date</label>
                        <input type="text" class="form-control" id="purchase_date" name="purchase_date"
                            value="{{ $purchase ? $purchase->purchase_date : \Carbon\Carbon::now()->toDateString() }}"
                            readonly>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_name">Supplier</label>
                        <input type="text" class="form-control" id="supplier_name" name="supplier_name"
                            value="{{ $purchase ? $purchase->supplier->supplier_name : old('supplier_name') }}"
                            placeholder="Enter Supplier Name" required>
                        <input id="supplier_id"
                            value="{{ $purchase ? $purchase->supplier->supplier_id : old('supplier_id') }}" type="hidden"
                            class="form-control supplier_id" name="supplier_id" required>
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
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Cost</th>
                        <th>Currency</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($purchase && $purchase->details->count())
                        @foreach ($purchase->details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <input type="text" class="form-control item-name"
                                        value="{{ $detail->item->item_name }}"
                                        name="details[{{ $loop->iteration }}][item_name]" required>
                                    <input type="hidden" class="form-control item-id" value="{{ $detail->item_id }}"
                                        name="details[{{ $loop->iteration }}][item_id]" required>
                                </td>
                                <td>
                                    <input type="hidden" class="form-control unit_id" value="{{ $detail->unit->id }}"
                                        name="details[{{ $loop->iteration }}][unit_id]" required>
                                    <input type="text" class="form-control unit" value="{{ $detail->unit->unit_name }}"
                                        name="details[{{ $loop->iteration }}][unit]" disabled>
                                </td>
                                <td>
                                    <input type="number" class="form-control quantity" value="{{ $detail->quantity }}"
                                        min="1" name="details[{{ $loop->iteration }}][quantity]" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control cost" value="{{ $detail->cost }}"
                                        min="0" step="0.01" name="details[{{ $loop->iteration }}][cost]"
                                        required>
                                </td>
                                <td>
                                    <select class="form-control currency"
                                        name="details[{{ $loop->iteration }}][currency_id]" required>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}"
                                                {{ $currency->id == $detail->currency_id ? 'selected' : '' }}>
                                                {{ $currency->currency_code }}
                                            </option>
                                        @endforeach
                                    </select>
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
                        <th colspan="3" class="text-right">Total Quantity:</th>
                        <th>
                            <input type="number" class="form-control" id="total_quantity" name="total_quantity"
                                value="0" disabled>
                        </th>
                        <th colspan="2" class="text-right">Total Amount:</th>
                        <th>
                            <input type="number" class="form-control" id="total_amount_table" name="total_amount_table"
                                value="{{ $purchase ? $purchase->details->sum('total') : 0 }}" disabled>
                        </th>
                    </tr>
                </tfoot>
            </table>

            <div class="text-right mb-3">
                <a href="{{ route('purchase.index') }}" class="btn btn-danger">Discard</a>
                <button type="submit" class="btn btn-success" id="save_btn">Save</button>
            </div>
        </form>

        <!-- Toasts for Success/Error Messages -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
            <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">Purchase transaction saved successfully!</div>
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
        let rowIndex = {{ $purchase ? $purchase->details->count() + 1 : 1 }};
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
                        row.find('.item-id').val(selectedItem.id);
                        row.find('.unit').val(selectedItem.unit.unit_name);
                        row.find('.unit-id').val(selectedItem.unit.id);

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

            // Serialize the form data
            const formData = $('#purchase_form').serialize();

            $.ajax({
                url: $('#purchase_form').attr('action'), // The action URL of the form
                method: 'POST', // The HTTP method to use
                data: formData, // The serialized form data
                success: function(response) {
                    // Show success toast
                    $('#successToast').toast('show');
                    window.location.href = "{{ route('purchase.index') }}"; // Redirect after success

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
        });
    </script>
@stop
