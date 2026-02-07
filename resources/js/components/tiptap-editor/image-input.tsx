import React, { useState } from 'react';
import { Editor } from '@tiptap/react';
import { Button } from '@/components/ui/button';
import { Image as ImageIcon } from 'lucide-react';

interface ImageInputProps {
    editor: Editor;
}

export function ImageInput({ editor }: ImageInputProps) {
    const [showImageInput, setShowImageInput] = useState<boolean>(false);
    const [imageUrl, setImageUrl] = useState<string>('');

    const addImage = () => {
        if (imageUrl) {
            editor.chain().focus().setImage({ src: imageUrl }).run();
            setImageUrl('');
            setShowImageInput(false);
        }
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addImage();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            setShowImageInput(false);
        }
    };

    if (!showImageInput) {
        return (
            <Button
                variant="outline"
                size="sm"
                onClick={() => setShowImageInput(true)}
                className="h-8 px-2"
                aria-label="Add image"
                title="Add image"
            >
                <ImageIcon className="h-4 w-4" aria-hidden="true" />
            </Button>
        );
    }

    return (
        <div className="flex items-center gap-1" role="dialog" aria-label="Insert image">
            <input
                type="url"
                value={imageUrl}
                onChange={(e) => setImageUrl(e.target.value)}
                placeholder="https://example.com/image.jpg"
                className="h-8 px-2 text-sm border border-sidebar-border rounded"
                onKeyDown={handleKeyDown}
                aria-label="Image URL"
                autoFocus
            />
            <Button
                variant="outline"
                size="sm"
                onClick={addImage}
                className="h-8 px-2"
                aria-label="Add image"
            >
                Add
            </Button>
            <Button
                variant="outline"
                size="sm"
                onClick={() => setShowImageInput(false)}
                className="h-8 px-2"
                aria-label="Cancel adding image"
            >
                Cancel
            </Button>
        </div>
    );
}
