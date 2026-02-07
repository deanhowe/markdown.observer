import React, { createContext, ReactNode, useContext, useState } from 'react';

interface AppContextType {
    // Add global state properties here
    theme: 'light' | 'dark' | 'system';
    setTheme: (theme: 'light' | 'dark' | 'system') => void;
}

const AppContext = createContext<AppContextType | undefined>(undefined);

interface AppProviderProps {
    children: ReactNode;
}

export function AppProvider({ children }: AppProviderProps) {
    // Initialize state
    const [theme, setTheme] = useState<'light' | 'dark' | 'system'>('system');

    // Create the context value
    const contextValue: AppContextType = {
        theme,
        setTheme,
    };

    return (
        <AppContext.Provider value={contextValue}>
            {children}
        </AppContext.Provider>
    );
}

// Custom hook to use the app context
export function useAppContext() {
    const context = useContext(AppContext);
    if (context === undefined) {
        throw new Error('useAppContext must be used within an AppProvider');
    }
    return context;
}
