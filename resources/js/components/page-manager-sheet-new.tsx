import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { toast } from '@/components/ui/use-toast';
import { Trash, Save, FileText, Plus, RefreshCw } from 'lucide-react';

interface Page {
    filename: string;
    last_modified: number;
}

interface PageManagerSheetProps {
    onPageSelect: (content: string, filename: string) => void;
    currentContent: string;
    currentFilename: string;
}

export function PageManagerSheet({ onPageSelect, currentContent, currentFilename }: PageManagerSheetProps) {
    const [pages, setPages] = useState<Page[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [newFilename, setNewFilename] = useState<string>('');
    const [isCreateDialogOpen, setIsCreateDialogOpen] = useState<boolean>(false);
    const [isSaving, setIsSaving] = useState<boolean>(false);
    const [isDeleting, setIsDeleting] = useState<boolean>(false);
    const [isSheetOpen, setIsSheetOpen] = useState<boolean>(false);

    // Fetch all pages
    const fetchPages = async () => {
        setLoading(true);
        try {
            const response = await axios.get('/api/pages');
            setPages(response.data.data);
        } catch (error) {
            console.error('Error fetching pages:', error);
            toast({
                title: 'Error',
                description: 'Failed to load pages',
                variant: 'destructive',
            });
        } finally {
            setLoading(false);
        }
    };

    // Load pages on component mount
    useEffect(() => {
        fetchPages();

        // Add event listener for opening the sheet from the sidebar
        const handleOpenPageManager = () => {
            setIsSheetOpen(true);
        };

        window.addEventListener('open-page-manager', handleOpenPageManager);

        // Clean up event listener
        return () => {
            window.removeEventListener('open-page-manager', handleOpenPageManager);
        };
    }, []);

    // Create a new page
    const createPage = async () => {
        if (!newFilename.trim()) {
            toast({
                title: 'Error',
                description: 'Filename cannot be empty',
                variant: 'destructive',
            });
            return;
        }

        setIsSaving(true);
        try {
            const response = await axios.post('/api/pages', {
                filename: newFilename,
                content: '# New Page\n\nStart writing your content here...',
            });

            toast({
                title: 'Success',
                description: 'Page created successfully',
            });

            setNewFilename('');
            setIsCreateDialogOpen(false);
            fetchPages();

            // Select the newly created page
            onPageSelect('# New Page\n\nStart writing your content here...', response.data.filename);
            setIsSheetOpen(false);
        } catch (error) {
            console.error('Error creating page:', error);
            toast({
                title: 'Error',
                description: 'Failed to create page',
                variant: 'destructive',
            });
        } finally {
            setIsSaving(false);
        }
    };

    // Save the current page
    const savePage = async () => {
        if (!currentFilename) {
            toast({
                title: 'Error',
                description: 'No file selected',
                variant: 'destructive',
            });
            return;
        }

        setIsSaving(true);
        try {
            await axios.put(`/api/pages/${currentFilename}`, {
                content: currentContent,
            });

            toast({
                title: 'Success',
                description: 'Page saved successfully',
            });
        } catch (error) {
            console.error('Error saving page:', error);
            toast({
                title: 'Error',
                description: 'Failed to save page',
                variant: 'destructive',
            });
        } finally {
            setIsSaving(false);
        }
    };

    // Delete a page
    const deletePage = async () => {
        if (!currentFilename) {
            toast({
                title: 'Error',
                description: 'No file selected',
                variant: 'destructive',
            });
            return;
        }

        if (!confirm(`Are you sure you want to delete "${currentFilename}"?`)) {
            return;
        }

        setIsDeleting(true);
        try {
            await axios.delete(`/api/pages/${currentFilename}`);

            toast({
                title: 'Success',
                description: 'Page deleted successfully',
            });

            // Clear the current page
            onPageSelect('', '');
            fetchPages();
            setIsSheetOpen(false);
        } catch (error) {
            console.error('Error deleting page:', error);
            toast({
                title: 'Error',
                description: 'Failed to delete page',
                variant: 'destructive',
            });
        } finally {
            setIsDeleting(false);
        }
    };

    // Load a page
    const loadPage = async (filename: string) => {
        try {
            const response = await axios.get(`/api/pages/${filename}`);
            onPageSelect(response.data.markdown_content, response.data.filename);
            setIsSheetOpen(false);
        } catch (error) {
            console.error('Error loading page:', error);
            toast({
                title: 'Error',
                description: 'Failed to load page',
                variant: 'destructive',
            });
        }
    };

    return (
        <div className="mb-4 flex justify-end">
            <Sheet open={isSheetOpen} onOpenChange={setIsSheetOpen}>
                <SheetContent side="right">
                    <SheetHeader>
                        <SheetTitle>Pages</SheetTitle>
                    </SheetHeader>
                    <div className="flex flex-col gap-4 mt-4">
                        <div className="flex items-center justify-between">
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={fetchPages}
                                disabled={loading}
                            >
                                <RefreshCw className="h-4 w-4 mr-1" />
                                Refresh
                            </Button>
                            <Dialog open={isCreateDialogOpen} onOpenChange={setIsCreateDialogOpen}>
                                <DialogTrigger asChild>
                                    <Button variant="outline" size="sm">
                                        <Plus className="h-4 w-4 mr-1" />
                                        New Page
                                    </Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Create New Page</DialogTitle>
                                    </DialogHeader>
                                    <div className="flex flex-col gap-4">
                                        <Input
                                            placeholder="Enter filename (without extension)"
                                            value={newFilename}
                                            onChange={(e) => setNewFilename(e.target.value)}
                                        />
                                        <Button
                                            onClick={createPage}
                                            disabled={isSaving || !newFilename.trim()}
                                        >
                                            {isSaving ? 'Creating...' : 'Create Page'}
                                        </Button>
                                    </div>
                                </DialogContent>
                            </Dialog>
                        </div>

                        <div className="flex flex-col gap-2 max-h-[calc(100vh-200px)] overflow-y-auto">
                            {loading ? (
                                <div className="text-center py-4">Loading pages...</div>
                            ) : pages.length === 0 ? (
                                <div className="text-center py-4">No pages found. Create your first page!</div>
                            ) : (
                                pages.map((page) => (
                                    <div
                                        key={page.filename}
                                        className={`flex items-center justify-between p-2 rounded-md cursor-pointer hover:bg-neutral-100 dark:hover:bg-neutral-800 ${
                                            currentFilename === page.filename ? 'bg-neutral-200 dark:bg-neutral-700' : ''
                                        }`}
                                        onClick={() => loadPage(page.filename)}
                                    >
                                        <div className="flex items-center">
                                            <FileText className="h-4 w-4 mr-2" />
                                            <span>{page.filename}</span>
                                        </div>
                                        <div className="text-xs text-neutral-500">
                                            {new Date(page.last_modified * 1000).toLocaleDateString()}
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                    </div>

                    {currentFilename && (
                        <div className="flex items-center justify-between mt-6 pt-4 border-t border-sidebar-border">
                            <div className="text-sm">
                                Current: <span className="font-medium">{currentFilename}</span>
                            </div>
                            <div className="flex gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={savePage}
                                    disabled={isSaving || !currentContent}
                                >
                                    <Save className="h-4 w-4 mr-1" />
                                    {isSaving ? 'Saving...' : 'Save'}
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    onClick={deletePage}
                                    disabled={isDeleting}
                                >
                                    <Trash className="h-4 w-4 mr-1" />
                                    {isDeleting ? 'Deleting...' : 'Delete'}
                                </Button>
                            </div>
                        </div>
                    )}
                </SheetContent>
            </Sheet>

            {currentFilename && (
                <div className="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={savePage}
                        disabled={isSaving || !currentContent}
                    >
                        <Save className="h-4 w-4 mr-1" />
                        {isSaving ? 'Saving...' : 'Save'}
                    </Button>
                    <Button
                        variant="destructive"
                        size="sm"
                        onClick={deletePage}
                        disabled={isDeleting}
                    >
                        <Trash className="h-4 w-4 mr-1" />
                        {isDeleting ? 'Deleting...' : 'Delete'}
                    </Button>
                </div>
            )}
        </div>
    );
}
