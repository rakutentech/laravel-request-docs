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
    <div className="my-1">
      <div className="mt-1 flex rounded-md shadow-sm">
        <span className="inline-flex items-center rounded-l-md border border-r-0 border-base-content/20 bg-base-content/10 px-3 text-base-content/80 font-semibold sm:text-sm">
          {method}
        </span>
        <input
          type="text"
          name="request-url"
          id="request-url"
          className={`block w-full flex-1 rounded-none border border-base-content/20 
            bg-base-content/10 px-3 py-2 focus:bg-inherit focus:outline-none sm:text-sm`}
          value={url}
          onChange={(e) => setURL(e.target.value)}
        />
        <button
          type="button"
          className={`relative -ml-px inline-flex items-center space-x-2 rounded-r-md border border-base-content/20
          bg-primary px-4 py-2 text-sm font-medium text-primary-content hover:bg-primary-focus
          focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary`}
        >
          <span>Send</span>
          <PaperAirplaneIcon className="h-5 w-5 text-primary-content -rotate-45 transform" aria-hidden="true" />
        </button>
      </div>
    </div>
  )
}