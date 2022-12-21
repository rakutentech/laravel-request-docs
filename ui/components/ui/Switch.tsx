import React, { useState } from "react"
import { Switch } from "@headlessui/react"

interface SwitchButtonProps {
  checked: boolean
  onChange: (enabled: boolean) => void
  label?: string
}


export default function SwitchButton({ checked, onChange, label }: SwitchButtonProps) {
  return (
    <Switch
      checked={checked}
      onChange={onChange}
      className={`${checked ? "bg-base-content/60" : "bg-base-content/30"}
          relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2  focus-visible:ring-white focus-visible:ring-opacity-75`}
    >
      <span className="sr-only">{label}</span>
      <span
        aria-hidden="true"
        className={`${checked ? "translate-x-[1.25rem]" : "translate-x-0"}
            pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out`}
      />
    </Switch>
  )
}