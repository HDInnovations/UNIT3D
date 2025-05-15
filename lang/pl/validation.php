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
    'accepted'       => ':attribute musi zostać zaakceptowany.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute jest nieprawidłowym adresem URL.',
    'after'          => ':attribute musi być datą późniejszą od :date.',
    'after_or_equal' => ':attribute musi być datą nie wcześniejszą niż :date.',
    'alpha'          => ':attribute może zawierać jedynie litery.',
    'alpha_dash'     => ':attribute może zawierać jedynie litery, cyfry i myślniki.',
    'alpha_num'      => ':attribute może zawierać jedynie litery i cyfry.',
    'array'          => ':attribute musi być tablicą.',
    'attributes'     => [
    ],
    'before'          => ':attribute musi być datą wcześniejszą od :date.',
    'before_or_equal' => ':attribute musi być datą nie późniejszą niż :date.',
    'between'         => [
        'array'   => ':attribute musi składać się z :min - :max elementów.',
        'file'    => ':attribute musi zawierać się w granicach :min - :max kilobajtów.',
        'numeric' => ':attribute musi zawierać się w granicach :min - :max.',
        'string'  => ':attribute musi zawierać się w granicach :min - :max znaków.'
    ],
    'boolean'          => ':attribute musi mieć wartość prawda albo fałsz',
    'confirmed'        => 'Potwierdzenie :attribute nie zgadza się.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute nie jest prawidłową datą.',
    'date_equals'    => ':attribute musi być datą równą :date.',
    'date_format'    => ':attribute nie jest w formacie :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute oraz :other muszą się różnić.',
    'digits'         => ':attribute musi składać się z :digits cyfr.',
    'digits_between' => ':attribute musi mieć od :min do :max cyfr.',
    'dimensions'     => ':attribute ma niepoprawne wymiary.',
    'distinct'       => ':attribute ma zduplikowane wartości.',
    'email'          => 'Format :attribute jest nieprawidłowy.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Zaznaczony :attribute jest nieprawidłowy.',
    'file'           => ':attribute musi być plikiem.',
    'filled'         => 'Pole :attribute jest wymagane.',
    'gt'             => [
        'array'   => ':attribute musi mieć więcej niż :value elementów.',
        'file'    => ':attribute musi być większy niż :value kilobajtów.',
        'numeric' => ':attribute musi być większy niż :value.',
        'string'  => ':attribute musi być dłuższy niż :value znaków.'
    ],
    'gte' => [
        'array'   => ':attribute musi mieć :value lub więcej elementów.',
        'file'    => ':attribute musi być większy lub równy :value kilobajtów.',
        'numeric' => ':attribute musi być większy lub równy :value.',
        'string'  => ':attribute musi być dłuższy lub równy :value znaków.'
    ],
    'image'    => ':attribute musi być obrazkiem.',
    'in'       => 'Zaznaczony :attribute jest nieprawidłowy.',
    'in_array' => ':attribute nie znajduje się w :other.',
    'integer'  => ':attribute musi być liczbą całkowitą.',
    'ip'       => ':attribute musi być prawidłowym adresem IP.',
    'ipv4'     => ':attribute musi być prawidłowym adresem IPv4.',
    'ipv6'     => ':attribute musi być prawidłowym adresem IPv6.',
    'json'     => ':attribute musi być poprawnym ciągiem znaków JSON.',
    'lt'       => [
        'array'   => ':attribute musi mieć mniej niż :value elementów.',
        'file'    => ':attribute musi być mniejszy niż :value kilobajtów.',
        'numeric' => ':attribute musi być mniejszy niż :value.',
        'string'  => ':attribute musi być krótszy niż :value znaków.'
    ],
    'lte' => [
        'array'   => ':attribute musi mieć :value lub mniej elementów.',
        'file'    => ':attribute musi być mniejszy lub równy :value kilobajtów.',
        'numeric' => ':attribute musi być mniejszy lub równy :value.',
        'string'  => ':attribute musi być krótszy lub równy :value znaków.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute nie może mieć więcej niż :max elementów.',
        'file'    => ':attribute nie może być większy niż :max kilobajtów.',
        'numeric' => ':attribute nie może być większy niż :max.',
        'string'  => ':attribute nie może być dłuższy niż :max znaków.'
    ],
    'mimes'     => ':attribute musi być plikiem typu :values.',
    'mimetypes' => ':attribute musi być plikiem typu :values.',
    'min'       => [
        'array'   => ':attribute musi mieć przynajmniej :min elementów.',
        'file'    => ':attribute musi mieć przynajmniej :min kilobajtów.',
        'numeric' => ':attribute musi być nie mniejszy od :min.',
        'string'  => ':attribute musi mieć przynajmniej :min znaków.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Zaznaczony :attribute jest nieprawidłowy.',
    'not_regex'   => 'Format :attribute jest nieprawidłowy.',
    'numeric'     => ':attribute musi być liczbą.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Pole :attribute musi być obecne.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Format :attribute jest nieprawidłowy.',
    'required'             => 'Pole :attribute jest wymagane.',
    'required_if'          => 'Pole :attribute jest wymagane gdy :other jest :value.',
    'required_unless'      => ':attribute jest wymagany jeżeli :other nie znajduje się w :values.',
    'required_with'        => 'Pole :attribute jest wymagane gdy :values jest obecny.',
    'required_with_all'    => 'Pole :attribute jest wymagane gdy :values jest obecny.',
    'required_without'     => 'Pole :attribute jest wymagane gdy :values nie jest obecny.',
    'required_without_all' => 'Pole :attribute jest wymagane gdy żadne z :values nie są obecne.',
    'same'                 => 'Pole :attribute i :other muszą się zgadzać.',
    'size'                 => [
        'array'   => ':attribute musi zawierać :size elementów.',
        'file'    => ':attribute musi mieć :size kilobajtów.',
        'numeric' => ':attribute musi mieć :size.',
        'string'  => ':attribute musi mieć :size znaków.'
    ],
    'starts_with' => ':attribute musi się zaczynać jednym z wymienionych: :values',
    'string'      => ':attribute musi być ciągiem znaków.',
    'timezone'    => ':attribute musi być prawidłową strefą czasową.',
    'unique'      => 'Taki :attribute już występuje.',
    'uploaded'    => 'Nie udało się wgrać pliku :attribute.',
    'url'         => 'Format :attribute jest nieprawidłowy.',
    'uuid'        => ':attribute musi być poprawnym identyfikatorem UUID.'
];
