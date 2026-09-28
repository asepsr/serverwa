<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;
use Zxing\QrReader;

class QrisConverter
{
    public function generate(string $orderNumber, float|int|string $amount): ?string
    {
        $source = $this->sourceFile();
        if (! $source) {
            return null;
        }

        try {
            $reader = new QrReader($source);
            $payload = $reader->text();
            if (! is_string($payload) || $payload === '') {
                throw new RuntimeException('Payload QRIS tidak dapat dibaca.');
            }

            $dynamicPayload = $this->makeDynamicPayload($payload, $amount);
            $directory = public_path('uploads/qris/generated');
            File::ensureDirectoryExists($directory);
            $filename = 'qris-'.$this->safeFilename($orderNumber).'.png';
            $path = $directory.'/'.$filename;

            (new Builder(
                data: $dynamicPayload,
                size: 600,
                margin: 16,
            ))->build()->saveToFile($path);

            return asset('uploads/qris/generated/'.$filename);
        } catch (Throwable) {
            return null;
        }
    }

    private function sourceFile(): ?string
    {
        $files = File::glob(public_path('uploads/qris/qris.*'));
        return $files[0] ?? null;
    }

    private function makeDynamicPayload(string $payload, float|int|string $amount): string
    {
        $tags = $this->parseTags($payload);
        $amountValue = (string) (int) round((float) $amount);
        $hasAmount = false;
        $output = '';

        foreach ($tags as $tag) {
            if ($tag['id'] === '63') {
                continue;
            }
            if ($tag['id'] === '01') {
                $tag['value'] = '12';
            }
            if ($tag['id'] === '54') {
                $tag['value'] = $amountValue;
                $hasAmount = true;
            }
            $output .= $this->encodeTag($tag['id'], $tag['value']);
        }

        if (! $hasAmount) {
            $output .= $this->encodeTag('54', $amountValue);
        }

        $crcInput = $output.'6304';
        return $crcInput.$this->encodeCrc($crcInput);
    }

    private function parseTags(string $payload): array
    {
        $tags = [];
        $offset = 0;
        $length = strlen($payload);

        while ($offset + 4 <= $length) {
            $id = substr($payload, $offset, 2);
            $valueLength = (int) substr($payload, $offset + 2, 2);
            $value = substr($payload, $offset + 4, $valueLength);
            if (strlen($value) !== $valueLength) {
                throw new RuntimeException('Payload QRIS tidak valid.');
            }
            $tags[] = ['id' => $id, 'value' => $value];
            $offset += 4 + $valueLength;
        }

        if ($offset !== $length) {
            throw new RuntimeException('Payload QRIS memiliki panjang tidak valid.');
        }

        return $tags;
    }

    private function encodeTag(string $id, string $value): string
    {
        return $id.str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT).$value;
    }

    private function encodeCrc(string $input): string
    {
        $crc = 0xFFFF;
        for ($index = 0; $index < strlen($input); $index++) {
            $crc ^= ord($input[$index]) << 8;
            for ($bit = 0; $bit < 8; $bit++) {
                $crc = ($crc & 0x8000) !== 0
                    ? (($crc << 1) ^ 0x1021) & 0xFFFF
                    : ($crc << 1) & 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    private function safeFilename(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9_-]/', '-', $value) ?: 'order';
    }
}
