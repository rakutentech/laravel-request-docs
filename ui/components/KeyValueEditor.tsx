"use client"

import React, { useEffect, useState } from "react"
import Table from "./Table"
import { TrashIcon, NoSymbolIcon } from "@heroicons/react/24/outline"
import { classNames, getLocalStorageJSONData } from "../utils/utils"
import SwitchButton from "./ui/Switch"

type Intent = "Headers" | "Params" | "Body"

interface KeyValueEditorProps {
  api: IAPIInfo;
  intent: Intent;
}

interface AddKeyValueFormProps {
  data?: IKeyValueParams;
  setData?: (data: IKeyValueParams) => void;
  intent: Intent;
  className?: string;
  onChange: (
    data: IKeyValueParams,
    setKeyValueData?: (data: IKeyValueParams) => void,
  ) => void;
  newRow?: boolean;
}

const initialKeyValueData: IKeyValueParams = {
  key: "",
  value: "",
  disabled: false,
  deleted: false,
}

function AddKeyValueForm(props: AddKeyValueFormProps) {
  const { data, intent, onChange, newRow, className } = props
  const [keyValueData, setKeyValueData] = useState(data || initialKeyValueData)
  return (
    <Table.Row className={classNames("divide-x divide-base-content/20", className || "")}>
      <td className="border-b-0">
        <input
          type="text"
          value={keyValueData?.key}
          name={`${intent}_key`}
          className={classNames(
            "w-full h-full pl-4 py-2 focus:ring-2 bg-transparent",
            keyValueData?.disabled ? "text-base-content/40" : "",
          )}
          placeholder="Key"
          onChange={(e) => setKeyValueData({ ...keyValueData, key: e.target.value })}
          onBlur={(e) => {
            console.log("onBlur Key", e.target.value, keyValueData)
            onChange(keyValueData, setKeyValueData)
          }}
          disabled={keyValueData?.disabled}
        />
      </td>
      <td className="border-b-0">
        <input
          type="text"
          value={keyValueData?.value}
          name={`${intent}_value`}
          className={classNames(
            "w-full h-full pl-4 py-2 focus:ring-2 bg-transparent",
            keyValueData?.disabled ? "text-base-content/40" : "",
          )}
          placeholder="Value"
          onChange={(e) => setKeyValueData({ ...keyValueData, value: e.target.value })}
          onBlur={(e) => {
            console.log("onBlur Value", e.target.value, keyValueData)
            onChange(keyValueData, setKeyValueData)
          }}
          disabled={keyValueData?.disabled}
        />
      </td>
      <td className="border-b-0 px-2 pt-1 w-fit h-8 flex items-center">
        {!newRow && (// Disable and Delete buttons are not shown for new row
          <>
            <SwitchButton
              checked={!keyValueData?.disabled}
              onChange={(enabled) => {
                console.log("Switch", enabled)
                setKeyValueData({ ...keyValueData, disabled: !enabled })
                onChange({ ...keyValueData, disabled: !enabled }, setKeyValueData)
              }}
              label={keyValueData?.disabled ? "Enable" : "Disable"}
            />
            <button
              type="button"
              className="flex p-2 items-center justify-center hover:text-primary focus:text-primary"
              onClick={() => {
                setKeyValueData({ ...keyValueData, deleted: true })
                onChange({ ...keyValueData, deleted: true }, setKeyValueData)
              }}
            >
              <span className="sr-only">Delete</span>
              <TrashIcon className="h-5 w-5" aria-hidden="true" />
            </button>
          </>
        )}
      </td>
    </Table.Row>
  )
}

const getLocalStorageKey = (api: IAPIInfo, intent: Intent) => {
  return `__lrd_${api.httpMethod}_${api.uri}_${intent}`
}




export default function KeyValueEditor(props: KeyValueEditorProps) {
  const { api, intent } = props
  const localStorageKey = getLocalStorageKey(api, intent)
  const [data, setData] = useState<IKeyValueParams[]>(getLocalStorageJSONData(localStorageKey) || [])

  return (
    <div className="overflow-hidden ring-1 ring-base-content/20 rounded-md">
      <Table className="w-full divide-y divide-base-content/20 text-sm auto">
        <Table.Head className="bg-base-content/10 text-xs">
          <Table.Row className="divide-x divide-base-content/20">
            <Table.HeadCell className="text-left px-4 uppercase text-base-content/75">Key</Table.HeadCell>
            <Table.HeadCell className="text-left px-4 uppercase text-base-content/75">Value</Table.HeadCell>
            <Table.HeadCell className="text-left px-4 uppercase text-base-content/75 w-0">Actions</Table.HeadCell>
          </Table.Row>
        </Table.Head>
        <Table.Body className="divide-y divide-base-content/20">
          {data.map((item, index) => (
            <AddKeyValueForm
              key={index}
              data={item}
              intent={intent}
              className={classNames(item.deleted ? "hidden" : "")}
              onChange={(val, setChildData) => {
                let newData = [...data]
                if (JSON.stringify(newData) === JSON.stringify(val)) return
                newData[index] = val
                if (val.deleted) {
                  localStorage.setItem(localStorageKey, JSON.stringify(newData.filter((_, i) => i !== index)))
                  setData(newData)
                  return
                }
                localStorage.setItem(localStorageKey, JSON.stringify(newData))
                setData(newData)
              }} />)
          )}
          <AddKeyValueForm newRow data={initialKeyValueData} intent={intent} onChange={(val, setChildData) => {
            if (!val.key || !val.value) return
            const newData = [...data, val]
            setData(newData)
            localStorage.setItem(localStorageKey, JSON.stringify(newData))
            if (setChildData) setChildData(initialKeyValueData)
          }} />
        </Table.Body>
      </Table>
    </div>
  )
}