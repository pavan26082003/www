<?php

/**
 * GRID Chat timestamp helpers.
 * DB stores datetime(3) as UTC; the React UI expects ISO-8601 UTC strings.
 */

if (!function_exists('grid_chat_now')) {
    function grid_chat_now(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.v');
    }
}

if (!function_exists('grid_chat_iso')) {
    function grid_chat_iso(?string $datetime): ?string
    {
        if ($datetime === null || $datetime === '') {
            return null;
        }

        try {
            $dt = new DateTimeImmutable($datetime, new DateTimeZone('UTC'));
            return $dt->format('Y-m-d\TH:i:s.v\Z');
        } catch (Exception $e) {
            return null;
        }
    }
}

if (!function_exists('grid_chat_from_iso')) {
    function grid_chat_from_iso(?string $iso): ?string
    {
        if ($iso === null || $iso === '') {
            return null;
        }

        try {
            $dt = new DateTimeImmutable($iso);
            return $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s.v');
        } catch (Exception $e) {
            return null;
        }
    }
}

if (!function_exists('grid_chat_is_future')) {
    function grid_chat_is_future(?string $datetime): bool
    {
        if ($datetime === null || $datetime === '') {
            return false;
        }

        try {
            return new DateTimeImmutable($datetime, new DateTimeZone('UTC')) > new DateTimeImmutable('now', new DateTimeZone('UTC'));
        } catch (Exception $e) {
            return false;
        }
    }
}
