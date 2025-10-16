# Mediadrop

Laravel 12 / PHP 8.4 project — assignment to build an authenticated API for media uploads.

## TODO

- [X] Task 1:
    <details>
    <summary>Initialize project</summary>
    <p>Use Laravel Herd to initialize the project. Use PHP 8.4 and Laravel 12. Replace PHPUnit with Pest. Initialize git repository and push to GitHub.</p>
    </details>
- [X] Task 2:
    <details>
    <summary>Environment Setup</summary>
    <p>Configure .env, set up postgres database (use Docker), run migrations, install and link required packages (Sanctum, Ray).</p>
    </details>
- [ ] Task 3:
    <details>
    <summary>Write Tests First (TDD)</summary>
    <p>Create feature tests for the media upload endpoint covering authentication, validation, and database storage. One test, one implementation, then next test, next implementation, etc.</p>
    </details>
- [ ] Task 4:
    <details>
    <summary>Implement Models & Migrations</summary>
    <p>Define the <code>Media</code> model with UUIDv7 IDs, create a migration including title, description, file path, media (MIME) type, and size columns.</p>
    </details>
- [ ] Task 5:
    <details>
    <summary>Implement Controller Logic</summary>
    <p>Build <code>MediaController@store</code> to handle uploads, store files on the public disk, create DB records, and return metadata + public URL.</p>
    </details>
- [ ] Task 6:
    <details>
    <summary>Add Authentication</summary>
    <p>Use Laravel Sanctum for token-based authentication and protect the API route with <code>auth:sanctum</code> middleware.</p>
    </details>
- [ ] Task 7:
    <details>
    <summary>Documentation</summary>
    <p>Document setup steps, API endpoint details, and testing instructions in this README.</p>
    </details>
- [ ] Task 8:
    <details>
    <summary>Polish & Optional Additions</summary>
    <p>Run tests, clean up code, add Ray debugging, and consider extending with Livewire for a simple upload UI.</p>
    </details>

## VSCode extensions installed

- PHP Intelephense
- Laravel Extra Intellisense
- Laravel goto view

## Commands run

```bash
npm i
herd composer require spatie/laravel-ray
herd composer require laravel/sanctum
herd php artisan tinker
> DB::connection()->getPdo();
herd php artisan migrate
herd composer require --dev barryvdh/laravel-ide-helper
herd php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
herd php artisan migrate
```
