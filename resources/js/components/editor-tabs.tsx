import React, { useState, useEffect } from 'react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { TiptapEditor } from '@/components/tiptap-editor';
import { MarkdownEditor } from '@/components/markdown-editor';
import { type ContentFormat, type ConversionStatus, type WorkflowStage } from '@/components/workflow-status';
import axios from 'axios';

interface EditorTabsProps {
    content: string;
    onChange: (content: string) => void;
    placeholder?: string;
    initialWorkflowStage?: WorkflowStage;
    onContentFormatChange?: (format: ContentFormat) => void;
    onConversionStatusChange?: (status: ConversionStatus) => void;
    onErrorMessageChange?: (message: string | undefined) => void;
}

export function EditorTabs({
    content,
    onChange,
    placeholder,
    initialWorkflowStage = 'loading',
    onContentFormatChange,
    onConversionStatusChange,
    onErrorMessageChange
}: EditorTabsProps) {
    const [activeTab, setActiveTab] = useState<string>('tiptap');
    const [markdownContent, setMarkdownContent] = useState<string>(content);
    const [htmlContent, setHtmlContent] = useState<string>('');
    const [currentFilename, setCurrentFilename] = useState<string>('');

    // Workflow status state
    const [contentFormat, setContentFormat] = useState<ContentFormat>('markdown');
    const [conversionStatus, setConversionStatus] = useState<ConversionStatus>('idle');
    const [workflowStage, setWorkflowStage] = useState<WorkflowStage>(initialWorkflowStage);
    const [errorMessage, setErrorMessage] = useState<string | undefined>(undefined);

    // Update parent component when state changes
    useEffect(() => {
        onContentFormatChange?.(contentFormat);
    }, [contentFormat, onContentFormatChange]);

    useEffect(() => {
        onConversionStatusChange?.(conversionStatus);
    }, [conversionStatus, onConversionStatusChange]);

    useEffect(() => {
        onErrorMessageChange?.(errorMessage);
    }, [errorMessage, onErrorMessageChange]);

    // Notify parent component when workflow stage changes
    useEffect(() => {
        // This is now handled by the parent component directly
        // but we keep the state for internal use
        console.log('Workflow stage changed:', workflowStage);
    }, [workflowStage]);

    // Extract filename from content or URL if possible
    useEffect(() => {
        // Try to determine the current filename from the URL or other sources
        const pathname = window.location.pathname;
        const matches = pathname.match(/\/pages\/([^/]+)$/);
        if (matches && matches[1]) {
            setCurrentFilename(matches[1]);
        }
    }, []);

    // Update content when props change
    useEffect(() => {
        setMarkdownContent(content);
        setWorkflowStage('loading');

        // If we have a filename, try to get the HTML content from localStorage
        if (currentFilename) {
            const storedHtml = localStorage.getItem(`page_html_${currentFilename}`);
            if (storedHtml) {
                setHtmlContent(storedHtml);
                setWorkflowStage('editing');
                // Set content format based on active tab
                setContentFormat(activeTab === 'tiptap' ? 'tiptap' : 'markdown');
            } else {
                // If no HTML in localStorage, convert markdown to HTML
                setConversionStatus('converting');
                convertMarkdownToHtml(content).then(html => {
                    if (html) {
                        setHtmlContent(html);
                        setConversionStatus('success');
                    } else {
                        setConversionStatus('error');
                        setErrorMessage('Failed to convert Markdown to HTML');
                    }
                    setWorkflowStage('editing');
                    // Set content format based on active tab
                    setContentFormat(activeTab === 'tiptap' ? 'tiptap' : 'markdown');
                }).catch(error => {
                    console.error('Error converting Markdown to HTML:', error);
                    setConversionStatus('error');
                    setErrorMessage('Failed to convert Markdown to HTML');
                    setWorkflowStage('editing');
                });
            }
        } else {
            setWorkflowStage('editing');
            // Set content format based on active tab
            setContentFormat(activeTab === 'tiptap' ? 'tiptap' : 'markdown');
        }
    }, [content, currentFilename, activeTab]);

    // Convert HTML to Markdown when switching from tiptap to raw
    const convertHtmlToMarkdown = async (html: string | undefined) => {
        if (!html || !html.trim()) return '';

        setConversionStatus('converting');
        setErrorMessage(undefined);

        try {
            const response = await axios.post('/api/markdown/to-markdown', {
                html
            });
            setConversionStatus('success');
            return response.data.markdown;
        } catch (error) {
            console.error('Error converting HTML to Markdown:', error);
            setConversionStatus('error');
            setErrorMessage('Failed to convert HTML to Markdown');
            return '';
        }
    };

    // Convert Markdown to HTML when switching from raw to tiptap
    const convertMarkdownToHtml = async (markdown: string) => {
        if (!markdown.trim()) return '';

        setConversionStatus('converting');
        setErrorMessage(undefined);

        try {
            const response = await axios.post('/api/markdown/to-html', {
                markdown
            });
            setConversionStatus('success');
            return response.data.html;
        } catch (error) {
            console.error('Error converting Markdown to HTML:', error);
            setConversionStatus('error');
            setErrorMessage('Failed to convert Markdown to HTML');
            return '';
        }
    };

    // Handle tab change
    const handleTabChange = async (value: string) => {
        if (value === activeTab) return;

        // Set workflow stage to indicate we're processing
        setWorkflowStage('loading');

        if (value === 'raw' && activeTab === 'tiptap') {
            // Convert HTML to Markdown when switching from tiptap to raw
            setContentFormat('markdown');
            const markdown = await convertHtmlToMarkdown(htmlContent || content);
            setMarkdownContent(markdown);
            onChange(markdown);
        } else if (value === 'tiptap' && activeTab === 'raw') {
            // Convert Markdown to HTML when switching from raw to tiptap
            setContentFormat('tiptap');
            const html = await convertMarkdownToHtml(markdownContent);
            setHtmlContent(html);
        } else if (value === 'preview') {
            // For preview, we're just showing the HTML content
            setContentFormat('html');
            setConversionStatus('idle');
        }

        setActiveTab(value);
        setWorkflowStage('editing');
    };

    // Handle content change from tiptap editor
    const handleTiptapChange = (html: string) => {
        setHtmlContent(html);
        onChange(html);
        setWorkflowStage('editing');
    };

    // Handle content change from markdown editor
    const handleMarkdownChange = (markdown: string) => {
        setMarkdownContent(markdown);
        onChange(markdown);
        setWorkflowStage('editing');
    };

    return (
        <Tabs value={activeTab} onValueChange={handleTabChange} className="w-full">
            <div className="bg-white dark:bg-neutral-900 p-2 border-b border-sidebar-border flex justify-between items-center">
                <TabsList>
                    <TabsTrigger value="tiptap">Rich Editor</TabsTrigger>
                    <TabsTrigger value="raw">Raw Markdown</TabsTrigger>
                    <TabsTrigger value="preview">Preview</TabsTrigger>
                </TabsList>

                {/* WorkflowStatus component moved to Dashboard */}
            </div>

            <TabsContent value="tiptap" className="p-0 border-none">
                <TiptapEditor
                    content={activeTab === 'tiptap' ? (htmlContent || content) : content}
                    onChange={handleTiptapChange}
                    placeholder={placeholder}
                    isMarkdown={true}
                />
            </TabsContent>

            <TabsContent value="raw" className="p-0 border-none">
                <MarkdownEditor
                    content={markdownContent}
                    onChange={handleMarkdownChange}
                    placeholder={placeholder}
                />
            </TabsContent>

            <TabsContent value="preview" className="p-0 border-none">
                <div className="border border-sidebar-border rounded-lg overflow-hidden">
                    <div className="prose dark:prose-invert max-w-none p-4 min-h-[400px]">
                        {activeTab === 'preview' && (
                            <div dangerouslySetInnerHTML={{ __html: htmlContent || content }} />
                        )}
                    </div>
                </div>
            </TabsContent>
        </Tabs>
    );
}
