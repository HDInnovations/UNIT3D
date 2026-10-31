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
    | as the size rules. Feel free to tweak each of these messages.
    |
    */
    'accepted' => 'El camp :attribute s\'ha d\'acceptar.',
    'active_url' => 'El camp :attribute no és un URL vàlid.',
    'after' => 'El camp :attribute ha de ser una data posterior a :date.',
    'after_or_equal' => 'El camp :attribute ha de ser una data posterior o igual a :date.',
    'alpha' => 'El camp :attribute només pot contenir lletres.',
    'alpha_dash' => 'El camp :attribute només pot contenir lletres, números, guions i guions baixos.',
    'alpha_num' => 'El camp :attribute només pot contenir lletres i números.',
    'array' => 'El camp :attribute ha de ser una matriu.',
    'before' => 'El camp :attribute ha de ser una data anterior a :date.',
    'before_or_equal' => 'El camp :attribute ha de ser una data anterior o igual a :date.',
    'between' => [
        'numeric' => "El camp :attribute ha d'estar entre :min i :max.",
        'file' => 'El camp :attribute ha de tenir entre :min i :max kilobytes.',
        'string' => 'El camp :attribute ha de tenir entre :min i :max caràcters.',
        'array' => 'El camp :attribute ha de tenir entre :min i :max elements.',
    ],
    'boolean' => 'El camp :attribute ha de ser vertader o fals.',
    'confirmed' => 'La confirmació del camp :attribute no coincideix.',
    'date' => 'El camp :attribute no és una data vàlida.',
    'date_equals' => 'El camp :attribute ha de ser una data igual a :date.',
    'date_format' => 'El camp :attribute no coincideix amb el format :format.',
    'different' => 'Els camps :attribute i :other han de ser diferents.',
    'digits' => 'El camp :attribute ha de tenir :digits dígits.',
    'digits_between' => 'El camp :attribute ha de tenir entre :min i :max dígits.',
    'dimensions' => 'El camp :attribute té unes dimensions d\'imatge no vàlides.',
    'distinct' => 'El camp :attribute té un valor duplicat.',
    'email' => 'El camp :attribute ha de ser una adreça electrònica vàlida.',
    'exists' => 'El valor seleccionat de :attribute no és vàlid.',
    'file' => 'El camp :attribute ha de ser un fitxer.',
    'filled' => 'El camp :attribute ha de tenir un valor.',
    'gt' => [
        'numeric' => 'El camp :attribute ha de ser més gran que :value.',
        'file' => 'El camp :attribute ha de tenir més de :value kilobytes.',
        'string' => 'El camp :attribute ha de tenir més de :value caràcters.',
        'array' => 'El camp :attribute ha de tenir més de :value elements.',
    ],
    'gte' => [
        'numeric' => 'El camp :attribute ha de ser més gran o igual que :value.',
        'file' => 'El camp :attribute ha de tenir :value kilobytes o més.',
        'string' => 'El camp :attribute ha de tenir :value caràcters o més.',
        'array' => 'El camp :attribute ha de tenir :value elements o més.',
    ],
    'image' => 'El camp :attribute ha de ser una imatge.',
    'in' => 'El valor seleccionat de :attribute no és vàlid.',
    'in_array' => 'El camp :attribute no existeix a :other.',
    'integer' => 'El camp :attribute ha de ser un nombre enter.',
    'ip' => 'El camp :attribute ha de ser una adreça IP vàlida.',
    'ipv4' => 'El camp :attribute ha de ser una adreça IPv4 vàlida.',
    'ipv6' => 'El camp :attribute ha de ser una adreça IPv6 vàlida.',
    'json' => 'El camp :attribute ha de ser una cadena JSON vàlida.',
    'lt' => [
        'numeric' => 'El camp :attribute ha de ser més petit que :value.',
        'file' => 'El camp :attribute ha de tenir menys de :value kilobytes.',
        'string' => 'El camp :attribute ha de tenir menys de :value caràcters.',
        'array' => 'El camp :attribute ha de tenir menys de :value elements.',
    ],
    'lte' => [
        'numeric' => 'El camp :attribute ha de ser més petit o igual que :value.',
        'file' => 'El camp :attribute ha de tenir :value kilobytes o menys.',
        'string' => 'El camp :attribute ha de tenir :value caràcters o menys.',
        'array' => 'El camp :attribute no pot tenir més de :value elements.',
    ],
    'max' => [
        'numeric' => 'El camp :attribute no pot ser més gran que :max.',
        'file' => 'El camp :attribute no pot tenir més de :max kilobytes.',
        'string' => 'El camp :attribute no pot tenir més de :max caràcters.',
        'array' => 'El camp :attribute no pot tenir més de :max elements.',
    ],
    'mimes' => 'El camp :attribute ha de ser un fitxer de tipus: :values.',
    'mimetypes' => 'El camp :attribute ha de ser un fitxer de tipus: :values.',
    'min' => [
        'numeric' => "El camp :attribute ha de ser com a mínim :min.",
        'file' => "El camp :attribute ha de tenir com a mínim :min kilobytes.",
        'string' => 'El camp :attribute ha de tenir com a mínim :min caràcters.',
        'array' => 'El camp :attribute ha de tenir com a mínim :min elements.',
    ],
    'not_in' => 'El valor seleccionat de :attribute no és vàlid.',
    'not_regex' => 'El format del camp :attribute no és vàlid.',
    'numeric' => 'El camp :attribute ha de ser un número.',
    'present' => 'El camp :attribute ha d\'estar present.',
    'regex' => 'El format del camp :attribute no és vàlid.',
    'required' => 'El camp :attribute és obligatori.',
    'required_if' => 'El camp :attribute és obligatori quan :other és :value.',
    'required_unless' => 'El camp :attribute és obligatori tret que :other sigui a :values.',
    'required_with' => 'El camp :attribute és obligatori quan :values és present.',
    'required_with_all' => 'El camp :attribute és obligatori quan :values són presents.',
    'required_without' => 'El camp :attribute és obligatori quan :values no és present.',
    'required_without_all' => 'El camp :attribute és obligatori quan cap de :values és present.',
    'same' => 'Els camps :attribute i :other han de coincidir.',
    'size' => [
        'numeric' => 'El camp :attribute ha de ser :size.',
        'file' => 'El camp :attribute ha de tenir :size kilobytes.',
        'string' => 'El camp :attribute ha de tenir :size caràcters.',
        'array' => 'El camp :attribute ha de contenir :size elements.',
    ],
    'starts_with' => 'El camp :attribute ha de començar amb un d\'aquests valors: :values.',
    'string' => 'El camp :attribute ha de ser una cadena de text.',
    'timezone' => 'El camp :attribute ha de ser una zona horària vàlida.',
    'unique' => 'El valor de :attribute ja està en ús.',
    'uploaded' => 'No s\'ha pogut pujar el fitxer :attribute.',
    'url' => 'El camp :attribute ha de ser un URL vàlid.',
    'uuid' => 'El camp :attribute ha de ser un UUID vàlid.',
    'custom' => [
        'attribute-name' => [
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
            'rule-name' => 'custom-message',
        ],
    ],
    'attributes' => [
        /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
        'name' => 'nom',
        'username' => 'usuari',
        'email' => 'correu electrònic',
        'first_name' => 'nom',
        'last_name' => 'cognom',
        'password' => 'contrasenya',
        'password_confirmation' => 'confirmació de la contrasenya',
        'city' => 'ciutat',
        'country' => 'país',
        'address' => 'adreça',
        'phone' => 'telèfon',
        'mobile' => 'mòbil',
        'age' => 'edat',
        'sex' => 'sexe',
        'gender' => 'gènere',
        'year' => 'any',
        'month' => 'mes',
        'day' => 'dia',
        'hour' => 'hora',
        'minute' => 'minut',
        'second' => 'segon',
        'title' => 'títol',
        'body' => 'contingut',
        'description' => 'descripció',
        'excerpt' => 'extracte',
        'date' => 'data',
        'time' => 'hora',
        'subject' => 'assumpte',
        'message' => 'missatge',
    ],
    'accepted_if' => 'El camp :attribute s\'ha d\'acceptar quan :other és :value.',
    'current_password' => 'La contrasenya no és correcta.',
    'declined' => 'El camp :attribute s\'ha de rebutjar.',
    'declined_if' => 'El camp :attribute s\'ha de rebutjar quan :other és :value.',
    'ends_with' => 'El camp :attribute ha d\'acabar amb un d\'aquests valors: :values.',
    'enum' => 'El valor seleccionat de :attribute no és vàlid.',
    'mac_address' => 'El camp :attribute ha de ser una adreça MAC vàlida.',
    'multiple_of' => 'El camp :attribute ha de ser múltiple de :value.',
    'password' => [
        'letters' => 'El camp :attribute ha de contenir com a mínim una lletra.',
        'mixed' => 'El camp :attribute ha de contenir com a mínim una lletra majúscula i una de minúscula.',
        'numbers' => 'El camp :attribute ha de contenir com a mínim un número.',
        'symbols' => 'El camp :attribute ha de contenir com a mínim un símbol.',
        'uncompromised' => 'El valor indicat a :attribute ha aparegut en una filtració de dades. Tria un altre valor per a :attribute.',
    ],
    'prohibited_if' => 'El camp :attribute està prohibit quan :other és :value.',
    'prohibited' => 'El camp :attribute està prohibit.',
    'prohibited_unless' => 'El camp :attribute està prohibit tret que :other sigui a :values.',
    'prohibits' => 'El camp :attribute impedeix que :other sigui present.',
    'email_list' => 'Aquest domini de correu electrònic no es pot fer servir en aquest lloc. Consulta la llista blanca de correus electrònics del lloc.',
    'recaptcha' => 'Completa el reCAPTCHA.',
];
