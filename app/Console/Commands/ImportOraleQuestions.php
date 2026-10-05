<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Test;
use App\Models\TestQuestion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportOraleQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:orale {test_id} {file_path?} {--dry-run : Parse only, do not insert} {--revert : Delete all questions for this test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Orale questions from a structured text file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testId = $this->argument('test_id');
        $filePath = $this->argument('file_path');
        $dryRun = $this->option('dry-run');
        $revert = $this->option('revert');

        $test = Test::find($testId);
        if (!$test) {
            $this->error("Test with ID {$testId} not found.");
            return 1;
        }

        // --- REVERT MODE ---
        if ($revert) {
            if ($dryRun) {
                $count = $test->questions()->count();
                $this->info("[DRY RUN] Would delete {$count} questions from Test {$testId}.");
                return 0;
            }

            if (!$this->confirm("Are you sure you want to DELETE ALL questions for Test ID {$testId} ({$test->title})?")) {
                $this->info("Operation cancelled.");
                return 0;
            }

            $count = $test->questions()->count();
            // Assuming model events or database cascade handles options deletion.
            // If not, we might need to iterate and delete.
            // Safe bet: iterate
            $test->questions()->each(function ($q) {
                $q->options()->delete(); // Delete options first just in case
                $q->delete();
            });

            $test->update(['total_questions' => 0]);

            $this->info("Deleted {$count} questions.");
            return 0;
        }

        // --- IMPORT MODE ---
        if (!$filePath || !file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $content = file_get_contents($filePath);
        // Normalize newlines
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // Split by separator
        $blocks = explode('----------------------------------------', $content);

        $questions = [];
        $baseDir = dirname($filePath);

        $this->info("Parsing file...");

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block))
                continue;

            $question = $this->parseBlock($block);
            if ($question) {
                $questions[] = $question;
            }
        }

        $this->info("Found " . count($questions) . " questions.");

        if ($dryRun) {
            $this->table(
                ['Num', 'Type', 'Question', 'Correct', 'Image', 'Audio'],
                array_map(function ($q) {
                    return [
                        $q['number'],
                        $q['type'],
                        Str::limit($q['text'], 30),
                        $q['correct_option'],
                        $q['image_url'] ?? 'N/A',
                        $q['audio_url'] ?? 'N/A',
                    ];
                }, $questions)
            );
            $this->info("Dry run complete. No changes made.");
            return 0;
        }

        $this->info("Starting import to Test ID: {$testId} (" . $test->title . ")...");

        $bar = $this->output->createProgressBar(count($questions));
        $bar->start();

        $currentOrder = $test->questions()->max('order') ?? 0;

        foreach ($questions as $q) {
            $currentOrder++;

            // Process Media (Local first, then remote)
            $mediaData = null;

            if ($q['type'] === 'image_audio_mcq') {
                $imagePath = $this->processMedia($q['local_image'], $q['image_url'], 'image', $baseDir);
                $audioPath = $this->processMedia($q['local_audio'], $q['audio_url'], 'audio', $baseDir);

                if ($imagePath && $audioPath) {
                    $mediaData = json_encode([
                        'image' => $imagePath,
                        'audio' => $audioPath
                    ]);
                } else {
                    $this->error("\nFailed to process media for Q{$q['number']}");
                    // continue; // Optional: skip question or proceed with partial? User said "without any error".
                    // Let's create it anyway but log error, or skip. 
                    // Better to fail safe for now or placeholder?
                    // Let's retry download if local failed? processMedia does that.
                }
            } elseif ($q['type'] === 'audio') {
                $audioPath = $this->processMedia($q['local_audio'], $q['audio_url'], 'audio', $baseDir);
                if ($audioPath) {
                    $mediaData = $audioPath;
                } else {
                    $this->error("\nFailed to process audio for Q{$q['number']}");
                }
            }

            // Create Question
            // Map Option Letter to Index (A=0, B=1...)
            $correctIndex = ord(strtoupper($q['correct_option'])) - ord('A');
            // Safety check
            if ($correctIndex < 0 || $correctIndex > 3)
                $correctIndex = 0;

            $questionRow = $test->questions()->create([
                'type' => $q['type'],
                'question_text' => $q['text'],
                'passage' => null,
                'question_media' => $mediaData,
                'correct_answer' => [$correctIndex], // Array of indices
                'explanation' => null,
                'points' => 1,
                'order' => $currentOrder
            ]);

            // Create Options
            foreach ($q['options'] as $idx => $optText) {
                $questionRow->options()->create([
                    'option_text' => $optText,
                    'is_correct' => ($idx === $correctIndex),
                    'order' => $idx + 1
                ]);
            }

            $bar->advance();
        }

        $bar->finish();

        // Update total questions
        $test->update(['total_questions' => $test->questions()->count()]);

        $this->newLine();
        $this->info("Import complete!");
    }

    private function parseBlock($block)
    {
        $lines = explode("\n", $block);
        $data = [
            'number' => '',
            'image_url' => 'N/A',
            'audio_url' => 'N/A',
            'local_image' => 'N/A',
            'local_audio' => 'N/A',
            'text' => '',
            'options' => [],
            'correct_option' => ''
        ];

        $parsingOptions = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line))
                continue;

            if (str_starts_with($line, 'Question Number:')) {
                $data['number'] = trim(substr($line, 16));
            } elseif (str_starts_with($line, 'Image URL:')) {
                $data['image_url'] = trim(substr($line, 10));
            } elseif (str_starts_with($line, 'Audio URL:')) {
                $data['audio_url'] = trim(substr($line, 10));
                $data['audio_url'] = explode('?', $data['audio_url'])[0];
            } elseif (str_starts_with($line, 'Local Image:')) {
                $data['local_image'] = trim(substr($line, 12));
            } elseif (str_starts_with($line, 'Local Audio:')) {
                $data['local_audio'] = trim(substr($line, 12));
            } elseif (str_starts_with($line, 'Question:')) {
                $data['text'] = trim(substr($line, 9));
            } elseif (str_starts_with($line, 'Options:')) {
                $parsingOptions = true;
            } elseif (str_starts_with($line, 'Correct option :')) {
                $parsingOptions = false;
                $data['correct_option'] = trim(substr($line, 16));
            } elseif ($parsingOptions) {
                if (preg_match('/^[A-D]\)\s*(.*)/', $line, $matches)) {
                    $data['options'][] = trim($matches[1]);
                }
            }
        }

        // Determine Type
        if ($data['image_url'] !== 'N/A' && !empty($data['image_url'])) {
            $data['type'] = 'image_audio_mcq';
        } else {
            $data['type'] = 'audio';
        }

        $data['text'] = preg_replace('/https?:\/\/\S+/', '', $data['text']);
        $data['text'] = trim($data['text']);

        return $data;
    }

    private function processMedia($localRelPath, $remoteUrl, $type, $baseDir)
    {
        // 1. Try Local File
        if ($localRelPath !== 'N/A' && !empty($localRelPath)) {
            $fullLocalPath = $baseDir . '/' . $localRelPath;
            if (file_exists($fullLocalPath)) {
                $filename = uniqid() . '_' . basename($fullLocalPath);
                $storagePath = "uploads/{$type}/{$filename}";

                // Copy to public storage
                try {
                    Storage::disk('public')->put($storagePath, file_get_contents($fullLocalPath));
                    $this->line("   [✓] Used local file: {$localRelPath}");
                    return "/storage/{$storagePath}";
                } catch (\Exception $e) {
                    $this->warn("Failed to copy local file: {$fullLocalPath}. Error: " . $e->getMessage());
                    // Fallthrough to remote
                }
            } else {
                $this->warn("   [!] Local file not found: {$fullLocalPath}. Trying remote...");
            }
        }

        // 2. Fallback to Remote URL
        $this->warn("   [!] Using remote URL for Q... (Fallback)");
        return $this->downloadFile($remoteUrl, $type);
    }

    private function downloadFile($url, $type)
    {
        if ($url === 'N/A' || empty($url))
            return null;

        try {
            $contents = Http::timeout(30)->get($url)->body();
            $name = basename($url);
            $name = preg_replace('/[^a-zA-Z0-9\._-]/', '', $name);
            $filename = uniqid() . '_' . $name;

            $path = "uploads/{$type}/{$filename}";

            Storage::disk('public')->put($path, $contents);

            return "/storage/{$path}";
        } catch (\Exception $e) {
            $this->error("Download failed ($url): " . $e->getMessage());
            return null;
        }
    }
}
