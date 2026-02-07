import React, { useEffect, useState, useCallback, KeyboardEvent } from 'react';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import axios from 'axios';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Loader2 } from 'lucide-react';

interface MarkdownEditorProps {
    content: string;
    onChange: (content: string) => void;
    placeholder?: string;
}

export function MarkdownEditor({ content, onChange, placeholder = 'Write your markdown here...' }: MarkdownEditorProps) {
    const [markdownContent, setMarkdownContent] = useState<string>(content);
    const [htmlPreview, setHtmlPreview] = useState<string>('');
    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [activeTab, setActiveTab] = useState<string>('edit');

    // Handle keyboard shortcuts
    const handleKeyDown = useCallback((e: KeyboardEvent<HTMLTextAreaElement>) => {
        // Ctrl+Shift+P to toggle preview
        if (e.ctrlKey && e.shiftKey && e.key === 'P') {
            e.preventDefault();
            setActiveTab(activeTab === 'edit' ? 'preview' : 'edit');
        }

        // Ctrl+B for bold
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            const textarea = e.currentTarget;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = markdownContent.substring(start, end);

            const newContent =
                markdownContent.substring(0, start) +
                '**' + selectedText + '**' +
                markdownContent.substring(end);

            setMarkdownContent(newContent);
            onChange(newContent);

            // Set cursor position after formatting
            setTimeout(() => {
                textarea.focus();
                textarea.setSelectionRange(start + 2, end + 2);
            }, 0);
        }

        // Ctrl+I for italic
        if (e.ctrlKey && e.key === 'i') {
            e.preventDefault();
            const textarea = e.currentTarget;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = markdownContent.substring(start, end);

            const newContent =
                markdownContent.substring(0, start) +
                '*' + selectedText + '*' +
                markdownContent.substring(end);

            setMarkdownContent(newContent);
            onChange(newContent);

            // Set cursor position after formatting
            setTimeout(() => {
                textarea.focus();
                textarea.setSelectionRange(start + 1, end + 1);
            }, 0);
        }
    }, [activeTab, markdownContent, onChange]);

    // Update local state when content prop changes
    useEffect(() => {
        setMarkdownContent(content);
    }, [content]);

    // Handle textarea change
    const handleChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
        const newContent = e.target.value;
        setMarkdownContent(newContent);
        onChange(newContent);
    };

    // Convert markdown to HTML for preview
    const generatePreview = async () => {
        if (!markdownContent.trim()) {
            setHtmlPreview('');
            return;
        }

        setIsLoading(true);
        try {
            const response = await axios.post('/api/markdown/to-html', {
                markdown: markdownContent
            });
            setHtmlPreview(response.data.html);
        } catch (error) {
            console.error('Error converting markdown to HTML:', error);
        } finally {
            setIsLoading(false);
        }
    };

    // Generate preview when switching to preview tab
    useEffect(() => {
        if (activeTab === 'preview') {
            generatePreview();
        }
    }, [activeTab, markdownContent]);

    return (
        <div className="border border-sidebar-border rounded-lg overflow-hidden">
            <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
                <div className="bg-white dark:bg-neutral-900 p-2 border-b border-sidebar-border flex items-center justify-between">
                    <TabsList>
                        <TabsTrigger value="edit">Edit</TabsTrigger>
                        <TabsTrigger value="preview">Preview</TabsTrigger>
                    </TabsList>

                    <Sheet>
                        <SheetTrigger asChild>
                            <Button variant="outline" size="sm">
                                Markdown Help
                            </Button>
                        </SheetTrigger>
                        <SheetContent>
                            <SheetHeader>
                                <SheetTitle>Markdown Syntax Guide</SheetTitle>
                                <SheetDescription>
                                    Learn how to format your text using Markdown
                                </SheetDescription>
                            </SheetHeader>
                            <div className="mt-4 space-y-4">
                                <div>
                                    <h3 className="text-sm font-medium">Headers</h3>
                                    <pre className="mt-1 p-2 bg-neutral-100 dark:bg-neutral-800 rounded text-xs">
                                        # Heading 1<br/>
                                        ## Heading 2<br/>
                                        ### Heading 3
                                    </pre>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium">Emphasis</h3>
                                    <pre className="mt-1 p-2 bg-neutral-100 dark:bg-neutral-800 rounded text-xs">
                                        *italic*<br/>
                                        **bold**<br/>
                                        ***bold and italic***
                                    </pre>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium">Lists</h3>
                                    <pre className="mt-1 p-2 bg-neutral-100 dark:bg-neutral-800 rounded text-xs">
                                        - Item 1<br/>
                                        - Item 2<br/>
                                        <br/>
                                        1. Item 1<br/>
                                        2. Item 2
                                    </pre>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium">Links and Images</h3>
                                    <pre className="mt-1 p-2 bg-neutral-100 dark:bg-neutral-800 rounded text-xs">
                                        [Link text](https://example.com)<br/>
                                        ![Alt text](image-url.jpg)
                                    </pre>
                                </div>
                                <div>
                                    <h3 className="text-sm font-medium">Code</h3>
                                    <pre className="mt-1 p-2 bg-neutral-100 dark:bg-neutral-800 rounded text-xs">
                                        `inline code`<br/>
                                        <br/>
                                        ```<br/>
                                        code block<br/>
                                        ```
                                    </pre>
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <TabsContent value="edit" className="p-0 border-none">
                    <Textarea
                        value={markdownContent}
                        onChange={handleChange}
                        onKeyDown={handleKeyDown}
                        placeholder={placeholder}
                        aria-label="Markdown editor"
                        aria-describedby="markdown-editor-description"
                        className="min-h-[400px] p-4 border-none rounded-none font-mono text-sm resize-none focus-visible:ring-0"
                    />
                    <div className="sr-only" id="markdown-editor-description">
                        Use Ctrl+B for bold, Ctrl+I for italic, and Ctrl+Shift+P to toggle preview mode.
                    </div>
                </TabsContent>

                <TabsContent value="preview" className="p-0 border-none">
                    {isLoading ? (
                        <div className="flex items-center justify-center min-h-[400px]" aria-live="polite" aria-busy="true">
                            <Loader2 className="h-8 w-8 animate-spin text-neutral-400" aria-hidden="true" />
                            <span className="sr-only">Loading preview...</span>
                        </div>
                    ) : (
                        <div
                            className="prose dark:prose-invert max-w-none p-4 min-h-[400px]"
                            dangerouslySetInnerHTML={{ __html: htmlPreview }}
                            aria-live="polite"
                            aria-label="Markdown preview"
                            role="region"
                        />
                    )}
                </TabsContent>
            </Tabs>
        </div>
    );
}
