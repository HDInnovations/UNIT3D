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
    | Мовні ресурси перевірки введення
    |--------------------------------------------------------------------------
    |
    | Наступні ресурси містять стандартні повідомлення перевірки коректності
    | введення даних. Деякі з цих правил мають декілька варіантів, як,
    | наприклад, size. Ви можете змінити будь-яке з цих повідомлень.
    |
    */
    'accepted' => 'Ви повинні прийняти :attribute.',
    'active_url' => 'Поле :attribute не є коректним URL.',
    'after' => 'Поле :attribute має містити дату не раніше :date.',
    'after_or_equal' => 'Поле :attribute має містити дату не раніше :date.',
    'alpha' => 'Поле :attribute має містити лише літери.',
    'alpha_dash' => 'Поле :attribute має містити лише літери, цифри, дефіс та підкреслення.',
    'alpha_num' => 'Поле :attribute має містити лише літери та цифри.',
    'array' => 'Поле :attribute має бути масивом.',
    'before' => 'Поле :attribute має містити дату не пізніше :date.',
    'before_or_equal' => 'Поле :attribute має містити дату не пізніше :date.',
    'between' => [
        'numeric' => 'Поле :attribute має бути між :min та :max.',
        'file' => 'Розмір файлу в полі :attribute має бути не менше :min та не більше :max кілобайт.',
        'string' => 'Текст в полі :attribute має бути не менше :min та не більше :max символів.',
        'array' => 'Поле :attribute має містити від :min до :max елементів.',
    ],
    'boolean' => 'Поле :attribute повинне містити логічний тип.',
    'confirmed' => 'Поле :attribute не збігається з підтвердженням.',
    'date' => 'Поле :attribute не є датою.',
    'date_equals' => 'Поле :attribute має бути датою рівною :date.',
    'date_format' => 'Поле :attribute не відповідає формату :format.',
    'different' => 'Поля :attribute та :other повинні бути різними.',
    'digits' => 'Довжина цифрового поля :attribute повинна дорівнювати :digits.',
    'digits_between' => 'Довжина цифрового поля :attribute повинна бути від :min до :max.',
    'dimensions' => 'Поле :attribute містить недопустимі розміри зображення.',
    'distinct' => 'Поле :attribute містить значення, яке дублюється.',
    'email' => 'Поле :attribute повинне містити коректну електронну адресу.',
    'file' => 'Поле :attribute має містити файл.',
    'filled' => "Поле :attribute є обов'язковим для заповнення.",
    'exists' => 'Вибране для :attribute значення не коректне.',
    'gt' => [
        'numeric' => 'Поле :attribute має бути більше ніж :value.',
        'file' => 'Поле :attribute має бути більше ніж :value кілобайт.',
        'string' => 'Поле :attribute має бути більше ніж :value символів.',
        'array' => 'Поле :attribute має містити більше ніж :value елементів.',
    ],
    'gte' => [
        'numeric' => 'Поле :attribute має дорівнювати чи бути більше ніж :value.',
        'file' => 'Поле :attribute має дорівнювати чи бути більше ніж :value кілобайт.',
        'string' => 'Поле :attribute має дорівнювати чи бути більше ніж :value символів.',
        'array' => 'Поле :attribute має містити :value чи більше елементів.',
    ],
    'image' => 'Поле :attribute має містити зображення.',
    'in' => 'Вибране для :attribute значення не коректне.',
    'in_array' => 'Значення поля :attribute не міститься в :other.',
    'integer' => 'Поле :attribute має містити ціле число.',
    'ip' => 'Поле :attribute має містити IP адресу.',
    'ipv4' => 'Поле :attribute має містити IPv4 адресу.',
    'ipv6' => 'Поле :attribute має містити IPv6 адресу.',
    'json' => 'Дані поля :attribute мають бути в форматі JSON.',
    'lt' => [
        'numeric' => 'Поле :attribute має бути менше ніж :value.',
        'file' => 'Поле :attribute має бути менше ніж :value кілобайт.',
        'string' => 'Поле :attribute має бути менше ніж :value символів.',
        'array' => 'Поле :attribute має містити менше ніж :value items.',
    ],
    'lte' => [
        'numeric' => 'Поле :attribute має дорівнювати чи бути менше ніж :value.',
        'file' => 'Поле :attribute має дорівнювати чи бути менше ніж :value кілобайт.',
        'string' => 'Поле :attribute має дорівнювати чи бути менше ніж :value символів.',
        'array' => 'Поле :attribute має містити не більше ніж :value елементів.',
    ],
    'max' => [
        'numeric' => 'Поле :attribute має бути не більше :max.',
        'file' => 'Файл в полі :attribute має бути не більше :max кілобайт.',
        'string' => 'Текст в полі :attribute повинен мати довжину не більшу за :max.',
        'array' => 'Поле :attribute повинне містити не більше :max елементів.',
    ],
    'mimes' => 'Для поля :attribute прийнятні лише файли типу: :values.',
    'mimetypes' => 'Для поля :attribute прийнятні лише файли типу: :values.',
    'min' => [
        'numeric' => 'Поле :attribute повинне бути не менше :min.',
        'file' => 'Розмір файлу в полі :attribute має бути не меншим :min кілобайт.',
        'string' => 'Текст в полі :attribute повинен містити не менше :min символів.',
        'array' => 'Поле :attribute повинне містити не менше :min елементів.',
    ],
    'not_in' => 'Вибране для :attribute значення не коректне.',
    'not_regex' => 'Формат поля :attribute не вірний.',
    'numeric' => 'Поле :attribute повинно містити число.',
    'present' => 'Поле :attribute є обов’язковим для заповнення.',
    'regex' => 'Поле :attribute має хибний формат.',
    'required' => "Поле :attribute є обов'язковим для заповнення.",
    'required_if' => "Поле :attribute є обов'язковим для заповнення, коли :other є рівним :value.",
    'required_unless' => "Поле :attribute є обов’язковим, якщо лише :other не належить до :values.",
    'required_with' => "Поле :attribute є обов'язковим для заповнення, коли :values вказано.",
    'required_with_all' => "Поле :attribute є обов'язковим для заповнення, коли :values вказано.",
    'required_without' => "Поле :attribute є обов'язковим для заповнення, коли :values не вказано.",
    'required_without_all' => "Поле :attribute є обов'язковим для заповнення, коли :values не вказано.",
    'same' => 'Поля :attribute та :other мають співпадати.',
    'size' => [
        'numeric' => 'Поле :attribute має бути довжини :size.',
        'file' => 'Файл в полі :attribute має бути розміром :size кілобайт.',
        'string' => 'Текст в полі :attribute повинен містити :size символів.',
        'array' => 'Поле :attribute повинне містити :size елементів.',
    ],
    'starts_with' => 'Поле «:attribute» має починатися з будь-якого з наведених значень: :values.',
    'string' => 'Поле :attribute повинне містити текст.',
    'timezone' => 'Поле :attribute повинне містити коректну часову зону.',
    'unique' => 'Таке значення поля :attribute вже існує.',
    'uploaded' => 'Завантаження поля :attribute не вдалося.',
    'url' => 'Формат поля :attribute неправильний.',
    'uuid' => 'Поле :attribute має бути коректним UUID ідентифікатором.',
    'custom' => [
        'attribute-name' => [
            /*
    |--------------------------------------------------------------------------
    | Додаткові ресурси для перевірки введення
    |--------------------------------------------------------------------------
    |
    | Тут Ви можете вказати власні ресурси для підтвердження введення,
    | використовуючи формат "attribute.rule", щоб дати назву текстовим змінним.
    | Так ви зможете легко додати текст повідомлення для заданого атрибуту.
    |
    */
            'rule-name' => 'користувацьке-повідомлення',
        ],
    ],
    'attributes' => [
        /*
    |--------------------------------------------------------------------------
    | Власні назви атрибутів
    |--------------------------------------------------------------------------
    |
    | Наступні правила дозволяють налаштувати заміну назв полів введення
    | для зручності користувачів. Наприклад, вказати "Електронна адреса" замість
    | "email".
    |
    | Приклад використання
    |
    |   'attributes' => [
    |       'email' => 'Електронна адреса',
    |   ],
    |
    */
        'name' => 'Ім\'я',
        'username' => 'Нікнейм',
        'email' => 'E-Mail адреса',
        'first_name' => 'Ім\'я',
        'last_name' => 'Прізвище',
        'password' => 'Пароль',
        'password_confirmation' => 'Підтвердження паролю',
        'city' => 'Місто',
        'country' => 'Країна',
        'address' => 'Адреса',
        'phone' => 'Телефон',
        'mobile' => 'Моб. номер',
        'age' => 'Вік',
        'sex' => 'Стать',
        'gender' => 'Стать',
        'day' => 'День',
        'month' => 'Місяць',
        'year' => 'Рік',
        'hour' => 'Година',
        'minute' => 'Хвилина',
        'second' => 'Секунда',
        'title' => 'Назва',
        'content' => 'Контент',
        'description' => 'Опис',
        'excerpt' => 'Уривок',
        'date' => 'Дата',
        'time' => 'Час',
        'available' => 'Доступно',
        'size' => 'Розмір',
    ],
    'current_password' => 'Неправильний пароль.',
    'recaptcha' => 'Будь ласка, заповніть ReCaptcha.',
    'password' => [
        'letters' => 'У полі :attribute має бути принаймні одна літера.',
        'mixed' => 'Поле :attribute має містити щонайменше одну велику та одну малу літеру.',
        'numbers' => 'Поле :attribute має містити принаймні одну цифру.',
        'symbols' => 'Поле :attribute має містити принаймні один символ.',
        'uncompromised' => ':attribute, який ви вказали, вже став публічним через витік даних. Будь ласка, підберіть новий.',
    ],
    'multiple_of' => 'Значення :attribute повинно бути кратним числу :value.',
    'prohibited' => 'Заповнення поля :attribute є недопустимим.',
    'prohibited_if' => 'Заповнення поля :attribute не допускається, якщо :other дорівнює :value.',
    'prohibited_unless' => 'Заповнення поля :attribute заборонено, хіба що :other міститься серед :values.',
    'prohibits' => 'Поле «:attribute» не може містити «:other».',
    'email_list' => 'Цей домен електронної пошти не відповідає вимогам сайту. Будь ласка, перегляньте список дозволених доменів.',
    'accepted_if' => ':attribute буде схвалено, якщо :other = :value.',
    'declined' => ':attribute слід відхилити.',
    'declined_if' => ':attribute слід відхилити, коли :other дорівнює :value.',
    'ends_with' => ':attribute повинен закінчуватися значенням із цього списку: :values.',
    'enum' => 'Обраний :attribute є некоректним.',
    'mac_address' => ':attribute має бути дійсною MAC-адресою.',
];
