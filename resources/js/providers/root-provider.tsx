import React, { ReactNode } from 'react';
import { AppProvider } from './app-provider';
import { QueryProvider } from './query-provider';
import { TooltipProvider } from '@/components/ui/tooltip';

interface RootProviderProps {
    children: ReactNode;
}

export function RootProvider({ children }: RootProviderProps) {
    return (
        <QueryProvider>
            <AppProvider>
                <TooltipProvider>
                    {children}
                </TooltipProvider>
            </AppProvider>
        </QueryProvider>
    );
}
