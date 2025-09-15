# Excel Upload Feature for Inventory Management

## Overview
This feature allows you to upload Excel files (.xlsx) containing inventory data that will be parsed and stored separately from manually entered data.

## How to Use

### 1. Upload Excel File
1. Go to the Inventory Management page
2. Scroll down to the "UPLOAD EXCEL FILE" section
3. Click "Choose File" and select your Excel file
4. Click "Upload Excel" button
5. The system will process the file and display success/error messages

### 2. Excel File Format
Your Excel file should have the following columns in order:
- **Column A**: Barangay
- **Column B**: Commodity  
- **Column C**: Farmer
- **Column D**: Planting Density (ha)
- **Column E**: Production Vol/ Hectare (kls)
- **Column F**: Newly Planted
- **Column G**: Vegetative
- **Column H**: Reproductive
- **Column I**: Maturity/
- **Column J**: TOTAL
- **Column K**: Newly Planted (per hectare)
- **Column L**: Vegetative (per hectare)
- **Column M**: Reproductive (per hectare)
- **Column N**: Maturity/Harvested (per hectare)
- **Column O**: Total (per hectare)
- **Column P**: Area (hectares)
- **Column Q**: Production Volume (MT)

### 3. View Uploaded Data
- Uploaded data appears in a separate table below the manual entries
- The table is labeled "UPLOADED INVENTORY DATA" with a blue header
- You can search through uploaded data using the search bar
- The system automatically calculates all derived values (per hectare values, totals, etc.)

### 4. Clear Uploaded Data
- If you need to clear all uploaded data, click the "Clear Uploaded Data" button
- This action cannot be undone, so confirm when prompted

## Features
- **Automatic Calculations**: The system automatically calculates:
  - Per hectare values (divided by planting density)
  - Total planted area
  - Area harvested
  - Production volume in MT
- **Data Validation**: Only accepts .xlsx and .xls files up to 10MB
- **Error Handling**: Shows detailed error messages for any issues
- **Search Functionality**: Search uploaded data by barangay, commodity, or farmer
- **Separate Storage**: Uploaded data is stored separately from manual entries

## Technical Details
- Uploaded data is stored in the `uploaded_inventories` table
- The system uses Laravel Excel package for parsing
- All calculations follow the same logic as manual entries
- Data is displayed with proper formatting and color coding

## Sample Excel Format
```
| A: Barangay | B: Commodity | C: Farmer | D: Planting Density | E: Prodn. Vol/ Hectare | F: Newly Planted | G: Vegetative | H: Reproductive | I: Maturity/ | J: TOTAL | K: Newly Planted/ha | L: Vegetative/ha | M: Reproductive/ha | N: Maturity/ha | O: Total/ha | P: Area | Q: Production Volume |
|-------------|--------------|-----------|---------------------|------------------------|------------------|---------------|-----------------|--------------|-----------|---------------------|------------------|-------------------|----------------|-------------|---------|-------------------|
| BUNGA       | Polesitao    | BUNGA BONTOC UPLAND DEVE | 18000 | 9000 | 100 | 0 | 0 | 200 | 200 | 0.0056 | 0.0000 | 0.0000 | 0.0111 | 0.0167 | 0.0111 | 0.1000 |
| TAA         | Polesitao    | ESTOPIA | 11000 | 3000 | 100 | 100 | 0 | 0 | 100 | 0.0091 | 0.0091 | 0.0000 | 0.0000 | 0.0182 | 0.0000 | 0.0000 |
```

## Notes
- The first row should contain headers (will be skipped during import)
- Empty rows are automatically skipped
- Numeric values are automatically converted and validated
- The system handles missing or invalid data gracefully
