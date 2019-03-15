<?php

namespace App;

use Gitonomy\Git\Repository;

class RepoAnalyzer
{
    protected $repo;
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
        $this->repo = new Repository($path);
    }

    public function analyze()
    {
        return [
            'commits' => $this->getCommitStats(),
            'contributors' => $this->getContributors(),
            'branches' => $this->getBranches(),
            'activity' => $this->getActivity()
        ];
    }

    protected function getCommitStats()
    {
        $log = $this->repo->getLog();
        $commits = iterator_to_array($log->getCommits());

        return [
            'total' => count($commits),
            'first' => count($commits) > 0 ? $commits[array_key_last($commits)]->getAuthorDate()->format('Y-m-d') : null,
            'last' => count($commits) > 0 ? $commits[0]->getAuthorDate()->format('Y-m-d') : null
        ];
    }

    protected function getContributors()
    {
        $contributors = [];
        $log = $this->repo->getLog();

        foreach ($log->getCommits() as $commit) {
            $author = $commit->getAuthorName();
            if (!isset($contributors[$author])) {
                $contributors[$author] = [
                    'name' => $author,
                    'email' => $commit->getAuthorEmail(),
                    'commits' => 0
                ];
            }
            $contributors[$author]['commits']++;
        }

        usort($contributors, function($a, $b) {
            return $b['commits'] - $a['commits'];
        });

        return $contributors;
    }

    protected function getBranches()
    {
        $branches = [];
        $refs = $this->repo->getReferences();

        foreach ($refs->getBranches() as $branch) {
            $branches[] = $branch->getName();
        }

        return $branches;
    }

    protected function getActivity()
    {
        $activity = [];
        $log = $this->repo->getLog();

        foreach ($log->getCommits() as $commit) {
            $date = $commit->getAuthorDate()->format('Y-m-d');
            if (!isset($activity[$date])) {
                $activity[$date] = 0;
            }
            $activity[$date]++;
        }

        return $activity;
    }

    public function getChurn($since = '6 months ago')
    {
        $log = $this->repo->getLog();
        $churn = [];

        foreach ($log->getCommits() as $commit) {
            if ($commit->getAuthorDate()->getTimestamp() < strtotime($since)) {
                continue;
            }

            $diff = $commit->getDiff();
            foreach ($diff->getFiles() as $file) {
                $name = $file->getName();
                if (!isset($churn[$name])) {
                    $churn[$name] = 0;
                }
                $churn[$name]++;
            }
        }

        arsort($churn);
        return array_slice($churn, 0, 20, true);
    }

    public function toJson()
    {
        return json_encode($this->analyze(), JSON_PRETTY_PRINT);
    }
}
