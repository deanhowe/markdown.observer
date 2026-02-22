import React from 'react';
import { Head, Link } from '@inertiajs/react';

interface Package {
    name: string;
    version: string;
    description?: string;
    type: 'prod' | 'dev';
}

interface PackageIndexProps {
    packages: Package[];
    prod_packages: Package[];
    dev_packages: Package[];
    top_packages: Package[];
}

export default function PackageIndex({
    packages,
    prod_packages,
    dev_packages,
    top_packages
}: PackageIndexProps) {
    return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900 p-8">
            <Head title="All Packages" />

            <div className="max-w-6xl mx-auto">
                <header className="mb-8">
                    <h1 className="text-3xl font-bold dark:text-white">Package Documentation Hub</h1>
                    <p className="text-gray-600 dark:text-gray-400">Select a package to view its documentation.</p>
                </header>

                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {packages.map((pkg) => (
                        <div key={pkg.name} className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                            <h2 className="text-xl font-bold dark:text-white mb-2 truncate" title={pkg.name}>
                                {pkg.name}
                            </h2>
                            <p className="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                Version: {pkg.version}
                            </p>
                            <Link
                                href={route('packages.markdown', { package: pkg.name, 'file-path': 'README.md' })}
                                className="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                            >
                                View Docs
                            </Link>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
