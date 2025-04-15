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
    'accepted'       => ':Attribute maydonini qabul qilishingiz kerak.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':Attribute maydoniga noto‘g‘ri URL kiritildi.',
    'after'          => ':Attribute maydonida sana :date dan keyingi bo‘lishi kerak.',
    'after_or_equal' => ':Attribute maydonida sana :date ga teng yoki undan keyingi bo‘lishi kerak.',
    'alpha'          => ':Attribute maydoni faqat harflarni qabul qilishi mumkin.',
    'alpha_dash'     => ':Attribute maydoni faqat harflar, sonlar va chiziqlarni qabul qilishi mumkin.',
    'alpha_num'      => ':Attribute maydoni faqat harflar va sonlarni qabul qilishi mumkin.',
    'array'          => ':Attribute maydoni qator (array) bo‘lishi kerak.',
    'attributes'     => [
    ],
    'before'          => ':Attribute maydonida sana :date gacham bo‘lishi kerak.',
    'before_or_equal' => ':Attribute maydonida sana :date ga teng yoki undan oldin bo‘lishi kerak.',
    'between'         => [
        'array'   => ':Attribute maydonida elementlar soni :min va :max orasida bo‘lishi kerak.',
        'file'    => ':Attribute maydonidagi faylning hajmi :min va :max kilobayt orasida bo‘lishi kerak.',
        'numeric' => ':Attribute maydonining qiymati :min va :max orasida bo‘lishi kerak.',
        'string'  => ':Attribute maydonidagi belgilar soni :min va :max orasida bo‘lishi kerak.'
    ],
    'boolean'          => ':Attribute maydoni faqat mantiqiy qiymatni qabul qiladi.',
    'confirmed'        => ':Attribute maydoni tasdiqlanmadi.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':Attribute sana maydoniga noto‘g‘ri qiymat kiritildi.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':Attribute maydoni :format formatga mos kelmadi.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':Attribute va :other maydonlari farqli bo‘lishi kerak.',
    'digits'         => ':Attribute raqamli maydon uzunligi :digits bo‘lishi kerak.',
    'digits_between' => ':Attribute raqamli maydon uzunligi :min va :max orasida bo‘lishi kerak.',
    'dimensions'     => ':Attribute maydonidagi tasvir to‘g‘ri kelmaydigan o‘lchamlarga ega.',
    'distinct'       => ':Attribute maydoni takrorlanuvchi qiymatlardan iborat.',
    'email'          => ':Attribute maydoni haqiyqiy elektron pochta manzili bo‘lishi kerak.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => ':Attribute maydoni uchun tanlangan qiymat noto‘g‘ri.',
    'file'           => ':Attribute maydoni fayl turida bo‘lishi kerak.',
    'filled'         => ':Attribute maydoni to‘ldirilishi shart.',
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
    'image'    => ':Attribute maydoni tasvir turida bo‘lishi kerak.',
    'in'       => ':Attribute maydoni uchun tanlangan qiymat xato.',
    'in_array' => ':Attribute maydonining qiymati :other da mavjud emas.',
    'integer'  => ':Attribute maydoni butun son bo‘lishi kerak.',
    'ip'       => ':Attribute maydoni haqiyqiy IP manzil bo‘lishi kerak.',
    'ipv4'     => ':Attribute maydoni haqiyqiy IPv4 manzil bo‘lishi kerak.',
    'ipv6'     => ':Attribute maydoni haqiyqiy IPv6 manzil bo‘lishi kerak.',
    'json'     => ':Attribute maydoni JSON qator (string) bo‘lishi kerak.',
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
        'array'   => ':Attribute maydonidagi elmentlar soni :max tadan oshmasligi kerak.',
        'file'    => ':Attribute maydonidagi faylning hajmi :max kilobaytdan oshmasligi kerak.',
        'numeric' => ':Attribute maydoni qiymati :max dan oshmasligi kerak.',
        'string'  => ':Attribute maydonidagi belgilar soni :max tadan oshmasligi kerak.'
    ],
    'mimes'     => ':Attribute maydonidagi fayl so‘ngida keltirilgan turlardan birida bo‘lishi kerak: :values.',
    'mimetypes' => ':Attribute maydonidagi fayl so‘ngida keltirilgan turlardan birida bo‘lishi kerak: :values.',
    'min'       => [
        'array'   => ':Attribute maydonidagi elmentlar soni :min tadan kam bo‘lmasligi kerak.',
        'file'    => ':Attribute maydonidagi faylning hajmi :min kilobaytdan kam bo‘lmasligi kerak.',
        'numeric' => ':Attribute maydoni qiymati :min dan kam bo‘lmasligi kerak.',
        'string'  => ':Attribute maydonidagi belgilar soni :min tadan kam bo‘lmasligi kerak.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => ':Attribute maydoni uchun tanlangan qiymat xato.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ':Attribute maydoni son bo‘lishi kerak.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':Attribute maydoni ko‘rsatilishi kerak.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':Attribute maydoni xato formatda.',
    'required'             => ':Attribute maydoni to‘ldirilishi shart.',
    'required_if'          => ':Attribute maydoni to‘ldirilishi shart, qachonki :other maydoni :value ga teng bo‘lsa.',
    'required_unless'      => ':Attribute maydoni to‘ldirilishi shart, qachonki :other maydoni :values ga teng bo‘lmasa.',
    'required_with'        => ':Attribute maydoni to‘ldirilishi shart, qachonki :values ko‘rsatilgan bo‘lsa.',
    'required_with_all'    => ':Attribute maydoni to‘ldirilishi shart, qachonki :values ko‘rsatilgan bo‘lsa.',
    'required_without'     => ':Attribute maydoni to‘ldirilishi shart, qachonki :values ko‘rsatilmagan bo‘lsa.',
    'required_without_all' => ':Attribute maydoni to‘ldirilishi shart, qachonki :values lardan hech biri ko‘rsatilmagan bo‘lsa.',
    'same'                 => ':Attribute maydonining qiymati :other bilan bir xil bo‘lishi kerak.',
    'size'                 => [
        'array'   => ':Attribute maydonidagi elmentlar soni :size ga teng bo‘lishi kerak.',
        'file'    => ':Attribute maydonidagi faylning hajmi :size kilobaytga teng bo‘lishi kerak.',
        'numeric' => ':Attribute maydoni qiymati :size ga teng bo‘lishi kerak.',
        'string'  => ':Attribute maydonidagi belgilar soni :size ga teng bo‘lishi kerak.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':Attribute maydoni qator (string) bo‘lishi kerak.',
    'timezone'    => ':Attribute maydonining qiymati mavjud vaqt mintaqasi bo‘lishi kerak.',
    'unique'      => ':Attribute maydonining bunday qiymati mavjud (kiritlgan).',
    'uploaded'    => ':Attribute maydonini yuklash muvaffaqiyatli amalga oshmadi.',
    'url'         => ':Attribute maydoni noto‘g‘ri formatga ega.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
