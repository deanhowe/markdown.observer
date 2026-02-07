import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { toast } from '@/components/ui/use-toast';
import { Trash, Save, FileText, Plus, RefreshCw, Database } from 'lucide-react';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface Page {
    filename: string;
    last_modified: number;
}

interface StorageDisk {
    name: string;
    label: string;
}

interface PageManagerSheetProps {
    onPageSelect: (content: string, filename: string) => void;
    currentContent: string;
    currentFilename: string;
    onSheetOpen?: () => void;
    onWorkflowStageChange?: (stage: 'loading' | 'editing' | 'saving' | 'saved') => void;
}

export function PageManagerSheet({ onPageSelect, currentContent, currentFilename, onSheetOpen, onWorkflowStageChange }: PageManagerSheetProps) {
    const [pages, setPages] = useState<Page[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [newFilename, setNewFilename] = useState<string>('');
    const [isCreateDialogOpen, setIsCreateDialogOpen] = useState<boolean>(false);
    const [isSaving, setIsSaving] = useState<boolean>(false);
    const [isDeleting, setIsDeleting] = useState<boolean>(false);
    const [isSheetOpen, setIsSheetOpen] = useState<boolean>(false);
    const [selectedDisk, setSelectedDisk] = useState<string>('pages');
    const [availableDisks, setAvailableDisks] = useState<StorageDisk[]>([
        { name: 'pages', label: 'Pages (Default)' },
    ]);

    // Fetch available disks
    const fetchDisks = async () => {
        try {
            const response = await axios.get('/api/pages/disks/list');
            setAvailableDisks(response.data.data);
        } catch (error) {
            console.error('Error fetching disks:', error);
            toast({
                title: 'Error',
                description: 'Failed to load available disks',
                variant: 'destructive',
            });
        }
    };

    // Fetch all pages
    const fetchPages = async () => {
        setLoading(true);
        try {
            const response = await axios.get(`/api/pages?disk=${selectedDisk}`);
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

    // Load pages when selected disk changes
    useEffect(() => {
        fetchPages();
    }, [selectedDisk]);

    // Load disks and set up event listener on component mount
    useEffect(() => {
        fetchDisks();

        // Add event listener for opening the sheet from the sidebar
        const handleOpenPageManager = () => {
            setIsSheetOpen(true);
            // Notify parent component that sheet has been opened
            onSheetOpen?.();
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
                disk: selectedDisk,
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
        // Notify parent component that we're saving
        onWorkflowStageChange?.('saving');

        try {
            await axios.put(`/api/pages/${currentFilename}`, {
                content: currentContent,
                disk: selectedDisk,
            });

            toast({
                title: 'Success',
                description: 'Page saved successfully',
            });

            // Notify parent component that save was successful
            onWorkflowStageChange?.('saved');

            // After a short delay, set back to editing
            setTimeout(() => {
                onWorkflowStageChange?.('editing');
            }, 2000);
        } catch (error) {
            console.error('Error saving page:', error);
            toast({
                title: 'Error',
                description: 'Failed to save page',
                variant: 'destructive',
            });

            // Notify parent component that we're back to editing
            onWorkflowStageChange?.('editing');
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
        // Notify parent component that we're saving (deleting is a type of saving)
        onWorkflowStageChange?.('saving');

        try {
            await axios.delete(`/api/pages/${currentFilename}?disk=${selectedDisk}`);

            toast({
                title: 'Success',
                description: 'Page deleted successfully',
            });

            // Clear the current page
            onPageSelect('', '');
            fetchPages();
            setIsSheetOpen(false);

            // Notify parent component that we're back to loading (since we cleared the page)
            onWorkflowStageChange?.('loading');
        } catch (error) {
            console.error('Error deleting page:', error);
            toast({
                title: 'Error',
                description: 'Failed to delete page',
                variant: 'destructive',
            });

            // Notify parent component that we're back to editing
            onWorkflowStageChange?.('editing');
        } finally {
            setIsDeleting(false);
        }
    };

    // Load a page
    const loadPage = async (filename: string) => {
        try {
            const response = await axios.get(`/api/pages/${filename}?disk=${selectedDisk}`);
            const data = response.data.data || response.data;

            // Check if we have the required data
            if (!data.markdown_content || !data.html_content) {
                console.error('Missing required data in API response:', data);
                toast({
                    title: 'Error',
                    description: 'Incomplete page data received from server',
                    variant: 'destructive',
                });
                return;
            }

            // Store the HTML content in localStorage for the editor to use
            localStorage.setItem(`page_html_${filename}`, data.html_content);

            // If we have tiptap_json, store it as well
            if (data.tiptap_json) {
                localStorage.setItem(`page_tiptap_${filename}`, JSON.stringify(data.tiptap_json));
            }

            onPageSelect(data.markdown_content, data.filename);
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
            <Sheet
                open={isSheetOpen}
                onOpenChange={(open) => {
                    setIsSheetOpen(open);
                    if (open) {
                        // Notify parent component that sheet has been opened
                        onSheetOpen?.();
                    }
                }}>
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

                        <div className="flex items-center gap-2 mt-2">
                            <Database className="h-4 w-4" />
                            <span className="text-sm font-medium">Storage Disk:</span>
                            <Select value={selectedDisk} onValueChange={setSelectedDisk}>
                                <SelectTrigger className="w-[180px]">
                                    <SelectValue placeholder="Select a disk" />
                                </SelectTrigger>
                                <SelectContent>
                                    {availableDisks.map((disk) => (
                                        <SelectItem key={disk.name} value={disk.name}>
                                            {disk.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="flex flex-col gap-2 max-h-[calc(100vh-200px)] overflow-y-auto p-4">
                            {loading ? (
                                <div className="text-center py-4">Loading pages...</div>
                            ) : pages.length === 0 ? (
                                <div className="text-center py-4">No pages found. Create your first page!</div>
                            ) : (
                                pages.map((page) => (
                                    <div
                                        key={page.filename}
                                        className={`flex items-center justify-between p-3 rounded-md cursor-pointer hover:bg-neutral-100 dark:hover:bg-neutral-800 ${
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
