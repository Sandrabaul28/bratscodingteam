<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HVCDP Print</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }

        /* Hide header and footer when printing */
        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 1cm;
            }
            /* Hide browser's default printing header/footer */
            header, footer {
                display: hidden;
            }
        }
    </style>
</head>
<body>
    <h4>HIGH VALUED CROPS DEVELOPMENT PROGRAM (HVCDP)</h4>
    
    @if($farmers->isNotEmpty())
        <h4>Barangay: {{ $farmers->first()->affiliation->name_of_barangay ?? 'N/A' }} - {{ $farmers->first()->affiliation->name_of_association }}</h4>
    @else
        <h4>Barangay: N/A</h4>
    @endif

    <table>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th colspan="4">NAME</th>
                <th style="background: lightgoldenrodyellow;" colspan="{{ $uniquePlants->count() }}"># OF TREES/HILL/PUNO</th>
            </tr>
            <tr>
                <th>SURNAME</th>
                <th>FIRSTNAME</th>
                <th>MI</th>
                <th>EXT</th>
                @foreach ($uniquePlants as $plant)
                    <th>{{ $plant }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $totals = array_fill(0, $uniquePlants->count(), 0); // Initialize totals array
            @endphp

            @foreach($farmers as $index => $farmer)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $farmer->last_name }}</td>
                    <td>{{ $farmer->first_name }}</td>
                    <td>{{ $farmer->middle_name }}</td>
                    <td>{{ $farmer->extension }}</td>
                    
                    @foreach ($uniquePlants as $plantIndex => $plant)
                        @php
                            $plantCount = $farmer->inventoryValuedCrops->where('plant.name_of_plants', $plant)->first()->count ?? 0;
                            $totals[$plantIndex] += $plantCount; // Add plant count to total
                        @endphp
                        <td>{{ $plantCount }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"><strong>Total</strong></td>
                @foreach ($totals as $total)
                    <td><strong>{{ $total }}</strong></td>
                @endforeach
            </tr>
        </tfoot>
    </table>

    <script>
        window.print();
    </script>
</body>
</html>
