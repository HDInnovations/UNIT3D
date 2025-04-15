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
    'accepted'       => ':attribute måste accepteras.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute är inte en giltig webbadress.',
    'after'          => ':attribute måste vara ett datum efter den :date.',
    'after_or_equal' => ':attribute måste vara ett datum senare eller samma dag som :date.',
    'alpha'          => ':attribute får endast innehålla bokstäver.',
    'alpha_dash'     => ':attribute får endast innehålla bokstäver, siffror och bindestreck.',
    'alpha_num'      => ':attribute får endast innehålla bokstäver och siffror.',
    'array'          => ':attribute måste vara en array.',
    'attributes'     => [
    ],
    'before'          => ':attribute måste vara ett datum innan den :date.',
    'before_or_equal' => ':attribute måste vara ett datum före eller samma dag som :date.',
    'between'         => [
        'array'   => ':attribute måste innehålla mellan :min - :max objekt.',
        'file'    => ':attribute måste vara mellan :min till :max kilobyte stor.',
        'numeric' => ':attribute måste vara en siffra mellan :min och :max.',
        'string'  => ':attribute måste innehålla :min till :max tecken.'
    ],
    'boolean'          => ':attribute måste vara sant eller falskt.',
    'confirmed'        => ':attribute bekräftelsen matchar inte.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute är inte ett giltigt datum.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute matchar inte formatet :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute och :other får inte vara lika.',
    'digits'         => ':attribute måste vara :digits tecken.',
    'digits_between' => ':attribute måste vara mellan :min och :max tecken.',
    'dimensions'     => ':attribute har felaktiga bilddimensioner.',
    'distinct'       => ':attribute innehåller fler än en repetition av samma element.',
    'email'          => ':attribute måste innehålla en korrekt e-postadress.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => ':attribute är ogiltigt.',
    'file'           => ':attribute måste vara en fil.',
    'filled'         => ':attribute är obligatoriskt.',
    'gt'             => [
        'array'   => ':attribute måste innehålla fler än :value objekt.',
        'file'    => ':attribute måste vara större än :value kilobyte stor.',
        'numeric' => ':attribute måste vara större än :value.',
        'string'  => ':attribute måste vara längre än :value tecken.'
    ],
    'gte' => [
        'array'   => ':attribute måste innehålla lika många eller fler än :value objekt.',
        'file'    => ':attribute måste vara lika med eller större än :value kilobyte stor.',
        'numeric' => ':attribute måste vara lika med eller större än :value.',
        'string'  => ':attribute måste vara lika med eller längre än :value tecken.'
    ],
    'image'    => ':attribute måste vara en bild.',
    'in'       => ':attribute är ogiltigt.',
    'in_array' => ':attribute finns inte i :other.',
    'integer'  => ':attribute måste vara en siffra.',
    'ip'       => ':attribute måste vara en giltig IP-adress.',
    'ipv4'     => ':attribute måste vara en giltig IPv4-adress.',
    'ipv6'     => ':attribute måste vara en giltig IPv6-adress.',
    'json'     => ':attribute måste vara en giltig JSON-sträng.',
    'lt'       => [
        'array'   => ':attribute måste innehålla färre än :value objekt.',
        'file'    => ':attribute måste vara mindre än :value kilobyte stor.',
        'numeric' => ':attribute måste vara mindre än :value.',
        'string'  => ':attribute måste vara kortare än :value tecken.'
    ],
    'lte' => [
        'array'   => ':attribute måste innehålla lika många eller färre än :value objekt.',
        'file'    => ':attribute måste vara lika med eller mindre än :value kilobyte stor.',
        'numeric' => ':attribute måste vara lika med eller mindre än :value.',
        'string'  => ':attribute måste vara lika med eller kortare än :value tecken.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute får inte innehålla mer än :max objekt.',
        'file'    => ':attribute får max vara :max kilobyte stor.',
        'numeric' => ':attribute får inte vara större än :max.',
        'string'  => ':attribute får max innehålla :max tecken.'
    ],
    'mimes'     => ':attribute måste vara en fil av typen: :values.',
    'mimetypes' => ':attribute måste vara en fil av typen: :values.',
    'min'       => [
        'array'   => ':attribute måste innehålla minst :min objekt.',
        'file'    => ':attribute måste vara minst :min kilobyte stor.',
        'numeric' => ':attribute måste vara större än :min.',
        'string'  => ':attribute måste innehålla minst :min tecken.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => ':attribute är ogiltigt.',
    'not_regex'   => 'Formatet för :attribute är ogiltigt.',
    'numeric'     => ':attribute måste vara en siffra.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute måste finnas med.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':attribute har ogiltigt format.',
    'required'             => ':attribute är obligatoriskt.',
    'required_if'          => ':attribute är obligatoriskt när :other är :value.',
    'required_unless'      => ':attribute är obligatoriskt när inte :other finns bland :values.',
    'required_with'        => ':attribute är obligatoriskt när :values är ifyllt.',
    'required_with_all'    => ':attribute är obligatoriskt när :values är ifyllt.',
    'required_without'     => ':attribute är obligatoriskt när :values ej är ifyllt.',
    'required_without_all' => ':attribute är obligatoriskt när ingen av :values är ifyllt.',
    'same'                 => ':attribute och :other måste vara lika.',
    'size'                 => [
        'array'   => ':attribute måste innehålla :size objekt.',
        'file'    => ':attribute får endast vara :size kilobyte stor.',
        'numeric' => ':attribute måste vara :size.',
        'string'  => ':attribute måste innehålla :size tecken.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute måste vara en sträng.',
    'timezone'    => ':attribute måste vara en giltig tidszon.',
    'unique'      => ':attribute används redan.',
    'uploaded'    => ':attribute kunde inte laddas upp.',
    'url'         => ':attribute har ett ogiltigt format.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
