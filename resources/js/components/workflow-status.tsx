import React from 'react';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Separator } from '@/components/ui/separator';
import { RadialChart } from '@/components/ui/radial-chart';

// Define the possible content formats
export type ContentFormat = 'markdown' | 'html' | 'tiptap';

// Define the possible conversion statuses
export type ConversionStatus = 'idle' | 'converting' | 'success' | 'error';

// Define the possible workflow stages
export type WorkflowStage = 'loading' | 'converting' | 'editing' | 'previewing' | 'saving' | 'publishing';

// Define the props for the WorkflowStatus component
interface WorkflowStatusProps {
    // The current format of the content
    contentFormat: ContentFormat;

    // The conversion status (if applicable)
    conversionStatus: ConversionStatus;

    // The overall workflow stage
    workflowStage: WorkflowStage;

    // Optional error message
    errorMessage?: string;

    // Optional className for styling
    className?: string;
}

export function WorkflowStatus({
    contentFormat,
    conversionStatus,
    workflowStage,
    errorMessage,
    className
}: WorkflowStatusProps) {
    // Get the badge variant based on the conversion status
    const getConversionVariant = (status: ConversionStatus) => {
        switch (status) {
            case 'idle':
                return 'secondary';
            case 'converting':
                return 'secondary';
            case 'success':
                return 'default';
            case 'error':
                return 'destructive';
            default:
                return 'secondary';
        }
    };

    // Get the badge variant based on the workflow stage
    const getWorkflowVariant = (stage: WorkflowStage) => {
        switch (stage) {
            case 'loading':
                return 'secondary';
            case 'converting':
                return 'secondary';
            case 'editing':
                return 'default';
            case 'previewing':
                return 'default';
            case 'saving':
                return 'secondary';
            case 'publishing':
                return 'default';
            default:
                return 'secondary';
        }
    };

    // Get the content format display text
    const getFormatText = (format: ContentFormat) => {
        switch (format) {
            case 'markdown':
                return 'Markdown';
            case 'html':
                return 'HTML';
            case 'tiptap':
                return 'Rich Text';
            default:
                return 'Unknown';
        }
    };

    // Get the conversion status display text
    const getConversionText = (status: ConversionStatus) => {
        switch (status) {
            case 'idle':
                return 'Idle';
            case 'converting':
                return 'Converting...';
            case 'success':
                return 'Converted';
            case 'error':
                return 'Error';
            default:
                return 'Unknown';
        }
    };

    // Get the workflow stage display text
    const getWorkflowText = (stage: WorkflowStage) => {
        switch (stage) {
            case 'loading':
                return 'Loading...';
            case 'converting':
                return 'Converting...';
            case 'editing':
                return 'Editing';
            case 'previewing':
                return 'Previewing';
            case 'saving':
                return 'Saving...';
            case 'publishing':
                return 'Publishing...';
            default:
                return 'Unknown';
        }
    };

    // Calculate workflow progress percentage
    const getWorkflowProgress = () => {
        switch (workflowStage) {
            case 'loading':
                return 16; // 1/6 of the process
            case 'converting':
                return 33; // 2/6 of the process
            case 'editing':
                return 50; // 3/6 of the process
            case 'previewing':
                return 67; // 4/6 of the process
            case 'saving':
                return 83; // 5/6 of the process
            case 'publishing':
                return 100; // 6/6 of the process
            default:
                return 0;
        }
    };

    // Get traffic light color for workflow stage
    const getWorkflowStatusColor = () => {
        switch (workflowStage) {
            case 'loading':
                return 'bg-yellow-500'; // Yellow for loading
            case 'converting':
                return 'bg-yellow-500'; // Yellow for converting
            case 'editing':
                return 'bg-blue-500';   // Blue for editing
            case 'previewing':
                return 'bg-blue-500';   // Blue for previewing
            case 'saving':
                return 'bg-yellow-500'; // Yellow for saving
            case 'publishing':
                return 'bg-green-500';  // Green for publishing
            default:
                return 'bg-gray-500';
        }
    };

    // Get chart color for workflow stage
    const getWorkflowChartColor = () => {
        switch (workflowStage) {
            case 'loading':
                return '#eab308'; // Yellow for loading
            case 'converting':
                return '#eab308'; // Yellow for converting
            case 'editing':
                return '#3b82f6'; // Blue for editing
            case 'previewing':
                return '#3b82f6'; // Blue for previewing
            case 'saving':
                return '#eab308'; // Yellow for saving
            case 'publishing':
                return '#22c55e'; // Green for publishing
            default:
                return '#6b7280';
        }
    };

    // Get traffic light color for conversion status
    const getConversionStatusColor = () => {
        switch (conversionStatus) {
            case 'idle':
                return 'bg-gray-500';   // Gray for idle
            case 'converting':
                return 'bg-yellow-500'; // Yellow for converting
            case 'success':
                return 'bg-green-500';  // Green for success
            case 'error':
                return 'bg-red-500';    // Red for error
            default:
                return 'bg-gray-500';
        }
    };

    return (
        <Card className={`w-full ${className}`}>
            <CardHeader className="pb-2">
                <CardTitle className="text-sm flex items-center justify-between">
                    Workflow Status
                    <div className="flex space-x-2">
                        <div className={`w-4 h-4 rounded-full ${getWorkflowStatusColor()} flex items-center justify-center`} title={`Workflow: ${getWorkflowText(workflowStage)}`}>
                            <span className="text-[8px] text-white font-bold">{getWorkflowProgress() / 16}</span>
                        </div>
                        <div className={`w-4 h-4 rounded-full ${getConversionStatusColor()} flex items-center justify-center`} title={`Conversion: ${getConversionText(conversionStatus)}`}>
                            <span className="text-[8px] text-white font-bold">{conversionStatus === 'success' ? '✓' : ''}</span>
                        </div>
                    </div>
                </CardTitle>
            </CardHeader>
            <CardContent className="flex flex-col gap-3 pt-0">
                {/* Radial Chart for workflow progress */}
                <div className="flex justify-center items-center h-32">
                    <RadialChart
                        data={[
                            {
                                name: "Progress",
                                value: getWorkflowProgress(),
                                fill: getWorkflowChartColor()
                            }
                        ]}
                        label={getWorkflowProgress()}
                        valueFormatter={(value) => `${value}%`}
                    />
                </div>

                <Separator />

                {/* Workflow Steps Indicator */}
                <div className="flex justify-between items-center">
                    {['loading', 'converting', 'editing', 'previewing', 'saving', 'publishing'].map((step, index) => {
                        const isCurrentStep = step === workflowStage;
                        const isPastStep = getWorkflowProgress() / 16 > index + 1;

                        return (
                            <div
                                key={step}
                                className={`flex flex-col items-center`}
                                title={getWorkflowText(step as WorkflowStage)}
                            >
                                <div
                                    className={`w-3 h-3 rounded-full ${
                                        isCurrentStep
                                            ? getWorkflowStatusColor()
                                            : isPastStep
                                                ? 'bg-green-500'
                                                : 'bg-gray-300'
                                    }`}
                                ></div>
                                <span className="text-[8px] mt-1">{index + 1}</span>
                            </div>
                        );
                    })}
                </div>

                <Separator />

                <div className="flex items-center justify-between">
                    <span className="text-xs text-muted-foreground">Content Format:</span>
                    <Badge variant="outline">{getFormatText(contentFormat)}</Badge>
                </div>

                <div className="flex items-center justify-between">
                    <span className="text-xs text-muted-foreground">Conversion:</span>
                    <Badge variant={getConversionVariant(conversionStatus)}>
                        {getConversionText(conversionStatus)}
                    </Badge>
                </div>

                <div className="flex items-center justify-between">
                    <span className="text-xs text-muted-foreground">Status:</span>
                    <Badge variant={getWorkflowVariant(workflowStage)}>
                        {getWorkflowText(workflowStage)}
                    </Badge>
                </div>

                {errorMessage && (
                    <Alert variant="destructive" className="mt-2">
                        <AlertDescription className="text-xs">
                            {errorMessage}
                        </AlertDescription>
                    </Alert>
                )}
            </CardContent>
        </Card>
    );
}
