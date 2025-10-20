<?php

use function Pest\Laravel\getJson;

describe('GET /api/health', function () {
    it('returns 200 OK', function () {
        getJson('/api/health')
            ->assertOk()
            ->assertJson(['ok' => true]);
    });
});
