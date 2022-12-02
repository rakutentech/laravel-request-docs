import React from "react"
import { Quicksand } from "@next/font/google"
import Image from "next/image"

const basePath = process.env.NEXT_PUBLIC_BASE_PATH

const logoFont = Quicksand({ subsets: ["latin"] })
export default function Logo() {
  return (
    <div className={logoFont.className}>
      <div className="group flex">
        <div className="mr-2">
          <Image className="" src={`${basePath ?? ""}/laravel.svg`} width={40} height={42} alt="Logo" />
        </div>
        <div className="cursor-default font-semibold flex flex-col my-auto leading-none bg-clip-text text-transparent bg-gradient-to-tl from-primary to-secondary">
          <span className="text-sm  mb-[-.65rem] p-0 h-fit">
            <span>Laravel</span>
          </span>
          <span className="lowercase text-lg h-fit">request docs</span>
        </div>
      </div>
    </div>
  )
}