<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Affiliation;

class AffiliationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $affiliations = [
            'Benit',
            'Buenavista',
            'Cabugason',
            'Calogcog',
            'Cancamares',
            'Dao',
            'Divisoria',
            'Esperanza',
            'Hilaan',
            'Himakilo',
            'Malbago',
            'Mandamo',
            'Manháa',
            'Masaymon',
            'Pamahawan',
            'Pasil',
            'Poblacion',
            'San Juan',
            'San Ramon',
            'Santo Niño',
            'Santo Rosario',
            'Talisay',
            'Tampoong',
            'Tuburan',
            'Union',
            'Malitbogay',
            'Tagbayaon',
            'Mahayahay',
            'San Vicente',
            'Cagnonocot',
            'Sampao',
            'Bagong Silang',
            'Sillonay',
            'Matin-ao',
            'San Pedro',
            'Pis-ong',
            'Si-it',
            'Pio Poblador',
            'Lawigan',
            'Matlang',
        ];

        foreach ($affiliations as $affiliation) {
            Affiliation::create([
                'name_of_barangay' => $affiliation,
            ]);
        }
    }
}
