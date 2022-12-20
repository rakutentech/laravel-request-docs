"use client"

import React, { useEffect, useState, useSyncExternalStore } from "react"
import Table from "./Table"
import { TrashIcon, NoSymbolIcon } from "@heroicons/react/24/outline"

type Intent = "Headers" | "Params" | "Body"

interface KeyValueEditorProps {
  api: IAPIInfo;
  intent: Intent;
}

interface AddKeyValueFormProps {
  data?: IKeyValueParams;
  intent: Intent;
  onChange: (data: IKeyValueParams) => void;
}

function AddKeyValueForm(props: AddKeyValueFormProps) {
  const { data, intent, onChange } = props
  const [key, setKey] = useState(data?.key || "")
  const [value, setValue] = useState(data?.value || "")

  return (
    <Table.Row className="divide-x divide-base-content/20">
      <td className="border-b-0">
        <input
          type="text"
          value={key}
          name={`${intent}_key`}
          className="w-full h-full pl-4 py-2 focus:ring-2"
          placeholder="Key"
          onChange={(e) => setKey(e.target.value)}
          onBlur={(e) => {
            console.log("onBlur Key", e.target.value, key, data)
            onChange({
              ...(data || {}) as IKeyValueParams, key: key
            })
          }}
        />
      </td>
      <td className="border-b-0">
        <input
          type="text"
          value={value}
          name={`${intent}_value`}
          className="w-full h-full pl-4 py-2 focus:ring-2"
          placeholder="Value"
          onChange={(e) => setValue(e.target.value)}
          onBlur={(e) => {
            console.log("onBlur Value", e.target.value, value, data)
            onChange({
              ...(data || {}) as IKeyValueParams, value: e.target.value
            })
          }}
        />
      </td>
      <td className="border-b-0 px-2 w-fit flex">
        <button type="button" className="flex p-2 items-center justify-center">
          <span className="sr-only">Disable</span>
          <NoSymbolIcon className="h-5 w-5" aria-hidden="true" />
        </button>
        <button type="button" className="flex p-2 items-center justify-center">
          <span className="sr-only">Delete</span>
          <TrashIcon className="h-5 w-5" aria-hidden="true" />
        </button>
      </td>
    </Table.Row>
  )
}

const getLocalStorageKey = (api: IAPIInfo, intent: Intent) => {
  return `__lrd_${api.httpMethod}_${api.uri}_${intent}`
}

const getLocalStorageData = (key: string): IKeyValueParams[] => {
  const localStorageData = localStorage.getItem(key)
  if (localStorageData) {
    return JSON.parse(localStorageData)
  }
  return []
}


export default function KeyValueEditor(props: KeyValueEditorProps) {
  const { api, intent } = props
  const localStorageKey = getLocalStorageKey(api, intent)
  const [data, setData] = useState<IKeyValueParams[]>(getLocalStorageData(localStorageKey))

  return (
    <div className="overflow-hidden ring-1 ring-base-content/20 rounded-md">
      <Table className="w-full divide-y divide-base-content/20 text-xs">
        <Table.Head className="bg-base-content/5">
          <Table.Row className="divide-x divide-base-content/20">
            <Table.HeadCell className="text-left px-4 uppercase text-base-content/75">Key</Table.HeadCell>
            <Table.HeadCell className="text-left px-4 uppercase text-base-content/75">Value</Table.HeadCell>
            <Table.HeadCell className="text-left px-4 uppercase text-base-content/75 w-fit">Actions</Table.HeadCell>
          </Table.Row>
        </Table.Head>
        <Table.Body className="divide-y divide-base-content/20">
          {data?.map((item, index) => {
            return (
              <AddKeyValueForm key={index} data={item} intent={intent} onChange={(val) => {
                const newData = [...data]
                newData[index] = val
                setData(newData)
                localStorage.setItem(localStorageKey, JSON.stringify(newData))
              }} />

            )
          })
          }
          <AddKeyValueForm intent={intent} onChange={(val) => {
            console.log("AddKeyValueForm onChange", val)
            if (!val.key || !val.value) return
            const newData = [...data, val]
            setData(newData)
            localStorage.setItem(localStorageKey, JSON.stringify(newData))
          }} />
        </Table.Body>
      </Table>
    </div>
  )
}