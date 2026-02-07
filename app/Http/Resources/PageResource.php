<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'filename' => $this['filename'],
            'markdown_content' => isset($this['markdown_content']) ? $this['markdown_content'] : null,
            'html_content' => isset($this['html_content']) ? $this['html_content'] : null,
            'tiptap_json' => isset($this['tiptap_json']) ? $this['tiptap_json'] : null,
            'last_modified' => $this->when(isset($this['last_modified']), $this['last_modified']),
            'links' => [
                'self' => route('api.pages.show', $this['filename']),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => [
                'api_version' => 'v1',
            ],
        ];
    }
}
