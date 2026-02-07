<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageRevision extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filename',
        'markdown_content',
        'html_content',
        'tiptap_json',
        'revision_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tiptap_json' => 'array',
    ];

    /**
     * Get the latest revision for a file
     *
     * @param string $filename
     * @return self|null
     */
    public static function getLatestRevision(string $filename): ?self
    {
        return self::where('filename', $filename)
            ->latest()
            ->first();
    }

    /**
     * Create a new revision for a file
     *
     * @param string $filename
     * @param string $markdownContent
     * @param string $htmlContent
     * @param array|null $tiptapJson
     * @param string $revisionType
     * @return self
     */
    public static function createRevision(
        string $filename,
        string $markdownContent,
        string $htmlContent,
        ?array $tiptapJson = null,
        string $revisionType = 'update'
    ): self {
        return self::create([
            'filename' => $filename,
            'markdown_content' => $markdownContent,
            'html_content' => $htmlContent,
            'tiptap_json' => $tiptapJson,
            'revision_type' => $revisionType,
        ]);
    }
}
