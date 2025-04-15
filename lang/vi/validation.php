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
    'accepted'       => 'Trường :attribute phải được chấp nhận.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Trường :attribute không phải là một URL hợp lệ.',
    'after'          => 'Trường :attribute phải là một ngày sau ngày :date.',
    'after_or_equal' => 'Trường :attribute phải là thời gian bắt đầu sau hoặc đúng bằng :date.',
    'alpha'          => 'Trường :attribute chỉ có thể chứa các chữ cái.',
    'alpha_dash'     => 'Trường :attribute chỉ có thể chứa chữ cái, số và dấu gạch ngang.',
    'alpha_num'      => 'Trường :attribute chỉ có thể chứa chữ cái và số.',
    'array'          => 'Trường :attribute phải là dạng mảng.',
    'attributes'     => [
    ],
    'before'          => 'Trường :attribute phải là một ngày trước ngày :date.',
    'before_or_equal' => 'Trường :attribute phải là thời gian bắt đầu trước hoặc đúng bằng :date.',
    'between'         => [
        'array'   => 'Trường :attribute phải có từ :min - :max phần tử.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải từ :min - :max kB.',
        'numeric' => 'Trường :attribute phải nằm trong khoảng :min - :max.',
        'string'  => 'Trường :attribute phải từ :min - :max ký tự.'
    ],
    'boolean'          => 'Trường :attribute phải là true hoặc false.',
    'confirmed'        => 'Giá trị xác nhận trong trường :attribute không khớp.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Trường :attribute không phải là định dạng của ngày-tháng.',
    'date_equals'    => 'Trường :attribute phải là một ngày bằng với :date.',
    'date_format'    => 'Trường :attribute không giống với định dạng :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Trường :attribute và :other phải khác nhau.',
    'digits'         => 'Độ dài của trường :attribute phải gồm :digits chữ số.',
    'digits_between' => 'Độ dài của trường :attribute phải nằm trong khoảng :min and :max chữ số.',
    'dimensions'     => 'Trường :attribute có kích thước không hợp lệ.',
    'distinct'       => 'Trường :attribute có giá trị trùng lặp.',
    'email'          => 'Trường :attribute phải là một địa chỉ email hợp lệ.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'file'           => 'Trường :attribute phải là một tệp tin.',
    'filled'         => 'Trường :attribute không được bỏ trống.',
    'gt'             => [
        'array'   => 'Mảng :attribute phải có nhiều hơn :value phần tử.',
        'file'    => 'Dung lượng trường :attribute phải lớn hơn :value kilobytes.',
        'numeric' => 'Giá trị trường :attribute phải lớn hơn :value.',
        'string'  => 'Độ dài trường :attribute phải nhiều hơn :value kí tự.'
    ],
    'gte' => [
        'array'   => 'Mảng :attribute phải có ít nhất :value phần tử.',
        'file'    => 'Dung lượng trường :attribute phải lớn hơn hoặc bằng :value kilobytes.',
        'numeric' => 'Giá trị trường :attribute phải lớn hơn hoặc bằng :value.',
        'string'  => 'Độ dài trường :attribute phải lớn hơn hoặc bằng :value kí tự.'
    ],
    'image'    => 'Trường :attribute phải là định dạng hình ảnh.',
    'in'       => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'in_array' => 'Trường :attribute phải thuộc tập cho phép: :other.',
    'integer'  => 'Trường :attribute phải là một số nguyên.',
    'ip'       => 'Trường :attribute phải là một địa chỉ IP.',
    'ipv4'     => 'Trường :attribute phải là một địa chỉ IPv4.',
    'ipv6'     => 'Trường :attribute phải là một địa chỉ IPv6.',
    'json'     => 'Trường :attribute phải là một chuỗi JSON.',
    'lt'       => [
        'array'   => 'Mảng :attribute phải có ít hơn :value phần tử.',
        'file'    => 'Dung lượng trường :attribute phải nhỏ hơn :value kilobytes.',
        'numeric' => 'Giá trị trường :attribute phải nhỏ hơn :value.',
        'string'  => 'Độ dài trường :attribute phải nhỏ hơn :value kí tự.'
    ],
    'lte' => [
        'array'   => 'Mảng :attribute không được có nhiều hơn :value phần tử.',
        'file'    => 'Dung lượng trường :attribute phải nhỏ hơn hoặc bằng :value kilobytes.',
        'numeric' => 'Giá trị trường :attribute phải nhỏ hơn hoặc bằng :value.',
        'string'  => 'Độ dài trường :attribute phải nhỏ hơn hoặc bằng :value kí tự.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'Trường :attribute không được lớn hơn :max phần tử.',
        'file'    => 'Dung lượng tập tin trong trường :attribute không được lớn hơn :max kB.',
        'numeric' => 'Trường :attribute không được lớn hơn :max.',
        'string'  => 'Trường :attribute không được lớn hơn :max ký tự.'
    ],
    'mimes'     => 'Trường :attribute phải là một tập tin có định dạng: :values.',
    'mimetypes' => 'Trường :attribute phải là một tập tin có định dạng: :values.',
    'min'       => [
        'array'   => 'Trường :attribute phải có tối thiểu :min phần tử.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải tối thiểu :min kB.',
        'numeric' => 'Trường :attribute phải tối thiểu là :min.',
        'string'  => 'Trường :attribute phải có tối thiểu :min ký tự.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'not_regex'   => 'Trường :attribute có định dạng không hợp lệ.',
    'numeric'     => 'Trường :attribute phải là một số.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Trường :attribute phải được cung cấp.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Trường :attribute có định dạng không hợp lệ.',
    'required'             => 'Trường :attribute không được bỏ trống.',
    'required_if'          => 'Trường :attribute không được bỏ trống khi trường :other là :value.',
    'required_unless'      => 'Trường :attribute không được bỏ trống trừ khi :other là :values.',
    'required_with'        => 'Trường :attribute không được bỏ trống khi một trong :values có giá trị.',
    'required_with_all'    => 'Trường :attribute không được bỏ trống khi tất cả :values có giá trị.',
    'required_without'     => 'Trường :attribute không được bỏ trống khi một trong :values không có giá trị.',
    'required_without_all' => 'Trường :attribute không được bỏ trống khi tất cả :values không có giá trị.',
    'same'                 => 'Trường :attribute và :other phải giống nhau.',
    'size'                 => [
        'array'   => 'Trường :attribute phải chứa :size phần tử.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải bằng :size kB.',
        'numeric' => 'Trường :attribute phải bằng :size.',
        'string'  => 'Trường :attribute phải chứa :size ký tự.'
    ],
    'starts_with' => 'Trường :attribute phải được bắt đầu bằng một trong những giá trị sau: :values',
    'string'      => 'Trường :attribute phải là một chuỗi ký tự.',
    'timezone'    => 'Trường :attribute phải là một múi giờ hợp lệ.',
    'unique'      => 'Trường :attribute đã có trong cơ sở dữ liệu.',
    'uploaded'    => 'Trường :attribute tải lên thất bại.',
    'url'         => 'Trường :attribute không giống với định dạng một URL.',
    'uuid'        => 'Trường :attribute phải là một chuỗi UUID hợp lệ.'
];
