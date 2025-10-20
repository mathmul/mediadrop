<?php

describe('GET /api/health', function () {
    it('returns 200 OK', function () {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJson(['ok' => true]);
    });
});
