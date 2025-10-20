<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function Pest\Laravel\withHeader;

describe('POST /api/media', function () {

    beforeEach(function () {
        withHeader('Accept', 'application/json');
        Storage::fake('public');
    });

    it('rejects unauthenticated requests', function () {
        post('/api/media', [
            'title' => 'No auth',
            'description' => '',
            'file' => UploadedFile::fake()->image('x.jpg'),
        ])
            ->assertUnauthorized();
    });

    describe('authorized', function () {

        beforeEach(function () {
            Sanctum::actingAs(User::factory()->create());
        });

        it('returns 201 Created with expected response shape', function () {
            $file = UploadedFile::fake()->image('test.jpg');
            post('/api/media', [
                'title' => 'Test upload',
                'description' => 'My first test image',
                'file' => $file,
            ])
                ->assertCreated()
                ->assertJsonStructure([
                    'id',
                    'title',
                    'description',
                    'media_type',
                    'size',
                    'public_url',
                ])
                ->assertJsonPath('public_url', fn ($url) => str_contains($url, '/storage/media/'));
        });

        it('stores the file on public disk', function () {
            $file = UploadedFile::fake()->image('test.jpg');
            post('/api/media', [
                'title' => 'Test upload',
                'description' => 'My first test image',
                'file' => $file,
            ]);
            Storage::disk('public')->assertExists('media/'.$file->hashName());
        });

        it('stores a media record in the database', function () {
            $file = UploadedFile::fake()->image('test.jpg');
            post('/api/media', [
                'title' => 'Test upload',
                'description' => 'My first test image',
                'file' => $file,
            ]);
            assertDatabaseHas('media', [
                'title' => 'Test upload',
                'description' => 'My first test image',
                'disk' => 'public',
                'path' => 'media/'.$file->hashName(),
                'media_type' => 'image/jpeg',
                'size' => $file->getSize(),
            ]);
        });

        describe('validation', function () {

            it('rejects JSON uploads with validation error', function () {
                postJson('/api/media', [
                    'title' => 'Bad JSON upload',
                    'description' => 'Request header "application/json" does not support file uploads',
                    'file' => 'path, base64, or some other string',
                ])
                    ->assertUnprocessable()
                    ->assertJsonValidationErrors(['file']);
            });

            it('requires a title', function () {
                $file = UploadedFile::fake()->image('x.jpg');
                post('/api/media', [
                    'title' => '',
                    'file' => $file,
                ])
                    ->assertUnprocessable()
                    ->assertJsonValidationErrors(['title']);
            });

            it('requires a file', function () {
                post('/api/media', [
                    'title' => 'Missing file',
                ])
                    ->assertUnprocessable()
                    ->assertJsonValidationErrors(['file']);
            });

            it('accepts supported media types', function (string $name, int $kb, string $mediaType) {
                $file = UploadedFile::fake()->create($name, $kb, $mediaType);
                post('/api/media', [
                    'title' => 'Bad type',
                    'file' => $file,
                ])
                    ->assertCreated();
            })->with('supported_media_types');

            it('rejects unsupported media types', function (string $name, int $kb, string $mediaType) {
                $file = UploadedFile::fake()->create($name, $kb, $mediaType);
                post('/api/media', [
                    'title' => 'Bad type',
                    'file' => $file,
                ])
                    ->assertUnprocessable()
                    ->assertJsonValidationErrors(['file']);
            })->with('unsupported_media_types');

            /**
             * Laravel's file "max" rule is in KB.
             * 200 MB = 204800 KB
             */
            it('accepts a 200 MB upload', function () {
                $file = UploadedFile::fake()->create('ok-200mb.mp4', 204_800, 'video/mp4');
                post('/api/media', [
                    'title' => 'Max size',
                    'description' => 'Exactly at 200 MB limit',
                    'file' => $file,
                ])
                    ->assertCreated();
            });

            it('rejects a 201 MB upload with validation error', function () {
                $file = UploadedFile::fake()->create('too-big-201mb.mp4', 206_000, 'video/mp4');
                post('/api/media', [
                    'title' => 'Too big',
                    'description' => 'Bigger than 200 MB',
                    'file' => $file,
                ])
                    ->assertUnprocessable()
                    ->assertJsonValidationErrors(['file']);
            });
        });
    });
});
