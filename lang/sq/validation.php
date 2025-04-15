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
    'accepted'       => ':attribute duhet të pranohet.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute nuk është adresë e saktë.',
    'after'          => ':attribute duhet të jetë datë pas :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha'          => ':attribute mund të përmbajë vetëm shkronja.',
    'alpha_dash'     => ':attribute mund të përmbajë vetëm shkronja, numra, dhe viza.',
    'alpha_num'      => ':attribute mund të përmbajë vetëm shkronja dhe numra.',
    'array'          => ':attribute duhet të jetë një bashkësi (array).',
    'attributes'     => [
    ],
    'before'          => ':attribute duhet të jetë datë para :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between'         => [
        'array'   => ':attribute duhet të jetë midis :min - :max elementëve.',
        'file'    => ':attribute duhet të jetë midis :min - :max kilobajtëve.',
        'numeric' => ':attribute duhet të jetë midis :min - :max.',
        'string'  => ':attribute duhet të jetë midis :min - :max karaktereve.'
    ],
    'boolean'          => 'Fusha :attribute duhet të jetë e vërtetë ose e gabuar',
    'confirmed'        => ':attribute konfirmimi nuk përputhet.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute nuk është një datë e saktë.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute nuk i përshtatet formatit :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute dhe :other duhet të jenë të ndryshme.',
    'digits'         => ':attribute duhet të jetë :digits shifra.',
    'digits_between' => ':attribute duhet të jetë midis :min dhe :max shifra.',
    'dimensions'     => 'The :attribute has invalid image dimensions.',
    'distinct'       => 'The :attribute field has a duplicate value.',
    'email'          => ':attribute formati është i pasaktë.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => ':attribute përzgjedhur është i/e pasaktë.',
    'file'           => 'The :attribute must be a file.',
    'filled'         => 'Fusha :attribute është e kërkuar.',
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
    'image'    => ':attribute duhet të jetë imazh.',
    'in'       => ':attribute përzgjedhur është i/e pasaktë.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer'  => ':attribute duhet të jetë numër i plotë.',
    'ip'       => ':attribute duhet të jetë një IP adresë e saktë.',
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
        'array'   => ':attribute nuk mund të ketë më tepër se :max elemente.',
        'file'    => ':attribute nuk mund të jetë më tepër se :max kilobajtë.',
        'numeric' => ':attribute nuk mund të jetë më tepër se :max.',
        'string'  => ':attribute nuk mund të jetë më tepër se :max karaktere.'
    ],
    'mimes'     => ':attribute duhet të jetë një dokument i tipit: :values.',
    'mimetypes' => ':attribute duhet të jetë një dokument i tipit: :values.',
    'min'       => [
        'array'   => ':attribute nuk mund të ketë më pak se :min elemente.',
        'file'    => ':attribute nuk mund të jetë më pak se :min kilobajtë.',
        'numeric' => ':attribute nuk mund të jetë më pak se :min.',
        'string'  => ':attribute nuk mund të jetë më pak se :min karaktere.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => ':attribute përzgjedhur është i/e pasaktë.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ':attribute duhet të jetë një numër.',
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
    'regex'                => 'Formati i :attribute është i pasaktë.',
    'required'             => 'Fusha :attribute është e kërkuar.',
    'required_if'          => 'Fusha :attribute është e kërkuar kur :other është :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'Fusha :attribute është e kërkuar kur :values ekziston.',
    'required_with_all'    => 'Fusha :attribute është e kërkuar kur :values ekziston.',
    'required_without'     => 'Fusha :attribute është e kërkuar kur :values nuk ekziston.',
    'required_without_all' => 'Fusha :attribute është e kërkuar kur nuk ekziston asnjë nga :values.',
    'same'                 => ':attribute dhe :other duhet të përputhen.',
    'size'                 => [
        'array'   => ':attribute duhet të ketë :size elemente.',
        'file'    => ':attribute duhet të jetë :size kilobajtë.',
        'numeric' => ':attribute duhet të jetë :size.',
        'string'  => ':attribute duhet të jetë :size karaktere.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute duhet të jetë varg.',
    'timezone'    => ':attribute duhet të jetë zonë e saktë.',
    'unique'      => ':attribute është marrë tashmë.',
    'uploaded'    => 'The :attribute failed to upload.',
    'url'         => 'Formati i :attribute është i pasaktë.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
