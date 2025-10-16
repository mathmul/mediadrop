<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;

it('allows an authenticated user to upload a media file and stores its record', function () {
    $disk = config('filesystems.default', 'public');
    Storage::fake($disk);
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->postJson('/api/media', [
        'title' => 'Test upload',
        'description' => 'My first test image',
        'file' => $file,
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'id',
            'title',
            'description',
            'media_type',
            'size',
            'public_url'
        ]);

    // Assert the file was stored
    /** @var \Illuminate\Filesystem\FilesystemAdapter|\Illuminate\Filesystem\FakeFilesystem $storage */
    $storage = Storage::disk($disk);
    $storage->assertExists('media/' . $file->hashName());

    // Assert the database has a record
    $this->assertDatabaseHas('media', [
        'title' => 'Test upload',
        'media_type' => 'image/jpeg',
    ]);
});
