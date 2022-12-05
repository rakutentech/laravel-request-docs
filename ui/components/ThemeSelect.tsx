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
                "group inline-flex content-evenly item-center rounded-md font-medium hover:text-base-content focus:outline focus:outline-2 focus:outline-primary focus:outline-offset-2"
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
                          currentTheme === item.id ? "outline outline-2" : "",
                          "overflow-hidden rounded-lg outline-primary hover:outline hover:outline-2"
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
      {/*
    <div title="Change Theme" className="dropdown dropdown-end p-0">
      <div tabIndex={0} className="btn gap-1 normal-case btn-ghost">
        <svg
          width={20}
          height={20}
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          className="inline-block h-5 w-5 stroke-current md:h-6 md:w-6"
        >
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            strokeWidth={2}
            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"
          />
        </svg>
        <span className="hidden md:inline">Theme</span>
        <svg
          width="12px"
          height="12px"
          className="ml-1 hidden h-3 w-3 fill-current opacity-60 sm:inline-block"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 2048 2048"
        >
          <path d="M1799 349l242 241-1017 1017L7 590l242-241 775 775 775-775z" />
        </svg>
      </div>
      <div className="dropdown-content bg-base-200 text-base-content rounded-t-box rounded-b-box top-px max-h-96 h-[70vh] w-52 overflow-y-auto shadow-2xl mt-16">
        <div className="grid grid-cols-1 gap-3 p-3" tabIndex={0}>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="light"
            data-act-class="outline"
          >
            <div
              data-theme="light"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">light</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="dark"
            data-act-class="outline"
          >
            <div
              data-theme="dark"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">dark</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="cupcake"
            data-act-class="outline"
          >
            <div
              data-theme="cupcake"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">cupcake</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="bumblebee"
            data-act-class="outline"
          >
            <div
              data-theme="bumblebee"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">bumblebee</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="emerald"
            data-act-class="outline"
          >
            <div
              data-theme="emerald"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">emerald</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="corporate"
            data-act-class="outline"
          >
            <div
              data-theme="corporate"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">corporate</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="synthwave"
            data-act-class="outline"
          >
            <div
              data-theme="synthwave"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">synthwave</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="retro"
            data-act-class="outline"
          >
            <div
              data-theme="retro"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">retro</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="cyberpunk"
            data-act-class="outline"
          >
            <div
              data-theme="cyberpunk"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">cyberpunk</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="valentine"
            data-act-class="outline"
          >
            <div
              data-theme="valentine"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">valentine</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="halloween"
            data-act-class="outline"
          >
            <div
              data-theme="halloween"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">halloween</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="garden"
            data-act-class="outline"
          >
            <div
              data-theme="garden"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">garden</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="forest"
            data-act-class="outline"
          >
            <div
              data-theme="forest"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">forest</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="aqua"
            data-act-class="outline"
          >
            <div
              data-theme="aqua"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">aqua</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="lofi"
            data-act-class="outline"
          >
            <div
              data-theme="lofi"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">lofi</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="pastel"
            data-act-class="outline"
          >
            <div
              data-theme="pastel"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">pastel</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="fantasy"
            data-act-class="outline"
          >
            <div
              data-theme="fantasy"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">fantasy</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="wireframe"
            data-act-class="outline"
          >
            <div
              data-theme="wireframe"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">wireframe</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="black"
            data-act-class="outline"
          >
            <div
              data-theme="black"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">black</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="luxury"
            data-act-class="outline"
          >
            <div
              data-theme="luxury"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">luxury</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="dracula"
            data-act-class="outline"
          >
            <div
              data-theme="dracula"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">dracula</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="cmyk"
            data-act-class="outline"
          >
            <div
              data-theme="cmyk"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">cmyk</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="autumn"
            data-act-class="outline"
          >
            <div
              data-theme="autumn"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">autumn</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="business"
            data-act-class="outline"
          >
            <div
              data-theme="business"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">business</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="acid"
            data-act-class="outline"
          >
            <div
              data-theme="acid"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">acid</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="lemonade"
            data-act-class="outline"
          >
            <div
              data-theme="lemonade"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">lemonade</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="night"
            data-act-class="outline"
          >
            <div
              data-theme="night"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">night</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="coffee"
            data-act-class="outline"
          >
            <div
              data-theme="coffee"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">coffee</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            className="outline-base-content overflow-hidden rounded-lg outline-2 outline-offset-2"
            data-set-theme="winter"
            data-act-class="outline"
          >
            <div
              data-theme="winter"
              className="bg-base-100 text-base-content w-full cursor-pointer font-sans"
            >
              <div className="grid grid-cols-5 grid-rows-3">
                <div className="col-span-5 row-span-3 row-start-1 flex gap-1 py-3 px-4">
                  <div className="flex-grow text-sm font-bold">winter</div>
                  <div className="flex flex-shrink-0 flex-wrap gap-1">
                    <div className="bg-primary w-2 rounded" />
                    <div className="bg-secondary w-2 rounded" />
                    <div className="bg-accent w-2 rounded" />
                    <div className="bg-neutral w-2 rounded" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> */}
    </>
  )
}