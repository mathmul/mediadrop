<?php

dataset('supported_media_types', [
    // [filename,    size_kb,  media_type]
    ['ok.jpg',       64,       'image/jpeg'],
    ['ok.png',       64,       'image/png'],
    ['ok.gif',       64,       'image/gif'],
    ['ok.webp',      64,       'image/webp'],
    ['ok.mp4',       1024,     'video/mp4'],
    ['ok.mov',       1024,     'video/quicktime'],
    ['ok.webm',      1024,     'video/webm'],
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
