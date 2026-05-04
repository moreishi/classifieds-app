<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegionCitySeeder extends Seeder
{
    public function run(): void
    {
        // --- REGIONS ---
        DB::table('regions')->updateOrInsert(
            ['id' => 1],
            ['id' => 1, 'name' => 'Central Visayas']
        );

        // --- PROVINCES (parent_id = null, type = 'province') ---
        $provinces = [
            ['id' => 1,  'name' => 'Cebu'],
            ['id' => 2,  'name' => 'Bohol'],
            ['id' => 3,  'name' => 'Siquijor'],
            ['id' => 4,  'name' => 'Negros Oriental'],
        ];

        foreach ($provinces as $p) {
            DB::table('cities')->insert([
                'id'        => $p['id'],
                'name'      => $p['name'],
                'slug'      => Str::slug($p['name']),
                'type'      => 'province',
                'region_id' => 1,
                'parent_id' => null,
                'is_active' => true,
            ]);
        }

        // --- CITIES & MUNICIPALITIES ---
        // parent_id references the province id above, type = 'city' or 'municipality'

        $locations = [
            // Cebu (province id: 1)
            ['name' => 'Cebu City',          'slug' => 'cebu-city',            'parent_id' => 1, 'type' => 'city'],
            ['name' => 'Lapu-Lapu City',     'slug' => 'lapu-lapu-city',      'parent_id' => 1, 'type' => 'city'],
            ['name' => 'Mandaue City',       'slug' => 'mandaue-city',        'parent_id' => 1, 'type' => 'city'],
            ['name' => 'Bogo City',          'slug' => 'bogo-city',           'parent_id' => 1, 'type' => 'city'],
            ['name' => 'Carcar City',        'slug' => 'carcar-city',         'parent_id' => 1, 'type' => 'city'],
            ['name' => 'Danao City',         'slug' => 'danao-city',          'parent_id' => 1, 'type' => 'city'],
            ['name' => 'Naga City',          'slug' => 'naga-city-cebu',      'parent_id' => 1, 'type' => 'city'],
            ['name' => 'Talisay City',       'slug' => 'talisay-city-cebu',   'parent_id' => 1, 'type' => 'city'],
            ['name' => 'Toledo City',        'slug' => 'toledo-city',         'parent_id' => 1, 'type' => 'city'],
            ['name' => 'Alcantara',          'slug' => 'alcantara',           'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Alcoy',              'slug' => 'alcoy',               'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Alegria',            'slug' => 'alegria',             'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Aloguinsan',         'slug' => 'aloguinsan',          'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Argao',              'slug' => 'argao',               'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Asturias',           'slug' => 'asturias',            'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Badian',             'slug' => 'badian',              'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Balamban',           'slug' => 'balamban',            'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Bantayan',           'slug' => 'bantayan',            'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Barili',             'slug' => 'barili',              'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Boljoon',            'slug' => 'boljoon',             'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Borbon',             'slug' => 'borbon',              'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Carmen',             'slug' => 'carmen-cebu',         'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Catmon',             'slug' => 'catmon',              'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Compostela',         'slug' => 'compostela-cebu',     'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Consolacion',        'slug' => 'consolacion',         'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Cordova',            'slug' => 'cordova',             'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Daanbantayan',       'slug' => 'daanbantayan',        'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Dalaguete',          'slug' => 'dalaguete',           'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Dumanjug',           'slug' => 'dumanjug',            'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Ginatilan',          'slug' => 'ginatilan',           'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Liloan',             'slug' => 'liloan',              'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Madridejos',         'slug' => 'madridejos',          'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Malabuyoc',          'slug' => 'malabuyoc',           'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Medellin',           'slug' => 'medellin',            'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Minglanilla',        'slug' => 'minglanilla',         'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Moalboal',           'slug' => 'moalboal',            'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Oslob',              'slug' => 'oslob',               'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Pilar',              'slug' => 'pilar-cebu',          'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Pinamungajan',       'slug' => 'pinamungajan',        'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Poro',               'slug' => 'poro',                'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Ronda',              'slug' => 'ronda',               'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Samboan',            'slug' => 'samboan',             'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'San Fernando',       'slug' => 'san-fernando-cebu',   'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'San Francisco',      'slug' => 'san-francisco-cebu',  'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'San Remigio',        'slug' => 'san-remigio',         'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Santa Fe',           'slug' => 'santa-fe-cebu',       'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Santander',          'slug' => 'santander',           'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Sibonga',            'slug' => 'sibonga',             'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Sogod',              'slug' => 'sogod',               'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Tabogon',            'slug' => 'tabogon',             'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Tabuelan',           'slug' => 'tabuelan',            'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Tuburan',            'slug' => 'tuburan',             'parent_id' => 1, 'type' => 'municipality'],
            ['name' => 'Tudela',             'slug' => 'tudela',              'parent_id' => 1, 'type' => 'municipality'],

            // Bohol (province id: 2)
            ['name' => 'Tagbilaran City',   'slug' => 'tagbilaran-city',     'parent_id' => 2, 'type' => 'city'],
            ['name' => 'Alburquerque',      'slug' => 'alburquerque',        'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Alicia',            'slug' => 'alicia-bohol',        'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Anda',              'slug' => 'anda',                'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Antequera',         'slug' => 'antequera',           'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Baclayon',          'slug' => 'baclayon',            'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Balilihan',         'slug' => 'balilihan',           'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Batuan',            'slug' => 'batuan-bohol',        'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Bien Unido',        'slug' => 'bien-unido',          'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Bilar',             'slug' => 'bilar',               'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Buenavista',        'slug' => 'buenavista-bohol',    'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Calape',            'slug' => 'calape',              'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Candijay',          'slug' => 'candijay',            'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Carmen',            'slug' => 'carmen-bohol',        'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Catigbian',         'slug' => 'catigbian',           'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Clarin',            'slug' => 'clarin-bohol',        'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Corella',           'slug' => 'corella',             'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Cortes',            'slug' => 'cortes-bohol',        'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Dagohoy',           'slug' => 'dagohoy',             'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Danao',             'slug' => 'danao-bohol',         'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Dauis',             'slug' => 'dauis',               'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Dimiao',            'slug' => 'dimiao',              'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Duero',             'slug' => 'duero',               'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Garcia Hernandez',  'slug' => 'garcia-hernandez',    'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Getafe',            'slug' => 'getafe',              'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Guindulman',        'slug' => 'guindulman',          'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Inabanga',          'slug' => 'inabanga',            'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Jagna',             'slug' => 'jagna',               'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Lila',              'slug' => 'lila',                'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Loay',              'slug' => 'loay',                'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Loboc',             'slug' => 'loboc',               'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Loon',              'slug' => 'loon',                'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Mabini',            'slug' => 'mabini-bohol',        'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Maribojoc',         'slug' => 'maribojoc',           'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Panglao',           'slug' => 'panglao',             'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Pilar',             'slug' => 'pilar-bohol',         'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Pres. C.P. Garcia', 'slug' => 'pres-cp-garcia',      'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Sagbayan',          'slug' => 'sagbayan',            'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'San Isidro',        'slug' => 'san-isidro-bohol',    'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'San Miguel',        'slug' => 'san-miguel-bohol',    'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Sevilla',           'slug' => 'sevilla-bohol',       'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Sierra Bullones',   'slug' => 'sierra-bullones',     'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Sikatuna',          'slug' => 'sikatuna',            'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Talibon',           'slug' => 'talibon',             'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Trinidad',          'slug' => 'trinidad',            'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Tubigon',           'slug' => 'tubigon',             'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Ubay',              'slug' => 'ubay',                'parent_id' => 2, 'type' => 'municipality'],
            ['name' => 'Valencia',          'slug' => 'valencia-bohol',      'parent_id' => 2, 'type' => 'municipality'],

            // Siquijor (province id: 3)
            ['name' => 'Enrique Villanueva', 'slug' => 'enrique-villanueva', 'parent_id' => 3, 'type' => 'municipality'],
            ['name' => 'Larena',            'slug' => 'larena',              'parent_id' => 3, 'type' => 'municipality'],
            ['name' => 'Lazi',              'slug' => 'lazi',                'parent_id' => 3, 'type' => 'municipality'],
            ['name' => 'Maria',             'slug' => 'maria-siquijor',      'parent_id' => 3, 'type' => 'municipality'],
            ['name' => 'San Juan',          'slug' => 'san-juan-siquijor',   'parent_id' => 3, 'type' => 'municipality'],
            ['name' => 'Siquijor',          'slug' => 'siquijor',            'parent_id' => 3, 'type' => 'municipality'],

            // Negros Oriental (province id: 4)
            ['name' => 'Dumaguete City',    'slug' => 'dumaguete-city',      'parent_id' => 4, 'type' => 'city'],
            ['name' => 'Bais City',         'slug' => 'bais-city',           'parent_id' => 4, 'type' => 'city'],
            ['name' => 'Bayawan City',      'slug' => 'bayawan-city',        'parent_id' => 4, 'type' => 'city'],
            ['name' => 'Canlaon City',      'slug' => 'canlaon-city',        'parent_id' => 4, 'type' => 'city'],
            ['name' => 'Guihulngan City',   'slug' => 'guihulngan-city',     'parent_id' => 4, 'type' => 'city'],
            ['name' => 'Tanjay City',       'slug' => 'tanjay-city',         'parent_id' => 4, 'type' => 'city'],
            ['name' => 'Amlan',             'slug' => 'amlan',               'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Ayungon',           'slug' => 'ayungon',             'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Bacong',            'slug' => 'bacong',              'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Basay',             'slug' => 'basay',               'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Bindoy',            'slug' => 'bindoy',              'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Dauin',             'slug' => 'dauin',               'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Jimalalud',         'slug' => 'jimalalud',           'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'La Libertad',       'slug' => 'la-libertad-ne',      'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Mabinay',           'slug' => 'mabinay',             'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Manjuyod',          'slug' => 'manjuyod',            'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Pamplona',          'slug' => 'pamplona-ne',         'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'San Jose',          'slug' => 'san-jose-ne',         'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Santa Catalina',    'slug' => 'santa-catalina',      'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Siaton',            'slug' => 'siaton',              'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Sibulan',           'slug' => 'sibulan',             'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Tayasan',           'slug' => 'tayasan',             'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Valencia',          'slug' => 'valencia-ne',         'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Vallehermoso',      'slug' => 'vallehermoso',        'parent_id' => 4, 'type' => 'municipality'],
            ['name' => 'Zamboanguita',      'slug' => 'zamboanguita',        'parent_id' => 4, 'type' => 'municipality'],
        ];

        $id = 5; // start after provinces
        foreach ($locations as $loc) {
            DB::table('cities')->updateOrInsert(
                ['slug' => $loc['slug']],
                [
                    'id'        => $id,
                    'name'      => $loc['name'],
                    'slug'      => $loc['slug'],
                    'type'      => $loc['type'],
                    'region_id' => 1,
                    'parent_id' => $loc['parent_id'],
                    'is_active' => true,
                ]
            );
            $id++;
        }
    }
}
