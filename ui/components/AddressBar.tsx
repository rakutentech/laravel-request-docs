import React from "react"
import { PaperAirplaneIcon } from "@heroicons/react/24/outline"

export interface AddressBarProps {
  method: string;
  url: string;
  setURL: (url: string) => void;
  queryParams?: { [key: string]: string }
}

export default function AddressBar({ method, url, setURL, queryParams }: AddressBarProps) {
  return (
    <div className="my-1 flex w-full gap-2 items-center">
      <div className="group flex rounded-md shadow-sm w-full focus-within:ring focus-within:ring-primary/50">
        <span className="inline-flex items-center rounded-l-md border border-r-0 border-base-content/20 bg-base-content/10 px-3 text-base-content/80 font-semibold sm:text-sm">
          {method}
        </span>
        <input
          type="text"
          name="request-url"
          id="request-url"
          className={`block w-full flex-1 rounded-r-md border border-base-content/20 
            bg-base-content/10 px-3 py-3 focus:bg-inherit focus:ring-0 sm:text-sm`}
          value={url}
          onChange={(e) => setURL(e.target.value)}
        />
      </div>
      <div className="w-fit">
        <button
          type="button"
          className={`relative inline-flex items-center space-x-2 rounded border border-base-content/20
          bg-primary px-4 py-2.5 text-sm font-medium text-primary-content hover:bg-primary-focus`}
        >
          <span>Send</span>
          <PaperAirplaneIcon className="h-6 w-6 text-primary-content -rotate-45 transform pb-1" aria-hidden="true" />
        </button>
      </div>
    </div>
  )
}