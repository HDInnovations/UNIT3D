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
    'accepted'       => 'Трябва да приемете :attribute.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Полето :attribute не е валиден URL адрес.',
    'after'          => 'Полето :attribute трябва да бъде дата след :date.',
    'after_or_equal' => 'Полето :attribute трябва да бъде дата след или равна на :date.',
    'alpha'          => 'Полето :attribute трябва да съдържа само букви.',
    'alpha_dash'     => 'Полето :attribute трябва да съдържа само букви, цифри, долна черта и тире.',
    'alpha_num'      => 'Полето :attribute трябва да съдържа само букви и цифри.',
    'array'          => 'Полето :attribute трябва да бъде масив.',
    'attributes'     => [
    ],
    'before'          => 'Полето :attribute трябва да бъде дата преди :date.',
    'before_or_equal' => 'Полето :attribute трябва да бъде дата преди или равна на :date.',
    'between'         => [
        'array'   => 'Полето :attribute трябва да има между :min - :max елемента.',
        'file'    => 'Полето :attribute трябва да бъде между :min и :max килобайта.',
        'numeric' => 'Полето :attribute трябва да бъде между :min и :max.',
        'string'  => 'Полето :attribute трябва да бъде между :min и :max знака.'
    ],
    'boolean'          => 'Полето :attribute трябва да съдържа Да или Не',
    'confirmed'        => 'Полето :attribute не е потвърдено.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Полето :attribute не е валидна дата.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => 'Полето :attribute не е във формат :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Полетата :attribute и :other трябва да са различни.',
    'digits'         => 'Полето :attribute трябва да има :digits цифри.',
    'digits_between' => 'Полето :attribute трябва да има между :min и :max цифри.',
    'dimensions'     => 'Невалидни размери за снимка :attribute.',
    'distinct'       => 'Данните в полето :attribute се дублират.',
    'email'          => 'Полето :attribute е в невалиден формат.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Избранато поле :attribute вече съществува.',
    'file'           => 'Полето :attribute трябва да бъде файл.',
    'filled'         => 'Полето :attribute е задължително.',
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
    'image'    => 'Полето :attribute трябва да бъде изображение.',
    'in'       => 'Избраното поле :attribute е невалидно.',
    'in_array' => 'Полето :attribute не съществува в :other.',
    'integer'  => 'Полето :attribute трябва да бъде цяло число.',
    'ip'       => 'Полето :attribute трябва да бъде IP адрес.',
    'ipv4'     => 'Полето :attribute трябва да бъде IPv4 адрес.',
    'ipv6'     => 'Полето :attribute трябва да бъде IPv6 адрес.',
    'json'     => 'Полето :attribute трябва да бъде JSON низ.',
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
        'array'   => 'Полето :attribute трябва да има по-малко от :max елемента.',
        'file'    => 'Полето :attribute трябва да бъде по-малко от :max килобайта.',
        'numeric' => 'Полето :attribute трябва да бъде по-малко от :max.',
        'string'  => 'Полето :attribute трябва да бъде по-малко от :max знака.'
    ],
    'mimes'     => 'Полето :attribute трябва да бъде файл от тип: :values.',
    'mimetypes' => 'Полето :attribute трябва да бъде файл от тип: :values.',
    'min'       => [
        'array'   => 'Полето :attribute трябва има минимум :min елемента.',
        'file'    => 'Полето :attribute трябва да бъде минимум :min килобайта.',
        'numeric' => 'Полето :attribute трябва да бъде минимум :min.',
        'string'  => 'Полето :attribute трябва да бъде минимум :min знака.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Избраното поле :attribute е невалидно.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => 'Полето :attribute трябва да бъде число.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Полето :attribute трябва да съествува.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Полето :attribute е в невалиден формат.',
    'required'             => 'Полето :attribute е задължително.',
    'required_if'          => 'Полето :attribute се изисква, когато :other е :value.',
    'required_unless'      => 'Полето :attribute се изисква, освен ако :other не е в :values.',
    'required_with'        => 'Полето :attribute се изисква, когато :values има стойност.',
    'required_with_all'    => 'Полето :attribute е задължително, когато :values имат стойност.',
    'required_without'     => 'Полето :attribute се изисква, когато :values няма стойност.',
    'required_without_all' => 'Полето :attribute се изисква, когато никое от полетата :values няма стойност.',
    'same'                 => 'Полетата :attribute и :other трябва да съвпадат.',
    'size'                 => [
        'array'   => 'Полето :attribute трябва да има :size елемента.',
        'file'    => 'Полето :attribute трябва да бъде :size килобайта.',
        'numeric' => 'Полето :attribute трябва да бъде :size.',
        'string'  => 'Полето :attribute трябва да бъде :size знака.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => 'Полето :attribute трябва да бъде знаков низ.',
    'timezone'    => 'Полето :attribute трябва да съдържа валидна часова зона.',
    'unique'      => 'Полето :attribute вече съществува.',
    'uploaded'    => 'Неуспешно качване на :attribute.',
    'url'         => 'Полето :attribute е в невалиден формат.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
