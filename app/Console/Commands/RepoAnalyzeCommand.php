<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\RepoAnalyzer;

class RepoAnalyzeCommand extends Command
{
    protected $signature = 'repo:analyze {path}';
    protected $description = 'Analyze git repository';

    public function handle()
    {
        $path = $this->argument('path');

        try {
            $analyzer = new RepoAnalyzer($path);
            $stats = $analyzer->analyze();

            $this->info("Repository Analysis");
            $this->line("==================");
            $this->line("Total Commits: " . $stats['commits']['total']);
            $this->line("First Commit: " . $stats['commits']['first']);
            $this->line("Last Commit: " . $stats['commits']['last']);
            $this->line("Branches: " . count($stats['branches']));

            $this->line("\nTop Contributors:");
            foreach (array_slice($stats['contributors'], 0, 5) as $contributor) {
                $this->line("  {$contributor['name']}: {$contributor['commits']} commits");
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
