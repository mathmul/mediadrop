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
- [X] Task 3:
    <details>
    <summary>Write Tests First (TDD)</summary>
    <p>Create feature tests for the media upload endpoint covering authentication, validation, and database storage. One test, one implementation, then next test, next implementation, etc.</p>
    </details>
- [X] Task 4:
    <details>
    <summary>Implement Models & Migrations</summary>
    <p>Define the <code>Media</code> model with UUIDv7 IDs, create a migration including title, description, file path, media (MIME) type, and size columns.</p>
    </details>
- [X] Task 5:
    <details>
    <summary>Implement Controller Logic</summary>
    <p>Build <code>MediaController@store</code> to handle uploads, store files on the public disk, create DB records, and return metadata + public URL.</p>
    </details>
- [X] Task 6:
    <details>
    <summary>Add Authentication</summary>
    <p>Use Laravel Sanctum for token-based authentication and protect the API route with <code>auth:sanctum</code> middleware.</p>
    </details>
- [X] Task 7:
    <details>
    <summary>Documentation</summary>
    <p>Document setup steps, API endpoint details, and testing instructions in this README.</p>
    </details>
- [ ] Task 8:
    <details>
    <summary>Polish & Optional Additions</summary>
    <p>Run tests, clean up code, add Ray debugging, and consider extending with Livewire for a simple upload UI.</p>
    </details>

## Development

