@extends('adminlte::page')

@section('title', $godownshop ? 'Edit Godown to Shop Transfer' : 'Create Godown to Shop Transfer')

@section('content_header')
    <h1>{{ $godownshop ? 'Edit' : 'Create' }} Godown to Shop Transfer</h1>
@stop

@section('content')
    <div class="card">
        <div class="container">
            <form id="godown_shop_form" method="POST"
                action="{{ $godownshop ? route(' godownshop.update', $godownshop->id) : route('godownshop.store') }}">
                @csrf
                @if ($godownshop)
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="transfert_number">Transfer Number</label>
                            <input type="text" class="form-control" id="transfert_number" name="transfert_number"
                                value="{{ $godownshop ? $godownshop->transfert_number : old('transfert_number') }}"
                                placeholder="Enter Transfer Number" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="transfert_date">Creation Date</label>
                            <input type="text" class="form-control form-transfert_date" id="transfert_date"
                                name="transfert_date"
                                value="{{ $godownshop ? $godownshop->transfert_date : \Carbon\Carbon::now()->toDateString() }}"
                                readonly>
                        </div>
                    </div>
                </div>


                <div class="text-right mb-3">
                    <button type="button" class="btn btn-primary" id="add_row_btn">Add Row</button>
                </div>

                <table class="table table-bordered" id="godown_shop_table">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Item Name</th>
                            <th>Godwan</th>
                            <th>Shop</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($godownshop && $godownshop->details->count())
                            @foreach ($godownshop->details as $detail)
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
                                        <input type="text" class="form-control unit"
                                            value="{{ $detail->unit->unit_name }}"
                                            name="details[{{ $loop->iteration }}][unit]" disabled>
                                        <input type="hidden" class="form-control unit-id" value="{{ $detail->unit->id }}"
                                            name="details[{{ $loop->iteration }}][unit_id]" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity" value="{{ $detail->quantity }}"
                                            min="1" name="details[{{ $loop->iteration }}][quantity]" required>
                                    </td>
                                    <td><button type="button" class="btn btn-danger remove-row-btn">Remove</button></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">Total Quantity:</th>
                            <th>
                                <input type="number" class="form-control" id="total_quantity" name="total_quantity"
                                    value="0" disabled>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <div class="text-right mb-3">
                    <a href="{{ route('godownshop.index') }}" class="btn btn-danger">Discard</a>
                    <button type="button" class="btn btn-success" id="save_btn">Save</button>
                </div>
            </form>

            <!-- Toasts for Success/Error Messages -->
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
                <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">Transfer saved successfully!</div>
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
        let rowIndex = {{ $godownshop ? $godownshop->details->count() + 1 : 1 }};
        const items = @json($items); // Items fetched from the database

        function calculateTotals() {
            let totalQuantity = 0;
            $('#godown_shop_table tbody tr').each(function() {
                const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                totalQuantity += quantity;
            });
            $('#total_quantity').val(totalQuantity);
        }

        function addRow() {
            let newRow = `
                <tr>
                    <td>${rowIndex}</td>
                    <td>
                        <input type="text" class="form-control item-name" placeholder="Item Name" name="details[${rowIndex}][item_name]" required>
                        <input type="hidden" class="form-control item-id" name="details[${rowIndex}][item_id]" required>
                    </td>
                       <td>
                                               <input  class="form-control item-godown_quantity"  disabled>


                    </td>
                   <td>
                                               <input  class="form-control item-shop_quantity"  disabled>


                    </td>

                    <td>
                        <input type="text" class="form-control unit" name="details[${rowIndex}][unit]" disabled>
                        <input type="hidden" class="form-control unit-id" name="details[${rowIndex}][unit_id]" required>
                    </td>
                    <td>
                        <input type="number"


                         class="form-control quantity " min="1" placeholder="Quantity" name="details[${rowIndex}][quantity]" required>
                    </td>
                    <td><button type="button" class="btn btn-danger remove-row-btn">Remove</button></td>
                </tr>
            `;

            $('#godown_shop_table tbody').append(newRow);
            rowIndex++;
            calculateTotals();
            initializeAutocomplete($('#godown_shop_table tbody tr:last .item-name'));
        }

        function initializeAutocomplete(element) {
            $(element).autocomplete({
                source: items.map(item => item.item_name), // Autocomplete based on item names
                select: function(event, ui) {
                    const selectedItem = items.find(item => item.item_name === ui.item.value);
                    if (selectedItem) {
                        console.log("sel godown_quantity", selectedItem)
                        const row = $(this).closest('tr');
                        row.find('.item-id').val(selectedItem.item_id);
                        row.find('.item-godown_quantity').val(selectedItem.godown_quantity);
                        row.find('.item-shop_quantity').val(selectedItem.shop_quantity);



                        row.find('.unit').val(selectedItem.unit_name);
                        row.find('.unit-id').val(selectedItem.unit_id);
                    }
                }
            });
        }

        $(document).ready(function() {
            initializeAutocomplete($('#godown_shop_table tbody .item-name'));

            $('#add_row_btn').click(function() {
                addRow();
            });

            $(document).on('input', '.quantity', function() {
                calculateTotals();
            });

            $(document).on('click', '.remove-row-btn', function() {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            $('#save_btn').click(function() {
                $('.form-control').removeClass('is-invalid');
                const formData = $('#godown_shop_form').serialize();
                $.ajax({
                    url: $('#godown_shop_form').attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#successToast').toast('show');
                            setTimeout(() => {
                                window.location.href =
                                    "{{ route('godownshop.index') }}";
                            }, 2000);
                        }
                    },
                    error: function(response) {
                        const errors = response.responseJSON.errors;
                        $.each(errors, function(field, messages) {

                            var formattedField = field;
                        
                            // If the field contains a dot (.), convert it to the bracketed format
                            if (field.includes('.')) {
                                var parts = field.split('.');
                                formattedField =
                                    `${parts[0]}[${parts[1]}][${parts[2]}]`;
                            }
                            $(`[name="${formattedField.replace(/\[/g, '\\[').replace(/\]/g, '\\]')}"]`)
                                .addClass('is-invalid');
                        });
                        $('#errorToastMessage').text('Please correct the highlighted errors.');
                        $('#errorToast').toast('show');
                    }
                });
            });
        });
    </script>
@stop
