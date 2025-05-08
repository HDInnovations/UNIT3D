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
    'accepted'       => 'ข้อมูล :attribute ต้องผ่านการยอมรับก่อน',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'ข้อมูล :attribute ต้องเป็น URL เท่านั้น',
    'after'          => 'ข้อมูล :attribute ต้องเป็นวันที่หลังจาก :date.',
    'after_or_equal' => 'ข้อมูล :attribute ต้องเป็นวันที่ตั้งแต่วันที่ :date หรือหลังจากนั้น.',
    'alpha'          => 'ข้อมูล :attribute ต้องเป็นตัวอักษรภาษาอังกฤษเท่านั้น',
    'alpha_dash'     => 'ข้อมูล :attribute ต้องเป็นตัวอักษรภาษาอังกฤษ ตัวเลข และ _ เท่านั้น',
    'alpha_num'      => 'ข้อมูล :attribute ต้องเป็นตัวอักษรภาษาอังกฤษ ตัวเลข เท่านั้น',
    'array'          => 'ข้อมูล :attribute ต้องเป็น array เท่านั้น',
    'attributes'     => [
    ],
    'before'          => 'ข้อมูล :attribute ต้องเป็นวันที่ก่อน :date.',
    'before_or_equal' => 'ข้อมูล :attribute ต้องเป็นวันที่ก่อนหรือเท่ากับวันที่ :date.',
    'between'         => [
        'array'   => 'ข้อมูล :attribute ต้องมีค่าระหว่าง :min - :max ค่า',
        'file'    => 'ข้อมูล :attribute ต้องมีขนาดระหว่าง :min - :max กิโลไบต์',
        'numeric' => 'ข้อมูล :attribute ต้องอยู่ในช่วงระหว่าง :min - :max.',
        'string'  => 'ข้อมูล :attribute ต้องมีความยาวตัวอักษรระหว่าง :min - :max ตัวอักษร'
    ],
    'boolean'          => 'ข้อมูล :attribute ต้องเป็นจริง หรือเท็จ เท่านั้น',
    'confirmed'        => 'ข้อมูล :attribute ไม่ตรงกัน',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'ข้อมูล :attribute ต้องเป็นวันที่',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => 'ข้อมูล :attribute ไม่ตรงกับข้อมูลกำหนด :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'ข้อมูล :attribute และ :other ต้องไม่เท่ากัน',
    'digits'         => 'ข้อมูล :attribute ต้องเป็น :digits',
    'digits_between' => 'ข้อมูล :attribute ต้องอยู่ในช่วงระหว่าง :min ถึง :max',
    'dimensions'     => 'ข้อมูล :attribute มีขนาดไม่ถูกต้อง.',
    'distinct'       => 'ข้อมูล :attribute มีค่าที่ซ้ำกัน',
    'email'          => 'ข้อมูล :attribute ต้องเป็นอีเมล์',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'ข้อมูล ที่ถูกเลือกจาก :attribute ไม่ถูกต้อง',
    'file'           => 'ข้อมูล :attribute ต้องเป็นไฟล์.',
    'filled'         => 'ข้อมูล :attribute จำเป็นต้องกรอก',
    'gt'             => [
        'array'   => 'ข้อมูล :attribute ต้องมีมากกว่า :value ค่า.',
        'file'    => 'ข้อมูล :attribute ต้องมีขนาดมากกว่า :value กิโลไบต์.',
        'numeric' => 'ข้อมูล :attribute ต้องมีค่ามากกว่า :value.',
        'string'  => 'ข้อมูล :attribute ต้องมีความยาวตัวอักษรมากกว่า :value ตัวอักษร.'
    ],
    'gte' => [
        'array'   => 'ข้อมูล :attribute ต้องมี :value ค่า หรือมากกว่า.',
        'file'    => 'ข้อมูล :attribute ต้องมีขนาดมากกว่าหรือเท่ากับ :value กิโลไบต์.',
        'numeric' => 'ข้อมูล :attribute ต้องมีค่ามากกว่าหรือเท่ากับ :value.',
        'string'  => 'ข้อมูล :attribute ต้องมีความยาวตัวอักษรมากกว่าหรือเท่ากับ :value ตัวอักษร.'
    ],
    'image'    => 'ข้อมูล :attribute ต้องเป็นรูปภาพ',
    'in'       => 'ข้อมูล ที่ถูกเลือกใน :attribute ไม่ถูกต้อง',
    'in_array' => 'ข้อมูล :attribute ไม่มีอยู่ภายในค่าของ :other',
    'integer'  => 'ข้อมูล :attribute ต้องเป็นตัวเลข',
    'ip'       => 'ข้อมูล :attribute ต้องเป็น IP',
    'ipv4'     => 'ข้อมูล :attribute ต้องตรงตามรูปแบบ IPv4 address.',
    'ipv6'     => 'ข้อมูล :attribute ต้องตรงตามรูปแบบ IPv6 address.',
    'json'     => 'ข้อมูล :attribute ต้องเป็นอักขระ JSON ที่สมบูรณ์',
    'lt'       => [
        'array'   => 'ข้อมูล :attribute ต้องมีน้อยกว่า :value ค่า.',
        'file'    => 'ข้อมูล :attribute ต้องมีขนาดน้อยกว่า :value กิโลไบต์.',
        'numeric' => 'ข้อมูล :attribute ต้องมีค่าน้อยกว่า :value.',
        'string'  => 'ข้อมูล :attribute ต้องมีความยาวตัวอักษรน้อยกว่า :value ตัวอักษร.'
    ],
    'lte' => [
        'array'   => 'ข้อมูล :attribute ต้องมีไม่เกิน :value ค่า.',
        'file'    => 'ข้อมูล :attribute ต้องมีขนาดน้อยกว่าหรือเท่ากับ :value กิโลไบต์.',
        'numeric' => 'ข้อมูล :attribute ต้องมีค่าน้อยกว่าหรือเท่ากับ :value.',
        'string'  => 'ข้อมูล :attribute ต้องมีความยาวตัวอักษรน้อยกว่าหรือเท่ากับ :value ตัวอักษร.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'ข้อมูล :attribute ต้องมีไม่เกิน :max ค่า',
        'file'    => 'ข้อมูล :attribute ต้องมีขนาดไม่เกิน :max กิโลไบต์',
        'numeric' => 'ข้อมูล :attribute ต้องมีค่าไม่เกิน :max.',
        'string'  => 'ข้อมูล :attribute ต้องมีความยาวตัวอักษรไม่เกิน :max ตัวอักษร'
    ],
    'mimes'     => 'ข้อมูล :attribute ต้องเป็นชนิดไฟล์: :values.',
    'mimetypes' => 'ข้อมูล :attribute ต้องเป็นชนิดไฟล์: :values.',
    'min'       => [
        'array'   => 'ข้อมูล :attribute ต้องมีอย่างน้อย :min ค่า',
        'file'    => 'ข้อมูล :attribute ต้องมีขนาดอย่างน้อย :min กิโลไบต์',
        'numeric' => 'ข้อมูล :attribute ต้องมีค่าอย่างน้อย :min.',
        'string'  => 'ข้อมูล :attribute ต้องมีความยาวตัวอักษรอย่างน้อย :min ตัวอักษร'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'ข้อมูล ที่เลือกจาก :attribute ไม่ถูกต้อง',
    'not_regex'   => 'ข้อมูล :attribute มีรูปแบบไม่ถูกต้อง.',
    'numeric'     => 'ข้อมูล :attribute ต้องเป็นตัวเลข',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'ข้อมูล :attribute ต้องเป็นปัจจุบัน',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'ข้อมูล :attribute มีรูปแบบไม่ถูกต้อง',
    'required'             => 'ข้อมูล :attribute จำเป็นต้องกรอก',
    'required_if'          => 'ข้อมูล :attribute จำเป็นต้องกรอกเมื่อ :other เป็น :value.',
    'required_unless'      => 'ข้อมูล :attribute จำเป็นต้องกรอกเว้นแต่ :other เป็น :values.',
    'required_with'        => 'ข้อมูล :attribute จำเป็นต้องกรอกเมื่อ :values มีค่า',
    'required_with_all'    => 'ข้อมูล :attribute จำเป็นต้องกรอกเมื่อ :values มีค่าทั้งหมด',
    'required_without'     => 'ข้อมูล :attribute จำเป็นต้องกรอกเมื่อ :values ไม่มีค่า',
    'required_without_all' => 'ข้อมูล :attribute จำเป็นต้องกรอกเมื่อ :values ไม่มีค่าทั้งหมด',
    'same'                 => 'ข้อมูล :attribute และ :other ต้องถูกต้อง',
    'size'                 => [
        'array'   => 'ข้อมูล :attribute ต้องเท่ากับ :size ค่า',
        'file'    => 'ข้อมูล :attribute ต้องเท่ากับ :size กิโลไบต์',
        'numeric' => 'ข้อมูล :attribute ต้องเท่ากับ :size',
        'string'  => 'ข้อมูล :attribute ต้องเท่ากับ :size ตัวอักษร'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => 'ข้อมูล :attribute ต้องเป็นอักขระ',
    'timezone'    => 'ข้อมูล :attribute ต้องเป็นข้อมูลเขตเวลาที่ถูกต้อง',
    'unique'      => 'ข้อมูล :attribute ไม่สามารถใช้ได้',
    'uploaded'    => 'ข้อมูล :attribute ไม่สามารพอัพโหลดได้.',
    'url'         => 'ข้อมูล :attribute ไม่ถูกต้อง',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
