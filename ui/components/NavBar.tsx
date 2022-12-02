import React from "react"

import Logo from "./Logo"
import ThemeSelect from "./ThemeSelect"


export default function NavBar() {
  return (
    <header className="relative bg-none">
      <nav className="relative z-20 bg-base-100 bg-opacity-10 backdrop-blur-md shadow inset-0" aria-label="Top">
        <div className="mx-auto flex items-center justify-between px-4 py-3 space-between">
          <div>
            <a href="#" className="flex">
              <span className="sr-only">Laravel Request Docs</span>
              <Logo />
            </a>
          </div>
          <div className="justify-end">
            <ThemeSelect />
          </div>
        </div>
      </nav>
    </header>
  )
}
