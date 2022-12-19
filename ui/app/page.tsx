"use client"

import React, { createRef, useEffect, useState } from "react"
import SideBar from "../components/SideBar"
import APICard from "../components/APICard"
import { getAPIInfoId } from "../utils/utils"

async function getData(): Promise<IAPIInfo[]> {
  try {
    const response = await fetch("http://localhost:3000/vendor/request-docs/api/sample")
    return response.json()
  } catch (error) {
    console.error(error)
    return []
  }
}

function useAPIInfoData(): IAPIInfo[] {
  const [apiInfoData, setAPIInfoData] = useState<IAPIInfo[]>([])
  useEffect(() => {
    async function setData() {
      const data = await getData()
      setAPIInfoData(data)
    }
    setData()
  }, [])
  return apiInfoData
}

export default function Home() {
  const data = useAPIInfoData()
  const [activeItemID, setActiveItemID] = useState(getAPIInfoId(data[0]))
  const baseURL = process.env.NEXT_PUBLIC_BASE_URL ||
    (typeof window !== "undefined") ? `${window.location.protocol}//${window.location.host}` : ""

  const refs = data.reduce((refsObj, item) => {
    refsObj[getAPIInfoId(item)] = createRef<HTMLElement>()
    return refsObj
  }, {} as { [key: string]: React.RefObject<HTMLElement> })

  const handleClick = (id: string) => {
    refs[id].current?.scrollIntoView({
      behavior: "smooth",
      block: "center",
    })
    if (typeof window !== "undefined") window.location.hash = id
  }

  return (
    <div className="mt-2" id="main-container">
      <div className="drawer drawer-mobile">
        <input id="side-bar-drawer" type="checkbox" className="drawer-toggle" />
        <div className="drawer-content flex flex-col items-center bg-base-100 ml-4 scroll-smooth">
          <label htmlFor="side-bar-drawer" className="btn btn-primary drawer-button lg:hidden">Open drawer</label>
          <main className="w-full">
            <div className="mx-auto">
              {data.map((item) => (
                <APICard
                  key={getAPIInfoId(item)}
                  item={item}
                  refs={refs}
                  activeItemID={activeItemID}
                  setActiveItemID={setActiveItemID}
                  baseURL={baseURL}
                />
              ))}
            </div>
          </main>
        </div>
        <div className="drawer-side bg-base-100">
          <label htmlFor="side-bar-drawer" className="drawer-overlay"></label>
          <SideBar data={data} handleClick={handleClick} activeItemID={activeItemID} />
        </div>
      </div>
    </div>
  )
}
