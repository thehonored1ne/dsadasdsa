<?php

namespace App\Services;

class TFIDFService
{
    private array $stopwords = [
        'and', 'or', 'the', 'a', 'an', 'in', 'on', 'at', 'to', 'for', 'of', 'with',
        'by', 'about', 'as', 'into', 'like', 'through', 'after', 'over', 'between',
        'out', 'against', 'during', 'without', 'before', 'under', 'around', 'among',
        'introduction', 'basics', 'advanced', 'part', 'i', 'ii', 'iii', '1', '2', '3',
    ];

    public function calculateMatchScore(string $expertiseAreas, string $subjectName): float
    {
        if (empty(trim($expertiseAreas)) || empty(trim($subjectName))) {
            return 0.0;
        }

        $subjectTokens = $this->tokenize($subjectName);
        if (empty($subjectTokens)) {
            return 0.0;
        }

        // Split expertise areas by pipe
        $areas = array_map('trim', explode('|', strtolower($expertiseAreas)));

        $highestScore = 0.0;

        foreach ($areas as $area) {
            if (empty($area)) {
                continue;
            }

            $areaTokens = $this->tokenize($area);
            if (empty($areaTokens)) {
                continue;
            }

            $score = $this->compareTokens($areaTokens, $subjectTokens);
            if ($score > $highestScore) {
                $highestScore = $score;
            }
        }

        return round($highestScore, 2);
    }

    private function tokenize(string $text): array
    {
        // Convert to lowercase and remove punctuation
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);

        // Split by whitespace or dash
        $tokens = preg_split('/[\s-]+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Remove stopwords
        return array_values(array_filter($tokens, function ($token) {
            return ! in_array($token, $this->stopwords) && strlen($token) > 1;
        }));
    }

    private function compareTokens(array $expertiseTokens, array $subjectTokens): float
    {
        $matchCount = 0;
        $totalWeight = count($subjectTokens);

        if ($totalWeight === 0) {
            return 0.0;
        }

        foreach ($subjectTokens as $sToken) {
            $bestMatchForToken = 0.0;

            foreach ($expertiseTokens as $eToken) {
                if ($sToken === $eToken) {
                    $bestMatchForToken = 1.0;
                    break;
                }

                // Partial match (e.g., "program" matches "programming")
                if (str_starts_with($sToken, $eToken) || str_starts_with($eToken, $sToken)) {
                    $bestMatchForToken = max($bestMatchForToken, 0.8);
                }

                // Levenshtein similarity for typos
                $lev = levenshtein($sToken, $eToken);
                $maxLength = max(strlen($sToken), strlen($eToken));
                $similarity = 1 - ($lev / $maxLength);

                if ($similarity >= 0.75) {
                    $bestMatchForToken = max($bestMatchForToken, $similarity);
                }
            }

            $matchCount += $bestMatchForToken;
        }

        return $matchCount / $totalWeight;
    }
}
