import * as React from "react"
import { RadialBarChart, RadialBar, Legend, ResponsiveContainer, PolarAngleAxis } from "recharts"

import { cn } from "@/lib/utils"

interface RadialChartProps extends React.HTMLAttributes<HTMLDivElement> {
  data: {
    name: string
    value: number
    fill: string
  }[]
  valueFormatter?: (value: number) => string
  label?: string
  showLegend?: boolean
}

export function RadialChart({
  data,
  valueFormatter = (value) => `${value}%`,
  label,
  showLegend = false,
  className,
  ...props
}: RadialChartProps) {
  return (
    <div className={cn("w-full h-full", className)} {...props}>
      <ResponsiveContainer width="100%" height="100%">
        <RadialBarChart
          cx="50%"
          cy="50%"
          innerRadius="40%"
          outerRadius="80%"
          barSize={10}
          data={data}
        >
          <PolarAngleAxis
            type="number"
            domain={[0, 100]}
            angleAxisId={0}
            tick={false}
          />
          <RadialBar
            background
            dataKey="value"
            angleAxisId={0}
            fill="var(--color-primary)"
            cornerRadius={5}
          />
          {label && (
            <text
              x="50%"
              y="50%"
              textAnchor="middle"
              dominantBaseline="middle"
              className="fill-foreground font-medium text-base"
            >
              {typeof label === 'number' ? valueFormatter(label) : label}
            </text>
          )}
          {showLegend && <Legend />}
        </RadialBarChart>
      </ResponsiveContainer>
    </div>
  )
}
