import React from "react"
import { Inter } from "@next/font/google"

import NavBar from "../components/NavBar"

import "../styles/globals.css"

const appFont = Inter({ subsets: ["latin"] })
export default async function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html>
      <body className={appFont.className + " antialiased"}>
        <NavBar />
        {children}
      </body>
    </html>
  )
}
