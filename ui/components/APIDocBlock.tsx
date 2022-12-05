import React from "react"
// import { ReactDOM } from "react"
import ReactMarkDown from "react-markdown"
import remarkGfm from "remark-gfm"

export interface APIDocBlockProps {
  children: string
}

export default function APIDocBlock({ children }: APIDocBlockProps) {
  return (
    <blockquote className="text-sm -mt-1 markdown-doc">
      {
        <ReactMarkDown remarkPlugins={[remarkGfm]}>{children}</ReactMarkDown>
      }
    </blockquote>
  )
}