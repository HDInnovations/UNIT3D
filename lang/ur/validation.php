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
    'accepted'       => ':attribute تسلیم کرنا لازمی ہے۔',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute قابلِ قبول یو آر ایل نہیں ہے۔',
    'after'          => ':attribute لازماً :date کے بعد کی کوئی تاریخ ہو۔',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha'          => ':attribute صرف حروفِ تہجی پر مشتمل ہو سکتا ہے۔',
    'alpha_dash'     => ':attribute صرف حروفِ تہجی، اعداد، ڈیشِز پر مشتمل ہو سکتا ہے۔',
    'alpha_num'      => ':attribute میں صرف حروفِ تہجی و اعداد شامل ہو سکتے ہیں۔',
    'array'          => ':attribute لازماً کسی رینج پر مشتمل ہو۔',
    'attributes'     => [
    ],
    'before'          => ':attribute لازماً :date سے پہلے کی کوئی تاریخ ہو۔',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between'         => [
        'array'   => ':attribute لازماً :min اور :max آئٹمز کے درمیان ہو۔',
        'file'    => ':attribute لازماً :min اور :max کلو بائٹس کے درمیان ہو۔',
        'numeric' => ':attribute لازماً :min اور :max کے درمیان ہو۔',
        'string'  => ':attribute لازماً :min اور :max کریکٹرز کے درمیان ہو۔'
    ],
    'boolean'          => ':attribute لازماً درست یا غلط ہونا چاہیے۔',
    'confirmed'        => ':attribute تصدیق سے مطابقت نہیں رکھتا۔',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute قابلِ قبول تاریخ نہیں ہے۔',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute فارمیٹ :format کے مطابق نہیں ہے۔',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute اور :other لازماً مختلف ہوں۔',
    'digits'         => ':attribute لازماً :digits اعداد ہوں۔',
    'digits_between' => ':attribute لازماً :min اور :max اعداد کے درمیان ہو۔',
    'dimensions'     => 'The :attribute has invalid image dimensions.',
    'distinct'       => ':attribute کی دہری ویلیو ہے۔',
    'email'          => ':attribute لازماً قابلِ قبول ای میل ہو۔',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'منتخب :attribute درست نہیں ہے۔',
    'file'           => 'The :attribute must be a file.',
    'filled'         => ':attribute کو بھرنا ضروری ہے۔',
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
    'image'    => ':attribute لازماً کوئی تصویر ہو۔',
    'in'       => 'منتخب :attribute قابلِ قبول نہیں ہے۔',
    'in_array' => ':attribute فیلڈ :other میں موجود نہیں ہے۔',
    'integer'  => ':attribute لازماً کوئی عدد ہو۔',
    'ip'       => ':attribute لازماً قابلِ قبول آئی پی پتہ ہو۔',
    'ipv4'     => 'The :attribute must be a valid IPv4 address.',
    'ipv6'     => 'The :attribute must be a valid IPv6 address.',
    'json'     => ':attribute لازماً قابلِ قبول JSON سٹرِنگ ہو۔',
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
        'array'   => ':attribute میں :max سے زیادہ آئٹمز نہیں ہو سکتیں۔',
        'file'    => ':attribute کو :max کلو بائٹس سے زیادہ نہیں ہونا چاہیے۔',
        'numeric' => ':attribute کو :max سے بڑا نہیں ہونا چاہیے۔',
        'string'  => ':attribute کو :max کریکٹرز سے زیادہ نہیں ہونا چاہیے۔'
    ],
    'mimes'     => ':attribute لازماً :type :values قسم کی فائل ہو۔',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min'       => [
        'array'   => ':attribute میں لازماً کم از کم :min آئٹمز ہوں۔',
        'file'    => ':attribute لازماً کم از کم :min کلو بائٹس کی ہو۔',
        'numeric' => ':attribute لازماً کم از کم :min ہو۔',
        'string'  => ':attribute لازماً کم از کم :min کریکٹرز طویل ہو۔'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'منتخب :attribute قابلِ قبول نہیں ہے۔',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ':attribute لازماً کوئی عدد ہو۔',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute فیلڈ موجود ہونا ضروری ہے۔',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':attribute قابلِ قبول فارمیٹ میں نہیں ہے۔',
    'required'             => ':attribute فیلڈ درکار ہے۔',
    'required_if'          => ':attribute درکار ہے اگر :other کی ویلیو :value ہو۔',
    'required_unless'      => 'جب تک :other :values میں نہ ہو تو :attribute فیلڈ درکار ہے۔',
    'required_with'        => ':attribute فیلڈ درکار ہے اگر :values موجود ہوں۔',
    'required_with_all'    => ':attribute فیلڈ درکار ہے اگر :values موجود ہوں۔',
    'required_without'     => ':attribute درکار ہے جب :values موجود ہو۔',
    'required_without_all' => ':attribute فیلڈ درکار ہے جب :values میں سے کوئی بھی موجود نہ ہو۔',
    'same'                 => ':attribute اور :other لازماً ایک دوسرے سے مماثل ہوں۔',
    'size'                 => [
        'array'   => ':attribute میں لازماً :size آئٹمز شامل ہوں۔',
        'file'    => ':attribute کا سائز لازماً :size کلو بائٹس ہو۔',
        'numeric' => ':attribute لازماً :size ہوں۔',
        'string'  => ':attribute لازماً :size کریکٹرز پر مشتمل ہو۔'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute لازماً کوئی سٹرنگ ہو۔',
    'timezone'    => ':attribute لازماً کوئی قابلِ قبول خطۂِ وقت ہو۔',
    'unique'      => ':attribute کو پہلے ہی کسی نے حاصل کر لیا ہے۔',
    'uploaded'    => 'The :attribute failed to upload.',
    'url'         => ':attribute فارمیٹ قابلِ قبول نہیں ہے۔',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
