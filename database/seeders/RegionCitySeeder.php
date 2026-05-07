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
        // parentSlug is the province slug, resolved to real ID at runtime
        $locations = [
            // ===================================================================
            // CEBU (slug: cebu, id: 1)
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
            ['Liloan',             'liloan',               'cebu', 'municipality'],
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
            // BOHOL (slug: bohol, id: 2)
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
            // SIQUIJOR (slug: siquijor, id: 3)
            // ===================================================================
            ['Enrique Villanueva', 'enrique-villanueva',   'siquijor', 'municipality'],
            ['Larena',             'larena',               'siquijor', 'municipality'],
            ['Lazi',               'lazi',                 'siquijor', 'municipality'],
            ['Maria',              'maria-siquijor',       'siquijor', 'municipality'],
            ['San Juan',           'san-juan-siquijor',    'siquijor', 'municipality'],
            ['Siquijor (Poblacion)', 'siquijor-poblacion','siquijor', 'municipality'],

            // ===================================================================
            // NEGROS ORIENTAL (slug: negros-oriental, id: 4)
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
            ['Basay',              'basay',                'negros-oriental', 'municipality'],
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
            // EASTERN VISAYAS PROVINCES (cities only, municipalities can be added later)
            // ===================================================================

            // LEYTE (slug: leyte)
            ['Tacloban City',      'tacloban-city',        'leyte', 'city'],
            ['Ormoc City',         'ormoc-city',           'leyte', 'city'],
            ['Baybay City',        'baybay-city',          'leyte', 'city'],

            // SOUTHERN LEYTE (slug: southern-leyte)
            ['Maasin City',        'maasin-city',          'southern-leyte', 'city'],

            // SAMAR (slug: samar)
            ['Catbalogan City',    'catbalogan-city',      'samar', 'city'],
            ['Calbayog City',      'calbayog-city',        'samar', 'city'],

            // EASTERN SAMAR (slug: eastern-samar)
            ['Borongan City',      'borongan-city',        'eastern-samar', 'city'],

            // NORTHERN SAMAR (slug: northern-samar)
            ['Catarman',           'catarman',             'northern-samar', 'municipality'],

            // BILIRAN (slug: biliran)
            ['Naval',              'naval',                'biliran', 'municipality'],

            // ===================================================================
            // WESTERN VISAYAS PROVINCES
            // ===================================================================

            // ILOILO (slug: iloilo)
            ['Iloilo City',         'iloilo-city',          'iloilo', 'city'],
            ['Passi City',          'passi-city',           'iloilo', 'city'],

            // NEGROS OCCIDENTAL (slug: negros-occidental)
            ['Bacolod City',        'bacolod-city',         'negros-occidental', 'city'],
            ['Bago City',           'bago-city',            'negros-occidental', 'city'],
            ['Cadiz City',          'cadiz-city',           'negros-occidental', 'city'],
            ['Himamaylan City',     'himamaylan-city',      'negros-occidental', 'city'],
            ['Kabankalan City',     'kabankalan-city',      'negros-occidental', 'city'],
            ['La Carlota City',     'la-carlota-city',      'negros-occidental', 'city'],
            ['Sagay City',          'sagay-city',           'negros-occidental', 'city'],
            ['San Carlos City',     'san-carlos-city-negros','negros-occidental', 'city'],
            ['Silay City',          'silay-city',           'negros-occidental', 'city'],
            ['Sipalay City',        'sipalay-city',         'negros-occidental', 'city'],
            ['Talisay City',        'talisay-city-negros',  'negros-occidental', 'city'],
            ['Victorias City',      'victorias-city',       'negros-occidental', 'city'],

            // CAPIZ (slug: capiz)
            ['Roxas City',          'roxas-city',           'capiz', 'city'],

            // AKLAN (slug: aklan)
            ['Kalibo',              'kalibo',               'aklan', 'municipality'],
            ['Boracay (Malay)',     'malay-aklan',          'aklan', 'municipality'],

            // ANTIQUE (slug: antique)
            ['San Jose de Buenavista', 'san-jose-buenavista','antique', 'municipality'],

            // GUIMARAS (slug: guimaras)
            ['Jordan',              'jordan-guimaras',      'guimaras', 'municipality'],

            // ===================================================================
            // ZAMBOANGA PENINSULA
            // ===================================================================

            // ZAMBOANGA DEL SUR (slug: zamboanga-del-sur)
            ['Zamboanga City',      'zamboanga-city',       'zamboanga-del-sur', 'city'],
            ['Pagadian City',       'pagadian-city',        'zamboanga-del-sur', 'city'],

            // ZAMBOANGA DEL NORTE (slug: zamboanga-del-norte)
            ['Dipolog City',        'dipolog-city',         'zamboanga-del-norte', 'city'],
            ['Dapitan City',        'dapitan-city',         'zamboanga-del-norte', 'city'],

            // ZAMBOANGA SIBUGAY (slug: zamboanga-sibugay)
            ['Ipil',                'ipil',                 'zamboanga-sibugay', 'municipality'],

            // ===================================================================
            // NORTHERN MINDANAO
            // ===================================================================

            // BUKIDNON (slug: bukidnon)
            ['Malaybalay City',     'malaybalay-city',      'bukidnon', 'city'],
            ['Valencia City',       'valencia-city-bukidnon','bukidnon', 'city'],

            // MISAMIS ORIENTAL (slug: misamis-oriental)
            ['Cagayan de Oro City', 'cagayan-de-oro-city',  'misamis-oriental', 'city'],
            ['Gingoog City',        'gingoog-city',         'misamis-oriental', 'city'],

            // MISAMIS OCCIDENTAL (slug: misamis-occidental)
            ['Oroquieta City',      'oroquieta-city',       'misamis-occidental', 'city'],
            ['Ozamiz City',         'ozamiz-city',          'misamis-occidental', 'city'],
            ['Tangub City',         'tangub-city',          'misamis-occidental', 'city'],

            // LANAO DEL NORTE (slug: lanao-del-norte)
            ['Iligan City',         'iligan-city',          'lanao-del-norte', 'city'],

            // CAMIGUIN (slug: camiguin)
            ['Mambajao',            'mambajao',             'camiguin', 'municipality'],

            // ===================================================================
            // DAVAO REGION
            // ===================================================================

            // DAVAO DEL SUR (slug: davao-del-sur)
            ['Davao City',          'davao-city',           'davao-del-sur', 'city'],
            ['Digos City',          'digos-city',           'davao-del-sur', 'city'],

            // DAVAO DEL NORTE (slug: davao-del-norte)
            ['Tagum City',          'tagum-city',           'davao-del-norte', 'city'],
            ['Panabo City',         'panabo-city',          'davao-del-norte', 'city'],
            ['Samal City',          'samal-city',           'davao-del-norte', 'city'],

            // DAVAO ORIENTAL (slug: davao-oriental)
            ['Mati City',           'mati-city',            'davao-oriental', 'city'],

            // DAVAO DE ORO (slug: davao-de-oro)
            ['Nabunturan',          'nabunturan',           'davao-de-oro', 'municipality'],

            // DAVAO OCCIDENTAL (slug: davao-occidental)
            ['Malita',              'malita',               'davao-occidental', 'municipality'],

            // ===================================================================
            // SOCCSKSARGEN
            // ===================================================================

            // SOUTH COTABATO (slug: south-cotabato)
            ['General Santos City', 'general-santos-city',  'south-cotabato', 'city'],
            ['Koronadal City',      'koronadal-city',       'south-cotabato', 'city'],

            // NORTH COTABATO (slug: north-cotabato)
            ['Kidapawan City',      'kidapawan-city',       'north-cotabato', 'city'],
            ['Cotabato City',       'cotabato-city',        'north-cotabato', 'city'],

            // SARANGANI (slug: sarangani)
            ['Alabel',              'alabel',               'sarangani', 'municipality'],

            // SULTAN KUDARAT (slug: sultan-kudarat)
            ['Tacurong City',       'tacurong-city',        'sultan-kudarat', 'city'],

            // ===================================================================
            // CARAGA
            // ===================================================================

            // AGUSAN DEL NORTE (slug: agusan-del-norte)
            ['Butuan City',         'butuan-city',          'agusan-del-norte', 'city'],
            ['Cabadbaran City',     'cabadbaran-city',      'agusan-del-norte', 'city'],

            // AGUSAN DEL SUR (slug: agusan-del-sur)
            ['Prosperidad',         'prosperidad',          'agusan-del-sur', 'municipality'],

            // SURIGAO DEL NORTE (slug: surigao-del-norte)
            ['Surigao City',        'surigao-city',         'surigao-del-norte', 'city'],

            // SURIGAO DEL SUR (slug: surigao-del-sur)
            ['Tandag City',         'tandag-city',          'surigao-del-sur', 'city'],
            ['Bislig City',         'bislig-city',          'surigao-del-sur', 'city'],

            // DINAGAT ISLANDS (slug: dinagat-islands)
            ['San Jose',            'san-jose-dinagat',     'dinagat-islands', 'municipality'],

            // ===================================================================
            // BARMM
            // ===================================================================

            // MAGUINDANAO (slug: maguindanao)
            ['Shariff Aguak',       'shariff-aguak',        'maguindanao', 'municipality'],

            // LANAO DEL SUR (slug: lanao-del-sur)
            ['Marawi City',         'marawi-city',          'lanao-del-sur', 'city'],

            // BASILAN (slug: basilan)
            ['Lamitan City',        'lamitan-city',         'basilan', 'city'],

            // SULU (slug: sulu)
            ['Jolo',                'jolo',                 'sulu', 'municipality'],

            // TAWI-TAWI (slug: tawi-tawi)
            ['Bongao',              'bongao',               'tawi-tawi', 'municipality'],
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
