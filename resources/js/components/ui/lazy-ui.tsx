import React, { lazy, Suspense } from 'react';
import { Loader2 } from 'lucide-react';

// Loading component to show while lazy components are loading
const LoadingComponent = () => (
    <div className="flex items-center justify-center p-4">
        <Loader2 className="h-6 w-6 animate-spin text-neutral-400" aria-hidden="true" />
        <span className="sr-only">Loading UI component...</span>
    </div>
);

// Lazy load Dialog components (used in modals, sheets, etc.)
export const LazyDialog = lazy(() => import('./dialog').then(module => ({
    default: module.Dialog,
})));
export const LazyDialogTrigger = lazy(() => import('./dialog').then(module => ({
    default: module.DialogTrigger
})));
export const LazyDialogContent = lazy(() => import('./dialog').then(module => ({
    default: module.DialogContent
})));
export const LazyDialogHeader = lazy(() => import('./dialog').then(module => ({
    default: module.DialogHeader
})));
export const LazyDialogFooter = lazy(() => import('./dialog').then(module => ({
    default: module.DialogFooter
})));
export const LazyDialogTitle = lazy(() => import('./dialog').then(module => ({
    default: module.DialogTitle
})));
export const LazyDialogDescription = lazy(() => import('./dialog').then(module => ({
    default: module.DialogDescription
})));

// Lazy load Collapsible components
export const LazyCollapsible = lazy(() => import('./collapsible').then(module => ({
    default: module.Collapsible,
})));
export const LazyCollapsibleTrigger = lazy(() => import('./collapsible').then(module => ({
    default: module.CollapsibleTrigger
})));
export const LazyCollapsibleContent = lazy(() => import('./collapsible').then(module => ({
    default: module.CollapsibleContent
})));

// Lazy load Avatar components
export const LazyAvatar = lazy(() => import('./avatar').then(module => ({
    default: module.Avatar,
})));
export const LazyAvatarImage = lazy(() => import('./avatar').then(module => ({
    default: module.AvatarImage
})));
export const LazyAvatarFallback = lazy(() => import('./avatar').then(module => ({
    default: module.AvatarFallback
})));

// Lazy load react-medium-image-zoom
export const LazyImageZoom = lazy(() => import('react-medium-image-zoom'));

// Wrapper components with Suspense
export const Dialog = (props: React.ComponentProps<typeof LazyDialog>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyDialog {...props} />
    </Suspense>
);

export const DialogTrigger = (props: React.ComponentProps<typeof LazyDialogTrigger>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyDialogTrigger {...props} />
    </Suspense>
);

export const DialogContent = (props: React.ComponentProps<typeof LazyDialogContent>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyDialogContent {...props} />
    </Suspense>
);

export const DialogHeader = (props: React.ComponentProps<typeof LazyDialogHeader>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyDialogHeader {...props} />
    </Suspense>
);

export const DialogFooter = (props: React.ComponentProps<typeof LazyDialogFooter>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyDialogFooter {...props} />
    </Suspense>
);

export const DialogTitle = (props: React.ComponentProps<typeof LazyDialogTitle>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyDialogTitle {...props} />
    </Suspense>
);

export const DialogDescription = (props: React.ComponentProps<typeof LazyDialogDescription>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyDialogDescription {...props} />
    </Suspense>
);

export const Collapsible = (props: React.ComponentProps<typeof LazyCollapsible>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyCollapsible {...props} />
    </Suspense>
);

export const CollapsibleTrigger = (props: React.ComponentProps<typeof LazyCollapsibleTrigger>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyCollapsibleTrigger {...props} />
    </Suspense>
);

export const CollapsibleContent = (props: React.ComponentProps<typeof LazyCollapsibleContent>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyCollapsibleContent {...props} />
    </Suspense>
);

export const Avatar = (props: React.ComponentProps<typeof LazyAvatar>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyAvatar {...props} />
    </Suspense>
);

export const AvatarImage = (props: React.ComponentProps<typeof LazyAvatarImage>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyAvatarImage {...props} />
    </Suspense>
);

export const AvatarFallback = (props: React.ComponentProps<typeof LazyAvatarFallback>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyAvatarFallback {...props} />
    </Suspense>
);

export const ImageZoom = (props: React.ComponentProps<typeof LazyImageZoom>) => (
    <Suspense fallback={<LoadingComponent />}>
        <LazyImageZoom {...props} />
    </Suspense>
);
