import React, { useEffect, useState } from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import TextStyle from '@tiptap/extension-text-style';
import Color from '@tiptap/extension-color';
import Heading from '@tiptap/extension-heading';
import HorizontalRule from '@tiptap/extension-horizontal-rule';
import Typography from '@tiptap/extension-typography';
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight';
import { common, createLowlight } from 'lowlight';
import { markdownToHtml } from '@/utils/markdown';
import { ErrorDisplay } from './tiptap-editor/error-display';
import { EditorToolbar } from './tiptap-editor/editor-toolbar';

const lowlight = createLowlight(common);

interface TiptapEditorProps {
    content?: string;
    onChange?: (html: string) => void;
    placeholder?: string;
    isMarkdown?: boolean;
}

export function TiptapEditor({ content = '', onChange, placeholder = 'Write something...', isMarkdown = true }: TiptapEditorProps) {
    const [conversionError, setConversionError] = useState<string | undefined>(undefined);
    const [isConverting, setIsConverting] = useState<boolean>(false);

    const editor = useEditor({
        extensions: [
            StarterKit,
            Underline,
            Link.configure({
                openOnClick: false,
                HTMLAttributes: {
                    class: 'text-blue-500 underline',
                },
            }),
            Image,
            Placeholder.configure({
                placeholder,
            }),
            TextStyle,
            Color,
            Heading.configure({
                levels: [1, 2, 3],
            }),
            HorizontalRule,
            Typography,
            CodeBlockLowlight.configure({
                lowlight,
            }),
        ],
        content,
        onUpdate: ({ editor }) => {
            const html = editor.getHTML();
            onChange?.(html);
        },
    });

    useEffect(() => {
        const updateContent = async () => {
            if (!editor || !content || isConverting) return;

            // Only update if the content from props doesn't match editor content
            const currentContent = editor.getHTML();

            if (isMarkdown) {
                try {
                    setIsConverting(true);
                    setConversionError(undefined);

                    // Convert markdown to HTML using server-side API
                    const html = await markdownToHtml(content);

                    if (html !== currentContent) {
                        editor.commands.setContent(html);
                    }
                } catch (error) {
                    console.error('Error converting markdown to HTML:', error);
                    setConversionError('Failed to convert markdown to HTML. Please check your syntax.');

                    // Use the raw content as fallback
                    if (content !== currentContent) {
                        editor.commands.setContent(content);
                    }
                } finally {
                    setIsConverting(false);
                }
            } else {
                // In non-markdown mode, set the content directly
                setConversionError(undefined);

                if (content !== currentContent) {
                    editor.commands.setContent(content);
                }
            }
        };

        updateContent();
    }, [content, editor, isMarkdown, isConverting]);


    if (!editor) {
        return null;
    }

    return (
        <div className="border border-sidebar-border rounded-lg overflow-hidden">
            <ErrorDisplay
                error={conversionError}
                onDismiss={() => setConversionError(undefined)}
            />

            <EditorToolbar
                editor={editor}
            />

            <div aria-describedby="tiptap-editor-description">
                <EditorContent
                    editor={editor}
                    className="prose dark:prose-invert max-w-none p-4 min-h-[200px] focus:outline-none"
                    aria-label="Rich text editor"
                    role="textbox"
                    aria-multiline="true"
                />
                <div className="sr-only" id="tiptap-editor-description">
                    Rich text editor with formatting options. Use toolbar buttons for formatting or keyboard shortcuts:
                    Ctrl+B for bold, Ctrl+I for italic, Ctrl+Shift+1 for heading 1,
                    Ctrl+Shift+2 for heading 2, Ctrl+Shift+3 for heading 3.
                </div>
            </div>
        </div>
    );
}
