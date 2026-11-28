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
    | Validation language lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted' => 'O :attribute precisa ser aceito.',
    'accepted_if' => 'O :attribute deve ser aceito quando :other for :value.',
    'active_url' => 'O :attribute não é uma URL válida.',
    'after' => 'O :attribute deve ser uma data após :date.',
    'after_or_equal' => 'O :attribute deve ser uma data depois de :date ou na mesma data.',
    'alpha' => 'O :attribute precisa conter apenas letras.',
    'alpha_dash' => 'O :attribute deve conter apenas letras, números, traços e sublinhados.',
    'alpha_num' => 'O :attribute precisa conter apenas letras e números.',
    'array' => 'O :attribute deve ser um array.',
    'before' => 'O :attribute deve ser uma data anterior a :date.',
    'before_or_equal' => 'O :attribute deve ser uma data anterior ou igual a :date.',
    'between' => [
        'numeric' => 'O :attribute precisa estar entre :min e :max.',
        'file' => 'O :attribute precisa ser entre :min e :max kilobytes.',
        'string' => 'O :attribute deve ter entre :min e :max caracteres.',
        'array' => 'O :attribute deve ter entre :min e :max itens.',
    ],
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed' => 'A confirmação do :attribute não corresponde.',
    'current_password' => 'A senha está incorreta.',
    'date' => 'O :attribute não é uma data válida.',
    'date_equals' => 'O campo :attribute deve ser uma data igual a :date.',
    'date_format' => 'O atributo :attribute não corresponde ao formato :format.',
    'declined' => 'O atributo :attribute deve ser recusado.',
    'declined_if' => 'O atributo :attribute deve ser recusado quando :other for :value.',
    'different' => 'Os atributos :attribute e :other devem ser diferentes.',
    'digits' => 'O atributo :attribute deve ser :digits dígitos.',
    'digits_between' => 'O atributo :attribute deve ter entre :min e :max dígitos.',
    'dimensions' => 'O atributo :attribute possui dimensões de imagem inválidas.',
    'distinct' => 'O campo :attribute possui um valor duplicado.',
    'email' => 'O atributo :attribute deve ser um endereço de e-mail válido.',
    'ends_with' => 'O :attribute deve terminar com um dos seguintes: :values.',
    'enum' => 'O :attribute selecionado é inválido.',
    'exists' => 'O :attribute selecionado é inválido.',
    'file' => 'O :attribute deve ser um arquivo.',
    'filled' => 'O campo :attribute deve ter um valor.',
    'gt' => [
        'numeric' => 'O campo :attribute deve ser maior que :value.',
        'file' => 'O campo :attribute deve ser maior que :value kilobytes.',
        'string' => 'O campo :attribute deve ter mais de :value caracteres.',
        'array' => 'O campo :attribute deve conter mais de :value itens.',
    ],
    'gte' => [
        'array' => 'O campo :attribute deve conter :value itens ou mais.',
        'numeric' => 'O campo :attribute deve ser maior ou igual a :value.',
        'file' => 'O campo :attribute deve ser maior ou igual a :value kilobytes.',
        'string' => 'O campo :attribute deve ser maior ou igual a :value caracteres.',
    ],
    'image' => 'O :attribute deve ser uma imagem.',
    'ip' => 'O :attribute deve ser um endereço IP válido.',
    'ipv4' => 'O :attribute deve ser um endereço IPv4 válido.',
    'ipv6' => 'O :attribute deve ser um endereço IPv6 válido.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'password' => [
        'letters' => 'O campo :attribute deve conter pelo menos uma letra.',
        'mixed' => 'O campo :attribute deve conter pelo menos uma letra maiúscula e uma letra minúscula.',
        'numbers' => 'O campo :attribute deve conter pelo menos um número.',
        'symbols' => 'O campo :attribute deve conter pelo menos um símbolo.',
        'uncompromised' => 'O campo :attribute indicado apareceu em um vazamento de dados. Por favor, escolha um :attribute diferente.',
    ],
    'in' => 'O :attribute selecionado é inválido.',
    'in_array' => 'O campo :attribute não existe em :other.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'mac_address' => 'O campo :attribute deve ser um endereço MAC válido.',
    'json' => 'O campo :attribute deve ser uma string JSON válida.',
    'lt' => [
        'numeric' => 'O campo :attribute deve ser menor que :value.',
        'file' => 'O campo :attribute deve ser menor que :value kilobytes.',
        'string' => 'O campo :attribute deve ter menos de :value caracteres.',
        'array' => 'O campo :attribute deve conter menos de :value itens.',
    ],
    'lte' => [
        'numeric' => 'O campo :attribute deve ser menor ou igual a :value.',
        'file' => 'O campo :attribute deve ser menor ou igual a :value kilobytes.',
        'string' => 'O campo :attribute deve ter :value caracteres ou menos.',
        'array' => 'O campo :attribute não deve conter mais de :value itens.',
    ],
    'max' => [
        'numeric' => 'O campo :attribute não deve ser maior que :max.',
        'file' => 'O campo :attribute não deve ser maior que :max kilobytes.',
        'string' => 'O campo :attribute não deve ter mais de :max caracteres.',
        'array' => 'O campo :attribute não deve conter mais de :max itens.',
    ],
    'mimes' => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes' => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'min' => [
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
        'file' => 'O campo :attribute deve ter pelo menos :min kilobytes.',
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'array' => 'O campo :attribute deve conter pelo menos :min itens.',
    ],
    'multiple_of' => 'O campo :attribute deve ser um múltiplo de :value.',
    'not_in' => 'O campo :attribute selecionado é inválido.',
    'not_regex' => 'O formato do campo :attribute é inválido.',
    'present' => 'O campo :attribute deve estar presente.',
    'prohibited' => 'O campo :attribute é proibido.',
    'prohibited_if' => 'O campo :attribute é proibido quando :other é :value.',
    'prohibited_unless' => 'O campo :attribute é proibido, a menos que :other esteja em :values.',
    'prohibits' => 'O campo :attribute proíbe a presença de :other.',
    'regex' => 'O formato do campo :attribute é inválido.',
    'required' => 'O campo :attribute é obrigatório.',
    'required_if' => 'O campo :attribute é obrigatório quando :other é :value.',
    'required_unless' => 'O campo :attribute é obrigatório, a menos que :other esteja em :values.',
    'required_with' => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_with_all' => 'O campo :attribute é obrigatório quando :values estão presentes.',
    'required_without' => 'O campo :attribute é obrigatório quando :values não está presente.',
    'required_without_all' => 'O campo :attribute é obrigatório quando nenhum dos campos :values está presente.',
    'same' => 'O campo :attribute e :other devem coincidir.',
    'size' => [
        'numeric' => 'O campo :attribute deve ser :size.',
        'file' => 'O arquivo :attribute deve ter :size kilobytes.',
        'string' => 'O campo :attribute deve ter :size caracteres.',
        'array' => 'O campo :attribute deve conter :size itens.',
    ],
    'starts_with' => 'O campo :attribute deve começar com um dos seguintes valores: :values.',
    'string' => 'O campo :attribute deve ser uma sequência de caracteres.',
    'timezone' => 'O campo :attribute deve ser um fuso horário válido.',
    'unique' => 'O campo :attribute já está em uso.',
    'uploaded' => 'Ocorreu uma falha ao enviar o campo :attribute.',
    'url' => 'O campo :attribute deve ser uma URL válida.',
    'uuid' => 'O campo :attribute deve ser um UUID válido.',
    'email_list' => 'Desculpe, este domínio de e-mail não é permitido neste site. Por favor, consulte a whitelist de e-mails do site.',
    'recaptcha' => 'Por favor, complete o ReCaptcha.',
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
];
