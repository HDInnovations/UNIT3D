<?php
return [
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
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted' => ':attribute debe ser aceptado.',
    'active_url' => ':attribute non é unha URL válida.',
    'after' => ':attribute debe ser unha data posterior a :date.',
    'after_or_equal' => 'O :attribute debe ser unha data posterior ou igual a :date.',
    'alpha' => ':attribute só debe conter letras.',
    'alpha_dash' => ':attribute só debe conter letras, números e guións.',
    'alpha_num' => ':attribute só debe conter letras e números.',
    'array' => ':attribute debe ser un conxunto.',
    'before' => ':attribute debe ser unha data anterior a :date.',
    'before_or_equal' => 'O :attribute debe ser unha data previa ou igual a :date.',
    'between' => [
        'numeric' => ':attribute debe estar entre :min e :max.',
        'file' => 'O tamaño de :attribute debe estar entre :min e :max quilobites.',
        'string' => ':attribute debe ter entre :min e :max caracteres.',
        'array' => ':attribute debe conter entre :min e :max elementos.',
    ],
    'boolean' => 'O campo :attribute debe ser verdadeiro ou falso.',
    'confirmed' => 'A confirmación de :attribute non coincide.',
    'date' => ':attribute non é unha data válida.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => ':attribute non concorda co formato :format.',
    'different' => ':attribute e :other deben ser diferentes.',
    'digits' => ':attribute debe ter :digits díxitos.',
    'digits_between' => ':attribute debe ter entre :min e :max díxitos.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => ':attribute debe ser unha dirección de correo electrónico válida.',
    'exists' => 'O :attribute seleccionado non é válido.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'O campo :attribute é obrigatorio.',
    'gt' => [
        'numeric' => 'O :attribute debe ser maior que :value.',
        'file' => 'O :attribute debe ter máis de :value quilobytes.',
        'string' => 'O :attribute debe ter máis de :value caracteres.',
        'array' => 'O :attribute debe ter máis de :value elementos.',
    ],
    'gte' => [
        'numeric' => 'O :attribute debe ser polo menos :value.',
        'file' => 'O :attribute debe ter polo menos de :value quilobytes.',
        'string' => 'O :attribute debe ter polo menos :value caracteres.',
        'array' => 'O :attribute deber ter polo menos :value elementos.',
    ],
    'image' => ':attribute debe ser unha imaxe.',
    'in' => 'O :attribute seleccionado non é válido.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => ':attribute debe ser un número enteiro.',
    'ip' => ':attribute debe ser unha dirección IP válida.',
    'ipv4' => 'O :attribute debe ser unha dirección IPv4 válida.',
    'ipv6' => 'O :attribute debe ser unha dirección IPv6 válida.',
    'json' => ':attribute debe ser unha cadea JSON válida.',
    'lt' => [
        'numeric' => 'O :attribute debe ser menor que :value.',
        'file' => 'O :attribute debe ter menos de :value quilobytes.',
        'string' => 'O :attribute debe ter menos de :value caracteres.',
        'array' => 'O :attribute debe ter menos de :value elementos.',
    ],
    'lte' => [
        'numeric' => 'O :attribute debe ser como moito :value.',
        'file' => 'O :attribute debe ter como moito :value quilobytes.',
        'string' => 'O :attribute debe ter como moito :value caracteres.',
        'array' => 'O :attribute non debe ter máis de :value elementos.',
    ],
    'max' => [
        'numeric' => ':attribute non debe ser maior de :max.',
        'file' => 'O tamaño de :attribute non debe ser maior de :max quilobites.',
        'string' => ':attribute non debe ter máis de :max caracteres.',
        'array' => ':attribute non debe conter máis de :max elementos.',
    ],
    'mimes' => ':attribute debe ser un arquivo de tipo: :values.',
    'mimetypes' => ':attribute debe ser un arquivo de tipo: :values.',
    'min' => [
        'numeric' => ':attribute debe ser polo menos :min.',
        'file' => 'O tamaño de :attribute debe ser polo menos de :min quilobites.',
        'string' => ':attribute debe ter polo menos :min caracteres.',
        'array' => ':attribute debe conter polo menos :min elementos.',
    ],
    'not_in' => ':attribute non é válido.',
    'not_regex' => 'O formato de :attribute non é válido.',
    'numeric' => ':attribute debe de ser un número.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'O formato de :attribute non é válido.',
    'required' => 'O campo :attribute é obrigatorio.',
    'required_if' => 'O campo :attribute é obrigatorio cando :other é :value.',
    'required_unless' => 'O campo :attribute é obrigatorio excepto que :other estea en :values.',
    'required_with' => 'O campo :attribute é obrigatorio cando :values está presente.',
    'required_with_all' => 'O campo :attribute é obrigatorio cando :values está presente',
    'required_without' => 'O campo :attribute é obrigatorio cando :values non está presente.',
    'required_without_all' => 'O campo :attribute é obrigatorio cando ningún de :values están presentes.',
    'same' => 'O campo :attribute e :other deben coincidir.',
    'size' => [
        'numeric' => ':attribute debe ser :size.',
        'file' => 'O tamaño de :attribute debe ser :size quilobites.',
        'string' => ':attribute debe ter :size caracteres.',
        'array' => ':attribute debe conter :size elementos.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string' => ':attribute debe ser unha cadea de caracteres.',
    'timezone' => ':attribute debe ser unha zona válida.',
    'unique' => ':attribute xa foi empregado.',
    'uploaded' => 'O :attribute fallou na subida.',
    'url' => 'O formato de :attribute é inválido.',
    'uuid' => 'The :attribute must be a valid UUID.',
    'custom' => [
        'attribute-name' => [
            /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
            'rule-name' => 'custom-message',
        ],
    ],
    'accepted_if' => 'O :attribute debe aceptarse cando :other é :value.',
    'current_password' => 'O contrasinal é incorrecto.',
    'declined' => 'O :attribute debe declinarse.',
    'declined_if' => 'O :attribute debe declinarse cando :other é :value.',
    'ends_with' => 'O :attribute debe rematar nun dos seguintes valores: :values.',
    'enum' => 'O :attribute seleccionado non é válido.',
    'mac_address' => 'O :attribute debe ser un enderezo MAC válido.',
    'multiple_of' => 'O :attribute debe ser un múltiplo de :value.',
    'password' => [
        'letters' => 'O :attribute debe conter polo menos unha letra.',
        'mixed' => 'O :attribute debe conter polo menos unha letra maiúscula e unha minúscula.',
        'numbers' => 'O :attribute debe conter polo menos un número.',
        'symbols' => 'O :attribute debe conter polo menos un símbolo.',
        'uncompromised' => 'O :attribute indicado apareceu nunha filtración de datos. Por favor, escolle un :attribute diferente.',
    ],
    'prohibited' => 'O campo :attribute está prohibido.',
    'prohibited_if' => 'O campo :attribute está prohibido cando :other é :value.',
    'prohibited_unless' => 'O campo :attribute está prohibido a menos que :other estea en :values.',
    'prohibits' => 'O campo :attribute impide a presenza de :other.',
    'email_list' => 'Sentímolo, non se permite utilizar este dominio de correo electrónico neste sitio. Por favor, consulte a lista de dominios de correo electrónico permitidos do sitio.',
    'recaptcha' => 'Por favor, completa o reCAPTCHA.',
];
