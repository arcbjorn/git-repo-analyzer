# Git Repository Analyzer

Deep analysis of git repositories with statistics, contributor insights, and code metrics.

## Features

- Commit history analysis (frequency, size, patterns)
- Contributor statistics and rankings
- Code churn metrics
- Language breakdown
- Branch analysis
- Hotspot detection (frequently changed files)
- Time-based activity heatmaps
- Technical debt estimation

## Usage

```bash
# Analyze repository
php artisan repo:analyze /path/to/repo

# Generate report
php artisan repo:report /path/to/repo --format=html

# Contributor stats
php artisan repo:contributors /path/to/repo --top=10

# Code churn
php artisan repo:churn /path/to/repo --since="6 months ago"
```

## Metrics

- Total commits, contributors, branches
- Lines of code by language
- Average commit size
- Busiest files and directories
- Commit patterns (time of day, day of week)
- Author contribution percentages

## Requirements

- PHP 7.2+
- Laravel 5.8
- Git binary
