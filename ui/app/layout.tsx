import React from "react"
import { Inter, JetBrains_Mono } from "@next/font/google"

import NavBar from "../components/NavBar"

import "../styles/globals.css"

const fontSans = Inter({
  subsets: ["latin"],
  variable: "--font-sans",
  fallback: ["-apple-system", "BlinkMacSystemFont", "Segoe UI",
    "Roboto", "Helvetica Neue", "Ubuntu", "Noto Sans", "sans-serif",
    "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"
    ],
})

const fontMono = JetBrains_Mono({
  subsets: ["latin"],
  variable: "--font-mono",
  fallback: ["ui-monospace", "SFMono-Regular", "Menlo", "Monaco", "Consolas", "monospace"],
})

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html className="antialiased">
      <body className={`${fontSans.variable} ${fontMono.variable} font-sans h-100vh`}>
        <NavBar />
        {children}
      </body>
    </html>
  )
}
