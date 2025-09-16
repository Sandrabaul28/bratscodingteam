# 📊 Visual Excel Format - Column A to Q

## 🎯 **Excel Layout Visualization**

```
┌─────────────┬─────────────┬─────────────────────┬──────────────────┬─────────────────────┬─────────────┬─────────────┬─────────────┬─────────────┬─────────┬─────────────┬─────────────┬─────────────┬─────────────┬─────────┬─────────┬─────────────────────┐
│     A       │     B       │         C           │        D         │         E           │     F       │     G       │     H       │     I       │    J    │     K       │     L       │     M       │     N       │    O    │    P    │         Q           │
├─────────────┼─────────────┼─────────────────────┼──────────────────┼─────────────────────┼─────────────┼─────────────┼─────────────┼─────────────┼─────────┼─────────────┼─────────────┼─────────────┼─────────────┼─────────┼─────────┼─────────────────────┤
│  Barangay   │ Commodity   │       Farmer        │ Planting Density │ Production Vol/     │ Newly       │ Vegetative  │Reproductive │ Maturity/   │  TOTAL  │ Newly       │ Vegetative  │Reproductive │ Maturity/   │ Total/  │  Area   │ Production Volume   │
│             │             │                     │     (ha)         │ Hectare (kls)       │ Planted     │             │             │             │         │ Planted/ha  │    /ha      │    /ha      │    ha       │   ha    │         │      (MT)           │
├─────────────┼─────────────┼─────────────────────┼──────────────────┼─────────────────────┼─────────────┼─────────────┼─────────────┼─────────────┼─────────┼─────────────┼─────────────┼─────────────┼─────────────┼─────────┼─────────┼─────────────────────┤
│   BUNGA     │ Polesitao   │BUNGA BONTOC UPLAND  │     18000        │       9000          │     100     │      0      │      0      │     200     │   200   │   0.0056    │   0.0000    │   0.0000    │   0.0111    │ 0.0167  │ 0.0111  │      0.1000         │
│             │             │       DEVE          │                 │                     │             │             │             │             │         │             │             │             │             │         │         │                     │
├─────────────┼─────────────┼─────────────────────┼──────────────────┼─────────────────────┼─────────────┼─────────────┼─────────────┼─────────────┼─────────┼─────────────┼─────────────┼─────────────┼─────────────┼─────────┼─────────┼─────────────────────┤
│    TAA      │ Polesitao   │      ESTOPIA        │     11000        │       3000          │     100     │     100     │      0      │      0      │   100   │   0.0091    │   0.0091    │   0.0000    │   0.0000    │ 0.0182  │ 0.0000  │      0.0000         │
├─────────────┼─────────────┼─────────────────────┼──────────────────┼─────────────────────┼─────────────┼─────────────┼─────────────┼─────────────┼─────────┼─────────────┼─────────────┼─────────────┼─────────────┼─────────┼─────────┼─────────────────────┤
│    PAKU     │   Squash    │PAKU FARMERS         │     15000        │       5000          │      50     │      75     │     25      │      0      │   150   │   0.0033    │   0.0050    │   0.0017    │   0.0000    │ 0.0100  │ 0.0000  │      0.0000         │
│             │             │   ASSOCIATION       │                 │                     │             │             │             │             │         │             │             │             │             │         │         │                     │
└─────────────┴─────────────┴─────────────────────┴──────────────────┴─────────────────────┴─────────────┴─────────────┴─────────────┴─────────────┴─────────┴─────────────┴─────────────┴─────────────┴─────────────┴─────────┴─────────┴─────────────────────┘
```

## 📋 **Column Details**

| Col | Field | Type | Description | Example |
|-----|-------|------|-------------|---------|
| A | Barangay | Text | Location name | BUNGA, TAA, PAKU |
| B | Commodity | Text | Crop type | Rice, Corn, Tomato |
| C | Farmer | Text | Farmer/Organization | BUNGA BONTOC UPLAND DEVE |
| D | Planting Density | Number | Plants per hectare | 18000 |
| E | Production Vol/Ha | Number | Kilos per hectare | 9000 |
| F | Newly Planted | Number | New plants count | 100 |
| G | Vegetative | Number | Vegetative stage count | 0 |
| H | Reproductive | Number | Reproductive stage count | 0 |
| I | Maturity | Number | Maturity stage count | 200 |
| J | TOTAL | Number | Sum of F+G+H+I | 200 |
| K | Newly/ha | Number | F÷D | 0.0056 |
| L | Vegetative/ha | Number | G÷D | 0.0000 |
| M | Reproductive/ha | Number | H÷D | 0.0000 |
| N | Maturity/ha | Number | I÷D | 0.0111 |
| O | Total/ha | Number | J÷D | 0.0167 |
| P | Area | Number | I÷D (same as N) | 0.0111 |
| Q | Production Vol (MT) | Number | P×E÷1000 | 0.1000 |

## ⚠️ **Critical Requirements**

1. **17 Columns Exactly**: A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q
2. **First Row = Headers**: Must match exactly as shown
3. **Numeric Columns**: D-Q must be numbers only
4. **File Format**: .xlsx only (not .xls or .csv)
5. **File Size**: Under 10MB

## 📁 **Download Sample**

Use `Inventory_Sample_Format.csv` as your template:
1. Download the CSV file
2. Open in Excel
3. Save as .xlsx
4. Replace sample data with your data
5. Upload to system


