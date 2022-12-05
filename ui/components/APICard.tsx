"use client"

import React, { RefObject, useEffect } from "react"
import { useRouter } from "next/navigation"
import { getAPIInfoId } from "../utils/utils"
import APIDocBlock from "./APIDocBlock"
import APIParamTable from "./APIParamTable"
import APIRefTable from "./APIRefTable"
import APITryBlock from "./APITryBlock"

export interface APICardProps {
  item: IAPIInfo;
  baseURL: string;
  activeItemID?: string;
  setActiveItemID: (id: string) => void;
  refs: { [key: string]: RefObject<HTMLElement> };
}

const apiMethodColor: { [key: string]: string[] } = {
  GET: ["text-info"],
  POST: ["text-success"],
  PUT: ["text-warning"],
  PATCH: ["text-warning"],
  DELETE: ["text-error"],
  HEAD: ["text-info"],
}


export default function APICard({ item, activeItemID, setActiveItemID, refs, baseURL }: APICardProps) {
  const ruleList = Object.keys(item.rules)
  const elementID = getAPIInfoId(item)
  const router = useRouter()

  // Auto change URI & highlight NavItems on scroll
  useEffect(() => {
    const observerConfig = {
      rootMargin: "-5% 0px -95% 0px",
      threshold: [0.0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0],
    }
    const handleIntersection = (entries: IntersectionObserverEntry[]) => {
      entries.forEach((entry) => {
        if (entry.target.id !== activeItemID && entry.isIntersecting) {
          setActiveItemID(entry.target.id)
        }

      })
    }
    const observer = new IntersectionObserver(
        handleIntersection,
        observerConfig)
    if(!!refs[elementID].current) observer.observe(refs[elementID].current)
    return () => observer.disconnect() // Cleanup the observer if  component unmount.
  }, [activeItemID, elementID, refs, setActiveItemID])

  return (
    <section
      data-scrollspy
      ref={refs[elementID]}
      id={elementID}
      className="api-card ml-2 mr-10 my-6 divide-y divide-base-content/20 overflow-hidden rounded-lg bg-base-100 border border-base-content/10 group/api-card focus:border-primary">
      <div className="flex flex-col p-8 gap-2">
        <h2 className="flex items-center gap-2 font-semibold text-xl">
          <span className="flex flex-row hover:font-semibold hover:bg-inherit items-center">
            <span className={`${apiMethodColor[item.httpMethod]?.join(" ")} uppercase text-sm w-fit pr-2 flex flex-row mt-0.5`}>{item.httpMethod}</span>
            <span className="flex-1 p-0 text-md items-center">{item.uri}</span>
          </span>
        </h2>
        <APIDocBlock>{item.docBlock}</APIDocBlock>
        <APIRefTable
          controller={item.controller_full_path}
          method={item.method}
          middlewares={item.middlewares}
        />
        {ruleList.length > 0 ? (
          <APIParamTable params={item.rules} />
        ) : null}
        <APITryBlock item={item} baseURL={baseURL} />
      </div>
    </section>

  )
}