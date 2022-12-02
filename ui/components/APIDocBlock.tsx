import React from "react"
import ReactMarkDown from "react-markdown"

export interface APIDocBlockProps {
  children?: string
}

export default function APIDocBlock({ children }: APIDocBlockProps) {
  return (
    <blockquote className="text-sm">
      <ReactMarkDown>{children ?? ""}</ReactMarkDown>
    </blockquote>
  )
}