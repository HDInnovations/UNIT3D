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
    'accepted'       => 'Reiturinn :attribute verður að vera samþykktur.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Reiturinn :attribute er ekki leyfileg vefslóð.',
    'after'          => 'Reiturinn :attribute verður að vera dagsetning eftir :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha'          => 'Reiturinn :attribute má aðeins innihalda bókstafi.',
    'alpha_dash'     => 'Reiturinn :attribute má aðeins innihalda bókstafi, tölur og undirstikanir.',
    'alpha_num'      => 'Reiturinn :attribute má aðeins innihalda bókstafi og tölur.',
    'array'          => 'Reiturinn :attribute verður að vera fylki.',
    'attributes'     => [
    ],
    'before'          => 'Reiturinn :attribute verður að vera dagsetning eftir :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between'         => [
        'array'   => 'Reiturinn :attribute verður að vera á milli :min - :max staka.',
        'file'    => 'Reiturinn :attribute verður að vera á milli :min - :max kílóbæta.',
        'numeric' => 'Reiturinn :attribute verður að vera á milli :min - :max.',
        'string'  => 'Reiturinn :attribute verður að vera á milli :min - :max stafa.'
    ],
    'boolean'          => 'Reiturinn :attribute verður að vera réttur eða rangur.',
    'confirmed'        => 'Staðfesting á reitnum :attribute passar ekki.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Reiturinn :attribute er ekki rétt dagsetning.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => 'Reiturinn :attribute passar ekki við :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Reiturinn :attribute og :other verða að vera ólíkir.',
    'digits'         => 'Reiturinn :attribute verður að vera :digits tölustafir.',
    'digits_between' => 'Reiturinn :attribute verður að vera á milli :min og :max tölustafa.',
    'dimensions'     => 'The :attribute has invalid image dimensions.',
    'distinct'       => 'The :attribute field has a duplicate value.',
    'email'          => 'Reiturinn :attribute snið netfangsins er ekki rétt.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Reiturinn :attribute er nú þegar til.',
    'file'           => 'The :attribute must be a file.',
    'filled'         => 'Reiturinn :attribute verður að innihalda eitthvað.',
    'gt'             => [
        'array'   => 'The :attribute must have more than :value items.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'numeric' => 'The :attribute must be greater than :value.',
        'string'  => 'The :attribute must be greater than :value characters.'
    ],
    'gte' => [
        'array'   => 'The :attribute must have :value items or more.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'string'  => 'The :attribute must be greater than or equal :value characters.'
    ],
    'image'    => 'Reiturinn :attribute verður að vera mynd.',
    'in'       => 'Reiturinn :attribute er ekki réttur.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer'  => 'Reiturinn :attribute verður að vera tala.',
    'ip'       => 'Reiturinn :attribute verður að vera lögleg IP-tala.',
    'ipv4'     => 'The :attribute must be a valid IPv4 address.',
    'ipv6'     => 'The :attribute must be a valid IPv6 address.',
    'json'     => 'The :attribute must be a valid JSON string.',
    'lt'       => [
        'array'   => 'The :attribute must have less than :value items.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'numeric' => 'The :attribute must be less than :value.',
        'string'  => 'The :attribute must be less than :value characters.'
    ],
    'lte' => [
        'array'   => 'The :attribute must not have more than :value items.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
        'numeric' => 'The :attribute must be less than or equal :value.',
        'string'  => 'The :attribute must be less than or equal :value characters.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'Reiturinn :attribute verður að innihalda færri en :max stök.',
        'file'    => 'Reiturinn :attribute verður að vera minni en :max kílóbæt.',
        'numeric' => 'Reiturinn :attribute verður að innihalda færri stafi en :max.',
        'string'  => 'Reiturinn :attribute verður að innihalda færri en :max stafi.'
    ],
    'mimes'     => 'Reiturinn :attribute verður að vera skrá af gerðinni: :values.',
    'mimetypes' => 'Reiturinn :attribute verður að vera skrá af gerðinni: :values.',
    'min'       => [
        'array'   => 'Reiturinn :attribute verður að vera að lágmarki :min stök.',
        'file'    => 'Reiturinn :attribute verður að vera að lágmarki :min kílóbæt.',
        'numeric' => 'Reiturinn :attribute verður að vera að lágmarki :min tölustafir.',
        'string'  => 'Reiturinn :attribute verður að vera að lágmarki :min stafir.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Reiturinn :attribute er ógildur.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => 'Reiturinn :attribute verður að vera tala.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'The :attribute field must be present.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Reiturinn :attribute er ekki á réttu formi.',
    'required'             => 'Reiturinn :attribute er nauðsynlegur.',
    'required_if'          => 'Reiturinn :attribute er nauðsynlegur þegar :other er :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'Reiturinn :attribute og :other verða að stemma.',
    'size'                 => [
        'array'   => 'Reiturinn :attribute verður að innihalda :size hluti.',
        'file'    => 'Reiturinn :attribute verður að vera :size kílóbæt.',
        'numeric' => 'Reiturinn :attribute verður að vera :size.',
        'string'  => 'Reiturinn :attribute verður að vera :size stafir.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => 'The :attribute must be a string.',
    'timezone'    => 'Reiturinn :attribute verður að vera rétt tímabelti.',
    'unique'      => 'Reiturinn :attribute er því miður ekki leyfilegur. Það er annar eins.',
    'uploaded'    => 'The :attribute failed to upload.',
    'url'         => 'Reiturinn :attribute verður að vera netslóð.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
