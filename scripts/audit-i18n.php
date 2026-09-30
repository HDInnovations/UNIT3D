<?php

declare(strict_types=1);

// Run with php scripts/audit-i18n.php; does not boot Laravel or access the database.
$root = dirname(__DIR__);
$catalogues = [];
$catalogueIssues = [];
$duplicateKeys = static function (string $source): array {
    $tokens = token_get_all($source);
    $arrays = [];
    $duplicates = [];
    foreach ($tokens as $index => $token) {
        if ($token === '[') {
            $arrays[] = [];
        } elseif ($token === ']') {
            array_pop($arrays);
        } elseif ($arrays !== [] && is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING) {
            $next = $index + 1;
            while (isset($tokens[$next]) && is_array($tokens[$next]) && in_array($tokens[$next][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                $next++;
            }
            if (isset($tokens[$next]) && is_array($tokens[$next]) && $tokens[$next][0] === T_DOUBLE_ARROW) {
                $key = substr($token[1], 1, -1);
                $level = count($arrays) - 1;
                if (isset($arrays[$level][$key])) {
                    $duplicates[] = ['key' => $key, 'line' => $token[2]];
                }
                $arrays[$level][$key] = true;
            }
        }
    }
    return $duplicates;
};
$flatten = function (array $values, string $prefix = '') use (&$flatten): array {
    $result = [];
    foreach ($values as $key => $value) {
        $name = $prefix === '' ? (string) $key : $prefix.'.'.$key;
        if (is_array($value)) {
            $result += $flatten($value, $name);
        } elseif (is_string($value)) {
            $result[$name] = $value;
        }
    }
    return $result;
};
foreach (glob($root.'/lang/*', GLOB_ONLYDIR) as $directory) {
    $locale = basename($directory);
    if ($locale === 'vendor') {
        continue;
    }
    $catalogues[$locale] = [];
    foreach (glob($directory.'/*.php') as $file) {
        if (in_array($locale, ['en', 'cs'], true)) {
            foreach ($duplicateKeys(file_get_contents($file)) as $duplicate) {
                $catalogueIssues[] = ['type' => 'duplicate', 'locale' => $locale, 'file' => substr($file, strlen($root) + 1)] + $duplicate;
            }
        }
        $values = require $file;
        if (is_array($values)) {
            $catalogues[$locale] += $flatten($values, pathinfo($file, PATHINFO_FILENAME));
        }
    }
}
$references = [];
foreach (['app', 'resources', 'routes', 'config', 'database'] as $directory) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/'.$directory, FilesystemIterator::SKIP_DOTS));
    foreach ($files as $file) {
        if (!$file->isFile() || !in_array($file->getExtension(), ['php', 'js'], true)) {
            continue;
        }
        $source = file_get_contents($file->getPathname());
        preg_match_all('/(?:__|trans|trans_choice|@lang|@choice|Lang::get|Lang::choice)\s*\(\s*([\'"])([a-zA-Z][a-zA-Z0-9_-]*\.[a-zA-Z0-9_.-]+)\1(?!\s*\.)/', $source, $matches);
        preg_match_all('/const\s+string\s+DESCRIPTION_KEY\s*=\s*([\'"])([a-zA-Z][a-zA-Z0-9_-]*\.[a-zA-Z0-9_.-]+)\1/', $source, $achievementKeys);
        foreach (array_merge($matches[2], $achievementKeys[2]) as $key) {
            $references[$key][] = substr($file->getPathname(), strlen($root) + 1);
        }
    }
}
ksort($references);
$issues = $catalogueIssues;
foreach ($references as $key => $files) {
    foreach (['en', 'cs'] as $locale) {
        $exists = array_key_exists($key, $catalogues[$locale]);
        if (!$exists) {
            // Laravel also returns complete translation arrays (e.g. invitation rules).
            foreach ($catalogues[$locale] as $entry => $_) {
                if (str_starts_with($entry, $key.'.')) {
                    $exists = true;
                    break;
                }
            }
        }
        if (!$exists) {
            $issues[] = ['type' => 'missing', 'locale' => $locale, 'key' => $key, 'files' => array_values(array_unique($files))];
        }
    }
}
$placeholders = static function (string $text): array {
    preg_match_all('/(?<![a-zA-Z0-9]):([a-zA-Z_][a-zA-Z0-9_]*)/', $text, $matches);
    $names = array_values(array_unique(array_map('strtolower', $matches[1])));
    sort($names);
    return $names;
};
foreach ($catalogues['en'] as $key => $text) {
    if (!array_key_exists($key, $catalogues['cs'])) {
        $issues[] = ['type' => 'missing-catalogue', 'locale' => 'cs', 'key' => $key];
    } elseif ($placeholders($text) !== $placeholders($catalogues['cs'][$key])) {
        $issues[] = ['type' => 'placeholders', 'locale' => 'cs', 'key' => $key, 'en' => $placeholders($text), 'cs' => $placeholders($catalogues['cs'][$key])];
    }
}
if (in_array('--catalogues', $argv, true)) {
    echo json_encode($catalogues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
    exit(0);
}
echo json_encode(['locales' => count($catalogues), 'referenced_keys' => count($references), 'issues' => $issues], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
exit($issues === [] ? 0 : 1);
