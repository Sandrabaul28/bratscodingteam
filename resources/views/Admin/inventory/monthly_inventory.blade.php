<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Inventory Export</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #E7F0F7; /* Light background color */
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
            vertical-align: middle; /* Center-align text vertically */
        }
        th {
            color: white;
            font-weight: bold;
        }
        .sub-header {
            background-color: #A2C5E0; /* Light blue for sub-headers */
            font-weight: bold;
            text-align: center;
        }
        .centered {
            text-align: center; /* Center-align specific cells */
        }
        .rotated {
            transform: rotate(-90deg); /* Rotate text for headers */
            white-space: nowrap; /* Prevent text wrapping */
            font-size: 12px; /* Smaller font for rotated text */
            width: 20px; /* Adjust width as necessary */
            height: 50px; /* Adjust height for rotation */
            text-align: left; /* Align text to the left */
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th rowspan="3">BARANGAY</th>
                <th rowspan="3">COMMODITY</th>
                <th rowspan="3" class="centered">FARMER'S</th>
            </tr>
            <tr>
                <th rowspan="2" class="sub-header">Planting Density (ha)</th>
                <th rowspan="2" class="sub-header">Prodn. Vol/ Hectare (MT)</th>
                <th colspan="5" style="text-align: center;" class="sub-header"># Hill/Puno</th>
                <th colspan="5" style="text-align: center;" class="sub-header">PLANTED AREA (Ha)</th>
                <th rowspan="2" class="sub-header">AREA HARVESTED (HAS)</th>
                <th rowspan="2" class="sub-header">PRODUCTION VOLUME (MT)</th>
                <th rowspan="2" class="sub-header">LATITUDE</th>
                <th rowspan="2" class="sub-header">LONGITUDE</th>
                <th rowspan="2" class="sub-header">REFERENCE NO. / REFERENCE NO.</th>
                <th rowspan="2" class="sub-header">BIRTHDATE</th>
                <th rowspan="2" class="sub-header">AFFILIATION</th>


            </tr>
            <tr>
                <th class="sub-header">Newly Planted</th>
                <th class="sub-header">Vegetative</th>
                <th class="sub-header">Reproductive</th>
                <th class="sub-header">Maturity/ Harvested</th>
                <th class="sub-header">Total</th>
                <th class="sub-header">Newly Planted</th>
                <th class="sub-header">Vegetative</th>
                <th class="sub-header">Reproductive</th>
                <th class="sub-header">Maturity/ Harvested</th>
                <th class="sub-header">Total</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
