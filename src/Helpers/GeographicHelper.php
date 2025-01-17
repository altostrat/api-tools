<?php

namespace Altostrat\Tools\Helpers;

class GeographicHelper
{
    public static function getCurrencyFromIso2(string $iso2)
    {
        $default = 'USD';
        $countryToCurrency = [
            'AT' => 'EUR', // Austria
            'BE' => 'EUR', // Belgium
            'HR' => 'EUR', // Croatia
            'CY' => 'EUR', // Cyprus
            'EE' => 'EUR', // Estonia
            'FI' => 'EUR', // Finland
            'FR' => 'EUR', // France
            'DE' => 'EUR', // Germany
            'GR' => 'EUR', // Greece
            'IE' => 'EUR', // Ireland
            'IT' => 'EUR', // Italy
            'LV' => 'EUR', // Latvia
            'LT' => 'EUR', // Lithuania
            'LU' => 'EUR', // Luxembourg
            'MT' => 'EUR', // Malta
            'NL' => 'EUR', // the Netherlands
            'PT' => 'EUR', // Portugal
            'SK' => 'EUR', // Slovakia
            'SI' => 'EUR', // Slovenia
            'ES' => 'EUR', // Spain
            'BG' => 'EUR', // Bulgaria
            'CZ' => 'EUR', // Czechia
            'HU' => 'EUR', // Hungary
            'PL' => 'EUR', // Poland
            'RO' => 'EUR', // Romania
            'SE' => 'EUR', // Sweden
            'DK' => 'EUR', // Denmark
            'GB' => 'GBP', // United Kingdom
            'ZA' => 'ZAR', // South Africa
            'AU' => 'AUD', // Australia
        ];

        return $countryToCurrency[$iso2] ?? $default;
    }

    public static function getCurrencySymbol(string $code)
    {
        $default = '$';
        $currencyToSymbol = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'ZAR' => 'R',
            'AUD' => 'A$',
        ];

