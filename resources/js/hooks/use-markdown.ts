import { useMutation } from '@tanstack/react-query';
import { markdownToHtml as markdownToHtmlUtil, htmlToMarkdown as htmlToMarkdownUtil } from '@/utils/markdown';

// Convert Markdown to HTML
export function useMarkdownToHtml() {
    return useMutation({
        mutationFn: async (markdown: string) => {
            return await markdownToHtmlUtil(markdown);
        },
    });
}

// Convert HTML to Markdown
export function useHtmlToMarkdown() {
    return useMutation({
        mutationFn: async (html: string) => {
            return await htmlToMarkdownUtil(html);
        },
    });
}
