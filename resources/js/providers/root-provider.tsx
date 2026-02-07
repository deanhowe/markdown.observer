import React, { ReactNode } from 'react';
import { AppProvider } from './app-provider';
import { QueryProvider } from './query-provider';
import { TooltipProvider } from '@/components/ui/tooltip';
import AnimatedBlobs from '@/components/AnimatedBlobs';

interface RootProviderProps {
    children: ReactNode;
}

export function RootProvider({ children }: RootProviderProps) {
    return (
        <QueryProvider>
            <AppProvider>
                <TooltipProvider>
                    <AnimatedBlobs />
                    {children}
                </TooltipProvider>
            </AppProvider>
        </QueryProvider>
    );
}
