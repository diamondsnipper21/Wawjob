<?php

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('languages')->delete();
        DB::table('languages')->insert(array (
            0 => 
            array (
                'id' => 1,
            'name' => 'Afrikaans (South Africa)',
                'code' => 'AF',
                'country' => 'ZA',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
            'name' => 'Albanian (Albania)',
                'code' => 'SQ',
                'country' => 'AL',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
            'name' => 'Alsatian (France)',
                'code' => 'GS',
                'country' => 'FR',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
            'name' => 'Amharic (Ethiopia)',
                'code' => 'AM',
                'country' => 'FR',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
            'name' => 'Arabic (Algeria)',
                'code' => 'AR',
                'country' => 'DZ',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
            'name' => 'Arabic (Bahrain)',
                'code' => 'AR',
                'country' => 'BH',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
            'name' => 'Arabic (Egypt)',
                'code' => 'AR',
                'country' => 'EG',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
            'name' => 'Arabic (Iraq)',
                'code' => 'AR',
                'country' => 'IQ',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
            'name' => 'Arabic (Jordan)',
                'code' => 'AR',
                'country' => 'JO',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
            'name' => 'Arabic (Kuwait)',
                'code' => 'AR',
                'country' => 'KW',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
            'name' => 'Arabic (Lebanon)',
                'code' => 'AR',
                'country' => 'LB',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
            'name' => 'Arabic (Libya)',
                'code' => 'AR',
                'country' => 'LY',
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
            'name' => 'Arabic (Morocco)',
                'code' => 'AR',
                'country' => 'MA',
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
            'name' => 'Arabic (Oman)',
                'code' => 'AR',
                'country' => 'OM',
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
            'name' => 'Arabic (Qatar)',
                'code' => 'AR',
                'country' => 'QA',
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
            'name' => 'Arabic (Saudi Arabia)',
                'code' => 'AR',
                'country' => 'SA',
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
            'name' => 'Arabic (Syria)',
                'code' => 'AR',
                'country' => 'SY',
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
            'name' => 'Arabic (Tunisia)',
                'code' => 'AR',
                'country' => 'TN',
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
            'name' => 'Arabic (U.A.E.)',
                'code' => 'AR',
                'country' => 'AE',
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
            'name' => 'Arabic (Yemem)',
                'code' => 'AR',
                'country' => 'YE',
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
            'name' => 'Armenian (Armenia)',
                'code' => 'HY',
                'country' => 'AM',
                'deleted_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
            'name' => 'Assamese (India)',
                'code' => 'AS',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
            'name' => 'Azeri (Cyrillic, Azerbaijan)',
                'code' => 'AZ',
                'country' => 'AZ',
                'deleted_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
            'name' => 'Azeri (Latin, Azerbaijan)',
                'code' => 'AZ',
                'country' => 'AZ',
                'deleted_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
            'name' => 'Bashkir (Russia)',
                'code' => 'BA',
                'country' => 'RU',
                'deleted_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
            'name' => 'Basque (Basque)',
                'code' => 'EU',
                'country' => 'ES',
                'deleted_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
            'name' => 'Belarusian (Belarus)',
                'code' => 'BE',
                'country' => 'BY',
                'deleted_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
            'name' => 'Bengali (Bangladesh)',
                'code' => 'BN',
                'country' => 'BD',
                'deleted_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
            'name' => 'Bengali (India)',
                'code' => 'BN',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
            'name' => 'Bosnian (Cyrillic, Bosnia and Herzegovina)',
                'code' => 'BS',
                'country' => 'BA',
                'deleted_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
            'name' => 'Bosnian (Latin, Bosnia and Herzegovina)',
                'code' => 'BS',
                'country' => 'BA',
                'deleted_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
            'name' => 'Breton (France)',
                'code' => 'BR',
                'country' => 'FR',
                'deleted_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
            'name' => 'Bulgarian (Bulgaria)',
                'code' => 'BG',
                'country' => 'BG',
                'deleted_at' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
            'name' => 'Catalan (Catalan)',
                'code' => 'CA',
                'country' => 'ES',
                'deleted_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
            'name' => 'Chinese (Simplified, PRC)',
                'code' => 'CH',
                'country' => 'CN',
                'deleted_at' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
            'name' => 'Chinese (Traditional, Hong Kong S.A.R)',
                'code' => 'ZH',
                'country' => 'HK',
                'deleted_at' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
            'name' => 'Chinese (Traditional, Singapore)',
                'code' => 'ZH',
                'country' => 'SG',
                'deleted_at' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
            'name' => 'Chinese (Traditional, Taiwan)',
                'code' => 'CH',
                'country' => 'TW',
                'deleted_at' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
            'name' => 'Chinese (Traditional, Macao S.A.R.)',
                'code' => 'ZH',
                'country' => 'MO',
                'deleted_at' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
            'name' => 'Corsican (France)',
                'code' => 'CO',
                'country' => 'FR',
                'deleted_at' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
            'name' => 'Croatian (Croatia)',
                'code' => 'HR',
                'country' => 'HR',
                'deleted_at' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
            'name' => 'Croatian (Latin, Bosnia and Herzegovina)',
                'code' => 'HR',
                'country' => 'BA',
                'deleted_at' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
            'name' => 'Czech (Czech Republic)',
                'code' => 'CS',
                'country' => 'CZ',
                'deleted_at' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
            'name' => 'Danish (Denmark)',
                'code' => 'DA',
                'country' => 'DK',
                'deleted_at' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
            'name' => 'Dari (Afghanistan)',
                'code' => 'PR',
                'country' => 'AF',
                'deleted_at' => NULL,
            ),
            45 => 
            array (
                'id' => 46,
            'name' => 'Divehi (Maldives)',
                'code' => 'DI',
                'country' => 'MV',
                'deleted_at' => NULL,
            ),
            46 => 
            array (
                'id' => 47,
            'name' => 'Dutch (Belgium)',
                'code' => 'NL',
                'country' => 'BE',
                'deleted_at' => NULL,
            ),
            47 => 
            array (
                'id' => 48,
            'name' => 'Dutch (Netherlands)',
                'code' => 'NL',
                'country' => 'NL',
                'deleted_at' => NULL,
            ),
            48 => 
            array (
                'id' => 49,
            'name' => 'English (Australia)',
                'code' => 'EN',
                'country' => 'AU',
                'deleted_at' => NULL,
            ),
            49 => 
            array (
                'id' => 50,
            'name' => 'English (Belize)',
                'code' => 'EN',
                'country' => 'BZ',
                'deleted_at' => NULL,
            ),
            50 => 
            array (
                'id' => 51,
            'name' => 'English (Canada)',
                'code' => 'EN',
                'country' => 'CA',
                'deleted_at' => NULL,
            ),
            51 => 
            array (
                'id' => 53,
            'name' => 'English (India)',
                'code' => 'EN',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            52 => 
            array (
                'id' => 54,
            'name' => 'English (Ireland)',
                'code' => 'EN',
                'country' => 'IE',
                'deleted_at' => NULL,
            ),
            53 => 
            array (
                'id' => 55,
            'name' => 'English (Jamaica)',
                'code' => 'EN',
                'country' => 'JM',
                'deleted_at' => NULL,
            ),
            54 => 
            array (
                'id' => 56,
            'name' => 'English (Malaysia)',
                'code' => 'EN',
                'country' => 'MY',
                'deleted_at' => NULL,
            ),
            55 => 
            array (
                'id' => 57,
            'name' => 'English (New Zealand)',
                'code' => 'EN',
                'country' => 'NZ',
                'deleted_at' => NULL,
            ),
            56 => 
            array (
                'id' => 58,
            'name' => 'English (Republic of the Philippines)',
                'code' => 'EN',
                'country' => 'PH',
                'deleted_at' => NULL,
            ),
            57 => 
            array (
                'id' => 59,
            'name' => 'English (Singapore)',
                'code' => 'EN',
                'country' => 'SG',
                'deleted_at' => NULL,
            ),
            58 => 
            array (
                'id' => 60,
            'name' => 'English (South Africa)',
                'code' => 'EN',
                'country' => 'ZA',
                'deleted_at' => NULL,
            ),
            59 => 
            array (
                'id' => 61,
            'name' => 'English (Trinidad and Tobago)',
                'code' => 'EN',
                'country' => 'TT',
                'deleted_at' => NULL,
            ),
            60 => 
            array (
                'id' => 62,
            'name' => 'English (United Kingdom)',
                'code' => 'EN',
                'country' => 'GB',
                'deleted_at' => NULL,
            ),
            61 => 
            array (
                'id' => 63,
            'name' => 'English (United States)',
                'code' => 'EN',
                'country' => 'US',
                'deleted_at' => NULL,
            ),
            62 => 
            array (
                'id' => 64,
            'name' => 'English (Zimbabwe)',
                'code' => 'EN',
                'country' => 'ZW',
                'deleted_at' => NULL,
            ),
            63 => 
            array (
                'id' => 65,
            'name' => 'English (Estonia)',
                'code' => 'EN',
                'country' => 'EE',
                'deleted_at' => NULL,
            ),
            64 => 
            array (
                'id' => 66,
            'name' => 'Faroese (Faroe Islands)',
                'code' => 'FO',
                'country' => 'FO',
                'deleted_at' => NULL,
            ),
            65 => 
            array (
                'id' => 67,
            'name' => 'Filipino (Philippines)',
                'code' => 'FP',
                'country' => 'PH',
                'deleted_at' => NULL,
            ),
            66 => 
            array (
                'id' => 68,
            'name' => 'Finnish (Finland)',
                'code' => 'FI',
                'country' => 'FI',
                'deleted_at' => NULL,
            ),
            67 => 
            array (
                'id' => 69,
            'name' => 'French (Belgium)',
                'code' => 'FR',
                'country' => 'BE',
                'deleted_at' => NULL,
            ),
            68 => 
            array (
                'id' => 70,
            'name' => 'French (Canada)',
                'code' => 'FR',
                'country' => 'CA',
                'deleted_at' => NULL,
            ),
            69 => 
            array (
                'id' => 71,
            'name' => 'French (France)',
                'code' => 'FR',
                'country' => 'FR',
                'deleted_at' => NULL,
            ),
            70 => 
            array (
                'id' => 72,
            'name' => 'French (Luxembourg)',
                'code' => 'FR',
                'country' => 'LU',
                'deleted_at' => NULL,
            ),
            71 => 
            array (
                'id' => 73,
            'name' => 'French (Monaco)',
                'code' => 'FR',
                'country' => 'MC',
                'deleted_at' => NULL,
            ),
            72 => 
            array (
                'id' => 74,
            'name' => 'French (Switzerland)',
                'code' => 'FR',
                'country' => 'CH',
                'deleted_at' => NULL,
            ),
            73 => 
            array (
                'id' => 75,
            'name' => 'Frisian (Netherlands)',
                'code' => 'FY',
                'country' => 'NL',
                'deleted_at' => NULL,
            ),
            74 => 
            array (
                'id' => 76,
            'name' => 'Galician (Galician)',
                'code' => 'GL',
                'country' => 'ES',
                'deleted_at' => NULL,
            ),
            75 => 
            array (
                'id' => 77,
            'name' => 'Georgian (Georgia)',
                'code' => 'KA',
                'country' => 'GE',
                'deleted_at' => NULL,
            ),
            76 => 
            array (
                'id' => 78,
            'name' => 'German (Austria)',
                'code' => 'DE',
                'country' => 'AT',
                'deleted_at' => NULL,
            ),
            77 => 
            array (
                'id' => 79,
            'name' => 'German (Germany)',
                'code' => 'DE',
                'country' => 'DE',
                'deleted_at' => NULL,
            ),
            78 => 
            array (
                'id' => 80,
            'name' => 'German (Liechtenstein)',
                'code' => 'DE',
                'country' => 'LI',
                'deleted_at' => NULL,
            ),
            79 => 
            array (
                'id' => 81,
            'name' => 'German (Luxembourg)',
                'code' => 'DE',
                'country' => 'LU',
                'deleted_at' => NULL,
            ),
            80 => 
            array (
                'id' => 82,
            'name' => 'German (Switzerland)',
                'code' => 'DE',
                'country' => 'CH',
                'deleted_at' => NULL,
            ),
            81 => 
            array (
                'id' => 83,
            'name' => 'Greek (Greece)',
                'code' => 'EL',
                'country' => 'GR',
                'deleted_at' => NULL,
            ),
            82 => 
            array (
                'id' => 84,
            'name' => 'Greenlandic (Greenland)',
                'code' => 'KA',
                'country' => 'GL',
                'deleted_at' => NULL,
            ),
            83 => 
            array (
                'id' => 85,
            'name' => 'Gujarati (India)',
                'code' => 'GU',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            84 => 
            array (
                'id' => 86,
            'name' => 'Hausa (Latin, Nigeria)',
                'code' => 'HA',
                'country' => 'NG',
                'deleted_at' => NULL,
            ),
            85 => 
            array (
                'id' => 87,
            'name' => 'Hebrew (Israel)',
                'code' => 'HE',
                'country' => 'IL',
                'deleted_at' => NULL,
            ),
            86 => 
            array (
                'id' => 88,
            'name' => 'Hindi (India)',
                'code' => 'HI',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            87 => 
            array (
                'id' => 89,
            'name' => 'Hungarian (Hungary)',
                'code' => 'HU',
                'country' => 'HU',
                'deleted_at' => NULL,
            ),
            88 => 
            array (
                'id' => 90,
            'name' => 'Icelandic (Iceland)',
                'code' => 'IS',
                'country' => 'IS',
                'deleted_at' => NULL,
            ),
            89 => 
            array (
                'id' => 91,
            'name' => 'Igbo (Nigeria)',
                'code' => 'IB',
                'country' => 'NG',
                'deleted_at' => NULL,
            ),
            90 => 
            array (
                'id' => 92,
            'name' => 'Indonesian (Indonesia)',
                'code' => 'IN',
                'country' => 'ID',
                'deleted_at' => NULL,
            ),
            91 => 
            array (
                'id' => 93,
            'name' => 'Inuktitut (Latin, Canada)',
                'code' => 'IU',
                'country' => 'CA',
                'deleted_at' => NULL,
            ),
            92 => 
            array (
                'id' => 94,
            'name' => 'Inuktitut (Syllabics, Canada)',
                'code' => 'IU',
                'country' => 'CA',
                'deleted_at' => NULL,
            ),
            93 => 
            array (
                'id' => 95,
            'name' => 'Irish (Ireland)',
                'code' => 'IR',
                'country' => 'IE',
                'deleted_at' => NULL,
            ),
            94 => 
            array (
                'id' => 96,
            'name' => 'isiXhosa (South Africa)',
                'code' => 'XH',
                'country' => 'ZA',
                'deleted_at' => NULL,
            ),
            95 => 
            array (
                'id' => 97,
            'name' => 'isiZulu (South Africa)',
                'code' => 'ZU',
                'country' => 'ZA',
                'deleted_at' => NULL,
            ),
            96 => 
            array (
                'id' => 98,
            'name' => 'Italian (Italy)',
                'code' => 'IT',
                'country' => 'IT',
                'deleted_at' => NULL,
            ),
            97 => 
            array (
                'id' => 99,
            'name' => 'Italian (Switzerland)',
                'code' => 'IT',
                'country' => 'CH',
                'deleted_at' => NULL,
            ),
            98 => 
            array (
                'id' => 100,
            'name' => 'Japanese (Japan)',
                'code' => 'JP',
                'country' => 'JP',
                'deleted_at' => NULL,
            ),
            99 => 
            array (
                'id' => 102,
            'name' => 'Kannada (India)',
                'code' => 'KD',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            100 => 
            array (
                'id' => 103,
            'name' => 'Kazakh (Kazakhstan)',
                'code' => 'KK',
                'country' => 'KZ',
                'deleted_at' => NULL,
            ),
            101 => 
            array (
                'id' => 104,
            'name' => 'Khmer (Cambodia)',
                'code' => 'KH',
                'country' => 'KH',
                'deleted_at' => NULL,
            ),
            102 => 
            array (
                'id' => 105,
            'name' => 'Kiche (Guatemala)',
                'code' => 'QU',
                'country' => 'GT',
                'deleted_at' => NULL,
            ),
            103 => 
            array (
                'id' => 106,
            'name' => 'Kinyarwanda (Rwanda)',
                'code' => 'KI',
                'country' => 'RW',
                'deleted_at' => NULL,
            ),
            104 => 
            array (
                'id' => 107,
            'name' => 'Kiswahili (Kenya)',
                'code' => 'SW',
                'country' => 'KE',
                'deleted_at' => NULL,
            ),
            105 => 
            array (
                'id' => 108,
            'name' => 'Konkani (India)',
                'code' => 'KN',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            106 => 
            array (
                'id' => 109,
            'name' => 'Korean (Korea)',
                'code' => 'KO',
                'country' => 'KO',
                'deleted_at' => NULL,
            ),
            107 => 
            array (
                'id' => 110,
            'name' => 'Korean (Korea, D.P.R.)',
                'code' => 'KP',
                'country' => 'KP',
                'deleted_at' => NULL,
            ),
            108 => 
            array (
                'id' => 111,
            'name' => 'Kyrgyz (Kyrgyzstan)',
                'code' => 'KY',
                'country' => 'KG',
                'deleted_at' => NULL,
            ),
            109 => 
            array (
                'id' => 112,
            'name' => 'Lao (Lao P.D.R.)',
                'code' => 'LA',
                'country' => 'LA',
                'deleted_at' => NULL,
            ),
            110 => 
            array (
                'id' => 113,
            'name' => 'Latvian (Latvia)',
                'code' => 'LV',
                'country' => 'LV',
                'deleted_at' => NULL,
            ),
            111 => 
            array (
                'id' => 114,
            'name' => 'Lithuanian (Lithuania)',
                'code' => 'LT',
                'country' => 'LT',
                'deleted_at' => NULL,
            ),
            112 => 
            array (
                'id' => 115,
            'name' => 'Lower Sorbian (Germany)',
                'code' => 'DS',
                'country' => 'DE',
                'deleted_at' => NULL,
            ),
            113 => 
            array (
                'id' => 116,
            'name' => 'Luxembourgish (Luxembourg)',
                'code' => 'LB',
                'country' => 'LU',
                'deleted_at' => NULL,
            ),
            114 => 
            array (
                'id' => 117,
            'name' => 'Macedonian (Former Yugoslav Republic of Macedonia)',
                'code' => 'MK',
                'country' => 'MK',
                'deleted_at' => NULL,
            ),
            115 => 
            array (
                'id' => 118,
            'name' => 'Malay (Brunei Darussalam)',
                'code' => 'MS',
                'country' => 'BN',
                'deleted_at' => NULL,
            ),
            116 => 
            array (
                'id' => 119,
            'name' => 'Malay (Malaysia)',
                'code' => 'MS',
                'country' => 'MY',
                'deleted_at' => NULL,
            ),
            117 => 
            array (
                'id' => 120,
            'name' => 'Malayalam (India)',
                'code' => 'MY',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            118 => 
            array (
                'id' => 121,
            'name' => 'Maltese (Malta)',
                'code' => 'ML',
                'country' => 'MT',
                'deleted_at' => NULL,
            ),
            119 => 
            array (
                'id' => 122,
            'name' => 'Maori (New Zealand)',
                'code' => 'MR',
                'country' => 'NZ',
                'deleted_at' => NULL,
            ),
            120 => 
            array (
                'id' => 123,
            'name' => 'Mapudungun (Chile)',
                'code' => 'MP',
                'country' => 'CL',
                'deleted_at' => NULL,
            ),
            121 => 
            array (
                'id' => 124,
            'name' => 'Marathi (India)',
                'code' => 'MA',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            122 => 
            array (
                'id' => 125,
            'name' => 'Mohawk (Mohawk)',
                'code' => 'MW',
                'country' => 'CA',
                'deleted_at' => NULL,
            ),
            123 => 
            array (
                'id' => 126,
            'name' => 'Mongolian (Cyrillic, Mongolia)',
                'code' => 'MN',
                'country' => 'MN',
                'deleted_at' => NULL,
            ),
            124 => 
            array (
                'id' => 127,
            'name' => 'Mongolian (Traditional Mongolian, PRC)',
                'code' => 'MN',
                'country' => 'MN',
                'deleted_at' => NULL,
            ),
            125 => 
            array (
                'id' => 128,
            'name' => 'Nepali (Nepal)',
                'code' => 'NE',
                'country' => 'NP',
                'deleted_at' => NULL,
            ),
            126 => 
            array (
                'id' => 129,
            'name' => 'Norwegian, Bokmal (Norway)',
                'code' => 'NO',
                'country' => 'NO',
                'deleted_at' => NULL,
            ),
            127 => 
            array (
                'id' => 130,
            'name' => 'Norwegian, Nynorsk (Norway)',
                'code' => 'NO',
                'country' => 'NO',
                'deleted_at' => NULL,
            ),
            128 => 
            array (
                'id' => 131,
            'name' => 'Occitan (France)',
                'code' => 'OC',
                'country' => 'FR',
                'deleted_at' => NULL,
            ),
            129 => 
            array (
                'id' => 132,
            'name' => 'Oriya (India)',
                'code' => 'OR',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            130 => 
            array (
                'id' => 133,
            'name' => 'Pashto (Afghanistan)',
                'code' => 'PA',
                'country' => 'AF',
                'deleted_at' => NULL,
            ),
            131 => 
            array (
                'id' => 134,
                'name' => 'Persian',
                'code' => 'FA',
                'country' => 'IR',
                'deleted_at' => NULL,
            ),
            132 => 
            array (
                'id' => 135,
            'name' => 'Polish (Poland)',
                'code' => 'PL',
                'country' => 'PL',
                'deleted_at' => NULL,
            ),
            133 => 
            array (
                'id' => 136,
            'name' => 'Portuguese (Brazil)',
                'code' => 'PT',
                'country' => 'BR',
                'deleted_at' => NULL,
            ),
            134 => 
            array (
                'id' => 137,
            'name' => 'Portuguese (Portugal)',
                'code' => 'PT',
                'country' => 'PT',
                'deleted_at' => NULL,
            ),
            135 => 
            array (
                'id' => 138,
            'name' => 'Punjabi (India)',
                'code' => 'PA',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            136 => 
            array (
                'id' => 139,
            'name' => 'Quechua (Bolivia)',
                'code' => 'QU',
                'country' => 'BO',
                'deleted_at' => NULL,
            ),
            137 => 
            array (
                'id' => 140,
            'name' => 'Quechua (Ecuador)',
                'code' => 'QU',
                'country' => 'EC',
                'deleted_at' => NULL,
            ),
            138 => 
            array (
                'id' => 141,
            'name' => 'Quechua (Peru)',
                'code' => 'QU',
                'country' => 'PE',
                'deleted_at' => NULL,
            ),
            139 => 
            array (
                'id' => 142,
            'name' => 'Romanian (Romania)',
                'code' => 'RO',
                'country' => 'RO',
                'deleted_at' => NULL,
            ),
            140 => 
            array (
                'id' => 143,
            'name' => 'Romansh (Switzerland)',
                'code' => 'RM',
                'country' => 'CH',
                'deleted_at' => NULL,
            ),
            141 => 
            array (
                'id' => 144,
            'name' => 'Russian (Russia)',
                'code' => 'RU',
                'country' => 'RU',
                'deleted_at' => NULL,
            ),
            142 => 
            array (
                'id' => 145,
            'name' => 'Sami, Inari (Finland)',
                'code' => 'SM',
                'country' => 'FI',
                'deleted_at' => NULL,
            ),
            143 => 
            array (
                'id' => 146,
            'name' => 'Sami, Lule (Norway)',
                'code' => 'SM',
                'country' => 'NO',
                'deleted_at' => NULL,
            ),
            144 => 
            array (
                'id' => 147,
            'name' => 'Sami, Lule (Sweden)',
                'code' => 'SM',
                'country' => 'SE',
                'deleted_at' => NULL,
            ),
            145 => 
            array (
                'id' => 148,
            'name' => 'Sami, Northern (Finland)',
                'code' => 'SM',
                'country' => 'FI',
                'deleted_at' => NULL,
            ),
            146 => 
            array (
                'id' => 149,
            'name' => 'Sami, Northern (Norway)',
                'code' => 'SM',
                'country' => 'NO',
                'deleted_at' => NULL,
            ),
            147 => 
            array (
                'id' => 150,
            'name' => 'Sami, Northern (Sweden)',
                'code' => 'SM',
                'country' => 'SE',
                'deleted_at' => NULL,
            ),
            148 => 
            array (
                'id' => 151,
            'name' => 'Sami, Skolt (Finland)',
                'code' => 'SM',
                'country' => 'FI',
                'deleted_at' => NULL,
            ),
            149 => 
            array (
                'id' => 152,
            'name' => 'Sami, Southern (Norway)',
                'code' => 'SM',
                'country' => 'NO',
                'deleted_at' => NULL,
            ),
            150 => 
            array (
                'id' => 153,
            'name' => 'Sami, Southern (Sweden)',
                'code' => 'SM',
                'country' => 'SE',
                'deleted_at' => NULL,
            ),
            151 => 
            array (
                'id' => 154,
            'name' => 'Sanskrit (India)',
                'code' => 'SA',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            152 => 
            array (
                'id' => 155,
            'name' => 'Scottish Gaelic (United Kingdom)',
                'code' => 'GL',
                'country' => 'GB',
                'deleted_at' => NULL,
            ),
            153 => 
            array (
                'id' => 156,
            'name' => 'Serbian (Cyrillic, Bosnia and Herzegovina)',
                'code' => 'SR',
                'country' => 'BA',
                'deleted_at' => NULL,
            ),
            154 => 
            array (
                'id' => 157,
            'name' => 'Serbian (Cyrillic, Montenegro)',
                'code' => 'SR',
                'country' => 'ME',
                'deleted_at' => NULL,
            ),
            155 => 
            array (
                'id' => 159,
            'name' => 'Serbian (Cyrillic, Serbia)',
                'code' => 'SR',
                'country' => 'RS',
                'deleted_at' => NULL,
            ),
            156 => 
            array (
                'id' => 160,
            'name' => 'Serbian (Latin, Bosnia and Herzegovina)',
                'code' => 'SR',
                'country' => 'BA',
                'deleted_at' => NULL,
            ),
            157 => 
            array (
                'id' => 161,
            'name' => 'Serbian (Latin, Montenegro)',
                'code' => 'SR',
                'country' => 'ME',
                'deleted_at' => NULL,
            ),
            158 => 
            array (
                'id' => 163,
            'name' => 'Serbian (Latin, Serbia)',
                'code' => 'SR',
                'country' => 'RS',
                'deleted_at' => NULL,
            ),
            159 => 
            array (
                'id' => 164,
            'name' => 'Sesotho sa Leboa (South Africa)',
                'code' => 'NS',
                'country' => 'ZA',
                'deleted_at' => NULL,
            ),
            160 => 
            array (
                'id' => 165,
            'name' => 'Setswana (South Africa)',
                'code' => 'TS',
                'country' => 'ZA',
                'deleted_at' => NULL,
            ),
            161 => 
            array (
                'id' => 166,
            'name' => 'Sinhala (Sri Lanka)',
                'code' => 'SI',
                'country' => 'LK',
                'deleted_at' => NULL,
            ),
            162 => 
            array (
                'id' => 167,
            'name' => 'Slovak (Slovakia)',
                'code' => 'SK',
                'country' => 'SK',
                'deleted_at' => NULL,
            ),
            163 => 
            array (
                'id' => 168,
            'name' => 'Slovenian (Slovenia)',
                'code' => 'SL',
                'country' => 'SI',
                'deleted_at' => NULL,
            ),
            164 => 
            array (
                'id' => 169,
            'name' => 'Spanish (Argentina)',
                'code' => 'ES',
                'country' => 'AR',
                'deleted_at' => NULL,
            ),
            165 => 
            array (
                'id' => 170,
            'name' => 'Spanish (Bolivarian Republic of Venezuela)',
                'code' => 'ES',
                'country' => 'VE',
                'deleted_at' => NULL,
            ),
            166 => 
            array (
                'id' => 171,
            'name' => 'Spanish (Bolivia)',
                'code' => 'ES',
                'country' => 'BO',
                'deleted_at' => NULL,
            ),
            167 => 
            array (
                'id' => 172,
            'name' => 'Spanish (Chile)',
                'code' => 'ES',
                'country' => 'CL',
                'deleted_at' => NULL,
            ),
            168 => 
            array (
                'id' => 173,
            'name' => 'Spanish (Colombia)',
                'code' => 'ES',
                'country' => 'CO',
                'deleted_at' => NULL,
            ),
            169 => 
            array (
                'id' => 174,
            'name' => 'Spanish (Costa Rica)',
                'code' => 'ES',
                'country' => 'CR',
                'deleted_at' => NULL,
            ),
            170 => 
            array (
                'id' => 175,
            'name' => 'Spanish (Dominican Republic)',
                'code' => 'ES',
                'country' => 'DO',
                'deleted_at' => NULL,
            ),
            171 => 
            array (
                'id' => 176,
            'name' => 'Spanish (Ecuador)',
                'code' => 'ES',
                'country' => 'EC',
                'deleted_at' => NULL,
            ),
            172 => 
            array (
                'id' => 177,
            'name' => 'Spanish (El Salvador)',
                'code' => 'ES',
                'country' => 'SV',
                'deleted_at' => NULL,
            ),
            173 => 
            array (
                'id' => 178,
            'name' => 'Spanish (Guatemala)',
                'code' => 'ES',
                'country' => 'GT',
                'deleted_at' => NULL,
            ),
            174 => 
            array (
                'id' => 179,
            'name' => 'Spanish (Honduras)',
                'code' => 'ES',
                'country' => 'HN',
                'deleted_at' => NULL,
            ),
            175 => 
            array (
                'id' => 180,
            'name' => 'Spanish (Mexico)',
                'code' => 'ES',
                'country' => 'MX',
                'deleted_at' => NULL,
            ),
            176 => 
            array (
                'id' => 181,
            'name' => 'Spanish (Nicaragua)',
                'code' => 'ES',
                'country' => 'NI',
                'deleted_at' => NULL,
            ),
            177 => 
            array (
                'id' => 182,
            'name' => 'Spanish (Panama)',
                'code' => 'ES',
                'country' => 'PA',
                'deleted_at' => NULL,
            ),
            178 => 
            array (
                'id' => 183,
            'name' => 'Spanish (Paraguay)',
                'code' => 'ES',
                'country' => 'PY',
                'deleted_at' => NULL,
            ),
            179 => 
            array (
                'id' => 184,
            'name' => 'Spanish (Peru)',
                'code' => 'ES',
                'country' => 'PE',
                'deleted_at' => NULL,
            ),
            180 => 
            array (
                'id' => 185,
            'name' => 'Spanish (Puerto Rico)',
                'code' => 'ES',
                'country' => 'PR',
                'deleted_at' => NULL,
            ),
            181 => 
            array (
                'id' => 186,
            'name' => 'Spanish (Spain, International Sort)',
                'code' => 'ES',
                'country' => 'ES',
                'deleted_at' => NULL,
            ),
            182 => 
            array (
                'id' => 187,
            'name' => 'Spanish (Spain, Traditional Sort)',
                'code' => 'ES',
                'country' => 'ES',
                'deleted_at' => NULL,
            ),
            183 => 
            array (
                'id' => 188,
            'name' => 'Spanish (United States)',
                'code' => 'ES',
                'country' => 'US',
                'deleted_at' => NULL,
            ),
            184 => 
            array (
                'id' => 189,
            'name' => 'Spanish (Uruguay)',
                'code' => 'ES',
                'country' => 'UY',
                'deleted_at' => NULL,
            ),
            185 => 
            array (
                'id' => 190,
            'name' => 'Swedish (Finland)',
                'code' => 'SV',
                'country' => 'FI',
                'deleted_at' => NULL,
            ),
            186 => 
            array (
                'id' => 191,
            'name' => 'Swedish (Sweden)',
                'code' => 'SV',
                'country' => 'SE',
                'deleted_at' => NULL,
            ),
            187 => 
            array (
                'id' => 192,
            'name' => 'Syriac (Syria)',
                'code' => 'SY',
                'country' => 'SY',
                'deleted_at' => NULL,
            ),
            188 => 
            array (
                'id' => 193,
            'name' => 'Tajik (Cyrillic, Tajikistan)',
                'code' => 'TA',
                'country' => 'TJ',
                'deleted_at' => NULL,
            ),
            189 => 
            array (
                'id' => 194,
            'name' => 'Tamazight (Latin, Algeria)',
                'code' => 'TZ',
                'country' => 'DZ',
                'deleted_at' => NULL,
            ),
            190 => 
            array (
                'id' => 195,
            'name' => 'Tamil (India)',
                'code' => 'TA',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            191 => 
            array (
                'id' => 196,
            'name' => 'Tatar (Russia)',
                'code' => 'TT',
                'country' => 'RU',
                'deleted_at' => NULL,
            ),
            192 => 
            array (
                'id' => 197,
            'name' => 'Telugu (India)',
                'code' => 'TE',
                'country' => 'IN',
                'deleted_at' => NULL,
            ),
            193 => 
            array (
                'id' => 198,
            'name' => 'Thai (Thailand)',
                'code' => 'TH',
                'country' => 'TH',
                'deleted_at' => NULL,
            ),
            194 => 
            array (
                'id' => 199,
            'name' => 'Tibetan (PRC)',
                'code' => 'BO',
                'country' => 'CN',
                'deleted_at' => NULL,
            ),
            195 => 
            array (
                'id' => 200,
            'name' => 'Turkish (Turkey)',
                'code' => 'TR',
                'country' => 'TR',
                'deleted_at' => NULL,
            ),
            196 => 
            array (
                'id' => 201,
            'name' => 'Turkmen (Turkmenistan)',
                'code' => 'TU',
                'country' => 'TM',
                'deleted_at' => NULL,
            ),
            197 => 
            array (
                'id' => 202,
            'name' => 'Ukrainian (Ukraine)',
                'code' => 'UK',
                'country' => 'UA',
                'deleted_at' => NULL,
            ),
            198 => 
            array (
                'id' => 203,
            'name' => 'Upper Sorbian (Germany)',
                'code' => 'HS',
                'country' => 'DE',
                'deleted_at' => NULL,
            ),
            199 => 
            array (
                'id' => 204,
            'name' => 'Urdu (Islamic Republic of Pakistan)',
                'code' => 'UR',
                'country' => 'PK',
                'deleted_at' => NULL,
            ),
            200 => 
            array (
                'id' => 205,
            'name' => 'Uyghur (PRC)',
                'code' => 'UI',
                'country' => 'CN',
                'deleted_at' => NULL,
            ),
            201 => 
            array (
                'id' => 206,
            'name' => 'Uzbek (Cyrillic, Uzbekistan)',
                'code' => 'UZ',
                'country' => 'UZ',
                'deleted_at' => NULL,
            ),
            202 => 
            array (
                'id' => 207,
            'name' => 'Uzbek (Latin, Uzbekistan)',
                'code' => 'UZ',
                'country' => 'UZ',
                'deleted_at' => NULL,
            ),
            203 => 
            array (
                'id' => 208,
            'name' => 'Vietnamese (Vietnam)',
                'code' => 'VI',
                'country' => 'VN',
                'deleted_at' => NULL,
            ),
            204 => 
            array (
                'id' => 209,
            'name' => 'Welsh (United Kingdom)',
                'code' => 'CY',
                'country' => 'GB',
                'deleted_at' => NULL,
            ),
            205 => 
            array (
                'id' => 210,
            'name' => 'Wolof (Senegal)',
                'code' => 'WO',
                'country' => 'SN',
                'deleted_at' => NULL,
            ),
            206 => 
            array (
                'id' => 211,
            'name' => 'Yakut (Russia)',
                'code' => 'SA',
                'country' => 'RU',
                'deleted_at' => NULL,
            ),
            207 => 
            array (
                'id' => 212,
            'name' => 'Yi (PRC)',
                'code' => 'II',
                'country' => 'CN',
                'deleted_at' => NULL,
            ),
            208 => 
            array (
                'id' => 213,
            'name' => 'Yoruba (Nigeria)',
                'code' => 'YO',
                'country' => 'NG',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}