import React from "react"

export default function Head({ params }: { params: { slug: string } }) {
  return (
    <>
      <title>Laravel Request Docs</title>
      <link rel="icon" type="image/x-icon" href={`${process.env.NEXT_PUBLIC_BASE_PATH}/laravel.svg`} />
    </>
  )
}