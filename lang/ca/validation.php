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
    'accepted'       => ':attribute ha de ser acceptat.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute no és un URL vàlid.',
    'after'          => ':attribute ha de ser una data posterior a :date.',
    'after_or_equal' => ':attribute ha de ser una data posterior o igual a :date.',
    'alpha'          => ':attribute només pot contenir lletres.',
    'alpha_dash'     => ':attribute només pot contenir lletres, números i guions.',
    'alpha_num'      => ':attribute només pot contenir lletres i números.',
    'array'          => ':attribute ha de ser una matriu.',
    'attributes'     => [
    ],
    'before'          => ':attribute ha de ser una data anterior a :date.',
    'before_or_equal' => ':attribute ha de ser una data anterior o igual a :date.',
    'between'         => [
        'array'   => ':attribute ha de tenir entre :min - :max ítems.',
        'file'    => ':attribute ha de pesar entre :min - :max kilobytes.',
        'numeric' => ':attribute ha d\'estar entre :min - :max.',
        'string'  => ':attribute ha de tenir entre :min - :max caràcters.'
    ],
    'boolean'          => 'El camp :attribute ha de ser verdader o fals',
    'confirmed'        => 'La confirmació de :attribute no coincideix.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute no és una data vàlida.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => 'El camp :attribute no concorda amb el format :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute i :other han de ser diferents.',
    'digits'         => ':attribute ha de tenir :digits dígits.',
    'digits_between' => ':attribute ha de tenir entre :min i :max dígits.',
    'dimensions'     => 'Les dimensions de la imatge :attribute no són vàlides.',
    'distinct'       => 'El camp :attribute té un valor duplicat.',
    'email'          => ':attribute no és un e-mail vàlid',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => ':attribute és invàlid.',
    'file'           => 'El camp :attribute ha de ser un arxiu.',
    'filled'         => 'El camp :attribute és obligatori.',
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
    'image'    => ':attribute ha de ser una imatge.',
    'in'       => ':attribute és invàlid',
    'in_array' => 'El camp :attribute no existeix dintre de :other.',
    'integer'  => ':attribute ha de ser un nombre enter.',
    'ip'       => ':attribute ha de ser una adreça IP vàlida.',
    'ipv4'     => ':attribute ha de ser una adreça IPv4 vàlida.',
    'ipv6'     => ':attribute ha de ser una adreça IPv6 vàlida.',
    'json'     => 'El camp :attribute ha de ser una cadena JSON vàlida.',
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
        'array'   => ':attribute no pot tenir més de :max ítems.',
        'file'    => ':attribute no pot ser més gran que :max kilobytes.',
        'numeric' => ':attribute no pot ser més gran que :max.',
        'string'  => ':attribute no pot ser més gran que :max caràcters.'
    ],
    'mimes'     => ':attribute ha de ser un arxiu amb format: :values.',
    'mimetypes' => ':attribute ha de ser un arxiu amb format: :values.',
    'min'       => [
        'array'   => ':attribute ha de tenir almenys :min ítems.',
        'file'    => 'El tamany de :attribute ha de ser d\'almenys :min kilobytes.',
        'numeric' => 'El tamany de :attribute ha de ser d\'almenys :min.',
        'string'  => ':attribute ha de contenir almenys :min caràcters.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => ':attribute és invàlid.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ':attribute ha de ser numèric.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'El camp :attribute ha d\'existir.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'El format de :attribute és invàlid.',
    'required'             => 'El camp :attribute és obligatori.',
    'required_if'          => 'El camp :attribute és obligatori quan :other és :value.',
    'required_unless'      => 'El camp :attribute és obligatori a no ser que :other sigui a :values.',
    'required_with'        => 'El camp :attribute és obligatori quan hi ha :values.',
    'required_with_all'    => 'El camp :attribute és obligatori quan hi ha :values.',
    'required_without'     => 'El camp :attribute és obligatori quan no hi ha :values.',
    'required_without_all' => 'El camp :attribute és obligatori quan no hi ha cap valor dels següents: :values.',
    'same'                 => ':attribute i :other han de coincidir.',
    'size'                 => [
        'array'   => ':attribute ha de contenir :size ítems.',
        'file'    => 'El tamany de :attribute ha de ser :size kilobytes.',
        'numeric' => 'El tamany de :attribute ha de ser :size.',
        'string'  => ':attribute ha de contenir :size caràcters.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => 'El camp :attribute ha de ser una cadena.',
    'timezone'    => 'El camp :attribute ha de ser una zona vàlida.',
    'unique'      => ':attribute ja està registrat i no es pot repetir.',
    'uploaded'    => ':attribute ha fallat al pujar.',
    'url'         => ':attribute no és una adreça web vàlida.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
