<?php

namespace App\Services;

class ProfanityFilterService
{
    /**
     * Daftar kata kasar, kotor, dan ejekan/toxic (Bahasa Indonesia & English).
     */
    protected static array $defaultBadWords = [
        // ==========================================
        // 🇮🇩 BAHASA INDONESIA - KATA KASAR / VULGAR
        // ==========================================
        'anjing', 'asu', 'babi', 'bangsat', 'bajingan', 'kampret', 'pantek',
        'kontol', 'memek', 'ngentot', 'itil', 'jembut', 'pepek', 'titit',
        'tetek', 'toket', 'perek', 'lonte', 'sundal', 'jablay', 'pelacur',
        'bokep', 'porno', 'ngocok', 'coli', 'sange', 'jancok', 'jancuk',
        'dancok', 'tai', 'taik', 'tahi', 'celeng', 'kunyuk', 'bangke',
        'keparat', 'biadab', 'setan', 'iblis', 'dajal', 'laknat', 'kimak',
        'puki', 'pukimak', 'cukimay', 'tempik',

        // ==========================================
        // 🇮🇩 BAHASA INDONESIA - EJEKAN & TOXIC
        // ==========================================
        'tolol', 'goblok', 'bego', 'bodoh', 'bloon', 'dungu', 'idiot',
        'autis', 'cacat', 'gila', 'sarap', 'sinting', 'miring', 'sampah',
        'pecundang', 'kampungan', 'udik', 'bebal', 'jongos', 'babu',
        'bencong', 'banci', 'sialan', 'brengsek', 'bacot', 'bacotmu',
        'muka jelek', 'buruk rupa',

        // ==========================================
        // 🇬🇧 ENGLISH - PROFANITY & VULGAR
        // ==========================================
        'fuck', 'fucker', 'fucking', 'motherfucker', 'shit', 'bullshit',
        'bitch', 'bitches', 'asshole', 'ass', 'cunt', 'dick', 'cock',
        'pussy', 'bastard', 'slut', 'whore', 'wanker', 'prick', 'twat',
        'jackass', 'dipshit', 'blowjob', 'handjob', 'nigger', 'nigga',
        'faggot',

        // ==========================================
        // 🇬🇧 ENGLISH - MOCKERY & TOXIC INSULTS
        // ==========================================
        'idiot', 'stupid', 'moron', 'retard', 'retarded', 'loser',
        'dumbass', 'dumb', 'scumbag', 'trash', 'toxic', 'ugly', 'freak',
        'stfu', 'worthless', 'pathetic', 'kill yourself', 'kys'
    ];

    /**
     * Map leetspeak / variasi angka dan karakter ke huruf asli.
     */
    protected static array $leetMap = [
        '0' => 'o',
        '1' => 'i',
        '3' => 'e',
        '4' => 'a',
        '5' => 's',
        '7' => 't',
        '8' => 'b',
        '@' => 'a',
        '$' => 's',
        '!' => 'i',
        '|' => 'i',
        '+' => 't',
    ];

    /**
     * Normalisasi teks: lowercasing, konversi leetspeak, dan reduksi karakter berulang (e.g. "annjjiiing" -> "anjing").
     */
    public static function normalize(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');

        // Ganti simbol leetspeak
        $text = strtr($text, self::$leetMap);

        // Kompresi huruf berulang lebih dari 2x menjadi 1x (contoh: "goooobloooook" -> "goblok", "fuuuuck" -> "fuck")
        $text = preg_replace('/(.)\\1{2,}/u', '$1', $text);

        return $text;
    }

    /**
     * Deteksi apakah teks mengandung kata kotor / toxic.
     * Mengembalikan array hasil: ['clean' => bool, 'detected' => string[], 'message' => string]
     */
    public static function check(?string $text): array
    {
        if (empty($text) || trim($text) === '') {
            return [
                'clean' => true,
                'detected' => [],
                'message' => '',
            ];
        }

        $rawNormalized = mb_strtolower($text, 'UTF-8');
        $leetNormalized = self::normalize($text);

        // Hilangkan spasi/titik antar huruf untuk deteksi evasion seperti "a n j i n g" atau "f.u.c.k"
        $compactRaw = preg_replace('/[\\s._\\-*]+/u', '', $rawNormalized);
        $compactLeet = preg_replace('/[\\s._\\-*]+/u', '', $leetNormalized);

        $detected = [];

        foreach (self::getBadWords() as $word) {
            $pattern = '/\\b' . preg_quote($word, '/') . '\\b/iu';

            // 1. Cek pada teks asli
            if (preg_match($pattern, $rawNormalized)) {
                $detected[] = $word;
                continue;
            }

            // 2. Cek pada teks hasil leetspeak normalisasi
            if (preg_match($pattern, $leetNormalized)) {
                $detected[] = $word;
                continue;
            }

            // 3. Cek pada teks padat tanpa spasi (hanya untuk kata panjang >= 4 huruf untuk menghindari false positive)
            if (mb_strlen($word) >= 4) {
                if (str_contains($compactRaw, $word) || str_contains($compactLeet, $word)) {
                    $detected[] = $word;
                }
            }
        }

        $detected = array_values(array_unique($detected));
        $isClean = empty($detected);

        $message = '';
        if (!$isClean) {
            $wordList = implode(', ', array_map(fn($w) => "'{$w}'", $detected));
            $message = "Peringatan Etika & Kebijakan: Masukan Anda terdeteksi mengandung kata yang tidak pantas, kasar, atau bernada mengejek ({$wordList}). Mohon sampaikan masukan secara santun, objektif, dan membangun demi kenyamanan bersama.";
        }

        return [
            'clean' => $isClean,
            'detected' => $detected,
            'message' => $message,
        ];
    }

    /**
     * Cek cepat boolean apakah teks aman dan bebas dari kata kotor/toxic.
     */
    public static function isClean(?string $text): bool
    {
        return self::check($text)['clean'];
    }

    /**
     * Sensor teks dengan mengganti kata kotor dengan karakter pengganti (default: *).
     */
    public static function mask(?string $text, string $replacement = '*'): string
    {
        if (empty($text)) {
            return '';
        }

        $result = $text;
        foreach (self::getBadWords() as $word) {
            $pattern = '/\\b' . preg_quote($word, '/') . '\\b/iu';
            $result = preg_replace_callback($pattern, function ($matches) use ($replacement) {
                $len = mb_strlen($matches[0]);
                if ($len <= 2) {
                    return str_repeat($replacement, $len);
                }
                // Tampilkan huruf pertama dan terakhir, sisanya sensor (contoh: k***l)
                return mb_substr($matches[0], 0, 1) . str_repeat($replacement, $len - 2) . mb_substr($matches[0], -1);
            }, $result);
        }

        return $result;
    }

    /**
     * Ambil seluruh kata terlarang untuk kebutuhan API / client-side JS filter.
     */
    public static function getBadWords(): array
    {
        $custom = \App\Models\Setting::get('profanity_words', '');
        $customWords = preg_split('/[\r\n,]+/', (string) $custom, -1, PREG_SPLIT_NO_EMPTY);
        $customWords = array_map(fn ($word) => mb_strtolower(trim($word), 'UTF-8'), $customWords);
        $customWords = array_filter($customWords, fn ($word) => mb_strlen($word) >= 2);

        return array_values(array_unique(array_merge(self::$defaultBadWords, $customWords)));
    }
}
