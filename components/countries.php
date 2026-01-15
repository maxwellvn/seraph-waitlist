<?php
/**
 * Country Codes Component
 * Returns array of country codes for phone number input
 */

function getCountryCodes() {
    return [
        ['code' => 'US', 'name' => 'United States', 'dial_code' => '+1'],
        ['code' => 'GB', 'name' => 'United Kingdom', 'dial_code' => '+44'],
        ['code' => 'CA', 'name' => 'Canada', 'dial_code' => '+1'],
        ['code' => 'AU', 'name' => 'Australia', 'dial_code' => '+61'],
        ['code' => 'NZ', 'name' => 'New Zealand', 'dial_code' => '+64'],
        ['code' => 'IN', 'name' => 'India', 'dial_code' => '+91'],
        ['code' => 'PK', 'name' => 'Pakistan', 'dial_code' => '+92'],
        ['code' => 'BD', 'name' => 'Bangladesh', 'dial_code' => '+880'],
        ['code' => 'NG', 'name' => 'Nigeria', 'dial_code' => '+234'],
        ['code' => 'ZA', 'name' => 'South Africa', 'dial_code' => '+27'],
        ['code' => 'KE', 'name' => 'Kenya', 'dial_code' => '+254'],
        ['code' => 'GH', 'name' => 'Ghana', 'dial_code' => '+233'],
        ['code' => 'UG', 'name' => 'Uganda', 'dial_code' => '+256'],
        ['code' => 'TZ', 'name' => 'Tanzania', 'dial_code' => '+255'],
        ['code' => 'ET', 'name' => 'Ethiopia', 'dial_code' => '+251'],
        ['code' => 'EG', 'name' => 'Egypt', 'dial_code' => '+20'],
        ['code' => 'MA', 'name' => 'Morocco', 'dial_code' => '+212'],
        ['code' => 'DZ', 'name' => 'Algeria', 'dial_code' => '+213'],
        ['code' => 'TN', 'name' => 'Tunisia', 'dial_code' => '+216'],
        ['code' => 'LY', 'name' => 'Libya', 'dial_code' => '+218'],
        ['code' => 'SD', 'name' => 'Sudan', 'dial_code' => '+249'],
        ['code' => 'CM', 'name' => 'Cameroon', 'dial_code' => '+237'],
        ['code' => 'CI', 'name' => 'Ivory Coast', 'dial_code' => '+225'],
        ['code' => 'SN', 'name' => 'Senegal', 'dial_code' => '+221'],
        ['code' => 'ML', 'name' => 'Mali', 'dial_code' => '+223'],
        ['code' => 'BF', 'name' => 'Burkina Faso', 'dial_code' => '+226'],
        ['code' => 'NE', 'name' => 'Niger', 'dial_code' => '+227'],
        ['code' => 'TD', 'name' => 'Chad', 'dial_code' => '+235'],
        ['code' => 'ZW', 'name' => 'Zimbabwe', 'dial_code' => '+263'],
        ['code' => 'ZM', 'name' => 'Zambia', 'dial_code' => '+260'],
        ['code' => 'MW', 'name' => 'Malawi', 'dial_code' => '+265'],
        ['code' => 'MZ', 'name' => 'Mozambique', 'dial_code' => '+258'],
        ['code' => 'BW', 'name' => 'Botswana', 'dial_code' => '+267'],
        ['code' => 'NA', 'name' => 'Namibia', 'dial_code' => '+264'],
        ['code' => 'MU', 'name' => 'Mauritius', 'dial_code' => '+230'],
        ['code' => 'RW', 'name' => 'Rwanda', 'dial_code' => '+250'],
        ['code' => 'AE', 'name' => 'United Arab Emirates', 'dial_code' => '+971'],
        ['code' => 'SA', 'name' => 'Saudi Arabia', 'dial_code' => '+966'],
        ['code' => 'QA', 'name' => 'Qatar', 'dial_code' => '+974'],
        ['code' => 'KW', 'name' => 'Kuwait', 'dial_code' => '+965'],
        ['code' => 'BH', 'name' => 'Bahrain', 'dial_code' => '+973'],
        ['code' => 'OM', 'name' => 'Oman', 'dial_code' => '+968'],
        ['code' => 'JO', 'name' => 'Jordan', 'dial_code' => '+962'],
        ['code' => 'LB', 'name' => 'Lebanon', 'dial_code' => '+961'],
        ['code' => 'SY', 'name' => 'Syria', 'dial_code' => '+963'],
        ['code' => 'IQ', 'name' => 'Iraq', 'dial_code' => '+964'],
        ['code' => 'IR', 'name' => 'Iran', 'dial_code' => '+98'],
        ['code' => 'AF', 'name' => 'Afghanistan', 'dial_code' => '+93'],
        ['code' => 'CN', 'name' => 'China', 'dial_code' => '+86'],
        ['code' => 'JP', 'name' => 'Japan', 'dial_code' => '+81'],
        ['code' => 'KR', 'name' => 'South Korea', 'dial_code' => '+82'],
        ['code' => 'TH', 'name' => 'Thailand', 'dial_code' => '+66'],
        ['code' => 'VN', 'name' => 'Vietnam', 'dial_code' => '+84'],
        ['code' => 'PH', 'name' => 'Philippines', 'dial_code' => '+63'],
        ['code' => 'MY', 'name' => 'Malaysia', 'dial_code' => '+60'],
        ['code' => 'SG', 'name' => 'Singapore', 'dial_code' => '+65'],
        ['code' => 'ID', 'name' => 'Indonesia', 'dial_code' => '+62'],
        ['code' => 'MM', 'name' => 'Myanmar', 'dial_code' => '+95'],
        ['code' => 'KH', 'name' => 'Cambodia', 'dial_code' => '+855'],
        ['code' => 'LA', 'name' => 'Laos', 'dial_code' => '+856'],
        ['code' => 'NP', 'name' => 'Nepal', 'dial_code' => '+977'],
        ['code' => 'LK', 'name' => 'Sri Lanka', 'dial_code' => '+94'],
        ['code' => 'MV', 'name' => 'Maldives', 'dial_code' => '+960'],
        ['code' => 'BT', 'name' => 'Bhutan', 'dial_code' => '+975'],
        ['code' => 'BR', 'name' => 'Brazil', 'dial_code' => '+55'],
        ['code' => 'MX', 'name' => 'Mexico', 'dial_code' => '+52'],
        ['code' => 'AR', 'name' => 'Argentina', 'dial_code' => '+54'],
        ['code' => 'CO', 'name' => 'Colombia', 'dial_code' => '+57'],
        ['code' => 'CL', 'name' => 'Chile', 'dial_code' => '+56'],
        ['code' => 'PE', 'name' => 'Peru', 'dial_code' => '+51'],
        ['code' => 'VE', 'name' => 'Venezuela', 'dial_code' => '+58'],
        ['code' => 'EC', 'name' => 'Ecuador', 'dial_code' => '+593'],
        ['code' => 'BO', 'name' => 'Bolivia', 'dial_code' => '+591'],
        ['code' => 'PY', 'name' => 'Paraguay', 'dial_code' => '+595'],
        ['code' => 'UY', 'name' => 'Uruguay', 'dial_code' => '+598'],
        ['code' => 'GY', 'name' => 'Guyana', 'dial_code' => '+592'],
        ['code' => 'SR', 'name' => 'Suriname', 'dial_code' => '+597'],
        ['code' => 'DE', 'name' => 'Germany', 'dial_code' => '+49'],
        ['code' => 'FR', 'name' => 'France', 'dial_code' => '+33'],
        ['code' => 'IT', 'name' => 'Italy', 'dial_code' => '+39'],
        ['code' => 'ES', 'name' => 'Spain', 'dial_code' => '+34'],
        ['code' => 'PT', 'name' => 'Portugal', 'dial_code' => '+351'],
        ['code' => 'NL', 'name' => 'Netherlands', 'dial_code' => '+31'],
        ['code' => 'BE', 'name' => 'Belgium', 'dial_code' => '+32'],
        ['code' => 'CH', 'name' => 'Switzerland', 'dial_code' => '+41'],
        ['code' => 'AT', 'name' => 'Austria', 'dial_code' => '+43'],
        ['code' => 'SE', 'name' => 'Sweden', 'dial_code' => '+46'],
        ['code' => 'NO', 'name' => 'Norway', 'dial_code' => '+47'],
        ['code' => 'DK', 'name' => 'Denmark', 'dial_code' => '+45'],
        ['code' => 'FI', 'name' => 'Finland', 'dial_code' => '+358'],
        ['code' => 'IE', 'name' => 'Ireland', 'dial_code' => '+353'],
        ['code' => 'PL', 'name' => 'Poland', 'dial_code' => '+48'],
        ['code' => 'CZ', 'name' => 'Czech Republic', 'dial_code' => '+420'],
        ['code' => 'SK', 'name' => 'Slovakia', 'dial_code' => '+421'],
        ['code' => 'HU', 'name' => 'Hungary', 'dial_code' => '+36'],
        ['code' => 'RO', 'name' => 'Romania', 'dial_code' => '+40'],
        ['code' => 'BG', 'name' => 'Bulgaria', 'dial_code' => '+359'],
        ['code' => 'GR', 'name' => 'Greece', 'dial_code' => '+30'],
        ['code' => 'TR', 'name' => 'Turkey', 'dial_code' => '+90'],
        ['code' => 'RU', 'name' => 'Russia', 'dial_code' => '+7'],
        ['code' => 'UA', 'name' => 'Ukraine', 'dial_code' => '+380'],
        ['code' => 'BY', 'name' => 'Belarus', 'dial_code' => '+375'],
        ['code' => 'KZ', 'name' => 'Kazakhstan', 'dial_code' => '+7'],
        ['code' => 'UZ', 'name' => 'Uzbekistan', 'dial_code' => '+998'],
        ['code' => 'TM', 'name' => 'Turkmenistan', 'dial_code' => '+993'],
        ['code' => 'KG', 'name' => 'Kyrgyzstan', 'dial_code' => '+996'],
        ['code' => 'TJ', 'name' => 'Tajikistan', 'dial_code' => '+992'],
        ['code' => 'AM', 'name' => 'Armenia', 'dial_code' => '+374'],
        ['code' => 'AZ', 'name' => 'Azerbaijan', 'dial_code' => '+994'],
        ['code' => 'GE', 'name' => 'Georgia', 'dial_code' => '+995'],
        ['code' => 'IL', 'name' => 'Israel', 'dial_code' => '+972'],
        ['code' => 'PS', 'name' => 'Palestine', 'dial_code' => '+970'],
    ];
}

/**
 * Render country code dropdown options
 */
function renderCountryOptions() {
    $countries = getCountryCodes();

    // Sort countries by dial code (numerically from lowest to highest)
    usort($countries, function($a, $b) {
        $dialA = intval(str_replace('+', '', $a['dial_code']));
        $dialB = intval(str_replace('+', '', $b['dial_code']));
        return $dialA - $dialB;
    });

    $html = '';

    foreach ($countries as $country) {
        $html .= sprintf(
            '<option value="%s">%s %s</option>',
            htmlspecialchars($country['dial_code']),
            htmlspecialchars($country['code']),
            htmlspecialchars($country['dial_code'])
        );
    }

    return $html;
}

