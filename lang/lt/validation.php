<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

return [
    'accepted'       => 'Laukas :attribute turi būti priimtas.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Laukas :attribute nėra galiojantis internetinis adresas.',
    'after'          => 'Lauko :attribute reikšmė turi būti po :date datos.',
    'after_or_equal' => 'Lauko :attribute reikšmė privalo būti data lygi arba vėlesnė negu :date.',
    'alpha'          => 'Laukas :attribute gali turėti tik raides.',
    'alpha_dash'     => 'Laukas :attribute gali turėti tik raides, skaičius ir brūkšnelius.',
    'alpha_num'      => 'Laukas :attribute gali turėti tik raides ir skaičius.',
    'array'          => 'Laukas :attribute turi būti masyvas.',
    'attributes'     => [
    ],
    'before'          => 'Laukas :attribute turi būti data prieš :date.',
    'before_or_equal' => 'Lauko :attribute reikšmė privalo būti data lygi arba ankstesnė negu :date.',
    'between'         => [
        'array'   => 'Elementų skaičius lauke :attribute turi turėti nuo :min iki :max.',
        'file'    => 'Failo dydis lauke :attribute turi būti tarp :min ir :max kilobaitų.',
        'numeric' => 'Lauko :attribute reikšmė turi būti tarp :min ir :max.',
        'string'  => 'Simbolių skaičius lauke :attribute turi būti tarp :min ir :max.'
    ],
    'boolean'          => 'Lauko reikšmė :attribute turi būti \'taip\' arba \'ne\'.',
    'confirmed'        => 'Lauko :attribute patvirtinimas nesutampa.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Lauko :attribute reikšmė nėra galiojanti data.',
    'date_equals'    => 'Lauko :attribute reikšmė turi būti data lygi :date.',
    'date_format'    => 'Lauko :attribute reikšmė neatitinka formato :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Laukų :attribute ir :other reikšmės turi skirtis.',
    'digits'         => 'Laukas :attribute turi būti sudarytas iš :digits skaitmenų.',
    'digits_between' => 'Laukas :attribute tuti turėti nuo :min iki :max skaitmenų.',
    'dimensions'     => 'Lauke :attribute įkeltas paveiksliukas neatitinka išmatavimų reikalavimo.',
    'distinct'       => 'Laukas :attribute pasikartoja.',
    'email'          => 'Lauko :attribute reikšmė turi būti galiojantis el. pašto adresas.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Pasirinkta negaliojanti :attribute reikšmė.',
    'file'           => 'The :attribute must be a file.',
    'filled'         => 'Laukas :attribute turi būti užpildytas.',
    'gt'             => [
        'array'   => 'Laukas :attribute turi turėti daugiau nei :value elementus.',
        'file'    => 'Lauko :attribute reikšmė turi būti didesnė negu :value kilobaitai.',
        'numeric' => 'Lauko :attribute reikšmė turi būti didesnė negu :value.',
        'string'  => 'Lauko :attribute reikšmė turi būti didesnė negu :value simboliai.'
    ],
    'gte' => [
        'array'   => 'Laukas :attribute  turi turėti :value elementus arba daugiau.',
        'file'    => 'Lauko :attribute reikšmė turi būti didesnė arba lygi :value kilobaitams.',
        'numeric' => 'Lauko :attribute reikšmė turi būti didesnė arba lygi :value.',
        'string'  => 'Lauko :attribute reikšmė turi būti didesnė arba lygi :value simboliams.'
    ],
    'image'    => 'Lauko :attribute reikšmė turi būti paveikslėlis.',
    'in'       => 'Pasirinkta negaliojanti :attribute reikšmė.',
    'in_array' => 'Laukas :attribute neegzistuoja :other lauke.',
    'integer'  => 'Lauko :attribute reikšmė turi būti sveikasis skaičius.',
    'ip'       => 'Lauko :attribute reikšmė turi būti galiojantis IP adresas.',
    'ipv4'     => 'Lauko :attribute reikšmė turi būti galiojantis IPv4 adresas.',
    'ipv6'     => 'Lauko :attribute reikšmė turi būti galiojantis IPv6 adresas.',
    'json'     => 'Lauko :attribute reikšmė turi būti JSON tekstas.',
    'lt'       => [
        'array'   => 'Laukas :attribute turi turėti mažiau negu :value elementus.',
        'file'    => 'Lauko :attribute reikšmė turi būti mažesnė negu :value kilobaitai.',
        'numeric' => 'Lauko :attribute reikšmė turi būti mažesnė negu :value.',
        'string'  => 'Lauko :attribute reikšmė turi būti mažesnė negu :value simboliai.'
    ],
    'lte' => [
        'array'   => 'Laukas :attribute turi turėti mažiau arba lygiai :value elementus.',
        'file'    => 'Lauko :attribute reikšmė turi būti mažesnė arba lygi :value kilobaitams.',
        'numeric' => 'Lauko :attribute reikšmė turi būti mažesnė arba lygi :value.',
        'string'  => 'Lauko :attribute  reikšmė turi būti mažesnė arba lygi :value simboliams.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'Elementų kiekis lauke :attribute negali turėti daugiau nei :max elementų.',
        'file'    => 'Failo dydis lauke :attribute reikšmė negali būti didesnė nei :max kilobaitų.',
        'numeric' => 'Lauko :attribute reikšmė negali būti didesnė nei :max.',
        'string'  => 'Simbolių kiekis lauke :attribute reikšmė negali būti didesnė nei :max simbolių.'
    ],
    'mimes'     => 'Lauko reikšmė :attribute turi būti failas vieno iš sekančių tipų: :values.',
    'mimetypes' => 'Lauko reikšmė :attribute turi būti failas vieno iš sekančių tipų: :values.',
    'min'       => [
        'array'   => 'Elementų kiekis lauke :attribute turi būti ne mažiau nei :min.',
        'file'    => 'Failo dydis lauke :attribute turi būti ne mažesnis nei :min kilobaitų.',
        'numeric' => 'Lauko :attribute reikšmė turi būti ne mažesnė nei :min.',
        'string'  => 'Simbolių kiekis lauke :attribute turi būti ne mažiau nei :min.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Pasirinkta negaliojanti reikšmė :attribute.',
    'not_regex'   => 'Lauko :attribute formatas yra neteisingas.',
    'numeric'     => 'Lauko :attribute reikšmė turi būti skaičius.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Laukas :attribute turi egzistuoti.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Negaliojantis lauko :attribute formatas.',
    'required'             => 'Privaloma užpildyti lauką :attribute.',
    'required_if'          => 'Privaloma užpildyti lauką :attribute kai :other yra :value.',
    'required_unless'      => 'Laukas :attribute yra privalomas, nebent :other yra tarp :values reikšmių.',
    'required_with'        => 'Privaloma užpildyti lauką :attribute kai pateikta :values.',
    'required_with_all'    => 'Privaloma užpildyti lauką :attribute kai pateikta :values.',
    'required_without'     => 'Privaloma užpildyti lauką :attribute kai nepateikta :values.',
    'required_without_all' => 'Privaloma užpildyti lauką :attribute kai nepateikta nei viena iš reikšmių :values.',
    'same'                 => 'Laukai :attribute ir :other turi sutapti.',
    'size'                 => [
        'array'   => 'Elementų kiekis lauke :attribute turi būti :size.',
        'file'    => 'Failo dydis lauke :attribute turi būti :size kilobaitai.',
        'numeric' => 'Lauko :attribute reikšmė turi būti :size.',
        'string'  => 'Simbolių skaičius lauke :attribute turi būti :size.'
    ],
    'starts_with' => 'Laukas :attribute turi prasidėti vienu iš: :values',
    'string'      => 'Laukas :attribute turi būti tekstinis.',
    'timezone'    => 'Lauko :attribute reikšmė turi būti galiojanti laiko zona.',
    'unique'      => 'Tokia :attribute reikšmė jau pasirinkta.',
    'uploaded'    => 'Nepavyko įkelti :attribute lauko.',
    'url'         => 'Negaliojantis lauko :attribute formatas.',
    'uuid'        => 'Lauko :attribute reikšmė turi būti galiojantis UUID.'
];
