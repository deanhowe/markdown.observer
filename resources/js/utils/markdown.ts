import axios from 'axios';

// Simple debounce function to prevent repeated API calls
function debounce<Args extends unknown[], Result>(
    func: (...args: Args) => Promise<Result>,
    wait: number
): (...args: Args) => Promise<Result> {
    let timeout: ReturnType<typeof setTimeout> | undefined;
    let pending: Array<{ resolve: (value: Result) => void; reject: (reason?: unknown) => void }> = [];
    let latestArgs: Args;

    return (...args: Args) => new Promise<Result>((resolve, reject) => {
        latestArgs = args;
        pending.push({ resolve, reject });

        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(async () => {
            const waiting = pending;
            pending = [];

            try {
                const result = await func(...latestArgs);
                waiting.forEach(({ resolve }) => resolve(result));
            } catch (error) {
                waiting.forEach(({ reject }) => reject(error));
            }
        }, wait);
    });
}

// The original functions
async function _markdownToHtml(markdown: string): Promise<string> {
    try {
        const response = await axios.post('/api/markdown/to-html', { markdown });
        return response.data.html;
    } catch (error) {
        console.error('Error converting Markdown to HTML:', error);
        throw error;
    }
}

async function _htmlToMarkdown(html: string): Promise<string> {
    try {
        const response = await axios.post('/api/markdown/to-markdown', { html });
        return response.data.markdown;
    } catch (error) {
        console.error('Error converting HTML to Markdown:', error);
        throw error;
    }
}

// Debounced versions of the functions
export const markdownToHtml = debounce(_markdownToHtml, 300);
export const htmlToMarkdown = debounce(_htmlToMarkdown, 300);
