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
    'accepted'       => 'O campo :attribute deverá ser aceite.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'O campo :attribute não contém um URL válido.',
    'after'          => 'O campo :attribute deverá conter uma data posterior a :date.',
    'after_or_equal' => 'O campo :attribute deverá conter uma data posterior ou igual a :date.',
    'alpha'          => 'O campo :attribute deverá conter apenas letras.',
    'alpha_dash'     => 'O campo :attribute deverá conter apenas letras, números e traços.',
    'alpha_num'      => 'O campo :attribute deverá conter apenas letras e números .',
    'array'          => 'O campo :attribute deverá conter uma coleção de elementos.',
    'attributes'     => [
    ],
    'before'          => 'O campo :attribute deverá conter uma data anterior a :date.',
    'before_or_equal' => 'O Campo :attribute deverá conter uma data anterior ou igual a :date.',
    'between'         => [
        'array'   => 'O campo :attribute deverá conter entre :min - :max elementos.',
        'file'    => 'O campo :attribute deverá ter um tamanho entre :min - :max kilobytes.',
        'numeric' => 'O campo :attribute deverá ter um valor entre :min - :max.',
        'string'  => 'O campo :attribute deverá conter entre :min - :max caracteres.'
    ],
    'boolean'          => 'O campo :attribute deverá conter o valor verdadeiro ou falso.',
    'confirmed'        => 'A confirmação para o campo :attribute não coincide.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'O campo :attribute não contém uma data válida.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => 'A data indicada para o campo :attribute não respeita o formato :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Os campos :attribute e :other deverão conter valores diferentes.',
    'digits'         => 'O campo :attribute deverá conter :digits caracteres.',
    'digits_between' => 'O campo :attribute deverá conter entre :min a :max caracteres.',
    'dimensions'     => 'O campo :attribute deverá conter uma dimensão de imagem válida.',
    'distinct'       => 'O campo :attribute contém um valor duplicado.',
    'email'          => 'O campo :attribute não contém um endereço de correio eletrónico válido.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'O valor selecionado para o campo :attribute é inválido.',
    'file'           => 'O campo :attribute deverá conter um ficheiro.',
    'filled'         => 'É obrigatória a indicação de um valor para o campo :attribute.',
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
    'image'    => 'O campo :attribute deverá conter uma imagem.',
    'in'       => 'O campo :attribute não contém um valor válido.',
    'in_array' => 'O campo :attribute não existe em :other.',
    'integer'  => 'O campo :attribute deverá conter um número inteiro.',
    'ip'       => 'O campo :attribute deverá conter um IP válido.',
    'ipv4'     => 'O campo :attribute deverá conter um IPv4 válido.',
    'ipv6'     => 'O campo :attribute deverá conter um IPv6 válido.',
    'json'     => 'O campo :attribute deverá conter um texto JSON válido.',
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
        'array'   => 'O campo :attribute não deverá conter mais de :max elementos.',
        'file'    => 'O campo :attribute não deverá ter um tamanho superior a :max kilobytes.',
        'numeric' => 'O campo :attribute não deverá conter um valor superior a :max.',
        'string'  => 'O campo :attribute não deverá conter mais de :max caracteres.'
    ],
    'mimes'     => 'O campo :attribute deverá conter um ficheiro do tipo: :values.',
    'mimetypes' => 'O campo :attribute deverá conter um ficheiro do tipo: :values.',
    'min'       => [
        'array'   => 'O campo :attribute deverá conter no mínimo :min elementos.',
        'file'    => 'O campo :attribute deverá ter no mínimo :min kilobytes.',
        'numeric' => 'O campo :attribute deverá ter um valor superior ou igual a :min.',
        'string'  => 'O campo :attribute deverá conter no mínimo :min caracteres.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'O campo :attribute contém um valor inválido.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => 'O campo :attribute deverá conter um valor numérico.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'O campo :attribute deverá estar presente.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'O formato do valor para o campo :attribute é inválido.',
    'required'             => 'É obrigatória a indicação de um valor para o campo :attribute.',
    'required_if'          => 'É obrigatória a indicação de um valor para o campo :attribute quando o valor do campo :other é igual a :value.',
    'required_unless'      => 'É obrigatória a indicação de um valor para o campo :attribute a menos que :other esteja presente em :values.',
    'required_with'        => 'É obrigatória a indicação de um valor para o campo :attribute quando :values está presente.',
    'required_with_all'    => 'É obrigatória a indicação de um valor para o campo :attribute quando um dos :values está presente.',
    'required_without'     => 'É obrigatória a indicação de um valor para o campo :attribute quando :values não está presente.',
    'required_without_all' => 'É obrigatória a indicação de um valor para o campo :attribute quando nenhum dos :values está presente.',
    'same'                 => 'Os campos :attribute e :other deverão conter valores iguais.',
    'size'                 => [
        'array'   => 'O campo :attribute deverá conter :size elementos.',
        'file'    => 'O campo :attribute deverá ter o tamanho de :size kilobytes.',
        'numeric' => 'O campo :attribute deverá conter o valor :size.',
        'string'  => 'O campo :attribute deverá conter :size caracteres.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => 'O campo :attribute deverá conter texto.',
    'timezone'    => 'O campo :attribute deverá ter um fuso horário válido.',
    'unique'      => 'O valor indicado para o campo :attribute já se encontra registado.',
    'uploaded'    => 'O upload do ficheiro :attribute falhou.',
    'url'         => 'O formato do URL indicado para o campo :attribute é inválido.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
