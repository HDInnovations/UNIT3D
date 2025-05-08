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
    'accepted'       => 'Ви повинні прийняти :attribute.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Поле :attribute не є правильним URL.',
    'after'          => 'Поле :attribute має містити дату не раніше :date.',
    'after_or_equal' => 'Поле :attribute має містити дату не раніше або дорівнюватися :date.',
    'alpha'          => 'Поле :attribute має містити лише літери.',
    'alpha_dash'     => 'Поле :attribute має містити лише літери, цифри та підкреслення.',
    'alpha_num'      => 'Поле :attribute має містити лише літери та цифри.',
    'array'          => 'Поле :attribute має бути масивом.',
    'attributes'     => [
    ],
    'before'          => 'Поле :attribute має містити дату не пізніше :date.',
    'before_or_equal' => 'Поле :attribute має містити дату не пізніше або дорівнюватися :date.',
    'between'         => [
        'array'   => 'Поле :attribute має містити від :min до :max елементів.',
        'file'    => 'Розмір файлу в полі :attribute має бути не менше :min та не більше :max кілобайт.',
        'numeric' => 'Поле :attribute має бути між :min та :max.',
        'string'  => 'Текст в полі :attribute має бути не менше :min та не більше :max символів.'
    ],
    'boolean'          => 'Поле :attribute повинне містити логічний тип.',
    'confirmed'        => 'Поле :attribute не збігається з підтвердженням.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Поле :attribute не є датою.',
    'date_equals'    => 'Поле :attribute має бути датою рівною :date.',
    'date_format'    => 'Поле :attribute не відповідає формату :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Поля :attribute та :other повинні бути різними.',
    'digits'         => 'Довжина цифрового поля :attribute повинна дорівнювати :digits.',
    'digits_between' => 'Довжина цифрового поля :attribute повинна бути від :min до :max.',
    'dimensions'     => 'Поле :attribute містіть неприпустимі розміри зображення.',
    'distinct'       => 'Поле :attribute містить значення, яке дублюється.',
    'email'          => 'Поле :attribute повинне містити коректну електронну адресу.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Вибране для :attribute значення не коректне.',
    'file'           => 'Поле :attribute має містити файл.',
    'filled'         => 'Поле :attribute є обов\'язковим для заповнення.',
    'gt'             => [
        'array'   => 'Поле :attribute має містити більше ніж :value елементів.',
        'file'    => 'Поле :attribute має бути більше ніж :value кілобайт.',
        'numeric' => 'Поле :attribute має бути більше ніж :value.',
        'string'  => 'Поле :attribute має бути більше ніж :value символів.'
    ],
    'gte' => [
        'array'   => 'Поле :attribute має містити :value чи більше елементів.',
        'file'    => 'Поле :attribute має доріванювати чи бути більше ніж :value кілобайт.',
        'numeric' => 'Поле :attribute має доріванювати чи бути більше ніж :value.',
        'string'  => 'Поле :attribute має доріванювати чи бути більше ніж :value символів.'
    ],
    'image'    => 'Поле :attribute має містити зображення.',
    'in'       => 'Вибране для :attribute значення не коректне.',
    'in_array' => 'Значення поля :attribute не міститься в :other.',
    'integer'  => 'Поле :attribute має містити ціле число.',
    'ip'       => 'Поле :attribute має містити IP адресу.',
    'ipv4'     => 'Поле :attribute має містити IPv4 адресу.',
    'ipv6'     => 'Поле :attribute має містити IPv6 адресу.',
    'json'     => 'Дані поля :attribute мають бути в форматі JSON.',
    'lt'       => [
        'array'   => 'Поле :attribute має містити менше ніж :value items.',
        'file'    => 'Поле :attribute має бути менше ніж :value кілобайт.',
        'numeric' => 'Поле :attribute має бути менше ніж :value.',
        'string'  => 'Поле :attribute має бути менше ніж :value символів.'
    ],
    'lte' => [
        'array'   => 'Поле :attribute має містити не більше ніж :value елементів.',
        'file'    => 'Поле :attribute має дорівнювати чи бути менше ніж :value кілобайт.',
        'numeric' => 'Поле :attribute має дорівнювати чи бути менше ніж :value.',
        'string'  => 'Поле :attribute має дорівнювати чи бути менше ніж :value символів.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'Поле :attribute повинне містити не більше :max елементів.',
        'file'    => 'Файл в полі :attribute має бути не більше :max кілобайт.',
        'numeric' => 'Поле :attribute має бути не більше :max.',
        'string'  => 'Текст в полі :attribute повинен мати довжину не більшу за :max.'
    ],
    'mimes'     => 'Поле :attribute повинне містити файл одного з типів: :values.',
    'mimetypes' => 'Поле :attribute повинне містити файл одного з типів: :values.',
    'min'       => [
        'array'   => 'Поле :attribute повинне містити не менше :min елементів.',
        'file'    => 'Розмір файлу в полі :attribute має бути не меншим :min кілобайт.',
        'numeric' => 'Поле :attribute повинне бути не менше :min.',
        'string'  => 'Текст в полі :attribute повинен містити не менше :min символів.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Вибране для :attribute значення не коректне.',
    'not_regex'   => 'Формат поля :attribute не вірний.',
    'numeric'     => 'Поле :attribute повинно містити число.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Поле :attribute повинне бути присутнє.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Поле :attribute має хибний формат.',
    'required'             => 'Поле :attribute є обов\'язковим для заповнення.',
    'required_if'          => 'Поле :attribute є обов\'язковим для заповнення, коли :other є рівним :value.',
    'required_unless'      => 'Поле :attribute є обов\'язковим для заповнення, коли :other відрізняється від :values',
    'required_with'        => 'Поле :attribute є обов\'язковим для заповнення, коли :values вказано.',
    'required_with_all'    => 'Поле :attribute є обов\'язковим для заповнення, коли :values вказано.',
    'required_without'     => 'Поле :attribute є обов\'язковим для заповнення, коли :values не вказано.',
    'required_without_all' => 'Поле :attribute є обов\'язковим для заповнення, коли :values не вказано.',
    'same'                 => 'Поля :attribute та :other мають співпадати.',
    'size'                 => [
        'array'   => 'Поле :attribute повинне містити :size елементів.',
        'file'    => 'Файл в полі :attribute має бути розміром :size кілобайт.',
        'numeric' => 'Поле :attribute має бути довжини :size.',
        'string'  => 'Текст в полі :attribute повинен містити :size символів.'
    ],
    'starts_with' => 'Поле :attribute повинне розпочинатись з одного з наступного: :values',
    'string'      => 'Поле :attribute повинне містити текст.',
    'timezone'    => 'Поле :attribute повинне містити коректну часову зону.',
    'unique'      => 'Таке значення поля :attribute вже існує.',
    'uploaded'    => 'Завантаження поля :attribute не вдалося.',
    'url'         => 'Формат поля :attribute неправильний.',
    'uuid'        => 'Поле :attribute має бути коректним UUID ідентифікатором.'
];
