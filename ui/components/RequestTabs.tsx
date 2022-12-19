"use client"

import React, { useState } from "react"

interface RequestTabsProps {
  item: IAPIInfo;
}

const getTabs = (method: string) => {
  const res = [
    { name: "Header", href: "#", default: false },
  ]
  switch (method) {
    case "POST":
    case "PUT":
    case "PATCH":
      res.push({ name: "Body", href: "#", default: true })
      break
    default:
      res.push({ name: "Param", href: "#", default: true })
      break
  }
  return res
}

function classNames(...classes: string[]) {
  return classes.filter(Boolean).join(" ")
}

export default function RequestTabs({ item }: RequestTabsProps) {
  const tabs = getTabs(item.httpMethod)
  const [activeTab, setActiveTab] = useState(tabs.filter(tab => !!tab.default)[0].name)
  return (
    <div className="my-4">
      <div className="sm:hidden">
        <label htmlFor="tabs" className="sr-only">
          Select a tab
        </label>
        {/* Use an "onChange" listener to redirect the user to the selected tab URL. */}
        <select
          id="tabs"
          name="tabs"
          className={`block w-full rounded-md border bg-inherit border-base-content/20 py-2 pl-3 pr-10 text-base-content 
          focus:border-primary focus:ring-2 focus:outline-none focus:ring-primary sm:text-sm`}
          defaultValue={activeTab}
          onChange={(e) => setActiveTab(e.currentTarget.value)}
        >
          {tabs.map((tab) => (
            <option key={tab.name}>{tab.name}</option>
          ))}
        </select>
      </div>
      <div className="hidden sm:block">
        <div className="border-b border-base-content/20">
          <nav className="-mb-px flex space-x-8" aria-label="Tabs">
            {tabs.map((tab) => (
              <button
                key={tab.name}
                type="button"
                className={classNames(
                  tab.name === activeTab
                    ? "border-primary text-primary"
                    : "border-transparent text-base-content/80 hover:text-base-content hover:border-base-content/20",
                  "whitespace-nowrap py-1 px-1 border-b-2 font-medium text-xs"
                )}
                aria-current={tab.name === activeTab ? "page" : undefined}
                onClick={() => setActiveTab(tab.name)}
              >
                {tab.name}
              </button>
            ))}
          </nav>
        </div>
      </div>
    </div>
  )
}