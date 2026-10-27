# Contributing

Contributions are **welcome**. Please make all pull requests against the `development` branch and NOT `master` which is only for releases.

We accept contributions via Pull Requests on [Github](https://github.com/HDInnovations/UNIT3D).

**Working on your first Pull Request?** You can learn how from this *free* series [How to Contribute to an Open Source Project on GitHub](https://egghead.io/courses/how-to-contribute-to-an-open-source-project-on-github)

## AI Usage Policy

> [!IMPORTANT]
> This project does **not** accept pull requests that are fully or predominantly AI-generated. AI tools may be utilized solely in an assistive capacity.
>
> Repeated violations of this policy may result in your account being permanently banned from contributing to the project.

## Commits

**Commit Title Standard**

Please use the following title schema. 
- prefix: Title

Examples:
- update: French Translations
- fix: French Translations
- security fix: French Translations
- remove: French Translations
- add: French Translations
- revert: French Translations
- refactor: French Translations

https://www.conventionalcommits.org/en/v1.0.0/

## Pull Requests

**PR Title Standard**

Please use the following title schema. 
- (PREFIX) Title

Examples:
- (Update) French Translations
- (Fix) French Translations
- (Security Fix) French Translations
- (Remove) French Translations
- (Add) French Translations
- (Revert) French Translations
- (Refactor) French Translations

## Code Style

**[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** 
- Check the code style with ``$ ./vendor/bin/pint . --test`` and fix it with ``$ ./vendor/bin/pint .``.
- Prettier is also used to format Blade, SCSS, and JS: ``$ ./node_modules/.bin/prettier . --write``.

**[Follow Laravel naming conventions](https://github.com/alexeymezenin/laravel-best-practices/blob/master/README.md)**

What | How | Good | Bad
------------ | ------------- | ------------- | -------------
Controller | singular | ArticleController | ~~ArticlesController~~
Route | plural | articles/1 | ~~article/1~~
Route name | snake_case with dot notation | users.show_active | ~~users.show-active, show-active-users~~
Model | singular | User | ~~Users~~
hasOne or belongsTo relationship | singular | articleComment | ~~articleComments, article_comment~~
All other relationships | plural | articleComments | ~~articleComment, article_comments~~
Table | plural | article_comments | ~~article_comment, articleComments~~
Pivot table | singular model names in alphabetical order | article_user | ~~user_article, articles_users~~
Table column | snake_case without model name | meta_title | ~~MetaTitle; article_meta_title~~
Model property | snake_case | $model->created_at | ~~$model->createdAt~~
Foreign key | singular model name with _id suffix | article_id | ~~ArticleId, id_article, articles_id~~
Primary key | - | id | ~~custom_id~~
Migration | - | 2017_01_01_000000_create_articles_table | ~~2017_01_01_000000_articles~~
Method | camelCase | getAll | ~~get_all~~
Method in resource controller | [table](https://laravel.com/docs/master/controllers#resource-controllers) | store | ~~saveArticle~~
Method in test class | camelCase | testGuestCannotSeeArticle | ~~test_guest_cannot_see_article~~
Variable | camelCase | $articlesWithAuthor | ~~$articles_with_author~~
Collection | descriptive, plural | $activeUsers = User::active()->get() | ~~$active, $data~~
Object | descriptive, singular | $activeUser = User::active()->first() | ~~$users, $obj~~
Config and language files index | snake_case | articles_enabled | ~~ArticlesEnabled; articles-enabled~~
View | kebab-case | show-filtered.blade.php | ~~showFiltered.blade.php, show_filtered.blade.php~~
Config | snake_case | google_calendar.php | ~~googleCalendar.php, google-calendar.php~~
Contract (interface) | adjective or noun | AuthenticationInterface | ~~Authenticatable, IAuthentication~~
Trait | adjective | Notifiable | ~~NotificationTrait~~
Trait [(PSR)](https://www.php-fig.org/bylaws/psr-naming-conventions/) | adjective | NotifiableTrait | ~~Notification~~
Enum | singular | UserType | ~~UserTypes~~, ~~UserTypeEnum~~
FormRequest | singular | UpdateUserRequest | ~~UpdateUserFormRequest~~, ~~UserFormRequest~~, ~~UserRequest~~
Seeder | singular | UserSeeder | ~~UsersSeeder~~

**Use Laravel helpers when possible and not facades** - auth(), info(), cache(), response(), ext. [Laravel Helpers](https://laravel.com/docs/5.6/helpers)

**Use shortened syntax when possible** - Example: `[]` and not `array()`.
  
Common syntax | Shorter and more readable syntax
  ------------ | -------------
  `Session::get('cart')` | `session('cart')`
  `$request->session()->get('cart')` | `session('cart')`
  `Session::put('cart', $data)` | `session(['cart' => $data])`
  `$request->input('name'), Request::get('name')` | `$request->name, request('name')`
  `return Redirect::back()` | `return back()`
  `is_null($object->relation) ? null : $object->relation->id` | `optional($object->relation)->id` (in PHP 8: `$object->relation?->id`)
  `$request->has('value') ? $request->value : 'default';` | `$request->get('value', 'default')`
  `App::make('Class')` | `app('Class')`
  `->orderBy('created_at', 'desc')` | `->latest()`
  `->orderBy('age', 'desc')` | `->latest('age')`
  `->orderBy('created_at', 'asc')` | `->oldest()`

### CSS

CSS should follow the [BEM methodology](https://getbem.com/) and the [7-1 SCSS Architecture](https://sass-guidelin.es/#the-7-1-pattern).

Any new pages should use the existing component styles unless a new block, element and/or modifier is being added. It is highly discouraged to add new components when an existing one will do.

## Other

- **Document any change in behavior** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

**Happy coding**!
