"use client"

import React, { useEffect, useState } from "react"
import Table from "./Table"
import { TrashIcon, NoSymbolIcon } from "@heroicons/react/24/outline"

type Intent = "Headers" | "Params" | "Body"

interface KeyValueEditorProps {
  api: IAPIInfo;
  intent: Intent;
}

interface AddKeyValueFormProps {
  data?: IKeyValueParams;
  setData?: (data: IKeyValueParams) => void;
  intent: Intent;
  onChange: (data: IKeyValueParams, setKeyValueData: (data: IKeyValueParams) => void) => void;
}

const initialKeyValueData: IKeyValueParams = {
  key: "",
  value: "",
  disabled: false,
}

function AddKeyValueForm(props: AddKeyValueFormProps) {
  const { data, intent, onChange } = props
  const [keyValueData, setKeyValueData] = useState(data as IKeyValueParams)
  return (
    <Table.Row className="divide-x divide-base-content/20">
      <td className="border-b-0">
        <input
          type="text"
          value={keyValueData?.key}
          name={`${intent}_key`}
          className="w-full h-full pl-4 py-2 focus:ring-2 bg-transparent"
          placeholder="Key"
          onChange={(e) => setKeyValueData({ ...keyValueData, key: e.target.value })}
          onBlur={(e) => {
            console.log("onBlur Key", e.target.value, keyValueData)
            onChange(keyValueData, setKeyValueData)
          }}
        />
      </td>
      <td className="border-b-0">
        <input
          type="text"
          value={keyValueData?.value}
          name={`${intent}_value`}
          className="w-full h-full pl-4 py-2 focus:ring-2 bg-transparent"
          placeholder="Value"
          onChange={(e) => setKeyValueData({ ...keyValueData, value: e.target.value })}
          onBlur={(e) => {
            console.log("onBlur Value", e.target.value, keyValueData)
            onChange(keyValueData, setKeyValueData)
          }}
        />
      </td>
      <td className="border-b-0 px-2 w-fit flex">
        <button type="button" className="flex p-2 items-center justify-center">
          <span className="sr-only">Disable</span>
          <NoSymbolIcon className="h-5 w-5" aria-hidden="true" />
        </button>
        <button
          type="button"
          className="flex p-2 items-center justify-center"
          onClick={() => {
            console.log("Delete")

          }}
        >
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
          <AddKeyValueForm data={initialKeyValueData} intent={intent} onChange={(val, setChildData) => {
            if (!val.key || !val.value) return
            const newData = [...data, val]
            setData(newData)
            localStorage.setItem(localStorageKey, JSON.stringify(newData))
            setChildData({ key: "", value: "" } as IKeyValueParams)
          }} />
        </Table.Body>
      </Table>
    </div>
  )
}