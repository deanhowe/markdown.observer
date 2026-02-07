import { useEffect, useRef } from 'react'

export default function AnimatedBlobs() {
  const canvasRef = useRef<HTMLCanvasElement>(null)

  useEffect(() => {
    const canvas = canvasRef.current
    if (!canvas) return

    const ctx = canvas.getContext('2d')
    if (!ctx) return

    // Set canvas size
    const resize = () => {
      canvas.width = window.innerWidth
      canvas.height = window.innerHeight
    }
    resize()
    window.addEventListener('resize', resize)

    // Blob class
    class Blob {
      x: number
      y: number
      radius: number
      vx: number
      vy: number
      color: string

      constructor() {
        this.x = Math.random() * canvas.width
        this.y = Math.random() * canvas.height
        this.radius = Math.random() * 400 + 300
        this.vx = (Math.random() - 0.5) * 0.3
        this.vy = (Math.random() - 0.5) * 0.3
        
        // More blue/purple, less pink
        const colors = [
          'rgba(59, 130, 246, 0.4)',   // blue - stronger
          'rgba(99, 102, 241, 0.35)',  // indigo
          'rgba(147, 51, 234, 0.35)',  // purple
          'rgba(168, 85, 247, 0.3)',   // lighter purple
        ]
        this.color = colors[Math.floor(Math.random() * colors.length)]
      }

      update() {
        this.x += this.vx
        this.y += this.vy

        // Bounce off edges
        if (this.x < -this.radius || this.x > canvas.width + this.radius) {
          this.vx *= -1
        }
        if (this.y < -this.radius || this.y > canvas.height + this.radius) {
          this.vy *= -1
        }
      }

      draw(ctx: CanvasRenderingContext2D) {
        const gradient = ctx.createRadialGradient(
          this.x, this.y, 0,
          this.x, this.y, this.radius
        )
        gradient.addColorStop(0, this.color)
        gradient.addColorStop(1, 'rgba(0, 0, 0, 0)')

        ctx.fillStyle = gradient
        ctx.beginPath()
        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2)
        ctx.fill()
      }
    }

    // Create blobs
    const blobs: Blob[] = []
    for (let i = 0; i < 7; i++) {
      const blob = new Blob()
      // Force some blobs to bottom half
      if (i > 4) {
        blob.y = canvas.height * 0.7 + Math.random() * canvas.height * 0.3
      }
      blobs.push(blob)
    }

    // Animation loop
    let animationId: number
    const animate = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height)
      
      blobs.forEach(blob => {
        blob.update()
        blob.draw(ctx)
      })

      animationId = requestAnimationFrame(animate)
    }
    animate()

    return () => {
      window.removeEventListener('resize', resize)
      cancelAnimationFrame(animationId)
    }
  }, [])

  return (
    <canvas
      ref={canvasRef}
      className="fixed inset-0 z-0 pointer-events-none"
      style={{ filter: 'blur(40px)' }}
    />
  )
}
