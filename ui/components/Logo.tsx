import React from "react"
import { Quicksand, Antonio } from "@next/font/google"
import Image from "next/image"

const basePath = process.env.NEXT_PUBLIC_BASE_PATH

const brandFont = Quicksand({ subsets: ["latin"] })
const logoFont = Antonio({ subsets: ["latin"] })
export default function Logo() {
  return (
    <div className="group flex align-middle text-base">
      <div className={logoFont.className + " mr-2 text-3xl font-black leading-none uppercase"}>
        lrd
      </div>
      <div className={brandFont.className + " flex flex-col my-auto leading-none cursor-default font-black"}>
        <span className="text-sm  mb-[-.65rem] p-0 h-fit">
          <span>Laravel</span>
        </span>
        <span className="lowercase text-lg h-fit">request docs</span>
      </div>
    </div>
  )
}