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
- [X] Task 8:
    <details>
    <summary>Polish & Optional Additions</summary>
    <p>Run tests, clean up code.</p>
    </details>
- [X] Task 9:
    <details>
    <summary>Dockerize</summary>
    <p>Containerize the application (Nginx + PHP-FPM + Postgres) with Compose profiles for both “DB-only” (Herd) and full stack.</p>
    </details>
- [ ] Task 10:
    <details>
    <summary>Git Flow</summary>
    <p>If there are multiple maintainers, enforce a branching strategy. Protect <code>main</code> (no direct pushes), use feature branches and pull requests with reviews, and require passing CI before merges.</p>
    </details>




## Getting started

We support two local setups - pick one (don't run both on the same ports):

- Herd (MacOS, Windows) - uses Docker only for Postgres
- Docker (Linux, MacOS, Windows) - Nginx + PHP-FPM + Postgres in containers

### Herd

Prerequisites:
- [Laravel Herd](https://herd.laravel.com/docs/macos/getting-started/installation)
- Parent folder of project root is added to *Herd Paths* (in Herd settings)

Quick start:

```bash
# 1) Clone
git clone git@github.com:mathmul/mediadrop.git && cd mediadrop

# 2) Copy .env
cp .env.example .env

# 3) Uncomment Herd-specific config in .env
#    APP_URL=https://mediadrop.test
#    DB_HOST=127.0.0.1
#    SESSION_DRIVER=database

# 4) App key & storage link
herd php artisan key:generate
herd php artisan storage:link

# 5) Start Postgres only
docker compose --profile dbonly up -d
# To stop Postgres only: docker compose --profile dbonly down

# 6) Migrate
herd php artisan migrate

# 7) (Optional) Health + tests
curl https://mediadrop.test/api/health && echo
herd php artisan test

# 8) Serve via Herd
herd init
```

### Docker

Prerequisites

- Docker Desktop 4.x
- Ports 8080 (web) and 5432 (Postgres) free

Quick start:

```bash
# 1) Clone
git clone git@github.com:mathmul/mediadrop.git && cd mediadrop

# 2) Copy .env
cp .env.example .env

# 3) Uncomment Docker-specific config in .env
#    APP_URL=http://localhost:8080
#    DB_HOST=postgres
#    SESSION_DRIVER=file

# 4) Start full stack (Compose profile: full)
docker compose --profile full up -d
# To stop the full stack: docker compose --profile full down

# 5) Run migrations
docker compose exec app php artisan migrate

# 6) (Optional) Health + tests
curl http://localhost:8080/api/health && echo
docker compose exec app php artisan test
```

> **NOTE:** When using Docker, any commands you’d normally run with `herd` should be run with `docker compose exec app` instead (e.g., `docker compose exec app php artisan <command>`).


## Development

### TDD

We follow a Test-Driven Development (TDD) approach:

1. Write a feature test
2. Run the test and verify it fails (red)
3. Implement the minimal code to pass
4. Run the test and verify it passes (green)
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
# or
docker compose exec app php artisan test
```

Alternatively, we can run Pest directly:

```bash
./vendor/bin/pest
```

### Code style (Pint)

We use Laravel Pint for code formatting and linting.
Pint follows PSR-12 and Laravel conventions out of the box.

You can lint manually or run it via Composer:

```bash
# Herd
herd composer lint
# Docker
docker compose exec app composer lint
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

### *NOTES*

- `POST /api/media` accepts files up to **200 MB**.
- For large uploads:
    - **PHP**: set `upload_max_filesize`, `post_max_size` >= 200M; `memory_limit` >= 256M.
    - **Herd**: Configure *Max File Upload Size* >= 200 and *Memory Limit* >= 256 in Herd PHP settings.
    - **Nginx**: `client_max_body_size 256M` is set in *docker/nginx/default.conf*.
- The API stores and serves files; no image processing is performed. *GD is present in the dev container* for tests using `UploadedFile::fake()->image()`, but the API itself doesn’t require GD to serve or store files.


## Testing

Example test run:

```bash
herd php artisan test
# or
docker compose exec app php artisan test
```

<details>
<summary>Example test run</summary>
<pre>
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
</pre>
</details>

### Postman

Sanctum requires a Bearer token.

```bash
# Herd:
herd php artisan tinker
# Docker:
docker compose exec app php artisan tinker

# In Tinker:
$user = \App\Models\User::first() ?? \App\Models\User::factory()->create(['email' => 'tester@example.com']);
$token = $user->createToken('postman')->plainTextToken;
$token
# example: 1|mVsbWKUDHax0SSk52sE8byAYdfOrLkcZNd8m5Mk4d42ce190
```

Copy it and use it in:

- Postman: `Authorization tab > Auth type: Bearer token > Token: <paste the token here>`.
- cURL: `curl -H "Authorization: Bearer <token>" https://mediadrop.test/api/media`

#### Example request

Herd (HTTPS):

```bash
POST https://mediadrop.test/api/media
```

Docker (HTTP):

```bash
POST http://localhost:8080/api/media
```

**Body (multipart/form-data):**

- `title`: "Test upload"
- `description`: "Optional"
- `file`: (choose a file)

**cURL (Docker example)**

```bash
TOKEN="<paste token here>"
curl -X POST http://localhost:8080/api/media \
  -H "Authorization: Bearer $TOKEN" \
  -F "title=My upload" \
  -F "description=Optional" \
  -F "file=@/path/to/local/file.jpg"
```

**Example response body (201 Created):**

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

## Docker Compose profiles

We use profiles to support both workflows:

- `dbonly` - runs only Postgres (use with Herd).
- `full` - runs Nginx + PHP-FPM + Postgres (+ queue/scheduler).

```bash
# Herd + DB-only
docker compose --profile dbonly up -d
docker compose --profile dbonly down

# Full Docker stack
docker compose --profile full up -d
docker compose --profile full down
```
