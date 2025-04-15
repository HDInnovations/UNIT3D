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
    'accepted'       => ':attribute skal accepteres.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute er ikke en gyldig URL.',
    'after'          => ':attribute skal være en dato efter :date.',
    'after_or_equal' => ':attribute skal være en dato efter eller lig med :date.',
    'alpha'          => ':attribute må kun bestå af bogstaver.',
    'alpha_dash'     => ':attribute må kun bestå af bogstaver, tal og bindestreger.',
    'alpha_num'      => ':attribute må kun bestå af bogstaver og tal.',
    'array'          => ':attribute skal være et array.',
    'attributes'     => [
    ],
    'before'          => ':attribute skal være en dato før :date.',
    'before_or_equal' => ':attribute skal være en dato før eller lig med :date.',
    'between'         => [
        'array'   => ':attribute skal indeholde mellem :min - :max elementer.',
        'file'    => ':attribute skal være mellem :min - :max kilobytes.',
        'numeric' => ':attribute skal være mellem :min - :max.',
        'string'  => ':attribute skal være mellem :min - :max tegn.'
    ],
    'boolean'          => ':attribute skal være sandt eller falsk',
    'confirmed'        => ':attribute er ikke det samme som bekræftelsesfeltet.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute er ikke en gyldig dato.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute matcher ikke formatet :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute og :other skal være forskellige.',
    'digits'         => ':attribute skal have :digits cifre.',
    'digits_between' => ':attribute skal have mellem :min og :max cifre.',
    'dimensions'     => ':attribute har forkerte billede dimensioner.',
    'distinct'       => ':attribute har en duplikatværdi.',
    'email'          => ':attribute skal være en gyldig e-mailadresse.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Det valgte :attribute er ugyldig.',
    'file'           => ':attribute skal være en fil.',
    'filled'         => ':attribute skal udfyldes.',
    'gt'             => [
        'array'   => 'The :attribute skal være mere end :value elementer.',
        'file'    => 'The :attribute skal være større end :value kilobytes.',
        'numeric' => 'The :attribute skal være større end :value.',
        'string'  => 'The :attribute skal være større end :value tegn.'
    ],
    'gte' => [
        'array'   => 'The :attribute skal have :value items eller mere.',
        'file'    => 'The :attribute skal være større end eller lig med :value kilobytes.',
        'numeric' => 'The :attribute skal være større end eller lig med :value.',
        'string'  => 'The :attribute skal være større end eller lig med :value tegn.'
    ],
    'image'    => ':attribute skal være et billede.',
    'in'       => 'Det valgte :attribute er ugyldig.',
    'in_array' => ':attribute eksisterer ikke i :other.',
    'integer'  => ':attribute skal være et heltal.',
    'ip'       => ':attribute skal være en gyldig IP adresse.',
    'ipv4'     => ':attribute skal være en gyldig IPv4 adresse.',
    'ipv6'     => ':attribute skal være en gyldig IPv6 adresse.',
    'json'     => ':attribute skal være en gyldig JSON streng.',
    'lt'       => [
        'array'   => 'The :attribute skal have mindre end :value elementer.',
        'file'    => 'The :attribute skal være mindre end :value kilobytes.',
        'numeric' => 'The :attribute skal være mindre end :value.',
        'string'  => 'The :attribute skal være mindre end :value tegn.'
    ],
    'lte' => [
        'array'   => 'The :attribute må ikke have mere end :value elementer.',
        'file'    => 'The :attribute skal være mindre eller lig med :value kilobytes.',
        'numeric' => 'The :attribute skal være mindre eller lig med :value.',
        'string'  => 'The :attribute skal være mindre eller lig med :value tegn.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute må ikke indeholde mere end :max elementer.',
        'file'    => ':attribute skal være højest :max kilobytes.',
        'numeric' => ':attribute skal være højest :max.',
        'string'  => ':attribute skal være højest :max tegn.'
    ],
    'mimes'     => ':attribute skal være en fil af typen: :values.',
    'mimetypes' => ':attribute skal være en fil af typen: :values.',
    'min'       => [
        'array'   => ':attribute skal indeholde mindst :min elementer.',
        'file'    => ':attribute skal være mindst :min kilobytes.',
        'numeric' => ':attribute skal være mindst :min.',
        'string'  => ':attribute skal være mindst :min tegn.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Den valgte :attribute er ugyldig.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ':attribute skal være et tal.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute skal være tilstede.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':attribute formatet er ugyldigt.',
    'required'             => ':attribute skal udfyldes.',
    'required_if'          => ':attribute skal udfyldes når :other er :value.',
    'required_unless'      => ':attribute er påkrævet med mindre :other findes i :values.',
    'required_with'        => ':attribute skal udfyldes når :values er udfyldt.',
    'required_with_all'    => ':attribute skal udfyldes når :values er udfyldt.',
    'required_without'     => ':attribute skal udfyldes når :values ikke er udfyldt.',
    'required_without_all' => ':attribute skal udfyldes når ingen af :values er udfyldt.',
    'same'                 => ':attribute og :other skal være ens.',
    'size'                 => [
        'array'   => ':attribute skal indeholde :size elementer.',
        'file'    => ':attribute skal være :size kilobytes.',
        'numeric' => ':attribute skal være :size.',
        'string'  => ':attribute skal være :size tegn lang.'
    ],
    'starts_with' => ':attribute skal starte med et af følgende: :values',
    'string'      => ':attribute skal være en streng.',
    'timezone'    => ':attribute skal være en gyldig tidszone.',
    'unique'      => ':attribute er allerede taget.',
    'uploaded'    => ':attribute fejlene i uploaden.',
    'url'         => ':attribute formatet er ugyldigt.',
    'uuid'        => ':attribute skal være en gyldig UUID.'
];
