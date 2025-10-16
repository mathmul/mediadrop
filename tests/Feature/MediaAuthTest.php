<?php

it('rejects unauthenticated media upload', function () {
    $this->postJson('/api/media', [])
        ->assertUnauthorized();
});
