import React from "react"
import { APIRequest } from "../components/APICode"
import APIParamTable from "../components/APIParamTable"
import APIRefTable from "../components/APIRefTable"
import APIDocBlock from "../components/APIDocBlock"
import SideBar from "../components/SideBar"

async function getData(): Promise<APIInfo[]> {
  try {
    const response = await fetch("http://localhost:3000/vendor/request-docs/api/sample")
    return response.json()
  } catch (error) {
    console.error(error)
    return []
  }
}

const apiMethodColor: { [key: string]: string[] } = {
  GET: ["text-info"],
  POST: ["text-success"],
  PUT: ["text-warning"],
  PATCH: ["text-warning"],
  DELETE: ["text-error"],
  HEAD: ["text-info"],
}

export default async function Home() {
  const data = await getData()
  return (
    <div className="mt-2">
      <div className="drawer drawer-mobile">
        <input id="side-bar-drawer" type="checkbox" className="drawer-toggle" />
        <div className="drawer-content flex flex-col items-center bg-base-100 ml-4 scroll-smooth">
          <label htmlFor="side-bar-drawer" className="btn btn-primary drawer-button lg:hidden">Open drawer</label>
          <main className="w-full">
            <div className="mx-auto">
              {data.map((item) => {
                const ruleList = Object.keys(item.rules)
                return (
                  <div
                    id={encodeURIComponent(`${item.httpMethod}_${item.uri}`)}
                    key={`${item.httpMethod}_${item.uri}`}
                    className="card w-[96%] shadow-none bg-base-50"
                  >
                    <div className="card-body">
                      <h2 className="card-title">
                        <span className="flex flex-row hover:font-semibold hover:bg-inherit items-center">
                          <span className={`${apiMethodColor[item.httpMethod]?.join(" ")} uppercase text-sm w-fit pr-2 flex flex-row mt-0.5`}>{item.httpMethod}</span>
                          <span className="flex-1 p-0 text-md items-center">{item.uri}</span>
                        </span>
                      </h2>
                      <APIDocBlock>{item.docBlock}</APIDocBlock>
                      <div className="py-1"></div>
                      <h4 className="">API Reference</h4>
                      <APIRefTable
                        controller={item.controller_full_path}
                        method={item.method}
                        middlewares={item.middlewares}
                      />
                      {ruleList.length > 0 ? (
                        <APIParamTable params={item.rules} />
                      ) : null}
                      <div className="py-3"></div>
                      <APIRequest />
                      <div className="card-actions justify-end">
                        <button className="btn btn-sm btn-primary">Try</button>
                      </div>
                    </div>
                  </div>
                )
              })}

            </div>
          </main>
        </div>
        <div className="drawer-side bg-base-100">
          <label htmlFor="side-bar-drawer" className="drawer-overlay"></label>
          <SideBar data={data} />
        </div>
      </div>

    </div>
  )
}
