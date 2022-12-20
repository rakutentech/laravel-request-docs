import React from "react"
import { FontSans, FontMono } from "../components/DefaultFonts"
import GlobalState from "../components/GlobalState"

import NavBar from "../components/NavBar"

import "../styles/globals.css"


export default async function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <GlobalState>
      <html className="antialiased">
        <body className={`${FontSans.variable} ${FontMono.variable} font-sans h-100vh`}>
          <NavBar />
          {children}
        </body>
      </html>
    </GlobalState> 
  )
}
