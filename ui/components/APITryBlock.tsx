"use client"

import React, { Fragment, useState } from "react"
import { Disclosure, Transition } from "@headlessui/react"
import { EyeDropperIcon } from "@heroicons/react/24/outline"
import { ChevronRightIcon } from "@heroicons/react/20/solid"
import AddressBar from "./AddressBar"
import RequestTabs from "./RequestTabs"

interface APITryBlockProps {
  item: IAPIInfo;
  baseURL: string;
}

function classNames(...classes: string[]) {
  return classes.filter(Boolean).join(" ")
}

export default function APITryBlock({ item, baseURL }: APITryBlockProps) {
  const [url, setURL] = useState(`${baseURL}/${item.uri}`)
  return (
    <div className="w-full py-4">
      <div className="mx-auto w-full bg-base-100">
        <Disclosure>
          {({ open }) => (
            <>
              <Disclosure.Button className={classNames("flex w-full items-center justify-between px-4 py-2 bg-base-content/5", 
                "hover:bg-primary/10 rounded-md text-left text-base-content group/try")}>
                <h4 className={classNames(
                  open ? "font-semibold" : "",
                  "flex items-center space-x-2 text-base group-hover/try:text-primary"
                )}>
                  <EyeDropperIcon className="h-4 w-4" />
                  <span>Try</span>
                </h4>
                <ChevronRightIcon
                  className={`${open ? "rotate-90 transform" : ""
                    } h-5 w-5 text-base-content/50 group-hover/try:text-primary`}
                />
              </Disclosure.Button>
              <Transition
                as={Fragment}
                enter="transition duration-400 ease-out"
                enterFrom="transform -translate-x-3 opacity-0"
                enterTo="transform translate-x-0 opacity-100"
                leave="transition duration-400 ease-out"
                leaveFrom="transform translate-x-0 opacity-100"
                leaveTo="transform -translate-x-3 opacity-0"
              >
                <Disclosure.Panel className="">
                  <div className="py-4">
                    <h5 className="block text-xs uppercase font-semibold text-base-content/80 tracking-wider pb-2">
                      Request
                    </h5>
                    <AddressBar method={item.httpMethod} url={url} setURL={setURL} />
                    <RequestTabs item={item} />
                  </div>
                  <div className="py-4">
                    <h5 className="block text-xs uppercase font-semibold text-base-content/80 tracking-wider pb-2">
                      Response
                    </h5>
                  </div>
                </Disclosure.Panel>
              </Transition>
            </>
          )}
        </Disclosure>
      </div>
    </div>
  )
}