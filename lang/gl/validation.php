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
    'accepted'       => ':attribute debe ser aceptado.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute non é unha URL válida.',
    'after'          => ':attribute debe ser unha data posterior a :date.',
    'after_or_equal' => 'O :attribute debe ser unha data posterior ou igual a :date.',
    'alpha'          => ':attribute só debe conter letras.',
    'alpha_dash'     => ':attribute só debe conter letras, números e guións.',
    'alpha_num'      => ':attribute só debe conter letras e números.',
    'array'          => ':attribute debe ser un conxunto.',
    'attributes'     => [
    ],
    'before'          => ':attribute debe ser unha data anterior a :date.',
    'before_or_equal' => 'O :attribute debe ser unha data previa ou igual a :date.',
    'between'         => [
        'array'   => ':attribute debe conter entre :min e :max elementos.',
        'file'    => 'O tamaño de :attribute debe estar entre :min e :max quilobites.',
        'numeric' => ':attribute debe estar entre :min e :max.',
        'string'  => ':attribute debe ter entre :min e :max caracteres.'
    ],
    'boolean'          => 'O campo :attribute debe ser verdadeiro ou falso.',
    'confirmed'        => 'A confirmación de :attribute non coincide.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute non é unha data válida.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute non concorda co formato :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute e :other deben ser diferentes.',
    'digits'         => ':attribute debe ter :digits díxitos.',
    'digits_between' => ':attribute debe ter entre :min e :max díxitos.',
    'dimensions'     => 'The :attribute has invalid image dimensions.',
    'distinct'       => 'The :attribute field has a duplicate value.',
    'email'          => ':attribute debe ser unha dirección de correo electrónico válida.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'O :attribute seleccionado non é válido.',
    'file'           => 'The :attribute must be a file.',
    'filled'         => 'O campo :attribute é obrigatorio.',
    'gt'             => [
        'array'   => 'O :attribute debe ter máis de :value elementos.',
        'file'    => 'O :attribute debe ter máis de :value quilobytes.',
        'numeric' => 'O :attribute debe ser maior que :value.',
        'string'  => 'O :attribute debe ter máis de :value caracteres.'
    ],
    'gte' => [
        'array'   => 'O :attribute deber ter polo menos :value elementos.',
        'file'    => 'O :attribute debe ter polo menos de :value quilobytes.',
        'numeric' => 'O :attribute debe ser polo menos :value.',
        'string'  => 'O :attribute debe ter polo menos :value caracteres.'
    ],
    'image'    => ':attribute debe ser unha imaxe.',
    'in'       => 'O :attribute seleccionado non é válido.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer'  => ':attribute debe ser un número enteiro.',
    'ip'       => ':attribute debe ser unha dirección IP válida.',
    'ipv4'     => 'O :attribute debe ser unha dirección IPv4 válida.',
    'ipv6'     => 'O :attribute debe ser unha dirección IPv6 válida.',
    'json'     => ':attribute debe ser unha cadea JSON válida.',
    'lt'       => [
        'array'   => 'O :attribute debe ter menos de :value elementos.',
        'file'    => 'O :attribute debe ter menos de :value quilobytes.',
        'numeric' => 'O :attribute debe ser menor que :value.',
        'string'  => 'O :attribute debe ter menos de :value caracteres.'
    ],
    'lte' => [
        'array'   => 'O :attribute non debe ter máis de :value elementos.',
        'file'    => 'O :attribute debe ter como moito :value quilobytes.',
        'numeric' => 'O :attribute debe ser como moito :value.',
        'string'  => 'O :attribute debe ter como moito :value caracteres.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute non debe conter máis de :max elementos.',
        'file'    => 'O tamaño de :attribute non debe ser maior de :max quilobites.',
        'numeric' => ':attribute non debe ser maior de :max.',
        'string'  => ':attribute non debe ter máis de :max caracteres.'
    ],
    'mimes'     => ':attribute debe ser un arquivo de tipo: :values.',
    'mimetypes' => ':attribute debe ser un arquivo de tipo: :values.',
    'min'       => [
        'array'   => ':attribute debe conter polo menos :min elementos.',
        'file'    => 'O tamaño de :attribute debe ser polo menos de :min quilobites.',
        'numeric' => ':attribute debe ser polo menos :min.',
        'string'  => ':attribute debe ter polo menos :min caracteres.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => ':attribute non é válido.',
    'not_regex'   => 'O formato de :attribute non é válido.',
    'numeric'     => ':attribute debe de ser un número.',
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
    'regex'                => 'O formato de :attribute non é válido.',
    'required'             => 'O campo :attribute é obrigatorio.',
    'required_if'          => 'O campo :attribute é obrigatorio cando :other é :value.',
    'required_unless'      => 'O campo :attribute é obrigatorio excepto que :other estea en :values.',
    'required_with'        => 'O campo :attribute é obrigatorio cando :values está presente.',
    'required_with_all'    => 'O campo :attribute é obrigatorio cando :values está presente',
    'required_without'     => 'O campo :attribute é obrigatorio cando :values non está presente.',
    'required_without_all' => 'O campo :attribute é obrigatorio cando ningún de :values están presentes.',
    'same'                 => 'O campo :attribute e :other deben coincidir.',
    'size'                 => [
        'array'   => ':attribute debe conter :size elementos.',
        'file'    => 'O tamaño de :attribute debe ser :size quilobites.',
        'numeric' => ':attribute debe ser :size.',
        'string'  => ':attribute debe ter :size caracteres.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute debe ser unha cadea de caracteres.',
    'timezone'    => ':attribute debe ser unha zona válida.',
    'unique'      => ':attribute xa foi empregado.',
    'uploaded'    => 'O :attribute fallou na subida.',
    'url'         => 'O formato de :attribute é inválido.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
