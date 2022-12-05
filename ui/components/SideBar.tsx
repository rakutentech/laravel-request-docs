"use client"

import React from "react"
import { getAPIInfoId } from "../utils/utils"

interface Props {
  children?: React.ReactNode;
  data: IAPIInfo[];
  handleClick: (id: string) => void;
  activeItemID?: string;
}

const apiMethodColor: { [key: string]: string[] } = {
  GET: ["text-info"],
  POST: ["text-success"],
  PUT: ["text-warning"],
  PATCH: ["text-warning"],
  DELETE: ["text-error"],
  HEAD: ["text-info"],
}

export default function SideBar({ data, handleClick, activeItemID }: Props) {
  return (
    <aside className="w-80 pt-2 pb-4">
      <ul className="menu menu-compact flex flex-col p-0 px-4">
        <li className="menu-title uppercase font-bold text-sm tracking-wider pb-1">API list</li>
        {data.map((item) => (
          <button
            type="button"
            key={getAPIInfoId(item)}
            onClick={() => handleClick(getAPIInfoId(item))}
            className={getAPIInfoId(item) === activeItemID ? "font-semibold" : ""}
          >
            <li className="">
              <span className="flex flex-row px-0 py-1 hover:font-semibold hover:bg-inherit">
                <span className={`${apiMethodColor[item.httpMethod].join(" ")} uppercase text-xs w-12 p-0 flex flex-row-reverse`}>{item.httpMethod}</span>
                <span className="flex-1 p-0 text-sm text-left">{item.uri}</span>
              </span>
            </li>
          </button>
        ))}
      </ul>
    </aside>
  )
}