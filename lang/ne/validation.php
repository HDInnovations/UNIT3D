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
    'accepted'       => ':attribute स्वीकार गरिएको हुनुपर्छ।',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute URL अमान्य छ।',
    'after'          => ':attribute को मिति :date भन्दा पछि हुनुपर्छ।',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha'          => ':attribute मा अक्षरहरु मात्र हुनसक्छ।',
    'alpha_dash'     => ':attribute मा अक्षर, संख्या र ड्यासहरू मात्र हुनसक्छ।',
    'alpha_num'      => ':attribute मा अक्षर र संख्याहरू मात्र हुनसक्छ।',
    'array'          => ':attribute array हुनुपर्छ।',
    'attributes'     => [
    ],
    'before'          => ':attribute को मिति :date भन्दा अघि हुनुपर्छ।',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between'         => [
        'array'   => ':attribute मा आइटमको संख्या :min र :max को बिचमा हुनुपर्छ।',
        'file'    => ':attribute :min र :max kilobytes को बिचमा हुनुपर्छ।',
        'numeric' => ':attribute :min र :max को बिचमा हुनुपर्छ।',
        'string'  => ':attribute :min र :max वर्णको बिचमा हुनुपर्छ।'
    ],
    'boolean'          => ':attribute ठिक अथवा बेठिक हुनुपर्छ।',
    'confirmed'        => ':attribute दाेहाेर्याइएकाे मिलेन।',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute को मिति मिलेन।',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute को ढाँचा :format जस्तो हुनुपर्छ।',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute र :other फरक हुनुपर्छ।',
    'digits'         => ':attribute :digits अंकको हुनुपर्छ।',
    'digits_between' => ':attribute :min देखि :max अंकको हुनुपर्छ।',
    'dimensions'     => 'The :attribute has invalid image dimensions.',
    'distinct'       => 'The :attribute field has a duplicate value.',
    'email'          => ':attribute को इमेल ठेगाना मिलेन।',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'छानिएको :attribute अमान्य छ।',
    'file'           => 'The :attribute must be a file.',
    'filled'         => ':attribute दिइएको हुनुपर्छ।',
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
    'image'    => ':attribute मा फोटो हुनुपर्छ।',
    'in'       => 'छानिएको :attribute अमान्य छ।',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer'  => ':attribute पूर्ण संख्या हुनुपर्छ।',
    'ip'       => ':attribute मा दिइएको मान्य IP ठेगाना हुनुपर्छ।',
    'ipv4'     => 'The :attribute must be a valid IPv4 address.',
    'ipv6'     => 'The :attribute must be a valid IPv6 address.',
    'json'     => ':attribute मा दिइएको मान्य JSON string हुनुपर्छ।',
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
        'array'   => ':attribute मा :max भन्दा बढी आइटम हुनुहुदैन।',
        'file'    => ':attribute :max kilobytes भन्दा बढी हुनुहुदैन।',
        'numeric' => ':attribute :max भन्दा बढी हुनुहुदैन।',
        'string'  => ':attribute :max वर्ण भन्दा बढी हुनुहुदैन।'
    ],
    'mimes'     => ':attribute :values प्रकारको फाइल हुनुपर्छ।',
    'mimetypes' => ':attribute :values प्रकारको फाइल हुनुपर्छ।',
    'min'       => [
        'array'   => ':attribute मा कम्तिमा :min आइटम हुनुपर्छ।',
        'file'    => ':attribute कम्तिमा :min kilobytesको हुनुपर्छ।',
        'numeric' => ':attribute कम्तिमा :min हुनुपर्छ।',
        'string'  => ':attribute कम्तिमा :min वर्णको हुनुपर्छ।'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'छानिएको :attribute अमान्य छ।',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ':attribute संख्या हुनुपर्छ।',
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
    'regex'                => ':attribute को ढाँचा मिलेन।',
    'required'             => ':attribute दिइएको हुनुपर्छ।',
    'required_if'          => ':attribute चाहिन्छ जब :other :value हुन्छ।',
    'required_unless'      => ':other :values मा नभएसम्म :attribute चाहिन्छ।',
    'required_with'        => ':values भएसम्म :attribute चाहिन्छ।',
    'required_with_all'    => ':values भएसम्म :attribute चाहिन्छ।',
    'required_without'     => ':values नभएको बेला :attribute चाहिन्छ।',
    'required_without_all' => 'कुनैपनि :values नभएको बेला :attribute चाहिन्छ।',
    'same'                 => ':attribute र :other मिल्नुपर्छ।',
    'size'                 => [
        'array'   => ':attribute मा :size आइटम हुनुपर्छ।',
        'file'    => ':attribute :size kilobytesको हुनुपर्छ।',
        'numeric' => ':attribute :size हुनुपर्छ।',
        'string'  => ':attribute :size वर्णको हुनुपर्छ।.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute string हुनुपर्छ।',
    'timezone'    => ':attribute मान्य समय क्षेत्र हुनुपर्छ।',
    'unique'      => 'यो :attribute पहिले नै लिई सकेको छ।',
    'uploaded'    => 'The :attribute failed to upload.',
    'url'         => ':attribute को ढांचा मिलेन।',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
