"use client"

import React, { Fragment } from "react"
import { Tab } from "@headlessui/react"
import CodeEditor from "./CodeEditor"
import { classNames } from "../utils/utils"
import KeyValueEditor from "./KeyValueEditor"

const tabs = ["Key-Value Edit", "Bulk Edit"]

export default function RequestHeaders({ api } : {api: IAPIInfo}) {
  return (
    <div className="w-full h-full">
      <Tab.Group>
        <Tab.List>
          <nav className="flex w-full justify-end space-x-2 my-1" aria-label="Tabs">
            {tabs.map((tab) => (
              <Tab key={tab} as={Fragment}>
                {({ selected }) => (
                  <button className={classNames(
                    selected ? "text-primary" : "text-base-content/60",
                    "px-2 py-1.5 font-medium hover:text-base-content focus:text-primary rounded-md text-xs uppercase"
                  )}>
                    {tab}
                  </button>
                )}
              </Tab>
            ))}
          </nav>
        </Tab.List>
        <Tab.Panels>
          <Tab.Panel>
            <KeyValueEditor intent="Headers" api={api} />
          </Tab.Panel>
          <Tab.Panel>
            <CodeEditor height="20rem" />
          </Tab.Panel>
        </Tab.Panels>
      </Tab.Group>
    </div>
  )
}

