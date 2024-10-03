@include('pdf.invoice')

@php
    $totalItems = $godownshop->details->count();
    $numIterations = $totalItems > 21 ? ceil($totalItems / 21) : 1; // Calculate number of iterations
    $itemsPerIteration = $numIterations === 1 ? ($headers === 'false' ? 19 : 13) : ($headers === 'false' ? 26 : 21);
@endphp

<style>
    table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    td,
    th {
        padding: 5px;
        word-wrap: break-word;
    }

    .first-column {
        width: 5%;
    }

    .description-column {
        width: 45%;
    }

    .specification-column {
        width: 20%;
    }

    .qty-column {
        width: 10%;
    }
</style>

@for ($i = 0; $i < $numIterations; $i++)
    @php
        $start = $i * $itemsPerIteration;
        $end = min($start + $itemsPerIteration - 1, $totalItems - 1);
        $slicedDetails = $godownshop->details->slice($start, $itemsPerIteration);
        $numDetails = $slicedDetails->count(); // Number of items in this iteration
    @endphp
    <div class="invoice-division">
        {{-- Include header if it's the first iteration --}}
        @if ($i === 0 && $headers === 'true')
            <header name="header1">
                <div><img class="img-fluid" src="https://res.cloudinary.com/dx8hb4haj/image/upload/v1727981117/ktlheader_g4yayf.jpg">
                </div>
            </header>
        @endif

        <table class="first-table" style="padding-top:20px">
            @if ($i === 0)
                <tbody style="text-align: center;">
                    <tr class="title title-ktl">
                        <td colspan="5"><b><u>INTERNAL TRANSFER</u></b></td>
                    </tr>
                    <tr>
                        <td colspan="5"><b>Godown to Shop</b></td>
                    </tr>
                </tbody>
                <tbody class="detail-table">
                    <tr class="h20">
                        <td class="label">TRANSFER PERSON</td>
                        <td class="value"><span class="not-set">(not set)</span></td>
                        <td width="12%"></td>
                        <td class="label">INTERNAL TRANSFER #</td>
                        <td class="value">{{ $godownshop->transfert_number }}</td>
                    </tr>
                    <tr class="h20">
                        <td class="label">TIME OUT:</td>
                        <td class="value"><span class="not-set">(not set)</span></td>
                        <td></td>
                        <td class="label">DATE</td>
                        <td class="value">{{ \Carbon\Carbon::parse($godownshop->transfert_date)->format('M d, Y') }}
                        </td>
                    </tr>
                    <tr class="h20">
                        <td class="label">TIME IN:</td>
                        <td class="value"><span class="not-set">(not set)</span></td>
                        <td></td>
                    </tr>
                </tbody>
            @endif
        </table>

        <table style="padding-top:20px">
            <tbody>
                <tr class="h40 table-header-ktl">
                    <td class="first-column">#</td>
                    <td class="description-column" colspan="2">DESCRIPTION</td>
                    <td class="specification-column">SPECIFICATION</td>
                    <td class="qty-column">QTY</td>
                </tr>

                {{-- Loop through the sliced details --}}
                @foreach ($slicedDetails as $index => $detail)
                    <tr class="h40 rows">
                        <td class="first-column">{{ $start + $index + 1 }}</td> {{-- Global index --}}
                        <td class="description-column" colspan="2">{{ $detail->item->item_name }}</td>
                        <td class="specification-column">{{ $detail->item->item_size }}</td>
                        <td class="qty-column">{{ $detail->quantity }} {{ $detail->unit->unit_name }}</td>
                    </tr>
                @endforeach

                {{-- Fill empty rows to ensure a consistent row count per iteration --}}
                @for ($j = $numDetails; $j < $itemsPerIteration; $j++)
                    <tr class="h40 rows">
                        <td class="first-column">{{ $start + $j + 1 }}</td> {{-- Global index --}}
                        <td class="description-column" colspan="2">&nbsp;</td>
                        <td class="specification-column">&nbsp;</td>
                        <td class="qty-column">&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        {{-- Display footer if it's the last iteration and headers are true --}}
        @if ($i === $numIterations - 1 && $headers === 'true')
            <table>
                <tbody>
                    <tr class="h40">
                        <td class="footer-label">SHOP REFERENCE NO:</td>
                        <td class="footer-value"></td>
                        <td style="width:28%">&nbsp;</td>
                        <td class="footer-label">AUTHORISED SIGNATURE:</td>
                        <td class="footer-value"></td>
                    </tr>
                    <tr class="h40">
                        <td class="footer-label h40">VEHICLE NO:</td>
                        <td class="footer-value"></td>
                        <td>&nbsp;</td>
                        <td class="footer-label">RECEIVED SIGNATURE:</td>
                        <td class="footer-value"></td>
                    </tr>
                </tbody>
            </table>
            <footer name="footer1">
                <div><img style="padding-top:20px" class="img-fluid"
                        src="https://res.cloudinary.com/dx8hb4haj/image/upload/v1727981224/ktlfooter_wgl0nz.png"></div>
            </footer>
        @endif
    </div>
@endfor
