<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegionCitySeeder extends Seeder
{
    public function run(): void
    {
        // ========================================================================
        // REGIONS
        // ========================================================================
        $regions = [
            ['id' => 1, 'name' => 'Central Visayas'],
            ['id' => 2, 'name' => 'Eastern Visayas'],
            ['id' => 3, 'name' => 'Western Visayas'],
            ['id' => 4, 'name' => 'Zamboanga Peninsula'],
            ['id' => 5, 'name' => 'Northern Mindanao'],
            ['id' => 6, 'name' => 'Davao Region'],
            ['id' => 7, 'name' => 'SOCCSKSARGEN'],
            ['id' => 8, 'name' => 'Caraga'],
            ['id' => 9, 'name' => 'BARMM'],
        ];

        foreach ($regions as $r) {
            DB::table('regions')->updateOrInsert(['id' => $r['id']], $r);
        }

        // ========================================================================
        // PROVINCES (type = 'province', parent_id = null)
        // ========================================================================
        $provinces = [
            // Central Visayas (region_id: 1)
            ['id' => 1,  'name' => 'Cebu',              'region_id' => 1],
            ['id' => 2,  'name' => 'Bohol',             'region_id' => 1],
            ['id' => 3,  'name' => 'Siquijor',          'region_id' => 1],
            ['id' => 4,  'name' => 'Negros Oriental',   'region_id' => 1],

            // Eastern Visayas (region_id: 2)
            ['id' => 5,  'name' => 'Leyte',             'region_id' => 2],
            ['id' => 6,  'name' => 'Southern Leyte',    'region_id' => 2],
            ['id' => 7,  'name' => 'Samar',             'region_id' => 2],
            ['id' => 8,  'name' => 'Eastern Samar',     'region_id' => 2],
            ['id' => 9,  'name' => 'Northern Samar',    'region_id' => 2],
            ['id' => 10, 'name' => 'Biliran',           'region_id' => 2],

            // Western Visayas (region_id: 3)
            ['id' => 11, 'name' => 'Iloilo',            'region_id' => 3],
            ['id' => 12, 'name' => 'Negros Occidental', 'region_id' => 3],
            ['id' => 13, 'name' => 'Capiz',             'region_id' => 3],
            ['id' => 14, 'name' => 'Aklan',             'region_id' => 3],
            ['id' => 15, 'name' => 'Antique',           'region_id' => 3],
            ['id' => 16, 'name' => 'Guimaras',          'region_id' => 3],

            // Zamboanga Peninsula (region_id: 4)
            ['id' => 17, 'name' => 'Zamboanga del Sur',       'region_id' => 4],
            ['id' => 18, 'name' => 'Zamboanga del Norte',     'region_id' => 4],
            ['id' => 19, 'name' => 'Zamboanga Sibugay',       'region_id' => 4],
            ['id' => 20, 'name' => 'Isabela City (Basilan)',  'region_id' => 4],

            // Northern Mindanao (region_id: 5)
            ['id' => 21, 'name' => 'Bukidnon',          'region_id' => 5],
            ['id' => 22, 'name' => 'Misamis Oriental',  'region_id' => 5],
            ['id' => 23, 'name' => 'Misamis Occidental','region_id' => 5],
            ['id' => 24, 'name' => 'Lanao del Norte',   'region_id' => 5],
            ['id' => 25, 'name' => 'Camiguin',          'region_id' => 5],

            // Davao Region (region_id: 6)
            ['id' => 26, 'name' => 'Davao del Sur',     'region_id' => 6],
            ['id' => 27, 'name' => 'Davao del Norte',   'region_id' => 6],
            ['id' => 28, 'name' => 'Davao Oriental',    'region_id' => 6],
            ['id' => 29, 'name' => 'Davao de Oro',      'region_id' => 6],
            ['id' => 30, 'name' => 'Davao Occidental',  'region_id' => 6],

            // SOCCSKSARGEN (region_id: 7)
            ['id' => 31, 'name' => 'South Cotabato',    'region_id' => 7],
            ['id' => 32, 'name' => 'North Cotabato',    'region_id' => 7],
            ['id' => 33, 'name' => 'Sarangani',         'region_id' => 7],
            ['id' => 34, 'name' => 'Sultan Kudarat',    'region_id' => 7],

            // Caraga (region_id: 8)
            ['id' => 35, 'name' => 'Agusan del Norte',  'region_id' => 8],
            ['id' => 36, 'name' => 'Agusan del Sur',    'region_id' => 8],
            ['id' => 37, 'name' => 'Surigao del Norte', 'region_id' => 8],
            ['id' => 38, 'name' => 'Surigao del Sur',   'region_id' => 8],
            ['id' => 39, 'name' => 'Dinagat Islands',   'region_id' => 8],

            // BARMM (region_id: 9)
            ['id' => 40, 'name' => 'Maguindanao',       'region_id' => 9],
            ['id' => 41, 'name' => 'Lanao del Sur',     'region_id' => 9],
            ['id' => 42, 'name' => 'Basilan',           'region_id' => 9],
            ['id' => 43, 'name' => 'Sulu',              'region_id' => 9],
            ['id' => 44, 'name' => 'Tawi-Tawi',         'region_id' => 9],
        ];

        foreach ($provinces as $p) {
            DB::table('cities')->updateOrInsert(
                ['slug' => Str::slug($p['name'])],
                [
                    'name'      => $p['name'],
                    'slug'      => Str::slug($p['name']),
                    'type'      => 'province',
                    'region_id' => $p['region_id'],
                    'parent_id' => null,
                    'is_active' => true,
                ]
            );
        }

        // ========================================================================
        // CITIES & MUNICIPALITIES
        // We lookup province ID by slug for the parent_id to remain portable.
        // ========================================================================
        $provinceMap = [];
        foreach ($provinces as $p) {
            $provinceMap[Str::slug($p['name'])] = $p['id'];
        }
        // Helper to resolve parent_id
        $pid = fn(string $slug) => $provinceMap[$slug] ?? null;

        // Each entry: [name, slug, parentSlug, type]
        // parentSlug is the province slug, resolved to real ID at runtime.
        // Duplicate names across provinces get unique slugs with province prefix/suffix.
        $locations = [
            // ===================================================================
            // CEBU (slug: cebu, id: 1) — ALL cities & municipalities
            // ===================================================================
            ['Cebu City',          'cebu-city',            'cebu', 'city'],
            ['Lapu-Lapu City',     'lapu-lapu-city',       'cebu', 'city'],
            ['Mandaue City',       'mandaue-city',         'cebu', 'city'],
            ['Bogo City',          'bogo-city',            'cebu', 'city'],
            ['Carcar City',        'carcar-city',          'cebu', 'city'],
            ['Danao City',         'danao-city',           'cebu', 'city'],
            ['Naga City',          'naga-city-cebu',       'cebu', 'city'],
            ['Talisay City',       'talisay-city-cebu',   'cebu', 'city'],
            ['Toledo City',        'toledo-city',          'cebu', 'city'],
            ['Alcantara',          'alcantara',            'cebu', 'municipality'],
            ['Alcoy',              'alcoy',                'cebu', 'municipality'],
            ['Alegria',            'alegria',              'cebu', 'municipality'],
            ['Aloguinsan',         'aloguinsan',           'cebu', 'municipality'],
            ['Argao',              'argao',                'cebu', 'municipality'],
            ['Asturias',           'asturias',             'cebu', 'municipality'],
            ['Badian',             'badian',               'cebu', 'municipality'],
            ['Balamban',           'balamban',             'cebu', 'municipality'],
            ['Bantayan',           'bantayan',             'cebu', 'municipality'],
            ['Barili',             'barili',               'cebu', 'municipality'],
            ['Boljoon',            'boljoon',              'cebu', 'municipality'],
            ['Borbon',             'borbon',               'cebu', 'municipality'],
            ['Carmen',             'carmen-cebu',          'cebu', 'municipality'],
            ['Catmon',             'catmon',               'cebu', 'municipality'],
            ['Compostela',         'compostela-cebu',     'cebu', 'municipality'],
            ['Consolacion',        'consolacion',          'cebu', 'municipality'],
            ['Cordova',            'cordova',              'cebu', 'municipality'],
            ['Daanbantayan',       'daanbantayan',         'cebu', 'municipality'],
            ['Dalaguete',          'dalaguete',            'cebu', 'municipality'],
            ['Dumanjug',           'dumanjug',             'cebu', 'municipality'],
            ['Ginatilan',          'ginatilan',            'cebu', 'municipality'],
            ['Liloan',             'liloan-cebu',          'cebu', 'municipality'],
            ['Madridejos',         'madridejos',           'cebu', 'municipality'],
            ['Malabuyoc',          'malabuyoc',            'cebu', 'municipality'],
            ['Medellin',           'medellin',             'cebu', 'municipality'],
            ['Minglanilla',        'minglanilla',          'cebu', 'municipality'],
            ['Moalboal',           'moalboal',             'cebu', 'municipality'],
            ['Oslob',              'oslob',                'cebu', 'municipality'],
            ['Pilar',              'pilar-cebu',           'cebu', 'municipality'],
            ['Pinamungajan',       'pinamungajan',         'cebu', 'municipality'],
            ['Poro',               'poro',                 'cebu', 'municipality'],
            ['Ronda',              'ronda',                'cebu', 'municipality'],
            ['Samboan',            'samboan',              'cebu', 'municipality'],
            ['San Fernando',       'san-fernando-cebu',   'cebu', 'municipality'],
            ['San Francisco',      'san-francisco-cebu',  'cebu', 'municipality'],
            ['San Remigio',        'san-remigio',          'cebu', 'municipality'],
            ['Santa Fe',           'santa-fe-cebu',        'cebu', 'municipality'],
            ['Santander',          'santander',            'cebu', 'municipality'],
            ['Sibonga',            'sibonga',              'cebu', 'municipality'],
            ['Sogod',              'sogod-cebu',           'cebu', 'municipality'],
            ['Tabogon',            'tabogon',              'cebu', 'municipality'],
            ['Tabuelan',           'tabuelan',             'cebu', 'municipality'],
            ['Tuburan',            'tuburan',              'cebu', 'municipality'],
            ['Tudela',             'tudela-cebu',          'cebu', 'municipality'],

            // ===================================================================
            // BOHOL (slug: bohol, id: 2) — ALL cities & municipalities
            // ===================================================================
            ['Tagbilaran City',    'tagbilaran-city',      'bohol', 'city'],
            ['Alburquerque',       'alburquerque',         'bohol', 'municipality'],
            ['Alicia',             'alicia-bohol',         'bohol', 'municipality'],
            ['Anda',               'anda',                 'bohol', 'municipality'],
            ['Antequera',          'antequera',            'bohol', 'municipality'],
            ['Baclayon',           'baclayon',             'bohol', 'municipality'],
            ['Balilihan',          'balilihan',            'bohol', 'municipality'],
            ['Batuan',             'batuan-bohol',         'bohol', 'municipality'],
            ['Bien Unido',         'bien-unido',           'bohol', 'municipality'],
            ['Bilar',              'bilar',                'bohol', 'municipality'],
            ['Buenavista',         'buenavista-bohol',     'bohol', 'municipality'],
            ['Calape',             'calape',               'bohol', 'municipality'],
            ['Candijay',           'candijay',             'bohol', 'municipality'],
            ['Carmen',             'carmen-bohol',         'bohol', 'municipality'],
            ['Catigbian',          'catigbian',            'bohol', 'municipality'],
            ['Clarin',             'clarin-bohol',         'bohol', 'municipality'],
            ['Corella',            'corella',              'bohol', 'municipality'],
            ['Cortes',             'cortes-bohol',         'bohol', 'municipality'],
            ['Dagohoy',            'dagohoy',              'bohol', 'municipality'],
            ['Danao',              'danao-bohol',          'bohol', 'municipality'],
            ['Dauis',              'dauis',                'bohol', 'municipality'],
            ['Dimiao',             'dimiao',               'bohol', 'municipality'],
            ['Duero',              'duero',                'bohol', 'municipality'],
            ['Garcia Hernandez',   'garcia-hernandez',     'bohol', 'municipality'],
            ['Getafe',             'getafe',               'bohol', 'municipality'],
            ['Guindulman',         'guindulman',           'bohol', 'municipality'],
            ['Inabanga',           'inabanga',             'bohol', 'municipality'],
            ['Jagna',              'jagna',                'bohol', 'municipality'],
            ['Lila',               'lila',                 'bohol', 'municipality'],
            ['Loay',               'loay',                 'bohol', 'municipality'],
            ['Loboc',              'loboc',                'bohol', 'municipality'],
            ['Loon',               'loon',                 'bohol', 'municipality'],
            ['Mabini',             'mabini-bohol',         'bohol', 'municipality'],
            ['Maribojoc',          'maribojoc',            'bohol', 'municipality'],
            ['Panglao',            'panglao',              'bohol', 'municipality'],
            ['Pilar',              'pilar-bohol',          'bohol', 'municipality'],
            ['Pres. C.P. Garcia',  'pres-cp-garcia',       'bohol', 'municipality'],
            ['Sagbayan',           'sagbayan',             'bohol', 'municipality'],
            ['San Isidro',         'san-isidro-bohol',     'bohol', 'municipality'],
            ['San Miguel',         'san-miguel-bohol',     'bohol', 'municipality'],
            ['Sevilla',            'sevilla-bohol',        'bohol', 'municipality'],
            ['Sierra Bullones',    'sierra-bullones',      'bohol', 'municipality'],
            ['Sikatuna',           'sikatuna',             'bohol', 'municipality'],
            ['Talibon',            'talibon',              'bohol', 'municipality'],
            ['Trinidad',           'trinidad',             'bohol', 'municipality'],
            ['Tubigon',            'tubigon',              'bohol', 'municipality'],
            ['Ubay',               'ubay',                 'bohol', 'municipality'],
            ['Valencia',           'valencia-bohol',       'bohol', 'municipality'],

            // ===================================================================
            // SIQUIJOR (slug: siquijor, id: 3) — ALL municipalities
            // ===================================================================
            ['Enrique Villanueva', 'enrique-villanueva',   'siquijor', 'municipality'],
            ['Larena',             'larena',               'siquijor', 'municipality'],
            ['Lazi',               'lazi',                 'siquijor', 'municipality'],
            ['Maria',              'maria-siquijor',       'siquijor', 'municipality'],
            ['San Juan',           'san-juan-siquijor',    'siquijor', 'municipality'],
            ['Siquijor (Poblacion)', 'siquijor-poblacion','siquijor', 'municipality'],

            // ===================================================================
            // NEGROS ORIENTAL (slug: negros-oriental, id: 4) — ALL cities & municipalities
            // ===================================================================
            ['Dumaguete City',     'dumaguete-city',       'negros-oriental', 'city'],
            ['Bais City',          'bais-city',            'negros-oriental', 'city'],
            ['Bayawan City',       'bayawan-city',         'negros-oriental', 'city'],
            ['Canlaon City',       'canlaon-city',         'negros-oriental', 'city'],
            ['Guihulngan City',    'guihulngan-city',      'negros-oriental', 'city'],
            ['Tanjay City',        'tanjay-city',          'negros-oriental', 'city'],
            ['Amlan',              'amlan',                'negros-oriental', 'municipality'],
            ['Ayungon',            'ayungon',              'negros-oriental', 'municipality'],
            ['Bacong',             'bacong',               'negros-oriental', 'municipality'],
            ['Basay',              'basay-ne',             'negros-oriental', 'municipality'],
            ['Bindoy',             'bindoy',               'negros-oriental', 'municipality'],
            ['Dauin',              'dauin',                'negros-oriental', 'municipality'],
            ['Jimalalud',          'jimalalud',            'negros-oriental', 'municipality'],
            ['La Libertad',        'la-libertad-ne',       'negros-oriental', 'municipality'],
            ['Mabinay',            'mabinay',              'negros-oriental', 'municipality'],
            ['Manjuyod',           'manjuyod',             'negros-oriental', 'municipality'],
            ['Pamplona',           'pamplona-ne',          'negros-oriental', 'municipality'],
            ['San Jose',           'san-jose-ne',          'negros-oriental', 'municipality'],
            ['Santa Catalina',     'santa-catalina',       'negros-oriental', 'municipality'],
            ['Siaton',             'siaton',               'negros-oriental', 'municipality'],
            ['Sibulan',            'sibulan',              'negros-oriental', 'municipality'],
            ['Tayasan',            'tayasan',              'negros-oriental', 'municipality'],
            ['Valencia',           'valencia-ne',          'negros-oriental', 'municipality'],
            ['Vallehermoso',       'vallehermoso',         'negros-oriental', 'municipality'],
            ['Zamboanguita',       'zamboanguita',         'negros-oriental', 'municipality'],

            // ===================================================================
            // LEYTE (slug: leyte, id: 5) — ALL cities & municipalities
            // ===================================================================
            ['Tacloban City',      'tacloban-city',        'leyte', 'city'],
            ['Ormoc City',         'ormoc-city',           'leyte', 'city'],
            ['Baybay City',        'baybay-city',          'leyte', 'city'],
            ['Abuyog',             'abuyog',               'leyte', 'municipality'],
            ['Alangalang',         'alangalang',           'leyte', 'municipality'],
            ['Albuera',            'albuera',              'leyte', 'municipality'],
            ['Babatngon',          'babatngon',            'leyte', 'municipality'],
            ['Barugo',             'barugo',               'leyte', 'municipality'],
            ['Bato',               'bato-leyte',           'leyte', 'municipality'],
            ['Burauen',            'burauen',              'leyte', 'municipality'],
            ['Calubian',           'calubian',             'leyte', 'municipality'],
            ['Capoocan',           'capoocan',             'leyte', 'municipality'],
            ['Carigara',           'carigara',             'leyte', 'municipality'],
            ['Dagami',             'dagami',               'leyte', 'municipality'],
            ['Dulag',              'dulag',                'leyte', 'municipality'],
            ['Hilongos',           'hilongos',             'leyte', 'municipality'],
            ['Hindang',            'hindang',              'leyte', 'municipality'],
            ['Inopacan',           'inopacan',             'leyte', 'municipality'],
            ['Isabel',             'isabel-leyte',         'leyte', 'municipality'],
            ['Jaro',               'jaro-leyte',           'leyte', 'municipality'],
            ['Javier',             'javier-leyte',         'leyte', 'municipality'],
            ['Julita',             'julita',               'leyte', 'municipality'],
            ['Kananga',            'kananga',              'leyte', 'municipality'],
            ['La Paz',             'la-paz-leyte',         'leyte', 'municipality'],
            ['Leyte',              'leyte-municipality',   'leyte', 'municipality'],
            ['MacArthur',          'macarthur-leyte',      'leyte', 'municipality'],
            ['Mahaplag',           'mahaplag',             'leyte', 'municipality'],
            ['Matag-ob',           'matag-ob',             'leyte', 'municipality'],
            ['Matalom',            'matalom',              'leyte', 'municipality'],
            ['Mayorga',            'mayorga',              'leyte', 'municipality'],
            ['Merida',             'merida',               'leyte', 'municipality'],
            ['Palo',               'palo',                 'leyte', 'municipality'],
            ['Palompon',           'palompon',             'leyte', 'municipality'],
            ['Pastrana',           'pastrana',             'leyte', 'municipality'],
            ['San Isidro',         'san-isidro-leyte',     'leyte', 'municipality'],
            ['San Miguel',         'san-miguel-leyte',     'leyte', 'municipality'],
            ['Santa Fe',           'santa-fe-leyte',       'leyte', 'municipality'],
            ['Tabango',            'tabango',              'leyte', 'municipality'],
            ['Tabontabon',         'tabontabon',           'leyte', 'municipality'],
            ['Tanauan',            'tanauan-leyte',        'leyte', 'municipality'],
            ['Tolosa',             'tolosa',               'leyte', 'municipality'],
            ['Tunga',              'tunga',                'leyte', 'municipality'],
            ['Villaba',            'villaba',              'leyte', 'municipality'],

            // ===================================================================
            // SOUTHERN LEYTE (slug: southern-leyte, id: 6) — ALL
            // ===================================================================
            ['Maasin City',        'maasin-city',          'southern-leyte', 'city'],
            ['Anahawan',           'anahawan',             'southern-leyte', 'municipality'],
            ['Bontoc',             'bontoc-southern-leyte','southern-leyte', 'municipality'],
            ['Hinunangan',         'hinunangan',           'southern-leyte', 'municipality'],
            ['Hinundayan',         'hinundayan',           'southern-leyte', 'municipality'],
            ['Libagon',            'libagon',              'southern-leyte', 'municipality'],
            ['Liloan',             'liloan-southern-leyte','southern-leyte', 'municipality'],
            ['Limasawa',           'limasawa',             'southern-leyte', 'municipality'],
            ['Macrohon',           'macrohon',             'southern-leyte', 'municipality'],
            ['Malitbog',           'malitbog-southern-leyte','southern-leyte', 'municipality'],
            ['Padre Burgos',       'padre-burgos',         'southern-leyte', 'municipality'],
            ['Pintuyan',           'pintuyan',             'southern-leyte', 'municipality'],
            ['Saint Bernard',      'saint-bernard',        'southern-leyte', 'municipality'],
            ['San Francisco',      'san-francisco-southern-leyte','southern-leyte', 'municipality'],
            ['San Juan',           'san-juan-southern-leyte','southern-leyte', 'municipality'],
            ['San Ricardo',        'san-ricardo',          'southern-leyte', 'municipality'],
            ['Silago',             'silago',               'southern-leyte', 'municipality'],
            ['Sogod',              'sogod-southern-leyte', 'southern-leyte', 'municipality'],
            ['Tomas Oppus',        'tomas-oppus',          'southern-leyte', 'municipality'],

            // ===================================================================
            // SAMAR (slug: samar, id: 7) — ALL cities & municipalities
            // ===================================================================
            ['Catbalogan City',    'catbalogan-city',      'samar', 'city'],
            ['Calbayog City',      'calbayog-city',        'samar', 'city'],
            ['Almagro',            'almagro',              'samar', 'municipality'],
            ['Basay',              'basay-samar',          'samar', 'municipality'],
            ['Calbiga',            'calbiga',              'samar', 'municipality'],
            ['Daram',              'daram',                'samar', 'municipality'],
            ['Gandara',            'gandara',              'samar', 'municipality'],
            ['Hinabangan',         'hinabangan',           'samar', 'municipality'],
            ['Jiabong',            'jiabong',              'samar', 'municipality'],
            ['Marabut',            'marabut',              'samar', 'municipality'],
            ['Matuguinao',         'matuguinao',           'samar', 'municipality'],
            ['Motiong',            'motiong',              'samar', 'municipality'],
            ['Pagsanghan',         'pagsanghan',           'samar', 'municipality'],
            ['Paranas',            'paranas',              'samar', 'municipality'],
            ['Pinabacdao',         'pinabacdao',           'samar', 'municipality'],
            ['San Jorge',          'san-jorge',            'samar', 'municipality'],
            ['San Jose de Buan',   'san-jose-de-buan',     'samar', 'municipality'],
            ['San Sebastian',      'san-sebastian-samar',  'samar', 'municipality'],
            ['Santa Margarita',    'santa-margarita',      'samar', 'municipality'],
            ['Santa Rita',         'santa-rita-samar',     'samar', 'municipality'],
            ['Santo Nino',         'santo-nino-samar',     'samar', 'municipality'],
            ['Tagapul-an',         'tagapul-an',           'samar', 'municipality'],
            ['Talalora',           'talalora',             'samar', 'municipality'],
            ['Tarangnan',          'tarangnan',            'samar', 'municipality'],
            ['Villareal',          'villareal',            'samar', 'municipality'],
            ['Zumarraga',          'zumarraga',            'samar', 'municipality'],

            // ===================================================================
            // EASTERN SAMAR (slug: eastern-samar, id: 8) — ALL
            // ===================================================================
            ['Borongan City',      'borongan-city',        'eastern-samar', 'city'],
            ['Arteche',            'arteche',              'eastern-samar', 'municipality'],
            ['Balangiga',          'balangiga',            'eastern-samar', 'municipality'],
            ['Balangkayan',        'balangkayan',          'eastern-samar', 'municipality'],
            ['Can-avid',           'can-avid',             'eastern-samar', 'municipality'],
            ['Dolores',            'dolores-eastern-samar','eastern-samar', 'municipality'],
            ['General MacArthur',  'general-macarthur-es','eastern-samar', 'municipality'],
            ['Giporlos',           'giporlos',             'eastern-samar', 'municipality'],
            ['Guiuan',             'guiuan',               'eastern-samar', 'municipality'],
            ['Hernani',            'hernani',              'eastern-samar', 'municipality'],
            ['Jipapad',            'jipapad',              'eastern-samar', 'municipality'],
            ['Lawaan',             'lawaan',               'eastern-samar', 'municipality'],
            ['Llorente',           'llorente',             'eastern-samar', 'municipality'],
            ['Maslog',             'maslog',               'eastern-samar', 'municipality'],
            ['Maydolong',          'maydolong',            'eastern-samar', 'municipality'],
            ['Mercedes',           'mercedes-eastern-samar','eastern-samar', 'municipality'],
            ['Oras',               'oras-eastern-samar',   'eastern-samar', 'municipality'],
            ['Quinapondan',        'quinapondan',          'eastern-samar', 'municipality'],
            ['Salcedo',            'salcedo-eastern-samar','eastern-samar', 'municipality'],
            ['San Julian',         'san-julian',           'eastern-samar', 'municipality'],
            ['San Policarpo',      'san-policarpo',        'eastern-samar', 'municipality'],
            ['Sulat',              'sulat',                'eastern-samar', 'municipality'],
            ['Taft',               'taft-eastern-samar',   'eastern-samar', 'municipality'],

            // ===================================================================
            // NORTHERN SAMAR (slug: northern-samar, id: 9) — ALL
            // ===================================================================
            ['Catarman',           'catarman',             'northern-samar', 'municipality'],
            ['Allen',              'allen',                'northern-samar', 'municipality'],
            ['Biri',               'biri',                 'northern-samar', 'municipality'],
            ['Bobon',              'bobon',                'northern-samar', 'municipality'],
            ['Capul',              'capul',                'northern-samar', 'municipality'],
            ['Catubig',            'catubig',              'northern-samar', 'municipality'],
            ['Gamay',              'gamay',                'northern-samar', 'municipality'],
            ['Laoang',             'laoang',               'northern-samar', 'municipality'],
            ['Lapinig',            'lapinig',              'northern-samar', 'municipality'],
            ['Las Navas',          'las-navas',            'northern-samar', 'municipality'],
            ['Lavezares',          'lavezares',            'northern-samar', 'municipality'],
            ['Lope de Vega',       'lope-de-vega',         'northern-samar', 'municipality'],
            ['Mapanas',            'mapanas',              'northern-samar', 'municipality'],
            ['Mondragon',          'mondragon',            'northern-samar', 'municipality'],
            ['Palapag',            'palapag',              'northern-samar', 'municipality'],
            ['Pambujan',           'pambujan',             'northern-samar', 'municipality'],
            ['Rosario',            'rosario-northern-samar','northern-samar', 'municipality'],
            ['San Antonio',        'san-antonio-northern-samar','northern-samar', 'municipality'],
            ['San Isidro',         'san-isidro-northern-samar','northern-samar', 'municipality'],
            ['San Jose',           'san-jose-northern-samar','northern-samar', 'municipality'],
            ['San Roque',          'san-roque',            'northern-samar', 'municipality'],
            ['San Vicente',        'san-vicente-northern-samar','northern-samar', 'municipality'],
            ['Silvino Lobos',      'silvino-lobos',        'northern-samar', 'municipality'],
            ['Victoria',           'victoria-northern-samar','northern-samar', 'municipality'],

            // ===================================================================
            // BILIRAN (slug: biliran, id: 10) — ALL
            // ===================================================================
            ['Naval',              'naval',                'biliran', 'municipality'],
            ['Almeria',            'almeria',              'biliran', 'municipality'],
            ['Biliran',            'biliran-town',         'biliran', 'municipality'],
            ['Cabucgayan',         'cabucgayan',           'biliran', 'municipality'],
            ['Caibiran',           'caibiran',             'biliran', 'municipality'],
            ['Culaba',             'culaba',               'biliran', 'municipality'],
            ['Kawayan',            'kawayan',              'biliran', 'municipality'],
            ['Maripipi',           'maripipi',             'biliran', 'municipality'],

            // ===================================================================
            // ILOILO (slug: iloilo, id: 11) — ALL cities & municipalities
            // ===================================================================
            ['Iloilo City',         'iloilo-city',          'iloilo', 'city'],
            ['Passi City',          'passi-city',           'iloilo', 'city'],
            ['Ajuy',                'ajuy',                 'iloilo', 'municipality'],
            ['Alimodian',           'alimodian',            'iloilo', 'municipality'],
            ['Anilao',              'anilao',               'iloilo', 'municipality'],
            ['Badiangan',           'badiangan',            'iloilo', 'municipality'],
            ['Balasan',             'balasan',              'iloilo', 'municipality'],
            ['Banate',              'banate',               'iloilo', 'municipality'],
            ['Barotac Nuevo',       'barotac-nuevo',        'iloilo', 'municipality'],
            ['Barotac Viejo',       'barotac-viejo',        'iloilo', 'municipality'],
            ['Batad',               'batad',                'iloilo', 'municipality'],
            ['Bingawan',            'bingawan',             'iloilo', 'municipality'],
            ['Cabatuan',            'cabatuan-iloilo',      'iloilo', 'municipality'],
            ['Calinog',             'calinog',              'iloilo', 'municipality'],
            ['Carles',              'carles',               'iloilo', 'municipality'],
            ['Concepcion',          'concepcion-iloilo',    'iloilo', 'municipality'],
            ['Dingle',              'dingle',               'iloilo', 'municipality'],
            ['Duenas',              'duenas',               'iloilo', 'municipality'],
            ['Dumangas',            'dumangas',             'iloilo', 'municipality'],
            ['Estancia',            'estancia',             'iloilo', 'municipality'],
            ['Guimbal',             'guimbal',              'iloilo', 'municipality'],
            ['Igbaras',             'igbaras',              'iloilo', 'municipality'],
            ['Janiuay',             'janiuay',              'iloilo', 'municipality'],
            ['Lambunao',            'lambunao',             'iloilo', 'municipality'],
            ['Leganes',             'leganes',              'iloilo', 'municipality'],
            ['Lemery',              'lemery-iloilo',        'iloilo', 'municipality'],
            ['Leon',                'leon-iloilo',          'iloilo', 'municipality'],
            ['Maasin',              'maasin-iloilo',        'iloilo', 'municipality'],
            ['Miagao',              'miagao',               'iloilo', 'municipality'],
            ['Mina',                'mina',                 'iloilo', 'municipality'],
            ['New Lucena',          'new-lucena',           'iloilo', 'municipality'],
            ['Oton',                'oton',                 'iloilo', 'municipality'],
            ['Pavia',               'pavia',                'iloilo', 'municipality'],
            ['Pototan',             'pototan',              'iloilo', 'municipality'],
            ['San Dionisio',        'san-dionisio',         'iloilo', 'municipality'],
            ['San Enrique',         'san-enrique-iloilo',   'iloilo', 'municipality'],
            ['San Joaquin',         'san-joaquin',          'iloilo', 'municipality'],
            ['San Miguel',          'san-miguel-iloilo',    'iloilo', 'municipality'],
            ['San Rafael',          'san-rafael-iloilo',    'iloilo', 'municipality'],
            ['Santa Barbara',       'santa-barbara-iloilo', 'iloilo', 'municipality'],
            ['Sara',                'sara',                 'iloilo', 'municipality'],
            ['Tigbauan',            'tigbauan',             'iloilo', 'municipality'],
            ['Tubungan',            'tubungan',             'iloilo', 'municipality'],
            ['Zarraga',             'zarraga',              'iloilo', 'municipality'],

            // ===================================================================
            // NEGROS OCCIDENTAL (slug: negros-occidental, id: 12) — ALL
            // ===================================================================
            ['Bacolod City',        'bacolod-city',         'negros-occidental', 'city'],
            ['Bago City',           'bago-city',            'negros-occidental', 'city'],
            ['Cadiz City',          'cadiz-city',           'negros-occidental', 'city'],
            ['Escalante City',      'escalante-city',       'negros-occidental', 'city'],
            ['Himamaylan City',     'himamaylan-city',      'negros-occidental', 'city'],
            ['Kabankalan City',     'kabankalan-city',      'negros-occidental', 'city'],
            ['La Carlota City',     'la-carlota-city',      'negros-occidental', 'city'],
            ['Sagay City',          'sagay-city',           'negros-occidental', 'city'],
            ['San Carlos City',     'san-carlos-city-negros','negros-occidental', 'city'],
            ['Silay City',          'silay-city',           'negros-occidental', 'city'],
            ['Sipalay City',        'sipalay-city',         'negros-occidental', 'city'],
            ['Talisay City',        'talisay-city-negros',  'negros-occidental', 'city'],
            ['Victorias City',      'victorias-city',       'negros-occidental', 'city'],
            ['Binalbagan',          'binalbagan',           'negros-occidental', 'municipality'],
            ['Calatrava',           'calatrava-negros',     'negros-occidental', 'municipality'],
            ['Candoni',             'candoni',              'negros-occidental', 'municipality'],
            ['Cauayan',             'cauayan',              'negros-occidental', 'municipality'],
            ['Enrique B. Magalona', 'enrique-b-magalona',   'negros-occidental', 'municipality'],
            ['Hinigaran',           'hinigaran',            'negros-occidental', 'municipality'],
            ['Hinoba-an',           'hinoba-an',            'negros-occidental', 'municipality'],
            ['Ilog',                'ilog',                 'negros-occidental', 'municipality'],
            ['Isabela',             'isabela-negros',       'negros-occidental', 'municipality'],
            ['La Castellana',       'la-castellana',        'negros-occidental', 'municipality'],
            ['Manapla',             'manapla',              'negros-occidental', 'municipality'],
            ['Moises Padilla',      'moises-padilla',       'negros-occidental', 'municipality'],
            ['Murcia',              'murcia',               'negros-occidental', 'municipality'],
            ['Pontevedra',          'pontevedra-negros',    'negros-occidental', 'municipality'],
            ['Pulupandan',          'pulupandan',           'negros-occidental', 'municipality'],
            ['Salvador Benedicto',  'salvador-benedicto',   'negros-occidental', 'municipality'],
            ['San Enrique',         'san-enrique-negros',   'negros-occidental', 'municipality'],
            ['Toboso',              'toboso',               'negros-occidental', 'municipality'],
            ['Valladolid',          'valladolid',           'negros-occidental', 'municipality'],

            // ===================================================================
            // CAPIZ (slug: capiz, id: 13) — ALL
            // ===================================================================
            ['Roxas City',          'roxas-city',           'capiz', 'city'],
            ['Cuartero',            'cuartero',             'capiz', 'municipality'],
            ['Dao',                 'dao-capiz',            'capiz', 'municipality'],
            ['Dumalag',             'dumalag',              'capiz', 'municipality'],
            ['Dumarao',             'dumarao',              'capiz', 'municipality'],
            ['Ivisan',              'ivisan',               'capiz', 'municipality'],
            ['Jamindan',            'jamindan',             'capiz', 'municipality'],
            ['Ma-ayon',             'ma-ayon',              'capiz', 'municipality'],
            ['Mambusao',            'mambusao',             'capiz', 'municipality'],
            ['Panay',               'panay',                'capiz', 'municipality'],
            ['Panitan',             'panitan',              'capiz', 'municipality'],
            ['Pilar',               'pilar-capiz',          'capiz', 'municipality'],
            ['Pontevedra',          'pontevedra-capiz',     'capiz', 'municipality'],
            ['President Roxas',     'president-roxas',      'capiz', 'municipality'],
            ['Sapian',              'sapian',               'capiz', 'municipality'],
            ['Sigma',               'sigma',                'capiz', 'municipality'],
            ['Tapaz',               'tapaz',                'capiz', 'municipality'],

            // ===================================================================
            // AKLAN (slug: aklan, id: 14) — ALL
            // ===================================================================
            ['Kalibo',              'kalibo',               'aklan', 'municipality'],
            ['Malay (Boracay)',     'malay-aklan',          'aklan', 'municipality'],
            ['Altavas',             'altavas',              'aklan', 'municipality'],
            ['Balete',              'balete',               'aklan', 'municipality'],
            ['Banga',               'banga-aklan',          'aklan', 'municipality'],
            ['Batan',               'batan-aklan',          'aklan', 'municipality'],
            ['Buruanga',            'buruanga',             'aklan', 'municipality'],
            ['Ibajay',              'ibajay',               'aklan', 'municipality'],
            ['Lezo',                'lezo',                 'aklan', 'municipality'],
            ['Libacao',             'libacao',              'aklan', 'municipality'],
            ['Madalag',             'madalag',              'aklan', 'municipality'],
            ['Makato',              'makato',               'aklan', 'municipality'],
            ['Malinao',             'malinao',              'aklan', 'municipality'],
            ['Nabas',               'nabas',                'aklan', 'municipality'],
            ['New Washington',      'new-washington',       'aklan', 'municipality'],
            ['Numancia',            'numancia',             'aklan', 'municipality'],
            ['Tangalan',            'tangalan',             'aklan', 'municipality'],

            // ===================================================================
            // ANTIQUE (slug: antique, id: 15) — ALL
            // ===================================================================
            ['San Jose de Buenavista', 'san-jose-buenavista','antique', 'municipality'],
            ['Anini-y',             'anini-y',              'antique', 'municipality'],
            ['Barbaza',             'barbaza',              'antique', 'municipality'],
            ['Belison',             'belison',              'antique', 'municipality'],
            ['Bugasong',            'bugasong',             'antique', 'municipality'],
            ['Caluya',              'caluya',               'antique', 'municipality'],
            ['Culasi',              'culasi',               'antique', 'municipality'],
            ['Hamtic',              'hamtic',               'antique', 'municipality'],
            ['Laua-an',             'laua-an',              'antique', 'municipality'],
            ['Libertad',            'libertad-antique',     'antique', 'municipality'],
            ['Pandan',              'pandan-antique',       'antique', 'municipality'],
            ['Patnongon',           'patnongon',            'antique', 'municipality'],
            ['Sebaste',             'sebaste',              'antique', 'municipality'],
            ['Sibalom',             'sibalom',              'antique', 'municipality'],
            ['Tibiao',              'tibiao',               'antique', 'municipality'],
            ['Tobias Fornier',      'tobias-fornier',       'antique', 'municipality'],
            ['Valderrama',          'valderrama',           'antique', 'municipality'],

            // ===================================================================
            // GUIMARAS (slug: guimaras, id: 16) — ALL
            // ===================================================================
            ['Jordan',              'jordan-guimaras',      'guimaras', 'municipality'],
            ['Buenavista',          'buenavista-guimaras',  'guimaras', 'municipality'],
            ['Nueva Valencia',      'nueva-valencia',       'guimaras', 'municipality'],
            ['San Lorenzo',         'san-lorenzo-guimaras', 'guimaras', 'municipality'],
            ['Sibunag',             'sibunag',              'guimaras', 'municipality'],

            // ===================================================================
            // ZAMBOANGA DEL SUR (slug: zamboanga-del-sur, id: 17) — ALL
            // ===================================================================
            ['Zamboanga City',      'zamboanga-city',       'zamboanga-del-sur', 'city'],
            ['Pagadian City',       'pagadian-city',        'zamboanga-del-sur', 'city'],
            ['Aurora',              'aurora-zamboanga-del-sur','zamboanga-del-sur', 'municipality'],
            ['Bayog',               'bayog',                'zamboanga-del-sur', 'municipality'],
            ['Dimataling',          'dimataling',           'zamboanga-del-sur', 'municipality'],
            ['Dinas',               'dinas',                'zamboanga-del-sur', 'municipality'],
            ['Dumalinao',           'dumalinao',            'zamboanga-del-sur', 'municipality'],
            ['Dumingag',            'dumingag',             'zamboanga-del-sur', 'municipality'],
            ['Guipos',              'guipos',               'zamboanga-del-sur', 'municipality'],
            ['Josefina',            'josefina',             'zamboanga-del-sur', 'municipality'],
            ['Kumalarang',          'kumalarang',           'zamboanga-del-sur', 'municipality'],
            ['Labangan',            'labangan',             'zamboanga-del-sur', 'municipality'],
            ['Lakewood',            'lakewood',             'zamboanga-del-sur', 'municipality'],
            ['Lapuyan',             'lapuyan',              'zamboanga-del-sur', 'municipality'],
            ['Mahayag',             'mahayag',              'zamboanga-del-sur', 'municipality'],
            ['Margosatubig',        'margosatubig',         'zamboanga-del-sur', 'municipality'],
            ['Midsalip',            'midsalip',             'zamboanga-del-sur', 'municipality'],
            ['Molave',              'molave',               'zamboanga-del-sur', 'municipality'],
            ['Pitogo',              'pitogo',               'zamboanga-del-sur', 'municipality'],
            ['Ramon Magsaysay',     'ramon-magsaysay-zds',  'zamboanga-del-sur', 'municipality'],
            ['San Miguel',          'san-miguel-zds',       'zamboanga-del-sur', 'municipality'],
            ['San Pablo',           'san-pablo-zds',        'zamboanga-del-sur', 'municipality'],
            ['Sominot',             'sominot',              'zamboanga-del-sur', 'municipality'],
            ['Tabina',              'tabina',               'zamboanga-del-sur', 'municipality'],
            ['Tambulig',            'tambulig',             'zamboanga-del-sur', 'municipality'],
            ['Tigbao',              'tigbao',               'zamboanga-del-sur', 'municipality'],
            ['Tukuran',             'tukuran',              'zamboanga-del-sur', 'municipality'],
            ['Vincenzo A. Sagun',   'vincenzo-a-sagun',    'zamboanga-del-sur', 'municipality'],

            // ===================================================================
            // ZAMBOANGA DEL NORTE (slug: zamboanga-del-norte, id: 18) — ALL
            // ===================================================================
            ['Dipolog City',        'dipolog-city',         'zamboanga-del-norte', 'city'],
            ['Dapitan City',        'dapitan-city',         'zamboanga-del-norte', 'city'],
            ['Bacungan',            'bacungan',             'zamboanga-del-norte', 'municipality'],
            ['Baliguian',           'baliguian',            'zamboanga-del-norte', 'municipality'],
            ['Godod',               'godod',                'zamboanga-del-norte', 'municipality'],
            ['Gutalac',             'gutalac',              'zamboanga-del-norte', 'municipality'],
            ['Jose Dalman',         'jose-dalman',          'zamboanga-del-norte', 'municipality'],
            ['Kalawit',             'kalawit',              'zamboanga-del-norte', 'municipality'],
            ['Katipunan',           'katipunan',            'zamboanga-del-norte', 'municipality'],
            ['La Libertad',         'la-libertad-zdn',      'zamboanga-del-norte', 'municipality'],
            ['Labason',             'labason',              'zamboanga-del-norte', 'municipality'],
            ['Leon B. Postigo',     'leon-b-postigo',       'zamboanga-del-norte', 'municipality'],
            ['Liloy',               'liloy',                'zamboanga-del-norte', 'municipality'],
            ['Manukan',             'manukan',              'zamboanga-del-norte', 'municipality'],
            ['Mutia',               'mutia',                'zamboanga-del-norte', 'municipality'],
            ['Pinan',               'pinan',                'zamboanga-del-norte', 'municipality'],
            ['Polanco',             'polanco',              'zamboanga-del-norte', 'municipality'],
            ['Roxas',               'roxas-zdn',            'zamboanga-del-norte', 'municipality'],
            ['Rizal',               'rizal-zdn',            'zamboanga-del-norte', 'municipality'],
            ['Salug',               'salug',                'zamboanga-del-norte', 'municipality'],
            ['Sergio Osmena',       'sergio-osmena',        'zamboanga-del-norte', 'municipality'],
            ['Siayan',              'siayan',               'zamboanga-del-norte', 'municipality'],
            ['Sibuco',              'sibuco',               'zamboanga-del-norte', 'municipality'],
            ['Sibutad',             'sibutad',              'zamboanga-del-norte', 'municipality'],
            ['Sindangan',           'sindangan',            'zamboanga-del-norte', 'municipality'],
            ['Siocon',              'siocon',               'zamboanga-del-norte', 'municipality'],
            ['Sirawai',             'sirawai',              'zamboanga-del-norte', 'municipality'],
            ['Tampilisan',          'tampilisan',           'zamboanga-del-norte', 'municipality'],

            // ===================================================================
            // ZAMBOANGA SIBUGAY (slug: zamboanga-sibugay, id: 19) — ALL
            // ===================================================================
            ['Ipil',                'ipil',                 'zamboanga-sibugay', 'municipality'],
            ['Alicia',              'alicia-zbg',           'zamboanga-sibugay', 'municipality'],
            ['Buug',                'buug',                 'zamboanga-sibugay', 'municipality'],
            ['Diplahan',            'diplahan',             'zamboanga-sibugay', 'municipality'],
            ['Imelda',              'imelda-zbg',           'zamboanga-sibugay', 'municipality'],
            ['Kabasalan',           'kabasalan',            'zamboanga-sibugay', 'municipality'],
            ['Mabuhay',             'mabuhay',              'zamboanga-sibugay', 'municipality'],
            ['Malangas',            'malangas',             'zamboanga-sibugay', 'municipality'],
            ['Naga',                'naga-zbg',             'zamboanga-sibugay', 'municipality'],
            ['Olutanga',            'olutanga',             'zamboanga-sibugay', 'municipality'],
            ['Payao',               'payao',                'zamboanga-sibugay', 'municipality'],
            ['Roseller Lim',        'roseller-lim',         'zamboanga-sibugay', 'municipality'],
            ['Siay',                'siay',                 'zamboanga-sibugay', 'municipality'],
            ['Talusan',             'talusan',              'zamboanga-sibugay', 'municipality'],
            ['Titay',               'titay',                'zamboanga-sibugay', 'municipality'],

            // ===================================================================
            // ISABELA CITY (BASILAN) (slug: isabela-city-basilan, id: 20) — city only
            // ===================================================================
            ['Isabela City',        'isabela-city',         'isabela-city-basilan', 'city'],

            // ===================================================================
            // BUKIDNON (slug: bukidnon, id: 21) — ALL cities & municipalities
            // ===================================================================
            ['Malaybalay City',     'malaybalay-city',      'bukidnon', 'city'],
            ['Valencia City',       'valencia-city-bukidnon','bukidnon', 'city'],
            ['Baungon',             'baungon',              'bukidnon', 'municipality'],
            ['Cabanglasan',         'cabanglasan',          'bukidnon', 'municipality'],
            ['Damulog',             'damulog',              'bukidnon', 'municipality'],
            ['Dangcagan',           'dangcagan',            'bukidnon', 'municipality'],
            ['Don Carlos',          'don-carlos',           'bukidnon', 'municipality'],
            ['Impasugong',          'impasugong',           'bukidnon', 'municipality'],
            ['Kadingilan',          'kadingilan',           'bukidnon', 'municipality'],
            ['Kalilangan',          'kalilangan',           'bukidnon', 'municipality'],
            ['Kibawe',              'kibawe',               'bukidnon', 'municipality'],
            ['Kitaotao',            'kitaotao',             'bukidnon', 'municipality'],
            ['Lantapan',            'lantapan',             'bukidnon', 'municipality'],
            ['Libona',              'libona',               'bukidnon', 'municipality'],
            ['Malitbog',            'malitbog-bukidnon',    'bukidnon', 'municipality'],
            ['Manolo Fortich',      'manolo-fortich',       'bukidnon', 'municipality'],
            ['Maramag',             'maramag',              'bukidnon', 'municipality'],
            ['Pangantucan',         'pangantucan',          'bukidnon', 'municipality'],
            ['Quezon',              'quezon-bukidnon',      'bukidnon', 'municipality'],
            ['San Fernando',        'san-fernando-bukidnon','bukidnon', 'municipality'],
            ['Sumilao',             'sumilao',              'bukidnon', 'municipality'],
            ['Talakag',             'talakag',              'bukidnon', 'municipality'],

            // ===================================================================
            // MISAMIS ORIENTAL (slug: misamis-oriental, id: 22) — ALL
            // ===================================================================
            ['Cagayan de Oro City', 'cagayan-de-oro-city',  'misamis-oriental', 'city'],
            ['Gingoog City',        'gingoog-city',         'misamis-oriental', 'city'],
            ['El Salvador City',    'el-salvador-city',     'misamis-oriental', 'city'],
            ['Alubijid',            'alubijid',             'misamis-oriental', 'municipality'],
            ['Balingasag',          'balingasag',           'misamis-oriental', 'municipality'],
            ['Balingoan',           'balingoan',            'misamis-oriental', 'municipality'],
            ['Binuangan',           'binuangan',            'misamis-oriental', 'municipality'],
            ['Claveria',            'claveria-misamis',     'misamis-oriental', 'municipality'],
            ['Gitagum',             'gitagum',              'misamis-oriental', 'municipality'],
            ['Initao',              'initao',               'misamis-oriental', 'municipality'],
            ['Jasaan',              'jsaan',                'misamis-oriental', 'municipality'],
            ['Kinoguitan',          'kinoguitan',           'misamis-oriental', 'municipality'],
            ['Lagonglong',          'lagonglong',           'misamis-oriental', 'municipality'],
            ['Laguindingan',        'laguindingan',          'misamis-oriental', 'municipality'],
            ['Libertad',            'libertad-misamis',      'misamis-oriental', 'municipality'],
            ['Lugait',              'lugait',               'misamis-oriental', 'municipality'],
            ['Magsaysay',           'magsaysay-misamis',     'misamis-oriental', 'municipality'],
            ['Manticao',            'manticao',             'misamis-oriental', 'municipality'],
            ['Medina',              'medina',               'misamis-oriental', 'municipality'],
            ['Naawan',              'naawan',               'misamis-oriental', 'municipality'],
            ['Opol',                'opol',                 'misamis-oriental', 'municipality'],
            ['Salay',               'salay',                'misamis-oriental', 'municipality'],
            ['Sugbongcogon',        'sugbongcogon',         'misamis-oriental', 'municipality'],
            ['Tagoloan',            'tagoloan-misamis',     'misamis-oriental', 'municipality'],
            ['Talisayan',           'talisayan',            'misamis-oriental', 'municipality'],
            ['Villanueva',          'villanueva-misamis',   'misamis-oriental', 'municipality'],

            // ===================================================================
            // MISAMIS OCCIDENTAL (slug: misamis-occidental, id: 23) — ALL
            // ===================================================================
            ['Oroquieta City',      'oroquieta-city',       'misamis-occidental', 'city'],
            ['Ozamiz City',         'ozamiz-city',          'misamis-occidental', 'city'],
            ['Tangub City',         'tangub-city',          'misamis-occidental', 'city'],
            ['Aloran',              'aloran',               'misamis-occidental', 'municipality'],
            ['Baliangao',           'baliangao',            'misamis-occidental', 'municipality'],
            ['Bonifacio',           'bonifacio',            'misamis-occidental', 'municipality'],
            ['Calamba',             'calamba-misocc',       'misamis-occidental', 'municipality'],
            ['Clarin',              'clarin-misocc',        'misamis-occidental', 'municipality'],
            ['Concepcion',          'concepcion-misocc',    'misamis-occidental', 'municipality'],
            ['Don Victoriano Chiongbian', 'don-victoriano-chiongbian','misamis-occidental', 'municipality'],
            ['Jimenez',             'jimenez',              'misamis-occidental', 'municipality'],
            ['Lopez Jaena',         'lopez-jaena',          'misamis-occidental', 'municipality'],
            ['Panaon',              'panaon',               'misamis-occidental', 'municipality'],
            ['Plaridel',            'plaridel-misocc',      'misamis-occidental', 'municipality'],
            ['Sapang Dalaga',       'sapang-dalaga',        'misamis-occidental', 'municipality'],
            ['Sinacaban',           'sinacaban',            'misamis-occidental', 'municipality'],
            ['Tudela',              'tudela-misocc',        'misamis-occidental', 'municipality'],

            // ===================================================================
            // LANAO DEL NORTE (slug: lanao-del-norte, id: 24) — ALL
            // ===================================================================
            ['Iligan City',         'iligan-city',          'lanao-del-norte', 'city'],
            ['Bacolod',             'bacolod-ldn',          'lanao-del-norte', 'municipality'],
            ['Balo-i',              'balo-i',               'lanao-del-norte', 'municipality'],
            ['Baroy',               'baroy',                'lanao-del-norte', 'municipality'],
            ['Kapatagan',           'kapatagan-ldn',        'lanao-del-norte', 'municipality'],
            ['Kauswagan',           'kauswagan',            'lanao-del-norte', 'municipality'],
            ['Kolambugan',          'kolambugan',           'lanao-del-norte', 'municipality'],
            ['Lala',                'lala',                 'lanao-del-norte', 'municipality'],
            ['Linamon',             'linamon',              'lanao-del-norte', 'municipality'],
            ['Magsaysay',           'magsaysay-ldn',        'lanao-del-norte', 'municipality'],
            ['Maigo',               'maigo',                'lanao-del-norte', 'municipality'],
            ['Matungao',            'matungao',             'lanao-del-norte', 'municipality'],
            ['Munai',               'munai',                'lanao-del-norte', 'municipality'],
            ['Nunungan',            'nunungan',             'lanao-del-norte', 'municipality'],
            ['Pantao Ragat',        'pantao-ragat',         'lanao-del-norte', 'municipality'],
            ['Pantar',              'pantar',               'lanao-del-norte', 'municipality'],
            ['Poona Piagapo',       'poona-piagapo',        'lanao-del-norte', 'municipality'],
            ['Salvador',            'salvador-ldn',         'lanao-del-norte', 'municipality'],
            ['Sapad',               'sapad',                'lanao-del-norte', 'municipality'],
            ['Sultan Naga Dimaporo','sultan-naga-dimaporo', 'lanao-del-norte', 'municipality'],
            ['Tagoloan',            'tagoloan-ldn',         'lanao-del-norte', 'municipality'],
            ['Tangcal',             'tangcal',              'lanao-del-norte', 'municipality'],
            ['Tubod',               'tubod',                'lanao-del-norte', 'municipality'],

            // ===================================================================
            // CAMIGUIN (slug: camiguin, id: 25) — ALL
            // ===================================================================
            ['Mambajao',            'mambajao',             'camiguin', 'municipality'],
            ['Catarman',            'catarman-camiguin',    'camiguin', 'municipality'],
            ['Guinsiliban',         'guinsiliban',          'camiguin', 'municipality'],
            ['Mahinog',             'mahinog',              'camiguin', 'municipality'],
            ['Sagay',               'sagay-camiguin',       'camiguin', 'municipality'],

            // ===================================================================
            // DAVAO DEL SUR (slug: davao-del-sur, id: 26) — ALL
            // ===================================================================
            ['Davao City',          'davao-city',           'davao-del-sur', 'city'],
            ['Digos City',          'digos-city',           'davao-del-sur', 'city'],
            ['Bansalan',            'bansalan',             'davao-del-sur', 'municipality'],
            ['Hagonoy',             'hagonoy-davao',        'davao-del-sur', 'municipality'],
            ['Kiblawan',            'kiblawan',             'davao-del-sur', 'municipality'],
            ['Magsaysay',           'magsaysay-davao',      'davao-del-sur', 'municipality'],
            ['Malalag',             'malalag',              'davao-del-sur', 'municipality'],
            ['Matanao',             'matanao',              'davao-del-sur', 'municipality'],
            ['Padada',              'padada',               'davao-del-sur', 'municipality'],
            ['Santa Cruz',          'santa-cruz-davao',     'davao-del-sur', 'municipality'],
            ['Sulop',               'sulop',                'davao-del-sur', 'municipality'],

            // ===================================================================
            // DAVAO DEL NORTE (slug: davao-del-norte, id: 27) — ALL
            // ===================================================================
            ['Tagum City',          'tagum-city',           'davao-del-norte', 'city'],
            ['Panabo City',         'panabo-city',          'davao-del-norte', 'city'],
            ['Samal City',          'samal-city',           'davao-del-norte', 'city'],
            ['Asuncion',            'asuncion',             'davao-del-norte', 'municipality'],
            ['Braulio E. Dujali',   'braulio-e-dujali',     'davao-del-norte', 'municipality'],
            ['Carmen',              'carmen-davao',         'davao-del-norte', 'municipality'],
            ['Kapalong',            'kapalong',             'davao-del-norte', 'municipality'],
            ['New Corella',         'new-corella',          'davao-del-norte', 'municipality'],
            ['San Isidro',          'san-isidro-davao-norte','davao-del-norte', 'municipality'],
            ['Santo Tomas',         'santo-tomas-davao',    'davao-del-norte', 'municipality'],
            ['Talaingod',           'talaingod',            'davao-del-norte', 'municipality'],

            // ===================================================================
            // DAVAO ORIENTAL (slug: davao-oriental, id: 28) — ALL
            // ===================================================================
            ['Mati City',           'mati-city',            'davao-oriental', 'city'],
            ['Baganga',             'baganga',              'davao-oriental', 'municipality'],
            ['Banaybanay',          'banaybanay',           'davao-oriental', 'municipality'],
            ['Boston',              'boston',               'davao-oriental', 'municipality'],
            ['Caraga',              'caraga-davao',         'davao-oriental', 'municipality'],
            ['Cateel',              'cateel',               'davao-oriental', 'municipality'],
            ['Governor Generoso',   'governor-generoso',    'davao-oriental', 'municipality'],
            ['Lupon',               'lupon',                'davao-oriental', 'municipality'],
            ['Manay',               'manay',                'davao-oriental', 'municipality'],
            ['San Isidro',          'san-isidro-davao-oriental','davao-oriental', 'municipality'],
            ['Tarragona',           'tarragona',            'davao-oriental', 'municipality'],

            // ===================================================================
            // DAVAO DE ORO (slug: davao-de-oro, id: 29) — ALL
            // ===================================================================
            ['Nabunturan',          'nabunturan',           'davao-de-oro', 'municipality'],
            ['Compostela',          'compostela-davao',     'davao-de-oro', 'municipality'],
            ['Laak',                'laak',                 'davao-de-oro', 'municipality'],
            ['Mabini',              'mabini-davao',         'davao-de-oro', 'municipality'],
            ['Maco',                'maco',                 'davao-de-oro', 'municipality'],
            ['Maragusan',           'maragusan',            'davao-de-oro', 'municipality'],
            ['Mawab',               'mawab',                'davao-de-oro', 'municipality'],
            ['Monkayo',             'monkayo',              'davao-de-oro', 'municipality'],
            ['Montevista',          'montevista',           'davao-de-oro', 'municipality'],
            ['New Bataan',          'new-bataan',           'davao-de-oro', 'municipality'],
            ['Pantukan',            'pantukan',             'davao-de-oro', 'municipality'],

            // ===================================================================
            // DAVAO OCCIDENTAL (slug: davao-occidental, id: 30) — ALL
            // ===================================================================
            ['Malita',              'malita',               'davao-occidental', 'municipality'],
            ['Don Marcelino',       'don-marcelino',        'davao-occidental', 'municipality'],
            ['Jose Abad Santos',    'jose-abad-santos',     'davao-occidental', 'municipality'],
            ['Santa Maria',         'santa-maria-davao',    'davao-occidental', 'municipality'],
            ['Sarangani',           'sarangani-island',     'davao-occidental', 'municipality'],

            // ===================================================================
            // SOUTH COTABATO (slug: south-cotabato, id: 31) — ALL
            // ===================================================================
            ['General Santos City', 'general-santos-city',  'south-cotabato', 'city'],
            ['Koronadal City',      'koronadal-city',       'south-cotabato', 'city'],
            ['Banga',               'banga-south-cotabato', 'south-cotabato', 'municipality'],
            ['Lake Sebu',           'lake-sebu',            'south-cotabato', 'municipality'],
            ['Norala',              'norala',               'south-cotabato', 'municipality'],
            ['Polomolok',           'polomolok',            'south-cotabato', 'municipality'],
            ['Sto. Nino',           'sto-nino-south-cotabato','south-cotabato', 'municipality'],
            ['Surallah',            'surallah',             'south-cotabato', 'municipality'],
            ['Tampakan',            'tampakan',             'south-cotabato', 'municipality'],
            ['Tantangan',           'tantangan',            'south-cotabato', 'municipality'],
            ['Tboli',               'tboli',                'south-cotabato', 'municipality'],
            ['Tupi',                'tupi',                 'south-cotabato', 'municipality'],

            // ===================================================================
            // NORTH COTABATO (slug: north-cotabato, id: 32) — ALL
            // ===================================================================
            ['Kidapawan City',      'kidapawan-city',       'north-cotabato', 'city'],
            ['Cotabato City',       'cotabato-city',        'north-cotabato', 'city'],
            ['Alamada',             'alamada',              'north-cotabato', 'municipality'],
            ['Aleosan',             'aleosan',              'north-cotabato', 'municipality'],
            ['Antipas',             'antipas',              'north-cotabato', 'municipality'],
            ['Arakan',              'arakan',               'north-cotabato', 'municipality'],
            ['Banisilan',           'banisilan',            'north-cotabato', 'municipality'],
            ['Carmen',              'carmen-north-cotabato','north-cotabato', 'municipality'],
            ['Kabacan',             'kabacan',              'north-cotabato', 'municipality'],
            ['Libungan',            'libungan',             'north-cotabato', 'municipality'],
            ['Mlang',               'mlang',                'north-cotabato', 'municipality'],
            ['Magpet',              'magpet',               'north-cotabato', 'municipality'],
            ['Makilala',            'makilala',             'north-cotabato', 'municipality'],
            ['Matalam',             'matalam',              'north-cotabato', 'municipality'],
            ['Midsayap',            'midsayap',             'north-cotabato', 'municipality'],
            ['Pikit',               'pikit',                'north-cotabato', 'municipality'],
            ['Pigcawayan',          'pigcawayan',           'north-cotabato', 'municipality'],
            ['President Roxas',     'president-roxas-nc',   'north-cotabato', 'municipality'],
            ['Tulunan',             'tulunan',              'north-cotabato', 'municipality'],

            // ===================================================================
            // SARANGANI (slug: sarangani, id: 33) — ALL
            // ===================================================================
            ['Alabel',              'alabel',               'sarangani', 'municipality'],
            ['Glan',                'glan',                 'sarangani', 'municipality'],
            ['Kiamba',              'kiamba',               'sarangani', 'municipality'],
            ['Maasim',              'maasim',               'sarangani', 'municipality'],
            ['Maitum',              'maitum',               'sarangani', 'municipality'],
            ['Malapatan',           'malapatan',            'sarangani', 'municipality'],
            ['Malungon',            'malungon',             'sarangani', 'municipality'],

            // ===================================================================
            // SULTAN KUDARAT (slug: sultan-kudarat, id: 34) — ALL
            // ===================================================================
            ['Tacurong City',       'tacurong-city',        'sultan-kudarat', 'city'],
            ['Bagumbayan',          'bagumbayan',           'sultan-kudarat', 'municipality'],
            ['Columbio',            'columbio',             'sultan-kudarat', 'municipality'],
            ['Esperanza',           'esperanza-sultan',     'sultan-kudarat', 'municipality'],
            ['Isulan',              'isulan',               'sultan-kudarat', 'municipality'],
            ['Kalamansig',          'kalamansig',           'sultan-kudarat', 'municipality'],
            ['Lambayong',           'lambayong',            'sultan-kudarat', 'municipality'],
            ['Lebak',               'lebak',                'sultan-kudarat', 'municipality'],
            ['Lutayan',             'lutayan',              'sultan-kudarat', 'municipality'],
            ['Palimbang',           'palimbang',            'sultan-kudarat', 'municipality'],
            ['President Quirino',   'president-quirino',    'sultan-kudarat', 'municipality'],
            ['Senator Ninoy Aquino','senator-ninoy-aquino', 'sultan-kudarat', 'municipality'],

            // ===================================================================
            // AGUSAN DEL NORTE (slug: agusan-del-norte, id: 35) — ALL
            // ===================================================================
            ['Butuan City',         'butuan-city',          'agusan-del-norte', 'city'],
            ['Cabadbaran City',     'cabadbaran-city',      'agusan-del-norte', 'city'],
            ['Buenavista',          'buenavista-agusan',    'agusan-del-norte', 'municipality'],
            ['Carmen',              'carmen-agusan',        'agusan-del-norte', 'municipality'],
            ['Jabonga',             'jabonga',              'agusan-del-norte', 'municipality'],
            ['Kitcharao',           'kitcharao',            'agusan-del-norte', 'municipality'],
            ['Las Nieves',          'las-nieves',           'agusan-del-norte', 'municipality'],
            ['Magallanes',          'magallanes-agusan',    'agusan-del-norte', 'municipality'],
            ['Nasipit',             'nasipit',              'agusan-del-norte', 'municipality'],
            ['Remedios T. Romualdez','remedios-t-romualdez','agusan-del-norte', 'municipality'],
            ['Santiago',            'santiago-agusan',      'agusan-del-norte', 'municipality'],
            ['Tubay',               'tubay',                'agusan-del-norte', 'municipality'],

            // ===================================================================
            // AGUSAN DEL SUR (slug: agusan-del-sur, id: 36) — ALL
            // ===================================================================
            ['Prosperidad',         'prosperidad',          'agusan-del-sur', 'municipality'],
            ['Bayugan City',        'bayugan-city',         'agusan-del-sur', 'city'],
            ['Bunawan',             'bunawan',              'agusan-del-sur', 'municipality'],
            ['Esperanza',           'esperanza-agusan',     'agusan-del-sur', 'municipality'],
            ['La Paz',              'la-paz-agusan',        'agusan-del-sur', 'municipality'],
            ['Loreto',              'loreto-agusan',        'agusan-del-sur', 'municipality'],
            ['Rosario',             'rosario-agusan',       'agusan-del-sur', 'municipality'],
            ['San Francisco',       'san-francisco-agusan','agusan-del-sur', 'municipality'],
            ['San Luis',            'san-luis-agusan',      'agusan-del-sur', 'municipality'],
            ['Santa Josefa',        'santa-josefa',         'agusan-del-sur', 'municipality'],
            ['Sibagat',             'sibagat',              'agusan-del-sur', 'municipality'],
            ['Talacogon',           'talacogon',            'agusan-del-sur', 'municipality'],
            ['Trento',              'trento',               'agusan-del-sur', 'municipality'],
            ['Veruela',             'veruela',              'agusan-del-sur', 'municipality'],

            // ===================================================================
            // SURIGAO DEL NORTE (slug: surigao-del-norte, id: 37) — ALL
            // ===================================================================
            ['Surigao City',        'surigao-city',         'surigao-del-norte', 'city'],
            ['Alegria',             'alegria-surigao',      'surigao-del-norte', 'municipality'],
            ['Bacuag',              'bacuag',               'surigao-del-norte', 'municipality'],
            ['Burgos',              'burgos-surigao',       'surigao-del-norte', 'municipality'],
            ['Claver',              'claver',               'surigao-del-norte', 'municipality'],
            ['Dapa',                'dapa',                 'surigao-del-norte', 'municipality'],
            ['Del Carmen',          'del-carmen',           'surigao-del-norte', 'municipality'],
            ['General Luna',        'general-luna',         'surigao-del-norte', 'municipality'],
            ['Gigaquit',            'gigaquit',             'surigao-del-norte', 'municipality'],
            ['Mainit',              'mainit',               'surigao-del-norte', 'municipality'],
            ['Malimono',            'malimono',             'surigao-del-norte', 'municipality'],
            ['Pilar',               'pilar-surigao',        'surigao-del-norte', 'municipality'],
            ['Placer',              'placer-surigao',       'surigao-del-norte', 'municipality'],
            ['San Benito',          'san-benito',           'surigao-del-norte', 'municipality'],
            ['San Francisco',       'san-francisco-surigao','surigao-del-norte', 'municipality'],
            ['San Isidro',          'san-isidro-surigao',   'surigao-del-norte', 'municipality'],
            ['San Jose',            'san-jose-surigao',     'surigao-del-norte', 'municipality'],
            ['Santa Monica',        'santa-monica',         'surigao-del-norte', 'municipality'],
            ['Sison',               'sison',                'surigao-del-norte', 'municipality'],
            ['Socorro',             'socorro',              'surigao-del-norte', 'municipality'],
            ['Tagana-an',           'tagana-an',            'surigao-del-norte', 'municipality'],
            ['Tubod',               'tubod-surigao',        'surigao-del-norte', 'municipality'],

            // ===================================================================
            // SURIGAO DEL SUR (slug: surigao-del-sur, id: 38) — ALL
            // ===================================================================
            ['Tandag City',         'tandag-city',          'surigao-del-sur', 'city'],
            ['Bislig City',         'bislig-city',          'surigao-del-sur', 'city'],
            ['Barobo',              'barobo',               'surigao-del-sur', 'municipality'],
            ['Bayabas',             'bayabas',              'surigao-del-sur', 'municipality'],
            ['Cagwait',             'cagwait',              'surigao-del-sur', 'municipality'],
            ['Cantilan',            'cantilan',             'surigao-del-sur', 'municipality'],
            ['Carmen',              'carmen-surigao-sur',   'surigao-del-sur', 'municipality'],
            ['Carrascal',           'carrascal',            'surigao-del-sur', 'municipality'],
            ['Cortes',              'cortes-surigao-sur',   'surigao-del-sur', 'municipality'],
            ['Hinatuan',            'hinatuan',             'surigao-del-sur', 'municipality'],
            ['Lanuza',              'lanuza',               'surigao-del-sur', 'municipality'],
            ['Lianga',              'lianga',               'surigao-del-sur', 'municipality'],
            ['Lingig',              'lingig',               'surigao-del-sur', 'municipality'],
            ['Madrid',              'madrid',               'surigao-del-sur', 'municipality'],
            ['Marihatag',           'marihatag',            'surigao-del-sur', 'municipality'],
            ['San Agustin',         'san-agustin-surigao',  'surigao-del-sur', 'municipality'],
            ['San Miguel',          'san-miguel-surigao',   'surigao-del-sur', 'municipality'],
            ['Tagbina',             'tagbina',              'surigao-del-sur', 'municipality'],
            ['Tago',                'tago',                 'surigao-del-sur', 'municipality'],

            // ===================================================================
            // DINAGAT ISLANDS (slug: dinagat-islands, id: 39) — ALL
            // ===================================================================
            ['San Jose',            'san-jose-dinagat',     'dinagat-islands', 'municipality'],
            ['Basilisa',            'basilisa',             'dinagat-islands', 'municipality'],
            ['Cagdianao',           'cagdianao',            'dinagat-islands', 'municipality'],
            ['Dinagat',             'dinagat-town',         'dinagat-islands', 'municipality'],
            ['Libjo',               'libjo',                'dinagat-islands', 'municipality'],
            ['Loreto',              'loreto-dinagat',       'dinagat-islands', 'municipality'],
            ['Tubajon',             'tubajon',              'dinagat-islands', 'municipality'],

            // ===================================================================
            // MAGUINDANAO (slug: maguindanao, id: 40) — ALL
            // ===================================================================
            ['Shariff Aguak',       'shariff-aguak',        'maguindanao', 'municipality'],
            ['Ampatuan',            'ampatuan',             'maguindanao', 'municipality'],
            ['Barira',              'barira',               'maguindanao', 'municipality'],
            ['Buldon',              'buldon',               'maguindanao', 'municipality'],
            ['Buluan',              'buluan',               'maguindanao', 'municipality'],
            ['Datu Abdullah Sangki','datu-abdullah-sangki', 'maguindanao', 'municipality'],
            ['Datu Anggal Midtimbang','datu-anggal-midtimbang','maguindanao', 'municipality'],
            ['Datu Blah T. Sinsuat','datu-blah-t-sinsuat',  'maguindanao', 'municipality'],
            ['Datu Hoffer Ampatuan','datu-hoffer-ampatuan', 'maguindanao', 'municipality'],
            ['Datu Montawal',       'datu-montawal',        'maguindanao', 'municipality'],
            ['Datu Odin Sinsuat',   'datu-odin-sinsuat',    'maguindanao', 'municipality'],
            ['Datu Paglas',         'datu-paglas',          'maguindanao', 'municipality'],
            ['Datu Piang',          'datu-piang',           'maguindanao', 'municipality'],
            ['Datu Salibo',         'datu-salibo',          'maguindanao', 'municipality'],
            ['Datu Saudi Ampatuan', 'datu-saudi-ampatuan',  'maguindanao', 'municipality'],
            ['Datu Unsay',          'datu-unsay',           'maguindanao', 'municipality'],
            ['Gen. S. K. Pendatun','general-salipada-pendatun','maguindanao', 'municipality'],
            ['Guindulungan',        'guindulungan',         'maguindanao', 'municipality'],
            ['Kabuntalan',          'kabuntalan',           'maguindanao', 'municipality'],
            ['Mamasapano',          'mamasapano',           'maguindanao', 'municipality'],
            ['Mangudadatu',         'mangudadatu',          'maguindanao', 'municipality'],
            ['Matanog',             'matanog',              'maguindanao', 'municipality'],
            ['Northern Kabuntalan', 'northern-kabuntalan',  'maguindanao', 'municipality'],
            ['Pagagawan',           'pagagawan',            'maguindanao', 'municipality'],
            ['Pagalungan',          'pagalungan',           'maguindanao', 'municipality'],
            ['Paglat',              'paglat',               'maguindanao', 'municipality'],
            ['Pandag',              'pandag',               'maguindanao', 'municipality'],
            ['Parang',              'parang-maguindanao',   'maguindanao', 'municipality'],
            ['Rajah Buayan',        'rajah-buayan',         'maguindanao', 'municipality'],
            ['South Upi',           'south-upi',            'maguindanao', 'municipality'],
            ['Sultan Kudarat',      'sultan-kudarat-mag',   'maguindanao', 'municipality'],
            ['Sultan Mastura',      'sultan-mastura',       'maguindanao', 'municipality'],
            ['Sultan sa Barongis',  'sultan-sa-barongis',   'maguindanao', 'municipality'],
            ['Talayan',             'talayan',              'maguindanao', 'municipality'],
            ['Talitay',             'talitay',              'maguindanao', 'municipality'],
            ['Upi',                 'upi-maguindanao',      'maguindanao', 'municipality'],

            // ===================================================================
            // LANAO DEL SUR (slug: lanao-del-sur, id: 41) — ALL
            // ===================================================================
            ['Marawi City',         'marawi-city',          'lanao-del-sur', 'city'],
            ['Bacolod-Kalawi',      'bacolod-kalawi',       'lanao-del-sur', 'municipality'],
            ['Balabagan',           'balabagan',            'lanao-del-sur', 'municipality'],
            ['Balindong',           'balindong',            'lanao-del-sur', 'municipality'],
            ['Bayang',              'bayang',               'lanao-del-sur', 'municipality'],
            ['Binidayan',           'binidayan',            'lanao-del-sur', 'municipality'],
            ['Buadiposo-Buntong',   'buadiposo-buntong',    'lanao-del-sur', 'municipality'],
            ['Bubong',              'bubong',               'lanao-del-sur', 'municipality'],
            ['Butig',               'butig',                'lanao-del-sur', 'municipality'],
            ['Calanogas',           'calanogas',            'lanao-del-sur', 'municipality'],
            ['Ditsaan-Ramain',      'ditsaan-ramain',       'lanao-del-sur', 'municipality'],
            ['Ganassi',             'ganassi',              'lanao-del-sur', 'municipality'],
            ['Kapai',               'kapai',                'lanao-del-sur', 'municipality'],
            ['Kapatagan',           'kapatagan-lds',        'lanao-del-sur', 'municipality'],
            ['Lumba-Bayabao',       'lumba-bayabao',        'lanao-del-sur', 'municipality'],
            ['Lumbaca-Unayan',      'lumbaca-unayan',       'lanao-del-sur', 'municipality'],
            ['Lumbatan',            'lumbatan',             'lanao-del-sur', 'municipality'],
            ['Lumbayanague',        'lumbayanague',         'lanao-del-sur', 'municipality'],
            ['Madalum',             'madalum',              'lanao-del-sur', 'municipality'],
            ['Madamba',             'madamba',              'lanao-del-sur', 'municipality'],
            ['Maguing',             'maguing',              'lanao-del-sur', 'municipality'],
            ['Malabang',            'malabang',             'lanao-del-sur', 'municipality'],
            ['Marantao',            'marantao',             'lanao-del-sur', 'municipality'],
            ['Marogong',            'marogong',             'lanao-del-sur', 'municipality'],
            ['Masiu',               'masiu',                'lanao-del-sur', 'municipality'],
            ['Mulondo',             'mulondo',              'lanao-del-sur', 'municipality'],
            ['Pagayawan',           'pagayawan',            'lanao-del-sur', 'municipality'],
            ['Piagapo',             'piagapo',              'lanao-del-sur', 'municipality'],
            ['Picong',              'picong',               'lanao-del-sur', 'municipality'],
            ['Poona Bayabao',       'poona-bayabao',        'lanao-del-sur', 'municipality'],
            ['Pualas',              'pualas',               'lanao-del-sur', 'municipality'],
            ['Saguiaran',           'saguiaran',            'lanao-del-sur', 'municipality'],
            ['Sultan Dumalondong',  'sultan-dumalondong',   'lanao-del-sur', 'municipality'],
            ['Tagoloan II',         'tagoloan-ii',          'lanao-del-sur', 'municipality'],
            ['Tamparan',            'tamparan',             'lanao-del-sur', 'municipality'],
            ['Taraka',              'taraka',               'lanao-del-sur', 'municipality'],
            ['Tubaran',             'tubaran',              'lanao-del-sur', 'municipality'],
            ['Tugaya',              'tugaya',               'lanao-del-sur', 'municipality'],
            ['Wao',                 'wao',                  'lanao-del-sur', 'municipality'],

            // ===================================================================
            // BASILAN (slug: basilan, id: 42) — ALL
            // ===================================================================
            ['Lamitan City',        'lamitan-city',         'basilan', 'city'],
            ['Akbar',               'akbar',                'basilan', 'municipality'],
            ['Al-Barka',            'al-barka',             'basilan', 'municipality'],
            ['Hadji Mohammad Ajul', 'hadji-mohammad-ajul',  'basilan', 'municipality'],
            ['Hadji Muhtamad',      'hadji-muhtamad',       'basilan', 'municipality'],
            ['Lantawan',            'lantawan',             'basilan', 'municipality'],
            ['Maluso',              'maluso',               'basilan', 'municipality'],
            ['Sumisip',             'sumisip',              'basilan', 'municipality'],
            ['Tabuan-Lasa',         'tabuan-lasa',          'basilan', 'municipality'],
            ['Tipo-Tipo',           'tipo-tipo',            'basilan', 'municipality'],
            ['Tuburan',             'tuburan-basilan',      'basilan', 'municipality'],
            ['Ungkaya Pukan',       'ungkaya-pukan',        'basilan', 'municipality'],

            // ===================================================================
            // SULU (slug: sulu, id: 43) — ALL
            // ===================================================================
            ['Jolo',                'jolo',                 'sulu', 'municipality'],
            ['Hadji Panglima Tahil','hadji-panglima-tahil', 'sulu', 'municipality'],
            ['Indanan',             'indanan',              'sulu', 'municipality'],
            ['Kalingalan Caluang',  'kalingalan-caluang',   'sulu', 'municipality'],
            ['Lugus',               'lugus',                'sulu', 'municipality'],
            ['Luuk',                'luuk',                 'sulu', 'municipality'],
            ['Maimbung',            'maimbung',             'sulu', 'municipality'],
            ['Old Panamao',         'old-panamao',          'sulu', 'municipality'],
            ['Omar',                'omar',                 'sulu', 'municipality'],
            ['Pandami',             'pandami',              'sulu', 'municipality'],
            ['Panglima Estino',     'panglima-estino',      'sulu', 'municipality'],
            ['Pangutaran',          'pangutaran',           'sulu', 'municipality'],
            ['Parang',              'parang-sulu',          'sulu', 'municipality'],
            ['Pata',                'pata',                 'sulu', 'municipality'],
            ['Patikul',             'patikul',              'sulu', 'municipality'],
            ['Siasi',               'siasi',                'sulu', 'municipality'],
            ['Talipao',             'talipao',              'sulu', 'municipality'],
            ['Tapul',               'tapul',                'sulu', 'municipality'],
            ['Tongkil',             'tongkil',              'sulu', 'municipality'],

            // ===================================================================
            // TAWI-TAWI (slug: tawi-tawi, id: 44) — ALL
            // ===================================================================
            ['Bongao',              'bongao',               'tawi-tawi', 'municipality'],
            ['Languyan',            'languyan',             'tawi-tawi', 'municipality'],
            ['Mapun',               'mapun',                'tawi-tawi', 'municipality'],
            ['Panglima Sugala',     'panglima-sugala',      'tawi-tawi', 'municipality'],
            ['Sapa-Sapa',           'sapa-sapa',            'tawi-tawi', 'municipality'],
            ['Sibutu',              'sibutu',               'tawi-tawi', 'municipality'],
            ['Simunul',             'simunul',              'tawi-tawi', 'municipality'],
            ['Sitangkai',           'sitangkai',            'tawi-tawi', 'municipality'],
            ['South Ubian',         'south-ubian',          'tawi-tawi', 'municipality'],
            ['Tandubas',            'tandubas',             'tawi-tawi', 'municipality'],
            ['Turtle Islands',      'turtle-islands',       'tawi-tawi', 'municipality'],
        ];

        // Insert cities/municipalities — no hardcoded IDs, match on slug
        foreach ($locations as $loc) {
            [$name, $slug, $parentSlug, $type] = $loc;
            $parentId = $pid($parentSlug);

            if ($parentId === null) {
                echo "WARNING: Province not found for slug '{$parentSlug}' (city: {$name})\n";
                continue;
            }

            $regionId = null;
            foreach ($provinces as $p) {
                if (Str::slug($p['name']) === $parentSlug) {
                    $regionId = $p['region_id'];
                    break;
                }
            }

            DB::table('cities')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name'      => $name,
                    'slug'      => $slug,
                    'type'      => $type,
                    'region_id' => $regionId,
                    'parent_id' => $parentId,
                    'is_active' => true,
                ]
            );
        }
    }
}
