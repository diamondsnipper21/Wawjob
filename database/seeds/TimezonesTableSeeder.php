<?php

use Illuminate\Database\Seeder;

class TimezonesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('timezones')->delete();
        DB::table('timezones')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Etc/UTC',
            'label' => 'UTC (Coordinated Universal Time)',
                'gmt_offset' => 0,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Europe/London',
            'label' => 'UTC (Coordinated Universal Time) Dublin, Edinburgh, London',
                'gmt_offset' => 0,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Africa/Casablanca',
            'label' => 'UTC (no DST) Tangiers, Casablanca',
                'gmt_offset' => 0,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Europe/Lisbon',
                'label' => 'UTC +00:00 Lisbon',
                'gmt_offset' => 0,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Africa/Algiers',
                'label' => 'UTC +01:00 Algeria',
                'gmt_offset' => 1,
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Europe/Berlin',
                'label' => 'UTC +01:00 Berlin, Stockholm, Rome, Bern, Brussels',
                'gmt_offset' => 1,
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Europe/Paris',
                'label' => 'UTC +01:00 Paris, Madrid',
                'gmt_offset' => 1,
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Europe/Prague',
                'label' => 'UTC +01:00 Prague, Warsaw',
                'gmt_offset' => 1,
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Europe/Athens',
                'label' => 'UTC +02:00 Athens, Helsinki, Istanbul',
                'gmt_offset' => 2,
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Africa/Cairo',
                'label' => 'UTC +02:00 Cairo',
                'gmt_offset' => 2,
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'EET',
                'label' => 'UTC +02:00 Eastern Europe',
                'gmt_offset' => 2,
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Africa/Harare',
                'label' => 'UTC +02:00 Harare, Pretoria',
                'gmt_offset' => 2,
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Asia/Jerusalem',
                'label' => 'UTC +02:00 Israel',
                'gmt_offset' => 2,
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Asia/Baghdad',
                'label' => 'UTC +03:00 Baghdad, Kuwait, Nairobi, Riyadh',
                'gmt_offset' => 3,
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Europe/Minsk',
                'label' => 'UTC +03:00 Minsk',
                'gmt_offset' => 3,
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Asia/Tehran',
                'label' => 'UTC +03:30 Tehran',
                'gmt_offset' => 3.5,
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Asia/Tbilisi',
                'label' => 'UTC +04:00 Abu Dhabi, Muscat, Tbilisi, Kazan',
                'gmt_offset' => 4,
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Asia/Yerevan',
                'label' => 'UTC +04:00 Armenia',
                'gmt_offset' => 4,
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Europe/Moscow',
                'label' => 'UTC +04:00 Moscow, St. Petersburg, Volgograd',
                'gmt_offset' => 4,
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Asia/Kabul',
                'label' => 'UTC +04:30 Kabul',
                'gmt_offset' => 4.5,
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Asia/Karachi',
                'label' => 'UTC +05:00 Islamabad, Karachi',
                'gmt_offset' => 5,
                'deleted_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'Asia/Tashkent',
                'label' => 'UTC +05:00 Tashkent',
                'gmt_offset' => 5,
                'deleted_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Asia/Calcutta',
                'label' => 'UTC +05:30 Mumbai, Kolkata, Chennai, New Delhi',
                'gmt_offset' => 5.5,
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Asia/Katmandu',
                'label' => 'UTC +05:45 Kathmandu, Nepal',
                'gmt_offset' => 5.75,
                'deleted_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'Asia/Almaty',
                'label' => 'UTC +06:00 Almaty, Dhaka',
                'gmt_offset' => 6,
                'deleted_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'Asia/Yekaterinburg',
                'label' => 'UTC +06:00 Sverdlovsk',
                'gmt_offset' => 6,
                'deleted_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'Asia/Bangkok',
                'label' => 'UTC +07:00 Bangkok, Jakarta, Hanoi',
                'gmt_offset' => 7,
                'deleted_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'Asia/Omsk',
                'label' => 'UTC +07:00 Omsk, Novosibirsk',
                'gmt_offset' => 7,
                'deleted_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'Asia/Shanghai',
                'label' => 'UTC +08:00 Beijing, Chongqing, Urumqi',
                'gmt_offset' => 8,
                'deleted_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'Australia/Perth',
                'label' => 'UTC +08:00 Hong Kong SAR, Perth, Singapore, Taipei',
                'gmt_offset' => 8,
                'deleted_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'Asia/Krasnoyarsk',
                'label' => 'UTC +08:00 Krasnoyarsk',
                'gmt_offset' => 8,
                'deleted_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'Asia/Pyongyang',
                'label' => 'UTC +09:00 Pyongyang',
                'gmt_offset' => 9,
                'deleted_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'Asia/Irkutsk',
            'label' => 'UTC +09:00 Irkutsk (Lake Baikal)',
                'gmt_offset' => 9,
                'deleted_at' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'Asia/Tokyo',
                'label' => 'UTC +09:00 Tokyo, Osaka, Sapporo, Seoul',
                'gmt_offset' => 9,
                'deleted_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'Australia/Adelaide',
                'label' => 'UTC +09:30 Adelaide',
                'gmt_offset' => 9.5,
                'deleted_at' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'Australia/Darwin',
                'label' => 'UTC +09:30 Darwin',
                'gmt_offset' => 9.5,
                'deleted_at' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'Australia/Brisbane',
                'label' => 'UTC +10:00 Brisbane',
                'gmt_offset' => 10,
                'deleted_at' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'Pacific/Guam',
                'label' => 'UTC +10:00 Guam, Port Moresby',
                'gmt_offset' => 10,
                'deleted_at' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'Australia/Sydney',
                'label' => 'UTC +10:00 Sydney, Melbourne',
                'gmt_offset' => 10,
                'deleted_at' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'Asia/Yakutsk',
            'label' => 'UTC +10:00 Yakutsk (Lena River)',
                'gmt_offset' => 10,
                'deleted_at' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'Australia/Hobart',
                'label' => 'UTC +11:00 Hobart',
                'gmt_offset' => 11,
                'deleted_at' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'Asia/Vladivostok',
                'label' => 'UTC +11:00 Vladivostok',
                'gmt_offset' => 11,
                'deleted_at' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'Pacific/Kwajalein',
                'label' => 'UTC +12:00 Eniwetok, Kwajalein',
                'gmt_offset' => 12,
                'deleted_at' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'Pacific/Fiji',
                'label' => 'UTC +12:00 Fiji Islands, Marshall Islands',
                'gmt_offset' => 12,
                'deleted_at' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'Asia/Kamchatka',
                'label' => 'UTC +12:00 Kamchatka',
                'gmt_offset' => 12,
                'deleted_at' => NULL,
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'Asia/Magadan',
                'label' => 'UTC +12:00 Magadan, Solomon Islands, New Caledonia',
                'gmt_offset' => 12,
                'deleted_at' => NULL,
            ),
            46 => 
            array (
                'id' => 47,
                'name' => 'Pacific/Auckland',
                'label' => 'UTC +12:00 Wellington, Auckland',
                'gmt_offset' => 12,
                'deleted_at' => NULL,
            ),
            47 => 
            array (
                'id' => 48,
                'name' => 'Pacific/Apia',
            'label' => 'UTC +13:00 Apia (Samoa)',
                'gmt_offset' => 13,
                'deleted_at' => NULL,
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'Atlantic/Azores',
                'label' => 'UTC -01:00 Azores, Cape Verde Island',
                'gmt_offset' => -1,
                'deleted_at' => NULL,
            ),
            49 => 
            array (
                'id' => 50,
                'name' => 'Atlantic/South_Georgia',
                'label' => 'UTC -02:00 Mid-Atlantic',
                'gmt_offset' => -2,
                'deleted_at' => NULL,
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'America/Buenos_Aires',
            'label' => 'UTC -03:00 E Argentina (BA, DF, SC, TF)',
                'gmt_offset' => -3,
                'deleted_at' => NULL,
            ),
            51 => 
            array (
                'id' => 52,
                'name' => 'America/Fortaleza',
            'label' => 'UTC -03:00 NE Brazil (MA, PI, CE, RN, PB)',
                'gmt_offset' => -3,
                'deleted_at' => NULL,
            ),
            52 => 
            array (
                'id' => 53,
                'name' => 'America/Recife',
                'label' => 'UTC -03:00 Pernambuco',
                'gmt_offset' => -3,
                'deleted_at' => NULL,
            ),
            53 => 
            array (
                'id' => 54,
                'name' => 'America/Sao_Paulo',
            'label' => 'UTC -03:00 S & SE Brazil (GO, DF, MG, ES, RJ, SP, PR, SC, RS)',
                'gmt_offset' => -3,
                'deleted_at' => NULL,
            ),
            54 => 
            array (
                'id' => 55,
                'name' => 'America/St_Johns',
                'label' => 'UTC -03:30 Newfoundland',
                'gmt_offset' => -3.5,
                'deleted_at' => NULL,
            ),
            55 => 
            array (
                'id' => 56,
                'name' => 'America/Halifax',
            'label' => 'UTC -04:00 Atlantic Time (Canada)',
                'gmt_offset' => -4,
                'deleted_at' => NULL,
            ),
            56 => 
            array (
                'id' => 57,
                'name' => 'America/La_Paz',
                'label' => 'UTC -04:00 La Paz',
                'gmt_offset' => -4,
                'deleted_at' => NULL,
            ),
            57 => 
            array (
                'id' => 58,
                'name' => 'America/Caracas',
                'label' => 'UTC -04:30 Caracas',
                'gmt_offset' => -4.5,
                'deleted_at' => NULL,
            ),
            58 => 
            array (
                'id' => 59,
                'name' => 'America/Bogota',
                'label' => 'UTC -05:00 Bogota, Lima',
                'gmt_offset' => -5,
                'deleted_at' => NULL,
            ),
            59 => 
            array (
                'id' => 60,
                'name' => 'America/New_York',
            'label' => 'UTC -05:00 Eastern Time (US & Canada)',
                'gmt_offset' => -5,
                'deleted_at' => NULL,
            ),
            60 => 
            array (
                'id' => 61,
                'name' => 'America/Indiana/Indianapolis',
                'label' => 'UTC -05:00 Eastern Time - Indiana - most locations',
                'gmt_offset' => -5,
                'deleted_at' => NULL,
            ),
            61 => 
            array (
                'id' => 62,
                'name' => 'America/Chicago',
            'label' => 'UTC -06:00 Central Time (US & Canada)',
                'gmt_offset' => -6,
                'deleted_at' => NULL,
            ),
            62 => 
            array (
                'id' => 63,
                'name' => 'America/Indiana/Knox',
                'label' => 'UTC -06:00 Eastern Time - Indiana - Starke County',
                'gmt_offset' => -6,
                'deleted_at' => NULL,
            ),
            63 => 
            array (
                'id' => 64,
                'name' => 'America/Mexico_City',
                'label' => 'UTC -06:00 Mexico City, Tegucigalpa',
                'gmt_offset' => -6,
                'deleted_at' => NULL,
            ),
            64 => 
            array (
                'id' => 65,
                'name' => 'America/Managua',
                'label' => 'UTC -06:00 Nicaragua',
                'gmt_offset' => -6,
                'deleted_at' => NULL,
            ),
            65 => 
            array (
                'id' => 66,
                'name' => 'America/Regina',
                'label' => 'UTC -06:00 Saskatchewan',
                'gmt_offset' => -6,
                'deleted_at' => NULL,
            ),
            66 => 
            array (
                'id' => 67,
                'name' => 'America/Phoenix',
                'label' => 'UTC -07:00 Arizona',
                'gmt_offset' => -7,
                'deleted_at' => NULL,
            ),
            67 => 
            array (
                'id' => 68,
                'name' => 'America/Denver',
            'label' => 'UTC -07:00 Mountain Time (US & Canada)',
                'gmt_offset' => -7,
                'deleted_at' => NULL,
            ),
            68 => 
            array (
                'id' => 69,
                'name' => 'America/Los_Angeles',
            'label' => 'UTC -08:00 Pacific Time (US & Canada); Los Angeles',
                'gmt_offset' => -8,
                'deleted_at' => NULL,
            ),
            69 => 
            array (
                'id' => 70,
                'name' => 'America/Tijuana',
            'label' => 'UTC -08:00 Pacific Time (US & Canada); Tijuana',
                'gmt_offset' => -8,
                'deleted_at' => NULL,
            ),
            70 => 
            array (
                'id' => 71,
                'name' => 'America/Nome',
                'label' => 'UTC -09:00 Alaska',
                'gmt_offset' => -9,
                'deleted_at' => NULL,
            ),
            71 => 
            array (
                'id' => 72,
                'name' => 'Pacific/Honolulu',
                'label' => 'UTC -10:00 Hawaii',
                'gmt_offset' => -10,
                'deleted_at' => NULL,
            ),
            72 => 
            array (
                'id' => 73,
                'name' => 'Pacific/Midway',
                'label' => 'UTC -11:00 Midway Island, Samoa',
                'gmt_offset' => -11,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}