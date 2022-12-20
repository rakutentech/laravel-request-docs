"use client"

import React, { createContext, ReducerAction } from "react"
import { getTheme, ITheme, saveTheme, setTheme } from "../utils/themes"

const initialState = {
  theme: getTheme(),
}

// Global Context
export const GlobalStateContext = createContext<{
  state: typeof initialState;
  dispatch: React.Dispatch<any>;
}>({
  state: initialState,
  dispatch: () => null,
})

// Actions
export enum Actions {
  SET_THEME = "SET_THEME",
}

// Reducer
export const themeReducer = (state: any, action: { type: any; payload: any }) => {
  switch (action.type) {
    case Actions.SET_THEME:
      const theme = (action.payload as ITheme).id
      saveTheme(theme)
      setTheme(theme)
      return {
        ...state,
        theme: action.payload,
      }
    default:
      return state
  }
}


const globalReducer = (state: typeof initialState, action: { type: any; payload: any }) => ({
  theme: themeReducer(state.theme, action),
})

export default function GlobalState({
  children,
}: { children: React.ReactNode }) {
  const [state, dispatch] = React.useReducer(globalReducer, {
    theme: getTheme(),
  })
  return (
    <GlobalStateContext.Provider value={{state, dispatch}}>
        {children}
    </GlobalStateContext.Provider>
  )
}

