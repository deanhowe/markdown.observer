import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import axios from 'axios';

interface MarkdownFile {
    name: string;
    path: string;
    relative_path: string;
    is_dir: boolean;
    children?: MarkdownFile[];
}

interface PackageMarkdownProps {
    package_name: string;
    file_path: string;
    content: string;
    html: string;
    files: MarkdownFile[];
    last_modified?: number;
}

export default function PackageMarkdown({
    package_name,
    file_path,
    content,
    html,
    files,
    last_modified: initialLastModified
}: PackageMarkdownProps) {
    const [lastModified, setLastModified] = useState(initialLastModified);
    const [isPolling, setIsPolling] = useState(true);
    const [isExporting, setIsExporting] = useState(false);

    const exportForAi = async () => {
        setIsExporting(true);
        try {
            const response = await axios.post(route('api.markdown.export-for-ai'), { markdown: content });
            await navigator.clipboard.writeText(response.data.markdown);
            alert('Optimized markdown copied to clipboard!');
        } catch (error) {
            console.error('Export error:', error);
            alert('Failed to export for AI');
        } finally {
            setIsExporting(false);
        }
    };

    // Polling for file changes
    useEffect(() => {
        if (!isPolling || !package_name || !file_path) return;

        const poll = async () => {
            try {
                // We use encoded file_path because it may contain slashes
                const response = await axios.get(route('packages.last-modified', {
                    package: package_name,
                    'file-path': file_path
                }));

                const newLastModified = response.data.last_modified;
                if (newLastModified && lastModified && newLastModified > lastModified) {
                    setLastModified(newLastModified);
                    // Refresh the page data
                    router.reload({ only: ['content', 'html', 'last_modified'] });
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        };

        const intervalId = setInterval(poll, 2000); // Poll every 2 seconds
        return () => clearInterval(intervalId);
    }, [isPolling, package_name, file_path, lastModified]);

    const renderFileTree = (nodes: MarkdownFile[]) => {
        return (
            <ul className="pl-4 space-y-1">
                {nodes.map((node) => (
                    <li key={node.path}>
                        {node.is_dir ? (
                            <div>
                                <span className="font-semibold text-gray-500">📁 {node.name}</span>
                                {node.children && renderFileTree(node.children)}
                            </div>
                        ) : (
                            <Link
                                href={route('packages.markdown', { package: package_name, 'file-path': node.relative_path })}
                                className={`block hover:text-blue-500 transition-colors ${file_path === node.relative_path ? 'text-blue-600 font-bold' : 'text-gray-700'}`}
                            >
                                📄 {node.name}
                            </Link>
                        )}
                    </li>
                ))}
            </ul>
        );
    };

    return (
        <div className="flex min-h-screen bg-gray-50 dark:bg-gray-900">
            <Head title={`${package_name} - ${file_path}`} />

            {/* Sidebar */}
            <aside className="w-80 border-r bg-white dark:bg-gray-800 p-6 overflow-y-auto hidden md:block">
                <div className="mb-6">
                    <Link href={route('packages.index')} className="text-sm text-blue-500 hover:underline">
                        ← All Packages
                    </Link>
                    <h1 className="text-xl font-bold mt-2 dark:text-white truncate" title={package_name}>
                        {package_name}
                    </h1>
                </div>

                <nav>
                    <h2 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Files</h2>
                    {renderFileTree(files)}
                </nav>
            </aside>

            {/* Main Content */}
            <main className="flex-1 overflow-y-auto">
                <header className="bg-white dark:bg-gray-800 border-b p-4 flex justify-between items-center sticky top-0 z-10">
                    <div className="flex items-center gap-2">
                        <button className="md:hidden text-gray-500">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        <span className="text-sm font-medium text-gray-500 dark:text-gray-400 truncate max-w-xs md:max-w-lg">
                            {file_path}
                        </span>
                    </div>

                    <div className="flex items-center gap-4">
                        <button
                            onClick={exportForAi}
                            disabled={isExporting}
                            className="text-xs px-3 py-1 bg-purple-500 text-white rounded hover:bg-purple-600 disabled:opacity-50 transition-colors"
                        >
                            {isExporting ? 'Exporting...' : 'Export for AI'}
                        </button>

                        <div className="flex items-center gap-2">
                            <span className={`w-2 h-2 rounded-full ${isPolling ? 'bg-green-500 animate-pulse' : 'bg-gray-300'}`}></span>
                            <span className="text-xs text-gray-500 dark:text-gray-400">
                                {isPolling ? 'Watching' : 'Paused'}
                            </span>
                        </div>
                        <button
                            onClick={() => setIsPolling(!isPolling)}
                            className="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors"
                            title={isPolling ? 'Stop Watching' : 'Start Watching'}
                        >
                            {isPolling ? (
                                <svg className="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            ) : (
                                <svg className="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            )}
                        </button>
                    </div>
                </header>

                <div className="max-w-4xl mx-auto p-8 lg:p-12">
                    <article
                        className="prose prose-blue dark:prose-invert max-w-none"
                        dangerouslySetInnerHTML={{ __html: html }}
                    />
                </div>
            </main>
        </div>
    );
}
