"use client"

import React, { Fragment, useEffect, useState } from "react"
import { Popover, Transition } from "@headlessui/react"
import { SwatchIcon } from "@heroicons/react/24/outline"
import { ChevronDownIcon } from "@heroicons/react/20/solid"

const themes = [
  { id: "light", name: "Light" },
  { id: "corporate", name: "Corporate" },
  { id: "forest", name: "Forest" },
  { id: "dracula", name: "Dracula" },
  { id: "night", name: "Night" },
]

function classNames(...classes: string[]) {
  return classes.filter(Boolean).join(" ")
}

function setTheme(theme: string) {
  if (typeof localStorage !== "undefined") {
    localStorage.setItem("__lrd-theme", theme)
  }
  document.documentElement.setAttribute("data-theme", theme)
}

function getTheme() {
  let theme
  if (typeof localStorage !== "undefined") {
    theme = localStorage.getItem("__lrd-theme")
  }
  return theme ?? themes[0].id
}

export default function ThemeSelect() {
  const [currentTheme, setCurrentTheme] = useState(getTheme())
  useEffect(() => {
    setTheme(currentTheme)
  }, [currentTheme])

  return (
    <>
      <Popover className="relative">
        {({ open }) => (
          <>
            <Popover.Button
              className={classNames(
                open ? "text-base-content" : "text-base-content/80",
                "group inline-flex content-evenly item-center rounded-md font-medium hover:text-base-content"
              )}>
              <SwatchIcon className="h-5 w-5" />
              <span className="text-sm ml-1">Themes</span>
              <ChevronDownIcon
                className={classNames(
                  open ? "text-base-content" : "text-base-content/80",
                  "ml-2 h-5 w-5 group-hover:text-base-content"
                )}
                aria-hidden="true"
              />
            </Popover.Button>
            <Transition
              as={Fragment}
              enter="transition ease-out duration-200"
              enterFrom="opacity-0 translate-y-1"
              enterTo="opacity-100 translate-y-0"
              leave="transition ease-in duration-150"
              leaveFrom="opacity-100 translate-y-0"
              leaveTo="opacity-0 translate-y-1"
            >
              <Popover.Panel className="absolute w-max -right-2 top-full z-10 transform shadow-lg ">
                <div className="overflow-hidden rounded-lg shadow-lg bg-base-200">
                  <div className="grid grid-cols-1 gap-3 p-3 data-choose-theme" tabIndex={0}>
                    {themes.map((item) => (
                      <button
                        type="button"
                        key={item.id}
                        className={classNames(
                          currentTheme === item.id ? "outline-none ring-4" : "",
                          "overflow-hidden rounded-lg ring-primary focus:ring-primary/50 hover:ring-primary/50 hover:ring-4"
                        )}
                        onClick={() => setCurrentTheme(item.id)}
                      >
                        <div
                          data-theme={item.id}
                          className="bg-base-100 text-base-content w-full cursor-pointer"
                        >
                          <div className="grid grid-cols-5 grid-rows-3">
                            <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-2 px-4">
                              <div className="flex-grow text-left text-sm font-bold">{item.name}</div>
                              <div className="flex flex-shrink-0 flex-wrap gap-1">
                                <div className="bg-primary w-2 rounded" />
                                <div className="bg-secondary w-2 rounded" />
                                <div className="bg-accent w-2 rounded" />
                                <div className="bg-neutral w-2 rounded" />
                              </div>
                            </div>
                          </div>
                        </div>
                      </button>
                    ))}
                  </div>
                </div>
              </Popover.Panel>
            </Transition>
          </>
        )}
      </Popover>
    </>
  )
}