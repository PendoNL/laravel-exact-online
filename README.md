22/05/2020: We've archived this package as it was originally intended to be used inside a project and scratch our own itch. We haven't touched our updated it since. It's archived for the people who use(d) it. Feel free to fork and re-release this in case you want to actively develop and support this package.

# Laravel Exact Online
This package is a Laravel wrapper around the Exact Online PHP API by Picqer 
(https://github.com/picqer/exact-php-client). It provides routes and a controller out of the box to connect
your Laravel app with an Exact Online app. It also provides a Facade which is chainable to make requests
to the API easily. Authorisation and refresh tokens are saved automatically.

`Note:` the tokens are saved in a json file for application wide use. Therefor this code is not suitable if
your platform need access to Exact Online for a single user! This set of code may be used to communicate with
a single Exact Online user's administration.

#### First draft - do not use!
So far, this package is just a draft. So use at own risk. We're currently working on a project to see if
it provides all functions we need. If you intend to use this package, please see below for instructions and
what functions you may use.

## Installation
First add the dev-master version of this package to your composer.json

```
"pendonl/laravel-exact-online": "dev-master"
```

Then run `composer update` and add the Service Provider to config/app.php (For L5.5 and up Auto-Discovery is enabled)

```
...
PendoNL\LaravelExactOnline\Providers\LaravelExactOnlineServiceProvider::class,
...
```

In the same file, add the Facade

```
...
'ExactOnline' => PendoNL\LaravelExactOnline\LaravelExactOnlineFacade::class,
...
```

Followed by this, publish the resources (views, config, etc.)

```
php artisan vendor:publish --provider="PendoNL\LaravelExactOnline\Providers\LaravelExactOnlineServiceProvider"
```

While developing this package, you might want to use the `--force` flag on this command to overwrite previous files.

And last but not least either edit config/laravel-exact-online to match your Exact Online app settings
or add these keys to your .env:

```
EXACT_CLIENT_ID=
EXACT_CLIENT_SECRET=
```

The following keys are optional

```
EXACT_COUNTRY_CODE=
EXACT_DIVISION=
```

## Multiuser support
Out of the box this plugin stores the exact keys inside a JSON file. This means every user uses the same credentials. If you would like to give your users the opportunity to make individual connections you can do so by setting the following parameter inside your .env file:

```
EXACT_MULTI_USER=true
```

Sidenote: There's no migration written for this feature yet. Feel free to do so. In the meantime you should add these changes to your user  migration:

```php
$table->text('exact_accessToken')->nullable();
$table->text('exact_refreshToken')->nullable();
$table->text('exact_tokenExpires')->nullable();
$table->text('exact_authorisationCode')->nullable();
```

and add these fillables to your user object:

```php
protected $fillable = [
    'name', 'email', 'password', 'exact_accessToken', 'exact_refreshToken', 'exact_tokenExpires', 'exact_authorisationCode'
];
```

## How to use connect Laravel & Exact Online
As said this package provides the route and controller to easily connect your  Exact App with 
your Laravel project. You may overwrite the routes in you routes/web.php file, I even insist on
doing so because the routes are `not protected` by default!

You may also edit the views to your liking, after publishing they can be found under
`/views/vendor/laravel-exact-online/` in your resources path.

##### Step 1: connect & authorise
Visit http://your-project.dev/exact/connect, you will be presented a submit button to go to
Exact Online. Once there, login and approve the app. After this you will be returned do 
http://your-project.dev/exact/oauth, this route takes care of saving the needed tokens for
future requests.

##### Step 2: use the Facade
That's it, you're now ready to use the package.

## How to use the API
The package by Picqer requires you to provide a valid connection parameter to each resource
you are about to use. This is done by a big piece of code which requires adding tokens. In
the ServiceProvider of this package we've made a singleton that does all this for you:

```php
$connection = app()->make('Exact\Connection');
```

This connection then is used when requesting resources using Picqer's classes:

```php
use \Picqer\Financials\Exact\Account;

// List all accounts
$account = new Account($connection);
dd($account->get());
```

Using the Facade, we tried to make things easy, for instance getting a list of accounts:

```php
ExactOnline::Account()->get();
```

Or finding a specific account:

```php
ExactOnline::Account()->find('account_ID');
```

All methods that change the connection are camelCased and prefixed with connection, for
example if you want to change the baseUrl of the API you would call:

```php
ExactOnline::connectionSetBaseUrl('http://start.exactonline.de');
```

Of course everything is chainable for readability:

```php
ExactOnline::connectionSetBaseUrl('http://start.exactonline.de')
    ->Account()
    ->find('account_ID');
```

## Credits

- [Picqer/exact-php-client](https://github.com/picqer/exact-php-client)

## Security

If you discover any security related issues, please email joshua@pendo.nl instead of using the issue tracker.

## About Pendo
Pendo is a webdevelopment agency based in Maastricht, Netherlands. If you'd like, you can [visit our website](https://pendo.nl).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
