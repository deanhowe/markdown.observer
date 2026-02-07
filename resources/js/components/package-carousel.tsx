import { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselPrevious,
  CarouselNext,
  type CarouselApi,
} from '@/components/ui/carousel';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import ReadmeRenderer from '@/components/readme-renderer';

interface Package {
  name: string;
  logo: {
    path: string | null;
    url: string;
  } | null;
  readme_html: string;
  rank: number;
  type: string;
  version: string;
  description: string;
}

type OrderBy = 'rank' | 'name' | 'usage_count' | 'type';
type Direction = 'asc' | 'desc';
type PkgType = 'all' | 'prod' | 'dev';

interface PackageCarouselProps {
  className?: string;
}

export default function PackageCarousel({ className }: PackageCarouselProps) {
  const [packages, setPackages] = useState<Package[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [api, setApi] = useState<CarouselApi>();
  const [current, setCurrent] = useState(0);
  const [count, setCount] = useState(0);
  // Controls
  const [limit, setLimit] = useState<string>('12');
  const [orderBy, setOrderBy] = useState<OrderBy>('rank');
  const [direction, setDirection] = useState<Direction>('asc');
  const [pkgType, setPkgType] = useState<PkgType>('all');
  const [includeReadme, setIncludeReadme] = useState<boolean>(false);

  useEffect(() => {
    const fetchPackages = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/api/packages/carousel', {
          params: {
            limit: parseInt(limit, 10),
            order_by: orderBy,
            direction,
            type: pkgType,
            include_readme: includeReadme ? 1 : 0,
            // ensure feature tests can opt-out of mock responses
            use_mock: 0,
          }
        });
        setPackages(response.data.packages);
        setLoading(false);
      } catch (err) {
        setError('Failed to load packages');
        setLoading(false);
        console.error('Error fetching packages:', err);
      }
    };

    fetchPackages();
  }, [limit, orderBy, direction, pkgType, includeReadme]);

  useEffect(() => {
    if (!api) return;

    setCount(api.scrollSnapList().length);
    setCurrent(api.selectedScrollSnap() + 1);

    api.on("select", () => {
      setCurrent(api.selectedScrollSnap() + 1);
    });
  }, [api]);

  const scrollTo = useCallback((index: number) => {
    api?.scrollTo(index);
  }, [api]);

  if (loading) {
    return (
      <div className={cn("w-full", className)}>
        <h2 className="text-2xl font-bold mb-4">Composer Packages</h2>

        {/* Controls */}
        <div className="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
          <div className="flex items-center gap-2" data-testid="carousel-limit">
            <Label htmlFor="limit-select" className="min-w-20">Results</Label>
            <Select value={limit} onValueChange={(v) => setLimit(v)}>
              <SelectTrigger id="limit-select" aria-label="Results per page" className="h-9">
                <SelectValue placeholder="Select" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="6">6</SelectItem>
                <SelectItem value="12">12</SelectItem>
                <SelectItem value="24">24</SelectItem>
                <SelectItem value="48">48</SelectItem>
                <SelectItem value="0">All</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div className="flex items-center gap-2" data-testid="carousel-order-by">
            <Label htmlFor="order-by-select" className="min-w-20">Order</Label>
            <Select value={orderBy} onValueChange={(v: string) => setOrderBy(v as OrderBy)}>
              <SelectTrigger id="order-by-select" aria-label="Order by" className="h-9">
                <SelectValue placeholder="Order By" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="rank">Rank</SelectItem>
                <SelectItem value="name">Name</SelectItem>
                <SelectItem value="usage_count">Usage</SelectItem>
                <SelectItem value="type">Type</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div className="flex items-center gap-2" data-testid="carousel-direction">
            <Label htmlFor="direction-select" className="min-w-20">Direction</Label>
            <Select value={direction} onValueChange={(v: string) => setDirection(v as Direction)}>
              <SelectTrigger id="direction-select" aria-label="Sort direction" className="h-9">
                <SelectValue placeholder="Direction" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="asc">Asc</SelectItem>
                <SelectItem value="desc">Desc</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div className="flex items-center gap-2" data-testid="carousel-type">
            <Label htmlFor="type-select" className="min-w-20">Type</Label>
            <Select value={pkgType} onValueChange={(v: string) => setPkgType(v as PkgType)}>
              <SelectTrigger id="type-select" aria-label="Package type" className="h-9">
                <SelectValue placeholder="Type" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                <SelectItem value="prod">Prod</SelectItem>
                <SelectItem value="dev">Dev</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div className="flex items-center justify-between gap-2" data-testid="carousel-include-readme">
            <Label htmlFor="include-readme-switch" className="min-w-24">Include README</Label>
            <Switch id="include-readme-switch" checked={includeReadme} onCheckedChange={setIncludeReadme} />
          </div>
        </div>
        <div className="w-full">
          <div className="flex overflow-hidden">
            {[1, 2, 3].map((i) => (
              <div key={i} className="min-w-[100%] md:min-w-[50%] lg:min-w-[33.333%] px-4">
                <div className="aspect-[335/376] lg:aspect-auto lg:h-[438px] rounded-lg bg-gray-100 dark:bg-gray-800 overflow-hidden animate-pulse">
                  <div className="flex flex-col items-center p-6 h-full">
                    <div className="mb-4 h-24 w-24 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div className="h-6 w-3/4 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                    <div className="h-4 w-1/2 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                    <div className="h-4 w-1/3 bg-gray-200 dark:bg-gray-700 rounded mb-4"></div>
                    <div className="flex-1 w-full space-y-2">
                      <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                      <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                      <div className="h-4 w-5/6 bg-gray-200 dark:bg-gray-700 rounded"></div>
                      <div className="h-4 w-4/6 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
        <div className="text-center mt-4">
          <div className="inline-block h-5 w-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
        </div>
        <div className="flex justify-center gap-1 mt-2">
          {[1, 2, 3].map((i) => (
            <div key={i} className="h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600"></div>
          ))}
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className={cn("w-full", className)}>
        <h2 className="text-2xl font-bold mb-4">Composer Packages</h2>
        <div className="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-8 text-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            className="h-12 w-12 mx-auto text-red-500 mb-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <h3 className="text-lg font-semibold text-red-700 dark:text-red-400 mb-2">Error Loading Packages</h3>
          <p className="text-red-600 dark:text-red-300">{error}</p>
          <button
            onClick={() => window.location.reload()}
            className="mt-4 px-4 py-2 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded-md hover:bg-red-200 dark:hover:bg-red-700 transition-colors"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  if (packages.length === 0) {
    return (
      <div className={cn("w-full", className)}>
        <h2 className="text-2xl font-bold mb-4">Composer Packages</h2>
        <div className="rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-8 text-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            className="h-12 w-12 mx-auto text-gray-400 mb-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <h3 className="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No Packages Found</h3>
          <p className="text-gray-600 dark:text-gray-400">We couldn't find any packages with logos to display.</p>
        </div>
      </div>
    );
  }

  return (
    <div className={cn("w-full", className)}>
      <Carousel className="w-full" setApi={setApi}>
        <CarouselContent>
          {packages.map((pkg) => (
            <CarouselItem key={pkg.name} className="md:basis-1/2 lg:basis-1/3">
              <Card className="lg:h-[560px] overflow-hidden rounded-lg bg-[#fff2f2] dark:bg-[#1D0002] ">
                <CardContent className="flex h-full flex-col p-4">
                  {/* Header image as background (fills the panel) */}
                  <div
                    className={cn(
                      "relative w-full h-40 sm:h-48 md:h-24 overflow-hidden rounded-md",
                      "bg-center bg-no-repeat ",
                      // When an image exists, ensure it covers the entire area
                      pkg.logo?.url ? "bg-contain" : "bg-white/60 dark:bg-black/30"
                    )}
                    style={pkg.logo?.url ? { backgroundImage: `url(${pkg.logo.url})` } : undefined}
                    role="img"
                    aria-label={`${pkg.name} logo`}
                  >
                    {!pkg.logo?.url && (
                      <div className="w-full h-full flex items-center justify-center text-xs text-gray-500 dark:text-gray-400">
                        No Image
                      </div>
                    )}
                  </div>

                  {/* Meta */}
                  <div className="mt-3">
                    <h3 className="text-base sm:text-lg font-semibold leading-tight">{pkg.name}</h3>
                    <div className="mt-2 flex flex-wrap items-center gap-2 text-xs">
                      <span className="inline-flex items-center gap-1 rounded-full bg-white/70 dark:bg-white/10 px-2 py-1">
                        <span className="opacity-70">Version</span>
                        <span className="font-medium">{pkg.version}</span>
                      </span>
                      <span className="inline-flex items-center gap-1 rounded-full bg-white/70 dark:bg-white/10 px-2 py-1 capitalize">
                        {pkg.type}
                      </span>
                      {pkg.rank ? (
                        <span className="inline-flex items-center gap-1 rounded-full bg-white/70 dark:bg-white/10 px-2 py-1">
                          Rank #{pkg.rank}
                        </span>
                      ) : null}
                    </div>
                    {pkg.description && (
                      <p className="mt-2 text-sm text-muted-foreground line-clamp-3">{pkg.description}</p>
                    )}
                  </div>

                  {/* README preview */}
                  <div className="mt-3 flex-1 min-h-0">
                    <ReadmeRenderer html={pkg.readme_html} className="h-full overflow-y-auto text-sm" />
                  </div>
                </CardContent>
              </Card>
            </CarouselItem>
          ))}
        </CarouselContent>
        <CarouselPrevious className="left-2" />
        <CarouselNext className="right-2" />
      </Carousel>

      {/* Package counter */}
      <div className="text-center mt-4">
        <span className="text-sm font-medium">
          {current} / {count}
        </span>
      </div>

      {/* Indicator dots (hide when too many) */}
      {count > 0 && count <= 12 && (
        <div className="flex justify-center gap-1 mt-2">
          {Array.from({ length: count }).map((_, index) => (
            <button
              key={index}
              className={cn(
                "h-2 w-2 rounded-full transition-colors",
                current === index + 1 ? "bg-primary" : "bg-gray-300 dark:bg-gray-600"
              )}
              onClick={() => scrollTo(index)}
              aria-label={`Go to slide ${index + 1}`}
            />
          ))}
        </div>
      )}
    </div>
  );
}
