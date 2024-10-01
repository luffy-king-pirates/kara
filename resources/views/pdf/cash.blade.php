@include('pdf.invoice')

@php

    $totalItems = $cash->details->count();
    $numIterations = $totalItems > 21 ? ceil($totalItems / 21) : 1; // Calculate number of iterations
    $itemsPerIteration = $numIterations === 1 ? ($headers === 'false' ? 19 : 13) : ($headers === 'false' ? 26 : 21);

@endphp

@for ($i = 0; $i < $numIterations; $i++)
    @php
        $start = $i * $itemsPerIteration;
        $end = min($start + $itemsPerIteration - 1, $totalItems - 1);
        $slicedDetails = $cash->details->slice($start, $itemsPerIteration);
        $numDetails = $slicedDetails->count(); // Get the number of sliced details
    @endphp
    <div class="invoice-division">

        @if ($start === 0)
            @if ($headers === 'true')
                <header name="header1">
                    <div class=""><img class="img-fluid"
                            src="http://ec2-13-246-17-70.af-south-1.compute.amazonaws.com/ktlheader.jpg"></div>
                </header>
            @endif
            <table class="first-table" style="padding-top:20px">
                <tbody>
                    <tr class="title title-ktl">
                        <td>
                            <b><u>CASH SALE</u></b>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table style="padding-top:20px">
                <tbody class="detail-table">
                    <tr class="h10address">
                        <td class="label-left-address">PO Box 993,</td>
                        <td></td>
                        <td></td>
                        <td class="label">DATE</td>
                        <td class="value">
                            @php

                                echo \Carbon\Carbon::parse($cash->creation_date)->format('M d, Y');
                            @endphp
                        </td>
                    </tr>
                    <tr class="h10address">
                        <td class="label-left-address">Cornser of India/Zanaki Street, Near Sabodo Parking,</td>
                        <td style="width: 1%;"></td>
                        <td style="font-size: 9pt;width: 30%; text-align: center;font-family:Calibri, sans-serif; border: .5px solid gray;line-height: 20px;"
                            rowspan="3" class="tin-vrn">
                            <b>TIN NUMBER: {{ $cash->customer->customer_tin }}<br>VRN NUMBER:
                                {{ $cash->customer->customer_vrn }}</b>
                        </td>
                        <td class="label">CASH SALE #</td>
                        <td class="value">{{ $cash->cash_number }}</td>
                    </tr>
                    <tr class="h10address">
                        <td class="label-left-address">Dar es Salaam, Tanzania.</td>
                        <td></td>
                        <td class="label">LPO #</td>
                        <td class="value"></td>
                    </tr>
                    <tr class="h10address">
                        <td class="label-left-address"><b>Whatsapp: +255 68 2140991</b></td>
                        <td></td>
                        <td class="label">LPO DATE</td>
                        <td class="value"></td>
                    </tr>
                </tbody>
            </table>

            <table class="detail-table" style="padding-top:20px">
                <tbody>
                    <tr class="h10details">
                        <td class="label-left-details">Company Name</td>
                        <td width="15%" class="text-center"><b>:</b></td>
                        <td colspan="4" class="value-left" style="text-transform: uppercase;">
                            {{ $cash->customer->customer_name }}</td>
                    </tr>
                    <tr class="h10details">
                        <td class="label-left-details">TIN Number</td>
                        <td width="15%" class="text-center"><b>:</b></td>
                        <td colspan="4" class="value-left">{{ $cash->customer->customer_tin }}</td>
                    </tr>
                    <tr class="h10details">
                        <td class="label-left-details">VRN Number</td>
                        <td width="15%" class="text-center"><b>:</b></td>
                        <td colspan="4" class="value-left">{{ $cash->customer->customer_vrn }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
        <table style="padding-top:20px">
            <tbody>
                <tr class="h40 table-header-ktl">
                    <td class="first-column" width="5%">#</td>
                    <td colspan="2">DESCRIPTION</td>
                    <td width="12%">SPECIFICATION</td>
                    <td width="7%">QTY</td>
                    <td width="10%" style="text-align: right;">UNIT PRICE</td>
                    <td width="10%" style="text-align: right;">TOTAL</td>
                </tr>
                @if ($cash && $cash->details->count())
                    {{-- Loop through the sliced details --}}
                    @foreach ($slicedDetails as $index => $detail)
                        @php
                            // Calculate the line total
                            $lineTotal = $detail->quantity * $detail->price;

                        @endphp

                        <tr class="h40 rows">
                            <td class="first-column">{{ $index + 1 }}</td> {{-- Adjusted counter --}}
                            <td colspan="2">{{ $detail->item->item_name }}</td>
                            <td>{{ $detail->item->item_size }}</td>
                            <td>{{ $detail->quantity }} {{ $detail->unit->unit_name }}</td>
                            <td style="text-align: right">{{ $detail->price }}</td>
                            <td style="text-align: right">{{ $lineTotal }}</td>
                        </tr>
                    @endforeach

                    {{-- Fill empty rows to ensure a total of 24 rows --}}
                    @for ($j = $numDetails; $j < $itemsPerIteration; $j++)
                        <tr class="h40 rows">
                            <td class="first-column">{{ $index + 1 + $j }}</td>
                            {{-- Adjusted counter --}}
                            <td colspan="2">&nbsp;</td> {{-- Empty columns --}}
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="text-align: right">&nbsp;</td>
                            <td style="text-align: right">&nbsp;</td>
                        </tr>
                    @endfor
                @endif



                @if ($end === $totalItems - 1)
                    <tr>
                        <td class="x1"></td>
                        <td colspan="2" class="x1"></td>
                        <td class="x1"></td>
                        <td class="x2"></td>
                        <td class="x3">SUBTOTAL</td>
                        <td class="x4" style="text-align: right"><b>total pefore</b></td>
                    </tr>
                    <tr>
                        <td class="x13ktl" colspan="4"><b>TERMS AND CONDITIONS :</b></td>


                    </tr>
                    <tr>
                        <td class="x24" colspan="4" style="border-right:.5pt solid gray">1. Our invoices are net
                            30
                            days payable monthly (Bank Interest will be charged on all overdue accounts)</td>
                        <td class="x6"></td>

                    </tr>
                    <tr>
                        <td class="x23" colspan="4" style="border-right:.5pt solid gray">2. Goods once sold,
                            delivered
                            and accepted by customer will not be returnable.</td>
                        <td class="x21"></td>
                        <td class="x3">VAT (18%)</td>
                        <td class="x4" style="text-align: right"><b>64,799.00</b></td>
                    </tr>
                    <tr>
                        <td class="x22" colspan="4" style="border-right:.5pt solid gray">3. We do not hold
                            ourselves
                            responsible for breakage and shortage of goods in transit.</td>
                        <td class="x6"></td>
                        <td class="x3"> TOTAL</td>
                        <td class="x19" style="text-align: right"><b>{{ $cash->total_amount }}</b></td>
                    </tr>
                    <tr>
                        <td style="height:8pt;"></td>
                        <td colspan="6" style="height:8pt;"></td>
                    </tr>
                @endif
            </tbody>
        </table>
        @if ($end === $totalItems - 1 && $headers === 'true')
            <footer name="footer1">
                <div class=""><img class="img-fluid"
                        src="http://ec2-13-246-17-70.af-south-1.compute.amazonaws.com/ktlfooter.png"></div>
            </footer>
        @endif
    </div>

@endfor





{{-- delivery  --}}

@php

    $totalItems = $cash->details->count();
    $numIterations = $totalItems > 21 ? ceil($totalItems / 21) : 1; // Calculate number of iterations
    $itemsPerIteration = $numIterations === 1 ? ($headers === 'false' ? 19 : 13) : ($headers === 'false' ? 26 : 21);

@endphp

@for ($i = 0; $i < $numIterations; $i++)
    @php
        $start = $i * $itemsPerIteration;
        $end = min($start + $itemsPerIteration - 1, $totalItems - 1);
        $slicedDetails = $cash->details->slice($start, $itemsPerIteration);
        $numDetails = $slicedDetails->count(); // Get the number of sliced details
    @endphp
    <div class="invoice-division">

        @if ($start === 0)
            @if ($headers === 'true')
                <header name="header1">
                    <div class=""><img class="img-fluid"
                            src="http://ec2-13-246-17-70.af-south-1.compute.amazonaws.com/ktlheader.jpg"></div>
                </header>
            @endif
            <table class="first-table" style="padding-top:20px">
                <tbody>
                    <tr class="title title-ktl">
                        <td>
                            <b><u>DELIVERY NOTE</u></b>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table style="padding-top:20px">
                <tbody class="detail-table">
                    <tr class="h10address">
                        <td class="label-left-address">PO Box 993,</td>
                        <td></td>
                        <td></td>
                        <td class="label">DATE</td>
                        <td class="value">
                            @php

                                echo \Carbon\Carbon::parse($cash->creation_date)->format('M d, Y');
                            @endphp
                        </td>
                    </tr>
                    <tr class="h10address">
                        <td class="label-left-address">Cornser of India/Zanaki Street, Near Sabodo Parking,</td>
                        <td style="width: 1%;"></td>
                        <td style="font-size: 9pt;width: 30%; text-align: center;font-family:Calibri, sans-serif; border: .5px solid gray;line-height: 20px;"
                            rowspan="3" class="tin-vrn">
                            <b>TIN NUMBER: {{ $cash->customer->customer_tin }}<br>VRN NUMBER:
                                {{ $cash->customer->customer_vrn }}</b>
                        </td>
                        <td class="label">DELIVERY NOTE #</td>
                        <td class="value">{{ $cash->cash_number }}</td>
                    </tr>
                    <tr class="h10address">
                        <td class="label-left-address">Dar es Salaam, Tanzania.</td>
                        <td></td>
                        <td class="label">LPO #</td>
                        <td class="value"></td>
                    </tr>
                    <tr class="h10address">
                        <td class="label-left-address"><b>Whatsapp: +255 68 2140991</b></td>
                        <td></td>
                        <td class="label">LPO DATE</td>
                        <td class="value"></td>
                    </tr>
                </tbody>
            </table>

            <table class="detail-table" style="padding-top:20px">
                <tbody>
                    <tr class="h10details">
                        <td class="label-left-details">Company Name</td>
                        <td width="15%" class="text-center"><b>:</b></td>
                        <td colspan="4" class="value-left" style="text-transform: uppercase;">
                            {{ $cash->customer->customer_name }}</td>
                    </tr>
                    <tr class="h10details">
                        <td class="label-left-details">TIN Number</td>
                        <td width="15%" class="text-center"><b>:</b></td>
                        <td colspan="4" class="value-left">{{ $cash->customer->customer_tin }}</td>
                    </tr>
                    <tr class="h10details">
                        <td class="label-left-details">VRN Number</td>
                        <td width="15%" class="text-center"><b>:</b></td>
                        <td colspan="4" class="value-left">{{ $cash->customer->customer_vrn }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
        <table style="padding-top:20px">
            <tbody>
                <tr class="h40 table-header-ktl">
                    <td class="first-column" width="5%">#</td>
                    <td colspan="2">DESCRIPTION</td>
                    <td width="12%">SPECIFICATION</td>
                    <td width="7%">QTY</td>
                    <td width="10%" style="text-align: right;">UNIT PRICE</td>
                    <td width="10%" style="text-align: right;">TOTAL</td>
                </tr>
                @if ($cash && $cash->details->count())
                    {{-- Loop through the sliced details --}}
                    @foreach ($slicedDetails as $index => $detail)
                        @php
                            // Calculate the line total
                            $lineTotal = $detail->quantity * $detail->price;

                        @endphp

                        <tr class="h40 rows">
                            <td class="first-column">{{ $index + 1 }}</td> {{-- Adjusted counter --}}
                            <td colspan="2">{{ $detail->item->item_name }}</td>
                            <td>{{ $detail->item->item_size }}</td>
                            <td>{{ $detail->quantity }} {{ $detail->unit->unit_name }}</td>
                            <td style="text-align: right">{{ $detail->price }}</td>
                            <td style="text-align: right">{{ $lineTotal }}</td>
                        </tr>
                    @endforeach

                    {{-- Fill empty rows to ensure a total of 24 rows --}}
                    @for ($j = $numDetails; $j < $itemsPerIteration; $j++)
                        <tr class="h40 rows">
                            <td class="first-column">{{ $index + 1 + $j }}</td>
                            {{-- Adjusted counter --}}
                            <td colspan="2">&nbsp;</td> {{-- Empty columns --}}
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="text-align: right">&nbsp;</td>
                            <td style="text-align: right">&nbsp;</td>
                        </tr>
                    @endfor
                @endif




            </tbody>
        </table>
        @if ($end === $totalItems - 1)
            <img class="img-fluid" style = "padding-top:32px"
                src="http://ec2-13-246-17-70.af-south-1.compute.amazonaws.com/proforma_sign.png">
            @if ($headers === 'true')
                <footer name="footer1">
                    <div class=""><img class="img-fluid"
                            src="http://ec2-13-246-17-70.af-south-1.compute.amazonaws.com/ktlfooter.png"></div>
                </footer>
            @endif
        @endif
    </div>

@endfor
