import React from 'react';
import { Editor } from '@tiptap/react';
import { Toggle } from '@/components/ui/toggle';
import { ToggleGroup } from '@/components/ui/toggle-group';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import {
    Bold,
    Italic,
    Underline as UnderlineIcon,
    Heading1,
    Heading2,
    Heading3,
    List,
    ListOrdered,
    Code,
    Minus,
    Undo,
    Redo,
} from 'lucide-react';
import { LinkInput } from './link-input';
import { ImageInput } from './image-input';

interface EditorToolbarProps {
    editor: Editor;
}

export function EditorToolbar({
    editor
}: EditorToolbarProps) {
    return (
        <div className="bg-white dark:bg-neutral-900 p-2 border-b border-sidebar-border flex flex-wrap gap-1 items-center">
            <ToggleGroup type="multiple" className="flex flex-wrap gap-1">
                <Toggle
                    size="sm"
                    pressed={editor.isActive('bold')}
                    onClick={() => editor.chain().focus().toggleBold().run()}
                    aria-label="Bold"
                >
                    <Bold className="h-4 w-4" />
                </Toggle>
                <Toggle
                    size="sm"
                    pressed={editor.isActive('italic')}
                    onClick={() => editor.chain().focus().toggleItalic().run()}
                    aria-label="Italic"
                >
                    <Italic className="h-4 w-4" />
                </Toggle>
                <Toggle
                    size="sm"
                    pressed={editor.isActive('underline')}
                    onClick={() => editor.chain().focus().toggleUnderline().run()}
                    aria-label="Underline"
                >
                    <UnderlineIcon className="h-4 w-4" />
                </Toggle>
            </ToggleGroup>

            <Separator orientation="vertical" className="h-6" />

            <ToggleGroup type="single" className="flex flex-wrap gap-1">
                <Toggle
                    size="sm"
                    pressed={editor.isActive('heading', { level: 1 })}
                    onClick={() => editor.chain().focus().toggleHeading({ level: 1 }).run()}
                    aria-label="Heading 1"
                >
                    <Heading1 className="h-4 w-4" />
                </Toggle>
                <Toggle
                    size="sm"
                    pressed={editor.isActive('heading', { level: 2 })}
                    onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()}
                    aria-label="Heading 2"
                >
                    <Heading2 className="h-4 w-4" />
                </Toggle>
                <Toggle
                    size="sm"
                    pressed={editor.isActive('heading', { level: 3 })}
                    onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()}
                    aria-label="Heading 3"
                >
                    <Heading3 className="h-4 w-4" />
                </Toggle>
            </ToggleGroup>

            <Separator orientation="vertical" className="h-6" />

            <ToggleGroup type="multiple" className="flex flex-wrap gap-1">
                <Toggle
                    size="sm"
                    pressed={editor.isActive('bulletList')}
                    onClick={() => editor.chain().focus().toggleBulletList().run()}
                    aria-label="Bullet List"
                >
                    <List className="h-4 w-4" />
                </Toggle>
                <Toggle
                    size="sm"
                    pressed={editor.isActive('orderedList')}
                    onClick={() => editor.chain().focus().toggleOrderedList().run()}
                    aria-label="Ordered List"
                >
                    <ListOrdered className="h-4 w-4" />
                </Toggle>
            </ToggleGroup>

            <Separator orientation="vertical" className="h-6" />

            <ToggleGroup type="multiple" className="flex flex-wrap gap-1">
                <Toggle
                    size="sm"
                    pressed={editor.isActive('code')}
                    onClick={() => editor.chain().focus().toggleCode().run()}
                    aria-label="Code"
                >
                    <Code className="h-4 w-4" />
                </Toggle>
                <Toggle
                    size="sm"
                    onClick={() => editor.chain().focus().setHorizontalRule().run()}
                    aria-label="Horizontal Rule"
                >
                    <Minus className="h-4 w-4" />
                </Toggle>
            </ToggleGroup>

            <Separator orientation="vertical" className="h-6" />

            <div className="flex flex-wrap gap-1">
                <LinkInput editor={editor} />
                <ImageInput editor={editor} />
            </div>

            <div className="ml-auto flex gap-1">
                <Button
                    variant="outline"
                    size="sm"
                    onClick={() => editor.chain().focus().undo().run()}
                    disabled={!editor.can().undo()}
                    className="h-8 px-2"
                >
                    <Undo className="h-4 w-4" />
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    onClick={() => editor.chain().focus().redo().run()}
                    disabled={!editor.can().redo()}
                    className="h-8 px-2"
                >
                    <Redo className="h-4 w-4" />
                </Button>
            </div>
        </div>
    );
}
