<?php

it('returns 200 OK for the API health endpoint', function () {
    $this->getJson('/api/health')
        ->assertOk()
        ->assertJson(['ok' => true]);
});
