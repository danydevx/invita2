<?php

namespace Modules\Locations\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MexicoLocationsSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            'AGS' => ['name' => 'Aguascalientes', 'lat' => 21.8818, 'lng' => -102.2916],
            'BC' => ['name' => 'Baja California', 'lat' => 30.8406, 'lng' => -115.2908],
            'BCS' => ['name' => 'Baja California Sur', 'lat' => 24.0726, 'lng' => -110.3947],
            'CAMP' => ['name' => 'Campeche', 'lat' => 19.8301, 'lng' => -90.5347],
            'COAH' => ['name' => 'Coahuila', 'lat' => 27.0587, 'lng' => -101.7068],
            'COL' => ['name' => 'Colima', 'lat' => 19.1133, 'lng' => -103.8704],
            'CHIS' => ['name' => 'Chiapas', 'lat' => 16.7529, 'lng' => -92.6374],
            'CHIH' => ['name' => 'Chihuahua', 'lat' => 28.6329, 'lng' => -106.0691],
            'CDMX' => ['name' => 'Ciudad de México', 'lat' => 19.4326, 'lng' => -99.1332],
            'DGO' => ['name' => 'Durango', 'lat' => 24.0277, 'lng' => -104.6532],
            'GTO' => ['name' => 'Guanajuato', 'lat' => 21.0190, 'lng' => -101.2574],
            'GRO' => ['name' => 'Guerrero', 'lat' => 17.4392, 'lng' => -99.5451],
            'HGO' => ['name' => 'Hidalgo', 'lat' => 20.0911, 'lng' => -98.7344],
            'JAL' => ['name' => 'Jalisco', 'lat' => 20.6595, 'lng' => -103.3496],
            'MEX' => ['name' => 'Estado de México', 'lat' => 19.4969, 'lng' => -99.1423],
            'MICH' => ['name' => 'Michoacán', 'lat' => 19.1856, 'lng' => -101.2774],
            'MOR' => ['name' => 'Morelos', 'lat' => 18.6813, 'lng' => -99.1013],
            'NAY' => ['name' => 'Nayarit', 'lat' => 21.7511, 'lng' => -104.8454],
            'NL' => ['name' => 'Nuevo León', 'lat' => 25.6721, 'lng' => -99.9856],
            'OAX' => ['name' => 'Oaxaca', 'lat' => 17.0732, 'lng' => -96.7266],
            'PUE' => ['name' => 'Puebla', 'lat' => 19.0414, 'lng' => -98.2063],
            'QRO' => ['name' => 'Querétaro', 'lat' => 20.5888, 'lng' => -100.3899],
            'QROO' => ['name' => 'Quintana Roo', 'lat' => 18.8987, 'lng' => -88.0036],
            'SLP' => ['name' => 'San Luis Potosí', 'lat' => 22.1562, 'lng' => -100.9855],
            'SIN' => ['name' => 'Sinaloa', 'lat' => 24.7426, 'lng' => -107.6058],
            'SON' => ['name' => 'Sonora', 'lat' => 29.2972, 'lng' => -110.3306],
            'TAB' => ['name' => 'Tabasco', 'lat' => 17.8409, 'lng' => -92.6189],
            'TAMS' => ['name' => 'Tamaulipas', 'lat' => 24.2669, 'lng' => -98.7833],
            'TLAX' => ['name' => 'Tlaxcala', 'lat' => 19.3179, 'lng' => -98.4005],
            'VER' => ['name' => 'Veracruz', 'lat' => 19.1738, 'lng' => -96.1342],
            'YUC' => ['name' => 'Yucatán', 'lat' => 20.7099, 'lng' => -89.0924],
            'ZAC' => ['name' => 'Zacatecas', 'lat' => 22.7709, 'lng' => -102.5063],
        ];

        foreach ($states as $code => $data) {
            DB::table('mx_states')->updateOrInsert(
                ['code' => $code],
                ['name' => $data['name'], 'lat' => $data['lat'], 'lng' => $data['lng']]
            );
        }

        $municipalities = [
            'CDMX' => [
                'ZGZ' => ['name' => 'Álvaro Obregón', 'lat' => 19.3587, 'lng' => -99.2034],
                'AZC' => ['name' => 'Azcapotzalco', 'lat' => 19.4869, 'lng' => -99.1859],
                'BJG' => ['name' => 'Benito Juárez', 'lat' => 19.3679, 'lng' => -99.1604],
                'CUAJ' => ['name' => 'Cuajimalpa de Morelos', 'lat' => 19.3401, 'lng' => -99.2920],
                'COYO' => ['name' => 'Coyoacán', 'lat' => 19.3467, 'lng' => -99.1617],
                'CUAU' => ['name' => 'Cuauhtémoc', 'lat' => 19.4350, 'lng' => -99.1412],
                'GAM' => ['name' => 'Gustavo A. Madero', 'lat' => 19.4919, 'lng' => -99.1134],
                'IZC' => ['name' => 'Iztacalco', 'lat' => 19.3959, 'lng' => -99.0955],
                'IZP' => ['name' => 'Iztapalapa', 'lat' => 19.3556, 'lng' => -99.0622],
                'MCA' => ['name' => 'La Magdalena Contreras', 'lat' => 19.3196, 'lng' => -99.2344],
                'MIL' => ['name' => 'Miguel Hidalgo', 'lat' => 19.4271, 'lng' => -99.1909],
                'TLA' => ['name' => 'Tláhuac', 'lat' => 19.2846, 'lng' => -98.9549],
                'XOC' => ['name' => 'Xochimilco', 'lat' => 19.2592, 'lng' => -99.1024],
            ],
            'MEX' => [
                'ATZAP' => ['name' => 'Atizapán de Zaragoza', 'lat' => 19.5812, 'lng' => -99.2627],
                'CHIC' => ['name' => 'Chicoloapan', 'lat' => 19.4173, 'lng' => -98.9128],
                'ECATE' => ['name' => 'Ecatepec de Morelos', 'lat' => 19.6019, 'lng' => -99.0560],
                'HUX' => ['name' => 'Huixquilucan', 'lat' => 19.3645, 'lng' => -99.2529],
                'NAUC' => ['name' => 'Naucalpan de Juárez', 'lat' => 19.4785, 'lng' => -99.2366],
                'NEZAH' => ['name' => 'Nezahualcóyotl', 'lat' => 19.4006, 'lng' => -98.9884],
                'NL' => ['name' => 'Nicolás Romero', 'lat' => 19.6394, 'lng' => -99.3112],
                'TEXC' => ['name' => 'Texcoco', 'lat' => 19.5172, 'lng' => -98.8828],
                'TLALN' => ['name' => 'Tlalnepantla de Baz', 'lat' => 19.5405, 'lng' => -99.1955],
                'TUL' => ['name' => 'Tultitlán', 'lat' => 19.6432, 'lng' => -99.1710],
                'VALLE' => ['name' => 'Valle de Chalco', 'lat' => 19.2956, 'lng' => -98.9388],
            ],
            'JAL' => [
                'ZGDL' => ['name' => 'Guadalajara', 'lat' => 20.6597, 'lng' => -103.3496],
                'ZAPOP' => ['name' => 'Zapopan', 'lat' => 20.7169, 'lng' => -103.3996],
                'TON' => ['name' => 'Tonalá', 'lat' => 20.6244, 'lng' => -103.2314],
                'TLAJ' => ['name' => 'Tlaquepaque', 'lat' => 20.6405, 'lng' => -103.3118],
                'PUERTO' => ['name' => 'Puerto Vallarta', 'lat' => 20.6534, 'lng' => -105.2253],
                'Tepat' => ['name' => 'Tepatitlán de Morelos', 'lat' => 20.8176, 'lng' => -102.7596],
                'LAGO' => ['name' => 'Lagos de Moreno', 'lat' => 21.3560, 'lng' => -101.9265],
            ],
            'NL' => [
                'MTY' => ['name' => 'Monterrey', 'lat' => 25.6721, 'lng' => -99.9856],
                'GUAD' => ['name' => 'Guadalupe', 'lat' => 25.6786, 'lng' => -100.2590],
                'SN' => ['name' => 'San Nicolás de los Garza', 'lat' => 25.7417, 'lng' => -100.3022],
                'GEA' => ['name' => 'General Escobedo', 'lat' => 25.7897, 'lng' => -100.3308],
                'SNPedro' => ['name' => 'San Pedro Garza García', 'lat' => 25.6528, 'lng' => -100.4028],
                'STA' => ['name' => 'Santa Catarina', 'lat' => 25.6733, 'lng' => -100.4583],
                'APOD' => ['name' => 'Apodaca', 'lat' => 25.7800, 'lng' => -100.1844],
            ],
            'PUE' => [
                'PUEBLA' => ['name' => 'Puebla', 'lat' => 19.0414, 'lng' => -98.2063],
                'CHOL' => ['name' => 'Cholula', 'lat' => 19.0633, 'lng' => -98.2992],
                'CUET' => ['name' => 'Cuautlancingo', 'lat' => 19.1057, 'lng' => -98.2684],
                'AMOZ' => ['name' => 'Amozoc', 'lat' => 19.0322, 'lng' => -98.0434],
            ],
            'QRO' => [
                'QRO' => ['name' => 'Querétaro', 'lat' => 20.5888, 'lng' => -100.3899],
                'AME' => ['name' => 'El Marqués', 'lat' => 20.6539, 'lng' => -100.2654],
                'COR' => ['name' => 'Corregidora', 'lat' => 20.5467, 'lng' => -100.4408],
                'CLE' => ['name' => 'Celaya', 'lat' => 20.5234, 'lng' => -100.8153],
                'IRAP' => ['name' => 'Irapuato', 'lat' => 20.6767, 'lng' => -101.3568],
            ],
            'GTO' => [
                'LEON' => ['name' => 'León', 'lat' => 21.1161, 'lng' => -101.6763],
                'IRAP' => ['name' => 'Irapuato', 'lat' => 20.6767, 'lng' => -101.3568],
                'CELA' => ['name' => 'Celaya', 'lat' => 20.5234, 'lng' => -100.8153],
                'GUANA' => ['name' => 'Guanajuato', 'lat' => 21.0190, 'lng' => -101.2574],
                'SANM' => ['name' => 'San Miguel de Allende', 'lat' => 20.9153, 'lng' => -100.7436],
            ],
            'SIN' => [
                'CUL' => ['name' => 'Culiacán', 'lat' => 24.8041, 'lng' => -107.3898],
                'MAZ' => ['name' => 'Mazatlán', 'lat' => 23.2494, 'lng' => -106.4111],
                'Ahome' => ['name' => 'Ahome', 'lat' => 25.7831, 'lng' => -108.8114],
            ],
            'VER' => [
                'JALAPA' => ['name' => 'Jalapa', 'lat' => 19.5431, 'lng' => -96.9103],
                'BOCA' => ['name' => 'Boca del Río', 'lat' => 19.1055, 'lng' => -96.1046],
                'XALAPA' => ['name' => 'Xalapa', 'lat' => 19.5431, 'lng' => -96.9103],
                'COATZ' => ['name' => 'Coatzacoalcos', 'lat' => 18.1344, 'lng' => -94.4634],
                'POZA' => ['name' => 'Poza Rica', 'lat' => 20.5273, 'lng' => -97.4544],
            ],
            'YUC' => [
                'MERIDA' => ['name' => 'Mérida', 'lat' => 20.9674, 'lng' => -89.5926],
                'Kanasín' => ['name' => 'Kanasín', 'lat' => 20.9328, 'lng' => -89.5539],
                'Umán' => ['name' => 'Umán', 'lat' => 20.8787, 'lng' => -89.7382],
                'Progreso' => ['name' => 'Progreso', 'lat' => 21.2833, 'lng' => -89.6667],
            ],
            'QROO' => [
                'CANCUN' => ['name' => 'Cancún', 'lat' => 21.1619, 'lng' => -86.8515],
                'PLAYA' => ['name' => 'Playa del Carmen', 'lat' => 20.6296, 'lng' => -87.0739],
                'COZUMEL' => ['name' => 'Cozumel', 'lat' => 20.4230, 'lng' => -86.9223],
                'TULUM' => ['name' => 'Tulum', 'lat' => 20.2114, 'lng' => -87.4654],
            ],
            'CHIH' => [
                'CHIHUAHUA' => ['name' => 'Chihuahua', 'lat' => 28.6329, 'lng' => -106.0691],
                'CDJUAREZ' => ['name' => 'Ciudad Juárez', 'lat' => 31.6904, 'lng' => -106.4245],
            ],
            'COAH' => [
                'SALTILLO' => ['name' => 'Saltillo', 'lat' => 25.4232, 'lng' => -101.0053],
                'MONCLOVA' => ['name' => 'Monclova', 'lat' => 26.9069, 'lng' => -101.4186],
                'PIEDRA' => ['name' => 'Piedras Negras', 'lat' => 28.7000, 'lng' => -100.5233],
                'TORREON' => ['name' => 'Torreón', 'lat' => 25.5428, 'lng' => -103.4068],
            ],
            'SON' => [
                'HERMOSILLO' => ['name' => 'Hermosillo', 'lat' => 29.0729, 'lng' => -110.3306],
                'CABORCA' => ['name' => 'Caborca', 'lat' => 30.3167, 'lng' => -112.1500],
                'NOGALES' => ['name' => 'Nogales', 'lat' => 31.3089, 'lng' => -110.9422],
                'GUAYMAS' => ['name' => 'Guaymas', 'lat' => 27.9333, 'lng' => -110.9000],
            ],
            'TAMS' => [
                'REYNOSA' => ['name' => 'Reynosa', 'lat' => 26.0806, 'lng' => -98.2881],
                'MATAMOROS' => ['name' => 'Matamoros', 'lat' => 25.8718, 'lng' => -97.5027],
                'NUEVOLAREDO' => ['name' => 'Nuevo Laredo', 'lat' => 27.4764, 'lng' => -99.4956],
                'TAMPICO' => ['name' => 'Tampico', 'lat' => 22.2032, 'lng' => -97.8617],
            ],
            'HGO' => [
                'PACHUCA' => ['name' => 'Pachuca', 'lat' => 20.0911, 'lng' => -98.7344],
                'TULANCINGO' => ['name' => 'Tulancingo', 'lat' => 20.0844, 'lng' => -98.3692],
            ],
            'MICH' => [
                'MORELIA' => ['name' => 'Morelia', 'lat' => 19.7059, 'lng' => -101.1949],
                'URUAPAN' => ['name' => 'Uruapan', 'lat' => 19.4207, 'lng' => -102.0561],
                'ZAMORA' => ['name' => 'Zamora', 'lat' => 19.9869, 'lng' => -102.2839],
            ],
            'OAX' => [
                'OAXACA' => ['name' => 'Oaxaca', 'lat' => 17.0732, 'lng' => -96.7266],
                'PUTLA' => ['name' => 'Putla Villa de Guerrero', 'lat' => 17.0269, 'lng' => -97.8797],
                'PUERTO' => ['name' => 'Puerto Escondido', 'lat' => 15.8611, 'lng' => -97.0728],
            ],
            'TAB' => [
                'VILLAHERMOSA' => ['name' => 'Villahermosa', 'lat' => 17.9892, 'lng' => -92.9475],
                'CARDENAS' => ['name' => 'Cárdenas', 'lat' => 18.0000, 'lng' => -93.3667],
            ],
            'SLP' => [
                'SLP' => ['name' => 'San Luis Potosí', 'lat' => 22.1562, 'lng' => -100.9855],
                'SOLEDAD' => ['name' => 'Soledad de Graciano Sánchez', 'lat' => 22.1900, 'lng' => -100.9353],
                'MATEH' => ['name' => 'Matehuala', 'lat' => 23.6433, 'lng' => -100.6458],
            ],
            'ZAC' => [
                'ZACATECAS' => ['name' => 'Zacatecas', 'lat' => 22.7709, 'lng' => -102.5063],
                'GUADALUPE' => ['name' => 'Guadalupe', 'lat' => 22.7500, 'lng' => -102.5167],
                'FRESN' => ['name' => 'Fresnillo', 'lat' => 23.1744, 'lng' => -102.8708],
            ],
            'AGS' => [
                'AGS' => ['name' => 'Aguascalientes', 'lat' => 21.8818, 'lng' => -102.2916],
                'CALVILLO' => ['name' => 'Calvillo', 'lat' => 21.8444, 'lng' => -102.7181],
            ],
            'BC' => [
                'TIJUANA' => ['name' => 'Tijuana', 'lat' => 32.5149, 'lng' => -117.0382],
                'MEXICALI' => ['name' => 'Mexicali', 'lat' => 32.6245, 'lng' => -115.4523],
                'ENSENADA' => ['name' => 'Ensenada', 'lat' => 31.8711, 'lng' => -116.6000],
                'PLAYAS' => ['name' => 'Playas de Rosarito', 'lat' => 32.3167, 'lng' => -117.0500],
            ],
            'BCS' => [
                'LACOM' => ['name' => 'La Paz', 'lat' => 24.1426, 'lng' => -110.3123],
                'LOSCABOS' => ['name' => 'Los Cabos', 'lat' => 23.0611, 'lng' => -109.6992],
            ],
            'CAMP' => [
                'CAMPECHE' => ['name' => 'Campeche', 'lat' => 19.8301, 'lng' => -90.5347],
                'CARMEN' => ['name' => 'Ciudad del Carmen', 'lat' => 18.6459, 'lng' => -91.8340],
            ],
            'COL' => [
                'COLIMA' => ['name' => 'Colima', 'lat' => 19.1133, 'lng' => -103.8704],
                'MANZANILLO' => ['name' => 'Manzanillo', 'lat' => 19.0797, 'lng' => -104.5594],
            ],
            'CHIS' => [
                'TUXTLA' => ['name' => 'Tuxtla Gutiérrez', 'lat' => 16.7529, 'lng' => -92.6374],
                'SCRISTOBAL' => ['name' => 'San Cristóbal de las Casas', 'lat' => 16.7372, 'lng' => -92.6376],
                'TAPACHULA' => ['name' => 'Tapachula', 'lat' => 14.9092, 'lng' => -92.2575],
            ],
            'DGO' => [
                'DURANGO' => ['name' => 'Durango', 'lat' => 24.0277, 'lng' => -104.6532],
                'GOMEZ' => ['name' => 'Gómez Palacio', 'lat' => 25.5686, 'lng' => -103.4949],
            ],
            'GRO' => [
                'CHILPANCINGO' => ['name' => 'Chilpancingo', 'lat' => 17.5511, 'lng' => -99.5007],
                'ACAPULCO' => ['name' => 'Acapulco', 'lat' => 16.8531, 'lng' => -99.8234],
                'IGUALA' => ['name' => 'Iguala', 'lat' => 18.3453, 'lng' => -99.5397],
            ],
            'MOR' => [
                'CUERNAVACA' => ['name' => 'Cuernavaca', 'lat' => 18.9242, 'lng' => -99.2210],
                'CUATLA' => ['name' => 'Cuautla', 'lat' => 18.8050, 'lng' => -98.9536],
                'JIUTEPEC' => ['name' => 'Jiutepec', 'lat' => 18.7422, 'lng' => -99.1631],
            ],
            'NAY' => [
                'TEPIC' => ['name' => 'Tepic', 'lat' => 21.5094, 'lng' => -104.8956],
                'BAHIA' => ['name' => 'Bahía de Banderas', 'lat' => 20.7833, 'lng' => -105.2667],
            ],
            'TLAX' => [
                'TLAXCALA' => ['name' => 'Tlaxcala', 'lat' => 19.3179, 'lng' => -98.4005],
                'APIZ' => ['name' => 'Apizaco', 'lat' => 19.4167, 'lng' => -98.1333],
            ],
        ];

        foreach ($municipalities as $stateCode => $municipalityData) {
            foreach ($municipalityData as $code => $data) {
                DB::table('mx_municipalities')->updateOrInsert(
                    [
                        'state_code' => $stateCode,
                        'code' => $code,
                    ],
                    [
                        'name' => $data['name'],
                        'lat' => $data['lat'],
                        'lng' => $data['lng'],
                        'is_metropolitan' => true,
                    ]
                );
            }
        }
    }
}
