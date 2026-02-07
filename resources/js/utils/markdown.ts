import axios from 'axios';

// Simple debounce function to prevent repeated API calls
function debounce<T extends (...args: unknown[]) => Promise<unknown>>(
    func: T,
    wait: number
): (...args: Parameters<T>) => Promise<ReturnType<T>> {
    let timeout: NodeJS.Timeout | null = null;
    let lastResult: unknown = null;
    let lastArgs: Parameters<T> | null = null;

    return async (...args: Parameters<T>): Promise<ReturnType<T>> => {
        // If the args are the same as the last call, return the cached result
        if (
            lastArgs &&
            lastArgs.length === args.length &&
            lastArgs.every((arg, i) => JSON.stringify(arg) === JSON.stringify(args[i]))
        ) {
            return lastResult as ReturnType<T>;
        }

        // Store the args for comparison in the next call
        lastArgs = args;

        // Clear any existing timeout
        if (timeout) {
            clearTimeout(timeout);
        }

        // Return a promise that resolves when the debounced function is called
        return new Promise((resolve) => {
            timeout = setTimeout(async () => {
                lastResult = await func(...args);
                resolve(lastResult as ReturnType<T>);
            }, wait);
        });
    };
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
