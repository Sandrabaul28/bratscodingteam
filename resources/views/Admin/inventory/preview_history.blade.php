<table class="table table-bordered">
    <thead>
        <tr>
            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Barangay</th>
            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Association</th>
            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Commodity</th>
            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Farmers</th>
            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Planting Density (ha)</th>
            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Production Vol/ Hectare (MT)</th>
            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Newly Planted</th>
            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Vegetative</th>
            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Reproductive</th>
            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Maturity Harvested</th>
            <th style="position: sticky; top: 0; background: greenyellow; z-index: 1;">Total/ #hill/puno</th>
            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Newly Planted/ha</th>
            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Vegetative/ha</th>
            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Reproductive/ha</th>
            <th style="position: sticky; top: 0; background: lightgray; z-index: 1;">Maturity Harvested/ha</th>
            <th style="position: sticky; top: 0; background: greenyellow; z-index: 1;">Total/ Planted Area</th>
            <th style="position: sticky; top: 0; background: orange; z-index: 1;">Area Harvested</th>
            <th style="position: sticky; top: 0; background: orange; z-index: 1;">Production Volume (MT)</th>
            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Latitude</th>
            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Longitude</th>
            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Control Number</th>
            <th style="position: sticky; top: 0; background: yellow; z-index: 1;">Birthdate</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inventories as $inventory)
            <tr>
                <td style="background: lightyellow;">{{ $inventory->affiliation->name_of_barangay }}</td>
                <td style="background: lightyellow;">{{ $inventory->affiliation->name_of_association }}</td>
                <td style="background: lightyellow;">{{ $inventory->plant->name_of_plants }}</td>
                <td style="background: lightyellow;">{{ $inventory->farmer->first_name }} {{ $inventory->farmer->last_name }}</td>
                <td>{{ number_format($inventory->planting_density, 2) }}</td>
                <td>{{ number_format($inventory->production_volume, 2) }}</td>
                <td>{{ number_format($inventory->newly_planted, 0) }}</td>
                <td>{{ number_format($inventory->vegetative, 0) }}</td>
                <td>{{ number_format($inventory->reproductive, 0) }}</td>
                <td>{{ number_format($inventory->maturity_harvested, 0) }}</td>
                <td style="background: lightgreen;">{{ number_format($inventory->total, 0) }}</td>
                <td>{{ number_format($inventory->newly_planted_divided, 4) }}</td>
                <td>{{ number_format($inventory->vegetative_divided, 4) }}</td>
                <td>{{ number_format($inventory->reproductive_divided, 4) }}</td>
                <td>{{ number_format($inventory->maturity_harvested_divided, 4) }}</td>
                <td style="background: lightgreen;">{{ number_format($inventory->total_planted_area, 4) }}</td>
                <td style="background: #ffb768;">{{ number_format($inventory->area_harvested, 4) }}</td>
                <td style="background: #ffb768;">{{ number_format($inventory->final_production_volume, 4) }}</td>

                <!-- Adding new columns for latitude, longitude, control number, and birthdate -->
                 <td>{{ number_format($inventory->latitude, 4) }}</td>
                <td>{{ number_format($inventory->longitude, 4) }}</td>                
                <td>{{ $inventory->farmer->control_number }}</td>
                <td>{{ \Carbon\Carbon::parse($inventory->farmer->birthdate)->format('Y-m-d') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
