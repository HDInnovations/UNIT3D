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
    'accepted'       => 'Полето :attribute мора да биде прифатено.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Полето :attribute не е валиден URL.',
    'after'          => 'Полето :attribute мора да биде датум после :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha'          => 'Полето :attribute може да содржи само букви.',
    'alpha_dash'     => 'Полето :attribute може да содржи само букви, цифри, долна црта и тире.',
    'alpha_num'      => 'Полето :attribute може да содржи само букви и цифри.',
    'array'          => 'Полето :attribute мора да биде низа.',
    'attributes'     => [
    ],
    'before'          => 'Полето :attribute мора да биде датум пред :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between'         => [
        'array'   => 'Полето :attribute мора да има помеѓу :min - :max карактери.',
        'file'    => 'Полето :attribute мора да биде помеѓу :min и :max килобајти.',
        'numeric' => 'Полето :attribute мора да биде помеѓу :min и :max.',
        'string'  => 'Полето :attribute мора да биде помеѓу :min и :max карактери.'
    ],
    'boolean'          => 'The :attribute field must be true or false',
    'confirmed'        => 'Полето :attribute не е потврдено.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Полето :attribute не е валиден датум.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => 'Полето :attribute не е во формат :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Полињата :attribute и :other треба да се различни.',
    'digits'         => 'Полето :attribute треба да има :digits цифри.',
    'digits_between' => 'Полето :attribute треба да има помеѓу :min и :max цифри.',
    'dimensions'     => 'The :attribute has invalid image dimensions.',
    'distinct'       => 'The :attribute field has a duplicate value.',
    'email'          => 'Полето :attribute не е во валиден формат.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Избранато поле :attribute веќе постои.',
    'file'           => 'The :attribute must be a file.',
    'filled'         => 'Полето :attribute е задолжително.',
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
    'image'    => 'Полето :attribute мора да биде слика.',
    'in'       => 'Избраното поле :attribute е невалидно.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer'  => 'Полето :attribute мора да биде цел број.',
    'ip'       => 'Полето :attribute мора да биде IP адреса.',
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
        'array'   => 'Полето :attribute не може да има повеќе од :max карактери.',
        'file'    => 'Полето :attribute мора да биде помало од :max килобајти.',
        'numeric' => 'Полето :attribute мора да биде помало од :max.',
        'string'  => 'Полето :attribute мора да има помалку од :max карактери.'
    ],
    'mimes'     => 'Полето :attribute мора да биде фајл од типот: :values.',
    'mimetypes' => 'Полето :attribute мора да биде фајл од типот: :values.',
    'min'       => [
        'array'   => 'Полето :attribute мора да има минимум :min карактери.',
        'file'    => 'Полето :attribute мора да биде минимум :min килобајти.',
        'numeric' => 'Полето :attribute мора да биде минимум :min.',
        'string'  => 'Полето :attribute мора да има минимум :min карактери.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Избраното поле :attribute е невалидно.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => 'Полето :attribute мора да биде број.',
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
    'regex'                => 'Полето :attribute е во невалиден формат.',
    'required'             => 'Полето :attribute е задолжително.',
    'required_if'          => 'Полето :attribute е задолжително, кога :other е :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'Полето :attribute е задолжително, кога е внесено :values.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'Полето :attribute е задолжително, кога не е внесено :values.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'Полињата :attribute и :other треба да совпаѓаат.',
    'size'                 => [
        'array'   => 'Полето :attribute мора да има :size карактери.',
        'file'    => 'Полето :attribute мора да биде :size килобајти.',
        'numeric' => 'Полето :attribute мора да биде :size.',
        'string'  => 'Полето :attribute мора да има :size карактери.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => 'The :attribute must be a string.',
    'timezone'    => 'The :attribute must be a valid zone.',
    'unique'      => 'Полето :attribute веќе постои.',
    'uploaded'    => 'The :attribute failed to upload.',
    'url'         => 'Полето :attribute не е во валиден формат.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
