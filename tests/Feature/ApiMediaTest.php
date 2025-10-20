<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;

dataset('supported_media_types', [
    ['ok.jpg',   64,   'image/jpeg'],
    ['ok.png',   64,   'image/png'],
    ['ok.gif',   64,   'image/gif'],
    ['ok.webp',  64,   'image/webp'],
    ['ok.mp4',   1024, 'video/mp4'],
    ['ok.mov',   1024, 'video/quicktime'],
    ['ok.webm',  1024, 'video/webm'],
]);
dataset('unsupported_media_types', [
    // [filename,    size_kb,  media_type]
    ['readme.txt',   4,        'text/plain'],
    ['vector.svg',   10,       'image/svg+xml'], // TODO (mathmul): Should we support SVG uploads?
    ['photo.heic',   512,      'image/heic'],
    ['photo.heif',   512,      'image/heif'],
    ['track.mp3',    1024,     'audio/mpeg'],
    ['movie.mkv',    2048,     'video/x-matroska'],
    ['archive.zip',  64,       'application/zip'],
    ['script.js',    5,        'application/javascript'],
]);

describe('POST /api/media', function () {

    beforeEach(function () {
        $this->withHeader('Accept', 'application/json');
        Storage::fake('public');
    });

    it('rejects unauthenticated requests', function () {
        $this->post('/api/media', [
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
            $this->post('/api/media', [
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
                    'public_url'
                ])
                ->assertJsonPath('public_url', fn($url) => str_contains($url, '/storage/media/'));
        });

        it('stores the file on public disk', function () {
            $file = UploadedFile::fake()->image('test.jpg');
            $this->post('/api/media', [
                'title' => 'Test upload',
                'description' => 'My first test image',
                'file' => $file,
            ]);
            Storage::disk('public')->assertExists('media/' . $file->hashName());
        });

        it('stores a media record in the database', function () {
            $file = UploadedFile::fake()->image('test.jpg');
            $this->post('/api/media', [
                'title' => 'Test upload',
                'description' => 'My first test image',
                'file' => $file,
            ]);
            $this->assertDatabaseHas('media', [
                'title' => 'Test upload',
                'description' => 'My first test image',
                'disk' => 'public',
                'path' => 'media/' . $file->hashName(),
                'media_type' => 'image/jpeg',
                'size' => $file->getSize(),
            ]);
        });

        describe('validation', function () {

            it('rejects JSON uploads with validation error', function () {
                $this->postJson('/api/media', [
                    'title' => 'Bad JSON upload',
                    'description' => 'Request header "application/json" does not support file uploads',
                    'file' => 'path, base64, or some other string',
                ])
                    ->assertUnprocessable()
                    ->assertJsonValidationErrors(['file']);
            });

            it('requires a title', function () {
                $file = UploadedFile::fake()->image('x.jpg');
                $this->post('/api/media', [
                    'title' => '',
                    'file' => $file,
                ])
                    ->assertUnprocessable()
                    ->assertJsonValidationErrors(['title']);
            });

            it('requires a file', function () {
                $this->post('/api/media', [
                    'title' => 'Missing file',
                ])
                    ->assertUnprocessable()
                    ->assertJsonValidationErrors(['file']);
            });

            it('accepts supported media types', function (string $name, int $kb, string $mediaType) {
                $file = UploadedFile::fake()->create($name, $kb, $mediaType);
                $this->post('/api/media', [
                    'title' => 'Bad type',
                    'file' => $file,
                ])
                    ->assertCreated();
            })->with('supported_media_types');

            it('rejects unsupported media types', function (string $name, int $kb, string $mediaType) {
                $file = UploadedFile::fake()->create($name, $kb, $mediaType);
                $this->post('/api/media', [
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
                $this->post('/api/media', [
                    'title' => 'Max size',
                    'description' => 'Exactly at 200 MB limit',
                    'file' => $file,
                ])
                    ->assertCreated();
            });

            it('rejects a 201 MB upload with validation error', function () {
                $file = UploadedFile::fake()->create('too-big-201mb.mp4', 206_000, 'video/mp4');
                $this->post('/api/media', [
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
