<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class TranslationManager extends Command
{
    protected $signature = 'translations:manage
                        {action : Action to perform (check, sync, report, find-hardcoded, replace-hardcoded, report-hardcoded)}
                        {--lang= : Specific language to check (default: all)}
                        {--show-extra : Show keys that exist in target language but not in base language}
                        {--path= : Path to search for hardcoded strings (default: resources/views)}
                        {--exclude= : Comma-separated paths to exclude}
                        {--markdown : Generate reports in Markdown format instead of plain text}';

    protected $description = 'Manage translation files - check for missing keys, hardcoded text, sync file to base or generate reports';

    protected array $missingKeys = [];
    protected array $extraKeys = [];
    protected array $stats = [];
    protected string $baseLang = 'en';
    protected array $excludedDirs = ['vendor'];

    public function handle(): int
    {
        $action = $this->argument('action');
        $lang = $this->option('lang');
        $showExtra = $this->option('show-extra');

        $this->info('Translation Manager');
        $this->line('Base language: '.$this->baseLang);

        switch ($action) {
            case 'check':
                $languages = $this->getLanguages();

                if ($lang && !\in_array($lang, $languages, true)) {
                    $this->error("Language '{$lang}' not found in lang directory");

                    return 1;
                }
                $languages = $lang ? [$lang] : array_filter($languages, fn ($l) => $l !== $this->baseLang);

                return $this->checkTranslations($languages, $showExtra);
            case 'sync':
                $languages = $this->getLanguages();

                if ($lang && !\in_array($lang, $languages, true)) {
                    $this->error("Language '{$lang}' not found in lang directory");

                    return 1;
                }
                $languages = $lang ? [$lang] : array_filter($languages, fn ($l) => $l !== $this->baseLang);

                return $this->syncTranslations($languages);
            case 'report':
                $languages = $this->getLanguages();

                if ($lang && !\in_array($lang, $languages, true)) {
                    $this->error("Language '{$lang}' not found in lang directory");

                    return 1;
                }
                $languages = $lang ? [$lang] : array_filter($languages, fn ($l) => $l !== $this->baseLang);

                return $this->generateReport($languages, $showExtra);
            case 'find-hardcoded':
                return $this->findHardcodedStrings();
            case 'replace-hardcoded':
                return $this->replaceHardcodedStrings();
            case 'report-hardcoded':
                return $this->reportHardcodedStrings();
            default:
                $this->error("Invalid action: {$action}. Use 'check', 'sync', 'report', 'find-hardcoded', or 'replace-hardcoded'");

                return 1;
        }
    }

    protected function getLanguages(): array
    {
        return collect(File::directories(base_path('lang')))
            ->filter(fn ($dir) => !\in_array(basename($dir), $this->excludedDirs))
            ->map(fn ($dir) => basename($dir))
            ->toArray();
    }

    protected function checkTranslations(array $languages, bool $showExtra = false): int
    {
        $this->info('Checking for missing translation keys...');

        $baseFiles = $this->getTranslationFiles($this->baseLang);
        $baseKeys = $this->getAllKeys($this->baseLang, $baseFiles);

        $this->line("Base language has ".\count($baseKeys)." translation keys in ".\count($baseFiles)." files.");

        foreach ($languages as $lang) {
            $this->line("\nChecking {$lang} translations...");
            $langFiles = $this->getTranslationFiles($lang);
            $langKeys = $this->getAllKeys($lang, $langFiles);

            // Keys missing from target language
            $missing = array_diff_key($baseKeys, $langKeys);

            // Keys in target language but not in base language
            $extra = array_diff_key($langKeys, $baseKeys);

            $existingCount = \count($langKeys) - \count($extra);

            if (!empty($missing)) {
                $this->missingKeys[$lang] = $missing;
                $this->extraKeys[$lang] = $extra;

                $this->stats[$lang] = [
                    'total'      => \count($baseKeys),
                    'existing'   => $existingCount,
                    'missing'    => \count($missing),
                    'extra'      => \count($extra),
                    'percentage' => round($existingCount / \count($baseKeys) * 100, 2)
                ];

                $this->warn("{$this->stats[$lang]['missing']} missing keys, {$existingCount} existing keys (".
                    $this->stats[$lang]['percentage']."% coverage)");

                // Show first 5 missing keys for preview
                if (\count($missing) > 0) {
                    $this->line("First 5 missing keys:");
                    $i = 0;

                    foreach ($missing as $key => $value) {
                        if ($i >= 5) {
                            break;
                        }
                        $this->line("  - {$key} = \"{$value}\"");
                        $i++;
                    }
                }

                // Show extra keys if requested
                if ($showExtra && \count($extra) > 0) {
                    $this->line("\n{$this->stats[$lang]['extra']} extra keys found in {$lang} (not in base language):");
                    $i = 0;

                    foreach ($extra as $key => $value) {
                        if ($i >= 5) {
                            $this->line("  ... and ".(\count($extra) - 5)." more");

                            break;
                        }
                        $this->line("  + {$key} = \"{$value}\"");
                        $i++;
                    }
                }
            } else {
                if (\count($extra) > 0) {
                    $this->info('All base keys present! '.\count($extra).' extra keys found.');

                    if ($showExtra) {
                        $this->line("First 5 extra keys:");
                        $i = 0;

                        foreach ($extra as $key => $value) {
                            if ($i >= 5) {
                                break;
                            }
                            $this->line("  + {$key} = \"{$value}\"");
                            $i++;
                        }
                    }
                } else {
                    $this->info('Perfect match! All keys present with no extras.');
                }

                $this->extraKeys[$lang] = $extra;
                $this->stats[$lang] = [
                    'total'      => \count($baseKeys),
                    'existing'   => $existingCount,
                    'missing'    => 0,
                    'extra'      => \count($extra),
                    'percentage' => 100
                ];
            }
        }

        return 0;
    }

    protected function syncTranslations(array $languages): int
    {
        $this->checkTranslations($languages, true); // true to also track extra keys

        if (empty($this->missingKeys) && empty($this->extraKeys)) {
            $this->info('All translations are in sync!');

            return 0;
        }

        $confirmation = 'This will add missing keys to translation files';

        if (!empty($this->extraKeys) && $this->confirm('Would you also like to remove extra keys that are not in the base language?', false)) {
            $confirmation .= ' and remove extra keys';
            $removeExtra = true;
        } else {
            $removeExtra = false;
        }

        if (!$this->confirm($confirmation.'. Continue?')) {
            $this->warn('Operation cancelled');

            return 1;
        }

        // Process missing keys (existing functionality)
        foreach ($this->missingKeys as $lang => $keys) {
            $this->line("\nSyncing {$lang} translations...");

            // First, group keys by file
            $fileKeys = [];

            foreach ($keys as $key => $value) {
                [$file, $path] = explode('.', $key, 2);
                $fileKeys[$file][$path] = $value;
            }

            // Process each file
            foreach ($fileKeys as $file => $pathValues) {
                $filePath = base_path("lang/{$lang}/{$file}.php");

                // Get current translations or create new file
                if (!File::exists($filePath)) {
                    File::put($filePath, $this->getNewFileTemplate($file));
                    $currentTranslations = [];
                } else {
                    $currentTranslations = require $filePath;

                    if (!\is_array($currentTranslations)) {
                        $this->warn("Warning: {$filePath} does not return an array");
                        $currentTranslations = [];
                    }
                }

                // Add each missing key to the translations array
                foreach ($pathValues as $path => $value) {
                    $currentTranslations = $this->arraySetNested(
                        $currentTranslations,
                        $path,
                        "{$value}"
                    );
                }

                // Remove extra keys if requested
                if ($removeExtra && isset($this->extraKeys[$lang])) {
                    foreach ($this->extraKeys[$lang] as $extraKey => $value) {
                        [$extraFile, $extraPath] = explode('.', $extraKey, 2);

                        if ($extraFile === $file) {
                            $currentTranslations = $this->arrayUnsetNested($currentTranslations, $extraPath);
                        }
                    }
                }

                // Sort translations and write to file
                $currentTranslations = $this->sortArrayRecursively($currentTranslations);
                $fileContent = "<?php\n\ndeclare(strict_types=1);\n/**\n * NOTICE OF LICENSE.\n *\n * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0\n * The details is bundled with this project in the file LICENSE.txt.\n *\n * @project    UNIT3D Community Edition\n *\n * @author     HDVinnie <hdinnovations@protonmail.com>\n * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0\n */\n\nreturn ".$this->varExport($currentTranslations).";\n";
                File::put($filePath, $fileContent);
            }

            $this->info("Added {$this->stats[$lang]['missing']} missing keys to {$lang} translations");

            if ($removeExtra && isset($this->extraKeys[$lang]) && !empty($this->extraKeys[$lang])) {
                $this->info("Removed {$this->stats[$lang]['extra']} extra keys from {$lang} translations");
            }
        }

        return 0;
    }

    // New helper method to unset nested array keys
    protected function arrayUnsetNested(array $array, string $path): array
    {
        $keys = explode('.', $path);
        $current = &$array;
        $lastKey = array_pop($keys);

        // Navigate to the parent of the key to remove
        foreach ($keys as $key) {
            if (!isset($current[$key]) || !\is_array($current[$key])) {
                // Path doesn't exist, nothing to remove
                return $array;
            }
            $current = &$current[$key];
        }

        // Remove the key if it exists
        if (isset($current[$lastKey])) {
            unset($current[$lastKey]);
        }

        return $array;
    }

    /**
     * Sort array recursively to maintain alphabetical order of keys.
     */
    protected function sortArrayRecursively(array $array): array
    {
        // Sort the current level
        ksort($array);

        // Recursively sort any nested arrays
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $array[$key] = $this->sortArrayRecursively($value);
            }
        }

        return $array;
    }

    protected function getNewFileTemplate(string $filename): string
    {
        return <<<EOT
<?php

declare(strict_types=1);
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

return [];

EOT;
    }

    protected function generateReport(array $languages, bool $showExtra = false): int
    {
        $this->checkTranslations($languages, $showExtra);
        $markdownReport = $this->option('markdown') ?? false;

        $this->info("\nTranslation Coverage Report:");

        $headers = ['Language', 'Total Base Keys', 'Existing Keys', 'Missing Keys', 'Coverage'];

        if ($showExtra) {
            $headers[] = 'Extra Keys';
        }

        $rows = collect($this->stats)->map(function ($stat, $lang) use ($showExtra) {
            $row = [
                $lang,
                $stat['total'],
                $stat['existing'],
                $stat['missing'],
                $stat['percentage'].'%'
            ];

            if ($showExtra) {
                $row[] = $stat['extra'];
            }

            return $row;
        })->toArray();

        $this->table($headers, $rows);

        if ($this->confirm('Generate detailed missing keys report?')) {
            foreach ($this->missingKeys as $lang => $keys) {
                if ($markdownReport) {
                    $reportPath = base_path("docs/translation-report-{$lang}.md");
                    $content = "# Missing Translation Keys Report for {$lang}\n\n";

                    $content .= "## Summary\n\n";
                    $content .= "| Metric | Value |\n";
                    $content .= "| --- | --- |\n";
                    $content .= "| Total Base Keys | {$this->stats[$lang]['total']} |\n";
                    $content .= "| Missing Keys | {$this->stats[$lang]['missing']} |\n";
                    $content .= "| Coverage | {$this->stats[$lang]['percentage']}% |\n";

                    if ($showExtra && !empty($this->extraKeys[$lang])) {
                        $content .= "| Extra Keys | {$this->stats[$lang]['extra']} |\n";
                    }

                    $content .= "\n## Missing Keys\n\n";
                    $content .= "| Key | Value |\n";
                    $content .= "| --- | --- |\n";

                    foreach ($keys as $key => $value) {
                        // Escape pipe characters for Markdown tables
                        $escapedValue = str_replace('|', '\|', $value);
                        $escapedKey = str_replace('|', '\|', $key);
                        $content .= "| `{$escapedKey}` | \"{$escapedValue}\" |\n";
                    }

                    if ($showExtra && !empty($this->extraKeys[$lang])) {
                        $content .= "\n## Extra Keys (not in base language)\n\n";
                        $content .= "| Key | Value |\n";
                        $content .= "| --- | --- |\n";

                        foreach ($this->extraKeys[$lang] as $key => $value) {
                            // Escape pipe characters for Markdown tables
                            $escapedValue = str_replace('|', '\|', $value);
                            $escapedKey = str_replace('|', '\|', $key);
                            $content .= "| `{$escapedKey}` | \"{$escapedValue}\" |\n";
                        }
                    }
                } else {
                    // Original text format
                    $reportPath = base_path("docs/translation-report-{$lang}.txt");
                    $content = "Missing translation keys for {$lang}:\n\n";

                    foreach ($keys as $key => $value) {
                        $content .= "{$key} = \"{$value}\"\n";
                    }

                    if ($showExtra && !empty($this->extraKeys[$lang])) {
                        $content .= "\n\nExtra keys in {$lang} (not in base language):\n\n";

                        foreach ($this->extraKeys[$lang] as $key => $value) {
                            $content .= "{$key} = \"{$value}\"\n";
                        }
                    }
                }

                File::put($reportPath, $content);
                $this->info("Report for {$lang} saved to {$reportPath}");
            }
        }

        return 0;
    }

    protected function getTranslationFiles(string $lang): array
    {
        $path = base_path("lang/{$lang}");

        if (!File::exists($path)) {
            return [];
        }

        return collect(File::files($path))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->map(fn (SplFileInfo $file) => $file->getFilenameWithoutExtension())
            ->toArray();
    }

    protected function getAllKeys(string $lang, array $files): array
    {
        $allKeys = [];

        foreach ($files as $file) {
            $filePath = base_path("lang/{$lang}/{$file}.php");

            if (!File::exists($filePath)) {
                continue;
            }

            $translations = require $filePath;

            // Make sure translations is an array
            if (!\is_array($translations)) {
                $this->warn("Warning: {$filePath} does not return an array");

                continue;
            }

            $fileKeys = $this->flattenArray($translations, $file);

            // Fix performance issue with array_merge in loop
            foreach ($fileKeys as $key => $value) {
                $allKeys[$key] = $value;
            }
        }

        return $allKeys;
    }

    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix.'.'.$key;

            if (\is_array($value)) {
                // Fix performance issue with array_merge in loop
                $nestedKeys = $this->flattenArray($value, $newKey);

                foreach ($nestedKeys as $nestedKey => $nestedValue) {
                    $result[$nestedKey] = $nestedValue;
                }
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    protected function arraySetNested(array $array, string $path, $value): array
    {
        $keys = explode('.', $path);
        $current = &$array;

        foreach ($keys as $i => $key) {
            if ($i === \count($keys) - 1) {
                // Last key, set the value
                $current[$key] = $value;
            } else {
                // Not the last key, make sure we have an array
                if (!isset($current[$key]) || !\is_array($current[$key])) {
                    $current[$key] = [];
                }
                $current = &$current[$key];
            }
        }

        return $array;
    }

    protected function varExport($var, $indent = ''): string
    {
        switch (\gettype($var)) {
            case 'string':
                return "'".addcslashes($var, "'\\\0..\37")."'";
            case 'array':
                $indexed = array_keys($var) === range(0, \count($var) - 1);
                $r = [];

                foreach ($var as $key => $value) {
                    $r[] = "{$indent}    "
                        .($indexed ? '' : $this->varExport($key).' => ')
                        .$this->varExport($value, "{$indent}    ");
                }

                return "[\n".implode(",\n", $r)."\n{$indent}]";
            case 'boolean':
                return $var ? 'true' : 'false';
            case 'NULL':
                return 'null';
            default:
                return (string) $var;
        }
    }

    protected function findHardcodedStrings(): int
    {
        $searchPath = $this->option('path') ?: 'resources/views';
        $excludePaths = $this->option('exclude') ? explode(',', $this->option('exclude')) : [];

        $this->info("Searching for hardcoded English strings in {$searchPath}...");

        $files = $this->getBladeFiles($searchPath, $excludePaths);
        $this->line("Found ".\count($files)." blade files to analyze.");

        $hardcodedStrings = [];
        $totalHardcoded = 0;

        $bar = $this->output->createProgressBar(\count($files));
        $bar->start();

        foreach ($files as $file) {
            $content = File::get($file);
            $relativePath = $this->getRelativePath($file);

            // Find hardcoded strings inside HTML tags that aren't using translations
            $hardcoded = $this->findHardcodedInBladeFile($content, $relativePath);

            if (!empty($hardcoded)) {
                $hardcodedStrings[$relativePath] = $hardcoded;
                $totalHardcoded += \count($hardcoded);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (empty($hardcodedStrings)) {
            $this->info("No hardcoded strings found!");

            return 0;
        }

        $this->info("Found {$totalHardcoded} potentially hardcoded strings in ".\count($hardcodedStrings)." files:");

        // Display findings
        foreach ($hardcodedStrings as $file => $strings) {
            $this->newLine();
            $this->line("<options=bold>{$file}</>");

            foreach ($strings as $string) {
                $this->line(" - Line {$string['line']}: \"{$string['text']}\"");
                $this->line("   Suggested key: \"{$string['suggested_key']}\"");
                $this->line("   Context: ".$string['context']);
            }
        }

        if ($this->confirm('Generate a report file with all hardcoded strings?')) {
            $reportPath = base_path('docs/hardcoded-strings-report.txt');
            $content = "Hardcoded strings found in blade templates:\n\n";

            foreach ($hardcodedStrings as $file => $strings) {
                $content .= "{$file}:\n";

                foreach ($strings as $string) {
                    $content .= "  - Line {$string['line']}: \"{$string['text']}\"\n";
                    $content .= "    Suggested key: \"{$string['suggested_key']}\"\n";
                    $content .= "    Context: {$string['context']}\n\n";
                }
            }

            File::put($reportPath, $content);
            $this->info("Report saved to {$reportPath}");
        }

        return 0;
    }

    protected function getBladeFiles(string $path, array $excludePaths = []): array
    {
        $basePath = base_path($path);

        if (!File::exists($basePath)) {
            return [];
        }

        $excludeFullPaths = array_map(fn ($p) => base_path($p), $excludePaths);

        return collect(File::allFiles($basePath))
            ->filter(function ($file) use ($excludeFullPaths) {
                $path = $file->getPathname();

                // Filter out non-blade files and excluded paths
                return $file->getExtension() === 'php' && str_contains($path, '.blade.') &&
                    !collect($excludeFullPaths)->contains(fn ($exclude) => str_starts_with($path, $exclude));
            })
            ->map(fn ($file) => $file->getPathname())
            ->toArray();
    }

    protected function getRelativePath(string $path): string
    {
        return str_replace(base_path().'/', '', $path);
    }

    protected function findHardcodedInBladeFile(string $content, string $filePath): array
    {
        $lines = explode("\n", $content);
        $result = [];
        $translatedTexts = $this->getTranslatedTexts();

        foreach ($lines as $lineNumber => $line) {
            // Skip comment lines and lines with translation functions
            if (preg_match('/^\s*{{--/', $line) ||
                preg_match('/@lang\(/', $line) ||
                preg_match('/\{\{\s*__\(/', $line) ||
                preg_match('/trans\(/', $line)) {
                continue;
            }

            // Process the line to temporarily mask all Blade syntax
            $processedLine = $line;

            // Mask blade control structures (@if, @foreach, etc)
            preg_match_all('/@[a-zA-Z]+(\s*\([^)]*\))?/', $processedLine, $controlMatches);

            foreach ($controlMatches[0] as $i => $match) {
                $processedLine = str_replace($match, "___BLADE_CONTROL_{$i}___", $processedLine);
            }

            // Mask {{ ... }} expressions
            preg_match_all('/\{\{.*?\}\}/', $processedLine, $bladeMatches);

            foreach ($bladeMatches[0] as $i => $match) {
                $processedLine = str_replace($match, "___BLADE_EXPR_{$i}___", $processedLine);
            }

            // Mask {!! ... !!} expressions
            preg_match_all('/\{!!.*?!!\}/', $processedLine, $rawMatches);

            foreach ($rawMatches[0] as $i => $match) {
                $processedLine = str_replace($match, "___RAW_EXPR_{$i}___", $processedLine);
            }

            // Mask @ directives and variables within PHP control structures
            if (preg_match('/@(if|foreach|forelse|while|for|unless)\s*\(.*\$[a-zA-Z0-9_]+.*\)/', $line)) {
                continue; // Skip lines with control structures containing variables
            }

            // Look for text between HTML tags in the processed line
            preg_match_all('/>([^<]+)</', $processedLine, $matches, PREG_OFFSET_CAPTURE);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $index => $match) {
                    $text = trim($match[0]);

                    // Skip if it's masked blade content, empty, numeric, very short
                    if (empty($text) ||
                        is_numeric($text) ||
                        \strlen($text) < 3 ||
                        str_contains($text, '___BLADE_') ||
                        str_contains($text, '___RAW_') ||
                        str_contains($text, '___DIRECTIVE_')) {
                        continue;
                    }

                    // Skip if this exact text is found in translations
                    if (\in_array($text, $translatedTexts)) {
                        continue;
                    }

                    // Generate a suggested translation key
                    $suggestedKey = $this->suggestTranslationKey($text, $filePath);

                    $result[] = [
                        'text'          => $text,
                        'line'          => $lineNumber + 1,
                        'context'       => $this->truncateContext($line, 100),
                        'suggested_key' => $suggestedKey
                    ];
                }
            }

            // Check for hardcoded strings in attribute values
            preg_match_all('/\s(\w+)="([^"{}]+)"/', $processedLine, $attrMatches);

            if (!empty($attrMatches[1])) {
                $count = \count($attrMatches[1]);

                for ($i = 0; $i < $count; $i++) {
                    $attr = $attrMatches[1][$i];
                    $value = $attrMatches[2][$i];

                    // Only check certain attributes that are likely to contain UI text
                    if (!\in_array($attr, ['title', 'placeholder', 'label', 'aria-label', 'alt'])) {
                        continue;
                    }

                    // Skip masked content, empty, numeric, very short
                    if (empty($value) ||
                        is_numeric($value) ||
                        \strlen($value) < 3 ||
                        str_contains($value, '___BLADE_') ||
                        str_contains($value, '___RAW_') ||
                        str_contains($value, '___DIRECTIVE_')) {
                        continue;
                    }

                    // Skip if already translated
                    if (\in_array($value, $translatedTexts)) {
                        continue;
                    }

                    // Generate a suggested translation key
                    $suggestedKey = $this->suggestTranslationKey($value, $filePath);

                    $result[] = [
                        'text'          => $value,
                        'line'          => $lineNumber + 1,
                        'context'       => $this->truncateContext($line, 100),
                        'suggested_key' => $suggestedKey
                    ];
                }
            }
        }

        return $result;
    }

    protected function replaceHardcodedStrings(): int
    {
        $searchPath = $this->option('path') ?: 'resources/views';
        $excludePaths = $this->option('exclude') ? explode(',', $this->option('exclude')) : [];

        $this->info("Finding hardcoded strings in {$searchPath}...");

        $files = $this->getBladeFiles($searchPath, $excludePaths);
        $this->line("Found ".\count($files)." blade files to analyze.");

        // First, collect all hardcoded strings
        $hardcodedStrings = [];
        $totalHardcoded = 0;
        $translationKeys = [];
        $baseFiles = $this->getTranslationFiles($this->baseLang);
        $baseKeys = $this->getAllKeys($this->baseLang, $baseFiles);

        $bar = $this->output->createProgressBar(\count($files));
        $bar->start();

        foreach ($files as $file) {
            $content = File::get($file);
            $relativePath = $this->getRelativePath($file);
            $hardcoded = $this->findHardcodedInBladeFile($content, $relativePath);

            if (!empty($hardcoded)) {
                $hardcodedStrings[$file] = $hardcoded;
                $totalHardcoded += \count($hardcoded);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (empty($hardcodedStrings)) {
            $this->info("No hardcoded strings found!");

            return 0;
        }

        $this->info("Found {$totalHardcoded} potentially hardcoded strings in ".\count($hardcodedStrings)." files.");

        if (!$this->confirm('Do you want to replace these hardcoded strings with translation keys?')) {
            $this->warn('Operation cancelled');

            return 0;
        }

        // Create a flat array of translation keys to add
        foreach ($hardcodedStrings as $file => $strings) {
            foreach ($strings as $string) {
                $key = $string['suggested_key'];
                $text = $string['text'];

                // Check if key already exists in base language
                if (isset($baseKeys[$key])) {
                    // If key exists but has different text, add a suffix to make it unique
                    if ($baseKeys[$key] !== $text) {
                        $suffix = 1;

                        while (isset($baseKeys["{$key}_{$suffix}"]) && $baseKeys["{$key}_{$suffix}"] !== $text) {
                            $suffix++;
                        }
                        $key = "{$key}_{$suffix}";
                    }
                }

                $translationKeys[$key] = $text;
            }
        }

        // Organize translation keys by file
        $fileKeys = [];

        foreach ($translationKeys as $key => $value) {
            [$file, $path] = explode('.', $key, 2);
            $fileKeys[$file][$path] = $value;
        }

        // Add new translations to base language
        foreach ($fileKeys as $file => $keys) {
            $filePath = base_path("lang/{$this->baseLang}/{$file}.php");

            // Get or create the translation file
            if (!File::exists($filePath)) {
                File::put($filePath, $this->getNewFileTemplate($file));
                $currentTranslations = [];
            } else {
                $currentTranslations = require $filePath;

                if (!\is_array($currentTranslations)) {
                    $this->warn("Warning: {$filePath} does not return an array");
                    $currentTranslations = [];
                }
            }

            // Add each key to the translations
            foreach ($keys as $path => $value) {
                // Ensure path is a string and handle numeric segments by prefixing with underscore
                $pathSegments = explode('.', (string) $path);

                // Rebuild the path with prefixed numeric segments
                $processedPath = implode('.', array_map(fn ($segment) => is_numeric($segment) ? "_{$segment}" : $segment, $pathSegments));

                $currentTranslations = $this->arraySetNested(
                    $currentTranslations,
                    $processedPath,
                    $value
                );
            }

            // Sort and save translations
            $currentTranslations = $this->sortArrayRecursively($currentTranslations);
            $fileContent = "<?php\n\ndeclare(strict_types=1);\n/**\n * NOTICE OF LICENSE.\n *\n * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0\n * The details is bundled with this project in the file LICENSE.txt.\n *\n * @project    UNIT3D Community Edition\n *\n * @author     HDVinnie <hdinnovations@protonmail.com>\n * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0\n */\n\nreturn ".$this->varExport($currentTranslations).";\n";
            File::put($filePath, $fileContent);
        }

        $this->info("Added ".\count($translationKeys)." new translation keys to base language files.");

        // Now replace hardcoded strings in blade files
        $replacedCount = 0;

        $bar = $this->output->createProgressBar(\count($hardcodedStrings));
        $bar->start();

        foreach ($hardcodedStrings as $file => $strings) {
            $content = File::get($file);
            $modified = false;

            foreach ($strings as $string) {
                $text = $string['text'];
                $key = $string['suggested_key'];

                // Update key if it was modified due to duplication
                if (isset($translationKeys[$key]) && $translationKeys[$key] === $text) {
                    // Find exact matches for the text within HTML tags or in attributes
                    $pattern = '/>(\s*)'.preg_quote($text, '/').'(\s*)</';
                    $replacement = '>${1}{{ __(\''.$key.'\') }}$2<';

                    $newContent = preg_replace($pattern, $replacement, $content);

                    // If HTML replacement didn't change anything, try attributes
                    if ($newContent === $content) {
                        // Check for attributes like title="Text" or placeholder="Text"
                        foreach (['title', 'placeholder', 'label', 'aria-label', 'alt'] as $attr) {
                            $attrPattern = '/\s'.$attr.'="'.preg_quote($text, '/').'"(?!\s*\{\{)/i';
                            $attrReplacement = ' '.$attr.'="{{ __(\''.addslashes($key).'\') }}"';
                            $newContent = preg_replace($attrPattern, $attrReplacement, $content);

                            if ($newContent !== $content) {
                                break;
                            }
                        }
                    }

                    if ($newContent !== $content) {
                        $content = $newContent;
                        $modified = true;
                        $replacedCount++;
                    }
                }
            }

            if ($modified) {
                File::put($file, $content);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Replaced {$replacedCount} hardcoded strings with translation keys.");

        return 0;
    }

    protected function reportHardcodedStrings(): int
    {
        $searchPath = $this->option('path') ?: 'resources/views';
        $excludePaths = $this->option('exclude') ? explode(',', $this->option('exclude')) : [];
        $markdownReport = $this->option('markdown') ?? false;

        $this->info("Analyzing Blade files for hardcoded strings in {$searchPath}...");

        $files = $this->getBladeFiles($searchPath, $excludePaths);
        $totalFiles = \count($files);
        $this->line("Found {$totalFiles} Blade files to analyze.");

        $hardcodedStrings = [];
        $totalHardcoded = 0;
        $hardcodedByCategory = [];

        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        foreach ($files as $file) {
            $content = File::get($file);
            $relativePath = $this->getRelativePath($file);

            // Find hardcoded strings
            $hardcoded = $this->findHardcodedInBladeFile($content, $relativePath);

            if (!empty($hardcoded)) {
                $hardcodedStrings[$relativePath] = $hardcoded;
                $totalHardcoded += \count($hardcoded);

                // Categorize hardcoded strings
                $category = explode('/', $relativePath)[1] ?? 'other';

                if (!isset($hardcodedByCategory[$category])) {
                    $hardcodedByCategory[$category] = [
                        'files'   => 0,
                        'strings' => 0
                    ];
                }
                $hardcodedByCategory[$category]['files']++;
                $hardcodedByCategory[$category]['strings'] += \count($hardcoded);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Sort categories by number of strings (descending)
        arsort($hardcodedByCategory);

        $filesWithHardcoded = \count($hardcodedStrings);
        $percentageOfFiles = $totalFiles ? round(($filesWithHardcoded / $totalFiles) * 100, 2) : 0;

        // Display summary statistics
        $this->info('Hardcoded Strings Analysis Report');
        $this->newLine();

        $this->line("<options=bold>Summary:</>");
        $this->line("• Total Blade files scanned: {$totalFiles}");
        $this->line("• Files with hardcoded strings: {$filesWithHardcoded} ({$percentageOfFiles}% of all files)");
        $this->line("• Total hardcoded strings found: {$totalHardcoded}");
        $this->line("• Average hardcoded strings per affected file: ".
            ($filesWithHardcoded ? round($totalHardcoded / $filesWithHardcoded, 2) : 0));

        $this->newLine();
        $this->line("<options=bold>Hardcoded strings by category:</>");

        $categoryTable = [];

        foreach ($hardcodedByCategory as $category => $stats) {
            $categoryTable[] = [
                $category,
                $stats['files'],
                $stats['strings'],
                round($stats['strings'] / $totalHardcoded * 100, 2).'%'
            ];
        }

        $this->table(
            ['Category', 'Files', 'Strings', '% of Total'],
            $categoryTable
        );

        // Show top files with most hardcoded strings
        $this->newLine();
        $this->line("<options=bold>Top 10 files with most hardcoded strings:</>");

        $topFiles = [];
        $fileCount = [];

        foreach ($hardcodedStrings as $file => $strings) {
            $fileCount[$file] = \count($strings);
        }

        // Sort by count (descending)
        arsort($fileCount);

        // Take top 10
        $i = 0;

        foreach ($fileCount as $file => $count) {
            if ($i >= 10) {
                break;
            }
            $topFiles[] = [$file, $count];
            $i++;
        }

        $this->table(['File', 'Hardcoded Strings'], $topFiles);

        // Ask if user wants detailed report
        if ($this->confirm('Do you want to see a detailed list of hardcoded strings?')) {
            foreach ($hardcodedStrings as $file => $strings) {
                $this->newLine();
                $this->line("<options=bold>{$file}</> ({$fileCount[$file]} strings)");

                foreach ($strings as $string) {
                    $this->line(" • Line {$string['line']}: \"{$string['text']}\"");
                    $this->line("   Suggested key: \"{$string['suggested_key']}\"");
                    $this->line("   Context: ".$this->truncateContext($string['context'], 80));
                }
            }
        }

        // Generate report file
        if ($this->confirm('Generate a detailed report file?')) {
            if ($markdownReport) {
                $reportPath = base_path('docs/hardcoded-strings-report.md');
                $content = "# Hardcoded Strings Analysis Report\n\n";

                $content .= "## Summary\n\n";
                $content .= "| Metric | Value |\n";
                $content .= "| --- | --- |\n";
                $content .= "| Total Blade files scanned | {$totalFiles} |\n";
                $content .= "| Files with hardcoded strings | {$filesWithHardcoded} ({$percentageOfFiles}% of all files) |\n";
                $content .= "| Total hardcoded strings found | {$totalHardcoded} |\n";
                $content .= "| Average hardcoded strings per affected file | ".
                    ($filesWithHardcoded ? round($totalHardcoded / $filesWithHardcoded, 2) : 0)." |\n\n";

                $content .= "## Hardcoded strings by category\n\n";
                $content .= "| Category | Files | Strings | % of Total |\n";
                $content .= "| --- | --- | --- | --- |\n";

                foreach ($hardcodedByCategory as $category => $stats) {
                    $content .= "| {$category} | {$stats['files']} | {$stats['strings']} | ".
                        round($stats['strings'] / $totalHardcoded * 100, 2)."% |\n";
                }

                $content .= "\n## Top 10 files with most hardcoded strings\n\n";
                $content .= "| File | Hardcoded Strings |\n";
                $content .= "| --- | --- |\n";

                $i = 0;

                foreach ($fileCount as $file => $count) {
                    if ($i >= 10) {
                        break;
                    }
                    $content .= "| `{$file}` | {$count} |\n";
                    $i++;
                }

                $content .= "\n## Detailed Analysis\n\n";

                foreach ($hardcodedStrings as $file => $strings) {
                    $content .= "### `{$file}` ({$fileCount[$file]} strings)\n\n";

                    foreach ($strings as $string) {
                        // Decode HTML entities for Markdown reports
                        $decodedContext = html_entity_decode($string['context'], ENT_QUOTES | ENT_HTML5);
                        $escapedText = str_replace('|', '\|', htmlspecialchars($string['text']));
                        $escapedContext = str_replace('|', '\|', htmlspecialchars($string['context']));

                        $content .= "- **Line {$string['line']}**: \"`{$escapedText}`\"\n";
                        $content .= "  - Suggested key: `{$string['suggested_key']}`\n";
                        $content .= "  - Context: `{$decodedContext}`\n\n";
                    }
                }
            } else {
                // Original text report format
                $reportPath = base_path('docs/hardcoded-strings-report.txt');
                $content = "HARDCODED STRINGS ANALYSIS REPORT\n";
                $content .= "===============================\n\n";

                $content .= "Summary:\n";
                $content .= "• Total Blade files scanned: {$totalFiles}\n";
                $content .= "• Files with hardcoded strings: {$filesWithHardcoded} ({$percentageOfFiles}% of all files)\n";
                $content .= "• Total hardcoded strings found: {$totalHardcoded}\n";
                $content .= "• Average hardcoded strings per affected file: ".
                    ($filesWithHardcoded ? round($totalHardcoded / $filesWithHardcoded, 2) : 0)."\n\n";

                $content .= "Hardcoded strings by category:\n";

                foreach ($hardcodedByCategory as $category => $stats) {
                    $content .= "• {$category}: {$stats['strings']} strings in {$stats['files']} files\n";
                }

                $content .= "\nDetailed Analysis:\n";
                $content .= "=================\n\n";

                foreach ($hardcodedStrings as $file => $strings) {
                    $content .= "{$file} ({$fileCount[$file]} strings):\n";

                    foreach ($strings as $string) {
                        $content .= "  • Line {$string['line']}: \"{$string['text']}\"\n";
                        $content .= "    Suggested key: \"{$string['suggested_key']}\"\n";
                        $content .= "    Context: {$string['context']}\n\n";
                    }
                }
            }

            File::put($reportPath, $content);
            $this->info("Detailed report saved to {$reportPath}");
        }

        return 0;
    }

    protected function getTranslatedTexts(): array
    {
        // Get all translated strings from English translations
        $baseFiles = $this->getTranslationFiles($this->baseLang);
        $baseKeys = $this->getAllKeys($this->baseLang, $baseFiles);

        // Return all values (the actual translated texts)
        return array_values($baseKeys);
    }

    protected function suggestTranslationKey(string $text, string $filePath): string
    {
        // Get a list of existing translation files
        $existingFiles = $this->getTranslationFiles($this->baseLang);

        // Convert text to a valid key name
        $key = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $text));
        $key = trim($key, '_');
        $key = substr($key, 0, 30); // Truncate very long keys

        // Map view directories to language files
        $directoryMappings = [
            'resources/views/user/'         => 'user',
            'resources/views/torrent/'      => 'torrent',
            'resources/views/forum/'        => 'forum',
            'resources/views/Staff/'        => 'staff',
            'resources/views/article/'      => 'article',
            'resources/views/page/'         => 'page',
            'resources/views/ticket/'       => 'ticket',
            'resources/views/playlist/'     => 'playlist',
            'resources/views/poll/'         => 'poll',
            'resources/views/request/'      => 'request',
            'resources/views/rss/'          => 'rss',
            'resources/views/stats/'        => 'stat',
            'resources/views/subtitle/'     => 'subtitle',
            'resources/views/notification/' => 'notification',
        ];

        // Default component
        $component = 'common';

        // Check if the file path matches any of our mappings
        foreach ($directoryMappings as $path => $mappedComponent) {
            if (str_starts_with($filePath, $path)) {
                $component = $mappedComponent;

                break;
            }
        }

        // If we didn't find a direct mapping, try to determine the component
        if ($component === 'common') {
            // Extract directory name
            $dirParts = explode('/', $filePath);

            if (\count($dirParts) > 2 && $dirParts[0] === 'resources' && $dirParts[1] === 'views') {
                $possibleComponent = $dirParts[2];

                // Check if a singular form of the directory exists as a language file
                $singular = rtrim($possibleComponent, 's');

                if (\in_array($singular, $existingFiles)) {
                    $component = $singular;
                }
                // Or check if the directory name itself exists as a language file
                elseif (\in_array($possibleComponent, $existingFiles)) {
                    $component = $possibleComponent;
                }
            }
        }

        return "{$component}.{$key}";
    }

    protected function truncateContext(string $line, int $length = 100): string
    {
        if (mb_strlen($line) <= $length) {
            return $line;
        }

        return mb_substr($line, 0, $length / 2).'...'.mb_substr($line, -$length / 2);
    }
}
