<?php

namespace MiPressCz\Core\Support;

use MiPressCz\Core\Models\Revision;

class RevisionDiffer
{
    /**
     * Compare two revisions and return an array of changed fields.
     *
     * @return array<string, array{label: string, old: mixed, new: mixed, diff_html?: string}>
     */
    public static function compare(?Revision $old, Revision $new): array
    {
        if ($old === null) {
            return [];
        }

        $changes = [];

        // Title
        if ($old->title !== $new->title) {
            $changes['title'] = [
                'label' => __('content.entry_fields.title'),
                'old' => $old->title,
                'new' => $new->title,
                'diff_html' => self::diffWords((string) ($old->title ?? ''), (string) ($new->title ?? '')),
            ];
        }

        // Status
        if ($old->status !== $new->status) {
            $changes['status'] = [
                'label' => __('content.entry_fields.status'),
                'old' => $old->status,
                'new' => $new->status,
            ];
        }

        // Data fields (blueprint custom fields)
        $oldData = is_array($old->data) ? $old->data : [];
        $newData = is_array($new->data) ? $new->data : [];
        $allKeys = array_unique(array_merge(array_keys($oldData), array_keys($newData)));

        foreach ($allKeys as $key) {
            $oldVal = $oldData[$key] ?? null;
            $newVal = $newData[$key] ?? null;

            if ($oldVal !== $newVal) {
                $entry = [
                    'label' => $key,
                    'old' => $oldVal,
                    'new' => $newVal,
                ];

                if (is_string($oldVal) && is_string($newVal) && strlen($oldVal) + strlen($newVal) <= 2000) {
                    $entry['diff_html'] = self::diffWords($oldVal, $newVal);
                }

                $changes['data.'.$key] = $entry;
            }
        }

        return $changes;
    }

    /**
     * Generate an inline HTML diff between two strings, marking
     * removed words with <del> and inserted words with <ins>.
     */
    public static function diffWords(string $old, string $new): string
    {
        if ($old === $new) {
            return htmlspecialchars($new);
        }

        $oldTokens = self::tokenize($old);
        $newTokens = self::tokenize($new);

        // Safety: skip word diff for very long token sequences
        if (count($oldTokens) + count($newTokens) > 2000) {
            return '<del class="bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">'
                .htmlspecialchars($old)
                .'</del> <ins class="bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 no-underline">'
                .htmlspecialchars($new)
                .'</ins>';
        }

        $dp = self::computeLcs($oldTokens, $newTokens);

        return self::buildDiffHtml($oldTokens, $newTokens, $dp);
    }

    /** @return string[] */
    private static function tokenize(string $text): array
    {
        preg_match_all('/\S+|\s+/', $text, $matches);

        return $matches[0];
    }

    /**
     * @param  string[]  $a
     * @param  string[]  $b
     * @return int[][]
     */
    private static function computeLcs(array $a, array $b): array
    {
        $m = count($a);
        $n = count($b);
        $dp = array_fill(0, $m + 1, array_fill(0, $n + 1, 0));

        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                $dp[$i][$j] = $a[$i - 1] === $b[$j - 1]
                    ? $dp[$i - 1][$j - 1] + 1
                    : max($dp[$i - 1][$j], $dp[$i][$j - 1]);
            }
        }

        return $dp;
    }

    /**
     * @param  string[]  $a
     * @param  string[]  $b
     * @param  int[][]  $dp
     */
    private static function buildDiffHtml(array $a, array $b, array $dp): string
    {
        $ops = [];
        $i = count($a);
        $j = count($b);

        while ($i > 0 || $j > 0) {
            if ($i > 0 && $j > 0 && $a[$i - 1] === $b[$j - 1]) {
                $ops[] = ['equal', $a[$i - 1]];
                $i--;
                $j--;
            } elseif ($j > 0 && ($i === 0 || $dp[$i][$j - 1] >= $dp[$i - 1][$j])) {
                $ops[] = ['insert', $b[$j - 1]];
                $j--;
            } else {
                $ops[] = ['delete', $a[$i - 1]];
                $i--;
            }
        }

        $ops = array_reverse($ops);
        $html = '';

        foreach ($ops as [$op, $token]) {
            $escaped = htmlspecialchars($token);
            $html .= match ($op) {
                'equal' => $escaped,
                'insert' => '<ins class="bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 no-underline">'.$escaped.'</ins>',
                'delete' => '<del class="bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">'.$escaped.'</del>',
            };
        }

        return $html;
    }
}