        return $currencyToSymbol[strtoupper($code)] ?? $default;
    }

    public static function countryCodes()
    {
        $flags = [
            'AD' => '🇦🇩',
            'AE' => '🇦🇪',
            'AF' => '🇦🇫',
            'AG' => '🇦🇬',
            'AI' => '🇦🇮',
            'AL' => '🇦🇱',
            'AM' => '🇦🇲',
            'AO' => '🇦🇴',
            'AQ' => '🇦🇶',
            'AR' => '🇦🇷',
            'AS' => '🇦🇸',
            'AT' => '🇦🇹',
            'AU' => '🇦🇺',
            'AW' => '🇦🇼',
            'AX' => '🇦🇽',
            'AZ' => '🇦🇿',
            'BA' => '🇧🇦',
            'BB' => '🇧🇧',
            'BD' => '🇧🇩',
            'BE' => '🇧🇪',
            'BF' => '🇧🇫',
            'BG' => '🇧🇬',
            'BH' => '🇧🇭',
            'BI' => '🇧🇮',
            'BJ' => '🇧🇯',
            'BL' => '🇧🇱',
            'BM' => '🇧🇲',
            'BN' => '🇧🇳',
            'BO' => '🇧🇴',
            'BQ' => '🇧🇶',
            'BR' => '🇧🇷',
            'BS' => '🇧🇸',
            'BT' => '🇧🇹',
            'BV' => '🇧🇻',
            'BW' => '🇧🇼',
            'BY' => '🇧🇾',
            'BZ' => '🇧🇿',
            'CA' => '🇨🇦',
            'CC' => '🇨🇨',
            'CD' => '🇨🇩',
            'CF' => '🇨🇫',
            'CG' => '🇨🇬',
            'CH' => '🇨🇭',
            'CI' => '🇨🇮',
            'CK' => '🇨🇰',
            'CL' => '🇨🇱',
            'CM' => '🇨🇲',
            'CN' => '🇨🇳',
            'CO' => '🇨🇴',
            'CR' => '🇨🇷',
            'CU' => '🇨🇺',
            'CV' => '🇨🇻',
            'CW' => '🇨🇼',
            'CX' => '🇨🇽',
            'CY' => '🇨🇾',
            'CZ' => '🇨🇿',
            'DE' => '🇩🇪',
            'DJ' => '🇩🇯',
            'DK' => '🇩🇰',
            'DM' => '🇩🇲',
            'DO' => '🇩🇴',
            'DZ' => '🇩🇿',
            'EC' => '🇪🇨',
            'EE' => '🇪🇪',
            'EG' => '🇪🇬',
            'EH' => '🇪🇭',
            'ER' => '🇪🇷',
            'ES' => '🇪🇸',
            'ET' => '🇪🇹',
            'FI' => '🇫🇮',
            'FJ' => '🇫🇯',
            'FK' => '🇫🇰',
            'FM' => '🇫🇲',
            'FO' => '🇫🇴',
            'FR' => '🇫🇷',
            'GA' => '🇬🇦',
            'GB' => '🇬🇧',
            'GD' => '🇬🇩',
            'GE' => '🇬🇪',
            'GF' => '🇬🇫',
            'GG' => '🇬🇬',
            'GH' => '🇬🇭',
            'GI' => '🇬🇮',
            'GL' => '🇬🇱',
            'GM' => '🇬🇲',
            'GN' => '🇬🇳',
            'GP' => '🇬🇵',
            'GQ' => '🇬🇶',
            'GR' => '🇬🇷',
            'GS' => '🇬🇸',
            'GT' => '🇬🇹',
            'GU' => '🇬🇺',
            'GW' => '🇬🇼',
            'GY' => '🇬🇾',
            'HK' => '🇭🇰',
            'HM' => '🇭🇲',
            'HN' => '🇭🇳',
            'HR' => '🇭🇷',
            'HT' => '🇭🇹',
            'HU' => '🇭🇺',
            'ID' => '🇮🇩',
            'IE' => '🇮🇪',
            'IL' => '🇮🇱',
            'IM' => '🇮🇲',
            'IN' => '🇮🇳',
            'IO' => '🇮🇴',
            'IQ' => '🇮🇶',
            'IR' => '🇮🇷',
            'IS' => '🇮🇸',
            'IT' => '🇮🇹',
            'JE' => '🇯🇪',
            'JM' => '🇯🇲',
            'JO' => '🇯🇴',
            'JP' => '🇯🇵',
            'KE' => '🇰🇪',
            'KG' => '🇰🇬',
            'KH' => '🇰🇭',
            'KI' => '🇰🇮',
            'KM' => '🇰🇲',
            'KN' => '🇰🇳',
            'KP' => '🇰🇵',
            'KR' => '🇰🇷',
            'KW' => '🇰🇼',
            'KY' => '🇰🇾',
            'KZ' => '🇰🇿',
            'LA' => '🇱🇦',
            'LB' => '🇱🇧',
            'LC' => '🇱🇨',
            'LI' => '🇱🇮',
            'LK' => '🇱🇰',
            'LR' => '🇱🇷',
            'LS' => '🇱🇸',
            'LT' => '🇱🇹',
            'LU' => '🇱🇺',
            'LV' => '🇱🇻',
            'LY' => '🇱🇾',
            'MA' => '🇲🇦',
            'MC' => '🇲🇨',
            'MD' => '🇲🇩',
            'ME' => '🇲🇪',
            'MF' => '🇲🇫',
            'MG' => '🇲🇬',
            'MH' => '🇲🇭',
            'MK' => '🇲🇰',
            'ML' => '🇲🇱',
            'MM' => '🇲🇲',
            'MN' => '🇲🇳',
            'MO' => '🇲🇴',
            'MP' => '🇲🇵',
            'MQ' => '🇲🇶',
            'MR' => '🇲🇷',
            'MS' => '🇲🇸',
            'MT' => '🇲🇹',
            'MU' => '🇲🇺',
            'MV' => '🇲🇻',
            'MW' => '🇲🇼',
            'MX' => '🇲🇽',
            'MY' => '🇲🇾',
            'MZ' => '🇲🇿',
            'NA' => '🇳🇦',
            'NC' => '🇳🇨',
            'NE' => '🇳🇪',
            'NF' => '🇳🇫',
            'NG' => '🇳🇬',
            'NI' => '🇳🇮',
            'NL' => '🇳🇱',
            'NO' => '🇳🇴',
            'NP' => '🇳🇵',
            'NR' => '🇳🇷',
            'NU' => '🇳🇺',
            'NZ' => '🇳🇿',
            'OM' => '🇴🇲',
            'PA' => '🇵🇦',
            'PE' => '🇵🇪',
            'PF' => '🇵🇫',
            'PG' => '🇵🇬',
            'PH' => '🇵🇭',
            'PK' => '🇵🇰',
            'PL' => '🇵🇱',
            'PM' => '🇵🇲',
            'PN' => '🇵🇳',
            'PR' => '🇵🇷',
            'PS' => '🇵🇸',
            'PT' => '🇵🇹',
            'PW' => '🇵🇼',
            'PY' => '🇵🇾',
            'QA' => '🇶🇦',
            'RE' => '🇷🇪',
            'RO' => '🇷🇴',
            'RS' => '🇷🇸',
            'RU' => '🇷🇺',
            'RW' => '🇷🇼',
            'SA' => '🇸🇦',
            'SB' => '🇸🇧',
            'SC' => '🇸🇨',
            'SD' => '🇸🇩',
            'SE' => '🇸🇪',
            'SG' => '🇸🇬',
            'SH' => '🇸🇭',
            'SI' => '🇸🇮',
            'SJ' => '🇸🇯',
            'SK' => '🇸🇰',
            'SL' => '🇸🇱',
            'SM' => '🇸🇲',
            'SN' => '🇸🇳',
            'SO' => '🇸🇴',
            'SR' => '🇸🇷',
            'SS' => '🇸🇸',
            'ST' => '🇸🇹',
            'SV' => '🇸🇻',
            'SX' => '🇸🇽',
            'SY' => '🇸🇾',
            'SZ' => '🇸🇿',
            'TC' => '🇹🇨',
            'TD' => '🇹🇩',
            'TF' => '🇹🇫',
            'TG' => '🇹🇬',
            'TH' => '🇹🇭',
            'TJ' => '🇹🇯',
            'TK' => '🇹🇰',
            'TL' => '🇹🇱',
            'TM' => '🇹🇲',
            'TN' => '🇹🇳',
            'TO' => '🇹🇴',
            'TR' => '🇹🇷',
            'TT' => '🇹🇹',
            'TV' => '🇹🇻',
            'TW' => '🇹🇼',
            'TZ' => '🇹🇿',
            'UA' => '🇺🇦',
            'UG' => '🇺🇬',
            'UM' => '🇺🇲',
            'US' => '🇺🇸',
            'MI' => '🇺🇸',
            'UY' => '🇺🇾',
            'UZ' => '🇺🇿',
            'VA' => '🇻🇦',
            'VC' => '🇻🇨',
            'VE' => '🇻🇪',
            'VG' => '🇻🇬',
            'VI' => '🇻🇮',
            'VN' => '🇻🇳',
            'VU' => '🇻🇺',
            'WF' => '🇼🇫',
            'WS' => '🇼🇸',
            'YE' => '🇾🇪',
            'YT' => '🇾🇹',
            'ZA' => '🇿🇦',
            'ZM' => '🇿🇲',
            'ZW' => '🇿🇼',
        ];

        $phone_numbers = [
            'AF' => [
                'code' => '+93',
                'name' => 'Afghanistan',
            ],
            'AL' => [
                'code' => '+355',
                'name' => 'Albania',
            ],
            'DZ' => [
                'code' => '+213',
                'name' => 'Algeria',
            ],
            'AS' => [
                'code' => '+1684',
                'name' => 'American Samoa',
            ],
            'AD' => [
                'code' => '+376',
                'name' => 'Andorra',
            ],
            'AO' => [
                'code' => '+244',
                'name' => 'Angola',
            ],
            'AI' => [
                'code' => '+1264',
                'name' => 'Anguilla',
            ],
            'AQ' => [
                'code' => '+672',
                'name' => 'Antarctica',
            ],
            'AG' => [
                'code' => '+1268',
                'name' => 'Antigua and Barbuda',
            ],
            'AR' => [
                'code' => '+54',
                'name' => 'Argentina',
            ],
            'AM' => [
                'code' => '+374',
                'name' => 'Armenia',
            ],
            'AW' => [
                'code' => '+297',
                'name' => 'Aruba',
            ],
            'AU' => [
                'code' => '+61',
                'name' => 'Australia',
            ],
            'AT' => [
                'code' => '+43',
                'name' => 'Austria',
            ],
            'AZ' => [
                'code' => '+994',
                'name' => 'Azerbaijan',
            ],
            'BH' => [
                'code' => '+973',
                'name' => 'Bahrain',
            ],
            'BS' => [
                'code' => '+1242',
                'name' => 'Bahamas',
            ],
            'BD' => [
                'code' => '+880',
                'name' => 'Bangladesh',
            ],
            'BB' => [
                'code' => '+1 246',
                'name' => 'Barbados',
            ],
            'BY' => [
                'code' => '+375',
                'name' => 'Belarus',
            ],
            'BE' => [
                'code' => '+32',
                'name' => 'Belgium',
            ],
            'BZ' => [
                'code' => '+501',
                'name' => 'Belize',
            ],
            'BJ' => [
                'code' => '+229',
                'name' => 'Benin',
            ],
            'BM' => [
                'code' => '+1441',
                'name' => 'Bermuda',
            ],
            'BT' => [
                'code' => '+975',
                'name' => 'Bhutan',
            ],
            'BO' => [
                'code' => '+591',
                'name' => 'Bolivia',
            ],
            'BQ' => [
                'code' => '+599',
                'name' => 'Bonaire',
            ],
            'BA' => [
                'code' => '+387',
                'name' => 'Bosnia and Herzegovina',
            ],
            'BW' => [
                'code' => '+267',
                'name' => 'Botswana',
            ],
            'BV' => [
                'code' => '+47',
                'name' => 'Bouvet',
            ],
            'BR' => [
                'code' => '+55',
                'name' => 'Brazil',
            ],
            'IO' => [
                'code' => '+246',
                'name' => 'British Indian Ocean Territory',
            ],
            'VG' => [
                'code' => '+1284',
                'name' => 'British Virgin Islands',
            ],
            'BN' => [
                'code' => '+673',
                'name' => 'Brunei',
            ],
            'BG' => [
                'code' => '+359',
                'name' => 'Bulgaria',
            ],
            'BF' => [
                'code' => '+226',
                'name' => 'Burkina Faso',
            ],
            'BI' => [
                'code' => '+257',
                'name' => 'Burundi',
            ],
            'KH' => [
                'code' => '+855',
                'name' => 'Cambodia',
            ],
            'CM' => [
                'code' => '+237',
                'name' => 'Cameroon',
            ],
            'CA' => [
                'code' => '+1',
                'name' => 'Canada',
            ],
            'CV' => [
                'code' => '+238',
                'name' => 'Cape Verde',
            ],
            'KY' => [
                'code' => '+1345',
                'name' => 'Cayman Islands',
            ],
            'CF' => [
                'code' => '+236',
                'name' => 'Central African Republic',
            ],
            'TD' => [
                'code' => '+235',
                'name' => 'Chad',
            ],
            'CL' => [
                'code' => '+56',
                'name' => 'Chile',
            ],
            'CN' => [
                'code' => '+86',
                'name' => 'China',
            ],
            'CX' => [
                'code' => '+61',
                'name' => 'Christmas Island',
            ],
            'CC' => [
                'code' => '+672',
                'name' => 'Cocos-Keeling Islands',
            ],
            'CO' => [
                'code' => '+57',
                'name' => 'Colombia',
            ],
            'KM' => [
                'code' => '+269',
                'name' => 'Comoros',
            ],
            'CG' => [
                'code' => '+242',
                'name' => 'Congo',
            ],
            'CD' => [
                'code' => '+243',
                'name' => 'Congo, Dem. Rep. of (Zaire)',
            ],
            'CK' => [
                'code' => '+682',
                'name' => 'Cook Islands',
            ],
            'CR' => [
                'code' => '+506',
                'name' => 'Costa Rica',
            ],
            'CI' => [
                'code' => '+225',
                'name' => "Cote d'Ivoire",
            ],
            'HR' => [
                'code' => '+385',
                'name' => 'Croatia',
            ],
            'CW' => [
                'code' => '+599',
                'name' => 'Curacao',
            ],
            'CU' => [
                'code' => '+53',
                'name' => 'Cuba',
            ],
            'CY' => [
                'code' => '+357',
                'name' => 'Cyprus',
            ],
            'CZ' => [
                'code' => '+420',
                'name' => 'Czech Republic',
            ],
            'DK' => [
                'code' => '+45',
                'name' => 'Denmark',
            ],
            'DJ' => [
                'code' => '+253',
                'name' => 'Djibouti',
            ],
            'DM' => [
                'code' => '+1767',
                'name' => 'Dominica',
            ],
            'DO' => [
                'code' => '+1809',
                'name' => 'Dominican Republic',
            ],
            'TL' => [
                'code' => '+670',
                'name' => 'East Timor',
            ],
            'EC' => [
                'code' => '+593',
                'name' => 'Ecuador',
            ],
            'EG' => [
                'code' => '+20',
                'name' => 'Egypt',
            ],
            'SV' => [
                'code' => '+503',
                'name' => 'El Salvador',
            ],
            'GQ' => [
                'code' => '+240',
                'name' => 'Equatorial Guinea',
            ],
            'ER' => [
                'code' => '+291',
                'name' => 'Eritrea',
            ],
            'EE' => [
                'code' => '+372',
                'name' => 'Estonia',
            ],
            'ET' => [
                'code' => '+251',
                'name' => 'Ethiopia',
            ],
            'FK' => [
                'code' => '+500',
                'name' => 'Falkland Islands',
            ],
            'FO' => [
                'code' => '+298',
                'name' => 'Faroe Islands',
            ],
            'FJ' => [
                'code' => '+679',
                'name' => 'Fiji',
            ],
            'FI' => [
                'code' => '+358',
                'name' => 'Finland',
            ],
            'FR' => [
                'code' => '+33',
                'name' => 'France',
            ],
            'GF' => [
                'code' => '+594',
                'name' => 'French Guiana',
            ],
            'PF' => [
                'code' => '+689',
                'name' => 'French Polynesia',
            ],
            'TF' => [
                'code' => '+262',
                'name' => 'French Southern and Antarctic Lands',
            ],
            'GA' => [
                'code' => '+241',
                'name' => 'Gabon',
            ],
            'GM' => [
                'code' => '+220',
                'name' => 'Gambia',
            ],
            'GE' => [
                'code' => '+995',
                'name' => 'Georgia',
            ],
            'DE' => [
                'code' => '+49',
                'name' => 'Germany',
            ],
            'GH' => [
                'code' => '+233',
                'name' => 'Ghana',
            ],
            'GI' => [
                'code' => '+350',
                'name' => 'Gibraltar',
            ],
            'GR' => [
                'code' => '+30',
                'name' => 'Greece',
            ],
            'GL' => [
                'code' => '+299',
                'name' => 'Greenland',
            ],
            'GD' => [
                'code' => '+1473',
                'name' => 'Grenada',
            ],
            'GP' => [
                'code' => '+590',
                'name' => 'Guadeloupe',
            ],
            'GU' => [
                'code' => '+1671',
                'name' => 'Guam',
            ],
            'GT' => [
                'code' => '+502',
                'name' => 'Guatemala',
            ],
            'GG' => [
                'code' => '+44',
                'name' => 'Guernsey',
            ],
            'GN' => [
                'code' => '+224',
                'name' => 'Guinea',
            ],
            'GW' => [
                'code' => '+245',
                'name' => 'Guinea-Bissau',
            ],
            'GY' => [
                'code' => '+592',
                'name' => 'Guyana',
            ],
            'HT' => [
                'code' => '+509',
                'name' => 'Haiti',
            ],
            'HM' => [
                'code' => '+0',
                'name' => 'Heard Island and McDonald Islands',
            ],
            'VA' => [
                'code' => '+39',
                'name' => 'Holy See (Vatican City)',
            ],
            'HN' => [
                'code' => '+504',
                'name' => 'Honduras',
            ],
            'HK' => [
                'code' => '+852',
                'name' => 'Hong Kong SAR China',
            ],
            'HU' => [
                'code' => '+36',
                'name' => 'Hungary',
            ],
            'IS' => [
                'code' => '+354',
                'name' => 'Iceland',
            ],
            'IN' => [
                'code' => '+91',
                'name' => 'India',
            ],
            'ID' => [
                'code' => '+62',
                'name' => 'Indonesia',
            ],
            'IR' => [
                'code' => '+98',
                'name' => 'Iran',
            ],
            'IQ' => [
                'code' => '+964',
                'name' => 'Iraq',
            ],
            'IE' => [
                'code' => '+353',
                'name' => 'Ireland',
            ],
            'IM' => [
                'code' => '+44',
                'name' => 'Isle of Man',
            ],
            'IL' => [
                'code' => '+972',
                'name' => 'Israel',
            ],
            'IT' => [
                'code' => '+39',
                'name' => 'Italy',
            ],
            'JM' => [
                'code' => '+1876',
                'name' => 'Jamaica',
            ],
            'JP' => [
                'code' => '+81',
                'name' => 'Japan',
            ],
            'JO' => [
                'code' => '+962',
                'name' => 'Jordan',
            ],
            'KZ' => [
                'code' => '+7',
                'name' => 'Kazakhstan',
            ],
            'KE' => [
                'code' => '+254',
                'name' => 'Kenya',
            ],
            'KI' => [
                'code' => '+686',
                'name' => 'Kiribati',
            ],
            'KW' => [
                'code' => '+965',
                'name' => 'Kuwait',
            ],
            'KG' => [
                'code' => '+996',
                'name' => 'Kyrgyzstan',
            ],
            'LA' => [
                'code' => '+856',
                'name' => 'Laos',
            ],
            'LV' => [
                'code' => '+371',
                'name' => 'Latvia',
            ],
            'LB' => [
                'code' => '+961',
                'name' => 'Lebanon',
            ],
            'LS' => [
                'code' => '+266',
                'name' => 'Lesotho',
            ],
            'LR' => [
                'code' => '+231',
                'name' => 'Liberia',
            ],
            'LY' => [
                'code' => '+218',
                'name' => 'Libya',
            ],
            'LI' => [
                'code' => '+423',
                'name' => 'Liechtenstein',
            ],
            'LT' => [
                'code' => '+370',
                'name' => 'Lithuania',
            ],
            'LU' => [
                'code' => '+352',
                'name' => 'Luxembourg',
            ],
            'MO' => [
                'code' => '+853',
                'name' => 'Macau SAR China',
            ],
            'MK' => [
                'code' => '+389',
                'name' => 'Macedonia',
            ],
            'MG' => [
                'code' => '+261',
                'name' => 'Madagascar',
            ],
            'MW' => [
                'code' => '+265',
                'name' => 'Malawi',
            ],
            'MY' => [
                'code' => '+60',
                'name' => 'Malaysia',
            ],
            'MV' => [
                'code' => '+960',
                'name' => 'Maldives',
            ],
            'ML' => [
                'code' => '+223',
                'name' => 'Mali',
            ],
            'MT' => [
                'code' => '+356',
                'name' => 'Malta',
            ],
            'MH' => [
                'code' => '+692',
                'name' => 'Marshall Islands',
            ],
            'MQ' => [
                'code' => '+596',
                'name' => 'Martinique',
            ],
            'MR' => [
                'code' => '+222',
                'name' => 'Mauritania',
            ],
            'MU' => [
                'code' => '+230',
                'name' => 'Mauritius',
            ],
            'YT' => [
                'code' => '+262',
                'name' => 'Mayotte',
            ],
            'MX' => [
                'code' => '+52',
                'name' => 'Mexico',
            ],
            'FM' => [
                'code' => '+691',
                'name' => 'Micronesia, Federated States Of',
            ],
            'MI' => [
                'code' => '+1808',
                'name' => 'Midway Island',
            ],
            'MD' => [
                'code' => '+373',
                'name' => 'Moldova',
            ],
            'MC' => [
                'code' => '+377',
                'name' => 'Monaco',
            ],
            'MN' => [
                'code' => '+976',
                'name' => 'Mongolia',
            ],
            'ME' => [
                'code' => '+382',
                'name' => 'Montenegro',
            ],
            'MS' => [
                'code' => '+1664',
                'name' => 'Montserrat',
            ],
            'MA' => [
                'code' => '+212',
                'name' => 'Morocco',
            ],
            'MZ' => [
                'code' => '+258',
                'name' => 'Mozambique',
            ],
            'MM' => [
                'code' => '+95',
                'name' => 'Myanmar',
            ],
            'NA' => [
                'code' => '+264',
                'name' => 'Namibia',
            ],
            'NR' => [
                'code' => '+674',
                'name' => 'Nauru',
            ],
            'NP' => [
                'code' => '+977',
                'name' => 'Nepal',
            ],
            'NL' => [
                'code' => '+31',
                'name' => 'Netherlands',
            ],
            'AN' => [
                'code' => '+599',
                'name' => 'Netherlands Antilles',
            ],
            'NC' => [
                'code' => '+687',
                'name' => 'New Caledonia',
            ],
            'NZ' => [
                'code' => '+64',
                'name' => 'New Zealand',
            ],
            'NI' => [
                'code' => '+505',
                'name' => 'Nicaragua',
            ],
            'NE' => [
                'code' => '+227',
                'name' => 'Niger',
            ],
            'NG' => [
                'code' => '+234',
                'name' => 'Nigeria',
            ],
            'NU' => [
                'code' => '+683',
                'name' => 'Niue',
            ],
            'NF' => [
                'code' => '+672',
                'name' => 'Norfolk Island',
            ],
            'KP' => [
                'code' => '+850',
                'name' => 'North Korea',
            ],
            'MP' => [
                'code' => '+1670',
                'name' => 'Northern Mariana Islands',
            ],
            'NO' => [
                'code' => '+47',
                'name' => 'Norway',
            ],
            'OM' => [
                'code' => '+968',
                'name' => 'Oman',
            ],
            'PK' => [
                'code' => '+92',
                'name' => 'Pakistan',
            ],
            'PW' => [
                'code' => '+680',
                'name' => 'Palau',
            ],
            'PA' => [
                'code' => '+507',
                'name' => 'Panama',
            ],
            'PG' => [
                'code' => '+675',
                'name' => 'Papua New Guinea',
            ],
            'PY' => [
                'code' => '+595',
                'name' => 'Paraguay',
            ],
            'PE' => [
                'code' => '+51',
                'name' => 'Peru',
            ],
            'PH' => [
                'code' => '+63',
                'name' => 'Philippines',
            ],
            'PN' => [
                'code' => '+870',
                'name' => 'Pitcairn Islands',
            ],
            'PL' => [
                'code' => '+48',
                'name' => 'Poland',
            ],
            'PT' => [
                'code' => '+351',
                'name' => 'Portugal',
            ],
            'PR' => [
                'code' => '+1787',
                'name' => 'Puerto Rico',
            ],
            'QA' => [
                'code' => '+974',
                'name' => 'Qatar',
            ],
            'RE' => [
                'code' => '+262',
                'name' => 'Reunion',
            ],
            'RO' => [
                'code' => '+40',
                'name' => 'Romania',
            ],
            'RU' => [
                'code' => '+7',
                'name' => 'Russia',
            ],
            'RW' => [
                'code' => '+250',
                'name' => 'Rwanda',
            ],
            'BL' => [
                'code' => '+590',
                'name' => 'Saint Barthelemy',
            ],
            'SH' => [
                'code' => '+290',
                'name' => 'Saint Helena',
            ],
            'KN' => [
                'code' => '+1869',
                'name' => 'Saint Kitts and Nevis',
            ],
            'LC' => [
                'code' => '+1758',
                'name' => 'Saint Lucia',
            ],
            'MF' => [
                'code' => '+1',
                'name' => 'Saint Martin',
            ],
            'PM' => [
                'code' => '+508',
                'name' => 'Saint Pierre and Miquelon',
            ],
            'ST' => [
                'code' => '+239',
                'name' => 'Saint tome and principle',
            ],
            'VC' => [
                'code' => '+1784',
                'name' => 'Saint Vincent and the Grenadines',
            ],
            'WS' => [
                'code' => '+684',
                'name' => 'Samoa',
            ],
            'SM' => [
                'code' => '+378',
                'name' => 'San Marino',
            ],
            'SA' => [
                'code' => '+966',
                'name' => 'Saudi Arabia',
            ],
            'SN' => [
                'code' => '+221',
                'name' => 'Senegal',
            ],
            'RS' => [
                'code' => '+381',
                'name' => 'Serbia',
            ],
            'SC' => [
                'code' => '+248',
                'name' => 'Seychelles',
            ],
            'SL' => [
                'code' => '+232',
                'name' => 'Sierra Leone',
            ],
            'SG' => [
                'code' => '+65',
                'name' => 'Singapore',
            ],
            'SX' => [
                'code' => '+721',
                'name' => 'Sint Maarten',
            ],
            'SK' => [
                'code' => '+421',
                'name' => 'Slovakia',
            ],
            'SI' => [
                'code' => '+386',
                'name' => 'Slovenia',
            ],
            'SB' => [
                'code' => '+677',
                'name' => 'Solomon Islands',
            ],
            'ZA' => [
                'code' => '+27',
                'name' => 'South Africa',
            ],
            'GS' => [
                'code' => '+500',
                'name' => 'South Georgia and the South Sandwich Islands',
            ],
            'KR' => [
                'code' => '+82',
                'name' => 'South Korea',
            ],
            'SS' => [
                'code' => '+211',
                'name' => 'South Sudan',
            ],
            'ES' => [
                'code' => '+34',
                'name' => 'Spain',
            ],
            'LK' => [
                'code' => '+94',
                'name' => 'Sri Lanka',
            ],
            'SD' => [
                'code' => '+249',
                'name' => 'Sudan',
            ],
            'SR' => [
                'code' => '+597',
                'name' => 'Suriname',
            ],
            'SJ' => [
                'code' => '+47',
                'name' => 'Svalbard',
            ],
            'SZ' => [
                'code' => '+268',
                'name' => 'Swaziland',
            ],
            'SE' => [
                'code' => '+46',
                'name' => 'Sweden',
            ],
            'CH' => [
                'code' => '+41',
                'name' => 'Switzerland',
            ],
            'SY' => [
                'code' => '+963',
                'name' => 'Syria',
            ],
            'TW' => [
                'code' => '+886',
                'name' => 'Taiwan',
            ],
            'TJ' => [
                'code' => '+992',
                'name' => 'Tajikistan',
            ],
            'TZ' => [
                'code' => '+255',
                'name' => 'Tanzania',
            ],
            'TH' => [
                'code' => '+66',
                'name' => 'Thailand',
            ],
            'TG' => [
                'code' => '+228',
                'name' => 'Togo',
            ],
            'TK' => [
                'code' => '+690',
                'name' => 'Tokelau',
            ],
            'TO' => [
                'code' => '+676',
                'name' => 'Tonga',
            ],
            'TT' => [
                'code' => '+1868',
                'name' => 'Trinidad and Tobago',
            ],
            'TN' => [
                'code' => '+216',
                'name' => 'Tunisia',
            ],
            'TR' => [
                'code' => '+90',
                'name' => 'Turkey',
            ],
            'TM' => [
                'code' => '+7370',
                'name' => 'Turkmenistan',
            ],
            'TC' => [
                'code' => '+1649',
                'name' => 'Turks and Caicos Islands',
            ],
            'TV' => [
                'code' => '+688',
                'name' => 'Tuvalu',
            ],
            'UG' => [
                'code' => '+256',
                'name' => 'Uganda',
            ],
            'UA' => [
                'code' => '+380',
                'name' => 'Ukraine',
            ],
            'AE' => [
                'code' => '+971',
                'name' => 'United Arab Emirates',
            ],
            'GB' => [
                'code' => '+44',
                'name' => 'United Kingdom',
            ],
            'UM' => [
                'code' => '+1',
                'name' => 'United States Minor Outlying Islands',
            ],
            'US' => [
                'code' => '+1',
                'name' => 'United States',
            ],
            'UY' => [
                'code' => '+598',
                'name' => 'Uruguay',
            ],
            'UZ' => [
                'code' => '+998',
                'name' => 'Uzbekistan',
            ],
            'VU' => [
                'code' => '+678',
                'name' => 'Vanuatu',
            ],
            'VE' => [
                'code' => '+58',
                'name' => 'Venezuela',
            ],
            'VN' => [
                'code' => '+84',
                'name' => 'Vietnam',
            ],
            'VI' => [
                'code' => '+1340',
                'name' => 'Virgin Islands',
            ],
            'WF' => [
                'code' => '+681',
                'name' => 'Wallis and Futuna',
            ],
            'EH' => [
                'code' => '+212',
                'name' => 'Western Sahara',
            ],
            'YE' => [
                'code' => '+967',
                'name' => 'Yemen',
            ],
            'ZM' => [
                'code' => '+260',
                'name' => 'Zambia',
            ],
            'ZW' => [
                'code' => '+263',
                'name' => 'Zimbabwe',
            ],
            'ZZ' => [
                'code' => '+',
                'name' => 'Unknown or unspecified country',
            ],
        ];

        $result = [];

        foreach ($phone_numbers as $isocode => $country) {
            $data = [
                'code' => $country['code'],
                'country' => $country['name'],
                'flag' => '',
                'iso2' => $isocode,
            ];

            if (isset($flags[$isocode])) {
                $data['flag'] = $flags[$isocode];
            }

            array_push($result, $data);
        }

        return $result;
    }

    public static function languageCodes()
    {
        $languages = [
            'aa' => 'Afar',
            'ab' => 'Abkhazian',
            'af' => 'Afrikaans',
            'am' => 'Amharic',
            'ar' => 'Arabic',
            'ar-ae' => 'Arabic (U.A.E.)',
            'ar-bh' => 'Arabic (Bahrain)',
            'ar-dz' => 'Arabic (Algeria)',
            'ar-eg' => 'Arabic (Egypt)',
            'ar-iq' => 'Arabic (Iraq)',
            'ar-jo' => 'Arabic (Jordan)',
            'ar-kw' => 'Arabic (Kuwait)',
            'ar-lb' => 'Arabic (Lebanon)',
            'ar-ly' => 'Arabic (libya)',
            'ar-ma' => 'Arabic (Morocco)',
            'ar-om' => 'Arabic (Oman)',
            'ar-qa' => 'Arabic (Qatar)',
            'ar-sa' => 'Arabic (Saudi Arabia)',
            'ar-sy' => 'Arabic (Syria)',
            'ar-tn' => 'Arabic (Tunisia)',
            'ar-ye' => 'Arabic (Yemen)',
            'as' => 'Assamese',
            'ay' => 'Aymara',
            'az' => 'Azeri',
            'ba' => 'Bashkir',
            'be' => 'Belarusian',
            'bg' => 'Bulgarian',
            'bh' => 'Bihari',
            'bi' => 'Bislama',
            'bn' => 'Bengali',
            'bo' => 'Tibetan',
            'br' => 'Breton',
            'ca' => 'Catalan',
            'co' => 'Corsican',
            'cs' => 'Czech',
            'cy' => 'Welsh',
            'da' => 'Danish',
            'de' => 'German',
            'de-at' => 'German (Austria)',
            'de-ch' => 'German (Switzerland)',
            'de-li' => 'German (Liechtenstein)',
            'de-lu' => 'German (Luxembourg)',
            'div' => 'Divehi',
            'dz' => 'Bhutani',
            'el' => 'Greek',
            'en' => 'English',
            'en-au' => 'English (Australia)',
            'en-bz' => 'English (Belize)',
            'en-ca' => 'English (Canada)',
            'en-gb' => 'English (United Kingdom)',
            'en-ie' => 'English (Ireland)',
            'en-jm' => 'English (Jamaica)',
            'en-nz' => 'English (New Zealand)',
            'en-ph' => 'English (Philippines)',
            'en-tt' => 'English (Trinidad)',
            'en-us' => 'English (United States)',
            'en-za' => 'English (South Africa)',
            'en-zw' => 'English (Zimbabwe)',
            'eo' => 'Esperanto',
            'es' => 'Spanish',
            'es-ar' => 'Spanish (Argentina)',
            'es-bo' => 'Spanish (Bolivia)',
            'es-cl' => 'Spanish (Chile)',
            'es-co' => 'Spanish (Colombia)',
            'es-cr' => 'Spanish (Costa Rica)',
            'es-do' => 'Spanish (Dominican Republic)',
            'es-ec' => 'Spanish (Ecuador)',
            'es-es' => 'Spanish (España)',
            'es-gt' => 'Spanish (Guatemala)',
            'es-hn' => 'Spanish (Honduras)',
            'es-mx' => 'Spanish (Mexico)',
            'es-ni' => 'Spanish (Nicaragua)',
            'es-pa' => 'Spanish (Panama)',
            'es-pe' => 'Spanish (Peru)',
            'es-pr' => 'Spanish (Puerto Rico)',
            'es-py' => 'Spanish (Paraguay)',
            'es-sv' => 'Spanish (El Salvador)',
            'es-us' => 'Spanish (United States)',
            'es-uy' => 'Spanish (Uruguay)',
            'es-ve' => 'Spanish (Venezuela)',
            'et' => 'Estonian',
            'eu' => 'Basque',
            'fa' => 'Farsi',
            'fi' => 'Finnish',
            'fj' => 'Fiji',
            'fo' => 'Faeroese',
            'fr' => 'French',
            'fr-be' => 'French (Belgium)',
            'fr-ca' => 'French (Canada)',
            'fr-ch' => 'French (Switzerland)',
            'fr-lu' => 'French (Luxembourg)',
            'fr-mc' => 'French (Monaco)',
            'fy' => 'Frisian',
            'ga' => 'Irish',
            'gd' => 'Gaelic',
            'gl' => 'Galician',
            'gn' => 'Guarani',
            'gu' => 'Gujarati',
            'ha' => 'Hausa',
            'he' => 'Hebrew',
            'hi' => 'Hindi',
            'hr' => 'Croatian',
            'hu' => 'Hungarian',
            'hy' => 'Armenian',
            'ia' => 'Interlingua',
            'id' => 'Indonesian',
            'ie' => 'Interlingue',
            'ik' => 'Inupiak',
            'in' => 'Indonesian',
            'is' => 'Icelandic',
            'it' => 'Italian',
            'it-ch' => 'Italian (Switzerland)',
            'iw' => 'Hebrew',
            'ja' => 'Japanese',
            'ji' => 'Yiddish',
            'jw' => 'Javanese',
            'ka' => 'Georgian',
            'kk' => 'Kazakh',
            'kl' => 'Greenlandic',
            'km' => 'Cambodian',
            'kn' => 'Kannada',
            'ko' => 'Korean',
            'kok' => 'Konkani',
            'ks' => 'Kashmiri',
            'ku' => 'Kurdish',
            'ky' => 'Kirghiz',
            'kz' => 'Kyrgyz',
            'la' => 'Latin',
            'ln' => 'Lingala',
            'lo' => 'Laothian',
            'ls' => 'Slovenian',
            'lt' => 'Lithuanian',
            'lv' => 'Latvian',
            'mg' => 'Malagasy',
            'mi' => 'Maori',
            'mk' => 'FYRO Macedonian',
            'ml' => 'Malayalam',
            'mn' => 'Mongolian',
            'mo' => 'Moldavian',
            'mr' => 'Marathi',
            'ms' => 'Malay',
            'mt' => 'Maltese',
            'my' => 'Burmese',
            'na' => 'Nauru',
            'nb-no' => 'Norwegian (Bokmal)',
            'ne' => 'Nepali (India)',
            'nl' => 'Dutch',
            'nl-be' => 'Dutch (Belgium)',
            'nn-no' => 'Norwegian',
            'no' => 'Norwegian (Bokmal)',
            'oc' => 'Occitan',
            'om' => '(Afan)/Oromoor/Oriya',
            'or' => 'Oriya',
            'pa' => 'Punjabi',
            'pl' => 'Polish',
            'ps' => 'Pashto/Pushto',
            'pt' => 'Portuguese',
            'pt-br' => 'Portuguese (Brazil)',
            'qu' => 'Quechua',
            'rm' => 'Rhaeto-Romanic',
            'rn' => 'Kirundi',
            'ro' => 'Romanian',
            'ro-md' => 'Romanian (Moldova)',
            'ru' => 'Russian',
            'ru-md' => 'Russian (Moldova)',
            'rw' => 'Kinyarwanda',
            'sa' => 'Sanskrit',
            'sb' => 'Sorbian',
            'sd' => 'Sindhi',
            'sg' => 'Sangro',
            'sh' => 'Serbo-Croatian',
            'si' => 'Singhalese',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'sm' => 'Samoan',
            'sn' => 'Shona',
            'so' => 'Somali',
            'sq' => 'Albanian',
            'sr' => 'Serbian',
            'ss' => 'Siswati',
            'st' => 'Sesotho',
            'su' => 'Sundanese',
            'sv' => 'Swedish',
            'sv-fi' => 'Swedish (Finland)',
            'sw' => 'Swahili',
            'sx' => 'Sutu',
            'syr' => 'Syriac',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'tg' => 'Tajik',
            'th' => 'Thai',
            'ti' => 'Tigrinya',
            'tk' => 'Turkmen',
            'tl' => 'Tagalog',
            'tn' => 'Tswana',
            'to' => 'Tonga',
            'tr' => 'Turkish',
            'ts' => 'Tsonga',
            'tt' => 'Tatar',
            'tw' => 'Twi',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'us' => 'English',
            'uz' => 'Uzbek',
            'vi' => 'Vietnamese',
            'vo' => 'Volapuk',
            'wo' => 'Wolof',
            'xh' => 'Xhosa',
            'yi' => 'Yiddish',
            'yo' => 'Yoruba',
            'zh' => 'Chinese',
            'zh-cn' => 'Chinese (China)',
            'zh-hk' => 'Chinese (Hong Kong SAR)',
            'zh-mo' => 'Chinese (Macau SAR)',
            'zh-sg' => 'Chinese (Singapore)',
            'zh-tw' => 'Chinese (Taiwan)',
            'zu' => 'Zulu',
        ];

        return collect($languages);
    }
}
