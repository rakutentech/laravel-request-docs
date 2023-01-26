"use client"

import React, { useContext } from "react"
import Editor, { EditorProps } from "@monaco-editor/react"
import { FontMono } from "./DefaultFonts"
import { GlobalStateContext } from "./GlobalState"
import { ClipboardDocumentIcon } from "@heroicons/react/24/outline"
import { classNames } from "@/utils/utils"

Editor.defaultProps = {
  loading: "Loading...",
}

interface CodeEditorProps extends EditorProps {
  label?: string
}

const CodeEditor = (props: CodeEditorProps) => {
  const { label, ...rest } = props
  const { state } = useContext(GlobalStateContext)
  return (
    <div className="rounded-md overflow-hidden ring ring-base-content/20">
      <div className={classNames("rounded-tl-md rounded-tr-md",
        "flex p-1.5 space-x-2 justify-end text-xs border-b-base-content/10",
        "bg-base-content/10 text-base-content/80")}>
        <span>{label || "Language: " + (props.defaultLanguage || "JSON")}</span>
        <span></span>
        <button
          type="button"
          className="flex items-center justify-center hover:text-primary focus:text-primary"
          onClick={() => {
            navigator.clipboard.writeText(props.value || "")
          }}
        >
          <span className="sr-only">Copy</span>
          <ClipboardDocumentIcon className="h-4 w-4" />
        </button>
      </div>
      <Editor
        height="100%"
        className={FontMono.className + " w-full h-full"}
        theme={state.theme.type === "dark" ? "vs-dark" : "light"}
        options={{
          fontFamily: FontMono.style.fontFamily,
          fontSize: 13,
          fontLigatures: true,
          minimap: {
            enabled: false,
          },
        }}
        {...rest}
      />
    </div>
  )
}

export default CodeEditor