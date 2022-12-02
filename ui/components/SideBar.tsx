"use client"

import React from "react"

interface Props {
  children?: React.ReactNode;
  data: APIInfo[];
}

const apiMethodColor: { [key: string]: string[] } = {
  GET: ["text-info"],
  POST: ["text-success"],
  PUT: ["text-warning"],
  PATCH: ["text-warning"],
  DELETE: ["text-error"],
  HEAD: ["text-info"],
}

export default function SideBar({ data }: Props) {
  const basePath = (typeof window === "undefined") ? "" :  window.location.origin + window.location.pathname
  return (
    <aside className="w-80 pt-2 pb-4">
      <ul className="menu menu-compact flex flex-col p-0 px-4">
        <li className="menu-title uppercase font-bold text-sm tracking-wider pb-1">API list</li>
        {data.map((item) => (
          <a
            key={`${item.httpMethod}_${item.uri}`}
            href={basePath + "#" + encodeURIComponent(`${item.httpMethod}_${item.uri}`)}
          >
            <li className="">
              <span className="flex flex-row px-0 py-1 hover:font-semibold hover:bg-inherit">
                <span className={`${apiMethodColor[item.httpMethod].join(" ")} uppercase text-xs w-12 p-0 flex flex-row-reverse`}>{item.httpMethod}</span>
                <span className="flex-1 p-0 text-sm">{item.uri}</span>
              </span>
            </li>
          </a>
        ))}
      </ul>
    </aside>
  )
}