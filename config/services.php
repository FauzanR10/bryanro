<?php

/**
 * config/services.php
 * ====================
 * File ini menyimpan konfigurasi layanan pihak ketiga.
 * Nilai-nilainya dibaca dari file .env menggunakan fungsi env().
 *
 * PENTING: File ini MENGGANTIKAN config/services.php yang ada di project Laravel.
 * Salin isi file ini ke config/services.php di project Laravel kamu.
 * Jangan hapus konfigurasi yang sudah ada di sana — tambahkan saja bagian 'fonnte'.
 */

return [

    // ── Konfigurasi layanan bawaan Laravel ──────────────
    // (Pertahankan yang sudah ada, tambahkan 'fonnte' di bawah ini)

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // ── WhatsApp Gateway: Fonnte ─────────────────────────
    // Digunakan oleh App\Services\WhatsAppService
    // Token diambil dari .env (FONNTE_TOKEN)
    'fonnte' => [
        'token' => env('FONNTE_TOKEN', ''),
    ],

];
