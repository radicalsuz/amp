<?php
if ( !defined( 'AMP_REGION_US_INCLUDE_INTERNATIONAL'))
    define( 'AMP_REGION_US_INCLUDE_INTERNATIONAL', false );

class Region {

    var $region;
    var $regions;

    function Region ( $region = null ) {

        if ( isset( $region ) ) {

            $this->region = $region;

        }

        $this->init();

    }

    function &instance( ){
        static $regionObj = false;
        if ( !$regionObj ) $regionObj = new Region( ) ;
        return $regionObj;
    }
    
    function init () {

        $this->regions = array(
    
        'WORLD' => array( 
          'USA' => 'United States',         'CAN' => 'Canada',              'GBR' => 'United Kingdom',
          'AFG' => 'Afghanistan',           'ALB' => 'Albania',             'DZA' => 'Algeria',
          'ASM' => 'American Samoa',        'AND' => 'Andorra',             'AGO' => 'Angola',
          'AIA' => 'Anguilla',              'ATG' => 'Antigua and Barbuda', 'ARG' => 'Argentina',
          'ARM' => 'Armenia',               'ABW' => 'Aruba',               'AUS' => 'Australia',
          'AUT' => 'Austria',               'AZE' => 'Azerbaijan',          'BHS' => 'Bahamas',
          'BHR' => 'Bahrain',               'BGD' => 'Bangladesh',          'BRB' => 'Barbados',
          'BLR' => 'Belarus',               'BEL' => 'Belgium',             'BLZ' => 'Belize',
          'BEN' => 'Benin',                 'BMU' => 'Bermuda',             'BTN' => 'Bhutan',
          'BOL' => 'Bolivia',               'BIH' => 'Bosnia and Herzegovina',      'BWA' => 'Botswana',
          'BRA' => 'Brazil',                'VGB' => 'British Virgin Islands',      'BRN' => 'Brunei Darussalam',
          'BGR' => 'Bulgaria',              'BFA' => 'Burkina Faso',        'BDI' => 'Burundi',
          'KHM' => 'Cambodia',              'CMR' => 'Cameroon',            'CAN' => 'Canada',
          'CPV' => 'Cape Verde',            'CYM' => 'Cayman Islands',      'CAF' => 'Central African Republic',
          'TCD' => 'Chad',                  'CHL' => 'Chile',               'CHN' => 'China',
          'COL' => 'Colombia',              'COM' => 'Comoros',             'COG' => 'Congo',
          'COK' => 'Cook Islands',          'CRI' => 'Costa Rica',          'CIV' => 'Cote d\'Ivoire',
          'HRV' => 'Croatia',               'CUB' => 'Cuba',                'CYP' => 'Cyprus',
          'CZE' => 'Czech Republic',        'DNK' => 'Denmark',             'DJI' => 'Djibouti',
          'DMA' => 'Dominica',              'DOM' => 'Dominican Republic',  'TMP' => 'East Timor',
          'ECU' => 'Ecuador',               'EGY' => 'Egypt',               'SLV' => 'El Salvador',
          'GNQ' => 'Equatorial Guinea',     'ERI' => 'Eritrea',             'EST' => 'Estonia',
          'ETH' => 'Ethiopia',              'FRO' => 'Faeroe Islands',      'FLK' => 'Falkland Islands',
          'FJI' => 'Fiji',                  'FIN' => 'Finland',             'FRA' => 'France',
          'GUF' => 'French Guiana',         'PYF' => 'French Polynesia',    'GAB' => 'Gabon',
          'GMB' => 'Gambia',                'GEO' => 'Georgia',             'DEU' => 'Germany',
          'GHA' => 'Ghana',                 'GIB' => 'Gibraltar',           'GRC' => 'Greece',
          'GRL' => 'Greenland',             'GRD' => 'Grenada',             'GLP' => 'Guadeloupe',
          'GUM' => 'Guam',                  'GTM' => 'Guatemala',           'GIN' => 'Guinea',
          'GNB' => 'Guinea-Bissau',         'GUY' => 'Guyana',              'HTI' => 'Haiti',
          'VAT' => 'Holy See',              'HND' => 'Honduras',            'HKG' => 'Hong Kong',
          'HUN' => 'Hungary',               'ISL' => 'Iceland',             'IND' => 'India',
          'IDN' => 'Indonesia',             'IRN' => 'Iran',                'IRQ' => 'Iraq',
          'IRL' => 'Ireland',               'ISR' => 'Israel',              'ITA' => 'Italy',
          'JAM' => 'Jamaica',               'JPN' => 'Japan',               'JOR' => 'Jordan',
          'KAZ' => 'Kazakhstan',            'KEN' => 'Kenya',               'KIR' => 'Kiribati',
          'KWT' => 'Kuwait',                'KGZ' => 'Kyrgyzstan',          'LAO' => 'Lao',
          'LVA' => 'Latvia',                'LBN' => 'Lebanon',             'LSO' => 'Lesotho',
          'LBR' => 'Liberia',               'LBY' => 'Libyan Arab Jamahiriya',      'LIE' => 'Liechtenstein',
          'LTU' => 'Lithuania',             'LUX' => 'Luxembourg',          'MAC' => 'Macao',
          'MKD' => 'Macedonia',             'MDG' => 'Madagascar',          'MWI' => 'Malawi',
          'MYS' => 'Malaysia',              'MDV' => 'Maldives',            'MLI' => 'Mali',
          'MLT' => 'Malta',                 'MHL' => 'Marshall Islands',    'MTQ' => 'Martinique',
          'MRT' => 'Mauritania',            'MUS' => 'Mauritius',           'MEX' => 'Mexico',
          'FSM' => 'Micronesia',            'MCO' => 'Monaco',              'MNG' => 'Mongolia',
          'MSR' => 'Montserrat',            'MAR' => 'Morocco',             'MOZ' => 'Mozambique',
          'MMR' => 'Myanmar',               'NAM' => 'Namibia',             'NRU' => 'Nauru',
          'NPL' => 'Nepal',                 'NLD' => 'Netherlands',         'ANT' => 'Netherlands Antilles',
          'NCL' => 'New Caledonia',         'NZL' => 'New Zealand',         'NIC' => 'Nicaragua',
          'NER' => 'Niger',                 'NGA' => 'Nigeria',             'NIU' => 'Niue',
          'NFK' => 'Norfolk Island',        'PRK' => 'North Korea',         'MNP' => 'Northern Mariana Islands',
          'NOR' => 'Norway',                'OMN' => 'Oman',                'PAK' => 'Pakistan',
          'PLW' => 'Palau',                 'PSE' => 'Palestinian Territory',       'PAN' => 'Panama',
          'PNG' => 'Papua New Guinea',      'PRY' => 'Paraguay',            'PER' => 'Peru',
          'PHL' => 'Philippines',           'PCN' => 'Pitcairn',            'POL' => 'Poland',
          'PRT' => 'Portugal',              'PRI' => 'Puerto Rico',         'QAT' => 'Qatar',
          'MDA' => 'Republic of Moldova',   'REU' => 'Réunion',             'ROM' => 'Romania',
          'RUS' => 'Russian Federation',    'RWA' => 'Rwanda',              'SHN' => 'Saint Helena',
          'KNA' => 'Saint Kitts and Nevis', 'LCA' => 'Saint Lucia',         'SPM' => 'Saint Pierre and Miquelon',
          'VCT' => 'Saint Vincent/Grenadines',      'WSM' => 'Samoa',       'SMR' => 'San Marino',
          'STP' => 'Sao Tome and Principe', 'SAU' => 'Saudi Arabia',        'SEN' => 'Senegal',
          'SYC' => 'Seychelles',            'SLE' => 'Sierra Leone',        'SGP' => 'Singapore',
          'SVK' => 'Slovakia',              'SVN' => 'Slovenia',            'SLB' => 'Solomon Islands',
          'SOM' => 'Somalia',               'ZAF' => 'South Africa',        'KOR' => 'South Korea',
          'ESP' => 'Spain',                 'LKA' => 'Sri Lanka',           'SDN' => 'Sudan',
          'SUR' => 'Suriname',              'SWZ' => 'Swaziland',           'SWE' => 'Sweden',
          'CHE' => 'Switzerland',           'SYR' => 'Syrian Arab Republic','TWN' => 'Taiwan Province of China',
          'TJK' => 'Tajikistan',            'TZA' => 'Tanzania',            'THA' => 'Thailand',
          'TGO' => 'Togo',                  'TKL' => 'Tokelau',             'TON' => 'Tonga',
          'TTO' => 'Trinidad and Tobago',   'TUN' => 'Tunisia',             'TUR' => 'Turkey',
          'TKM' => 'Turkmenistan',          'TCA' => 'Turks and Caicos Islands',    'TUV' => 'Tuvalu',
          'UGA' => 'Uganda',                'UKR' => 'Ukraine',             'ARE' => 'United Arab Emirates',
          'GBR' => 'United Kingdom',        'USA' => 'United States',       'URY' => 'Uruguay',
          'VIR' => 'US Virgin Islands',     'UZB' => 'Uzbekistan',          'VUT' => 'Vanuatu',
          'VEN' => 'Venezuela',             'VNM' => 'Viet Nam',            'WLF' => 'Wallis and Futuna Islands',
          'ESH' => 'Western Sahara',        'YEM' => 'Yemen',               'YUG' => 'Yugoslavia',
          'ZMB' => 'Zambia',                'ZWE' => 'Zimbabwe' ),
    
        'US' => array(
          'AL' => 'Alabama',                'AK' => 'Alaska',               'AZ' => 'Arizona',
          'AR' => 'Arkansas',               'CA' => 'California',           'CO' => 'Colorado',
          'CT' => 'Connecticut',            'DE' => 'Delaware',             'DC' => 'District of Columbia',
          'FL' => 'Florida',                'GA' => 'Georgia',              'HI' => 'Hawaii',
          'ID' => 'Idaho',                  'IL' => 'Illinois',             'IN' => 'Indiana',
          'IA' => 'Iowa',                   'KS' => 'Kansas',               'KY' => 'Kentucky',
          'LA' => 'Louisiana',              'ME' => 'Maine',                'MD' => 'Maryland',
          'MA' => 'Massachusetts',          'MI' => 'Michigan',             'MN' => 'Minnesota',
          'MS' => 'Mississippi',            'MO' => 'Missouri',             'MT' => 'Montana',
          'NE' => 'Nebraska',               'NV' => 'Nevada',               'NH' => 'New Hampshire',
          'NJ' => 'New Jersey',             'NM' => 'New Mexico',           'NY' => 'New York',
          'NC' => 'North Carolina',         'ND' => 'North Dakota',         'OH' => 'Ohio',
          'OK' => 'Oklahoma',               'OR' => 'Oregon',               'PA' => 'Pennsylvania',
          'PR' => 'Puerto Rico',            'RI' => 'Rhode Island',         'SC' => 'South Carolina',
          'SD' => 'South Dakota',           'TN' => 'Tennessee',            'TX' => 'Texas',
          'UT' => 'Utah',                   'VT' => 'Vermont',              'VA' => 'Virginia',
          'WA' => 'Washington',             'WV' => 'West Virginia',        'WI' => 'Wisconsin',
          'WY' => 'Wyoming' ),
                    
        'CDN' => array(
          'AB' => 'Alberta',        'BC' => 'British Columbia',     'MB' => 'Manitoba',
          'NB' => 'New Brunswick',  'NF' => 'Newfoundland',         'NT' => 'Northwest Territories',
          'NS' => 'Nova Scotia',    'NU' => 'Nunavut',              'ON' => 'Ontario',
          'PE' => 'P.E.I',          'PQ' => 'Quebec',               'SK' => 'Saskatchewan',
          'YK' => 'Yukon Territory' ),

        'WORLD-LONG' => array( ),
    
        );

        foreach ( array_values( $this->regions[ 'WORLD' ] ) as $country ) {
            $this->regions[ 'WORLD-LONG' ][ $country ] = $country;
        };
        if (  AMP_REGION_US_INCLUDE_INTERNATIONAL ){
            $international_values = array( 
                  'INTL' => 'International',
                  ' ' => '---' );
            $this->regions[ 'US' ] =  $international_values + $this->regions[ 'US' ];
            #$this->regions[ 'US' ] = array_merge( $international_values, $this->regions[ 'US' ]);
        }

	$this->regions[ 'US AND CANADA'] = $this->regions[ 'US' ] + array( "---" ) + $this->regions[ 'CDN' ];

        return 1;

    }

    /*****
     *
     * array getSubRegions( string parentRegion )
     *
     * Returns an array of regions contained within parentRegion.
     *
     *****/
    
    function getSubRegions( $parentRegion = null ) {

        if ( !isset( $parentRegion ) ) {
            if ( isset( $this->region ) ) {
                $parentRegion = $this->region;
            } else {
                return null;
            }
        }

        $regions =& $this->regions;
    
        if ( isset( $regions[ $parentRegion ] ) ) {
    
            return $regions[ $parentRegion ];
    
        } else {
    
            return null;
    
        }
    
    }

    /*****
     *
     * array getTLRegions( string parent )
     *
     * Returns an array of the available top-level regions.
     *
     * Once the region thing is built out, this function should take an
     * optional argument of the parent region, to return parent regions
     * in a given scope (e.g., Top-Level regions within the united states
     * would be the states themselves.)
     *
     *****/

    function getTLRegions( $parentRegion = null ) {

        $retArray = array();

        foreach ( array_keys( $this->regions ) as $tlRegion ) {

            $retArray[ $tlRegion ] = ucfirst( strtolower( $tlRegion ) );

        }

        return $retArray;

    }

}
    
?>
