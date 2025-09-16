# 📊 Excel Upload Format Guide - Column A to Q

## 🎯 **Required Format for Excel Upload (.xlsx)**

Your Excel file must have exactly **17 columns (A to Q)** in the following order:

| Column | Field Name | Data Type | Description | Example |
|--------|------------|-----------|-------------|---------|
| **A** | Barangay | Text | Name of the barangay | BUNGA, TAA, PAKU |
| **B** | Commodity | Text | Type of crop/commodity | Rice, Corn, Tomato, Squash |
| **C** | Farmer | Text | Farmer's name or organization | BUNGA BONTOC UPLAND DEVE |
| **D** | Planting Density (ha) | Number | Planting density per hectare | 18000, 15000, 20000 |
| **E** | Production Vol/ Hectare (kls) | Number | Production volume per hectare in kilos | 9000, 5000, 8000 |
| **F** | Newly Planted | Number | Number of newly planted plants | 100, 50, 0 |
| **G** | Vegetative | Number | Number of plants in vegetative stage | 0, 75, 100 |
| **H** | Reproductive | Number | Number of plants in reproductive stage | 0, 25, 100 |
| **I** | Maturity/ | Number | Number of plants in maturity stage | 200, 0, 50 |
| **J** | TOTAL | Number | Total number of plants (F+G+H+I) | 200, 150, 250 |
| **K** | Newly Planted/ha | Number | Newly planted per hectare (F/D) | 0.0056, 0.0033, 0.0000 |
| **L** | Vegetative/ha | Number | Vegetative per hectare (G/D) | 0.0000, 0.0050, 0.0050 |
| **M** | Reproductive/ha | Number | Reproductive per hectare (H/D) | 0.0000, 0.0017, 0.0050 |
| **N** | Maturity/ha | Number | Maturity per hectare (I/D) | 0.0111, 0.0000, 0.0025 |
| **O** | Total/ha | Number | Total per hectare (J/D) | 0.0167, 0.0100, 0.0125 |
| **P** | Area | Number | Area in hectares | 0.0111, 0.0000, 0.0025 |
| **Q** | Production Volume (MT) | Number | Production volume in metric tons | 0.1000, 0.0000, 0.0200 |

## 📋 **Sample Data Format**

```
| Barangay | Commodity | Farmer | Planting Density | Prodn. Vol/Ha | Newly Planted | Vegetative | Reproductive | Maturity | TOTAL | Newly/ha | Vegetative/ha | Repro/ha | Maturity/ha | Total/ha | Area | Prod. Vol (MT) |
|----------|-----------|--------|------------------|---------------|---------------|------------|--------------|----------|-------|----------|---------------|----------|-------------|----------|------|----------------|
| BUNGA    | Polesitao | BUNGA BONTOC UPLAND DEVE | 18000 | 9000 | 100 | 0 | 0 | 200 | 200 | 0.0056 | 0.0000 | 0.0000 | 0.0111 | 0.0167 | 0.0111 | 0.1000 |
| TAA      | Polesitao | ESTOPIA | 11000 | 3000 | 100 | 100 | 0 | 0 | 100 | 0.0091 | 0.0091 | 0.0000 | 0.0000 | 0.0182 | 0.0000 | 0.0000 |
| PAKU     | Squash    | PAKU FARMERS ASSOCIATION | 15000 | 5000 | 50 | 75 | 25 | 0 | 150 | 0.0033 | 0.0050 | 0.0017 | 0.0000 | 0.0100 | 0.0000 | 0.0000 |
```

## ⚠️ **Important Requirements**

### ✅ **What You MUST Do:**
1. **First Row = Headers**: The first row must contain the column headers exactly as shown above
2. **17 Columns Only**: Your Excel file must have exactly columns A through Q
3. **Correct Order**: Columns must be in the exact order shown (A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q)
4. **Numeric Values**: Columns D-Q must contain numeric values (numbers only)
5. **File Format**: Save as .xlsx format (Excel 2007 or later)
6. **File Size**: Maximum 10MB file size

### ❌ **What You MUST NOT Do:**
1. **Don't Skip Columns**: All 17 columns must be present
2. **Don't Change Order**: Don't rearrange the column order
3. **Don't Add Extra Columns**: Don't add columns beyond Q
4. **Don't Delete Headers**: Don't delete the first row with headers
5. **Don't Use Text in Number Columns**: Columns D-Q must be numbers only
6. **Don't Use Special Characters**: Avoid special characters in text fields

## 🔢 **Automatic Calculations**

The system will automatically calculate these values if they're missing or incorrect:

- **Column J (TOTAL)**: F + G + H + I
- **Column K (Newly Planted/ha)**: F ÷ D
- **Column L (Vegetative/ha)**: G ÷ D  
- **Column M (Reproductive/ha)**: H ÷ D
- **Column N (Maturity/ha)**: I ÷ D
- **Column O (Total/ha)**: J ÷ D
- **Column P (Area)**: I ÷ D (same as Maturity/ha)
- **Column Q (Production Volume)**: P × E ÷ 1000

## 📁 **How to Use the Sample File**

1. **Download the Sample**: Use the provided `Inventory_Sample_Format.csv` file
2. **Open in Excel**: Open the CSV file in Microsoft Excel
3. **Save as .xlsx**: Save the file as Excel format (.xlsx)
4. **Replace Sample Data**: Replace the sample data with your actual data
5. **Keep Headers**: Make sure the first row headers remain unchanged
6. **Upload**: Upload the completed file to the system

## 🎨 **Excel Formatting Tips**

### **Recommended Formatting:**
- **Headers**: Bold text, colored background (green recommended)
- **Numbers**: Use number format with appropriate decimal places
- **Text**: Left-aligned
- **Numbers**: Right-aligned or center-aligned
- **Borders**: Add borders to all cells for better readability

### **Column Widths:**
- **Column A (Barangay)**: 15 characters
- **Column B (Commodity)**: 15 characters  
- **Column C (Farmer)**: 25 characters
- **Columns D-Q**: Auto-fit to content

## 🚨 **Common Errors to Avoid**

1. **Wrong Column Order**: Make sure columns are A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q
2. **Missing Headers**: First row must contain the exact headers shown above
3. **Text in Number Fields**: Columns D-Q must contain only numbers
4. **Empty Required Fields**: Don't leave essential fields like Barangay, Commodity, or Farmer empty
5. **Wrong File Format**: Must be .xlsx, not .xls or .csv
6. **File Too Large**: Keep file size under 10MB

## 📞 **Need Help?**

If you encounter any issues:
1. Check this guide carefully
2. Compare your file with the sample format
3. Ensure all requirements are met
4. Try with a smaller dataset first
5. Contact support if problems persist

## 📊 **Sample File Download**

A sample file (`Inventory_Sample_Format.csv`) is provided with:
- ✅ Correct column headers
- ✅ Sample data for all 17 columns
- ✅ Proper formatting
- ✅ Ready to convert to .xlsx

**Steps to use:**
1. Download `Inventory_Sample_Format.csv`
2. Open in Excel
3. Save as .xlsx format
4. Replace sample data with your data
5. Upload to the system

---

**Remember**: The system is very strict about the format. Any deviation from this format may result in upload errors. When in doubt, use the sample file as your template!


