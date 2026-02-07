import React, { useState, useEffect, useRef, useMemo } from 'react';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';

interface ReadmeRendererProps {
  html: string;
  className?: string;
  maxInitialHeight?: number; // Height in pixels before truncation
}

export default function ReadmeRenderer({
  html,
  className,
  maxInitialHeight = 300
}: ReadmeRendererProps) {
  const [expanded, setExpanded] = useState(false);
  const [needsExpansion, setNeedsExpansion] = useState(false);
  const contentRef = useRef<HTMLDivElement>(null);

  // Optimize HTML output size by removing unnecessary attributes and whitespace
  const optimizedHtml = useMemo(() => {
    if (!html) return '';

    // Simple optimization to remove some common unnecessary attributes and whitespace
    return html
      .replace(/\s+/g, ' ')
      .replace(/\s+>/g, '>')
      .replace(/>\s+</g, '><')
      .replace(/style="[^"]*"/g, '')
      .trim();
  }, [html]);

  // Check if content needs expansion button after initial render
  useEffect(() => {
    if (contentRef.current && contentRef.current.scrollHeight > maxInitialHeight) {
      setNeedsExpansion(true);
    }
  }, [optimizedHtml, maxInitialHeight]);

  if (!html) {
    return <div className={cn("text-center py-4 text-gray-500", className)}>No README content available</div>;
  }

  return (
    <div className={cn("readme-content relative", className, {
      "max-h-[300px]": !expanded && needsExpansion
    })}>
      <div
        ref={contentRef}
        className={cn({
          "overflow-hidden": !expanded && needsExpansion
        })}
        dangerouslySetInnerHTML={{ __html: optimizedHtml }}
      />

      {/* Gradient fade for truncated content */}
      {!expanded && needsExpansion && (
        <div className="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-white dark:from-gray-900 to-transparent pointer-events-none"></div>
      )}

      {/* Expansion button */}
      {needsExpansion && (
        <div className="text-center mt-2">
          <Button
            variant="outline"
            size="sm"
            onClick={() => setExpanded(!expanded)}
            className="text-xs"
          >
            {expanded ? 'Show Less' : 'Show More'}
          </Button>
        </div>
      )}

      {/* Add CSS for README content (global style for this component scope) */}
      <style>{`
        .readme-content {
          /* Base styles */
          font-size: 0.875rem;
          line-height: 1.5;
          overflow-y: auto;
          width: 100%;

          /* Headings */
          h1, h2, h3, h4, h5, h6 {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            font-weight: 600;
            line-height: 1.25;
          }

          h1 {
            font-size: 1.5rem;
            border-bottom: 1px solid #eaecef;
            padding-bottom: 0.3rem;
          }

          h2 {
            font-size: 1.25rem;
            border-bottom: 1px solid #eaecef;
            padding-bottom: 0.3rem;
          }

          h3 {
            font-size: 1.125rem;
          }

          /* Paragraphs and lists */
          p {
            margin-bottom: 1rem;
          }

          ul, ol {
            padding-left: 2rem;
            margin-bottom: 1rem;
          }

          li {
            margin-bottom: 0.25rem;
          }

          /* Links */
          a {
            color: #0366d6;
            text-decoration: none;
          }

          a:hover {
            text-decoration: underline;
          }

          /* Images */
          img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 1rem auto;
            border-radius: 0.25rem;
          }

          /* Code blocks */
          pre {
            background-color: #f6f8fa;
            border-radius: 0.25rem;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
          }

          code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.85em;
            background-color: rgba(27, 31, 35, 0.05);
            padding: 0.2em 0.4em;
            border-radius: 0.25rem;
          }

          pre code {
            background-color: transparent;
            padding: 0;
            font-size: 0.85em;
            white-space: pre;
            word-break: normal;
            overflow-wrap: normal;
          }

          /* Tables */
          table {
            border-collapse: collapse;
            width: 100%;
            margin: 1rem 0;
            display: block;
            overflow-x: auto;
          }

          table th, table td {
            padding: 0.5rem 1rem;
            border: 1px solid #dfe2e5;
          }

          table th {
            background-color: #f6f8fa;
            font-weight: 600;
          }

          /* Blockquotes */
          blockquote {
            margin: 1rem 0;
            padding: 0 1rem;
            color: #6a737d;
            border-left: 0.25rem solid #dfe2e5;
          }

          /* Horizontal rule */
          hr {
            height: 0.25rem;
            padding: 0;
            margin: 1.5rem 0;
            background-color: #e1e4e8;
            border: 0;
          }

          /* Dark mode adjustments */
          @media (prefers-color-scheme: dark) {
            pre {
              background-color: #1a1a1a;
            }

            code {
              background-color: rgba(200, 200, 200, 0.1);
            }

            table th {
              background-color: #2d2d2d;
            }

            table th, table td {
              border-color: #444;
            }

            blockquote {
              border-left-color: #444;
              color: #aaa;
            }

            hr {
              background-color: #444;
            }

            a {
              color: #58a6ff;
            }
          }
        }
      `}</style>
    </div>
  );
}
