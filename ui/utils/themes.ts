import { LocalStorageKeys } from "./constants"

export const themes = [
  { id: "light", name: "Light", type: "light" },
  { id: "corporate", name: "Corporate", type: "light" },
  { id: "forest", name: "Forest", type: "dark" },
  { id: "dracula", name: "Dracula", type: "dark" },
  { id: "night", name: "Night", type: "dark" },
]

export type ITheme = typeof themes[0]

export function saveTheme(themeId: string) {
  if (typeof localStorage !== "undefined") {
    localStorage.setItem(LocalStorageKeys.THEME, themeId)
  }
  console.log(`setTheme: ${JSON.stringify({ themeId })}`)
}

export function setTheme(themeId: string) {
  document.documentElement.setAttribute("data-theme", themeId)
  document.documentElement.classList.toggle("dark", themes.filter(t => t.id === themeId)[0].type === "dark")
}

export function getThemeId() {
  let themeId
  if (typeof localStorage !== "undefined") {
    themeId = localStorage.getItem(LocalStorageKeys.THEME)
  }
  console.log(`getThemeId: ${JSON.stringify({ themeId })}`)
  return themeId ?? themes[0].id
}

export function getTheme() {
  return themes.filter(t => t.id === getThemeId())[0]
}