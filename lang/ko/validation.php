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
    'accepted'       => ':attribute을(를) 동의해야 합니다.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute은(는) 유효한 URL이 아닙니다.',
    'after'          => ':attribute은(는) :date 이후 날짜여야 합니다.',
    'after_or_equal' => ':attribute은(는) :date 이후 날짜이거나 같은 날짜여야 합니다.',
    'alpha'          => ':attribute은(는) 문자만 포함할 수 있습니다.',
    'alpha_dash'     => ':attribute은(는) 문자, 숫자, 대쉬(-)만 포함할 수 있습니다.',
    'alpha_num'      => ':attribute은(는) 문자와 숫자만 포함할 수 있습니다.',
    'array'          => ':attribute은(는) 배열이어야 합니다.',
    'attributes'     => [
    ],
    'before'          => ':attribute은(는) :date 이전 날짜여야 합니다.',
    'before_or_equal' => ':attribute은(는) :date 이전 날짜이거나 같은 날짜여야 합니다.',
    'between'         => [
        'array'   => ':attribute은(는) :min에서 :max 개의 항목이 있어야 합니다.',
        'file'    => ':attribute은(는) :min에서 :max 킬로바이트 사이여야 합니다.',
        'numeric' => ':attribute은(는) :min에서 :max 사이여야 합니다.',
        'string'  => ':attribute은(는) :min에서 :max 문자 사이여야 합니다.'
    ],
    'boolean'          => ':attribute은(는) true 또는 false 이어야 합니다.',
    'confirmed'        => ':attribute 확인 항목이 일치하지 않습니다.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute은(는) 유효한 날짜가 아닙니다.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute이(가) :format 형식과 일치하지 않습니다.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute와(과) :other은(는) 서로 달라야 합니다.',
    'digits'         => ':attribute은(는) :digits 자리 숫자여야 합니다.',
    'digits_between' => ':attribute)은(는) :min에서 :max 자리 사이여야 합니다.',
    'dimensions'     => ':attribute은(는) 유효하지 않는 이미지 크기입니다.',
    'distinct'       => ':attribute 필드에 중복된 값이 있습니다.',
    'email'          => ':attribute은(는) 유효한 이메일 주소여야 합니다.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => '선택된 :attribute은(는) 유효하지 않습니다.',
    'file'           => ':attribute은(는) 파일이어야 합니다.',
    'filled'         => ':attribute 필드는 값이 있어야 합니다.',
    'gt'             => [
        'array'   => ':attribute의 항목수는 :value개 보다 많아야 합니다.',
        'file'    => ':attribute의 용량은 :value킬로바이트보다 커야 합니다.',
        'numeric' => ':attribute의 값은 :value보다 커야 합니다.',
        'string'  => ':attribute의 길이는 :value보다 길어야 합니다.'
    ],
    'gte' => [
        'array'   => ':attribute의 항목수는 :value개 보다 같거나 많아야 합니다.',
        'file'    => ':attribute의 용량은 :value킬로바이트보다 같거나 커야 합니다.',
        'numeric' => ':attribute의 값은 :value보다 같거나 커야 합니다.',
        'string'  => ':attribute의 길이는 :value보다 같거나 길어야 합니다.'
    ],
    'image'    => ':attribute은(는) 이미지여야 합니다.',
    'in'       => '선택된 :attribute은(는) 유효하지 않습니다.',
    'in_array' => ':attribute 필드는 :other에 존재하지 않습니다.',
    'integer'  => ':attribute은(는) 정수여야 합니다.',
    'ip'       => ':attribute은(는) 유효한 IP 주소여야 합니다.',
    'ipv4'     => ':attribute은(는) 유효한 IPv4 주소여야 합니다.',
    'ipv6'     => ':attribute은(는) 유효한 IPv6 주소여야 합니다.',
    'json'     => ':attribute은(는) JSON 문자열이어야 합니다.',
    'lt'       => [
        'array'   => ':attribute의 항목수는 :value개 보다 작아야 합니다.',
        'file'    => ':attribute의 용량은 :value킬로바이트보다 작아야 합니다.',
        'numeric' => ':attribute의 값은 :value보다 작아야 합니다.',
        'string'  => ':attribute의 길이는 :value보다 짧아야 합니다.'
    ],
    'lte' => [
        'array'   => ':attribute의 항목수는 :value개 보다 같거나 작아야 합니다.',
        'file'    => ':attribute의 용량은 :value킬로바이트보다 같거나 작아야 합니다.',
        'numeric' => ':attribute의 값은 :value보다 같거나 작아야 합니다.',
        'string'  => ':attribute의 길이는 :value보다 같거나 짧아야 합니다.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute은(는) :max개보다 많을 수 없습니다.',
        'file'    => ':attribute은(는) :max킬로바이트보다 클 수 없습니다.',
        'numeric' => ':attribute은(는) :max보다 클 수 없습니다.',
        'string'  => ':attribute은(는) :max자보다 클 수 없습니다.'
    ],
    'mimes'     => ':attribute은(는) 다음의 파일 형식이어야 합니다: :values.',
    'mimetypes' => ':attribute은(는) 다음의 파일 형식이어야 합니다: :values.',
    'min'       => [
        'array'   => ':attribute은(는) 최소한 :min개의 항목이 있어야 합니다.',
        'file'    => ':attribute은(는) 최소한 :min킬로바이트이어야 합니다.',
        'numeric' => ':attribute은(는) 최소한 :min이어야 합니다.',
        'string'  => ':attribute은(는) 최소한 :min자이어야 합니다.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => '선택된 :attribute이(가) 유효하지 않습니다.',
    'not_regex'   => ':attribute의 형식이 올바르지 않습니다.',
    'numeric'     => ':attribute은(는) 숫자여야 합니다.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute 필드가 있어야 합니다.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':attribute 형식이 유효하지 않습니다.',
    'required'             => ':attribute 필드는 필수입니다.',
    'required_if'          => ':other이(가) :value 일 때 :attribute 필드는 필수입니다.',
    'required_unless'      => ':other이(가) :values에 없다면 :attribute 필드는 필수입니다.',
    'required_with'        => ':values이(가) 있는 경우 :attribute 필드는 필수입니다.',
    'required_with_all'    => ':values이(가) 모두 있는 경우 :attribute 필드는 필수입니다.',
    'required_without'     => ':values이(가) 없는 경우 :attribute 필드는 필수입니다.',
    'required_without_all' => ':values이(가) 모두 없는 경우 :attribute 필드는 필수입니다.',
    'same'                 => ':attribute와(과) :other은(는) 일치해야 합니다.',
    'size'                 => [
        'array'   => ':attribute은(는) :size개의 항목을 포함해야 합니다.',
        'file'    => ':attribute은(는) :size킬로바이트여야 합니다.',
        'numeric' => ':attribute은(는) :size (이)여야 합니다.',
        'string'  => ':attribute은(는) :size자여야 합니다.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute은(는) 문자열이어야 합니다.',
    'timezone'    => ':attribute은(는) 올바른 시간대 이어야 합니다.',
    'unique'      => ':attribute은(는) 이미 사용 중입니다.',
    'uploaded'    => ':attribute을(를) 업로드하지 못했습니다.',
    'url'         => ':attribute 형식은 유효하지 않습니다.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
