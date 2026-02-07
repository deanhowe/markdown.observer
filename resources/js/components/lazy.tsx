import React, { lazy, Suspense } from 'react';
import { Loader2 } from 'lucide-react';

// Loading component to show while lazy components are loading
const LoadingComponent = () => (
    <div className="flex items-center justify-center p-4">
        <Loader2 className="h-8 w-8 animate-spin text-neutral-400" aria-hidden="true" />
        <span className="sr-only">Loading component...</span>
    </div>
);

// Lazy load the Markdown editor
export const LazyMarkdownEditor = lazy(() => import('./markdown-editor').then(module => ({
    default: module.MarkdownEditor
})));

// Lazy load the TipTap editor
export const LazyTiptapEditor = lazy(() => import('./tiptap-editor').then(module => ({
    default: module.TiptapEditor
})));

// Lazy load the PageManager component
export const LazyPageManager = lazy(() => import('./page-manager').then(module => ({
    default: module.PageManager
})));

// Lazy load the PageManagerSheet component
export const LazyPageManagerSheet = lazy(() => import('./page-manager-sheet').then(module => ({
    default: module.PageManagerSheet
})));

// Wrapper components with Suspense
export const MarkdownEditor = (props: React.ComponentProps<typeof LazyMarkdownEditor>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyMarkdownEditor {...props} />
    </Suspense>
);

export const TiptapEditor = (props: React.ComponentProps<typeof LazyTiptapEditor>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyTiptapEditor {...props} />
    </Suspense>
);

export const PageManager = (props: React.ComponentProps<typeof LazyPageManager>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyPageManager {...props} />
    </Suspense>
);

export const PageManagerSheet = (props: React.ComponentProps<typeof LazyPageManagerSheet>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyPageManagerSheet {...props} />
    </Suspense>
);
