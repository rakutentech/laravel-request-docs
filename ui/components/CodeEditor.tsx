"use client"

import React, { useContext } from "react"
import Editor, { EditorProps } from "@monaco-editor/react"
import { FontMono } from "./DefaultFonts"
import { GlobalStateContext } from "./GlobalState"

const CodeEditor = (props: EditorProps) => {
  const { state } = useContext(GlobalStateContext)  
  return (
    <Editor
      height="100%"
      defaultLanguage="json"
      className={FontMono.className + " w-full h-full rounded-md overflow-hidden border border-base-content/20"}
      theme={state.theme.type === "dark" ? "vs-dark" : "light"}
      options={{
        fontFamily: FontMono.style.fontFamily,
        fontSize: 13,
        fontLigatures: true,
        minimap: {
          enabled: false,
        },
      }}
      {...props}
    />
  )
}

export default CodeEditor