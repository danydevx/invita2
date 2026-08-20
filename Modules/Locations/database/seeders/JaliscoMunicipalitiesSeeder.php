<?php

namespace Modules\Locations\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JaliscoMunicipalitiesSeeder extends Seeder
{
    public function run(): void
    {
        $country = DB::table('countries')->where('code', 'MX')->first();
        if (!$country) {
            $this->command->error('Mexico country not found. Run migration first.');
            return;
        }

        $state = DB::table('states')->where('country_id', $country->id)->where('code', 'JAL')->first();
        if (!$state) {
            $this->command->error('Jalisco state not found. Run migration first.');
            return;
        }

        $municipalities = [
            ['code' => 'ACG', 'name' => 'Acatic', 'lat' => 20.7803, 'lng' => -102.9103, 'metro' => false],
            ['code' => 'ACJ', 'name' => 'Acatlán de Juárez', 'lat' => 20.4206, 'lng' => -103.5936, 'metro' => false],
            ['code' => 'AHM', 'name' => 'Ahualulco de Mercado', 'lat' => 20.6992, 'lng' => -103.9750, 'metro' => false],
            ['code' => 'AMA', 'name' => 'Amacueca', 'lat' => 20.0142, 'lng' => -103.5986, 'metro' => false],
            ['code' => 'AMT', 'name' => 'Amatitán', 'lat' => 20.8364, 'lng' => -103.7289, 'metro' => false],
            ['code' => 'AME', 'name' => 'Ameca', 'lat' => 20.5481, 'lng' => -104.0456, 'metro' => false],
            ['code' => 'ARA', 'name' => 'Arandas', 'lat' => 20.7061, 'lng' => -102.3456, 'metro' => false],
            ['code' => 'ATB', 'name' => 'Atemajac de Brizuela', 'lat' => 20.1417, 'lng' => -103.7153, 'metro' => false],
            ['code' => 'ATE', 'name' => 'Atengo', 'lat' => 20.3411, 'lng' => -104.3056, 'metro' => false],
            ['code' => 'ATG', 'name' => 'Atenguillo', 'lat' => 20.1197, 'lng' => -104.5122, 'metro' => false],
            ['code' => 'AEO', 'name' => 'Atotonilco el Alto', 'lat' => 20.5500, 'lng' => -102.5083, 'metro' => false],
            ['code' => 'ATY', 'name' => 'Atoyac', 'lat' => 20.0103, 'lng' => -103.5156, 'metro' => false],
            ['code' => 'AUN', 'name' => 'Autlán de Navarro', 'lat' => 19.7719, 'lng' => -104.3644, 'metro' => false],
            ['code' => 'AYL', 'name' => 'Ayotlán', 'lat' => 20.5289, 'lng' => -102.3244, 'metro' => false],
            ['code' => 'AYU', 'name' => 'Ayutla', 'lat' => 20.1264, 'lng' => -104.4750, 'metro' => false],
            ['code' => 'BOL', 'name' => 'Bolaños', 'lat' => 21.8311, 'lng' => -103.7806, 'metro' => false],
            ['code' => 'CAC', 'name' => 'Cabo Corrientes', 'lat' => 20.3114, 'lng' => -105.4114, 'metro' => false],
            ['code' => 'CNO', 'name' => 'Cañadas de Obregón', 'lat' => 21.1644, 'lng' => -102.6961, 'metro' => false],
            ['code' => 'CAS', 'name' => 'Casimiro Castillo', 'lat' => 19.6050, 'lng' => -104.4361, 'metro' => false],
            ['code' => 'CHA', 'name' => 'Chapala', 'lat' => 20.2906, 'lng' => -103.1908, 'metro' => false],
            ['code' => 'CHI', 'name' => 'Chimaltitán', 'lat' => 21.7333, 'lng' => -103.7667, 'metro' => false],
            ['code' => 'CHQ', 'name' => 'Chiquilistlán', 'lat' => 20.0881, 'lng' => -103.8653, 'metro' => false],
            ['code' => 'CIH', 'name' => 'Cihuatlán', 'lat' => 19.2389, 'lng' => -104.5683, 'metro' => false],
            ['code' => 'COC', 'name' => 'Cocula', 'lat' => 20.3664, 'lng' => -103.8219, 'metro' => false],
            ['code' => 'COL', 'name' => 'Colotlán', 'lat' => 22.1158, 'lng' => -103.2644, 'metro' => false],
            ['code' => 'CBO', 'name' => 'Concepción de Buenos Aires', 'lat' => 19.9772, 'lng' => -103.2606, 'metro' => false],
            ['code' => 'CGB', 'name' => 'Cuautitlán de García Barragán', 'lat' => 19.4503, 'lng' => -104.3989, 'metro' => false],
            ['code' => 'CUA', 'name' => 'Cuautla', 'lat' => 20.2039, 'lng' => -104.4042, 'metro' => false],
            ['code' => 'CUQ', 'name' => 'Cuquío', 'lat' => 20.9256, 'lng' => -103.0236, 'metro' => false],
            ['code' => 'DEG', 'name' => 'Degollado', 'lat' => 20.4467, 'lng' => -102.1333, 'metro' => false],
            ['code' => 'EJU', 'name' => 'Ejutla', 'lat' => 19.8803, 'lng' => -104.1350, 'metro' => false],
            ['code' => 'EAR', 'name' => 'El Arenal', 'lat' => 20.7758, 'lng' => -103.6931, 'metro' => false],
            ['code' => 'EGR', 'name' => 'El Grullo', 'lat' => 19.8114, 'lng' => -104.2189, 'metro' => false],
            ['code' => 'ELI', 'name' => 'El Limón', 'lat' => 19.8222, 'lng' => -104.1408, 'metro' => false],
            ['code' => 'ESA', 'name' => 'El Salto', 'lat' => 20.5186, 'lng' => -103.1817, 'metro' => true],
            ['code' => 'EDI', 'name' => 'Encarnación de Díaz', 'lat' => 21.5267, 'lng' => -102.2411, 'metro' => false],
            ['code' => 'ETZ', 'name' => 'Etzatlán', 'lat' => 20.7633, 'lng' => -104.0833, 'metro' => false],
            ['code' => 'GFA', 'name' => 'Gómez Farías', 'lat' => 19.7919, 'lng' => -103.4842, 'metro' => false],
            ['code' => 'GUA', 'name' => 'Guachinango', 'lat' => 20.6500, 'lng' => -104.3833, 'metro' => false],
            ['code' => 'GDL', 'name' => 'Guadalajara', 'lat' => 20.6767, 'lng' => -103.3475, 'metro' => true],
            ['code' => 'HOS', 'name' => 'Hostotipaquillo', 'lat' => 21.0608, 'lng' => -104.0578, 'metro' => false],
            ['code' => 'HUE', 'name' => 'Huejúcar', 'lat' => 22.3411, 'lng' => -103.2114, 'metro' => false],
            ['code' => 'HJQ', 'name' => 'Huejuquilla el Alto', 'lat' => 22.6022, 'lng' => -103.8708, 'metro' => false],
            ['code' => 'IXM', 'name' => 'Ixtlahuacán de los Membrillos', 'lat' => 20.3556, 'lng' => -103.1917, 'metro' => false],
            ['code' => 'IXR', 'name' => 'Ixtlahuacán del Río', 'lat' => 20.8697, 'lng' => -103.2433, 'metro' => false],
            ['code' => 'JAL', 'name' => 'Jalostotitlán', 'lat' => 21.1639, 'lng' => -102.4636, 'metro' => false],
            ['code' => 'JAM', 'name' => 'Jamay', 'lat' => 20.2889, 'lng' => -102.7103, 'metro' => false],
            ['code' => 'JES', 'name' => 'Jesús María', 'lat' => 20.6397, 'lng' => -102.1053, 'metro' => false],
            ['code' => 'JIL', 'name' => 'Jilotlán de los Dolores', 'lat' => 19.3564, 'lng' => -102.8631, 'metro' => false],
            ['code' => 'JOC', 'name' => 'Jocotepec', 'lat' => 20.2839, 'lng' => -103.4283, 'metro' => false],
            ['code' => 'JUA', 'name' => 'Juanacatlán', 'lat' => 20.5103, 'lng' => -103.1481, 'metro' => false],
            ['code' => 'JUC', 'name' => 'Juchitlán', 'lat' => 20.0833, 'lng' => -104.1000, 'metro' => false],
            ['code' => 'LBA', 'name' => 'La Barca', 'lat' => 20.2903, 'lng' => -102.5456, 'metro' => false],
            ['code' => 'LHU', 'name' => 'La Huerta', 'lat' => 19.4811, 'lng' => -104.6458, 'metro' => false],
            ['code' => 'LMP', 'name' => 'La Manzanilla de la Paz', 'lat' => 20.0003, 'lng' => -103.1342, 'metro' => false],
            ['code' => 'LAG', 'name' => 'Lagos de Moreno', 'lat' => 21.3531, 'lng' => -101.9314, 'metro' => true],
            ['code' => 'MAG', 'name' => 'Magdalena', 'lat' => 20.9133, 'lng' => -103.9850, 'metro' => false],
            ['code' => 'MAS', 'name' => 'Mascota', 'lat' => 20.5253, 'lng' => -104.7889, 'metro' => false],
            ['code' => 'MAZ', 'name' => 'Mazamitla', 'lat' => 19.9164, 'lng' => -103.0194, 'metro' => false],
            ['code' => 'MEX', 'name' => 'Mexticacán', 'lat' => 21.2611, 'lng' => -102.8114, 'metro' => false],
            ['code' => 'MEZ', 'name' => 'Mezquitic', 'lat' => 22.3789, 'lng' => -103.6961, 'metro' => false],
            ['code' => 'MIX', 'name' => 'Mixtlán', 'lat' => 20.5019, 'lng' => -104.2858, 'metro' => false],
            ['code' => 'OCOT', 'name' => 'Ocotlán', 'lat' => 20.3500, 'lng' => -102.7667, 'metro' => false],
            ['code' => 'OJU', 'name' => 'Ojuelos de Jalisco', 'lat' => 21.8683, 'lng' => -101.5906, 'metro' => false],
            ['code' => 'PIH', 'name' => 'Pihuamo', 'lat' => 19.2514, 'lng' => -103.3831, 'metro' => false],
            ['code' => 'PON', 'name' => 'Poncitlán', 'lat' => 20.3800, 'lng' => -102.9242, 'metro' => false],
            ['code' => 'PVR', 'name' => 'Puerto Vallarta', 'lat' => 20.6534, 'lng' => -105.2253, 'metro' => true],
            ['code' => 'QUI', 'name' => 'Quitupan', 'lat' => 19.8272, 'lng' => -102.8942, 'metro' => false],
            ['code' => 'SCL', 'name' => 'San Cristóbal de la Barranca', 'lat' => 21.0306, 'lng' => -103.4319, 'metro' => false],
            ['code' => 'SDA', 'name' => 'San Diego de Alejandría', 'lat' => 21.0000, 'lng' => -101.9833, 'metro' => false],
            ['code' => 'SGA', 'name' => 'San Gabriel', 'lat' => 19.7314, 'lng' => -103.7667, 'metro' => false],
            ['code' => 'SIC', 'name' => 'San Ignacio Cerro Gordo', 'lat' => 20.7411, 'lng' => -102.5281, 'metro' => false],
            ['code' => 'SJL', 'name' => 'San Juan de los Lagos', 'lat' => 21.2444, 'lng' => -102.3333, 'metro' => false],
            ['code' => 'SJE', 'name' => 'San Juanito de Escobedo', 'lat' => 20.7936, 'lng' => -103.9575, 'metro' => false],
            ['code' => 'SJU', 'name' => 'San Julián', 'lat' => 21.0117, 'lng' => -102.1794, 'metro' => false],
            ['code' => 'SMC', 'name' => 'San Marcos', 'lat' => 20.7833, 'lng' => -104.2000, 'metro' => false],
            ['code' => 'SMB', 'name' => 'San Martín de Bolaños', 'lat' => 21.6811, 'lng' => -103.8164, 'metro' => false],
            ['code' => 'SMH', 'name' => 'San Martín Hidalgo', 'lat' => 20.4325, 'lng' => -103.9292, 'metro' => false],
            ['code' => 'SMA', 'name' => 'San Miguel el Alto', 'lat' => 21.0306, 'lng' => -102.4042, 'metro' => false],
            ['code' => 'SSO', 'name' => 'San Sebastián del Oeste', 'lat' => 20.7589, 'lng' => -104.8519, 'metro' => false],
            ['code' => 'SLA', 'name' => 'Santa María de los Ángeles', 'lat' => 22.1644, 'lng' => -103.2661, 'metro' => false],
            ['code' => 'SMO', 'name' => 'Santa María del Oro', 'lat' => 19.5639, 'lng' => -102.7136, 'metro' => false],
            ['code' => 'SAY', 'name' => 'Sayula', 'lat' => 19.8817, 'lng' => -103.5936, 'metro' => false],
            ['code' => 'TLA', 'name' => 'Tala', 'lat' => 20.6522, 'lng' => -103.7011, 'metro' => false],
            ['code' => 'TPA', 'name' => 'Talpa de Allende', 'lat' => 20.3808, 'lng' => -104.8219, 'metro' => false],
            ['code' => 'TMG', 'name' => 'Tamazula de Gordiano', 'lat' => 19.6319, 'lng' => -103.2506, 'metro' => false],
            ['code' => 'TAP', 'name' => 'Tapalpa', 'lat' => 19.9467, 'lng' => -103.7589, 'metro' => false],
            ['code' => 'TEC', 'name' => 'Tecalitlán', 'lat' => 19.4689, 'lng' => -103.3022, 'metro' => false],
            ['code' => 'TMG', 'name' => 'Techaluta de Montenegro', 'lat' => 20.0750, 'lng' => -103.5414, 'metro' => false],
            ['code' => 'TEO', 'name' => 'Tecolotlán', 'lat' => 20.2036, 'lng' => -104.0478, 'metro' => false],
            ['code' => 'TNA', 'name' => 'Tenamaxtlán', 'lat' => 20.2178, 'lng' => -104.1647, 'metro' => false],
            ['code' => 'TEP', 'name' => 'Teocaltiche', 'lat' => 21.4300, 'lng' => -102.5739, 'metro' => false],
            ['code' => 'TOC', 'name' => 'Teocuitatlán de Corona', 'lat' => 20.1031, 'lng' => -103.5261, 'metro' => false],
            ['code' => 'TPE', 'name' => 'Tepatitlán de Morelos', 'lat' => 20.8164, 'lng' => -102.7606, 'metro' => true],
            ['code' => 'TEQ', 'name' => 'Tequila', 'lat' => 20.8847, 'lng' => -103.8347, 'metro' => false],
            ['code' => 'TEU', 'name' => 'Teuchitlán', 'lat' => 20.6842, 'lng' => -103.8469, 'metro' => false],
            ['code' => 'TZA', 'name' => 'Tizapán el Alto', 'lat' => 20.1264, 'lng' => -103.0722, 'metro' => false],
            ['code' => 'TLJ', 'name' => 'Tlajomulco de Zúñiga', 'lat' => 20.4739, 'lng' => -103.4464, 'metro' => true],
            ['code' => 'TLQ', 'name' => 'Tlaquepaque', 'lat' => 20.6397, 'lng' => -103.3114, 'metro' => true],
            ['code' => 'TOL', 'name' => 'Tolimán', 'lat' => 19.5986, 'lng' => -103.9147, 'metro' => false],
            ['code' => 'TOM', 'name' => 'Tomatlán', 'lat' => 19.9367, 'lng' => -105.2506, 'metro' => false],
            ['code' => 'TON', 'name' => 'Tonalá', 'lat' => 20.6247, 'lng' => -103.2403, 'metro' => true],
            ['code' => 'TNY', 'name' => 'Tonaya', 'lat' => 19.7911, 'lng' => -103.9619, 'metro' => false],
            ['code' => 'TNL', 'name' => 'Tonila', 'lat' => 19.4325, 'lng' => -103.5133, 'metro' => false],
            ['code' => 'TTC', 'name' => 'Totatiche', 'lat' => 21.9214, 'lng' => -103.4439, 'metro' => false],
            ['code' => 'TTO', 'name' => 'Tototlán', 'lat' => 20.5422, 'lng' => -102.7933, 'metro' => false],
            ['code' => 'TUC', 'name' => 'Tuxcacuesco', 'lat' => 19.7042, 'lng' => -103.9933, 'metro' => false],
            ['code' => 'TXC', 'name' => 'Tuxcueca', 'lat' => 20.1539, 'lng' => -103.1872, 'metro' => false],
            ['code' => 'TXP', 'name' => 'Tuxpan', 'lat' => 19.5544, 'lng' => -103.5539, 'metro' => false],
            ['code' => 'USA', 'name' => 'Unión de San Antonio', 'lat' => 21.1306, 'lng' => -101.9617, 'metro' => false],
            ['code' => 'UTU', 'name' => 'Unión de Tula', 'lat' => 19.9542, 'lng' => -104.2694, 'metro' => false],
            ['code' => 'VAG', 'name' => 'Valle de Guadalupe', 'lat' => 21.0164, 'lng' => -102.6167, 'metro' => false],
            ['code' => 'VAJ', 'name' => 'Valle de Juárez', 'lat' => 19.9317, 'lng' => -102.9419, 'metro' => false],
            ['code' => 'VCR', 'name' => 'Villa Corona', 'lat' => 20.4161, 'lng' => -103.6667, 'metro' => false],
            ['code' => 'VGU', 'name' => 'Villa Guerrero', 'lat' => 21.9864, 'lng' => -103.5956, 'metro' => false],
            ['code' => 'VHI', 'name' => 'Villa Hidalgo', 'lat' => 21.6667, 'lng' => -102.6000, 'metro' => false],
            ['code' => 'VPU', 'name' => 'Villa Purificación', 'lat' => 19.7164, 'lng' => -104.6017, 'metro' => false],
            ['code' => 'YAH', 'name' => 'Yahualica de González Gallo', 'lat' => 21.1783, 'lng' => -102.8889, 'metro' => false],
            ['code' => 'ZAC', 'name' => 'Zacoalco de Torres', 'lat' => 20.2319, 'lng' => -103.5756, 'metro' => false],
            ['code' => 'ZAP', 'name' => 'Zapopan', 'lat' => 20.7222, 'lng' => -103.3917, 'metro' => true],
            ['code' => 'ZTI', 'name' => 'Zapotiltic', 'lat' => 19.6289, 'lng' => -103.4331, 'metro' => false],
            ['code' => 'ZDV', 'name' => 'Zapotitlán de Vadillo', 'lat' => 19.5469, 'lng' => -103.8117, 'metro' => false],
            ['code' => 'ZG', 'name' => 'Zapotlán el Grande', 'lat' => 19.7056, 'lng' => -103.4650, 'metro' => false],
            ['code' => 'ZAPN', 'name' => 'Zapotlanejo', 'lat' => 20.6222, 'lng' => -103.0678, 'metro' => false],
        ];

        $count = 0;
        foreach ($municipalities as $muni) {
            DB::table('municipalities')->updateOrInsert(
                [
                    'state_id' => $state->id,
                    'country_id' => $country->id,
                    'code' => $muni['code'],
                ],
                [
                    'name' => $muni['name'],
                    'lat' => $muni['lat'],
                    'lng' => $muni['lng'],
                    'is_metropolitan' => $muni['metro'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $count++;
        }

        $this->command->info("Jalisco municipalities seeded: {$count}");
    }
}
