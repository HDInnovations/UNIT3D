<?php
return [
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
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted' => '该 :attribute 必须被接受。',
    'accepted_if' => '该 :attribute 必须被接受，当 :other 是 :value。',
    'active_url' => '该 :attribute 不是有效的网址。',
    'after' => '该 :attribute 必须在:date 之后。',
    'after_or_equal' => '该 :attribute 必须大于或等于 :date。',
    'alpha' => '该 :attribute 只能包含字母。',
    'alpha_dash' => '该 :attribute 只能包含字母，数字和破折号。',
    'alpha_num' => '该 :attribute 只能包含字母和数字。',
    'array' => '该 :attribute 必须是一个数列。',
    'before' => '该 :attribute 必须是 :date 之前的日期。',
    'before_or_equal' => '该 :attribute 必须小于或等于 :date。',
    'between' => [
        'numeric' => '该 :attribute 必须在 :min 和 :max 之间。',
        'file' => '该 :attribute 必须在 :min 和 :max KB之间。',
        'string' => '该 :attribute 必须在 :min 和 :max 字符之间。',
        'array' => '该 :attribute 必须有 :min 至 :max 项目。',
    ],
    'boolean' => '该 :attribute 值必须是true或false。',
    'confirmed' => '该 :attribute 确认不符。',
    'current_password' => '密码不正确。',
    'date' => '该 :attribute 不是有效的日期。',
    'date_equals' => '该 :attribute 必须等于 :date。',
    'date_format' => '该 :attribute 与格式 :format不匹配。',
    'declined' => '该 :attribute 必须被拒绝。',
    'declined_if' => '该 :attribute 必须被拒绝，当 :other 是 :value。',
    'different' => '该 :attribute 和 :other 必须有所不同。',
    'digits' => '该 :attribute 必须是 :digits 位数。',
    'digits_between' => '该 :attribute 必须在 :min 和 :max 之间。',
    'dimensions' => '该 :attribute 为无效的图像尺寸。',
    'distinct' => '该 :attribute 值重复了。',
    'email' => '该 :attribute 必须是一个有效的E-mail地址。',
    'ends_with' => '该 :attribute 必须以以下值之一结束：:values。',
    'enum' => '所选的 :attribute 无效。',
    'exists' => '所选的 :attribute 无效。',
    'file' => '该 :attribute 必须是一个文件。',
    'filled' => '该 :attribute 是必需的。',
    'gt' => [
        'numeric' => '该 :attribute 必须大于 :value。',
        'file' => '该 :attribute 必须大于 :value kB。',
        'string' => '该 :attribute 必须大于 :value 字符。',
        'array' => '该 :attribute 必须大于 :value 项。',
    ],
    'gte' => [
        'numeric' => '该 :attribute 必须大过或等于 :value。',
        'file' => '该 :attribute 必须大过或等于 :value kB。',
        'string' => '该 :attribute 必须大过或等于 :value 字符。',
        'array' => '该 :attribute 必须大于或等于 :value 项。',
    ],
    'image' => '该 :attribute 必须是一个图像。',
    'in' => '所选的 :attribute 无效。',
    'in_array' => '该 :attribute 不存在于 :other。',
    'integer' => '该 :attribute 必须是整数。',
    'ip' => '该 :attribute 必须是有效的IP地址。',
    'ipv4' => '该 :attribute 必须是有效的IPv4地址。',
    'ipv6' => '该 :attribute 必须是有效的IPv6地址。',
    'mac_address' => '该 :attribute 必须是有效的MAC地址。',
    'json' => '该 :attribute 必须是有效的JSON string。',
    'lt' => [
        'numeric' => '该 :attribute 必须小过 :value。',
        'file' => '该 :attribute 必须小于 :value kB。',
        'string' => '该 :attribute 必须小于 :value 字符。',
        'array' => '该 :attribute 必须少于 :value 项。',
    ],
    'lte' => [
        'numeric' => '该 :attribute 必须小过或等于 :value。',
        'file' => '该 :attribute 必须小过或等于 :value kB。',
        'string' => '该 :attribute 必须小过或等于 :value 字符。',
        'array' => '该 :attribute 必须少于或等于 :value 项。',
    ],
    'max' => [
        'numeric' => '该 :attribute 不可以大于 :max。',
        'file' => '该 :attribute 不可以大于 :max KB。',
        'string' => '该 :attribute 不可以大于 :max 字符。',
        'array' => '该 :attribute 不可以超过 :max 项。',
    ],
    'mimes' => '该 :attribute 必须是一个类型为 :values的文件。',
    'mimetypes' => '该 :attribute 必须是一个类型为 :values的文件。',
    'min' => [
        'numeric' => '该 :attribute 必须至少 :min。',
        'file' => '该 :attribute 必须至少 :min KB。',
        'string' => '该 :attribute 必须至少 :min 字符。',
        'array' => '该 :attribute 至少必须有 :min 项。',
    ],
    'multiple_of' => '该 :attribute 必须是 :value 的倍数。',
    'not_in' => '所选 :attribute 无效。',
    'not_regex' => ' :attribute 的格式无效。',
    'numeric' => '该 :attribute 必须是一个数字。',
    'present' => '该 :attribute 必须存在。',
    'prohibited' => '该 :attribute 字段禁止使用。',
    'prohibited_if' => '该 :attribute 字段禁止使用，当 :other 是 :value。',
    'prohibited_unless' => '该 :attribute 字段禁止使用，除非 :other 在 :values 中。',
    'prohibits' => '该 :attribute 字段禁止 :other 出现。',
    'regex' => '该 :attribute 格式无效。',
    'required' => '该 :attribute 是必需的。',
    'required_if' => '当 :other 是 :value时，该 :attribute 是必需的。',
    'required_unless' => '当 :other 不是 :value时，该 :attribute 是必需的。',
    'required_with' => '当 :values 存在时，该 :attribute 字段是必需的。',
    'required_with_all' => '当 :values 存在时，该 :attribute 字段是必需的。',
    'required_without' => '当 :values 不存在时，该 :attribute 字段是必需的。',
    'required_without_all' => '当所有 :values 不存在时，该 :attribute 字段是必需的。',
    'same' => '该 :attribute 和 :other 必须匹配。',
    'size' => [
        'numeric' => '该 :attribute 必须是 :size 。',
        'file' => '该 :attribute 必须是 :size KB。',
        'string' => '该 :attribute 必须是 :size 字符。',
        'array' => '该 :attribute 必须包含 :size 项。',
    ],
    'starts_with' => '该 :attribute 必须由这些值起始: :values',
    'string' => '该 :attribute 必须是一个字符串。',
    'timezone' => '该 :attribute 必须是有效的时区。',
    'unique' => '该 :attribute 已经被使用过了。',
    'uploaded' => '该 :attribute 未能上传。',
    'url' => '该 :attribute 格式无效。',
    'uuid' => '该 :attribute 必须为有效的UUID.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'email_list' => '很抱歉，你无法使用此邮箱进行注册。请参见邮箱白名单。',
    'recaptcha' => '请完成人机验证。',
    'custom' => [
        'attribute-name' => [
            'rule-name' => '自定义规则名称',
        ],
    ],
    'password' => [
        'letters' => '属性必须至少包含一个字母。',
        'mixed' => ':属性必须至少包含一个大写字母和一个小写字母。',
        'numbers' => '属性必须至少包含一个数字。',
        'symbols' => '属性必须至少包含一个符号。',
        'uncompromised' => '给定的：属性出现在数据泄露中。请选择不同的：属性。',
    ],
];
