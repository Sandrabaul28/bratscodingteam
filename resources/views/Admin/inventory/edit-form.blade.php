<form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="inventory_id" value="{{ $inventory->id }}">
    
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="farmer_id_{{ $inventory->id }}">Farmer</label>
                <select class="form-control" name="farmer_id" id="farmer_id_{{ $inventory->id }}" required>
                    @foreach($farmers as $farmer)
                        <option value="{{ $farmer->id }}" {{ $inventory->farmer_id == $farmer->id ? 'selected' : '' }}>
                            {{ $farmer->first_name }} {{ $farmer->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="plant_id_{{ $inventory->id }}">Plant</label>
                <select class="form-control" name="plant_id" id="plant_id_{{ $inventory->id }}" required>
                    @foreach($plants as $plant)
                        <option value="{{ $plant->id }}" {{ $inventory->plant_id == $plant->id ? 'selected' : '' }}>
                            {{ $plant->name_of_plants }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="affiliation_id_{{ $inventory->id }}">Affiliation</label>
                <select class="form-control" name="affiliation_id" id="affiliation_id_{{ $inventory->id }}" required>
                    @foreach($affiliations as $affiliation)
                        <option value="{{ $affiliation->id }}" {{ $inventory->affiliation_id == $affiliation->id ? 'selected' : '' }}>
                            {{ $affiliation->name_of_association }} - {{ $affiliation->name_of_barangay }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="planting_density_{{ $inventory->id }}">Planting Density</label>
                <input type="number" class="form-control" name="planting_density" id="planting_density_{{ $inventory->id }}" value="{{ round($inventory->planting_density, 4) }}" step="0.0001" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="production_volume_{{ $inventory->id }}">Production Volume</label>
                <input type="number" class="form-control" name="production_volume" id="production_volume_{{ $inventory->id }}" value="{{ round($inventory->production_volume, 4) }}" step="0.0001" required>
            </div>

            <!-- Remaining Fields -->
            <div class="col-md-6 mb-3">
                <label for="newly_planted_{{ $inventory->id }}">Newly Planted</label>
                <input type="number" class="form-control" name="newly_planted" id="newly_planted_{{ $inventory->id }}" value="{{ $inventory->newly_planted }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="vegetative_{{ $inventory->id }}">Vegetative</label>
                <input type="number" class="form-control" name="vegetative" id="vegetative_{{ $inventory->id }}" value="{{ $inventory->vegetative }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="reproductive_{{ $inventory->id }}">Reproductive</label>
                <input type="number" class="form-control" name="reproductive" id="reproductive_{{ $inventory->id }}" value="{{ $inventory->reproductive }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="maturity_harvested_{{ $inventory->id }}">Maturity Harvested</label>
                <input type="number" class="form-control" name="maturity_harvested" id="maturity_harvested_{{ $inventory->id }}" value="{{ $inventory->maturity_harvested }}">
            </div>

            <!-- New Readonly Fields -->
            <div class="col-md-6 mb-3">
                <label for="newly_planted_divided_{{ $inventory->id }}">Newly Planted /ha</label>
                <input type="text" class="form-control" name="newly_planted_divided" id="newly_planted_divided_{{ $inventory->id }}" value="{{ number_format($inventory->newly_planted_divided, 4) }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="vegetative_divided_{{ $inventory->id }}">Vegetative /ha</label>
                <input type="text" class="form-control" name="vegetative_divided" id="vegetative_divided_{{ $inventory->id }}" value="{{ number_format($inventory->vegetative_divided, 4) }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="reproductive_divided_{{ $inventory->id }}">Reproductive /ha</label>
                <input type="text" class="form-control" name="reproductive_divided" id="reproductive_divided_{{ $inventory->id }}" value="{{ number_format($inventory->reproductive_divided, 4) }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="maturity_harvested_divided_{{ $inventory->id }}">Maturity Harvested /ha</label>
                <input type="text" class="form-control" name="maturity_harvested_divided" id="maturity_harvested_divided_{{ $inventory->id }}" value="{{ number_format($inventory->maturity_harvested_divided, 4) }}" readonly>
            </div>

            <!-- Calculated Fields -->
            <div class="col-md-6 mb-3">
                <label for="area_harvested_{{ $inventory->id }}">Area Harvested (HAS)</label>
                <input type="text" class="form-control" name="area_harvested" id="area_harvested_{{ $inventory->id }}" value="{{ number_format($inventory->area_harvested, 4) }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="final_production_volume_{{ $inventory->id }}">Production Volume (MT)</label>
                <input type="text" class="form-control" name="final_production_volume" id="final_production_volume_{{ $inventory->id }}" value="{{ number_format($inventory->final_production_volume, 4) }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="total_{{ $inventory->id }}">Total</label>
                <input type="text" class="form-control" name="total" id="total_{{ $inventory->id }}" value="{{ number_format($inventory->total) }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label for="total_planted_area_{{ $inventory->id }}">Total Planted Area</label>
                <input type="text" class="form-control" name="total_planted_area" id="total_planted_area_{{ $inventory->id }}" value="{{ number_format($inventory->total_planted_area, 4) }}" readonly>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>
