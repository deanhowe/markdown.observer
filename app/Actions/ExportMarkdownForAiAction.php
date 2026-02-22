<?php

namespace App\Actions;

class ExportMarkdownForAiAction
{
    /**
     * Export markdown optimized for LLM ingestion.
     * Goal: 80% token reduction by stripping cruft while keeping essential content.
     *
     * @param string $markdown
     * @return string
     */
    public function execute(string $markdown): string
    {
        // 1. Remove HTML comments
        $content = preg_replace('/<!--(.|\s)*?-->/', '', $markdown);

        // 2. Remove HTML tags but keep their content (if any)
        $content = strip_tags($content);

        // 3. Remove badges (common in READMEs, low signal for LLMs)
        // Match standard markdown images that look like badges: [![alt](image-url)](link)
        $content = preg_replace('/\[\!\[[^\]]*\]\([^\)]*\)\]\([^\)]*\)/', '', $content);
        // Match simple markdown images: ![alt](url)
        $content = preg_replace('/\!\[[^\]]*\]\([^\)]*\)/', '', $content);

        // 4. Compress multiple newlines into max two
        $content = preg_replace("/\n{3,}/", "\n\n", $content);

        // 5. Remove excessively long lines of dashes/equals/etc (dividers)
        $content = preg_replace('/^[\=\-\*\#\s]{5,}$/m', '', $content);

        // 6. Optional: Remove navigation-like patterns (e.g. [Back to top](#top))
        $content = preg_replace('/\[Back to [^\]]*\]\([^\)]*\)/i', '', $content);

        // 7. Remove empty links
        $content = preg_replace('/\[\s*\]\([^\)]*\)/', '', $content);

        // 8. Final trim
        return trim($content);
    }
}
