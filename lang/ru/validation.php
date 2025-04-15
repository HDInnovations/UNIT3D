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
    'accepted'       => 'Вы должны принять :attribute.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Поле :attribute содержит недействительный URL.',
    'after'          => 'В поле :attribute должна быть дата после :date.',
    'after_or_equal' => 'В поле :attribute должна быть дата после или равняться :date.',
    'alpha'          => 'Поле :attribute может содержать только буквы.',
    'alpha_dash'     => 'Поле :attribute может содержать только буквы, цифры, дефис и нижнее подчеркивание.',
    'alpha_num'      => 'Поле :attribute может содержать только буквы и цифры.',
    'array'          => 'Поле :attribute должно быть массивом.',
    'attributes'     => [
    ],
    'before'          => 'В поле :attribute должна быть дата до :date.',
    'before_or_equal' => 'В поле :attribute должна быть дата до или равняться :date.',
    'between'         => [
        'array'   => 'Количество элементов в поле :attribute должно быть между :min и :max.',
        'file'    => 'Размер файла в поле :attribute должен быть между :min и :max Килобайт(а).',
        'numeric' => 'Поле :attribute должно быть между :min и :max.',
        'string'  => 'Количество символов в поле :attribute должно быть между :min и :max.'
    ],
    'boolean'          => 'Поле :attribute должно иметь значение логического типа.',
    'confirmed'        => 'Поле :attribute не совпадает с подтверждением.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Поле :attribute не является датой.',
    'date_equals'    => 'Поле :attribute должно быть датой равной :date.',
    'date_format'    => 'Поле :attribute не соответствует формату :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Поля :attribute и :other должны различаться.',
    'digits'         => 'Длина цифрового поля :attribute должна быть :digits.',
    'digits_between' => 'Длина цифрового поля :attribute должна быть между :min и :max.',
    'dimensions'     => 'Поле :attribute имеет недопустимые размеры изображения.',
    'distinct'       => 'Поле :attribute содержит повторяющееся значение.',
    'email'          => 'Поле :attribute должно быть действительным электронным адресом.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Выбранное значение для :attribute некорректно.',
    'file'           => 'Поле :attribute должно быть файлом.',
    'filled'         => 'Поле :attribute обязательно для заполнения.',
    'gt'             => [
        'array'   => 'Количество элементов в поле :attribute должно быть больше :value.',
        'file'    => 'Размер файла в поле :attribute должен быть больше :value Килобайт(а).',
        'numeric' => 'Поле :attribute должно быть больше :value.',
        'string'  => 'Количество символов в поле :attribute должно быть больше :value.'
    ],
    'gte' => [
        'array'   => 'Количество элементов в поле :attribute должно быть больше или равно :value.',
        'file'    => 'Размер файла в поле :attribute должен быть больше или равен :value Килобайт(а).',
        'numeric' => 'Поле :attribute должно быть больше или равно :value.',
        'string'  => 'Количество символов в поле :attribute должно быть больше или равно :value.'
    ],
    'image'    => 'Поле :attribute должно быть изображением.',
    'in'       => 'Выбранное значение для :attribute ошибочно.',
    'in_array' => 'Поле :attribute не существует в :other.',
    'integer'  => 'Поле :attribute должно быть целым числом.',
    'ip'       => 'Поле :attribute должно быть действительным IP-адресом.',
    'ipv4'     => 'Поле :attribute должно быть действительным IPv4-адресом.',
    'ipv6'     => 'Поле :attribute должно быть действительным IPv6-адресом.',
    'json'     => 'Поле :attribute должно быть JSON строкой.',
    'lt'       => [
        'array'   => 'Количество элементов в поле :attribute должно быть меньше :value.',
        'file'    => 'Размер файла в поле :attribute должен быть меньше :value Килобайт(а).',
        'numeric' => 'Поле :attribute должно быть меньше :value.',
        'string'  => 'Количество символов в поле :attribute должно быть меньше :value.'
    ],
    'lte' => [
        'array'   => 'Количество элементов в поле :attribute должно быть меньше или равно :value.',
        'file'    => 'Размер файла в поле :attribute должен быть меньше или равен :value Килобайт(а).',
        'numeric' => 'Поле :attribute должно быть меньше или равно :value.',
        'string'  => 'Количество символов в поле :attribute должно быть меньше или равно :value.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'Количество элементов в поле :attribute не может превышать :max.',
        'file'    => 'Размер файла в поле :attribute не может быть более :max Килобайт(а).',
        'numeric' => 'Поле :attribute не может быть более :max.',
        'string'  => 'Количество символов в поле :attribute не может превышать :max.'
    ],
    'mimes'     => 'Поле :attribute должно быть файлом одного из следующих типов: :values.',
    'mimetypes' => 'Поле :attribute должно быть файлом одного из следующих типов: :values.',
    'min'       => [
        'array'   => 'Количество элементов в поле :attribute должно быть не менее :min.',
        'file'    => 'Размер файла в поле :attribute должен быть не менее :min Килобайт(а).',
        'numeric' => 'Поле :attribute должно быть не менее :min.',
        'string'  => 'Количество символов в поле :attribute должно быть не менее :min.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Выбранное значение для :attribute ошибочно.',
    'not_regex'   => 'Выбранный формат для :attribute ошибочный.',
    'numeric'     => 'Поле :attribute должно быть числом.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Поле :attribute должно присутствовать.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Поле :attribute имеет ошибочный формат.',
    'required'             => 'Поле :attribute обязательно для заполнения.',
    'required_if'          => 'Поле :attribute обязательно для заполнения, когда :other равно :value.',
    'required_unless'      => 'Поле :attribute обязательно для заполнения, когда :other не равно :values.',
    'required_with'        => 'Поле :attribute обязательно для заполнения, когда :values указано.',
    'required_with_all'    => 'Поле :attribute обязательно для заполнения, когда :values указано.',
    'required_without'     => 'Поле :attribute обязательно для заполнения, когда :values не указано.',
    'required_without_all' => 'Поле :attribute обязательно для заполнения, когда ни одно из :values не указано.',
    'same'                 => 'Значения полей :attribute и :other должны совпадать.',
    'size'                 => [
        'array'   => 'Количество элементов в поле :attribute должно быть равным :size.',
        'file'    => 'Размер файла в поле :attribute должен быть равен :size Килобайт(а).',
        'numeric' => 'Поле :attribute должно быть равным :size.',
        'string'  => 'Количество символов в поле :attribute должно быть равным :size.'
    ],
    'starts_with' => 'Поле :attribute должно начинаться из одного из следующих значений: :values',
    'string'      => 'Поле :attribute должно быть строкой.',
    'timezone'    => 'Поле :attribute должно быть действительным часовым поясом.',
    'unique'      => 'Такое значение поля :attribute уже существует.',
    'uploaded'    => 'Загрузка поля :attribute не удалась.',
    'url'         => 'Поле :attribute имеет ошибочный формат.',
    'uuid'        => 'Поле :attribute должно быть корректным UUID.'
];
