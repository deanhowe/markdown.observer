import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import axios from 'axios';

interface Page {
    filename: string;
    content: string;
    lastModified: string;
}

// Fetch all pages
export function usePages() {
    return useQuery({
        queryKey: ['pages'],
        queryFn: async () => {
            const response = await axios.get('/api/pages');
            return response.data.pages as Page[];
        },
    });
}

// Fetch a single page by filename
export function usePage(filename: string) {
    return useQuery({
        queryKey: ['pages', filename],
        queryFn: async () => {
            const response = await axios.get(`/api/pages/${filename}`);
            return response.data.page as Page;
        },
        enabled: !!filename, // Only run the query if filename is provided
    });
}

// Create a new page
export function useCreatePage() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async ({ filename, content }: { filename: string; content: string }) => {
            const response = await axios.post('/api/pages', {
                filename,
                content,
            });
            return response.data.page as Page;
        },
        onMutate: async ({ filename, content }) => {
            // Cancel any outgoing refetches
            await queryClient.cancelQueries({ queryKey: ['pages'] });

            // Snapshot the previous value
            const previousPages = queryClient.getQueryData<Page[]>(['pages']);

            // Optimistically update the cache
            if (previousPages) {
                const newPage: Page = {
                    filename,
                    content,
                    lastModified: new Date().toISOString(),
                };

                queryClient.setQueryData<Page[]>(
                    ['pages'],
                    [...previousPages, newPage]
                );
            }

            // Return a context object with the snapshotted value
            return { previousPages };
        },
        onError: (err, variables, context) => {
            // If the mutation fails, use the context returned from onMutate to roll back
            if (context?.previousPages) {
                queryClient.setQueryData<Page[]>(['pages'], context.previousPages);
            }
        },
        onSettled: () => {
            // Always refetch after error or success to ensure cache is in sync with server
            queryClient.invalidateQueries({ queryKey: ['pages'] });
        },
    });
}

// Update a page
export function useUpdatePage() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async ({ filename, content }: { filename: string; content: string }) => {
            const response = await axios.put(`/api/pages/${filename}`, {
                content,
            });
            return response.data.page as Page;
        },
        onMutate: async ({ filename, content }) => {
            // Cancel any outgoing refetches
            await queryClient.cancelQueries({ queryKey: ['pages', filename] });
            await queryClient.cancelQueries({ queryKey: ['pages'] });

            // Snapshot the previous value
            const previousPage = queryClient.getQueryData<Page>(['pages', filename]);
            const previousPages = queryClient.getQueryData<Page[]>(['pages']);

            // Optimistically update the cache
            if (previousPage) {
                queryClient.setQueryData<Page>(['pages', filename], {
                    ...previousPage,
                    content,
                    lastModified: new Date().toISOString(),
                });
            }

            if (previousPages) {
                queryClient.setQueryData<Page[]>(['pages'],
                    previousPages.map(page =>
                        page.filename === filename
                            ? { ...page, content, lastModified: new Date().toISOString() }
                            : page
                    )
                );
            }

            // Return a context object with the snapshotted value
            return { previousPage, previousPages };
        },
        onError: (err, variables, context) => {
            // If the mutation fails, use the context returned from onMutate to roll back
            if (context?.previousPage) {
                queryClient.setQueryData<Page>(['pages', variables.filename], context.previousPage);
            }
            if (context?.previousPages) {
                queryClient.setQueryData<Page[]>(['pages'], context.previousPages);
            }
        },
        onSettled: (_, __, variables) => {
            // Always refetch after error or success to ensure cache is in sync with server
            queryClient.invalidateQueries({ queryKey: ['pages', variables.filename] });
            queryClient.invalidateQueries({ queryKey: ['pages'] });
        },
    });
}

// Delete a page
export function useDeletePage() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (filename: string) => {
            await axios.delete(`/api/pages/${filename}`);
            return filename;
        },
        onMutate: async (filename) => {
            // Cancel any outgoing refetches
            await queryClient.cancelQueries({ queryKey: ['pages', filename] });
            await queryClient.cancelQueries({ queryKey: ['pages'] });

            // Snapshot the previous value
            const previousPage = queryClient.getQueryData<Page>(['pages', filename]);
            const previousPages = queryClient.getQueryData<Page[]>(['pages']);

            // Optimistically update the cache
            if (previousPages) {
                queryClient.setQueryData<Page[]>(
                    ['pages'],
                    previousPages.filter(page => page.filename !== filename)
                );
            }

            // Remove the specific page from the cache
            queryClient.removeQueries({ queryKey: ['pages', filename] });

            // Return a context object with the snapshotted value
            return { previousPage, previousPages };
        },
        onError: (err, filename, context) => {
            // If the mutation fails, use the context returned from onMutate to roll back
            if (context?.previousPage) {
                queryClient.setQueryData<Page>(['pages', filename], context.previousPage);
            }
            if (context?.previousPages) {
                queryClient.setQueryData<Page[]>(['pages'], context.previousPages);
            }
        },
        onSettled: () => {
            // Always refetch after error or success to ensure cache is in sync with server
            queryClient.invalidateQueries({ queryKey: ['pages'] });
        },
    });
}
