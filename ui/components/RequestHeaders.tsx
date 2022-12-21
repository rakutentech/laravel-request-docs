"use client"

import React, { Fragment, SetStateAction, useCallback, useEffect, useMemo } from "react"
import { Tab } from "@headlessui/react"
import CodeEditor from "./CodeEditor"
import { classNames, getLocalStorageJSONData, getLocalStorageKey } from "../utils/utils"
import KeyValueEditor from "./KeyValueEditor"

const tabs = ["Key-Value Edit", "Bulk Edit"]

export default function RequestHeaders({ api }: { api: IAPIInfo }) {
  const localStorageKey = getLocalStorageKey(api, "Headers")
  const [code, setCode] = React.useState<IKeyValueParams[]>(
    getLocalStorageJSONData(localStorageKey)
  )
  const value = useMemo(() => {
    const raw = JSON.parse(JSON.stringify(code || []))
    return JSON.stringify(
      raw.filter((item: IKeyValueParams) => !item.deleted && !item.disabled)
        .reduce((acc: any, item: IKeyValueParams) => ({ ...acc, ...{[item.key]: item.value }}), { }),
      null,
      2,
    )
  }, [code])

  const setValue = (val?: string) => {
    let raw = {} as any
    try {
      raw = JSON.parse(val || "{}")
    } catch (e) {
      console.log("PARSE ERROR! DO NO MORE")
      return
    }
    let newValue = [] as IKeyValueParams[]
    setCode((prevState: IKeyValueParams[]) => {
      const keySet = new Set([...Object.keys(raw), ...(prevState?.map((item) => item.key) || [])])
      newValue = Array.from(keySet).reduce<IKeyValueParams[]>((acc: any, item: string) => {
        const temp = acc || []
        const prevStateItem = prevState.find((i) => i.key === item)
        return [...temp, {
          key: item,
          value: raw[item] || prevStateItem?.value || "",
          deleted: !prevStateItem?.disabled && !raw[item],
          disabled: prevStateItem?.disabled || false,
        }]
      }, [] as IKeyValueParams[])
      console.log({ newValue })
      return newValue
    })
  }

  useEffect(() => {
    localStorage.setItem(localStorageKey, JSON.stringify(code || []))
  }, [code, localStorageKey])

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
            <CodeEditor height="20rem" value={value} onChange={setValue} />
          </Tab.Panel>
        </Tab.Panels>
      </Tab.Group>
    </div>
  )
}

