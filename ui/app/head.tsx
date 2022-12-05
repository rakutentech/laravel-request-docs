import Script from "next/script"
import React from "react"

export default function Head({ params }: { params: { slug: string } }) {
  return (
    <>
      <title>Laravel Request Docs</title>
      <link rel="icon" type="image/x-icon" href={`${process.env.NEXT_PUBLIC_BASE_PATH}/laravel.svg`} />
      {/* <Script src={`${process.env.NEXT_PUBLIC_BASE_PATH}/scripts/observer.js`} type="text/javascript" /> */}
    </>
  )
}