The easiest way is to use [Laravel Herd](https://herd.laravel.com/docs/macos/getting-started/installation):
- ensure the parent folder of project root is added to *Herd Paths*
- run `herd init` twice, once to set everything up, second time to serve the application

### TDD

We follow a Test-Driven Development (TDD) approach, where every feature is first defined by a test case, and then implemented to make the test pass. Here are the steps:

1. Create a feature test
2. Run the test and see it fail
3. Implement the feature
4. Run the test and see it pass
5. Refactor if needed
6. Repeat

#### Pest

We use Pest instead of PHPUnit.

Since we have [pest-plugin-laravel](https://pestphp.com/docs/plugins#content-laravel) installed, we can create new tests with

```bash
herd php artisan pest:test <TestName>         # Feature test (default)
herd php artisan pest:test <TestName> --unit  # Unit test
herd php artisan pest:dataset <DatasetName>   # Creates tests/Datasets/<DatasetName>.php
```

We can also avoid using `$this->` by using Pest's built-in functions from the `Pest\Laravel` namespace, eg.

```php
use function Pest\Laravel\{assertDatabaseHas, post};

# This example fails, but it shows how to use Pest's built-in functions
it('stores a media record in the database', function () {
    post('/api/media', ['title' => 'Test upload']);
    assertDatabaseHas('media', ['title' => 'Test upload']);
});
```

Run tests with

```bash
herd php artisan test
```

Alternatively, we can run Pest directly:

```bash
./vendor/bin/pest
```

### Misc

<details>
<summary>VSCode extensions installed</summary>
<ul>
<li>PHP Intelephense</li>
<li>Laravel Extra Intellisense</li>
<li>Laravel goto view</li>
</ul>
</details>
<details>
<summary>Commands history</summary>
<div>Commands ran to set up the project:<pre>
npm i
herd composer require spatie/laravel-ray
herd composer require laravel/sanctum
herd php artisan tinker
> DB::connection()->getPdo();
herd php artisan migrate
herd composer require --dev barryvdh/laravel-ide-helper
herd php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
herd php artisan migrate
herd php artisan route:clear
herd php artisan optimize:clear
herd php artisan storage:link
herd php artisan vendor:publish --provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config
herd php artisan ide-helper:generate
herd php artisan ide-helper:meta
herd php artisan ide-helper:models --write --reset
</div></pre>
</details>

### *NOTE*

`POST /api/media` endpoint allows files up to 200 MB to be uploaded to the server. Configure *Max File Upload Size* and *Memory Limit* in Herd PHP settings accordingly (or set `upload_max_filesize` and `memory_limit` to `200M` in *php.ini*).

## Testing

### Terminal

In this project we use Pest instead of PHPUnit.

```bash
herd php artisan test

   PASS  Tests\Unit\SanityCheckTest
  ✓ that true is true                                                                                                                 0.01s

   PASS  Tests\Feature\ApiHealthTest
  ✓ GET /api/health → it returns 200 OK                                                                                               0.12s

   PASS  Tests\Feature\ApiMediaTest
  ✓ POST /api/media → it rejects unauthenticated requests                                                                             0.02s
  ✓ POST /api/media → authorized → it returns 201 Created with expected response shape                                                0.03s
  ✓ POST /api/media → authorized → it stores the file on public disk                                                                  0.01s
  ✓ POST /api/media → authorized → it stores a media record in the database                                                           0.01s
  ✓ POST /api/media → authorized → validation → it rejects JSON uploads with validation error                                         0.01s
  ✓ POST /api/media → authorized → validation → it requires a title                                                                   0.01s
  ✓ POST /api/media → authorized → validation → it requires a file
  ✓ POST /api/media → authorized → validation → it accepts supported media types with ('ok.jpg', 64, 'image/jpeg')
  ✓ POST /api/media → authorized → validation → it accepts supported media types with ('ok.png', 64, 'image/png')                     0.01s
  ✓ POST /api/media → authorized → validation → it accepts supported media types with ('ok.gif', 64, 'image/gif')                     0.01s
  ✓ POST /api/media → authorized → validation → it accepts supported media types with ('ok.webp', 64, 'image/webp')                   0.01s
  ✓ POST /api/media → authorized → validation → it accepts supported media types with ('ok.mp4', 1024, 'video/mp4')                   0.01s
  ✓ POST /api/media → authorized → validation → it accepts supported media types with ('ok.mov', 1024, 'video/quicktime')             0.01s
  ✓ POST /api/media → authorized → validation → it accepts supported media types with ('ok.webm', 1024, 'video/webm')                 0.01s
  ✓ POST /api/media → authorized → validation → it rejects unsupported media types with ('readme.txt', 4, 'text/plain')               0.01s
  ✓ POST /api/media → authorized → validation → it rejects unsupported media types with ('vector.svg', 10, 'image/svg+xml')           0.04s
  ✓ POST /api/media → authorized → validation → it rejects unsupported media types with ('photo.heic', 512, 'image/heic')             0.01s
  ✓ POST /api/media → authorized → validation → it rejects unsupported media types with ('photo.heif', 512, 'image/heif')             0.03s
  ✓ POST /api/media → authorized → validation → it rejects unsupported media types with ('track.mp3', 1024, 'audio/mpeg')             0.01s
  ✓ POST /api/media → authorized → validation → it rejects unsupported media types with ('movie.mkv', 2048, 'video/x-matroska')       0.01s
  ✓ POST /api/media → authorized → validation → it rejects unsupported media types with ('archive.zip', 64, 'application/zip')        0.01s
  ✓ POST /api/media → authorized → validation → it rejects unsupported media types with ('script.js', 5, 'application/javascript')    0.01s
  ✓ POST /api/media → authorized → validation → it accepts a 200 MB upload                                                            0.01s
  ✓ POST /api/media → authorized → validation → it rejects a 201 MB upload with validation error                                      0.01s

  Tests:    26 passed (58 assertions)
  Duration: 0.47s
```

### Postman

To test the API, you can use Postman or any other API client. Because we have Sanctum authentication, you need to add a Bearer token to the Authorization header. You can get the token via command line:

```bash
herd php artisan tinker
> $user = \App\Models\User::first() ?? \App\Models\User::factory()->create(['email' => 'tester@example.com']);
> $token = $user->createToken('postman')->plainTextToken;
```

The token will look something like this: `1|mVsbWKUDHax0SSk52sE8byAYdfOrLkcZNd8m5Mk4d42ce190`. Copy it and use it in `Postman > Authorization tab > Auth type: Bearer token > Token: <paste the token here>`.

#### # Request

**POST https://mediadrop.test/api/media**

**Request Headers:**

- Authorization: Bearer 1|s3oZOex6F25tBSkLGkfQMAXNho89BxoTGXauXh9s70c83fa0
- User-Agent: PostmanRuntime/7.48.0
- Accept: */*
- Cache-Control: no-cache
- Host: mediadrop.test
- Accept-Encoding: gzip, deflate, br
- Connection: keep-alive
- Content-Type: multipart/form-data; boundary=--------------------------868968937604932384420559
- Content-Length: 61850511

**Request Body:**

- title: "Test video"
- description: "This is a test video"
- file: undefined (the file was a 61.9 MB video, but Postman doesn't show it in the console)

#### # Response

**Status Code:** 201

**Response Time:** 552 ms

**Response Headers:**

- Server: nginx/1.25.4
- Content-Type: application/json
- Transfer-Encoding: chunked
- Connection: keep-alive
- X-Powered-By: PHP/8.4.13
- Cache-Control: no-cache, private
- Date: Fri, 17 Oct 2025 16:30:48 GMT
- Vary: Origin

**Response Body:**

```json
{
    "id": "0199f302-7615-733f-96f4-083248778efe",
    "title": "Test video",
    "description": "This is a test video",
    "media_type": "video\/mp4",
    "size": 61850051,
    "public_url": "https:\/\/mediadrop.test\/storage\/media\/0VhKCm5nxsPEEhfqvxn9lhfzGMuHO0tCEjTQOydj.mp4",
    "created_at": "2025-10-17T16:30:48.000000Z"
}
```
