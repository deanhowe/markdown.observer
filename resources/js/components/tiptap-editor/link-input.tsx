import React, { useState } from 'react';
import { Editor } from '@tiptap/react';
import { Button } from '@/components/ui/button';
import { Link as LinkIcon } from 'lucide-react';

interface LinkInputProps {
    editor: Editor;
}

export function LinkInput({ editor }: LinkInputProps) {
    const [showLinkInput, setShowLinkInput] = useState<boolean>(false);
    const [linkUrl, setLinkUrl] = useState<string>('');

    const addLink = () => {
        if (linkUrl) {
            editor
                .chain()
                .focus()
                .extendMarkRange('link')
                .setLink({ href: linkUrl })
                .run();
            setLinkUrl('');
            setShowLinkInput(false);
        }
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addLink();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            setShowLinkInput(false);
        }
    };

    if (!showLinkInput) {
        return (
            <Button
                variant="outline"
                size="sm"
                onClick={() => setShowLinkInput(true)}
                className="h-8 px-2"
                aria-label="Add link"
                title="Add link"
            >
                <LinkIcon className="h-4 w-4" aria-hidden="true" />
            </Button>
        );
    }

    return (
        <div className="flex items-center gap-1" role="dialog" aria-label="Insert link">
            <input
                type="url"
                value={linkUrl}
                onChange={(e) => setLinkUrl(e.target.value)}
                placeholder="https://example.com"
                className="h-8 px-2 text-sm border border-sidebar-border rounded"
                onKeyDown={handleKeyDown}
                aria-label="Link URL"
                autoFocus
            />
            <Button
                variant="outline"
                size="sm"
                onClick={addLink}
                className="h-8 px-2"
                aria-label="Add link"
            >
                Add
            </Button>
            <Button
                variant="outline"
                size="sm"
                onClick={() => setShowLinkInput(false)}
                className="h-8 px-2"
                aria-label="Cancel adding link"
            >
                Cancel
            </Button>
        </div>
    );
}
