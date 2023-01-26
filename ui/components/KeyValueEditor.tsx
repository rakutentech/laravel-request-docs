"use client"

import React, { useState } from "react"
import Table from "./ui/Table"
import { TrashIcon } from "@heroicons/react/24/outline"
import { classNames } from "@/utils/utils"
import SwitchButton from "./ui/Switch"

interface KeyValueEditorProps {
  className?: string;
  value: IKeyValueParams[];
  onChange: (value: IKeyValueParams[]) => void;
}

interface AddKeyValueFormProps {
  data?: IKeyValueParams;
  setData?: (data: IKeyValueParams) => void;
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
  const { data, onChange, newRow, className } = props
  const [keyValueData, setKeyValueData] = useState(data || initialKeyValueData)

  return (
    <Table.Row className={classNames("divide-x divide-base-content/20", className || "")}>
      <td className="border-b-0">
        {/** TODO:  
           * Support using keyboard to navigate between rows
          */}
        <input
          type="text"
          value={keyValueData?.key}
          name={"key" + (newRow ? "_new" : "")}
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
          name={"value" + (newRow ? "_new" : "")}
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
      <td className="border-b-0 px-2 w-fit h-[2.5rem] flex items-center">
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
              className="flex px-2 items-center justify-center hover:text-primary focus:text-primary"
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

export default function KeyValueEditor(props: KeyValueEditorProps) {
  const { value: data, onChange: setData } = props
  const headCellClass = "sticky bg-base-content/10 backdrop-blur-2xl backdrop-filter top-0 z-10 text-left px-4 uppercase text-base-content/75"
  return (
    <div className={classNames(
      props.className || "",
      "ring-1 ring-base-content/20"
    )}>
      <Table className="w-full divide-y divide-base-content/20 text-sm auto">
        <Table.Head className="text-xs">
          <Table.Row>
            <Table.HeadCell className={classNames(headCellClass, "rounded-tl-md")}>Key</Table.HeadCell>
            <Table.HeadCell className={headCellClass}>Value</Table.HeadCell>
            <Table.HeadCell className={classNames(headCellClass, "w-0 rounded-tr-md")}>Actions</Table.HeadCell>
          </Table.Row>
        </Table.Head>
        <Table.Body className="divide-y divide-base-content/20">
          {data?.map((item, index) => (
            <AddKeyValueForm
              key={index}
              data={item}
              className={classNames(item.deleted ? "hidden" : "")}
              onChange={(val, _) => {
                let newData = [...data]
                if (JSON.stringify(newData) === JSON.stringify(val)) return
                newData[index] = val
                setData(newData)
              }} />)
          )}
          {/** TODO:  
           * Change tab action on Key input when adding a new row
           * Currently tab is jumping
          */}
          <AddKeyValueForm newRow data={initialKeyValueData} onChange={(val, setChildData) => {
            if (!val.key && !val.value) return
            const newData = [...(data || []), { key: val.key || "", value: val.value || "" }]
            setData(newData)
            if (setChildData) setChildData(initialKeyValueData)
          }} />
        </Table.Body>
      </Table>
    </div>
  )
}