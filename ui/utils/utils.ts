export function getAPIInfoId(info?: IAPIInfo) {
  return info ? `${info.httpMethod}-${info.uri}`: ""
}

export function classNames(...classes: string[]) {
  return classes.filter(Boolean).join(" ")
}